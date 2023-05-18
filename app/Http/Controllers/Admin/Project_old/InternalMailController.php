<?php

namespace App\Http\Controllers;

use App\Common\Constant\InternalMailFolderConstants;
use App\Common\Constant\InternalMailReceiveTypeConstants;
use App\Common\Constant\InternalMailStatusConstants;
use App\Common\Constant\UploadFileConstants;
use App\Helpers\UploadHelper;
use App\Http\Requests\InternalMailRequest;
use App\Models\DonVi;
use App\Models\Group;
use App\Models\InternalMail;
use App\Models\InternalMail_User;
use App\Models\User;
use App\Utils\NumUtils;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class InternalMailController extends Base\BaseController
{
    public function __construct(){

    }
    
    public function send(InternalMailRequest $request){
        
        error_log('---------------SendMail1------------------------'.$request->id);
        if(isset($request->id) && $request->id > 0) {
            $internalmail = $this->checkMailOfUser($request->id);
            if ($internalmail == null) {
                return $this->responseJson([
                'status' => 'ng',
                'err_code' => '1' , //danh sach to (receivers) bi rong
                'message' => 'Thư bị xóa hoặc bạn ko phải là người tạo thư này!',
                ]);
            }
        }

        if (!isset($internalmail)) {
            $internalmail = new InternalMail;
        }
        $internalmail->subject = $request->subject;
        $internalmail->keyword = $request->keyword;
        $internalmail->status = InternalMailStatusConstants::SENT; //1: sent
        $internalmail->body = $request->body;
        $internalmail->creator_id = auth()->user()->id;
        $internalmail->sent_at = Carbon::now();
        $internalmail->tos = $request->tos;
        $internalmail->bccs = $request->bccs;
        $internalmail->ccs = $request->ccs;
        $internalmail->source_type= $request->source_type;
        $internalmail->reply_mail_id = $request->reply_mail_id;
        $internalmail->forward_mail_id = $request->forward_mail_id;
        


        if (!(isset($request->tos) || count(is_array($request->tos) ? $request->tos : []) == 0)
        && (!isset($request->bccs) || count(is_array($request->bccs) ? $request->bccs : []) == 0)
        && (!isset($request->ccs) || count(is_array($request->ccs) ? $request->ccs : []) == 0))
         {
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1' //danh sach to (receivers) bi wrong
                ]);
        }
        try{
            DB::transaction(function () use ($request, $internalmail) {
                $internalmail->save();
                
                // forward mail with attached file 
                if (isset($internalmail->forward_mail_id) && $internalmail->forward_mail_id > 0 
                    && isset($request->attached_file_ids) && is_array($request->attached_file_ids)) {
                    foreach($request->attached_file_ids as $uploadedFile_id) {
                        
                        error_log('---------------forward_mail_id--------------------'.$uploadedFile_id);
                        UploadHelper::copyFile(
                            $uploadedFile_id,
                            $internalmail->id,
                            UploadFileConstants::INTERNAL_MAIL,
                            UploadFileConstants::INTERNAL_MAIL);
                    }
                }

                error_log('---------------SendMail5--------------------');
                if (count(is_array($request->tos) ? $request->tos : []) > 0) {
                    error_log('---------------SendMail5--------tos------------');

                    $this->insertInternalMail_User($request->tos,$internalmail->id,InternalMailReceiveTypeConstants::TO);
                }
                if (count(is_array($request->ccs) ? $request->ccs : []) > 0) {
                    error_log('---------------SendMail5--------ccs------------');
                    $this->insertInternalMail_User($request->ccs,$internalmail->id,InternalMailReceiveTypeConstants::CC); 
                }
                if (count(is_array($request->bccs) ? $request->bccs : []) > 0) {
                    error_log('---------------SendMail5--------bccs------------');
                    $this->insertInternalMail_User($request->bccs,$internalmail->id,InternalMailReceiveTypeConstants::BCC);
                }
                

                
                $lstPublishUser = InternalMail_User::withTrashed()
                    ->where('internalmail_id', $internalmail->id)
                    ->get();
                error_log('internalMail id: '.$request->id."---:".$internalmail->id."----:".count($lstPublishUser));
                $hasPublishUser = false;
                foreach ($lstPublishUser as $internalmail_user) {
                    //creator
                    //error_log('update ----1-:'.$thongbao_user->user_id."----:".auth()->user()->id);
                    if ($internalmail_user->user_id == auth()->user()->id &&
                         ($internalmail_user->from_folder == InternalMailFolderConstants::DRAFT
                            || $internalmail_user->from_folder == InternalMailFolderConstants::SENT)) {
                        
                        $hasPublishUser = true;
                        $internalmail_user->from_folder = InternalMailFolderConstants::SENT;
                        
                        $internalmail_user->save();
                    }
                }
                if (!$hasPublishUser) {
                    $lstReceiverMe_ids = array();
                    error_log('---------------SendMail4--------------------');
    
                    array_push($lstReceiverMe_ids,auth()->user()->id);
                    $lstInternalMail_User[] = [
                        'internalmail_id' => $internalmail->id,
                        'user_id' => auth()->user()->id,
                        'donvi_id' => 0,
                        'star_flg' => false,
                        'read_at' => Carbon::now(),
                        'from_folder' => InternalMailFolderConstants::SENT, //0, draft; 1: sent; 2:danh sach receivee
                        'receiver_type' => InternalMailReceiveTypeConstants::TO // '0: creator, 1: to; 2: bcc; 3: cc'
                        ];
                    InternalMail_User::insert($lstInternalMail_User);
                }
            }, 3);
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', //danh sach to (receivers) bi rong
                'err_mess' => $e->getMessage()
                ]);
        }
        
        return $this->responseJson([
            'status' => 'ok',
            'internalmail' => $internalmail
            ]);
    }

    private function insertInternalMail_User($lstId, $internalmail_id, $type) {
        error_log($lstId[0]);
        if ($lstId[0] === 'ADV') {
            error_log('Gửi tất cả');
            $list_users = User::where('csdt_id','!=',0)->get();
            $list_userIds = $list_users->map(function($user){
                return $user->id;
            });
            $lstInternalMail_User = array();
            foreach ($list_userIds as $receiverId) {
                $internalMail_user = InternalMail_User::where('internalmail_id',$internalmail_id)
                                                    ->where('user_id',$receiverId)->where('receiver_type','!=',0)->get();
                if (isset($internalMail_user) && count($internalMail_user)>0 && ($type != 0)) {
                    continue;
                }
                $lstInternalMail_User[] = [
                    'internalmail_id' => $internalmail_id,
                    'user_id' => $receiverId,
                    'donvi_id' => 0,
                    'star_flg' => false,
                    'read_at' => null,
                    'from_folder' => InternalMailFolderConstants::RECEIVE, //0, draft; 1: sent; 2:danh sach receivee
                    'receiver_type' => InternalMailReceiveTypeConstants::TO // '0: creator, 1: to; 2: bcc; 3: cc'
                ];
            }
            InternalMail_User::insert($lstInternalMail_User);
        }else {
            //user
            $lstUserId = array_filter($lstId, function($Id) {
                if (count(explode('_', $Id)) <= 1) {
                    return $Id;
                }
            });
            if (count($lstUserId) > 0) {
                $lstInternalMail_User = $this->buildSendMail(
                    $lstUserId,
                     $internalmail_id,
                      0,
                       $type);
                InternalMail_User::insert($lstInternalMail_User);
            }
            //don vi
            $lstDonViId = array_filter($lstId,function($Id) {
                return ((count(explode('_', $Id)) > 1) && explode('_', $Id)[0] == 'dv');
            });
            $listDonViId = array();
            foreach ($lstDonViId as $donviId){
                $donviId = str_replace('dv_','',$donviId);
                array_push($listDonViId,$donviId);
            }
            if (count($listDonViId) > 0 ) {
                $lstDonvi = DonVi::wherein('id', $listDonViId)->get()->load('users');
                foreach ($lstDonvi as $donvi) {
                    $lstUser = $donvi->users;
                    $listUserId = $lstUser->map(function($user){
                        return $user->id;
                    });
                    error_log('------insertInternalMail_User2.1---------'.strval(count($lstUserId)));
    
                    if (count($listUserId) > 0) {
                        $lstInternalMail_User = $this->buildSendMail(
                        $listUserId,
                         $internalmail_id, 
                         $donvi->id, 
                         $type);
                        
                        InternalMail_User::insert($lstInternalMail_User);
                    }
                }
                error_log('------insertInternalMail_User---------');
            }
            //group
            $lstGroupId = array_filter($lstId,function($Id) {
                return ((count(explode('_', $Id)) > 1) && explode('_', $Id)[0] == 'gr');
            });
            $listGroupId = array();
            $isAllGr = false;
            foreach ($lstGroupId as $groupId){
                if ($groupId == 'gr_0') {
                    $isAllGr = true;
                    break;
                }
                $groupId = str_replace('gr_','',$groupId);
                array_push($listGroupId,$groupId);
            }
            $lstGroup = null;
            if ($isAllGr) {
                $lstGroup = Group::get()->load('users');
            }else if (count($listGroupId) > 0 ) {
                $lstGroup = Group::wherein('id', $listGroupId)->get()->load('users');
            }
            if (isset($lstGroup)) {
                foreach ($lstGroup as $group) {
                    $lstUser = $group->users;
                    $listUserId = $lstUser->map(function($user){
                        return $user->id;
                    });
                    error_log('------insertInternalMail_User2.1---------'.strval(count($lstUserId)));

                    if (count($listUserId) > 0) {
                        $lstInternalMail_User = $this->buildSendMail(
                        $listUserId,
                        $internalmail_id, 
                        0, 
                        $type);
                        InternalMail_User::insert($lstInternalMail_User);
                    }
                }
            }

        }
    }

    private function buildSendMail($lstReceiver_ids, $internalmail_id, $donvi_id, $receiver_type, $from_folder = 2) {
        $lstInternalMail_User = array();
        foreach ($lstReceiver_ids as $receiverId) {
            $internalMail_user = InternalMail_User::where('internalmail_id',$internalmail_id)
                                                    ->where('user_id',$receiverId)->where('receiver_type','!=',0)->get();
            if (isset($internalMail_user) && count($internalMail_user)>0 && ($receiver_type != 0)) {
                continue;
            }
            $lstInternalMail_User[] = [
            'internalmail_id' => $internalmail_id,
            'user_id' => $receiverId,
            'donvi_id' => $donvi_id,
            'star_flg' => false,
            'read_at' => null,
            'from_folder' => $from_folder, //0, draft; 1: sent; 2:danh sach receivee
            'receiver_type' => $receiver_type // '0: creator, 1: to; 2: bcc; 3: cc'
            ];
        }
        return $lstInternalMail_User;
    }

    public function draft(InternalMailRequest $request){
        
        error_log('---------------draft------------------------'.$request->id);

        if(isset($request->id) && $request->id > 0) {
            //da draft
            $internalmail = $this->checkMailOfUser($request->id);
            if ($internalmail == null) {
                return $this->responseJson([
                'status' => 'ng',
                'err_code' => '1' , //danh sach to (receivers) bi rong
                'message' => 'Thư bị xóa hoặc bạn ko phải là người tạo thư này!',
                ]);
            }
        }

        if (!isset($internalmail)) {
            $internalmail = new InternalMail;
        }
        $internalmail->subject = $request->subject;
        $internalmail->keyword = $request->keyword;
        $internalmail->status = InternalMailStatusConstants::DRAFT; //1: sent
        $internalmail->body = $request->body;
        $internalmail->creator_id = auth()->user()->id;
        $internalmail->sent_at = Carbon::now();
        $internalmail->tos = $request->tos;
        $internalmail->bccs = $request->bccs;
        $internalmail->ccs = $request->ccs;
        $internalmail->source_type= $request->source_type;

        $internalmail->reply_mail_id = $request->reply_mail_id;
        $internalmail->forward_mail_id = $request->forward_mail_id;

        if (!(isset($request->tos) || count(is_array($request->tos) ? $request->tos : []) == 0)
        && (!isset($request->bccs) || count(is_array($request->bccs) ? $request->bccs : []) == 0)
        && (!isset($request->ccs) || count(is_array($request->ccs) ? $request->ccs : []) == 0))
         {
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1' //danh sach to (receivers) bi wrong
                ]);
        }
        // add list reveice to internalmails_users tables
        try{
            DB::transaction(function () use ($request, $internalmail) {
                $internalmail->save();
                if (isset($internalmail->forward_mail_id) && $internalmail->forward_mail_id > 0 
                    && isset($request->attached_file_ids) && is_array($request->attached_file_ids)) {
                    foreach($request->attached_file_ids as $uploadedFile_id) {
                        
                        error_log('---------------forward_mail_id--------------------'.$uploadedFile_id);
                        UploadHelper::copyFile(
                            $uploadedFile_id,
                            $internalmail->id,
                            UploadFileConstants::INTERNAL_MAIL,
                            UploadFileConstants::INTERNAL_MAIL);
                    }
                }
                //process attach file after save at other process
                error_log('0');
                $internalmail_user = InternalMail_User::where('internalmail_id', $request->id)
                ->where('user_id', auth()->user()->id)
                ->first();
                if (!isset($internalmail_user)) {
                    $internalmail_user = new InternalMail_User;
                }
                error_log('---------------SendMail1------------------------');
                $donvi_id = ($this->getLoginedUser() && count($this->getLoginedUser()->donvis) > 0) ? $this->getLoginedUser()->donvis[0]->id : 0;
                $internalmail_user->internalmail_id = $internalmail->id;
                $internalmail_user->user_id = auth()->user()->id;
                $internalmail_user->star_flg = false;
                $internalmail_user->read_at = Carbon::now();
                $internalmail_user->donvi_id = $donvi_id;
                $internalmail_user->from_folder = InternalMailFolderConstants::DRAFT; //draft
                $internalmail_user->save();
                
                error_log('---------------SendMail1------------------------');
            }, 3);
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', //danh sach to (receivers) bi rong
                'err_mess' => $e->getMessage()
                ]);
        }
        
        return $this->responseJson([
            'status' => 'ok',
            'internalmail' => $internalmail
            ]);
    }

    public function star(Request $request){
        error_log($request->star_flg);
        if(isset($request->id)) {
            //da draft
            if (isset($request->from_folder) && $request->from_folder != "") {
                $from_folder = is_numeric($request->from_folder) ? $request->from_folder : 999;
                $internalmail_user = Internalmail_User::where('internalmail_id', $request->id)
                ->where('user_id', auth()->user()->id)
                ->where('from_folder', $from_folder)
                ->first();
            } else {
                error_log($request->star_flg);
                $internalmail_user = Internalmail_User::where('internalmail_id', $request->id)
                ->where('user_id', auth()->user()->id)
                ->where('from_folder', 2)
                ->first();
            }
            
            if (isset($internalmail_user)) {
                $internalmail_user->star_flg = $request->star_flg;
                $internalmail_user->save();
            }
            return $this->responseJson([
                'status' => "ok",
                'internalmail' => $internalmail_user
                ]);
        }
        return $this->responseJson([
            'status' => "ng",
            'internalmail' => null,
            'message' => 'Unknow'
            ]);
    }
    public function read(Request $request){
        try {
        error_log('-----------Read-Mail-------'.$request->id);
        if(isset($request->id)) {
            //da draft
            $internalmail_user = InternalMail_User::where('internalmail_id', $request->id)
            ->where('user_id', auth()->user()->id)->where('from_folder',InternalMailFolderConstants::RECEIVE)
            ->first();
            if (isset($internalmail_user)) {
                if ($internalmail_user->from_folder == InternalMailFolderConstants::RECEIVE) {
                    $internalmail_user->read_at = (isset($request->read_flg) && $request->read_flg == true) ? Carbon::now() : null ;
                    $internalmail_user->save();
                }
            }
            return $this->responseJson([
                'status' => "ok",
                'internalmail' => $internalmail_user
                ]);
    }
        return $this->responseJson([
            'status' => "error",
            'internalmail' => null
            ]);
        }catch(Exception $e){
            error_log($e->getMessage());
        }
    }
    public function getInternalMail(Request $request) {
        $internalmail_user = InternalMail_User::withTrashed()
        ->where('internalmail_id', $request->internalmail_id)
        ->first();
        if (isset($internalmail_user)) {
            $internalmail = InternalMail::find($internalmail_user->internalmail_id)
            ->load('attachedFiles')
            ->load('nguoigui')
            ->load('receivers');
            if (isset($internalmail)) {
                if (isset($internalmail->nguoigui)) {
                    $internalmail->nguoigui->load('avatar');
                }
            
                $internalmail->receivers->load('donvis');
                $internalmail->receivers->load('roles');
                $internalmail->internalmail_user = $internalmail_user;
            }
            //$internalmail->read_flg = $internalmail_user->read_flg;
            // $internalmail->star_flg = $internalmail_user->star_flg;
            return $this->responseJson([
                'internalmail' => $internalmail,
                ]);
        }
        
        return $this->responseJson([
            'internalmail' => null
            ]);
    }
    public function getDrafts(Request $request) {
        try{
            $internalmail_users = InternalMail_User::where('user_id', auth()->user()->id)
            ->where('from_folder', InternalMailFolderConstants::DRAFT) //from draft
            ->get();
            
            $lstInternalMails = $internalmail_users->map(function ($internalmail_user) {
                $internalmail = $internalmail_user->internalmail;
                $internalmail->load('attachedFiles');
                $internalmail->load('nguoigui');
                if ($internalmail->nguoigui) {
                    $internalmail->nguoigui('avatar');
                }
                $internalmail->star_flg = $internalmail_user->star_flg;
                $internalmail->internalmail_user_id = $internalmail_user->id;
                $internalmail->read_at = $internalmail_user->read_at;
                $internalmail->internalmail_user_id = $internalmail_user->id;
            // $internalmail->load('tos');
                // $internalmail->load('bccs');
                // $internalmail->load('ccs');
                return $internalmail;
            });
            $lstInternalMails = collect($lstInternalMails);
            $lstInternalMailsSort = $lstInternalMails->sortByDesc('created_at');
            $lstInternalMails = collect($lstInternalMailsSort->values()->all());
        }catch (Exception $e){
            error_log($e->getMessage());
        };
        return $this->responseJson([
            'status' => 'ok',
            'internalmails' => $lstInternalMails
        ]);
    }
    public function getPublishs(Request $request) {
        $page_num = NumUtils::isInt($request->page_num) ? (int)$request->page_num :  1;
        $record_per_page = NumUtils::isInt($request->record_per_page) ? (int) $request->record_per_page :  25;        
        $internalMailFolder = InternalMailFolderConstants::SENT;
        error_log('------'.($internalMailFolder));
        $internalmail_users = InternalMail_User::where('user_id', auth()->user()->id)
        ->where('from_folder',InternalMailFolderConstants::SENT) 
        ->get()->load('InternalMail');
        $lstInternalMails = $internalmail_users->map(function ($internalmail_user) {
            $internalmail = $internalmail_user->internalmail;
            // $internalmail->load('tos');
         
            // $internalmail->load('bccs');
        
            // $internalmail->load('ccs');
            
            
            // $internalmail->load('attachedFiles');
            $internalmail->star_flg = $internalmail_user->star_flg;
            $internalmail->read_at = $internalmail_user->read_at;
            $internalmail->load('attachedFiles');
            $internalmail->load('nguoigui');
            if ($internalmail->nguoigui) {
                $internalmail->nguoigui('avatar');
            }
            $internalmail->internalmail_user_id = $internalmail_user->id;
            // $internalmail->read_flg = $internalmail_user->read_flg;
            error_log('------------GetAllPublist-------'.$internalmail->id);
            return $internalmail;
        });
        $lstInternalMails = collect($lstInternalMails);
            $lstInternalMailsSort = $lstInternalMails->sortByDesc('sent_at');
            $lstInternalMails = collect($lstInternalMailsSort->values()->all());
        return $this->responseJson([
            'status' => 'ok',
            'internalmails' => $lstInternalMails
        ]);
    }
    public function getAlls(Request $request) {
        $internalmail_users = internalmail_User::where(function($query){
            $query->where('user_id',auth()->user()->id)
                ->orwhere('user_id',0);
        })
        ->where('from_folder', InternalMailFolderConstants::RECEIVE) //from danh sach
        
        ->get();
        error_log('---');
        $lstInternalMails = $internalmail_users->map(function ($internalmail_user) {
            $internalmail = $internalmail_user->internalmail;
            $internalmail->star_flg = $internalmail_user->star_flg;
            $internalmail->read_at = $internalmail_user->read_at;
            $internalmail->load('nguoigui');
            if ($internalmail->nguoigui) {
                $internalmail->nguoigui('avatar');
            }
            $internalmail->load('attachedFiles');
            $internalmail->internalmail_user_id = $internalmail_user->id;
            // $internalmail->read_flg = $internalmail_user->read_flg;
            // $internalmail->load('tos');
            // $internalmail->load('bccs');
            // $internalmail->load('ccs');
            return $internalmail;
        });
        $lstInternalMails = collect($lstInternalMails);
            $lstInternalMailsSort = $lstInternalMails->sortByDesc('sent_at');
            $lstInternalMails = collect($lstInternalMailsSort->values()->all());
        return $this->responseJson([
            'status' => 'ok',
            'internalmails' => $lstInternalMails
        ]);
    }
    public function thongke(Request $request) {
         
        /* $internalmail_users = Internalmail_User::where('user_id', auth()->user()->id)
        ->where('from_folder', InternalMailFolderConstants::RECEIVE) //from danh sach
        ->orderBy('created_at')
        ->get();
        $lstInternalMails = $internalmail_users->map(function ($internalmail_user) {
            $internalmail = $internalmail_user->internalmail;
            
            $internalmail->read_at = $internalmail_user->read_at;
            // $internalmail->read_flg = $internalmail_user->read_flg;
            $internalmail->star_flg = $internalmail_user->star_flg;
            //$internalmail->load('attachedFiles');
            return $internalmail;
        });
        $lstInternalMails_Unread = $lstInternalMails->filter(function ($internal_mail
        ) {
            return $internal_mail->read_at == null;
        });
        $total_internalmail = count($lstInternalMails);
        $total_unread_internalmail = count($lstInternalMails_Unread); */
        $total_internalmail = Internalmail_User::where('user_id', auth()->user()->id)
        ->where('from_folder', InternalMailFolderConstants::RECEIVE) //from danh sach
        ->whereNull('read_at')
        ->orderBy('created_at')
        ->get()->count();
        $total_unread_internalmail = Internalmail_User::where('user_id', auth()->user()->id)
        ->where('from_folder', InternalMailFolderConstants::RECEIVE) //from danh sach
        ->whereNull('read_at')
        ->orderBy('created_at')
        ->get()->count();
        return $this->responseJson([
            'status' => 'ok',
            'total_internalmail' => $total_internalmail,
            'total_unread_internalmail' => $total_unread_internalmail,
           // 'ds_internalmails' => $lstInternalMails
        ]);
    }
    public function delete(Request $request){
        error_log($request->lst_internalmail_ids[0]);
        error_log($request->from_folder);
        $lstInternalMails_Ids = $request->lst_internalmail_ids;
        $fromFolder = $request->from_folder;
        if (!isset($lstInternalMails_Ids) || $lstInternalMails_Ids == "") {
            return $this->responseJson([
                'status' => 'ok',
                'message' => 'nothing'
            ]);
        }
        // $lstInternalMails_Ids = explode(',', $lstInternalMails_Ids);
        
        try{
            $from_folder = $request->from_folder;
            DB::transaction(function () use ($lstInternalMails_Ids, $from_folder) {
               // delete all in the internalmail_user
            InternalMail_User::where('user_id', auth()->user()->id)
                ->wherein('internalmail_id', $lstInternalMails_Ids)
                ->where('from_folder', $from_folder)
                ->delete();
                
            }, 3);
        }catch(Exception $e){
            error_log($e->getMessage());
        }
        
        return $this->responseJson([
            'status' => 'ok'
        ]);
    }
    public function recoverDeleted(Request $request){
        $InternalMail_Id = $request->id;
        error_log('------------recoverDeleted----------'.$request->id);
        error_log('------------recoverDeleted----------'.$request->from_folder);

        if (!isset($request->id) || $request->id == "") {
            return $this->responseJson([
                'status' => 'ok',
                'message' => 'nothing'
            ]);
        }
        
        try{
            DB::transaction(function () use ($request) {
                $InternalMail_User= InternalMail_User::withTrashed()
                ->where('user_id', auth()->user()->id)
                ->where('internalmail_id', $request->id)
                ->where('from_folder', $request->from_folder)
                ->restore();
            }, 3);
        }catch(Exception $e){
            error_log($e->getMessage());
        }
        
        return $this->responseJson([
            'status' => 'ok'
        ]);
    }
    public function remove(Request $request){
        $lst_internalmail_user_ids = $request->lst_internalmail_ids;
        error_log('remove:'.count($lst_internalmail_user_ids));
        if (!isset($lst_internalmail_user_ids) || (is_array($lst_internalmail_user_ids) && count($lst_internalmail_user_ids) == 0)) {
            return $this->responseJson([
                'status' => 'ok',
                'message' => 'nothing'
            ]);
        }

        try{
            DB::transaction(function () use ($lst_internalmail_user_ids) {
                 
                //delete all in the internalmail_user
                
                foreach ($lst_internalmail_user_ids as $internalmail_user_id) {
                    error_log('remove 1 id :'.$internalmail_user_id);
                    $internalmail_User = InternalMail_User::withTrashed()
                        ->where('user_id', auth()->user()->id)
                        ->where('id', $internalmail_user_id)
                        ->first();
                    $internalmail_id = null;
                    if (isset($internalmail_User)) {
                        error_log('remove 2 id:'.$internalmail_User->internalmail_id);
                        $internalmail_id = $internalmail_User->internalmail_id;
                        $internalmail_User->forceDelete();
                    }
                    error_log('remove 3 id:'.$internalmail_id);
                    if (isset($internalmail_id) && $internalmail_id != null) {
                        $count_all = internalmail_User::withTrashed()
                        ->where('internalmail_id', $internalmail_id)
                        ->count();
                        error_log('remove 4 id:'.$count_all);
                        if ($count_all == 0) {
                            $internalmail = internalmail::withTrashed()
                                ->where('id', $internalmail_id)
                                ->first();
                                error_log('remove 41');
                            if (isset($internalmail)) {
                                $internalmail->load('attachedFiles');
                                error_log('remove 5 id:'.count($internalmail->attachedFiles));
                               
                                foreach ($internalmail->attachedFiles as $upload_file) {
                                    error_log('remove 6 id:'.$upload_file->id);
                                    \App\Helpers\UploadHelper::deleteFile($upload_file->id);
                                }
                                error_log('remove 7 id:'.$internalmail_id);
                                internalmail::withTrashed()
                                    ->where('id', $internalmail_id)
                                    ->forceDelete();
                            }
                        }
                    }
                    
                }
            }, 3);
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'ng',
                'message' =>$e->getMessage()
            ]);
        }
        
        return $this->responseJson([
            'status' => 'ok'
        ]);


    }
    
    public function recall(Request $request){
        $internalmailId = $request->id;                    
        if (!isset($internalmailId) || $internalmailId == "") {
            return $this->responseJson([
                'status' => 'ok',
                'message' => 'nothing'
            ]);
        }
        $internalmail = InternalMail::find($internalmailId);
        if (!(isset($internalmail) && ($internalmail->creator_id == auth()->user()->id
            || $internalmail->publicer_id == auth()->user()->id)))  {
                return $this->responseJson([
                    'status' => 'ng',
                    'message' => 'Không phải người tạo!'
                ]);
        }

        try{
            $countAll = 0;
            $countDelete = 0;
            DB::transaction(function () use ($internalmail, &$countAll, &$countDelete) {
                $countAll = count(InternalMail_User::where('internalmail_id', $internalmail->id)
                    ->where('from_folder', InternalMailFolderConstants::RECEIVE)->get());
                error_log('---countAll---:'.$countAll);
                $countDelete = InternalMail_User::where('internalmail_id', $internalmail->id)
                    ->where('from_folder', InternalMailFolderConstants::RECEIVE)
                    ->forceDelete();
                    error_log('---countDelete---:'.$countDelete);
                $countDelete = $countAll - count(InternalMail_User::where('internalmail_id', $internalmail->id)
                    ->where('from_folder', InternalMailFolderConstants::RECEIVE)->get());
                error_log('---countDelete1---:'.$countDelete);
                if ($countAll == $countDelete) {
                    $internalmail->status = InternalMailFolderConstants::DRAFT;
                    $internalmail->save();
                    
                    //xoa hết
                    InternalMail_User::where('internalmail_id', $internalmail->id)
                    ->forceDelete();
                    
                    $internalmail_user = new InternalMail_User;
                    $donvi_id = ($this->getLoginedUser() && count($this->getLoginedUser()->donvis) > 0) ? $this->getLoginedUser()->donvis[0]->id : 0;
                    $internalmail_user->internalmail_id= $internalmail->id;
                    $internalmail_user->user_id = auth()->user()->id;
                    $internalmail_user->star_flg = false;
                    $internalmail_user->read_at = Carbon::now();
                    $internalmail_user->donvi_id = $donvi_id;
                    $internalmail_user->from_folder = InternalMailFolderConstants::DRAFT; //draft
                    $internalmail_user->save();
                }                            
            }, 3);
            return $this->responseJson([
                'status' => 'ok',
                'count_all' => $countAll,
                'count_deleted' => $countDelete
            ]);
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'ng',
                'message' => $e->getMessage(),
            ]);
        }
    }

    

    public function getAllStars(Request $request){
        $internalmail_users = InternalMail_User::where('user_id', auth()->user()->id)
        ->where('from_folder', 2) //from danh sach
        ->where('star_flg',1)
        ->orderBy('created_at')
        ->take(500)
        ->get();
        
        $lstInternalMail = $internalmail_users->map(function ($internalmail_user) {
            $mail = $internalmail_user->InternalMail;
            $mail->load('attachedFiles');
            $mail->star_flg = $internalmail_user->star_flg;
            $mail->read_at = $internalmail_user->read_at;
                $mail->internalmail_user_id = $internalmail_user->id;
            return $mail;
        });
        $lstInternalMails = collect($lstInternalMail);
            $lstInternalMailsSort = $lstInternalMails->sortByDesc('created_at');
            $lstInternalMails = collect($lstInternalMailsSort->values()->all());
        return $this->responseJson([
            'status' => 'ok',
            'internalmails' => $lstInternalMail
        ]);
    }

    public function getAllTrashs(Request $request) {
        $page_num = NumUtils::isInt($request->page_num) ? (int)$request->page_num :  1;
        $record_per_page = NumUtils::isInt($request->record_per_page) ? (int) $request->record_per_page :  25;        
        
        $intenalmails_users = InternalMail_User::onlyTrashed()
        ->where('user_id', auth()->user()->id)
        ->orderBy('created_at')
        ->take(500)
        ->get();
        
        $lstInternalMails = $intenalmails_users->map(function ($intenalmails_user) {
            $intenalmails = $intenalmails_user->InternalMail;
            $intenalmails->load('attachedFiles');
            $intenalmails->from_folder = $intenalmails_user->from_folder;
            $intenalmails->star_flg = $intenalmails_user->star_flg;
            $intenalmails->read_at = $intenalmails_user->read_at;
            $intenalmails->internalmail_user_id = $intenalmails_user->id;
            // $intenalmails->read_flg = $intenalmails_user->read_flg;
            //$mail->load('attachedFiles);
            return $intenalmails;
        });
        $lstInternalMails = collect($lstInternalMails);
            $lstInternalMailsSort = $lstInternalMails->sortByDesc('deleted_at');
            $lstInternalMails = collect($lstInternalMailsSort->values()->all());
        return $this->responseJson([
            'status' => 'ok',
            'internalmails' => $lstInternalMails
        ]);
    }
    private function checkMailOfUser($internalmail_id) {
        error_log('checkMailOfUser------'.auth()->user()->id.'------'.$internalmail_id);
        
        $internalmail_users = InternalMail_User::where('user_id', auth()->user()->id)
        ->where('internalmail_id', $internalmail_id)
        ->get();
        error_log('checkMailOfUser11111::::'.count($internalmail_users));
        foreach ($internalmail_users as $internalmail_user) {
            if (isset($internalmail_user->internalmail) && $internalmail_user->internalmail->creator_id == auth()->user()->id &&
            ($internalmail_user->from_folder == InternalMailFolderConstants::DRAFT
            || $internalmail_user->from_folder == InternalMailFolderConstants::SENT)) {
                return $internalmail_user->internalmail;
            }
        }
        error_log('checkMailOfUser2222');
        $internalmail = InternalMail::find($internalmail_id);
        if (isset($internalmail) &&$internalmail->creator_id == auth()->user()->id) {
            return $internalmail_id;
        }
        error_log('checkMailOfUser3333');
        return null;
    }
}
