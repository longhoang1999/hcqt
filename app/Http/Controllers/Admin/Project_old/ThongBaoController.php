<?php

namespace App\Http\Controllers;

use App\Common\Constant\ThongBaoFolderConstants;
use App\Common\Constant\ThongBaoStatusConstants;

use App\Common\Constant\UploadFileConstants;
use App\Helpers\UploadHelper;
use App\Http\Requests\ThongBaoRequest;
use App\Models\DonVi;
use App\Models\Group;
use App\Models\ThongBao;
use App\Models\ThongBaoComment;
use App\Models\ThongBao_LinhVuc;
use App\Models\ThongBao_User;
use App\Models\UploadFile;
use App\Models\User;
use App\Utils\NumUtils;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Http\Request;

class ThongBaoController extends Base\BaseController
{
    public function __construct(){

    }
    
    public function publish(ThongBaoRequest $request){
        if(isset($request->id) && $request->id > 0) {
            //da draft
            $thongbao = $this->checkThongBaoOfUser($request->id);
            if ($thongbao == null) {
                return $this->responseJson([
                'status' => 'ng',
                'err_code' => '1' , //danh sach to (receivers) bi rong
                'message' => 'Thông báo bị xóa hoặc bạn ko phải là người tạo thông báo này!',
                ]);
            }
        }
        if (!isset($thongbao)) {
            error_log('---------------publish thong bao --- khong thay');
            $thongbao = new ThongBao;
        }
        error_log('---------------publish thong bao --- 1-----'.count($request->receiver_ids));
        
        if (is_array($request->receiver_ids)) {
            $thongbao->receiver_ids = $request->receiver_ids;
        } else {
            $thongbao->receiver_ids = explode(",", $request->receiver_ids);
        }

        error_log('---------------publish thong bao --- 1.5-----'.count($request->linhvuc_ids));
        
        if (is_array($request->linhvuc_ids)) {
            $thongbao->linhvuc_ids = $request->linhvuc_ids;
        } else {
            $thongbao->linhvuc_ids = explode(",", $request->linhvuc_ids);
        }
        // if (is_array($request->linhvuc_ids)) {
        //     error_log('asssss');
        //     $thongbao->linhvuc_ids = $request->linhvuc_ids;
        // } else {
        //     error_log('bbbbbbbbbb');
        //     $thongbao->linhvuc_ids = explode(",", $request->linhvuc_ids);
        // }
        $lstReceiver_ids = $thongbao->receiver_ids;
        // error_log($request);
        if (!isset($lstReceiver_ids) || count($lstReceiver_ids) == 0 || !is_array($lstReceiver_ids)){
        error_log('---------------publish thong bao --- 1.5');
        return $this->responseJson([
                'status' => 'ng',
                'err_code' => '1' , //danh sach to (receivers) bi rong
                'message' => 'Danh sách không được rỗng',
                ]);
        }
        $thongbao->subject = $request->subject;
        $thongbao->keyword = $request->keyword;
        $thongbao->status = ThongBaoStatusConstants::PUBLISH; //1: publish
        $thongbao->body = $request->body;
        $thongbao->creator_id = auth()->user()->id;
        $thongbao->publicer_id = auth()->user()->id;
        $thongbao->public_at = Carbon::now();
        $thongbao->receiver_ids = $lstReceiver_ids;
        // add list reveice to thongbao_users tables
        $lstReceivers = array();
        error_log('---------------publish thong bao --- 2');
        try{
            DB::transaction(function () use ($request, $thongbao, $lstReceiver_ids) {
            $thongbao->save();
        //process attach file at other process
        error_log('---------------publish thong bao --- 3');
                
                 $lstThongBao_User = array();
                 if ($lstReceiver_ids[0]===0){
                     error_log('Gửi tất cả');
                     $list_users = User::get();
                    $list_userIds = $list_users->map(function($user){
                        return $user->id;
                    });
                    foreach ($list_userIds as $receiverId) {
                        $lstThongBao_User[$receiverId] = [
                            'thongbao_id' => $thongbao->id,
                            'user_id' => $receiverId,
                            'donvi_id' => 0,
                            'star_flg' => false,
                            'read_at' => null,
                            'from_folder' => ThongBaoFolderConstants::RECEIVE // danh sach receive
                         ];
                    }
                 }else {
                    error_log("thong bao id: ".$thongbao->id);
                    $lstReceiver_user_ids = array_filter($lstReceiver_ids, function($user_id) {
                        
                        return count(explode('_', $user_id)) <= 1;
                    });
                    
                    foreach ($lstReceiver_user_ids as $receiverId) {
                        error_log("receiverId: ".$receiverId);
                        if (array_key_exists($receiverId, $lstThongBao_User)){
                            continue;
                        }
                        $lstThongBao_User[$receiverId] = [
                            'thongbao_id' => $thongbao->id,
                            'user_id' => $receiverId,
                            'donvi_id' => 0,
                            'star_flg' => false,
                            'read_at' => null,
                            'from_folder' => ThongBaoFolderConstants::RECEIVE // danh sach receive
                        ];
                    }
                            
                    
                    //don vi
                    error_log('don vi');
                    $lstDonvi_Receiver_mas = array_filter($lstReceiver_ids, function($user_id) {
                        return ((count(explode('_', $user_id)) > 1) && explode('_', $user_id)[0] == 'dv');
                    });
                    $lstDonvi_Receiver_ids = array_map(function($user_id) {
                        if (strstr($user_id,'_', true) == 'dv') {
                            $donvi_Id = explode('_', $user_id)[1];
                        }
                        error_log("explode donvi id: ".$donvi_Id);
                        return $donvi_Id;
                    },  $lstDonvi_Receiver_mas);
                    error_log("so lương donvi : ".strval(count($lstDonvi_Receiver_ids)));
                    $lstDonvi_Receiver = DonVi::whereIn('id', $lstDonvi_Receiver_ids)
                    ->get()
                    ->load('users');
                    foreach ($lstDonvi_Receiver as $donvi) {
                        $lstUsers = $donvi->users;
                        if ($lstUsers->count() > 0) {
                            foreach ($lstUsers as $user) {
                                if (array_key_exists($user->id, $lstThongBao_User)){
                                    continue;
                                }
                                $lstThongBao_User[$user->id] = [
                                'thongbao_id' => $thongbao->id,
                                'user_id' => $user->id,
                                'donvi_id' => $donvi->id,
                                'star_flg' => false,
                                'read_at' => null,
                                'from_folder' => ThongBaoFolderConstants::RECEIVE // danh sach receive
                                ];
                            }
                            
                        }
                        
                    } 
                    //group
                    error_log('group');
                    $lstGroup_Receiver_mas = array_filter($lstReceiver_ids, function($user_id) {
                        return ((count(explode('_', $user_id)) > 1) && explode('_', $user_id)[0] == 'gr');
                    });
                    $lstGroup_Receiver_ids = array_map(function($user_id) {
                        if (strstr($user_id,'_', true) == 'gr') {
                            $group_Id = explode('_', $user_id)[1];
                        }
                        error_log("explode group id: ".$group_Id);
                        return $group_Id;
                    },  $lstGroup_Receiver_mas);
                    error_log("so lương grop : ".strval(count($lstGroup_Receiver_ids)));
                    $lstGroup_Receiver = Group::whereIn('id', $lstGroup_Receiver_ids)
                    ->get()
                    ->load('users');
                    foreach ($lstGroup_Receiver as $group) {
                        $lstUsers = $group->users;
                        if ($lstUsers->count() > 0) {
                            foreach ($lstUsers as $user) {
                                if (array_key_exists($user->id, $lstThongBao_User)){
                                    continue;
                                }
                                $lstThongBao_User[$user->id] = [
                                'thongbao_id' => $thongbao->id,
                                'user_id' => $user->id,
                                'donvi_id' => 0,
                                'star_flg' => false,
                                'read_at' => null,
                                'from_folder' => ThongBaoFolderConstants::RECEIVE // danh sach receive
                                ];
                            }
                        }
                    } 
                 }
                 
                //array_push($lstThongBao_User, $thongbao_users);
                 
                $lstPublishUser = ThongBao_User::withTrashed()
                    ->where('thongbao_id', $thongbao->id)
                    ->get();
                error_log('Thong bao id: '.$request->id."---:".$thongbao->id."----:".count($lstPublishUser));
                $hasPublishUser = false;
                foreach ($lstPublishUser as $thongbao_user) {
                    //creator
                    //error_log('update ----1-:'.$thongbao_user->user_id."----:".auth()->user()->id);
                    if ($thongbao_user->user_id == auth()->user()->id &&
                         ($thongbao_user->from_folder == ThongBaoFolderConstants::DRAFT
                            || $thongbao_user->from_folder == ThongBaoFolderConstants::PUBLISH)) {
                        
                        $hasPublishUser = true;
                        $thongbao_user->from_folder = ThongBaoFolderConstants::PUBLISH;
                        
                        $thongbao_user->save();
                    } else {
                        //danh sách gửi: không gửi user đã gửi
                        if (array_key_exists($thongbao_user->user_id, $lstThongBao_User)){
                            unset($lstThongBao_User[$thongbao_user->user_id]);
                        }
                    }
                }
                if (!$hasPublishUser) {
                    error_log('create cho creator');
                    $donvi_id = ($this->getLoginedUser() && count($this->getLoginedUser()->donvis) > 0) ? $this->getLoginedUser()->donvis[0]->id : 0;
                    $lstThongBao_User[] = [
                        'thongbao_id' => $thongbao->id,
                        'user_id' => auth()->user()->id,
                        'donvi_id' => $donvi_id,
                        'star_flg' => false,
                        'read_at' => Carbon::now(),
                        'from_folder' => ThongBaoFolderConstants::PUBLISH // danh sach public
                    ];
                }
                error_log('insert cho creator');
                if (count($lstThongBao_User) > 0) {
                    ThongBao_User::insert($lstThongBao_User);
                }
                ThongBao_LinhVuc::where('thongbao_id',$thongbao->id)->forceDelete();
                    $lstGroupUsers = array();
                    if ($request->linhvuc_ids !=null){
                        foreach ($request->linhvuc_ids as $linhvuc_id) {
                            $lstGroupUsers[] = [
                                'thongbao_id' => $thongbao->id,
                                'linhvuc_id' => $linhvuc_id,
                            ];
                        }
                }
                ThongBao_LinhVuc::insert($lstGroupUsers);
            }, 3);
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => '1', //danh sach to (receivers) bi rong
                'message' => $e->getMessage()
                ]);
        }
        
        return $this->responseJson([
            'status' => 'ok',
            'thongbao' => $thongbao
            ]);
    }
    public function draft(ThongBaoRequest $request){
        //error_log('---------------draft thong bao--------------------'.$request->receiver_ids);
       
        if(isset($request->id)) {
            //da draft
            $thongbao = ThongBao::find($request->id);
        }
        if (!isset($thongbao)) {
            $thongbao = new ThongBao;
        }
        $thongbao->subject = $request->subject;
        $thongbao->keyword = $request->keyword;
        $thongbao->status = ThongBaoStatusConstants::DRAFT; //0: draft
        $thongbao->body = $request->body;
        $thongbao->creator_id = auth()->user()->id;
        if (is_array($request->receiver_ids)) {
            $thongbao->receiver_ids = $request->receiver_ids;
        } else {
            $thongbao->receiver_ids = explode(",", $request->receiver_ids);
        }
        if (is_array($request->linhvuc_ids)) {
            $thongbao->linhvuc_ids = $request->linhvuc_ids;
        } else {
            $thongbao->linhvuc_ids = explode(",", $request->linhvuc_ids);
        }
        try{
            DB::transaction(function () use ($request, $thongbao) {
                $thongbao->save();
                
                //process attach file after save at other process
                error_log('0');
                $thongbao_user = ThongBao_User::where('thongbao_id', $request->id)
                ->where('user_id', auth()->user()->id)
                ->first();
                if (!isset($thongbao_user)) {
                    $thongbao_user = new ThongBao_User;
                }
                $donvi_id = ($this->getLoginedUser() && count($this->getLoginedUser()->donvis) > 0) ? $this->getLoginedUser()->donvis[0]->id : 0;
                $thongbao_user->thongbao_id= $thongbao->id;
                $thongbao_user->user_id = auth()->user()->id;
                $thongbao_user->star_flg = false;
                $thongbao_user->read_at = Carbon::now();
                $thongbao_user->donvi_id = $donvi_id;
                $thongbao_user->from_folder = ThongBaoFolderConstants::DRAFT; //draft
                $thongbao_user->save();
                ThongBao_LinhVuc::where('thongbao_id',$thongbao->id)->forceDelete();
                    $lstThongBaoLinhVucs = array();
                    if ($request->linhvuc_ids !=null){
                        foreach ($request->linhvuc_ids as $linhvuc_id) {
                            $lstThongBaoLinhVucs[] = [
                                'thongbao_id' => $thongbao->id,
                                'linhvuc_id' => $linhvuc_id,
                            ];
                        }
                }
                ThongBao_LinhVuc::insert($lstThongBaoLinhVucs);
            }, 3);
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => '1', //danh sach to (receivers) bi rong
                'message' => $e->getMessage()
                ]);
        }
        
        return $this->responseJson([
            'status' => 'ok',
            'thongbao' => $thongbao
            ]);
    }

    public function star(Request $request){
        
        if(isset($request->id)) {
            //da draft
            if (isset($request->from_folder) && $request->from_folder != "") {
                $from_folder = is_numeric($request->from_folder) ? $request->from_folder : 999;
                $thongbao_user = ThongBao_User::where('thongbao_id', $request->id)
                ->where('user_id', auth()->user()->id)
                ->where('from_folder', $from_folder)
                ->first();
            } else {
                $thongbao_user = ThongBao_User::where('thongbao_id', $request->id)
                ->where('user_id', auth()->user()->id)
                ->first();
            }
            
            if (isset($thongbao_user)) {
                $thongbao_user->star_flg = $request->star_flg;
                $thongbao_user->save();
            }
            return $this->responseJson([
                'status' => "ok",
                'thongbao' => $thongbao_user
                ]);
        }
        return $this->responseJson([
            'status' => "ng",
            'thongbao' => null,
            'message' => 'Unknow'
            ]);
    }
    public function read(Request $request){
        error_log("read: ".$request->id);
        
        if(isset($request->id)) {
            //da draft
            if (isset($request->from_folder) && $request->from_folder != "") {
                $from_folder = is_numeric($request->from_folder) ? $request->from_folder : 999;
                $thongbao_user = ThongBao_User::withTrashed()
                    ->where('thongbao_id', $request->id)
                    ->where('user_id', auth()->user()->id)
                    ->where('from_folder', $from_folder)
                    ->first();
            } else {
                $thongbao_user = ThongBao_User::withTrashed()
                    ->where('thongbao_id', $request->id)
                    ->where('user_id', auth()->user()->id)
                    ->first();
            }

            if (isset($thongbao_user)) {
                $thongbao_user->read_at = isset($request->read_flg) && $request->read_flg ? Carbon::now() : null;
                // $thongbao_user->save();
                try{
                    $thongbao_user->save();
                }
                catch(Exception $e){
                    error_log($e->getMessage());}
            }
            return $this->responseJson([
                'status' => "ok",
                'thongbao' => $thongbao_user->thongbao
                ]);
        }
        return $this->responseJson([
            'status' => "ng",
            'thongbao' => null,
            'message' => 'Unknow'
            ]);
    }

    public function getThongBao(Request $request) {
        if (isset($request->from_folder) && $request->from_folder != "") {
            $from_folder = is_numeric($request->from_folder) ? $request->from_folder : 999;
            $thongbao_users = ThongBao_User::withTrashed()
                ->where('thongbao_id', $request->id)
                ->where('user_id', auth()->user()->id)
                ->where('from_folder', $from_folder)
                ->get();
        } else {
            $thongbao_users = ThongBao_User::withTrashed()
                ->where('thongbao_id', $request->id)
                ->where('user_id', auth()->user()->id)
                ->get();
        }
        error_log('getThongBao --- count:'.count($thongbao_users));
        if (isset($thongbao_users) && count($thongbao_users)>0) {
            $thongbao = ThongBao::find($thongbao_users[0]->thongbao_id);
            if (isset($thongbao)) {
                $thongbao->load('attachedFiles');
                $thongbao->load('receivers');
                $thongbao->load('linhvuc');
                $thongbao->receivers->load('donvis');
                $thongbao->receivers->load('roles');
                $thongbao->creator = User::find($thongbao->creator_id);
                $thongbao->publicer = User::find($thongbao->publicer_id);
                $thongbao->thongbao_user_id = $thongbao_users[0]->id;
            }
            return $this->responseJson([
                'status' => 'ok',
                'thongbao' => $thongbao,
                'thongbao_user' => $thongbao_users[0]
                ]);
        }
        return $this->responseJson([
            'thongbao' => null,
            'thongbao_user' => null
            ]);
    }
    public function getAllDrafts(Request $request) {
        error_log('getAllDrafts----------------'.auth()->user()->id.'------'.ThongBaoFolderConstants::DRAFT);
        $thongbao_users = ThongBao_User::where('user_id', auth()->user()->id)
        ->where('from_folder', ThongBaoFolderConstants::DRAFT) //from draft
        ->orderBy('created_at', 'DESC')
        ->take(1000)
        ->get();
        $lstThongBaos = $thongbao_users->map(function ($thongbao_user) {
            $thongbao = $thongbao_user->thongbao;
            $thongbao->read_at = $thongbao_user->read_at;
            $thongbao->from_folder = $thongbao_user->from_folder;
            $thongbao->thongbao_user_id = $thongbao_user->id;
            $thongbao->load('attachedFiles');
            $thongbao->load('linhvuc');
            return $thongbao;
        });
        
        return $this->responseJson([
            'status' => 'ok',
            'ds_thongbao' => $lstThongBaos
        ]);
    }
    
    public function getAllPublish(Request $request) {
        $thongbao_users = ThongBao_User::where('user_id', auth()->user()->id)
        ->where('from_folder', ThongBaoFolderConstants::PUBLISH) //from publish
       
        ->take(500)
        ->get();
        
        $lstThongBaos = $thongbao_users->map(function ($thongbao_user) {
            $thongbao = $thongbao_user->thongbao;
            $thongbao->star_flg = $thongbao_user->star_flg;
            $thongbao->read_at = $thongbao_user->read_at;
            $thongbao->from_folder = $thongbao_user->from_folder;
            $thongbao->thongbao_user_id = $thongbao_user->id;
            $thongbao->load('attachedFiles');
            $thongbao->load('linhvuc');
            return $thongbao;
        });
        
        $lstThongBaos = collect($lstThongBaos);
        $lstThongBaosSort = $lstThongBaos->sortByDesc('public_at');
        $lstThongBaos = collect($lstThongBaosSort->values()->all());
        // error_log('-------------------'.$lstThongBaosSort);
        return $this->responseJson([
            'status' => 'ok',
            'ds_thongbao' => $lstThongBaos
        ]);
    }
    /**
     *  */
    public function getAlls(Request $request) {
        // $page_num = NumUtils::isInt($request->page_num) ? (int)$request->page_num :  1;
        // $record_per_page = NumUtils::isInt($request->record_per_page) ? (int) $request->record_per_page :  25;        
        
        $thongbao_users = ThongBao_User::where('user_id', auth()->user()->id)
        ->where('from_folder', ThongBaoFolderConstants::RECEIVE) //from danh sach
        ->take(1000)
        ->get();
        $lstThongBaos = array();
        foreach ($thongbao_users as $thongbao_user) {
            $thongbao = $thongbao_user->thongbao;
            $thongbao->load('attachedFiles');
            $thongbao->load('linhvuc');
            $thongbao->star_flg = $thongbao_user->star_flg;
            $thongbao->read_at = $thongbao_user->read_at;
            $thongbao->from_folder = $thongbao_user->from_folder;
            $thongbao->thongbao_user_id = $thongbao_user->id;
            $thongbao->publicer = User::find($thongbao->publicer_id);
            array_push($lstThongBaos, $thongbao);
        }
        // $lstThongBaos = $thongbao_users->map(function ($thongbao_user) {
        //     $thongbao = $thongbao_user->thongbao;
        //     $thongbao->load('receivers');
        //     $thongbao->load('attachedFiles');
        //     $thongbao->star_flg = $thongbao_user->star_flg;
        //     $thongbao->read_at = $thongbao_user->read_at;
        //     $thongbao->from_folder = $thongbao_user->from_folder;
        //     $thongbao->thongbao_user_id = $thongbao_user->id;
        //     $thongbao->publicer = User::find($thongbao->publicer_id);
        //     return $thongbao;
        // });
        $lstThongBaos = collect($lstThongBaos);
        $lstThongBaosSort = $lstThongBaos->sortByDesc('public_at');
        $lstThongBaos = collect($lstThongBaosSort->values()->all());
        return $this->responseJson([
            'status' => 'ok',
            'ds_thongbao' => $lstThongBaos
        ]);
    }
    public function getAllTrashs(Request $request) {
        $page_num = NumUtils::isInt($request->page_num) ? (int)$request->page_num :  1;
        $record_per_page = NumUtils::isInt($request->record_per_page) ? (int) $request->record_per_page :  25;        
        
        $thongbao_users = ThongBao_User::onlyTrashed()
        ->where('user_id', auth()->user()->id)
        ->take(1000)
        ->get();
        
        $lstThongBaos = $thongbao_users->map(function ($thongbao_user) {
            $thongbao = $thongbao_user->thongbao;
            $thongbao->load('attachedFiles');
            $thongbao->load('linhvuc');
            $thongbao->star_flg = $thongbao_user->star_flg;
            $thongbao->read_at = $thongbao_user->read_at;
            $thongbao->from_folder = $thongbao_user->from_folder;
            $thongbao->thongbao_user_id = $thongbao_user->id;
            $thongbao->tb_user_deleted_at = $thongbao_user->deleted_at;
            $thongbao->publicer = User::find($thongbao->publicer_id);
            $thongbao->creator = User::find($thongbao->creator_id);
            return $thongbao;
        });
        $lstThongBaos = collect($lstThongBaos);
        $lstThongBaosSort = $lstThongBaos->sortByDesc('tb_user_deleted_at');
        $lstThongBaos = collect($lstThongBaosSort->values()->all());
        return $this->responseJson([
            'status' => 'ok',
            'ds_thongbao' => $lstThongBaos
        ]);
    }
    
    public function getAllStars(){
        $thongbao_users = ThongBao_User::where('user_id', auth()->user()->id)
        ->where('from_folder', 2) //from danh sach
        ->where('star_flg', 1)
        ->orderBy('created_at')
        ->take(500)
        ->get();
        
        $lstThongBaos = $thongbao_users->map(function ($thongbao_user) {
            $thongbao = $thongbao_user->thongbao;
            $thongbao->load('attachedFiles');
            $thongbao->load('linhvuc');
            $thongbao->star_flg = $thongbao_user->star_flg;
            $thongbao->read_at = $thongbao_user->read_at;
            $thongbao->from_folder = $thongbao_user->from_folder;
            $thongbao->thongbao_user_id = $thongbao_user->id;
            $thongbao->publicer = User::find($thongbao->publicer_id);
            return $thongbao;
        });
        $lstThongBaos = collect($lstThongBaos);
        $lstThongBaosSort = $lstThongBaos->sortByDesc('public_at');
        $lstThongBaos = collect($lstThongBaosSort->values()->all());
        return $this->responseJson([
            'status' => 'ok',
            'ds_thongbao' => $lstThongBaos
        ]);
    }

    public function thongke() {
        /* $thongbao_users = ThongBao_User::where('user_id', auth()->user()->id)
        ->where('from_folder', ThongBaoFolderConstants::RECEIVE) //from danh sach
        ->orderBy('created_at')
        ->get();
        $lstThongBaos = $thongbao_users->map(function ($thongbao_user) {
            $thongbao = $thongbao_user->thongbao;

            $thongbao->read_at = $thongbao_user->read_at;
            return $thongbao;
        });
        $lstThongBao_Unreads = $lstThongBaos->filter(function ($thongbao) {
            return $thongbao->read_at == null;
        });
        $total_thongbaos = count($lstThongBaos);
        $total_unread_thongbaos = count($lstThongBao_Unreads); */
        $total_thongbaos = ThongBao_User::where('user_id', auth()->user()->id)
        ->where('from_folder', ThongBaoFolderConstants::RECEIVE) //from danh sach
        ->orderBy('created_at')
        ->get()->count();
        $total_unread_thongbaos = ThongBao_User::where('user_id', auth()->user()->id)
        ->where('from_folder', ThongBaoFolderConstants::RECEIVE) //from danh sach
        ->whereNull('read_at')
        ->orderBy('created_at')
        ->get()->count();
        return $this->responseJson([
            'status' => 'ok',
            'total_thongbao' => $total_thongbaos,
            'total_unread_thongbao' => $total_unread_thongbaos,
           // 'ds_thongbaos' => $lstThongBaos
        ]);
    }
    public function delete(Request $request){
        $lstThongBao_Ids = $request->lst_thongbao_ids;
        
        if (!isset($lstThongBao_Ids) || (is_array($lstThongBao_Ids) && count($lstThongBao_Ids) == 0)) {
            return $this->responseJson([
                'status' => 'ok',
                'message' => 'nothing'
            ]);
        }
        
        try{
            $from_folder = $request->from_folder;
            DB::transaction(function () use ($lstThongBao_Ids, $from_folder) {
                //move all thong bao of the user into thongbao_user_deleted tables
                
                // and then delete all in the thongbao_user
               // error_log('delete ThongBao_User:'.count($lstThongBao_Ids)."---:".auth()->user()->id);
                $lst = ThongBao_User::where('user_id', auth()->user()->id)
                ->whereIn('thongbao_id', $lstThongBao_Ids)
                ->where('from_folder', $from_folder)
                ->delete();
                
            }, 3);
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'ng',
                'message'=>$e->getMessage()
            ]);
        }
        
        return $this->responseJson([
            'status' => 'ok'
        ]);
    }
    public function recoverDeleted(Request $request){
        
        if (!isset($request->id) || $request->id == "") {
            return $this->responseJson([
                'status' => 'ok',
                'message' => 'nothing'
            ]);
        }
        
        try{
            DB::transaction(function () use ($request) {
               
                ThongBao_User::withTrashed()
                ->where('user_id', auth()->user()->id)
                ->where('thongbao_id', $request->id)
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
        $lst_thongbao_user_ids = $request->lst_thongbao_ids;
        error_log('remove:'.count($lst_thongbao_user_ids));
        if (!isset($lst_thongbao_user_ids) || (is_array($lst_thongbao_user_ids) && count($lst_thongbao_user_ids) == 0)) {
            return $this->responseJson([
                'status' => 'ok',
                'message' => 'nothing'
            ]);
        }
        // $lstThongBao_Ids = explode(',', $lstThongBao_Ids);
        
        try{
            DB::transaction(function () use ($lst_thongbao_user_ids) {
                 
                //delete all in the thongbao_user
                
                foreach ($lst_thongbao_user_ids as $thongbao_user_id) {
                    error_log('remove 1 id :'.$thongbao_user_id);
                    $thongBao_User = ThongBao_User::withTrashed()
                        ->where('user_id', auth()->user()->id)
                        ->where('id', $thongbao_user_id)
                        ->first();
                    $thongbao_id = null;
                    if (isset($thongBao_User)) {
                        error_log('remove 2 id:'.$thongBao_User->thongbao_id);
                        $thongbao_id = $thongBao_User->thongbao_id;
                        $thongBao_User->forceDelete();
                    }
                    error_log('remove 3 id:'.$thongbao_id);
                    if (isset($thongbao_id) && $thongbao_id != null) {
                        $count_all = ThongBao_User::withTrashed()
                        ->where('thongbao_id', $thongbao_id)
                        ->count();
                        error_log('remove 4 id:'.$count_all);
                        if ($count_all == 0) {
                            $thongbao = ThongBao::withTrashed()
                                ->where('id', $thongbao_id)
                                ->first();
                                error_log('remove 41');
                            if (isset($thongbao)) {
                                $thongbao->load('attachedFiles');
                                $thongbao->load('linhvuc');
                                error_log('remove 5 id:'.count($thongbao->attachedFiles));
                               
                                foreach ($thongbao->attachedFiles as $upload_file) {
                                    error_log('remove 6 id:'.$upload_file->id);
                                    \App\Helpers\UploadHelper::deleteFile($upload_file->id);
                                }
                                foreach($thongbao->linhvuc as $linhvuc){
                                    ThongBao_LinhVuc::where('id', $linhvuc->id)->delete();
                                }
                                error_log('remove 7 id:'.$thongbao_id);
                                ThongBao::withTrashed()
                                    ->where('id', $thongbao_id)
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
    public function comment(Request $request){
        error_log('comment: '.$request->thongbao_id);
        error_log('comment: '.$request->comment);
        if (isset($request->id)) {
            $comment = ThongBaoComment::find($request->id);
        }
        if (!isset($comment)){
            $comment = new ThongBaoComment();
        }
        $comment->thongbao_id = $request->thongbao_id;
        $comment->comment = $request->comment;
        $comment->nguoitao_id = auth()->user()->id;
        $comment->save();
        return $this->responseJson([
            'status' => 'ok',
            'comment'=> $comment
        ]);
    }
    public function getComments (Request $request) {
        error_log($request->thongbao_id);
        $list_comment = ThongBaoComment::where('thongbao_id',$request->thongbao_id)->orderBy('created_at', 'desc')->get()->load('nguoitao');
        // foreach ($list_comment as $comment) {
        //     $comment->nguoitao->load('donvis');
        //     $comment->nguoitao->load('roles');
        // }
        return $this->responseJson([
            'status' => 'ok',
            'list_comment'=> $list_comment
        ]);
    }
    public function reCall(Request $request){
        error_log('reCall: '.$request->id);
        $thongBaoId = $request->id;                    
        if (!isset($thongBaoId) || $thongBaoId == "") {
            return $this->responseJson([
                'status' => 'ok',
                'message' => 'nothing'
            ]);
        }
        $thongbao = ThongBao::find($thongBaoId);
        if (!(isset($thongbao) && ($thongbao->creator_id == auth()->user()->id
            || $thongbao->publicer_id == auth()->user()->id)))  {
                return $this->responseJson([
                    'status' => 'ng',
                    'message' => 'Không phải người tạo!'
                ]);
        }
        
        try{
            $countAll = 0;
            $countDelete = 0;
            DB::transaction(function () use ($thongbao, &$countAll, &$countDelete) {
                $countAll = count(ThongBao_User::where('thongbao_id', $thongbao->id)
                    ->where('from_folder', ThongBaoFolderConstants::RECEIVE)->get());
                
                $countDelete = ThongBao_User::where('thongbao_id', $thongbao->id)
                    ->where('from_folder', ThongBaoFolderConstants::RECEIVE)
                    ->forceDelete();
                $countDelete = $countAll - count(ThongBao_User::where('thongbao_id', $thongbao->id)
                    ->where('from_folder', ThongBaoFolderConstants::RECEIVE)->get());
                error_log($countAll.'--------------'.$countDelete);
                if ($countAll == $countDelete) {
                    $thongbao->status = ThongBaoStatusConstants::DRAFT;
                    $thongbao->save();
                    
                    //xoa hết
                    ThongBao_User::where('thongbao_id', $thongbao->id)
                    ->forceDelete();
                    
                    $thongbao_user = new ThongBao_User;
                    $donvi_id = ($this->getLoginedUser() && count($this->getLoginedUser()->donvis) > 0) ? $this->getLoginedUser()->donvis[0]->id : 0;
                    $thongbao_user->thongbao_id= $thongbao->id;
                    $thongbao_user->user_id = auth()->user()->id;
                    $thongbao_user->star_flg = false;
                    $thongbao_user->read_at = Carbon::now();
                    $thongbao_user->donvi_id = $donvi_id;
                    $thongbao_user->from_folder = ThongBaoFolderConstants::DRAFT; //draft
                    $thongbao_user->save();
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
    private function insertThongBao_User($lstId, $thongbao_id) {
        $lstUserId = $lstId->filter(function($Id) {
                        
            if (strstr($Id, '_', true) != 'dv') {
                return $Id;
            }
        });
        
        if (count($lstUserId) > 0) {
            $lstThongBao_User = array();
            foreach ($lstUserId as $user_Id) {
                $lstThongBao_User[] = [
               'thongbao_id' => $thongbao_id,
               'user_id' => $user_Id,
               'donvi_id' => 0,
               'star_flg' => false,
               'read_at' => null,
               'from_folder' => ThongBaoFolderConstants::RECEIVE // danh sach receive
            ];
            }
            ThongBao_User::insert($lstThongBao_User);
        }
        //group - don vi
        $lstDonViId = $lstId->filter(function($Id) {
            $donviId = strstr($Id, 'dv_');
            if (isset($donviId) && $donviId > 0) {
                return $donviId;
            }
        });
        if (count($lstDonViId) > 0 ) {
            $lstDonvi = DonVi::wherein('id', $lstDonViId)->load('users')->get();
            $lstCcUserId = [];
            foreach ($lstDonvi as $donvi) {
                $lstUserId = $donvi->users;
                if (count($lstCcUserId) > 0) {
                    $lstThongBao_User = array();
                    foreach ($lstUserId as $user_Id) {
                        $lstThongBao_User[] = [
                    'thongbao_id' => $thongbao_id,
                    'user_id' => $user_Id,
                    'donvi_id' => $donvi->id,
                    'star_flg' => false,
                    'read_at' => null,
                    'from_folder' => ThongBaoFolderConstants::RECEIVE // danh sach receive
                    ];
                    }
                    ThongBao_User::insert($lstThongBao_User);
                }
            }
        }
    }
    private function checkThongBaoOfUser($thongbao_id) {
        // error_log('checkThongBaoOfUser: user_id'.auth()->user()->id."---:".$thongbao_id);
        $thongbao_users = ThongBao_User::where('user_id', auth()->user()->id)
        ->where('thongbao_id', $thongbao_id)
        ->get();
        
        foreach ($thongbao_users as $thongbao_user) {
            if (isset($thongbao_user->thongbao) && $thongbao_user->thongbao->creator_id == auth()->user()->id &&
            ($thongbao_user->from_folder == ThongBaoFolderConstants::DRAFT
            || $thongbao_user->from_folder == ThongBaoFolderConstants::PUBLISH)) {
                return $thongbao_user->thongbao;
            }
        }
        $thongbao = ThongBao::find($thongbao_id);
        if (isset($thongbao) && $thongbao->creator_id == auth()->user()->id) {
            return $thongbao;
        }
        return null;
    }
}
