<?php

namespace App\Http\Controllers;

use App\Common\Constant\ChattingConstants;
use App\Common\Constant\UploadFileConstants;
use App\Helpers\UploadHelper;
use App\Models\AppUser_Message;
use App\Models\Chat;
use App\Models\Chat_Message;
use App\Models\Chat_Message_User;
use App\Models\Chat_Quote_Message;
use App\Models\Chat_User;
use App\Models\UploadFile;
use App\Models\User;
use App\Models\Chat_Administrator;

use App\Services\ChatService;
use Carbon\Carbon;
use Exception;
use Hamcrest\Arrays\IsArray;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;

class ChatController extends Base\BaseController
{
    public function __construct(){

    }
    
    public function getUnReadMessage(Request $request) {
        $lst_chat_user = Chat_User::where('user_id', auth()->user()->id)
            ->WhereNull('disabled_at')
            ->WhereNull('left_at')
            ->get()
            ->load('chat');
        $lst_chat_user = $lst_chat_user->filter(function($chat_user) {
            return isset($chat_user->chat);
        });
        foreach($lst_chat_user as $chat_user) {
            
            $chat_user->chat->unread_messages = ChatService::getUnreadMessageByChatId(
                $chat_user->chat->id);
        }
        return $this->responseJson([
            'status' => 'ok',
            'lst_chat_user' => $lst_chat_user
        ]);
    }
    public function getUnReadMessageByChatId(Request $request) {
        $chat_user = Chat_User::where('user_id', auth()->user()->id)
            ->where('chat_id', $request->chat_id)
            ->WhereNull('disabled_at')
            ->WhereNull('left_at')
            ->first();
        if (isset($chat_user)) {
            $chat_user->load('chat');
            if (isset($chat_user->chat)) {
                $chat_user->chat->unread_messages = ChatService::getUnreadMessageByChatId(
                    $chat_user->chat->id);
            } else {
                $chat_user = null;
            }
        }
        return $this->responseJson([
            'status' => 'ok',
            'chat_user' => $chat_user
        ]);
    }
    public function getMessageByChatId(Request $request) {
        $lst_user_chat_messages = ChatService::getMessageByChatId(
            $request->chat_id,
            $request->last_message_id,
            $request->records_of_page);
        
        return $this->responseJson([
            'status' => 'ok',
            'lst_messages' => $lst_user_chat_messages
            ]);
    }
    public function getChatByChatId(Request $request) {
        try {
            /* $chat_user = Chat_User::where('user_id', auth()->user()->id)
                ->where('chat_id', $request->chat_id)
                ->WhereNull('disabled_at')
                ->WhereNull('left_at')
                ->first();

            if (isset($chat_user)) {

            }*/
            /*
            $chat = Chat::find($request->chat_id);
            if (isset($chat)) {
                $chat_user = null;
                foreach ($chat->members as $member) {
                    if ($member->user_id == auth()->user()->id) {
                        $chat_user == $member;
                        return;
                    }
                }
                if (!isset($chat->member_me)) {
                    return $this->responseJson([
                    'status' => 'ng',
                    'err_code' => 1,
                    'message' => 'Không tìm thấy chat hoặc bạn đã bị khóa hoặc bạn đã rời khỏi chat.'
                ]);
                }
                $chat->member_me = $chat_user;
                $chat_Messages = ChatService::getMessageByChatId(
                    $chat->id,
                    $request->last_message_id,
                    $request->records_of_page
                );
                $chat->messages = $chat_Messages;
                $chat->unread_messages = ChatService::getUnreadMessageByChatId($chat->id);
                $chat->load('avatar');
                return $this->responseJson([
                'status' => 'ok',
                'chat' => $chat
            ]);
            } else {
                return $this->responseJson([
                    'status' => 'ng',
                    'err_code' => 1,
                    'message' => 'Không tìm thấy chat hoặc bạn đã bị khóa hoặc bạn đã rời khỏi chat.'
                ]);
            }*/
            return $this->responseJson(ChatService::getChatByChatId($request->chat_id, 
                $request->last_message_id, $request->records_of_page, $request->with_init));
        }catch(Exception $e) {
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => 1,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    public function getAllChatRooms(Request $request) {
        $lst_all_chat_users = Chat::all();
        $lst_chat_user = $lst_all_chat_users->filter(function($chat) {
            return isset($chat->member_me) && !(isset($chat->member_me->disabled_at) || isset($chat->member_me->left_at) || isset($chat->member_me->kick_at));
        });
        $ds_all_chat = $lst_chat_user->map(function($chat) {
            $chat->unread_messages = ChatService::getUnreadMessageByChatId(
                $chat->id);
            $chat->messages=ChatService::getMessageByChatId(
                    $chat->id);
            return $chat;
        }); 
        return $this->responseJson([
            'status' => 'ok',
            'lst_chats' => $ds_all_chat,
            ]);
    }

    public function sendMessage(Request $request) {
        //chat_message
        try{
            error_log($request->quote_content);
            $chat = Chat::find($request->chat_id);
            if (!isset($chat)) {
                return $this->responseJson([
                    'status' => 'ng',
                    'err_code' => '3', 
                    'err_message' => 'Chat room không tồn tài hoặc đã bị xóa.'
                    ]);
            }
            $chatMessage = null;
            $lst_active_member = null;
            $action = $request->action;
            $type = isset($request->type) && 
                ($request->type == ChattingConstants::TYPE_TEXT 
                || $request->type == ChattingConstants::TYPE_FILE 
                || $request->type == ChattingConstants::TYPE_IMAGE) ?
                $request->type : ChattingConstants::TYPE_TEXT;

            if (isset($request->id) && isset($action)) {
                $chatMessage = Chat_Message::find($request->id);
            }
            if (!isset($chatMessage)) 
            {
                $action = ChattingConstants::ACTION_CREATE;
                $chatMessage = new Chat_Message();
            } else {
                $type = $chatMessage->type;
                if ($chatMessage->type != ChattingConstants::TYPE_TEXT) {
                    return $this->responseJson([
                        'status' => 'ng',
                        'err_code' => '3', 
                        'err_message' => 'Bạn không sửa được tin nhắn.'
                        ]);
                }
                $action = ChattingConstants::ACTION_UPDATE;
            }
            //set data
            $chatMessage->chat_id = $chat->id;
            $chatMessage->sender_id = auth()->user()->id;
            $chatMessage->content = $request->contentBody;
            $chatMessage->action = $action;
            $chatMessage->vect = $request->vect;
            $chatMessage->salt = $request->salt;
            $chatMessage->encryptType = $request->encryptType;
            $chatMessage->expiredAfterMinute = $request->expiredAfterMinute;
            $chatMessage->notifity_type = 0;
            $encryptContents = (isset($request->encryptedContents) && is_array($request->encryptedContents)) ? \collect($request->encryptedContents) : \collect([]);
            if ($action == ChattingConstants::ACTION_CREATE) {
                $chatMessage->type = $type;
            }

            DB::transaction(function () use ($request, &$chatMessage, &$lst_active_member, $chat, $encryptContents) {               
                
                if (ChattingConstants::TYPE_TEXT ==$chatMessage->type) {
                    if ((!isset($request->contentBody) || trim($request->contentBody) == '')
                        && (!isset($request->quote_content) || trim($request->quote_content) == '')) {
                            throw new \Exception('ERROR_001');
                    }
                    $chatMessage->save();
                } else if (ChattingConstants::TYPE_IMAGE ==$chatMessage->type
                    || ChattingConstants::TYPE_FILE ==$chatMessage->type) {
                    if (!$request->hasFile('attached_file')) {
                        throw new \Exception('ERROR_002');
                    }
                    $uploadFiles = $request->file('attached_file');
                    if (empty($uploadFiles)) {
                        throw new \Exception('ERROR_003');
                    }
                    if (is_array($uploadFiles)) {
                        //for multifiles
                        // $arr_uploadFiles = $uploadFiles;
                        throw new \Exception('ERROR_004');
                    } else {
                        // assume the upload is file
                        //$uploadFiles->store('uploads1');
                        $chatMessage->save();
                        $upload_file = UploadHelper::storeFile(
                            $uploadFiles,
                            0, //$post->uploadfile_id,
                            $chatMessage->id,
                            UploadFileConstants::CHAT,
                            UploadFileConstants::CHAT,
                        );
                        if(!isset($upload_file)) {
                            throw new \Exception('ERROR_005');
                        }
                        $chatMessage->file_id = $upload_file->id;
                        $chatMessage->save();
                    }
                }
                //save to chat_member
                error_log('save to chat_members ');
                if ($chatMessage->action == ChattingConstants::ACTION_UPDATE) {
                    $list_old_member = Chat_Message_User::where('message_id', $chatMessage->id)
                        ->get();
                    $list_old_member_id = [];
                    foreach($list_old_member as $old_member) {
                        array_push($list_old_member_id, $old_member->user_id);
                    }
                    $lst_active_member = $chat->active_members->whereIn('user_id', $list_old_member_id);
                } else {
                    $lst_active_member = $chat->active_members;
                }

                foreach($lst_active_member as $member) {
                    $chatMessageUser = Chat_Message_User::where('chat_id', $chatMessage->chat_id)
                    ->where('user_id', $member->user_id)
                    ->where('message_id', $chatMessage->id)
                    ->first();
                    if (!isset($chatMessageUser)) {
                        $chatMessageUser = new Chat_Message_User();
                    }
                    $chatMessageUser->chat_id = $chatMessage->chat_id;
                    $chatMessageUser->user_id = $member->user_id;
                    $chatMessageUser->message_id = $chatMessage->id;
                    $chatMessageUser->read_at = ($member->user_id == $chatMessage->sender_id ? Carbon::now() : null);
                    $chatMessageUser->save();
                    //save to encrypt message 
                    // appuser_message
                    if (ChattingConstants::TYPE_TEXT == $chatMessage->type 
                        && $chatMessage->encryptType > 0) {

                        $dataEnc = $encryptContents->filter(function ($item) use($member) {

                            return $item['user_id'] == $member->user_id ;
                        })->first();
                        //error_log('save to AppUser_Message ');
                        if (isset($dataEnc)) {
                            $appUserMessage = AppUser_Message::where('user_message_id', $chatMessageUser->id)
                            ->first();
                            if (!isset($appUserMessage)) {
                                $appUserMessage = new AppUser_Message();
                            }
                            $appUserMessage->user_message_id = $chatMessageUser->id;
                            $appUserMessage->senderId = $chatMessage->sender_id;
                            $appUserMessage->receiverId = $member->user_id;
                            $appUserMessage->body = $dataEnc['contentBody'];
                            $appUserMessage->content_id = $chatMessage->id;
                            $appUserMessage->content_type = 1;
                            $appUserMessage->vect = $dataEnc['vect'];
                            $appUserMessage->salt = $dataEnc['salt'];
                            $appUserMessage->save();
                        }
                    } 
                }
                //save to message_qutoe
                error_log('sent to quote_content: '.$request->quote_content);
                if (isset($request->quote_content)) {
                    
                    $chatQuoteMessage = Chat_Quote_Message::where('message_id', $chatMessage->id)->first();

                    if (!isset($chatQuoteMessage)) {
                        $chatQuoteMessage = new Chat_Quote_Message();
                    }
                    $chatQuoteMessage->message_id = $chatMessage->id;
                    $chatQuoteMessage->message_quote_id = $request->message_quote_id;
                    $chatQuoteMessage->quote_content = $request->quote_content;
                    $chatQuoteMessage->save();
                    
                } 
            }, 3);
            error_log('sent to chat socket: '.strval($chatMessage->id));
            if (isset($chatMessage) && $chatMessage->id > 0) {
                //sent to chat socket
                ChatService::sendMessageToChat($chatMessage, $lst_active_member);
                
                return $this->responseJson([
                'status' => 'ok',
                'chatMessage' => $chatMessage
                ]);
            } else {
                return $this->responseJson([
                    'status' => 'ng',
                    'err_code' => '1', 
                    'message' => 'Không luu được message',
                    'chatMessage' => null,
                ]);
            }
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => '99', 
                'err_message' => $e->getMessage()
                ]);
        }
    }

    public function reCallMessage(Request $request){
        try{
            
            if (isset($request->id)) {
                $chatMessage = Chat_Message::find($request->id);
            }

            if (!isset($chatMessage)) 
            {
                return $this->responseJson([
                    'status' => 'ng',
                    'err_code' => '1', 
                    'err_message' => 'Không tìm thấy message hoặc message có thể đã bị xóa.'
                ]);
            }
            DB::transaction(function () use ($chatMessage) {
                $chatMessage->recall_at = Carbon::now();
                $chatMessage->save();
                //co nen xoa noi dung message 
                AppUser_Message::where('content_id', $chatMessage->id)->delete();
                
            }, 3);
            ChatService::sendMessageToChat($chatMessage);
            return $this->responseJson([
                'status' => 'ok',
                'chatMessage' => $chatMessage
            ]);
            
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '0', 
                'err_message' => $e->getMessage()
                ]);
        }
    }
    public function readMessage(Request $request){
        try{
            if (isset($request->id)) {
                $chatMessageUser = Chat_Message_User::find($request->id);
            }

            if (!isset($chatMessageUser)) 
            {
                return $this->responseJson([
                    'status' => 'ng',
                    'err_code' => '1', 
                    'err_message' => 'Không tìm thấy message hoặc message có thể đã bị xóa.'
                ]);
            }
            DB::transaction(function () use (&$chatMessageUser) {
                $chatMessageUser->read_at = Carbon::now();
                $chatMessageUser->save();
            }, 3);
            $chatMessage = Chat_Message::find($chatMessageUser->message_id);
            if (isset($chatMessage)) {
                ChatService::sendMessageToChat($chatMessage);
            }
            return $this->responseJson([
                'status' => 'ok'
            ]);
            
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '0', 
                'err_message' => $e->getMessage()
                ]);
        }
    }
    public function deleteMessageUser(Request $request){
        try{
            error_log('deleteMessageUser: '.$request->message_id.'----'.$request->id);
            if (isset($request->id)) {
                $chatMessageUser = Chat_Message_User::find($request->id);
            }

            if (!isset($chatMessageUser)) 
            {
                return $this->responseJson([
                    'status' => 'ng',
                    'err_code' => '1', 
                    'err_message' => 'Không tìm thấy message hoặc message có thể đã bị xóa.'
                ]);
            }
            DB::transaction(function () use ($chatMessageUser) {
                $chatMessage = Chat_Message::find($chatMessageUser->message_id);

                AppUser_Message::where('user_message_id', $chatMessageUser->id)->delete();
                $chatMessageUser->delete();
                
                if (isset($chatMessage)) {
                    //xoa ở bảng message-user
                    $chatMessageUser->delete();
                } else {
                    Chat_Message_User::where('message_id', $chatMessageUser->message_id)->delete();
                    AppUser_Message::where('content_id', $chatMessageUser->message_id)->delete();
                }
                //check if all user delete message
                $exist_messsage_count = Chat_Message_User::where('message_id', $chatMessage->id)
                    ->count();
                if ($exist_messsage_count == 0 && isset($chatMessage)) {
                    $chatMessage->load('attachFile');
                    if (isset($chatMessage->attachFile) || $chatMessage->attachFile != null) {
                        if ($chatMessage->attachFile->id > 0) {
                            UploadHelper::deleteFile($chatMessage->attachFile->id);
                        }
                    }
                    $chatMessage->delete();
                }
                
            }, 3);
            return $this->responseJson([
            'status' => 'ok'
            ]);
            
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => '0', 
                'err_message' => $e->getMessage()
                ]);
        }
    }
    public function deleteChatChannel(Request $request){
        try{
            
            if (isset($request->chat_id)) {
                $chat = Chat::find($request->chat_id);
            }

            if (!isset($chat)) 
            {
                return $this->responseJson([
                    'status' => 'ng',
                    'err_code' => '2', 
                    'err_message' => 'Không tìm thấy chat-room hoặc chat-room có thể đã bị xóa.'
                ]);
            }
            DB::transaction(function () use ($chat) {
                //------------xoa lich su -----------
                //xoa ở bảng message-user
                
                $lst_message_user = Chat_Message_User::where('user_id', auth()->user()->id)
                    ->where('chat_id', $chat->id)
                    ->get();
                
                //check if all user delete message                
                foreach ($lst_message_user as $chatMessageUser) {
                    
                    $chatMessage = Chat_Message::find($chatMessageUser->message_id);
                    
                    if (isset($chatMessage)) {
                        $chatMessageUser->delete();
                    } else {
                        Chat_Message_User::where('message_id', $chatMessage->id)->delete();
                        AppUser_Message::where('content_id', $chatMessage->id)
                            ->where('content_type', 1)->delete();
                        continue;
                    }
                    $exist_messsage_count = Chat_Message_User::where('message_id', $chatMessage->id)
                    ->count();
                    if ($exist_messsage_count == 0) {
                        $chatMessage->load('attachFile');
                        if (isset($chatMessage->attachFile) || $chatMessage->attachFile != null) {
                            if ($chatMessage->attachFile->id > 0) {
                                UploadHelper::deleteFile($chatMessage->attachFile->id);
                            }
                        }
                        $chatMessage->delete();
                    }
                }
                //-------------- left of chat------------------
                Chat_User::where('user_id', auth()->user()->id)
                    ->where('chat_id', $chat->id)
                    ->update(['left_at' => Carbon::now()]);
                    
                
            }, 3);
            //retur chat channel
            return $this->responseJson([
                'status' => 'ok',
                ]);
            
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => '0', 
                'err_message' => $e->getMessage()
                ]);
        }
    }
    public function deleteHistory(Request $request){
        try{
            
            if (isset($request->chat_id)) {
                $chat = Chat::find($request->chat_id);
            }

            if (!isset($chat)) 
            {
                return $this->responseJson([
                    'status' => 'ng',
                    'err_code' => '2', 
                    'err_message' => 'Không tìm thấy chat-room hoặc chat-room có thể đã bị xóa.'
                ]);
            }
            DB::transaction(function () use ($chat) {
                //xoa ở bảng message-user
                
                $lst_message_user = Chat_Message_User::where('user_id', auth()->user()->id)
                    ->where('chat_id', $chat->id)
                    ->get();
                
                //check if all user delete message                
                foreach ($lst_message_user as $chatMessageUser) {
                    
                    $chatMessage = Chat_Message::find($chatMessageUser->message_id);
                    
                    if (isset($chatMessage)) {
                        $chatMessageUser->delete();
                    } else {
                        Chat_Message_User::where('message_id', $chatMessage->id)->delete();
                        continue;
                    }
                    $exist_messsage_count = Chat_Message_User::where('message_id', $chatMessage->id)
                    ->count();
                    if ($exist_messsage_count == 0) {
                        $chatMessage->load('attachFile');
                        if (isset($chatMessage->attachFile) || $chatMessage->attachFile != null) {
                            if ($chatMessage->attachFile->id > 0) {
                                UploadHelper::deleteFile($chatMessage->attachFile->id);
                            }
                        }
                        $chatMessage->delete();
                    }
                }
                
            }, 3);
            //retur chat channel
            return $this->responseJson(ChatService::getChatByChatId($request->chat_id, $request->last_message_id, $request->records_of_page));
            
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => '0', 
                'err_message' => $e->getMessage()
                ]);
        }
    }
    public function initChat(Request $request) {
        try{
            $lst_member_ids = $request->lst_member_ids;
            $is_group = count($lst_member_ids) > 2 || $request->is_group;
            if ($is_group == true) {
                return $this->responseJson([
                    'status' => 'ng',
                    'err_code' => '0', 
                    'err_message' => 'Không phải là chat đơn.'
                    ]);
            }
            $lst_user = User::whereIn('id', $lst_member_ids)->get();
            if (count($lst_user) != 2) {
                return $this->responseJson([
                    'status' => 'ng',
                    'err_code' => '1', 
                    'err_message' => 'Không tìm thấy member.'
                    ]);
            }

            if (isset($request->id)) {
                $chat = Chat::where('id', $request->id)
                    ->where('is_group', false)
                    ->first();
            }
            
            if (!isset($chat)) 
            {
                error_log('initChat ');
                
                //tim dau hieu da tao
                $lst_chats = Chat_User::whereIn('user_id', $lst_member_ids)
                    ->get()->unique('chat_id');
                    
                $arr_chat_id = [];
                foreach($lst_chats as $chat1) {
                    array_push($arr_chat_id, $chat1->chat_id);
                }
                $lst_chat = Chat::whereIn('id', $arr_chat_id)
                ->where('is_group', false)
                ->get();

                $lst_chat = $lst_chat->filter(function($chat2) {
                    return count($chat2->members) == 2;
                });
                foreach($lst_chat as $chat3) {
                    $count = 0;
                    foreach($chat3->members as $member) {
                        if ($member->user_id == $lst_user[0]->id || $member->user_id == $lst_user[1]->id) {
                            $count++;
                        }
                    }
                    
                    if ($count == 2) {
                        $chat = $chat3;
                        break;
                    }
                }
                    
                
            }
            if (!isset($chat)) {

                DB::transaction(function () use ($request, &$chat, $lst_member_ids, $is_group, $lst_user) {
                    error_log('initChat 7: ');
                    $chat = new Chat();
                    $chat->name = isset($request->name) && $request->name != '' ? $request->name : $lst_user[0]->name.' - '.$lst_user[1]->name ;
                    $chat->is_group = $is_group;
                    $chat->group_chat_type = 0; //nhom chat binh thuong
                    $chat->group_id = null;
                    $chat->wall_backgroup = $request->wall_backgroup;
                    if (!isset($request->isLockChat) || $request->isLockChat == false) {
                        $chat->group_chat_lock_at = null;
                    } else if (isset($request->group_chat_lock_at)) {
                        $chat->group_chat_lock_at = $request->group_chat_lock_at;
                    } else {
                        $chat->group_chat_lock_at = Carbon::now();
                    }
                    

                    $chat->save();
                    if ($request->hasFile('avatar_file')) {
                        $uploadFiles = $request->file('avatar_file');
                        if (!empty($uploadFiles)) {
                            if (is_array($uploadFiles)) {
                                //for multifiles
                                // $arr_uploadFiles = $uploadFiles;
                            } else {
                                // assume the upload is file
                                //$uploadFiles->store('uploads1');
                                $upload_file = UploadHelper::storeFile(
                                    $uploadFiles,
                                    0, //$post->uploadfile_id,
                                    $chat->id,
                                    UploadFileConstants::CHAT,
                                    UploadFileConstants::AVATAR,
                                );
                                if (!isset($upload_file)) {
                                    throw new \Exception('ERROR_001');
                                }
                                $chat->avatar_id = $upload_file->id;
                                $chat->save();
                            }
                        }
                    }
                    //save member of chat
                    foreach ($lst_member_ids as $member_id) {
                        $user = $lst_user->filter(function ($user) use ($member_id) {
                            return $member_id != $user->id;
                        })->first();
                        
                        Chat_User::Create([
                            'user_id' => $member_id,
                            'chat_id' => $chat->id,
                            'display_name' => isset($user) ? $user->name : " "
                        ]);
                    }
                }, 3);
            } else {
                error_log('initChat 7 1: ');
                //update nếu user đã left
                foreach($chat->members as $member) {
                    if ($member->left_at) {
                        $member->left_at = null;
                        $member->save();
                    }
                }
            }

            if (!isset($chat) || !isset($chat->id) || $chat->id <= 0) {
                return $this->responseJson([
                    'status' => 'ng',
                    'err_code' => '1', 
                    'err_message' => 'Không khởi tạo được chat'
                    ]);
            }
            $chat = Chat::find($chat->id);
            if (!isset($chat) || !isset($chat->id) || $chat->id <= 0 || !isset($chat->member_me)) {
                return $this->responseJson([
                    'status' => 'ng',
                    'err_code' => '2', 
                    'err_message' => 'Không khởi tạo được chat'
                    ]);
            }
            error_log('initChat 8: ');
            //$chat->load('messages');
            //error_log('initChat 8 1: '.count($chat->members));
            Log::info(['initChat 8 1:', $chat->member_me]);
            $chat->messages = ChatService::getMessageByChatId($chat->id);
            $chat->unread_messages = ChatService::getUnreadMessageByChatId($chat->id);
            error_log('initChat 9: '.count($chat->messages));
            error_log('initChat 10: '.count($chat->unread_messages));
            return $this->responseJson([
                'status' => 'ok',
                'chat' => $chat
            ]);
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => '0', 
                'err_message' => $e->getMessage()
                ]);
        }
    }
    public function createGroupChat(Request $request) {
        try{
            $isCreate = false;
            if (isset($request->id)) {
                
                $chat = Chat::find($request->id);
            }
            $lst_member_ids = $request->lst_member_ids;
            if (!isset($chat)) 
            {
                $isCreate = true;
                $chat = new Chat();
            }
            
            if (!isset($lst_member_ids) || count($lst_member_ids) == 0) {
                $lst_member_ids = [];
                array_push($lst_member_ids, auth()->user()->id);
            }
            
            $lst_member_admin_ids = $request->lst_member_admin_ids;
            if (!isset($lst_member_admin_ids) || count($lst_member_admin_ids) == 0) {
                $lst_member_admin_ids = [];
                array_push($lst_member_admin_ids, auth()->user()->id);
            }
            foreach ($lst_member_admin_ids as $admin_id) {
                $hasMember = false;
                foreach ($lst_member_ids as $member_id) {
                    if ($member_id == $admin_id) {
                        $hasMember = true;
                    }
                }
                if (!$hasMember) {
                    array_push($lst_member_ids, $admin_id);
                }
            }
            $chat->name = $request->name;
            $chat->donvi_id = $request->donvi_id;
            $chat->is_group = true;  
            if (!$isCreate) {          
                $chat->group_chat_type = isset($request->group_chat_type) ? $request->group_chat_type : 0; //nhom chat binh thuong
            } else {
                $chat->group_id = null;
                $chat->group_chat_type = 0;
            }
            
            $chat->wall_backgroup = $request->wall_backgroup;
            if (!isset($request->isLockChat) || $request->isLockChat == false) {
                $chat->group_chat_lock_at = null;
            } else if (isset($request->group_chat_lock_at)) {
                $chat->group_chat_lock_at = $request->group_chat_lock_at;
            } else {
                $chat->group_chat_lock_at = Carbon::now();
            }
            $member_kick_at_message = "";
            $member_re_add_message = "";
            $member_add_message = "";
            $member_admin_delete_message = "";
            $member_admin_message = "";
            DB::transaction(function () use ($request, &$chat, $lst_member_ids, $lst_member_admin_ids, 
                &$member_kick_at_message, &$member_re_add_message, &$member_add_message, $isCreate,
                &$member_admin_delete_message, &$member_admin_message) {
                $chat->save();
                if ($request->hasFile('avatar_file')) {
                    $uploadFiles = $request->file('avatar_file');
                    if (!empty($uploadFiles)) {
                        if (is_array($uploadFiles)) {
                            //for multifiles
                            // $arr_uploadFiles = $uploadFiles;
                        } else {
                            // assume the upload is file
                            //$uploadFiles->store('uploads1');
                            $upload_file = UploadHelper::storeFile(
                                $uploadFiles,
                                0, //$post->uploadfile_id,
                                $chat->id,
                                UploadFileConstants::CHAT,
                                UploadFileConstants::AVATAR,
                            );
                            if (!isset($upload_file)) {
                                throw new \Exception('ERROR_001');
                            }
                            $chat->avatar_id = $upload_file->id;
                            $chat->save();
                        }
                    }
                }
                
                //save member of chat
                error_log('createGroupChat 50: '.strval(count($lst_member_ids)));
                if ($chat->group_chat_type == ChattingConstants::GROUP_CHAT_TYPE_NORMAL) {
                    $lstUserKicks = Chat_User::where('chat_id', $chat->id)->whereNotIn('user_id',$lst_member_ids)
                        ->whereNull('kick_at')->get();
                        
                        error_log('createGroupChat 51: '.strval(count($lstUserKicks)));

                    foreach ($lstUserKicks as $userkick) {
                        $userkick->load('userInf');
                        error_log('createGroupChat 52: '.$userkick);
                        if (!isset($userkick->left_at) && isset($userkick->userInf)) {                            
                            if ($member_kick_at_message != '') {
                                $member_kick_at_message = $member_kick_at_message."; ".$userkick->userInf->full_name;
                            } else {
                                $member_kick_at_message = $userkick->userInf->full_name;
                            }
                        } 
                    }

                    
                    Chat_User::where('chat_id', $chat->id)->whereNotIn('user_id',$lst_member_ids)
                        ->whereNotNull('kick_at') ->update(['kick_at' => Carbon::now()]);
                    
                    foreach ($lst_member_ids as $member_id) {
                        $members = Chat_User::where('chat_id', $chat->id)->where('user_id', $member_id)->get();
                        if (count($members) > 0) {
                            $idx = 0;
                            
                            foreach($members as $member) {
                                $member->load('userInf');
                                
                                if ($idx == 0 && isset($member->userInf)) {
                                    $idx == 1;
                                    if (isset($member->kick_at) || isset($member->left_at)) {
                                        if($member_re_add_message == "") {
                                            $member_re_add_message = $member->userInf->full_name;
                                        } else {
                                            $member_re_add_message = $member_re_add_message."; ".$member->userInf->full_name;
                                        }
                                    }
                                    $member->kick_at = null;
                                    $member->left_at = null;
                                    $member->save();
                                } else {
                                    error_log('delete members:'.$member->userInf);
                                    $member->delete();
                                }

                            }
                        } else { //them moi
                            $member = new Chat_User();
                            $member->user_id = $member_id;
                            $member->chat_id = $chat->id;
                            $member->kick_at = null;
                            $member->left_at = null;
                            $member->save();
                            $member->load('userInf');
                            if (!$isCreate && isset($member->userInf)) {
                                if($member_add_message == "") {
                                    $member_add_message = $member->userInf->full_name;
                                } else {
                                    $member_add_message = $member_add_message."; ".$member->userInf->full_name;
                                }
                            }
                        }
                    }
                }
                
                error_log('createGroupChat 6 '.strval(count($lst_member_admin_ids)));
                // admin
                //xoa quan tri nhom
                $lstUserAdminDelete = Chat_Administrator::where('chat_id', $chat->id)->whereNotIn('admin_id',$lst_member_admin_ids)->get();
                foreach ($lstUserAdminDelete as $userAdminDelete) {
                    $userAdminDelete->load('user');
                    error_log('createGroupChat 54: '.$userAdminDelete);
                    if (isset($userAdminDelete->user)) {                            
                        if ($member_admin_delete_message != '') {
                            $member_admin_delete_message = $member_admin_delete_message."; ".$userAdminDelete->user->full_name;
                        } else {
                            $member_admin_delete_message = $userAdminDelete->user->full_name;
                        }
                    } 
                }
                $lstUserAdminDelete = Chat_Administrator::where('chat_id', $chat->id)->whereNotIn('admin_id',$lst_member_admin_ids)->delete();
                foreach ($lst_member_admin_ids as $member_id) {
                    $chat_Administrator = Chat_Administrator::where('admin_id', $member_id)
                        ->where('chat_id', $chat->id)->first();
                    if (!isset($chat_Administrator)) {
                        $chat_Administrator = new Chat_Administrator();
                        $chat_Administrator->admin_id = $member_id;
                        $chat_Administrator->chat_id = $chat->id;
                        $chat_Administrator->save();

                        $chat_Administrator->load('user');
                        error_log('createGroupChat 55: '.$chat_Administrator);
                        if (isset($chat_Administrator->user)) {                            
                            if ($member_admin_message != '') {
                                $member_admin_message = $member_admin_message."; ".$chat_Administrator->user->full_name;
                            } else {
                                $member_admin_message = $chat_Administrator->user->full_name;
                            }
                        } 
                    }
                    
                }
            
                
            }, 3);
            
            $chat->messages = ChatService::getMessageByChatId($chat->id);
            
            $chat->unread_messages = ChatService::getUnreadMessageByChatId($chat->id);
            
            error_log('createGroupChat '.$chat->group_chat_type );
            
            //infor chat
            /*
            if (!$isCreate && $chat->is_group 
                && ($chat->group_chat_type == ChattingConstants::GROUP_CHAT_TYPE_WORK_GROUP 
                    || $chat->group_chat_type == ChattingConstants::GROUP_CHAT_TYPE_UNIT)) {
                        error_log('create group chat 2: '.strval(count($chat->unread_messages)));
                */
            
            if (!$isCreate && $chat->is_group ) {
                
                //$chat->load('ActiveMembers');

                $lst_active_member = $chat->ActiveMembers;
                error_log('createGroupChat lst_active_member: '.strval(count($lst_active_member)));
                if ($member_kick_at_message != "") {    
                    ChatService::createMessageNotifyGroup($chat->id, $member_kick_at_message." đã bị kick khỏi nhóm bởi ".auth()->user()->full_name, 
                        $lst_active_member, auth()->user()->id);
                }
                if ($member_re_add_message != "") {
                    ChatService::createMessageNotifyGroup($chat->id, $member_re_add_message." đã được thêm lại vào nhóm bởi ".auth()->user()->full_name, 
                        $lst_active_member, auth()->user()->id);
                }
                if ($member_add_message != "") {
                    ChatService::createMessageNotifyGroup($chat->id, $member_add_message." đã được thêm mới vào nhóm bởi ".auth()->user()->full_name, 
                        $lst_active_member, auth()->user()->id);
                }
                if ($member_admin_message != "") {
                    ChatService::createMessageNotifyGroup($chat->id, $member_admin_message." đã được thêm mới như quản trị nhóm bởi ".auth()->user()->full_name, 
                        $lst_active_member, auth()->user()->id);
                }
                if ($member_admin_delete_message != "") {
                    ChatService::createMessageNotifyGroup($chat->id, $member_admin_delete_message." đã được xoá như quản trị nhóm bởi ".auth()->user()->full_name, 
                        $lst_active_member, auth()->user()->id);
                }
            }
            
            return $this->responseJson([
                'status' => 'ok',
                'chat' => $chat
            ]);
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => '0', 
                'err_message' => $e->getMessage()
                ]);
        }
    }

    /* public function attachFile(Request $request){
        try{
            if ($request->hasFile('attached_file')) {
                $uploadFiles = $request->file('attached_file');
                $arr_uploadFiles = array();
                if (!empty($uploadFiles)) {
                    if (is_array($uploadFiles)) {
                        //for multifiles
                       // $arr_uploadFiles = $uploadFiles;
                    } else {
                        // assume the upload is file
                        //$uploadFiles->store('uploads1');
                        $upload_file = UploadHelper::storeFile(
                            $uploadFiles,
                            0, //$post->uploadfile_id,
                            0,
                            UploadFileConstants::CHAT,
                            UploadFileConstants::CHAT,
                        );
                        return $this->responseJson([
                            'status' => 'ok',
                            'uploadFile' => $upload_file
                            ]);
                    }
                }
            }    
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', //danh sach to (receivers) bi rong
                'err_message' => $e->getMessage()
                ]);
        }
        
        return $this->responseJson([
            'status' => 'ok',
            'uploadFile' => null
            ]);
    } */
    public function loadAttachFile(Request $request){
        try{
            $uploadFile_id = $request->upload_file_id;
            $uploadFile = UploadFile::find($uploadFile_id);
            
            return $this->responseJson([
                'status' => 'ok',
                'uploadFile' => $uploadFile
                ]);
                
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', //danh sach to (receivers) bi rong
                'err_message' => $e->getMessage()
                ]);
        }
        
    }
    public function deleteAttachFile(Request $request){
        try{
            $uploadFile_id = $request->upload_file_id;
            // UploadFile::delete(uploadFile_id);
            UploadHelper::deleteFile($uploadFile_id);
            return $this->responseJson([
                'status' => 'ok'
                ]);
                
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage()
                ]);
        }
        
    }
    public function leaveChat(Request $request) {
        try{            
            if (isset($request->chat_id)) {
                $chat = Chat::find($request->chat_id);
            }

            if (!isset($chat)) 
            {
                return $this->responseJson([
                    'status' => 'ng',
                    'err_code' => '2', 
                    'err_message' => 'Không tìm thấy chat-room hoặc chat-room có thể đã bị xóa.'
                ]);
            }
            $chatMessage = null;
            DB::transaction(function () use ($chat, &$chatMessage) {
                //xoa ở bảng message-user
                
                $lst_user_left = Chat_User::where('user_id', auth()->user()->id)
                    ->where('chat_id', $chat->id)
                    ->whereNull('left_at')
                    ->get();

                Chat_User::where('user_id', auth()->user()->id)
                    ->where('chat_id', $chat->id)
                    ->whereNull('left_at')
                    ->update(['left_at' => Carbon::now()]);
                if (isset($lst_user_left) && count($lst_user_left) > 0) {
                    // sent thong bao
                    $chatMessage = new Chat_Message();
                    $chatMessage->chat_id = $chat->id;
                    $chatMessage->sender_id = auth()->user()->id;
                    
                    $chatMessage->action = 0;
                    $chatMessage->vect = "";
                    $chatMessage->salt = 1;
                    $chatMessage->encryptType = 0;
                    $chatMessage->expiredAfterMinute = Carbon::now()->addDays(7);
                    $chatMessage->notifity_type = ChattingConstants::NOTIFY_TYPE_COMMON_SYSTEM;
                    $chatMessage->content = "".$lst_user_left[0]->userInf->full_name."vừa rời nhóm";
                    $chatMessage->save();
                    $lst_active_member = $chat->ActiveMembers;
                    foreach($lst_active_member as $member) {
                        $chatMessageUser = new Chat_Message_User();
                    
                        $chatMessageUser->chat_id = $chatMessage->chat_id;
                        $chatMessageUser->user_id = $member->user_id;
                        $chatMessageUser->message_id = $chatMessage->id;
                        $chatMessageUser->read_at = ($member->user_id == $chatMessage->sender_id ? Carbon::now() : null);
                        $chatMessageUser->save();
                    }
                    
                }
                
            }, 3);
            if (isset($chatMessage) && isset($chatMessage->id)) {
                ChatService::sendMessageToChat($chatMessage, $chat->ActiveMembers);
            }
            //retur chat channel
            //return $this->responseJson(ChatService::getChatByChatId($request->chat_id, $request->last_message_id, $request->records_of_page));
            return $this->responseJson([
                'status' => 'ok',
                'chat' => $chat
                ]);  
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => '0', 
                'err_message' => $e->getMessage()
                ]);
        }
    }
    
}
