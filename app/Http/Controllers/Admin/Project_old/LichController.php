<?php

namespace App\Http\Controllers;

use App\Common\Constant\LichConstants;
use App\Common\Constant\RoomOutSiteConstants;
use App\Commons\Constant\UploadFileConstants;
use App\Helpers\RoomHelper;
use App\Http\Requests\LichRequest;
use App\Models\DonVi;
use App\Models\DonVi_User;
use App\Models\Lich;
use App\Models\LichComment;
use App\Models\LichDuyet;
use App\Models\LichNguoiThamGia;
use App\Models\LichResult;
use App\Models\Role;
use App\Models\Room;

use App\Models\User;
use App\Services\LichService;
use App\Services\UserService;

use App\Utils\NumUtils;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LichController extends Base\BaseController
{
    public function __construct(){

    }
    
    public function dangkyLich(LichRequest $request){
        error_log('------dangkyLich------'.$request->nguoichutri_id);
        $resultRegister = [
            'status' => 'ng',
            'err_code' => '1',
            'message' => 'unknown'
        ];
        try{
            $loai_lich = isset($request->loai_lich) ? $request->loai_lich : 99;
            $lich = $this->buildLich($request);
            $lich->trangthai = LichConstants::STATUS_REGISTERED;
            switch ($loai_lich) {
                case LichConstants::LOAI_LICH_NHA_TRUONG:
                    error_log('dang ky lich nhà trường');
                   $resultRegister = LichService::dangKyLichNhaTruong($lich, $request->room_ids, $request->ghitrung);
                    break;
                case LichConstants::LOAI_LICH_LANH_DAO:
                    error_log('dang ky lich lanh dao');
                    $resultRegister = LichService::dangKyLichLanhDao($lich, $request->room_ids, $request->ghide);
                    break;
                case LichConstants::LOAI_LICH_DON_VI:
                    error_log('dang ky lich đơn vị');
                   $resultRegister = LichService::dangKyLichDonVi($lich, $request->room_ids);
                    break;
                case LichConstants::LOAI_LICH_CA_NHAN:
                    error_log('dang ky lich cá nhân'.$lich->trangthai);
                    $lich->nguoichutri_id = auth()->user()->id;
                    $resultRegister = LichService::dangKyLichCaNhan($lich);
                    break;
                default:
                    break;
            }
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => '999', //danh sach to (receivers) bi rong
                'message' => $e->getMessage()
                ]);
        }
        return $this->responseJson($resultRegister);
    }
    
    private function buildLich(Request $request) {
        error_log("buildLich - lichId: $request->id");
        if (isset($request->id)) {
            $lich = Lich::find($request->id);
        }
        if (!isset($lich)) {
            $lich = new Lich();
        }
        // set data ->
        if (isset($request->duyet_ids)){
            
        $duyet_ids = implode(',',$request->duyet_ids);
        $lich->duyet_ids = $duyet_ids;
        
        }else {
            $lich->duyet_ids = $request->duyet_ids;
        }
        if (count(explode('_', $request->nguoichutri_id)) > 1) {
            if (explode('_', $request->nguoichutri_id)[0] == 'gr') {
                $lich->source_chu_tri =  explode('_', $request->nguoichutri_id)[0];
                $lich->source_chu_tri_id =  explode('_', $request->nguoichutri_id)[1];
                error_log("buildLich - lichId: $lich->source_chu_tri -------- $lich->source_chu_tri_id");
            }
        }
        else {
            $lich->nguoichutri_id = $request->nguoichutri_id;
        }
        //error_log("buildLich - startTime:$request->thoigian_tu;---endTime:$request->thoigian_den");
        $lich->tieude = $request->tieude;
        $lich->noidung = $request->noidung;
        $lich->nguoitao_id = auth()->user()->id;
	    $lich->donvitao_id = $request->donvitao_id;
        $lich->csdt_id = $request->csdt_id;
        $lich->nguoichutri_id = $request->nguoichutri_id;
        $lich->nguoithamgia_ids = $request->nguoithamgia_ids;
        $lich->donvi_ids = $request->donvi_ids;
        $lich->thoigian_tu = $request->thoigian_tu;
        $lich->thoigian_den = $request->thoigian_den;
        $lich->donvitinhthoigian = $request->donvitinhthoigian;
        $lich->trangthai = $request->trangthai;
        $lich->nguoidieuchinh_id = $request->nguoidieuchinh_id;
        $lich->nguoixacnhan_id = $request->nguoixacnhan_id;
        $lich->loai_lich = $request->loai_lich;
        $lich->luuy = $request->luuy;
        if ($lich->loai_lich == LichConstants::LOAI_LICH_LANH_DAO) {
            $lich->is_hidden_lanh_dao = $request->is_hidden_lanh_dao != null ? $request->is_hidden_lanh_dao : false;
        }
        return $lich;
    }

    public function checkAndCreate(LichRequest $request){
        return $this->responseJson([
            'status' => 200, //true
        ]);
    }

    
    
    public function delete(Request $request){
        $lst_lich_ids = $request->lst_lich_ids;
        if (!isset($lst_lich_ids) || $lst_lich_ids == "" || 
            !is_array($lst_lich_ids) || count($lst_lich_ids) == 0) {
            return $this->responseJson([
                'status' => 'ok',
                'message' => 'nothing'
            ]);
        }
        $total_del = 0;
        try{
            DB::transaction(function () use ($lst_lich_ids, &$total_del) {
                $total_del = Lich::where('nguoitao_id', auth()->user()->id)
                ->wherein('id', $lst_lich_ids)
                ->where('trangthai', '!=', LichConstants::STATUS_ACCEPTED)
                ->delete();
            }, 3);
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'ng',
                'message' => $e->getMessage()
            ]);
        }
        
        return $this->responseJson([
            'status' => 'ok',
            'total_del' => $total_del
        ]);
    }
    
    public function rejectLichDuplicated(Request $request){

        // if (!array_key_exists('id', $lich)) {
        //     return $this->responseJson([
        //         'status' => 'ng',
        //         'message'=>'Data không hợp lệ',
        //         'lich' => $lich
        //     ]);
        // }
        error_log($request->id);
        
        if (!isset($request->id)) {
            return $this->responseJson([
                'status' => 'ng',
                'message' => 'Id chưa được gửi lên'
            ]);
        }   
        
        $lichReject = Lich::find($request->id);
        if (!isset($lichReject)) {
            return $this->responseJson([
                'status' => 'ng',
                'message' => 'không tìm thấy lịch'
            ]);
        }
        //xu ly booking room        
       /*  if ($lichReject->loai_lich != LichConstants::LOAI_LICH_CA_NHAN) {
            $lichReject->load('diadiem');
            foreach ($lichReject->diadiem as $room) {
                if ($room->pivot->outsite_booking_room_id > 0) {
                    $result_bookingroom = RoomHelper::bookingRoom(
                        $request->id,
                        $room->pivot->outsite_booking_room_id,
                        $request->thoigian_tu,
                        $request->thoigian_den,
                        $request->tieude,
                        RoomOutSiteConstants::STATUS_ACCEPTED,
                        RoomOutSiteConstants::DEL_DELETED,
                        auth()->user()->outsite_user_id
                    );
                }
            }
        } */
        $ly_do = isset($request->ly_do) ? $request->ly_do : "Xóa lịch trùng";
        DB::transaction(function () use (&$lichReject, $ly_do) {
            // xu ly Lich duyet
            if ($lichReject->loai_lich == LichConstants::LOAI_LICH_CA_NHAN) {
                LichDuyet::where('lich_id', $lichReject->id)
                        ->update(['tinhtrang'=>LichConstants::STATUS_REJECTED]);
            }
            // xu ly comment
            $lichComment = LichService::buildLichComment(
                $lichReject->id,
                auth()->user()->id,
                'Có lịch ưu tiên bị trùng',
                LichConstants::STATUS_REJECTED
            );
            $lichComment->save();
            //xu ly bang lich
            $lichReject->trangthai = LichConstants::STATUS_REJECTED;
            $lichReject->save();
        },3);
        return $this->responseJson([
            'status' => 'ok',
            'lich' => $lichReject,
        ]);
    }
    public function cancelLich(Request $request){
        try {
            $lich = Lich::find($request->id);
            if (!isset($lich) || $lich->trangthai == LichConstants::STATUS_COMPLETED) {
                return [
                    'status' => 'ng', //false
                    'err_code' => '1',
                    'message' => 'Không tìm thấy lịch hoặc lịch đã thực hiện!'
                ];
            }
            $ly_do  = $request->ly_do;
            // xóa bỏ booking room
            if ($lich->trangthai == LichConstants::STATUS_ACCEPTED) {
                if ($lich->loai_lich != LichConstants::LOAI_LICH_CA_NHAN) {
                    LichDuyet::where('lich_id', $lich->id)
                        ->update(['tinhtrang'=>LichConstants::STATUS_CANCELLED]);
                } else {
                    $lich->load('diadiem');
                    foreach ($lich->diadiem as $diadiem) {
                        if ($diadiem->pivot->outsite_room_id > 0) {
                            $result_bookingroom = RoomHelper::bookingRoom(
                                $lich->id,
                                $diadiem->pivot->outsite_room_id,
                                $lich->thoigian_tu,
                                $lich->thoigian_den,
                                $lich->tieude,
                                RoomOutSiteConstants::STATUS_ACCEPTED,
                                RoomOutSiteConstants::DEL_DELETED,
                                auth()->user()->outsite_user_id
                            );
                        }
                    }
                }
            }
            DB::transaction(function () use (&$lich, $ly_do) {
                $lichComment = LichService::buildLichComment(
                    $lich->id,
                    auth()->user()->id,
                    $ly_do,
                    LichConstants::STATUS_CANCELLED
                );
                $lichComment->save();
                $lich->trangthai = LichConstants::STATUS_CANCELLED;
                $lich->save();
            }, 3);
            return $this->responseJson([
                'status' => 'ok',
                'lich' => $lich
            ]);
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'ng',
                'message' => $e->getMessage()
            ]);
        }
        
    }
    public function confirmLichChuTri(Request $request){
        try {
            error_log('confirmLichChuTri: '.$request->id);
            $lich = Lich::find($request->id);
            if (!isset($lich) || $lich->trangthai != LichConstants::STATUS_REGISTERED) {
                return [
                    'status' => 'ng', //false
                    'err_code' => '1',
                    'message' => 'Không tìm thấy lịch hoặc lịch đã thai đổi trạng thái!'
                ];
            }
            $lst_lich_trung = LichService::getAllLichTrungByUserIdAndTime($lich->nguoichutri_id,$lich->id,$lich->thoigian_tu,$lich->thoigian_den);
            error_log('--------------------'.count($lst_lich_trung).'-------------'.strval($request->ghide));
            if (!$request->ghide && count($lst_lich_trung) > 0) {
                return [
                    'status' => 'ng', //false
                    'err_code' => 'BI_TRUNG',
                    'message' => 'Có lịch bị trùng',
                    'list_lich_trung' => $lst_lich_trung
                ];
            }
            
            DB::transaction(function () use ($lich,$lst_lich_trung) {
                if (count($lst_lich_trung)>0) {
                    foreach ($lst_lich_trung as $lich_trung) {
                        if ($lich_trung->trangthai != LichConstants::STATUS_COMPLETED) {
                            if ($lich_trung->loai_lich == LichConstants::LOAI_LICH_CA_NHAN) {
                                LichDuyet::where('lich_id', $lich_trung->id)
                                ->update(['tinhtrang'=>LichConstants::STATUS_REJECTED]);
                            }
                            $lich_trung->trangthai=LichConstants::STATUS_REJECTED;
                            $lich_trung->save();
                        }
                    }
                }
                // xóa bỏ booking room
                if ($lich->loai_lich == LichConstants::LOAI_LICH_CA_NHAN) {
                    LichDuyet::where('lich_id', $lich->id)
                        ->update(['tinhtrang'=>LichConstants::STATUS_ACCEPTED]);
                }
                $lich->trangthai=LichConstants::STATUS_ACCEPTED;
                $lich->save();
            }, 3);
            return $this->responseJson([
                'status' => 'ok'
            ]);
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'ng',
                'message' => $e->getMessage()
            ]);
        }
        
    }
    public function rejectLichChuTri(Request $request){
        try {
            error_log('rejectLichChuTri: '.$request->id);
            $lich = Lich::find($request->id);
            if (!isset($lich) || !(
                $lich->trangthai == LichConstants::STATUS_REGISTERED
                ||$lich->trangthai == LichConstants::STATUS_ACCEPTED)
                ) {
                return [
                    'status' => 'ng', //false
                    'err_code' => '1',
                    'message' => 'Không tìm thấy lịch hoặc lịch đã thai đổi trạng thái!'
                ];
            }
            $ly_do = $request->ly_do;
            DB::transaction(function () use ($lich, $ly_do) {
            
                if ($lich->loai_lich == LichConstants::LOAI_LICH_CA_NHAN) {
                    LichDuyet::where('lich_id', $lich->id)
                        ->update(['tinhtrang'=>LichConstants::STATUS_REJECTED]);
                }
                
                $lichComment = LichService::buildLichComment(
                    $lich->id,
                    auth()->user()->id,
                    $ly_do,
                    LichConstants::STATUS_REJECTED
                );
                $lichComment->save();
                $lich->trangthai=LichConstants::STATUS_REJECTED;
                $lich->save();
            }, 3);
            
            return $this->responseJson([
                'status' => 'ok'
            ]);
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'ng',
                'message' => $e->getMessage()
            ]);
        }
        
    }
    public function unConfirmLichChuTri(Request $request){
        try {
            error_log('unConfirmLichChuTri: '.$request->id);
            $lich = Lich::find($request->id);
            if (!isset($lich) || 
                !($lich->trangthai == LichConstants::STATUS_ACCEPTED
                || $lich->trangthai == LichConstants::STATUS_REJECTED)
                ) {
                return [
                    'status' => 'ng', //false
                    'err_code' => '1',
                    'message' => 'Không tìm thấy lịch hoặc lịch đã thay đổi trạng thái'
                ];
            }
            $ly_do  = $request->ly_do;
            DB::transaction(function () use ($lich, $ly_do) {
                if ($lich->loai_lich == LichConstants::LOAI_LICH_CA_NHAN) {
                    LichDuyet::where('lich_id', $lich->id)
                        ->update(['tinhtrang'=>LichConstants::STATUS_REGISTERED]);
                }
                $lich->trangthai = LichConstants::STATUS_REGISTERED;
                $lich->save();
            }, 3);
            return $this->responseJson([
                'status' => 'ok'
            ]);
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'ng',
                'message' => $e->getMessage()
            ]);
        }
        
    }
    public function startExecuteMeeting(Request $request) {
        $lich = Lich::find($request->id);
        if (!isset($lich)) {
            return [
                'status' => 'ng', //false
                'err_code' => '1',
                'message' => 'Không tìm thấy lịch hoặc lịch đã bị xóa!',
                'lich' => $lich
            ];
        }
        if ($lich->trangthai == LichConstants::STATUS_CANCELLED) {
            return [
                'status' => 'ng', //false
                'err_code' => '2',
                'message' => 'Lịch đã bị hủy bỏ',
                'lich' => $lich
            ];
        }
        
        if (!($lich->trangthai == LichConstants::STATUS_ACCEPTED 
            || $lich->trangthai == LichConstants::STATUS_DOING
            || $lich->trangthai == LichConstants::STATUS_COMPLETED
            )) {
            return [
                'status' => 'ng', //false
                'err_code' => '3',
                'message' => 'Lịch chưa đủ điều kiện để thực hiện hoặc lịch đã/đang thực hiện!',
                'lich' => $lich
            ];
        }
        
        try {
            if ($lich->trangthai != 7) {
                $lich->trangthai = LichConstants::STATUS_DOING;
                $lich->save();
            }
            $lich->load('nguoichutri')->load('groupchutri')
            ->load('danhsach_nguoithamgia')
            ->load('danhsach_donvithamgia')
            ->load('danhsach_groupthamgia')
            ->load('nguoitao')
            ->load('donvitao')
            ->load('diadiem')
            ->load('attachedFiles')
            ->load('bienBanFiles');
            return $this->responseJson([
                'status' => 'ok',
                'lich' => $lich
            ]);
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'ng',
                'message' => $e->getMessage(),
                'err_code' => '99',
            ]);
        }
    }
    
    public function executeMeeting(Request $request) {
        error_log('executeMeeting');
        $lich = Lich::find($request->id);
        if (!isset($lich)) {
            return [
                'status' => 'ng', //false
                'err_code' => '1',
                'message' => 'Không tìm thấy lịch!'
            ];
        }
        if ($lich->trangthai == LichConstants::STATUS_CANCELLED) {
            return [
                'status' => 'ng', //false
                'err_code' => '2',
                'message' => 'Lịch đã bị hủy bỏ',
                'lich' => $lich
            ];
        }
        
        if (!($lich->trangthai == LichConstants::STATUS_ACCEPTED 
            || $lich->trangthai == LichConstants::STATUS_DOING
            || $lich->trangthai == LichConstants::STATUS_COMPLETED
            )) {
            return [
                'status' => 'ng', //false
                'err_code' => '3',
                'message' => 'Lịch chưa đủ điều kiện để thực hiện hoặc lịch đã/đang thực hiện!',
                'lich' => $lich
            ];
        }
        //check quyền
        if (!(auth()->user()->id == $lich->nguoitao_id || auth()->user()->id == $lich->nguoichutri_id)) {
            return [
                'status' => 'ng', //false
                'err_code' => '4',
                'message' => 'Bạn không có quyền thực hiện!',
                'lich' => $lich
            ];
        }
        
        try {
            DB::transaction(function () use(&$lich, $request) {
                $lich->trangthai = ($request->trangthai == LichConstants::STATUS_DOING
                    || $request->trangthai == LichConstants::STATUS_COMPLETED) ? $request->trangthai : LichConstants::STATUS_COMPLETED;
                $lich->save();
                $ds_thanhphan_thamgia_meeting_id = isset($request->ds_thanhphan_thamgia_meeting_id) 
                    && is_array($request->ds_thanhphan_thamgia_meeting_id) ? $request->ds_thanhphan_thamgia_meeting_id : [];
                $lich->load('danhsach_nguoithamgia');
                $list_nguoithamgia = [];
                //xoa bo danh dau da tham gia
                LichNguoiThamGia::where('lich_id', $lich->id)
                        ->where('status', LichConstants::NGUOI_THAM_GIA_THAM_GIA)
                        ->where('accepted_at', '!=', null)
                        ->update(['status' => LichConstants::NGUOI_THAM_GIA_CANCELLED]);
                LichNguoiThamGia::where('lich_id', $lich->id)
                        ->where('status', LichConstants::NGUOI_THAM_GIA_THAM_GIA)
                        ->where('accepted_at', null)
                        ->update(['status' => LichConstants::NGUOI_THAM_GIA_ACCEPTED]);
                        
                foreach ($ds_thanhphan_thamgia_meeting_id as $thanhphanthamgia_id) {
                    $lichNguoiThamGia = null;
                    $user_id = null;
                    $donvi_id = null;
                    /* if (strstr($thanhphanthamgia_id, '_', true) == 'dv') {
                        $donvi_id = str_replace('dv_','',$thanhphanthamgia_id);
                    } else { */
                    $thanhphan = explode('dv_', $thanhphanthamgia_id);
                    if (count($thanhphan) > 0) {
                        $user_id = $thanhphan[0] == 0 ? null : $thanhphan[0];
                        if (count($thanhphan) > 1) {
                            $donvi_id = $thanhphan[1] == 0 ? null : $thanhphan[1];
                        }
                    }
                    //}
                    if ($user_id == null && $donvi_id == null) {
                        continue;
                    }
                    $lichNguoiThamGia = LichNguoiThamGia::where('daidien_donvi_id', $donvi_id)
                        ->where('lich_id', $lich->id)
                        ->where('user_id', $user_id)
                        ->first();
                    if (!isset($lichNguoiThamGia)) {
                        $lichNguoiThamGia = new LichNguoiThamGia;
                        $lichNguoiThamGia->lich_id = $lich->id;
                        $lichNguoiThamGia->user_id = $user_id;
                        $lichNguoiThamGia->daidien_donvi_id = $donvi_id;
                    }
                    //update trang thai
                    $lichNguoiThamGia->status = LichConstants::NGUOI_THAM_GIA_THAM_GIA;
                    $lichNguoiThamGia->save();
                }
                
                //luu ket qua
                /* $bienban = LichResult::where('lich_id', $lich->id)->first();
                if (!isset($bienban)) {
                    $bienban = new LichResult;
                    $bienban->lich_id = $lich->id;
                } 
                $bienban->content = $request->bienbancontent;
                $bienban->save(); */
                
            },3);
            
            $lich->load('nguoichutri')
            ->load('groupchutri')
            ->load('danhsach_nguoithamgia')
            ->load('danhsach_donvithamgia')
            ->load('danhsach_groupthamgia')
            ->load('nguoitao')
            ->load('donvitao')
            ->load('diadiem')
            ->load('attachedFiles')
            ->load('bienBanFiles');
            return $this->responseJson([
                'status' => 'ok',
                'lich' => $lich
            ]);
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'ng',
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /* public function updateStatusLich(Request $request){
        $lich = Lich::find($request->id);
        if (!isset($lich)) {
            return [
                'status' => 'ng', //false
                'err_code' => '1',
                'message' => 'Không tìm thấy lịch!'
            ];
        }
        $status = $request->status;
        $ghide = $request->ghide;
        $comment = $request->comment;
        error_log('updateStatusLich--'.$comment.'---------'.$status.'-----model'.$ghide.'--------LichId'.$lich->id);
        
        
        if ($status != LichConstants::STATUS_ACCEPTED) {
            if ($lich->trangthai != LichConstants::STATUS_ACCEPTED) {
                Lich::where('id', $lich->id)->update(['trangthai' => $status]);
            }else { //lich da accepted trc do
                if ($lich->loai_lich != LichConstants::LOAI_LICH_CA_NHAN){
                    LichDuyet::where('lich_id', $lich->id)
                        ->update(['tinhtrang'=>LichConstants::STATUS_ACCEPTED]);
                    Lich::where('id',$lich->id)
                        ->update(['trangthai'=>LichConstants::STATUS_REJECTED]);
                }else {
                    $result_bookingroom = RoomHelper::bookingRoom(
                        $lich->id,
                        $roomModel->outsite_room_id,
                        $lich->thoigian_tu,
                        $lich->thoigian_den,
                        $lich->tieude,
                        RoomOutSiteConstants::STATUS_ACCEPTED,
                        RoomOutSiteConstants::DEL_DELETED,
                        auth()->user()->outsite_user_id
                    );
                    Lich::where('id', $lich->id)
                        ->update(['trangthai' => LichConstants::STATUS_REJECTED]);
                }
            }
        }else {
            try {
                $lst_lich_by_chutri = LichService::getAllLichTrungByUserIdAndTime(
                    $lich['nguoichutri_id'],
                    $lich->id,
                    $lich['thoigian_tu'],
                    $lich['thoigian_den'],
                );
                if (!$ghide) {
                    if (count($lst_lich_by_chutri)>0) {
                        return [
                            'status' => 'ng', //false
                            'err_code' => 'BI_TRUNG',
                            'message' => 'Có lịch bị trùng',
                            'list_lich' => $lst_lich_by_chutri
                        ];
                    }
                    
                }
                
                DB::transaction(function () use($lich,$ghide,$roomModel,$status,$comment,$lst_lich_by_chutri) {
                    
                    if ($ghide) {
                        foreach($lst_lich_by_chutri as $lich_chutri){
                            error_log($lich_chutri->id);
                            if ($lich_chutri->loai_lich == LichConstants::LOAI_LICH_CA_NHAN) {
                                LichDuyet::where('lich_id',$lich->id)
                                    ->update(['tinhtrang'=> LichConstants::STATUS_ACCEPTED]);
                                Lich::wherein('id',$lich->id)
                                    ->update(['trangthai'=> LichConstants::STATUS_REJECTED]);
                            }else if ($lich_chutri->nguoichutri_id != $lich->nguoichutri_id){
                                LichNguoiThamGia::where('lich_id',$lich->id)
                                    ->where('user_id', $lich->nguoichutri_id)
                                    ->update(['tinhtrang'=> LichConstants::STATUS_ACCEPTED]);
                            }
                            else {
                                $result_bookingroom = RoomHelper::bookingRoom(
                                    $lich_chutri->id,
                                    $lich_chutri->diadiem[0]->outsite_room_id,
                                    $lich_chutri->thoigian_tu,
                                    $lich_chutri->thoigian_den,
                                    $lich_chutri->tieude,
                                    RoomOutSiteConstants::STATUS_ACCEPTED,
                                    RoomOutSiteConstants::DEL_DELETED,
                                    auth()->user()->outsite_user_id
                                );
                                Lich::where('id', $lich_chutri->id)
                                    ->update(['trangthai' => LichConstants::STATUS_REJECTED]);
                            }
                        }
                    }

                    // $result_bookingroom = RoomHelper::bookingRoom(
                    //     $lich->id,
                    //     $roomModel->outsite_room_id,
                    //     $lich->thoigian_tu,
                    //     $lich->thoigian_den,
                    //     $lich->tieude,
                    //     RoomOutSiteConstants::STATUS_INIT,
                    //     RoomOutSiteConstants::DEL_ADD_NEW,
                    //     auth()->user()->outsite_user_id
                    // );
                    // if ($result_bookingroom['status'] != 'ok') {
                    //     throw new \Exception($result_bookingroom['msg']);
                       
                    // }
                    // if (!isset($result_bookingroom['data']) || count($result_bookingroom['data']) == 0) {
                    //    throw new Exception('Không lấy được thông tin đăng ký phòng.');
                    // }
                    // $result_bookingroom_data = $result_bookingroom['data'][0];
                    //     foreach ($lich['diadiem'] as $room) {
                    //         $lich->diadiem()->detach($room->id);
                    //     } 
                    // $lich->diadiem()->attach($roomModel->id, 
                    //     ['outsite_booking_room_id'=>$result_bookingroom_data->HaUIRequestID]);
                    foreach ($lich->diadiem as $room) {
                        $lich->diadiem()->detach($room->id);
                    } 
                    $lich->diadiem()->attach(
                        $roomModel->id, 
                        ['outsite_booking_room_id'=>9999]
                    );
                    Lich::where('id',$lich->id)
                        ->update(['trangthai' =>$status]
                    );
                    $lichComment = LichService::buildLichComment(
                        $lich['id'],
                        auth()->user()->id,
                        $comment,
                        $status
                    );
                    $lichComment->save();
                    error_log('-------------diadiem---3-');
                               
                },3);
                return [
                    'status' => 'ok',
                ];
            }catch(Exception $e){
                //delete booking att server outsite TODO
           error_log('--------dangKyLichLanhDao------Exception--lich-oldId:'.';--new id:'.$lich->id);
               $result_bookingroom = RoomHelper::bookingRoom(
                   $lich->id,
                   $roomModel->outsite_room_id,
                   $lich->thoigian_tu,
                   $lich->thoigian_den,
                   $lich->tieude,
                   RoomOutSiteConstants::STATUS_ACCEPTED,
                   RoomOutSiteConstants::DEL_DELETED,
                   auth()->user()->outsite_user_id
               );
            error_log($e->getMessage());
            return [
                'status' => 'ng',
                'err_code' => '1',
                'message' => $e->getMessage()
                ];
            }
        }
        $lichComment = LichService::buildLichComment(
            $lich->id,
            auth()->user()->id,
            $comment,$status
        );
        $lichComment->save();
        return $this->responseJson([
            'status' => 'ok'
        ]);
    } */
    public function updateStatusLichCaNhan(Request $request){
        $lich_id = $request->lich_id;
        $tinhtrang = $request->status;
        $donvi_id = $request->donvi_id;
        $ghide = $request->ghide;
        $lich = Lich::find($request->lich_id);
        if (!isset($lich)) {
            return [
                'status' => 'ng', //false
                'err_code' => '1',
                'message' => 'Không tìm thấy lịch'
            ];
        }
        error_log('updateStatusLichCaNhan--'.$ghide.'-----'.$lich['nguoichutri_id'].'--------'.$tinhtrang);
        if ($tinhtrang == LichConstants::STATUS_ACCEPTED) {
            LichDuyet::where('lich_id', $lich_id)
                ->where('donvi_id', $donvi_id)
                ->update(['tinhtrang'=> $tinhtrang]);
        }else {
            $lst_lich_by_chutri = LichService::getAllLichTrungByUserIdAndTime(
                $lich['nguoichutri_id'],
                $lich->id,
                $lich['thoigian_tu'],
                $lich['thoigian_den'],
            );
            if (!$ghide) {
                error_log('updateStatusLichCaNhan- count:-'.count($lst_lich_by_chutri));
                if (count($lst_lich_by_chutri)>0) {
                    return [
                        'status' => 'ng', //false
                        'err_code' => 'BI_TRUNG',
                        'message' => 'Có lịch bị trùng',
                        'list_lich' => $lst_lich_by_chutri
                    ];
                }
            }
                foreach($lst_lich_by_chutri as $lich_chutri){
                    if ($lich_chutri->loai_lich == LichConstants::LOAI_LICH_CA_NHAN) {
                        LichDuyet::where('lich_id',$lich->id)
                            ->update(['tinhtrang'=> LichConstants::STATUS_ACCEPTED]);
                        Lich::where('id',$lich->id)
                            ->update(['trangthai'=> LichConstants::STATUS_REJECTED]);
                    }else if ($lich_chutri->nguoichutri_id != $lich->nguoichutri_id){
                        LichNguoiThamGia::where('lich_id',$lich->id)
                        ->where('user_id',$lich->nguoichutri_id)
                        ->update(['tinhtrang'=> LichConstants::STATUS_ACCEPTED]);
                    }
                    else {
                    //TODO diadiem???
                        /* $result_bookingroom = RoomHelper::bookingRoom(
                            $lich_chutri->id,
                            $lich_chutri->diadiem[0]->outsite_room_id,
                            $lich_chutri->thoigian_tu,
                            $lich_chutri->thoigian_den,
                            $lich_chutri->tieude,
                            RoomOutSiteConstants::STATUS_ACCEPTED,
                            RoomOutSiteConstants::DEL_DELETED,
                            auth()->user()->outsite_user_id
                        ); */
                        Lich::where('id', $lich_chutri->id)
                            ->update(['trangthai' => LichConstants::STATUS_REJECTED]);
                    }
                }
                LichDuyet::where('lich_id',$lich_id)
                    ->where('donvi_id',$donvi_id)
                    ->update(['tinhtrang'=>$tinhtrang]);
        
        }
        $checkDuyet = LichDuyet::where('lich_id',$lich_id)
            ->where('tinhtrang','!=', LichConstants::STATUS_REGISTERED)
            ->get();
        if (count($checkDuyet) == 0) {
            Lich::where('id',$lich_id)
                ->update(['trangthai'=> LichConstants::STATUS_ACCEPTED]);
        }else {
            Lich::where('id',$lich_id)
                ->update(['trangthai'=> LichConstants::STATUS_REJECTED]);
        }

        return $this->responseJson([
            'status' => 'ok'
        ]);
    }
    public function updateStatusLichDuplication(Request $request){
        $lich_id = $request->lich_id;
        $trangthai = $request->trangthai;
        $array_lichchutri_duplicate_id = $request->array_lichchutri_duplicate_id;
        $array_lichcanhan_duplicate_id = $request->array_lichcanhan_duplicate_id;
        //change lịch cá nhân
        if (count(is_array($array_lichcanhan_duplicate_id) ? $array_lichcanhan_duplicate_id : []) > 0) {
            LichDuyet::wherein('lich_id', $array_lichcanhan_duplicate_id)
                ->update(['tinhtrang'=> LichConstants::STATUS_ACCEPTED]);
            Lich::wherein('id',$array_lichcanhan_duplicate_id)
                ->update(['trangthai'=> LichConstants::STATUS_REJECTED]);
        }
        //change lịch chủ tri
        if (count(is_array($array_lichchutri_duplicate_id) ? $array_lichchutri_duplicate_id : []) > 0) {
            Lich::wherein('id', $array_lichchutri_duplicate_id)
                ->update(['trangthai' => LichConstants::STATUS_REJECTED]);
        }
        //Thay đổi trạng thái lịch đã chọn
        error_log('---------'.$lich_id.'-----------'.$trangthai);
        $lich = Lich::where('id', $lich_id)->update(['trangthai' => $trangthai]);
    }
    public function reCall(Request $request){
        
        $lich_id = $request->lich_id;
        if (isset($lich_id) || $lich_id == "") {
            return $this->responseJson([
                'status' => 'ok',
                'message' => 'nothing'
            ]);
        }
        $lich = null;
        try{
            DB::transaction(function () use ($lich_id, &$lich) {
                $lich = Lich::find($lich_id);
                if (isset($lich)) {
                    $lich->status = LichConstants::STATUS_DRAFT;
                    $lich->save();
                }
                                
            }, 3);
        }catch(Exception $e){
            error_log($e->getMessage());
        }
        
        return $this->responseJson([
            'status' => 'ok',
            $lich => $lich
        ]);
    }

    public function getAllLanhDao(Request $request){
        error_log("GetAllLanhDao:---:$request->thoigian_tu;--:$request->thoigian_den;--:$request->donvi_id");
        $donvi_id = isset($request->donvi_id) && $request->donvi_id > 0 ? $request->donvi_id : 0;
        if ($donvi_id == 0) {
            $user = User::find(auth()->user()->id);
            $user->load('donvis');
            $donvi_id = count($user->donvis) > 0 ? $user->donvis[0]->id : 0;
        }
        $lstLich = LichService::getAllLichLanhDao($donvi_id, $request->thoigian_tu,
            $request->thoigian_den);
                    
        return $this->responseJson([
            'status' => 'ok',
            'lstLich' => $lstLich
        ]);
    }
    public function getAllLanhDaoDangKy(Request $request){
        error_log("GetAllLanhDao:---:$request->thoigian_tu;--:$request->thoigian_den");
        $lstLich = LichService::getAllLichLanhDaoDangKy(
            $request->thoigian_tu,
            $request->thoigian_den);
        
                    
        return $this->responseJson([
            'status' => 'ok',
            'lstLich' => $lstLich
        ]);
    }
    public function GetAllLanhDaoConfirmChuTri(Request $request){
        $user = User::find(auth()->user()->id);
        if (!isset($user)) {
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => 1,
                'message' => 'User not found',
                'lstLich' => []
            ]);
        }
        $user->load('roles');
        $hasPermission = false;
        $isThuKyLanhDao = false;
        foreach($user->roles as $role) {
            if ($role->code == 'egov_LANH_DAO') {
                $hasPermission = true;
            }
            if ($role->code == 'egov_THU_KY_BAN_GIAM_HIEU') {
                $isThuKyLanhDao = true;
                $hasPermission = true;
            }
        }
        $user->load('donvis');
        foreach($user->donvis as $donvi) {
            if ($donvi->ma_donvi == '1001') {
                $hasPermission = true;
            }
        }
        if (!$hasPermission) {
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => 1,
                'message' => 'Bạn không có quyền',
                'lstLich' => []
            ]);
        }
        if ($isThuKyLanhDao) {
            $lstLichLanhDao = LichService::getAllLichChuTriLanhDao();
        } else {
            $user->load('lichsChuTri');
            $lstLichLanhDao = $user->lichsChuTri;
        }
        $lstLich_ids = $lstLichLanhDao->map(function ($lich) {
            return $lich->id;
        });
        $thoigian_tu = $request->thoigian_tu;
        $thoigian_den = $request->thoigian_den;
        $lstLich = Lich::wherein('id', $lstLich_ids)
                    ->whereBetween('thoigian_tu',[$thoigian_tu,$thoigian_den])
                    ->orderby('thoigian_tu')
                    ->get()
                    ->load('nguoichutri')
                    ->load('groupchutri')
                    ->load('danhsach_nguoithamgia')
                    ->load('danhsach_donvithamgia')
                    ->load('danhsach_groupthamgia')
                    ->load('nguoitao')
                    ->load('donvitao')
                    ->load('diadiem')
                    ->load('attachedFiles')
                    ->load('bienBanFiles');
        return $this->responseJson([
            'status' => 'ok',
            'lstLich' => $lstLich
        ]);
    }
    public function GetAllCoQuanConfirmChuTri(Request $request){
        error_log('GetAllCoQuanConfirmChuTri');
        $user = User::find(auth()->user()->id);
        if (!isset($user)) {
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => 1,
                'message' => 'User not found',
                'lstLich' => []
            ]);
        }
        error_log('GetAllCoQuanConfirmChuTri 1');
        $user->load('roles');
        $isThuKyLanhDao = false;
        $hasPermission = false;
        foreach($user->roles as $role) {
            if ($role->code == 'egov_THU_KY_BAN_GIAM_HIEU'
                || $role->code == 'egov_LANH_DAO') {
                $isThuKyLanhDao = true;
                $hasPermission = true;
            }
            if ($role->code == 'egov_TRUONG_DON_VI' 
                || $role->code == 'egov_PHO_DON_VI') {
                $hasPermission = true;
            }
        }
        
        // error_log('GetAllCoQuanConfirmChuTri 2: donvi_id'.$request->donvi_id);
        $donvi_id = isset($request->donvi_id) ? $request->donvi_id : 0;            
        $user->load('donvis');
        if ($donvi_id == 0) {
            foreach ($user->donvis as $donvi) {
                if ($donvi->truong_dv == auth()->user()->id) {
                    $hasPermission = true;
                    $donvi_id = $donvi->id;
                }
            }
        } else {
            foreach ($user->donvis as $donvi) {
                if ( $donvi->id == $donvi_id
                    && $donvi->truong_dv == auth()->user()->id) {
                    $hasPermission = true;
                }
            }
        }
        
        error_log('GetAllCoQuanConfirmChuTri 3');
        if (!$hasPermission) {
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => 1,
                'message' => 'Bạn không có quyền',
                'lstLich' => []
            ]);
        }
        error_log('GetAllCoQuanConfirmChuTri 4');
        
        error_log('GetAllCoQuanConfirmChuTri 6: '.$request->thoigian_tu.";---:".$request->thoigian_den);
        
        $lstLich = LichService::getAllLichCoQuanLapLich(
            $donvi_id, 
            $request->thoigian_tu,
            $request->thoigian_den,
            $isThuKyLanhDao);
        error_log('GetAllCoQuanConfirmChuTri 7: '.count($lstLich));
        return $this->responseJson([
            'status' => 'ok',
            'lstLich' => $lstLich
        ]);
    }
    public function GetAllPhongBanConfirmChuTri(Request $request){
        error_log('GetAllPhongBanConfirmChuTri');
        $user = User::find(auth()->user()->id);
        if (!isset($user)) {
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => 1,
                'message' => 'User not found',
                'lstLich' => []
            ]);
        }
        
        $hasPermission = false;
        error_log('GetAllPhongBanConfirmChuTri 1');
        $user->load('roles');
        $isTruongPho = false;
        $isDuyetLichPhongBan = false;
        foreach($user->roles as $role) {
            if ($role->code == 'egov_TRUONG_DON_VI' 
                || $role->code == 'egov_PHO_DON_VI') {
                $isTruongPho = true;
            }
            if ($role->code == 'egov_DUYET_LICH_PHONG_BAN') {
                $isDuyetLichPhongBan = true;
            }
        }
        
        $donvi_id = isset($request->donvi_id) ? $request->donvi_id : 0;            
        $user->load('donvis');
        if ($donvi_id == 0) {
            foreach ($user->donvis as $donvi) {
                if ($donvi->truong_dv == auth()->user()->id) {
                    $hasPermission = true;
                    $donvi_id = $donvi->id;
                }
            }
        } else {
            foreach ($user->donvis as $donvi) {
                if ( $donvi->id == $donvi_id
                    && $donvi->truong_dv == auth()->user()->id) {
                    $hasPermission = true;
                }
            }
        }    
        error_log('GetAllPhongBanConfirmChuTri 5: '.$request->thoigian_tu.";---:".$request->thoigian_den);
        
        //get all
        $lstLich = LichService::getAllLichDonViLapLich(
            $donvi_id, 
            $request->thoigian_tu,
            $request->thoigian_den,
            $isDuyetLichPhongBan,
            $isTruongPho);
        error_log('GetAllPhongBanConfirmChuTri 7: '.count($lstLich));
        return $this->responseJson([
            'status' => 'ok',
            'lstLich' => $lstLich
        ]);
    }
    public function GetAllCanhanConfirm(Request $request){
        error_log('GetAllCanhanConfirm');
        $user = User::find(auth()->user()->id);
        if (!isset($user)) {
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => 1,
                'message' => 'User not found',
                'lstLich' => []
            ]);
        }
        //error_log('GetAllCanhanConfirm 1');
        $user->load('roles');
        $hasPermission = false;
        $isHanhChinhNhanSu = false;
        $isTruongPho = false;
        foreach($user->roles as $role) {
            if ($role->code == 'egov_HANH_CHINH_NHAN_SU' || $role->code == 'egov_DUYET_LICH_CA_NHAN') {
                $isHanhChinhNhanSu = true;
                $hasPermission = true;
            }
            if ($role->code == 'egov_TRUONG_DON_VI'
                || $role->code == 'egov_PHO_DON_VI') {
                $isTruongPho =true;
                $hasPermission = true;
            }
            
        }
        
        $donvi_id = isset($request->donvi_id) ? $request->donvi_id : 0;            
        $user->load('donvis');
        if ($donvi_id == 0) {
            foreach ($user->donvis as $donvi) {
                if ($donvi->truong_dv == $user->id) {
                    $hasPermission = true;
                    $donvi_id = $donvi->id;
                }
            }
        } else {
            foreach ($user->donvis as $donvi) {
                if ( $donvi->id == $donvi_id
                    && $donvi->truong_dv == $user->id) {
                    $hasPermission = true;
                }
            }
        }
        
        //error_log('GetAllCanhanConfirm 3');
        if (!$hasPermission) {
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => 1,
                'message' => 'Bạn không có quyền',
                'lstLich' => []
            ]);
        }
        
        error_log('GetAllCanhanConfirm 6: '.$request->thoigian_tu.";---:".$request->thoigian_den);
        
        $lstLich =  LichService::getAllLichCaNhanDuyetLich(
            $isHanhChinhNhanSu,
            $isTruongPho,
            $user,
            $request->thoigian_tu, 
            $request->thoigian_den);
        error_log('GetAllCanhanConfirm 7: '.count($lstLich));
        return $this->responseJson([
            'status' => 'ok',
            'lstLich' => $lstLich
        ]);
    }
    
    public function getDuplicate(Request $request){
        $nguoichutri_id = $request->nguoichutri_id;
        $thoigian_tu = $request->thoigian_tu;
        $thoigian_den = $request->thoigian_den;
        error_log($nguoichutri_id.'-----'.$thoigian_tu.'-----'.$thoigian_den);
        
        $duplicateChuTris = $this->checkDuplicateChuTri($nguoichutri_id,
            $thoigian_tu,
            $thoigian_den
        );
        
        return $this->responseJson([
            'status' => 'ok',
            'duplicateChuTris' => $duplicateChuTris
        ]);
    }
    
    public function GetCoquanConfirm(Request $request){
        $thoigian_tu = $request->thoigian_tu;
        $thoigian_den = $request->thoigian_den;
        $lst_lich = Lich::where('loai_lich', LichConstants::LOAI_LICH_NHA_TRUONG)
            ->whereBetween('thoigian_tu',[$thoigian_tu,$thoigian_den])
            ->where('trangthai', LichConstants::STATUS_ACCEPTED)
            ->orderBy('thoigian_tu')
            ->get()
            ->load('nguoichutri')
            ->load('groupchutri')
            ->load('danhsach_nguoithamgia')
            ->load('danhsach_donvithamgia')
            ->load('danhsach_groupthamgia')
            ->load('nguoitao')
            ->load('donvitao');
        
        return $this->responseJson([
            'status' => 'ok',
            'lstLich' => $lst_lich
        ]);
    }
    public function GetCoquanChuaConfirm(Request $request){
        $thoigian_tu = $request->thoigian_tu;
        $thoigian_den = $request->thoigian_den;
        $lst_lich = Lich::where('loai_lich', LichConstants::LOAI_LICH_NHA_TRUONG)
            ->where('trangthai','!=', LichConstants::STATUS_ACCEPTED)
            ->whereBetween('thoigian_tu',[$thoigian_tu,$thoigian_den])
            ->orderBy('thoigian_tu')
            ->get()
            ->load('nguoichutri')
            ->load('groupchutri')
            ->load('danhsach_nguoithamgia')
            ->load('danhsach_donvithamgia')
            ->load('danhsach_groupthamgia')
            ->load('nguoitao')
            ->load('donvitao');
        
        return $this->responseJson([
            'status' => 'ok',
            'lstLich' => $lst_lich
        ]);
    }
    
    public function getbyUserAndDiaDiem(Request $request){
        $user_id = $request->user_id;
        $diadiem_phonghop_id = $request->diadiem_phonghop_id;
        $lst_lichByUser_Ids = $this->getAllLichIdByUser($user_id);
        $list_lich = Lich::where(function($query) use($lst_lichByUser_Ids, $diadiem_phonghop_id){
                    $query->wherein('id', $lst_lichByUser_Ids);
            })
            ->where('trangthai', LichConstants::STATUS_ACCEPTED)
            ->orderby('thoigian_tu')
            ->get()->load('nguoichutri')->load('groupchutri');
        $list_lich = $list_lich->filter(function($lich) use($diadiem_phonghop_id){
            $lich->load('diadiem');
            $lst_dia_diem = $lich->filter(function($diadiem) use($diadiem_phonghop_id){
                return $diadiem->id == $diadiem_phonghop_id;
            });
            return count($lst_dia_diem) > 0;
        });
        foreach ($list_lich as $lich) {
            $lich->load('danhsach_nguoithamgia')
            ->load('danhsach_donvithamgia')
            ->load('danhsach_groupthamgia')
            ->load('nguoitao')
            ->load('donvitao');
        }
        return $this->responseJson([
            'status' => 'ok',
            'list_lich' => $list_lich
        ]);
    }
    public function getbyUserAndDiaDiemAndTime(Request $request){
        $user_id = $request->user_id;
        $diadiem_phonghop_id = $request->diadiem_phonghop_id;
        $thoigian_tu = $request->thoigian_tu;
        $thoigian_den = $request->thoigian_den;
        error_log($user_id.'----'.$diadiem_phonghop_id.'----'.$thoigian_tu.'----'.$thoigian_den);
        $lst_lichByUser_Ids = $this->getAllLichIdByUser($user_id);
        $list_lich = Lich::where(function($query)use($lst_lichByUser_Ids,$diadiem_phonghop_id){
            $query->wherein('id', $lst_lichByUser_Ids)
            ->orwhere('diadiem_phonghop_id',$diadiem_phonghop_id);
            })
            ->where('trangthai', LichConstants::STATUS_ACCEPTED)
            ->whereBetween('thoigian_tu',[$thoigian_tu,$thoigian_den])
            ->orderby('thoigian_tu')
            ->get()->load('nguoichutri')
            ->load('groupchutri')
            ->load('danhsach_nguoithamgia')
            ->load('danhsach_donvithamgia')
            ->load('danhsach_groupthamgia')
            ->load('nguoitao')
            ->load('donvitao');
        return $this->responseJson([
            'status' => 'ok',
            'list_lich' => $list_lich
        ]);
    }
    public function getByUser(Request $request){
        $user_id = $request->user_id;
        error_log($user_id);
        $lst_lichByUser_Ids = $this->getAllLichIdByUser($user_id);
        $list_lich = Lich::whereIn('id', $lst_lichByUser_Ids)
            ->where('trangthai', LichConstants::STATUS_ACCEPTED)
            ->orderby('thoigian_tu')
            ->get()
            ->load('nguoichutri')
            ->load('groupchutri')
            ->load('danhsach_nguoithamgia')
            ->load('danhsach_donvithamgia')
            ->load('danhsach_groupthamgia')
            ->load('nguoitao')
            ->load('donvitao');
        return $this->responseJson([
            'status' => 'ok',
            'list_lich' => $list_lich
        ]);
    }
    public function getByUserAndTime(Request $request){
        $user_id = $request->user_id;
        $thoigian_tu = $request->thoigian_tu;
        $thoigian_den = $request->thoigian_den;
        error_log($user_id);
        $lst_lichByUser_Ids = $this->getAllLichIdByUser($user_id);
        $list_lich = Lich::whereIn('id', $lst_lichByUser_Ids)
            ->where('trangthai', LichConstants::STATUS_ACCEPTED)
            ->whereBetween('thoigian_tu',[$thoigian_tu,$thoigian_den])
            ->orderby('thoigian_tu')
            ->get()->load('nguoichutri')->load('groupchutri')
            ->load('danhsach_nguoithamgia')
            ->load('danhsach_donvithamgia')
            ->load('danhsach_groupthamgia')
            ->load('nguoitao')
            ->load('donvitao');
        return $this->responseJson([
            'status' => 'ok',
            'list_lich' => $list_lich
        ]);
    }
    public function getAllLichIdByUser($user_id){
        $lich_thamgias = LichNguoiThamGia::where('user_id',$user_id)
        ->get('lich_id');
        $lst_lichthamgia_ids = $lich_thamgias->map(function ($lich) {
            return $lich->id;
        });
        
        $lichs = Lich::whereNotIn('id',$lst_lichthamgia_ids)
            ->where(function($query) use($user_id) {
                        $query->where('nguoichutri_id',$user_id)
                        ->orwhere(function($query) use($user_id){
                            $query->where('nguoitao_id', $user_id)
                            ->where('loai_lich', LichConstants::LOAI_LICH_CA_NHAN);
                        });
                    })
                    ->orderBy('thoigian_tu')
                    ->get('id');
        $lst_lichByUser_Ids =[];
        foreach ($lich_thamgias as $lichthamgia) {
            array_push($lst_lichByUser_Ids, $lichthamgia->lich_id);
        }
        foreach ($lichs as $lich) {
            array_push($lst_lichByUser_Ids, $lich->id);
        }
        return $lst_lichByUser_Ids;
    }
    public function getAllCoQuan(Request $request){
        error_log('getAllCoQuan 1');
        //for get all
        $isTroLyBGH = true;
        $donvi_id = isset($request->donvi_id) && $request->donvi_id > 0 ? $request->donvi_id : 0;
        if ($donvi_id == 0) {
            $user = User::find(auth()->user()->id);
            $user->load('donvis');
            $donvi_id = count($user->donvis) > 0 ? $user->donvis[0]->id : 0;
        }
        $lst_lich = LichService::getAllCoQuan($donvi_id,
            $request->thoigian_tu, $request->thoigian_den, $isTroLyBGH);
            error_log('getAllCoQuan 5');
        return $this->responseJson([
            'status' => 'ok',
            'lstLich' => $lst_lich
        ]);
    }
    public function GetAllCoQuanDangKy(Request $request){
        error_log($request->thoigian_tu.'-------'.$request->thoigian_den.'---------'.auth()->user()->id);
        $lst_lich = LichService::getAllCoQuanDangKy($request->thoigian_tu, $request->thoigian_den);
            
        return $this->responseJson([
            'status' => 'ok',
            'lstLich' => $lst_lich
        ]);
    }
    public function getAllPhongBan(Request $request){
        $donvi_id = isset($request->donvi_id) && $request->donvi_id > 0 ? $request->donvi_id : 0;
        if ($donvi_id == 0) {
            $user = User::find(auth()->user()->id);
            $user->load('donvis');
            $donvi_id = count($user->donvis) > 0 ? $user->donvis[0]->id : 0;
        }
        $lst_lich = LichService::getAllLichDonVi($donvi_id,
        $request->thoigian_tu, 
        $request->thoigian_den);
        return $this->responseJson([
            'status' => 'ok',
            'lstLich' => $lst_lich
        ]);
    }
    
    public function GetAllPhongBanDangKy(Request $request){
        $user = User::find(auth()->user()->id);
        $user->load('roles');
        $isTruongPho = false;
        foreach($user->roles as $role) {
            if ($role->code == 'egov_TRUONG_DON_VI'
            || $role->code == 'egov_PHO_DON_VI') {
                $isTruongPho = true;
            }
        }
        $user->load('donvis');
        foreach($user->donvis as $donvi) {
            if ($donvi->truong_dv == auth()->user()->id) {
                $isTruongPho = true;
            }
        }
        
        $lst_lich = LichService::getAllLichDonViDangKy(
            $request->donvi_id, $request->thoigian_tu, $request->thoigian_den, $isTruongPho);
        error_log('GetAllPhongBanByUser: '.count($lst_lich));
        return $this->responseJson([
            'status' => 'ok',
            'lstLich' => $lst_lich
        ]);
    }
	public function getAll (){
        $lichs =Lich::orderBy('thoigian_tu')
        ->get()
        ->load('nguoichutri')
        ->load('groupchutri')
        ->load('danhsach_nguoithamgia')
        ->load('danhsach_donvithamgia')
        ->load('danhsach_groupthamgia')
        ->load('nguoitao')
        ->load('donvitao');
        return $this->responseJson([
            'status' => 'ok',
            'lichs' => $lichs
        ]);
    }
    
    public function getLich(Request $request) {
        error_log('getLich: '.$request->id);
        $lich = Lich::find($request->id);
        if (isset($lich)) {
            $lich->load('nguoichutri')
            ->load('groupchutri')
            ->load('danhsach_nguoithamgia')
            ->load('danhsach_donvithamgia')
            ->load('danhsach_groupthamgia')
            ->load('nguoitao')
            ->load('diadiem')
            ->load('donvitao')
            ->load('attachedFiles')
            ->load('bienBanFiles');
        }
        return $this->responseJson([
            'status' => 'ok',
            'lich' => $lich
        ]);
    }
    //lich cong tac
    public function getAllLichCongTac(Request $request){
        error_log('getAllLich: '.$request->thoigian_tu.";---:".$request->thoigian_den);
        
        /* $lichNguoiChuTri_CaNhan_ids = Lich::where('nguoichutri_id', auth()->user()->id)
                            ->orwhere(function($query){
                                $query->where('nguoitao_id', auth()->user()->id)
                                ->where('loai_lich', LichConstants::LOAI_LICH_CA_NHAN);
                            })
                            ->get('id');

        $lichNguoiThamGia_ids = LichNguoiThamGia::where('user_id', auth()->user()->id)
                                            ->whereNotIn('lich_id', $lichNguoiChuTri_CaNhan_ids)    
                                            ->get('lich_id');
                    
        $lich_ids = [];
        foreach ($lichNguoiChuTri_CaNhan_ids as $lct){
            array_push($lich_ids, $lct->id);
        }
        foreach ($lichNguoiThamGia_ids as $ltg){
            array_push($lich_ids, $ltg->lich_id);
        } */
        $donvi_id = isset($request->donvi_id) && $request->donvi_id > 0 ? $request->donvi_id : 0;
        if ($donvi_id == 0) {
            $user = User::find(auth()->user()->id);
            $user->load('donvis');
            $donvi_id = count($user->donvis) > 0 ? $user->donvis[0]->id : 0;
        }
        $lichs = LichService::getAllLichCongTac($donvi_id, $request->thoigian_tu, $request->thoigian_den);
        return $this->responseJson([
            'status' => 'ok',
            'lstLich' => $lichs
        ]);
    }
    public function getAllLichCaNhan(Request $request){
        error_log('getAllLichCaNhan: '.auth()->user()->id.";---:".$request->thoigian_tu.";---:".$request->thoigian_den);
        
        $lichs = LichService::getAllLichCaNhan($request->thoigian_tu, $request->thoigian_den);
            
        return $this->responseJson([
            'status' => 'ok',
            'lstLich' => $lichs
        ]);
    }
    
    public function getLichDuyetCaNhanChuaConfirm(Request $request){
        $donvi_id = isset($request->donvi_id) && $request->donvi_id > 0 ? $request->donvi_id : 0;
        if ($donvi_id == 0) {
            $user = User::find(auth()->user()->id);
            $user->load('donvis');
            $donvi_id = count($user->donvis) > 0 ? $user->donvis[0]->id : 0;
        }
        $thoigian_tu = $request->thoigian_tu;
        $thoigian_den = $request->thoigian_den;
        error_log($donvi_id.'--------'.$thoigian_tu.'----'.$thoigian_den);
        $lst_lichDuyet = LichDuyet::where('donvi_id', $donvi_id)
            ->get()
            ->load('lich');
        $lich_ids = $lst_lichDuyet->map(function($lich) {
            return $lich->lich_id;
        });
        $lst_lich = Lich::whereIn('id', $lich_ids)
            ->whereBetween('thoigian_tu', [$thoigian_tu, $thoigian_den])
            ->get()
            ->load('lichduyet')
            ->load('nguoichutri')
            ->load('groupchutri')
            ->load('danhsach_nguoithamgia')
            ->load('danhsach_donvithamgia')
            ->load('danhsach_groupthamgia')
            ->load('nguoitao')
            ->load('donvitao');
        return $this->responseJson([
            'status' => 'ok',
            'lichs' => $lst_lich
        ]);
    }
    public function getAllLichCaNhanDangKy(Request $request){
        error_log('getallLichCaNhanDangKy--------'.$request->thoigian_tu.'--------'.$request->thoigian_den);
        $lst_lich = LichService::getAllLichCaNhanDangKy($request->thoigian_tu, 
            $request->thoigian_den);
        return $this->responseJson([
            'status' => 'ok',
            'lstLich' => $lst_lich
        ]);
    }
    public function setUserThamgia(Request $request){
        $donvi_id = $request->params['donvi_id'];
        $user_id = $request->params['user_id'];
        $lich_id = $request->params['lich_id'];
        error_log($donvi_id.'--------'.$user_id.'--------'.$lich_id);
        LichNguoiThamGia::where('lich_id', $lich_id)
            ->where('daidien_donvi_id', $donvi_id)
            ->update(['user_id'=>$user_id]);
        return $this->responseJson([
            'status' => 'ok',
            
        ]);
    }
    public function UpdateStatusThamGia(Request $request){
        $lich_id = $request->params['lich_id'];
        $status = $request->params['status'];
        error_log($lich_id.'--------'.$status);
        LichNguoiThamGia::where('lich_id', $lich_id)
            ->where('user_id',auth()->user()->id)
            ->update(['status'=>$status]);
        return $this->responseJson([
            'status' => 'ok',
        ]);
    }
    public function LichByIDTimeTrangthai($lst_lich_id,$thoigian_tu,$thoigian_den,$trangthais){
        $lst_lich = Lich::wherein('id', $lst_lich_id)
            ->wherein('trangthai', $trangthais)
            ->whereBetween('thoigian_tu',[$thoigian_tu,$thoigian_den])
            ->orderby('thoigian_tu')
            ->get()
            ->load('nguoichutri')
            ->load('groupchutri')
            ->load('danhsach_nguoithamgia')
            ->load('danhsach_donvithamgia')
            ->load('danhsach_groupthamgia')
            ->load('nguoitao')
            ->load('donvitao')
            ->load('diadiem');
        return $lst_lich;
    }
    public function getLichChuTriTrung(Request $request) {
        error_log('get danh sách tru trì trùng');
        
        $lst_lich_by_chutri = LichService::getAllLichTrungByUserIdAndTime(
            $request->nguoichutri_id,
            $request->id,
            $request->thoigian_tu,
            $request->thoigian_den
        );
        return $this->responseJson([
            'status' => 'ok', //false
            'list_lich' => $lst_lich_by_chutri
        ]);
    }
    public function joinLich(Request $request) {
        error_log('joinLich: '.$request->id);
        $lich = Lich::find($request->id);
        if (!isset($lich)) {
            return $this->responseJson([
                'status' => 'ng',
                'message' => 'Không tìm thấy lịch'
            ]);
        }
        
        LichService::addOrUpdateMemberThamGia($lich->id,
            $request->donvi_id,
            $request->lst_user_id,
            $request->status, //LichConstants::NGUOI_THAM_GIA_ACCEPTED,
            $request->act_mode
        );
        $lich->load('nguoichutri')
        ->load('groupchutri')
            ->load('danhsach_nguoithamgia')
            ->load('danhsach_donvithamgia')
            ->load('danhsach_groupthamgia')
            ->load('nguoitao')
            ->load('donvitao')
            ->load('diadiem')
            ->load('attachedFiles')
            ->load('bienBanFiles');
        return $this->responseJson([
            'status' => 'ok',
            'lich' => $lich
        ]);
    }
    public function cancelJoinLich(Request $request) {
        error_log('cancelJoinLich: '.$request->id);
        $lich = Lich::find($request->id);
        if (!isset($lich)) {
            return $this->responseJson([
                'status' => 'ng',
                'message' => 'Không tìm thấy lịch'
            ]);
        }
        
        LichNguoiThamGia::where('lich_id', $lich->id)
            ->where('user_id', auth()->user()->id)
            ->update(['status' =>LichConstants::NGUOI_THAM_GIA_CANCELLED]);
            
        return $this->responseJson([
            'status' => 'ok'
        ]);
    }
}
