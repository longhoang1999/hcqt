<?php

namespace App\Http\Controllers;
use App\Http\Requests\GroupRequest;
use App\Models\Group;
use App\Models\Group_User;
use App\Models\User;
use App\Models\Chat;
use App\Common\Constant\ChattingConstants;
use App\Services\ChatService;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroupController extends Base\BaseController
{
    public function __construct(){

    }
    public function importGroups(){

    }
    public function insertOrUpdate(Request $request){
        try{
            $id = $request->id;
            $bytes = random_bytes(5);
            $ten_nhom = $request->ten_nhom;
            $ma_nhom = bin2hex($bytes);
            $thanhvien_ids = isset($request->thanhvien_ids) && is_array($request->thanhvien_ids) ? $request->thanhvien_ids : [];
            $mo_ta = $request->mo_ta;
            $isCreate = false;
            if (isset($id)) {
                $group = Group::find($id);
            }
            if (!isset($group)) {
                $group = new Group();
                $groups_by_manhom = Group::where('ma_nhom',$ma_nhom)->where('id','!=',$id)->get();
                if (count($groups_by_manhom) > 0) {
                    $bytes = random_bytes(5);
                    $ma_nhom = bin2hex($bytes);
                }
                $group->ma_nhom = $ma_nhom;
                $isCreate = true;
            }
            $group->ten_nhom = $ten_nhom;
            $group->mo_ta = $mo_ta;
            $group->vanban_id = $request->vanban_id;
            $group->truong_nhom_id = $request->truong_nhom_id;
/*
            if (!isset($request->isLockChat) || $request->isLockChat == false) {
                $group->group_chat_lock_at = null;
            } else if (isset($request->group_chat_lock_at)) {
                $group->group_chat_lock_at = $request->group_chat_lock_at;
            } else {
                $group->group_chat_lock_at = Carbon::now();
            }
*/
            $member_kick_at_message = "";
            $member_re_add_message = "";
            $member_add_message = "";
            $lstDeleteUser = array();
            $lstNewUserIds = \collect();
            DB::transaction(function () use (&$group, $thanhvien_ids, &$lstDeleteUser, &$lstNewUserIds){
                $group->save();

                $lstDeleteUser = Group_User::where('group_id',$group->id)
                ->whereNotIn('user_id', $thanhvien_ids)
                ->get();   
                $lstGroupUsers = array();       
                foreach ($thanhvien_ids as $memberId) {
                    $hasUser = Group_User::where('group_id',$group->id)
                        ->where('user_id', $memberId)
                        ->first(); 
                    if (!isset($hasUser)) {
                        $lstNewUserIds->push($memberId);
                        $lstGroupUsers[] = [
                            'group_id' => $group->id,
                            'user_id' => $memberId,
                        ];
                    }                 
                }  
                if (count($lstGroupUsers) > 0) {
                    Group_User::insert($lstGroupUsers);
                }

            },3);
            //create nhom chat
            try{
                foreach($lstDeleteUser as $delUser) {
                    if ($member_kick_at_message == "") {
                        $member_kick_at_message = $delUser->user->full_name;
                    } else {
                        $member_kick_at_message = $member_kick_at_message."; ".$delUser->user->full_name;
                    }
                    
                }
                if (isset($lstNewUserIds) && count($lstNewUserIds) > 0) {
                    $lstUsers = User::whereIn('id', $lstNewUserIds)->get();
                    foreach($lstUsers as $user) {
                        if($member_add_message == "") {
                            $member_add_message = $user->full_name;
                        } else {
                            $member_add_message = $member_add_message."; ".$user->full_name;
                        }
                    }
                }
                $lstChatAdmins = [];
                array_push($lstChatAdmins, $group->truong_nhom_id);
                ChatService::createOrUpdateGroupChat($group, $lstChatAdmins, ChattingConstants::GROUP_CHAT_TYPE_WORK_GROUP,
                $lstDeleteUser, $member_kick_at_message, $member_re_add_message, $member_add_message);

            }catch(Exception $e){
            }

            return [
                'status' => 'ok',
                'group' => $group
            ];
        }catch(Exception $e){
            return [
                'status' => 'ng',
                'message' => $e->getMessage()
            ];
        }
    }

    public function getGroups(Request $request){
        $groups = Group::orderBy('ten_nhom')->get()->load('users')->load('vanban');
        foreach($groups as $group) {
            $group->users->load('donvis');
        }
        return [
            'status' => 'ok',
            'groups' => $groups
        ];
    }
    public function getAuthGroups (Request $request) {
        $user = User::find(auth()->user()->id)->load('groups');
        $groups = $user->groups;
        return [
            'status' => 'ok',
            'groups' => $groups
        ];
    }
    public function delete(Request $request){
        error_log('DeleteGroup---------GroupId:'.$request->group_id);
        $group_id = $request->group_id;
        if (!isset($group_id)){
            return [
                'status' => 'ng', //false
                'err_code' => 2, //group_id không tồn tại
                'message' => 'Không tồn tại nhóm người dùng đang thực hiện'
            ];
        }
        Group_User::where('group_id',$group_id)->forceDelete();
        Group::where('id',$group_id)->forceDelete();
        return [
            'status' => 'ok', //false
        ];
    }

    public function registerGroupChat($request) {
        try{
            $group = Group::find($request->id);
            if (!isset($group)) {
                throw new \Exception('Khong tim t hay Group');
            }
            $lstChatAdmins = [];
            array_push($lstChatAdmins, $group->truong_nhom_id);
            $group_chat = ChatService::createOrUpdateGroupChat($group, $lstChatAdmins, ChattingConstants::GROUP_CHAT_TYPE_WORK_GROUP,
            [], "", "", "");
            if (!isset($group_chat)) {
                throw new \Exception('Khong tao duoc nhom chat');
            }
            return [
                'status' => 'ok',
                'group_chat' => $group_chat
            ];
        } catch(Exception $e){
            return [
                'status' => 'ng',
            ];
        }
    }
    public function registerAllGroupChats($request) {
        try{
            $groups = Group::All();
            foreach($groups as $group) {
                try{
                    $lstChatAdmins = [];
                    array_push($lstChatAdmins, $group->truong_nhom_id);
                    ChatService::createOrUpdateGroupChat($group, $lstChatAdmins, ChattingConstants::GROUP_CHAT_TYPE_WORK_GROUP,
                    [], "", "", "");
                }catch(Exception $e){
                    error_log($e->getMessage());
                }
            }
            return [
                'status' => 'ok',
            ];
        } catch(Exception $e){
            return [
                'status' => 'ng',
            ];
        }
    }
}
