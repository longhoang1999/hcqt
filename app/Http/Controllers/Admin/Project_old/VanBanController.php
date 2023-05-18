<?php

namespace App\Http\Controllers;

use App\Common\Constant\UploadFileConstants;
use App\Common\Constant\VanBanConstants;
use App\Models\DonVi;
use App\Models\SoPhatHanhVanBan;
use App\Models\SoVanBan_VanBan;
use App\Models\User;
use App\Models\VanBan;
use App\Models\VanBan_ButPhe;
use App\Models\VanBan_ButPhe_GiaoViec;
use App\Models\VanBan_Comment;
use App\Models\VanBan_HoatDong;
use App\Models\VanBan_NoiNhan;
use App\Models\VanBan_XinYKien;

use App\Services\VanBanService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Http\Request;

use function PHPSTORM_META\type;

class VanBanController extends Base\BaseController
{
    public function __construct(){

    }
    public function insertOrUpdate(Request $request){
        $err_code = 0;
        try{
            if (isset($request->id)) {
                $vanBan = VanBan::find($request->id);
            } 
            if (!isset($vanBan)) 
            {
                $vanBan = new VanBan();
            }
            
            // thêm số thứ tự
            // $vanBanGanNhat = VanBan::where('phanloai',$request->phanloai)
            //                     ->where('id','!=',$vanBan->id)                 
            //                     ->orderBy('sothutu','desc')->first();
            // if (!isset($vanBanGanNhat)){
            //     $vanBan->sothutu = 1;
            // }else {
            //     $vanBan->sothutu = $vanBanGanNhat->sothutu+1;
            // }
            if (is_array($request->noinhan_noibo_ids) && count($request->noinhan_noibo_ids)>0 && $request->noinhan_noibo_ids[0]  == 'ADV') {
                $vanBan->noinhan_noibo_ids = 'ADV';
            }else {
                $vanBan->noinhan_noibo_ids = null;
            }
            if (is_array($request->noinhan_benngoai_ids) && count($request->noinhan_benngoai_ids)>0 && $request->noinhan_benngoai_ids[0]  == 'ADV') {
                $vanBan->noinhan_benngoai_ids = 'ADV';
            }else {
                error_log('ádsadsa');
                $vanBan->noinhan_benngoai_ids = null;
            }
            error_log('ádsadsa');
            $vanBan->phanloai = $request->phanloai;
            $vanBan->tenvanban = $request->tenvanban;
            $vanBan->trich_yeu = $request->trich_yeu;
            $vanBan->sothutu = $request->sothutu;
            $vanBan->hanxuly = $request->hanxuly;
            
            $vanBan->sophathanhvanban_id = $request->sophathanhvanban_id;
            $vanBan->sokyhieuvanban = $request->sokyhieuvanban;
            
            $vanBan->loaivanban_id = $request->loaivanban_id;
            $vanBan->so_van_ban_id = $request->so_van_ban_id;
            $vanBan->coquanphathanhvanban_id = $request->coquanphathanhvanban_id;
            $vanBan->domatvanban_id = $request->domatvanban_id;
            $vanBan->dokhanvanban_id = $request->dokhanvanban_id;
            $vanBan->ngayden = $request->ngayden;
            $vanBan->ngayphathanh = $request->ngayphathanh;
            $vanBan->ngaycohieuluc = $request->ngaycohieuluc;
            $vanBan->ngayhethieuluc = $request->ngayhethieuluc;
            $vanBan->soban = $request->soban ? $request->soban : 1;
            $vanBan->soto = $request->soto;
            $donvi_id = isset($request->donvi_id) && $request->donvi_id > 0 ? $request->donvi_id : 0;
            if ($request->phanloai == VanBanConstants::PHAN_LOAI_VAN_BAN_PHAT_HANH 
                && $donvi_id == 0) {
                    $donvi_id = auth()->user() != null && isset(auth()->user()->donvis) && count(auth()->user()->donvis) > 0 ?
                        auth()->user()->donvis[0]->id : 0;
            }
            $vanBan->donvi_phathanh_id = $donvi_id;
            $vanBan->nguoi_pheduyet_id = $request->nguoi_pheduyet_id;
            $vanBan->ngay_pheduyet = $request->ngay_pheduyet;
            $vanBan->minhchung = $request->minhchung;
            if ($vanBan->phanloai == VanBanConstants::PHAN_LOAI_VAN_BAN_DEN) {
                $vanBan->trangthai = VanBanConstants::DU_THAO;
            }else {
                if ($vanBan->trangthai <= 0 && isset($request->trangthai) && $request->trangthai > 0) {
                    $vanBan->trangthai = $request->trangthai;
                }
            }
            

            if ($vanBan->trangthai == VanBanConstants::TU_CHOI_THE_THUC) {
                $vanBan->trangthai = VanBanConstants::DA_SUA_TU_CHOI_THE_THUC;
            } else if ($vanBan->trangthai == VanBanConstants::TU_CHOI_KY) {
                $vanBan->trangthai = VanBanConstants::DA_SUA_TU_CHOI_KY;
            }
            
            error_log($vanBan->trangthai);
            DB::transaction(function () use (&$vanBan, $request, &$error_code) {
                $vanBan->save();
                //;$vanBan->load('DsHoatDong');
                $vanBan->DsHoatDong()->sync($request->hoatdongvanban_ids);
                if (isset($request->so_van_ban_id)) {
                    $this->vaoSoDen($vanBan,$request->so_van_ban_id,$request->phanloai);
                }
                error_log('----------insertOrUpdate-------');
                error_log('----------insertOrUpdate----3---');
                VanBanService::insertOrUpdateNoiNhan($vanBan->id,$request->noinhan_noibo_ids,$request->noinhan_benngoai_ids);
                
                

                //cap nhan sử dụng số-ký hiệu - end
                //luu vào các bảng quan hệ khác
                //bang thay the
                VanBanService::vanBanThayThe($vanBan->id, $request->ghichu, 
                    $request->ngaycohieuluc, $request->ds_van_ban_bi_thay_the_mot_phan_ids,
                    $request->ds_van_ban_bi_thay_the_toan_phan_ids,
                    $request->ds_van_ban_bi_thu_hoi_ids, $request->ds_van_ban_phuc_dap_ids
                );
            }, 3);
            

            return $this->responseJson(array(
                'status'=>'ok',
                'van_ban' => $vanBan
            ));
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => $err_code, //danh sach to (receivers) bi rong
                'err_message' => $e->getMessage()
                ]);
        }
    }

    public function vaoSoDen($vanban,$so_van_ban_id,$phanloai_vanban) {
        try{
            
            DB::transaction(function () use ($vanban, $so_van_ban_id,$phanloai_vanban) {
                $trangthai = $phanloai_vanban == 1 ? VanBanConstants::VAO_SO_BAN_HANH : VanBanConstants::VAO_SO_VAN_BAN_DEN;
                //ghi comment
                
                // ghi vào bảng quan hệ sổ văn bản - văn bản
                $vanban->load('DsSoVanBan');
                $isUpdated = false;
                if (count($vanban->DsSoVanBan) > 0) {
                    
                    foreach($vanban->DsSoVanBan as $sovanban) {
                        if ($sovanban->id == $so_van_ban_id) {
                            // cập nhật số trang và tổng số
                            VanBanService::updateSoVanBan($sovanban->id);
                            $isUpdated = true;
                        } else {
                            $vanban->DsSoVanBan()->detach($sovanban->id);
                            VanBanService::updateSoVanBan($sovanban->id);
                        }
                    }
                }
                if(!$isUpdated) {
                    SoVanBan_VanBan::updateOrCreate(
                        [
                    'vanban_id' => $vanban->id
                    ],
                        ['sovanban_id' => $so_van_ban_id,
                    'vanban_id' => $vanban->id]
                    );
                    // cập nhật số trang và tổng số
                    VanBanService::updateSoVanBan($so_van_ban_id);
                }
            }, 3);return $this->responseJson(array(
            'status'=>'ok',
        ));
        return $this->responseJson(array(
            'status'=>'ok',
            'vanban' => $vanban
        ));
        
        } catch(Exception $e) {
            return $this->responseJson(array(
                'status'=>'ng',
                'message' => $e->getMessage()
            ));
        }
    }
    //GetAllVanBan
    //using
    public function getDsVanBan(){
        try{
            $lstVanBan = VanBan::orderBy('ngayden', 'desc')
                                ->orderBy('updated_at', 'desc')
                                ->get()->load('SoPhatHanh');
            return $this->responseJson([
                'status' => 'ok',
                'ds_van_ban' => $lstVanBan
            ]);
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'ds_van_ban' => null
                ]);
        }
    }
    //Văn bản đến
    //using 
    public function getDsVanBanDen(){
        try{
            $lstVanBan = VanBan::where('phanloai',VanBanConstants::PHAN_LOAI_VAN_BAN_DEN)
                                ->orderBy('ngayden', 'desc')
                                ->orderBy('updated_at', 'desc')
                                ->get()->load('SoPhatHanh');
            return $this->responseJson([
                'status' => 'ok',
                'ds_van_ban' => $lstVanBan
            ]);
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'ds_van_ban' => null
                ]);
        }
    }

    public function getDsTraCuuVanBanDen(Request $request){
        try {
            $isThuThu = false;
            $isTruongPho = false;
            $isButPhe = false;
            $list_trangthai = [];
                if (auth()->user() != null && isset(auth()->user()->roles)) {
                foreach(auth()->user()->roles as $role) {
                    if ($role->code == 'egov_THU_THU') {
                        $isThuThu = true;
                        $list_trangthai = [VanBanConstants::DU_THAO,VanBanConstants::VAO_SO_VAN_BAN_DEN,VanBanConstants::DANG_KY
                        ,VanBanConstants::BUT_PHE, VanBanConstants::DANG_KY,VanBanConstants::TU_CHOI_BUT_PHE
                        ,VanBanConstants::CHUYEN_XU_LY,VanBanConstants::PHAN_CONG_THUC_HIEN
                        ,VanBanConstants::DANG_THUC_HIEN,VanBanConstants::DA_THUC_HIEN,VanBanConstants::HOAN_THANH_THUC_HIEN,
                        VanBanConstants::DANG_BAO_CAO_PHAN_HOI,
                        VanBanConstants::XAC_NHAN_HOAN_THANH_BAO_CAO,
                        VanBanConstants::CHUA_XAC_NHAN_HOAN_THANH_BAO_CAO,
                        VanBanConstants::HOAN_THANH];
                    }
                    else if ($role->code == 'egov_but_phe') {
                        $isButPhe = true;
                        $list_trangthai = [VanBanConstants::BUT_PHE,VanBanConstants::TU_CHOI_BUT_PHE
                        ,VanBanConstants::CHUYEN_XU_LY,VanBanConstants::PHAN_CONG_THUC_HIEN
                        ,VanBanConstants::DANG_THUC_HIEN,VanBanConstants::DA_THUC_HIEN,VanBanConstants::HOAN_THANH_THUC_HIEN,
                        VanBanConstants::DANG_BAO_CAO_PHAN_HOI,
                        VanBanConstants::XAC_NHAN_HOAN_THANH_BAO_CAO,
                        VanBanConstants::CHUA_XAC_NHAN_HOAN_THANH_BAO_CAO,
                        VanBanConstants::HOAN_THANH];
                    }
                    else if ($role->code == 'egov_TRUONG_ƯDON_VI' || $role->code == 'egov_PHO_DON_VI') {
                        $isTruongPho = true;
                        $list_trangthai = [VanBanConstants::CHUYEN_XU_LY,VanBanConstants::PHAN_CONG_THUC_HIEN
                        ,VanBanConstants::DANG_THUC_HIEN,VanBanConstants::DA_THUC_HIEN,VanBanConstants::HOAN_THANH_THUC_HIEN,
                        VanBanConstants::DANG_BAO_CAO_PHAN_HOI,
                        VanBanConstants::XAC_NHAN_HOAN_THANH_BAO_CAO,
                        VanBanConstants::CHUA_XAC_NHAN_HOAN_THANH_BAO_CAO,
                        VanBanConstants::HOAN_THANH];
                    }else {
                        $list_trangthai = [VanBanConstants::CHUYEN_XU_LY,VanBanConstants::PHAN_CONG_THUC_HIEN
                        ,VanBanConstants::DANG_THUC_HIEN,VanBanConstants::DA_THUC_HIEN,VanBanConstants::HOAN_THANH_THUC_HIEN,
                        VanBanConstants::DANG_BAO_CAO_PHAN_HOI,
                        VanBanConstants::XAC_NHAN_HOAN_THANH_BAO_CAO,
                        VanBanConstants::CHUA_XAC_NHAN_HOAN_THANH_BAO_CAO,
                        VanBanConstants::HOAN_THANH];
                    }
                }
            }
            $lstVanBan = VanBan::where('phanloai', VanBanConstants::PHAN_LOAI_VAN_BAN_DEN)
            ->wherein('trangthai', $list_trangthai)
            ->orderBy('ngayden', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->load('NguoiDangKy')
            ->load('SoVanBan')
            ->load('NhomNguoiDung')
            ->load('CoQuanPhatHanhVanBan')
            ->load('SoPhatHanh')
            ->load('LoaiVanBan')
            ->load('DoKhanVanBan')
            ->load('DsHoatDong')
            ->load('DoMatVanBan')
            ->load('DsNoiNhan')
            ->load('NguoiPheDuyet')
            ->load('dsComment')
            ->load('attachedFiles')
            ->load('DsVanBanThayThe')
            ->load('DsButPhe')
            ->load('DsGiaoViec')
            ->load('DsXinYKien')
            ->load('NguoiKiemTraThucThe')
            ->load('NguoiTrinhKy')
            ->load('NguoiPhatHanh')
            ->load('DonViPhatHanh')
            ->load('NguoiXacNhanHoanThanh')
            ->load('NguoiTrinhChuyen')
            ->load('DsSoVanBan');
            if (!$isThuThu && !$isButPhe) {
                if ($isTruongPho) {
                    $donvi_id = (auth()->user() != null && isset(auth()->user()->donvis) && count(auth()->user()->donvis) > 0) ? 
                    auth()->user()->donvis[0]->id : 0;
                    $lstVanBan = $lstVanBan->filter(function ($vanban) use($donvi_id) {
                            return ($vanban->DsGiaoViec->filter(function($butphe) use($donvi_id) {
                                return $butphe->donvi_xuly_id == $donvi_id;
                            })->count() > 0) || ($vanban->DsButPhe->filter(function ($butphe) use ($donvi_id) {
                                return $butphe->donvi_xuly_id == $donvi_id || $butphe->nguoi_xuly_id == auth()->user()->id;
                            })->count() > 0);
                    });
                } else {
                    $lstVanBan = $lstVanBan->filter(function ($vanban) {
                        return ($vanban->DsGiaoViec->filter(function ($butphe) {
                            return $butphe->nguoi_xuly_id == auth()->user()->id;
                        })->count() > 0) || ($vanban->DsButPhe->filter(function ($butphe) {
                            return $butphe->nguoi_xuly_id == auth()->user()->id;
                        })->count() > 0);
                    });
                }
            }
            foreach($lstVanBan as $vanban) {
                $vanban->LoaiVanBan->load('NhomVanBan');
                $vanban->DsVanBanThayThe->load('SoPhatHanh');
                $vanban->DsNoiNhan->load('DonViNhanVanBan')->load('NguoiNhanVanBan');
                $vanban->DsXinYKien->load('DonViDuocXinYKien')->load('NguoiDuocXinYKien');
                $vanban->dsButPhe->load('NguoiButPhe')->load('DonViXuLy')->load('NguoiXyLy');
                $vanban->DsGiaoViec->load('NguoiGiaoViec')->load('NguoiXuLy');
                if (isset($vanban->NhomNguoiDung)) {
                    $vanban->NhomNguoiDung->load('users');
                }
            }
            
            return $this->responseJson([
                'status' => 'ok',
                'ds_van_ban' => $lstVanBan
            ]);
        }catch(Exception $e) {
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'ds_van_ban' => null
                ]);
        }
    }

    //using
    public function getDsVanBanTiepNhan(Request $request){
        $hasAllPermision = false;
        if (auth()->user() != null && isset(auth()->user()->roles)) {
            foreach(auth()->user()->roles as $role) {
                if ($role->code == 'egov_THU_THU') {
                    $hasAllPermision = true;
                }
            }
        }
        if (!$hasAllPermision) {
            return $this->responseJson([
                'status' => 'ok',
                'ds_van_ban' => [],
                'message' => 'Không có quyền'
            ]);
        }
        $list_trangthai = [VanBanConstants::DU_THAO,VanBanConstants::VAO_SO_VAN_BAN_DEN,VanBanConstants::DANG_KY];
        try{
            $lstVanBan = VanBanService::getDsVanBanAll(
                VanBanConstants::PHAN_LOAI_VAN_BAN_DEN,
                $list_trangthai); 
                    
            return $this->responseJson([
                'status' => 'ok',
                'ds_van_ban' => $lstVanBan
            ]);
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'ds_van_ban' => null
                ]);
        }
        
    }
    //using
    public function getDsVanBanTrinhChuyen(Request $request){
        $hasAllPermision = false;
        if (auth()->user() != null && isset(auth()->user()->roles)) {
            foreach(auth()->user()->roles as $role) {
                if ($role->code == 'egov_THU_THU'
                    || $role->code == 'egov_BUT_PHE'
                    || $role->code == 'egov_LANH_DAO') {
                    $hasAllPermision = true;
                }
            }
        }
        if (!$hasAllPermision) {
            return $this->responseJson([
                'status' => 'ok',
                'ds_van_ban' => [],
                'message' => 'Không có quyền'
            ]);
        }
        $list_trangthai = [VanBanConstants::BUT_PHE, VanBanConstants::DANG_KY,VanBanConstants::TU_CHOI_BUT_PHE,VanBanConstants::CHUYEN_XU_LY];
        try{
            // Danh cho thu thu
            $lstVanBan = VanBanService::getDsVanBanAll(
                VanBanConstants::PHAN_LOAI_VAN_BAN_DEN,
                $list_trangthai); 
            return $this->responseJson([
                'status' => 'ok',
                'ds_van_ban' => $lstVanBan
            ]);
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'ds_van_ban' => null
                ]);
        }
        
    }
    //using
    // public function getDsVanBanGiaoViec(Request $request){
    //     $hasAllPermision = false;
    //     $isTruongPho = false;
    //     if (auth()->user() != null && isset(auth()->user()->roles)) {
    //         foreach(auth()->user()->roles as $role) {
    //             if ($role->code == 'egov_THU_THU') {
    //                 $hasAllPermision = true;
    //             }
    //             if ($role->code == 'egov_TRUONG_DON_VI'
    //                 || $role->code == 'egov_PHO_DON_VI') {
    //                 $isTruongPho = true;
    //             }
    //         }
    //     }
        
    //     $list_trangthai = [VanBanConstants::BUT_PHE, VanBanConstants::CHUYEN_XU_LY];
    //     try{
    //         $lstVanBan = VanBanService::getDsVanBanAll(
    //             VanBanConstants::PHAN_LOAI_VAN_BAN_DEN,
    //             $list_trangthai);
    //         if (!$hasAllPermision) {
    //             $donvi_id = (auth()->user() != null && isset(auth()->user()->donvis) && count(auth()->user()->donvis) > 0) ?
    //             auth()->user()->donvis[0]->id : 0;
    //             if ($isTruongPho) {
    //                 $lstVanBan = $lstVanBan->filter(function ($vanban) use ($donvi_id) {
    //                     return $vanban->DsButPhe->filter(function ($butphe) use ($donvi_id) {
    //                         return $butphe->donvi_xuly_id == $donvi_id || $butphe->nguoi_xuly_id == auth()->user()->id;
    //                     })->count() > 0;
    //                 });
    //             } else {
    //                 $lstVanBan = $lstVanBan->filter(function ($vanban) use ($donvi_id) {
    //                     return $vanban->DsButPhe->filter(function ($butphe) use ($donvi_id) {
    //                         return $butphe->nguoi_xuly_id == auth()->user()->id;
    //                     })->count() > 0;
    //                 });
    //             }
    //         }
    //         return $this->responseJson([
    //             'status' => 'ok',
    //             'ds_van_ban' => $lstVanBan
    //         ]);
    //     }catch(Exception $e){
    //         error_log($e->getMessage());
    //         return $this->responseJson([
    //             'status' => 'error',
    //             'err_code' => '1', 
    //             'err_message' => $e->getMessage(),
    //             'ds_van_ban' => null
    //             ]);
    //     }
        
    // }
    //using
    public function getDsVanBanXuLyVanBan(Request $request){
        $hasAllPermision = false;
        $isTruongPho = false;
        if (auth()->user() != null && isset(auth()->user()->roles)) {
            foreach(auth()->user()->roles as $role) {
                if ($role->code == 'egov_THU_THU' || $role->code == 'egov_BUT_PHE') {
                    $hasAllPermision = true;
                }
                if ($role->code == 'egov_TRUONG_DON_VI'
                    || $role->code == 'egov_PHO_DON_VI') {
                    $isTruongPho = true;
                }
            }
        }
        
        $list_trangthai = [
            VanBanConstants::BUT_PHE, VanBanConstants::CHUYEN_XU_LY,
            VanBanConstants::PHAN_CONG_THUC_HIEN,
            VanBanConstants::DANG_THUC_HIEN,
            VanBanConstants::DA_THUC_HIEN,
            // VanBanConstants::HOAN_THANH_THUC_HIEN,
        ];
        try{
            $lstVanBan = VanBanService::getDsVanBanAll(
                VanBanConstants::PHAN_LOAI_VAN_BAN_DEN,
                $list_trangthai);
            if (!$hasAllPermision) {
                if ($isTruongPho) {
                    $donvi_id = (auth()->user() != null && isset(auth()->user()->donvis) && count(auth()->user()->donvis) > 0) ? 
                    auth()->user()->donvis[0]->id : 0;
                    $lstVanBan = $lstVanBan->filter(function ($vanban) use($donvi_id) {
                            return ($vanban->DsGiaoViec->filter(function($butphe) use($donvi_id) {
                                return $butphe->donvi_xuly_id == $donvi_id;
                            })->count() > 0) || ($vanban->DsButPhe->filter(function ($butphe) use ($donvi_id) {
                                return $butphe->donvi_xuly_id == $donvi_id || $butphe->nguoi_xuly_id == auth()->user()->id;
                            })->count() > 0);
                    });
                } else {
                    $lstVanBan = $lstVanBan->filter(function ($vanban) {
                        return ($vanban->DsGiaoViec->filter(function ($butphe) {
                            return $butphe->nguoi_xuly_id == auth()->user()->id;
                        })->count() > 0) || ($vanban->DsButPhe->filter(function ($butphe) {
                            return $butphe->nguoi_xuly_id == auth()->user()->id;
                        })->count() > 0);
                    });
                }
            }
            return $this->responseJson([
                'status' => 'ok',
                'ds_van_ban' => $lstVanBan
            ]);
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'ds_van_ban' => null
                ]);
        }
        
    }
    //using
    public function getDsVanBanPhucDapLuuTru(Request $request){
        
        $hasAllPermision = false;
        $isTruongPho = false;
        if (auth()->user() != null && isset(auth()->user()->roles)) {
            foreach(auth()->user()->roles as $role) {
                if ($role->code == 'egov_THU_THU' || $role->code == 'egov_BUT_PHE') {
                    $hasAllPermision = true;
                }
                if ($role->code == 'egov_TRUONG_DON_VI'
                    || $role->code == 'egov_PHO_DON_VI') {
                    $isTruongPho = true;
                }
            }
        }
        
        $list_trangthai = [
            VanBanConstants::HOAN_THANH_THUC_HIEN,
            VanBanConstants::DANG_BAO_CAO_PHAN_HOI,
            VanBanConstants::XAC_NHAN_HOAN_THANH_BAO_CAO,
            VanBanConstants::CHUA_XAC_NHAN_HOAN_THANH_BAO_CAO,
            VanBanConstants::HOAN_THANH
            ];
        try{
            $lstVanBan = VanBanService::getDsVanBanAll(
                VanBanConstants::PHAN_LOAI_VAN_BAN_DEN,
                $list_trangthai);
            if (!$hasAllPermision) {
                if ($isTruongPho) {
                    $donvi_id = (auth()->user() != null && isset(auth()->user()->donvis) && count(auth()->user()->donvis) > 0) ? 
                    auth()->user()->donvis[0]->id : 0;
                    error_log($donvi_id);
                    $lstVanBan = $lstVanBan->filter(function ($vanban) use($donvi_id) {
                        return $vanban->DsButPhe->filter(function($butphe) use($donvi_id) {
                            return $butphe->donvi_xuly_id == $donvi_id && $butphe->is_main;
                        })->count() > 0;
                    });
                    
                } else {
                    $lstVanBan = $lstVanBan->filter(function ($vanban) {
                        return $vanban->DsButPhe->filter(function ($butphe) {
                            return $butphe->nguoi_xuly_id == auth()->user()->id && $butphe->is_main;;
                        })->count() > 0;
                    });
                }
            }
            return $this->responseJson([
                'status' => 'ok',
                'ds_van_ban' => $lstVanBan
            ]);
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'ds_van_ban' => null
                ]);
        }
        
    }


    //Văn bản dự thảo

    //Using
    public function getDsTraCuuVanBanDuThao(Request $request){
        try {
            $isTruongPho = false;
            $isKiemTraThucThe = false;
            $isTrinhKy = false;
            $thoigian_tu = $request->thoigian_tu;
            $thoigian_den = $request->thoigian_den;
            $list_trangthai = [VanBanConstants::DU_THAO,
                                        VanBanConstants::DANG_KY,
                                        VanBanConstants::XIN_SO,VanBanConstants::LAY_Y_KIEN,
                                        VanBanConstants::KIEM_TRA_THE_THUC,
                                        VanBanConstants::DONG_Y_THE_THUC,
                                        VanBanConstants::TU_CHOI_THE_THUC,
                                        VanBanConstants::DA_SUA_TU_CHOI_THE_THUC,VanBanConstants::TRINH_KY];
            if (auth()->user() != null && isset(auth()->user()->roles)) {
                foreach(auth()->user()->roles as $role) {
                    if ($role->code == 'egov_TRUONG_DON_VI'
                        || $role->code == 'egov_PHO_DON_VI') {
                        $isTruongPho = true;
                        $list_trangthai_Valid = [VanBanConstants::DU_THAO,
                                        VanBanConstants::DANG_KY,
                                        VanBanConstants::XIN_SO,VanBanConstants::LAY_Y_KIEN,
                                        VanBanConstants::KIEM_TRA_THE_THUC,
                                        VanBanConstants::DONG_Y_THE_THUC,
                                        VanBanConstants::TU_CHOI_THE_THUC,
                                        VanBanConstants::DA_SUA_TU_CHOI_THE_THUC,VanBanConstants::TRINH_KY];
                    }
                    if ($role->code == 'egov_PHAP_CHE') {
                        $isKiemTraThucThe = true;
                        $list_trangthai_Valid = [
                                        VanBanConstants::KIEM_TRA_THE_THUC,
                                        VanBanConstants::DONG_Y_THE_THUC,
                                        VanBanConstants::TU_CHOI_THE_THUC,
                                        VanBanConstants::DA_SUA_TU_CHOI_THE_THUC,VanBanConstants::TRINH_KY];
                    }
                    if ($role->code == 'egov_TRINH_KY') {
                        $isTrinhKy = true;
                        $list_trangthai_Valid = [
                            VanBanConstants::DONG_Y_THE_THUC,
                            VanBanConstants::TRINH_KY];
                    }
                }
            }
            $lstVanBan = [];
            $lstVanBan_all = VanBan::where('phanloai', VanBanConstants::PHAN_LOAI_VAN_BAN_PHAT_HANH)
            ->wherein('trangthai', $list_trangthai)
            ->whereBetween('ngayphathanh',[$thoigian_tu,$thoigian_den])
            ->orderBy('ngayden', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->load('NguoiDangKy')
            ->load('SoVanBan')
            ->load('NhomNguoiDung')
            ->load('CoQuanPhatHanhVanBan')
            ->load('SoPhatHanh')
            ->load('LoaiVanBan')
            ->load('DoKhanVanBan')
            ->load('DsHoatDong')
            ->load('DoMatVanBan')
            ->load('DsNoiNhan')
            ->load('NguoiPheDuyet')
            ->load('dsComment')
            ->load('attachedFiles')
            ->load('DsVanBanThayThe')
            ->load('DsButPhe')
            ->load('DsGiaoViec')
            ->load('DsXinYKien')
            ->load('NguoiKiemTraThucThe')
            ->load('NguoiTrinhKy')
            ->load('NguoiPhatHanh')
            ->load('DonViPhatHanh')
            ->load('NguoiXacNhanHoanThanh')
            ->load('NguoiTrinhChuyen')
            ->load('DsSoVanBan');
                if ($isTruongPho) {
                    $donvi_id = (auth()->user() != null && isset(auth()->user()->donvis) && count(auth()->user()->donvis) > 0) ?
                        auth()->user()->donvis[0]->id : 0;
                
                    foreach($lstVanBan_all as $vanban) {
                        $count_cho_y_kien = $vanban->DsXinYKien->filter(function($xinykien) use($donvi_id) {
                            return $xinykien->donvi_id == $donvi_id;
                        })->count() ;
                        if ($vanban->donvi_phathanh_id == $donvi_id || $count_cho_y_kien > 0) {
                            array_push($lstVanBan, $vanban);
                        }
                        if ($vanban->donvi_phathanh_id == $donvi_id) {
                            array_push($lstVanBan, $vanban);
                        }
                    }
                } 
                if ($isKiemTraThucThe) {
                    foreach($lstVanBan_all as $vanban) {
                        if (in_array($vanban->trangthai,$list_trangthai_Valid)) {
                            array_push($lstVanBan, $vanban);
                        }
                    }
                }
                if ($isTrinhKy) {
                    foreach($lstVanBan_all as $vanban) {
                        if (in_array($vanban->trangthai,$list_trangthai_Valid)) {
                            array_push($lstVanBan, $vanban);
                        }
                    }
                }
                foreach($lstVanBan_all as $vanban) {
                    if ($vanban->created_by == auth()->user()->id) {
                        array_push($lstVanBan, $vanban);
                    }
                    
                }
                foreach($lstVanBan as $vanban) {
                    $vanban->LoaiVanBan->load('NhomVanBan');
                    $vanban->DsVanBanThayThe->load('SoPhatHanh');
                    $vanban->DsNoiNhan->load('DonViNhanVanBan')->load('NguoiNhanVanBan');
                    $vanban->DsXinYKien->load('DonViDuocXinYKien')->load('NguoiDuocXinYKien');
                    $vanban->dsButPhe->load('NguoiButPhe')->load('DonViXuLy')->load('NguoiXyLy');
                    $vanban->DsGiaoViec->load('NguoiGiaoViec')->load('NguoiXuLy');
                    if (isset($vanban->NhomNguoiDung)) {
                        $vanban->NhomNguoiDung->load('users');
                    }
                }
            return $this->responseJson([
                'status' => 'ok',
                'ds_van_ban' => $lstVanBan
            ]);
        }catch(Exception $e) {
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'ds_van_ban' => null
                ]);
        }
    }
    //using
    public function getDsVanBanDuThao(Request $request){
        $isTruongPho = false;
        if (auth()->user() != null && isset(auth()->user()->roles)) {
            foreach(auth()->user()->roles as $role) {
                if ($role->code == 'egov_TRUONG_DON_VI'
                    || $role->code == 'egov_PHO_DON_VI') {
                    $isTruongPho = true;
                }
            }
        }
        error_log('getDsVanBanDuThao');
        $list_trangthai = [VanBanConstants::DU_THAO,
            VanBanConstants::DANG_KY,VanBanConstants::DA_CAP_SO_PHAT_HANH,
            VanBanConstants::LAY_Y_KIEN];
        try{
            $lstVanBan = [];
                $lstVanBan = VanBanService::getDsVanBanNguoiTao(
                    VanBanConstants::PHAN_LOAI_VAN_BAN_PHAT_HANH,
                    $list_trangthai, auth()->user()->id);
            $user = User::find(auth()->user()->id);
            $user->load('groups');
            foreach ($user->groups as $group) {
                $group->load('vanban');
                if (isset($group->vanban)) {
                    if (in_array($group->vanban->trangthai,$list_trangthai)) {
                        $group->vanban->load('NguoiDangKy')
                        ->load('SoPhatHanh')
                        ->load('LoaiVanBan')
                        ->load('DoKhanVanBan')
                        ->load('DoMatVanBan')
                        ->load('CoQuanPhatHanhVanBan')   //Lay theo nguoi thuoc don vi
                        ->load('attachedFiles')
                        ->load('DsButPhe')
                        ->load('DoKhanVanBan')
                        ->load('DoMatVanBan')
                        ->load('DsGiaoViec')
                        ->load('DsNguoiDuocGiaoViec')
                        ->load('DsXinYKien')
                        ->load('NguoiPheDuyet')
                        ->load('DonViPhatHanh')
                        ->load('DsComment')
                        ->load('DsSoVanBan');
                        $lstVanBan->push($group->vanban);
                    }
                }
            }
            $lstVanBan->unique('id');
            return $this->responseJson([
                'status' => 'ok',
                'ds_van_ban' => $lstVanBan
            ]);
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'ds_van_ban' => null
                ]);
        }
    }
    
    //using
    public function getDsVanBanXinYKien(Request $request){
        $isTruongPho = false;
        if (auth()->user() != null && isset(auth()->user()->roles)) {
            foreach(auth()->user()->roles as $role) {
                if ($role->code == 'egov_TRUONG_DON_VI'
                    || $role->code == 'egov_PHO_DON_VI') {
                    $isTruongPho = true;
                }
            }
        }
        $list_trangthai = [VanBanConstants::LAY_Y_KIEN,VanBanConstants::DA_CAP_SO_PHAT_HANH];
        try{
            $lstVanBan = [];
            $lstVanBan_all = VanBanService::getDsVanBanAll(
                VanBanConstants::PHAN_LOAI_VAN_BAN_PHAT_HANH,
                $list_trangthai);
            if ($isTruongPho) {
                $donvi_id = (auth()->user() != null && isset(auth()->user()->donvis) && count(auth()->user()->donvis) > 0) ?
                        auth()->user()->donvis[0]->id : 0;
                
                foreach($lstVanBan_all as $vanban) {
                    $count_cho_y_kien = $vanban->DsXinYKien->filter(function($xinykien) use($donvi_id) {
                        return $xinykien->donvi_id == $donvi_id;
                    })->count() ;
                    if ($vanban->donvi_phathanh_id == $donvi_id || $count_cho_y_kien > 0) {
                        array_push($lstVanBan, $vanban);
                    }
                }
                
            } else {
                foreach($lstVanBan_all as $vanban) {
                    $count_cho_y_kien = $vanban->DsXinYKien->filter(function($xinykien) {
                        return $xinykien->user_id == auth()->user()->id;
                    })->count() ;
                    if ($vanban->created_by == auth()->user()->id || $count_cho_y_kien > 0) {
                        array_push($lstVanBan, $vanban);
                    }
                }
                    
            }
            $user = User::find(auth()->user()->id);
            $user->load('groups');
            foreach ($user->groups as $group) {
                $group->load('vanban');
                if (isset($group->vanban)) {
                    if (in_array($group->vanban->trangthai,$list_trangthai)) {
                        $group->vanban->load('NguoiDangKy')
                        ->load('SoPhatHanh')
                        ->load('LoaiVanBan')
                        ->load('DoKhanVanBan')
                        ->load('DoMatVanBan')
                        ->load('CoQuanPhatHanhVanBan')   //Lay theo nguoi thuoc don vi
                        ->load('attachedFiles')
                        ->load('DsButPhe')
                        ->load('DoKhanVanBan')
                        ->load('DoMatVanBan')
                        ->load('DsGiaoViec')
                        ->load('DsNguoiDuocGiaoViec')
                        ->load('DsXinYKien')
                        ->load('NguoiPheDuyet')
                        ->load('DonViPhatHanh')
                        ->load('DsComment')
                        ->load('DsSoVanBan');
                        array_push($lstVanBan, $group->vanban);
                    }
                }
            }
            return $this->responseJson([
                'status' => 'ok',
                'ds_van_ban' => $lstVanBan
            ]);
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'ds_van_ban' => null
                ]);
        }
    }
    
    //using -> lay ds don vi, ca nhan xin y kien cho van ban
    public function getDsXinYKien(Request $request){
        $isTruongPho = false;
        if (auth()->user() != null && isset(auth()->user()->roles)) {
            foreach(auth()->user()->roles as $role) {
                if ($role->code == 'egov_TRUONG_DON_VI'
                    || $role->code == 'egov_PHO_DON_VI') {
                    $isTruongPho = true;
                }
            }
        }
        $list_trangthai = [VanBanConstants::DU_THAO,
        VanBanConstants::XIN_SO,
        VanBanConstants::DANG_KY,
        VanBanConstants::LAY_Y_KIEN,
        VanBanConstants::KIEM_TRA_THE_THUC,
        VanBanConstants::TU_CHOI_THE_THUC,
        VanBanConstants::TRINH_KY,
        VanBanConstants::TU_CHOI_KY];
        try{
            $vanban = VanBan::where('id', $request->vanban_id)
                ->where('phanloai', VanBanConstants::PHAN_LOAI_VAN_BAN_PHAT_HANH)
                ->wherein('trangthai', $list_trangthai)
                ->first();
            if (!isset($vanban)) {
                return $this->responseJson([
                    'status' => 'ng',
                    'err_code' => 1,
                    'message' => 'Không tìm thấy văn bản',
                    'ds_xin_y_kien' => []
                ]);
            }
            $ds_xin_y_kien = [];
            $vanban->load('DsXinYKien');
            if ($isTruongPho) {
                $donvi_id = (auth()->user() != null && isset(auth()->user()->donvis) && count(auth()->user()->donvis) > 0) ?
                        auth()->user()->donvis[0]->id : 0;
                if ($vanban->donvi_phathanh_id == $donvi_id
                    || $vanban->created_by == auth()->user()->id) {
                        $ds_xin_y_kien = $vanban->DsXinYKien;
                }
                $donViDuocXinYKien = VanBan_XinYKien::where('donvi_id', $donvi_id)
                                                    ->where('vanban_id',$vanban->id)->first();
                if (isset($donViDuocXinYKien)) {
                    $ds_xin_y_kien = $vanban->DsXinYKien;
                }
            } else {
                if ($vanban->created_by == auth()->user()->id) {
                        $ds_xin_y_kien = $vanban->DsXinYKien;
                } else {
                    $userDuocXinYKien = VanBan_XinYKien::where('user_id',auth()->user()->id)
                                        ->where('vanban_id',$vanban->id)->first();
                    if (isset($userDuocXinYKien)) {
                        $ds_xin_y_kien = $vanban->DsXinYKien;
                    }
                }
                    
            }
            foreach($ds_xin_y_kien as $xinykien) {
                $xinykien->load('NguoiTao')
                    ->load('DonViDuocXinYKien')
                    ->load('NguoiDuocXinYKien');
                $ds_comment = VanBan_Comment::where('vanban_id', $vanban->id)
                    ->where('donvi_comment_id', $xinykien->donvi_id)
                    ->where('nguoi_comment_id', $xinykien->user_id)
                    ->where('for_comment_id', $vanban->trangthai)
                    ->get();
                    $xinykien->ds_y_kien = $ds_comment;
            
            }
            return $this->responseJson([
                'status' => 'ok',
                'date_end_plan' => $vanban->ngay_ket_thuc_xin_y_kien_plan,
                'ds_xin_y_kien' => $ds_xin_y_kien
            ]);
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => 2, 
                'err_message' => $e->getMessage(),
                'ds_xin_y_kien' => null
                ]);
        }
    }
    //using
    public function themYKien (Request $request) {
        error_log($request->id);
        error_log($request->ghichu);
        VanBan_XinYKien::where('id', $request->id)
                        ->update(['ghichu'=>$request->ghichu]);
        return $this->responseJson([
            'status' => 'ok',
            ]);
    }
    //using
    public function DeleteYKien (Request $request) {
        error_log($request->id);
        VanBan_XinYKien::where('id', $request->id)
                        ->forceDelete();
        return $this->responseJson([
            'status' => 'ok',
            ]);
    }
    //using
    public function getDsVanBanKiemTraTheThuc(Request $request){
        $isTruongPho = false;
        $isKiemTraThucThe = false;
        $isTrinhKy = false;
        if (auth()->user() != null && isset(auth()->user()->roles)) {
            foreach(auth()->user()->roles as $role) {
                if ($role->code == 'egov_TRUONG_DON_VI'
                    || $role->code == 'egov_PHO_DON_VI') {
                    $isTruongPho = true;
                }
                if ($role->code == 'egov_PHAP_CHE') {
                    $isKiemTraThucThe = true;
                }
                if ($role->code == 'egov_TRINH_KY') {
                    $isTrinhKy = true;
                }
            }
        }
        error_log('-----------'.$isTrinhKy);
        $list_trangthai = [VanBanConstants::KIEM_TRA_THE_THUC,
            VanBanConstants::DONG_Y_THE_THUC,
            VanBanConstants::DA_CAP_SO_PHAT_HANH,
            VanBanConstants::TU_CHOI_THE_THUC,
            VanBanConstants::DA_SUA_TU_CHOI_THE_THUC];
        try{
            $lstVanBan = [];
            if ($isKiemTraThucThe) {
                $lstVanBan = VanBanService::getDsVanBanAll(
                    VanBanConstants::PHAN_LOAI_VAN_BAN_PHAT_HANH,
                    $list_trangthai); 
            } else {
                if ($isTruongPho || $isTrinhKy) {
                    $donvi_id = (auth()->user() != null && isset(auth()->user()->donvis) && count(auth()->user()->donvis) > 0) ?
                        auth()->user()->donvis[0]->id : 0;
                    $lstVanBan = VanBanService::getDsVanBanDonViPhatHanh(
                        VanBanConstants::PHAN_LOAI_VAN_BAN_PHAT_HANH,
                        $list_trangthai, $donvi_id, auth()->user()->id);
                    
                } else {
                    $lstVanBan = VanBanService::getDsVanBanNguoiTao(
                        VanBanConstants::PHAN_LOAI_VAN_BAN_PHAT_HANH,
                        $list_trangthai, auth()->user()->id);
                }
            }
            $user = User::find(auth()->user()->id);
            $user->load('groups');
            foreach ($user->groups as $group) {
                $group->load('vanban');
                if (isset($group->vanban)) {
                    if (in_array($group->vanban->trangthai,$list_trangthai)) {
                        $group->vanban->load('NguoiDangKy')
                        ->load('SoPhatHanh')
                        ->load('LoaiVanBan')
                        ->load('DoKhanVanBan')
                        ->load('DoMatVanBan')
                        ->load('CoQuanPhatHanhVanBan')   //Lay theo nguoi thuoc don vi
                        ->load('attachedFiles')
                        ->load('DsButPhe')
                        ->load('DoKhanVanBan')
                        ->load('DoMatVanBan')
                        ->load('DsGiaoViec')
                        ->load('DsNguoiDuocGiaoViec')
                        ->load('DsXinYKien')
                        ->load('NguoiPheDuyet')
                        ->load('DonViPhatHanh')
                        ->load('DsComment')
                        ->load('DsSoVanBan');
                        array_push($lstVanBan, $group->vanban);
                    }
                }
            }
            return $this->responseJson([
                'status' => 'ok',
                'ds_van_ban' => $lstVanBan
            ]);
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'ds_van_ban' => null
                ]);
        }
    }
    

    //Using
    public function getDsTraCuuVanBanPhatHanh(Request $request){
        try {
            $isTruongPho = false;
            $isKyDuyet = false;
            $isThuThu = false;
            $thoigian_tu = $request->thoigian_tu;
            $thoigian_den = $request->thoigian_den;
            $list_trangthai = [VanBanConstants::TRINH_KY,
                                VanBanConstants::TU_CHOI_KY,
                                VanBanConstants::DA_KY,
                                VanBanConstants::DA_SUA_TU_CHOI_KY,VanBanConstants::VAO_SO_BAN_HANH, 
                                VanBanConstants::DA_KY, 
                                VanBanConstants::DA_CAP_SO_PHAT_HANH, 
                                VanBanConstants::DA_THU_HOI_SO_PHAT_HANH, 
                                VanBanConstants::PHAT_HANH,
                                VanBanConstants::HUY_PHAT_HANH,
                                VanBanConstants::DA_HOAN_THANH_PHAT_HANH,VanBanConstants::PHAT_HANH, VanBanConstants::DA_HOAN_THANH_PHAT_HANH,];
            if (auth()->user() != null && isset(auth()->user()->roles)) {
                foreach(auth()->user()->roles as $role) {
                    if ($role->code == 'egov_TRUONG_DON_VI'
                        || $role->code == 'egov_PHO_DON_VI') {
                        $isTruongPho = true;
                    }
                    if ($role->code == 'egov_KY_DUYET_VAN_BAN') {
                        $isKyDuyet = true;
                    }
                    if ($role->code == 'egov_THU_THU') {
                        $isThuThu = true;
                        $list_trangthai_Valid = [
                            VanBanConstants::VAO_SO_BAN_HANH, 
                                VanBanConstants::DA_KY, 
                                VanBanConstants::DA_CAP_SO_PHAT_HANH, 
                                VanBanConstants::DA_THU_HOI_SO_PHAT_HANH, 
                                VanBanConstants::PHAT_HANH,
                                VanBanConstants::HUY_PHAT_HANH,
                                VanBanConstants::DA_HOAN_THANH_PHAT_HANH,VanBanConstants::PHAT_HANH, VanBanConstants::DA_HOAN_THANH_PHAT_HANH,];
                    }
                }
            }
            $lstVanBan = [];
            $lstVanBan_all = VanBan::where('phanloai', VanBanConstants::PHAN_LOAI_VAN_BAN_PHAT_HANH)
            ->wherein('trangthai', $list_trangthai)
            ->whereBetween('ngayphathanh',[$thoigian_tu,$thoigian_den])
            ->orderBy('ngayden', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->load('NguoiDangKy')
            ->load('SoVanBan')
            ->load('NhomNguoiDung')
            ->load('CoQuanPhatHanhVanBan')
            ->load('SoPhatHanh')
            ->load('LoaiVanBan')
            ->load('DoKhanVanBan')
            ->load('DsHoatDong')
            ->load('DoMatVanBan')
            ->load('DsNoiNhan')
            ->load('NguoiPheDuyet')
            ->load('dsComment')
            ->load('attachedFiles')
            ->load('DsVanBanThayThe')
            ->load('DsButPhe')
            ->load('DsGiaoViec')
            ->load('DsXinYKien')
            ->load('NguoiKiemTraThucThe')
            ->load('NguoiTrinhKy')
            ->load('NguoiPhatHanh')
            ->load('DonViPhatHanh')
            ->load('NguoiXacNhanHoanThanh')
            ->load('NguoiTrinhChuyen')
            ->load('DsSoVanBan');
            if ($isTruongPho) {
                $donvi_id = (auth()->user() != null && isset(auth()->user()->donvis) && count(auth()->user()->donvis) > 0) ?
                    auth()->user()->donvis[0]->id : 0;
            
                foreach($lstVanBan_all as $vanban) {
                    if ($vanban->donvi_phathanh_id == $donvi_id) {
                        array_push($lstVanBan, $vanban);
                    }
                }
            } 
            if ($isKyDuyet) {
                foreach($lstVanBan_all as $vanban) {
                    if (in_array($vanban->trangthai,$list_trangthai)) {
                        array_push($lstVanBan, $vanban);
                    }
                }
            }
            if ($isThuThu) {
                foreach($lstVanBan_all as $vanban) {
                    if (in_array($vanban->trangthai,$list_trangthai_Valid)) {
                        array_push($lstVanBan, $vanban);
                    }
                }
            }
            foreach($lstVanBan_all as $vanban) {
                if ($vanban->created_by == auth()->user()->id) {
                    array_push($lstVanBan, $vanban);
                }
            }
            $user = User::find(auth()->user()->id);
            $user->load('groups');
            foreach ($user->groups as $group) {
                $group->load('vanban');
                if (isset($group->vanban)) {
                    if (in_array($group->vanban->trangthai,$list_trangthai)) {
                        $group->vanban->load('NguoiDangKy')
                        ->load('SoVanBan')
                        ->load('NhomNguoiDung')
                        ->load('CoQuanPhatHanhVanBan')
                        ->load('SoPhatHanh')
                        ->load('LoaiVanBan')
                        ->load('DoKhanVanBan')
                        ->load('DsHoatDong')
                        ->load('DoMatVanBan')
                        ->load('DsNoiNhan')
                        ->load('NguoiPheDuyet')
                        ->load('dsComment')
                        ->load('attachedFiles')
                        ->load('DsVanBanThayThe')
                        ->load('DsButPhe')
                        ->load('DsGiaoViec')
                        ->load('DsXinYKien')
                        ->load('NguoiKiemTraThucThe')
                        ->load('NguoiTrinhKy')
                        ->load('NguoiPhatHanh')
                        ->load('DonViPhatHanh')
                        ->load('NguoiXacNhanHoanThanh')
                        ->load('NguoiTrinhChuyen')
                        ->load('DsSoVanBan');
                        array_push($lstVanBan, $group->vanban);
                    }
                }
            }
            foreach($lstVanBan as $vanban) {
                $vanban->LoaiVanBan->load('NhomVanBan');
                $vanban->DsVanBanThayThe->load('SoPhatHanh');
                $vanban->DsNoiNhan->load('DonViNhanVanBan')->load('NguoiNhanVanBan');
                $vanban->DsXinYKien->load('DonViDuocXinYKien')->load('NguoiDuocXinYKien');
                $vanban->dsButPhe->load('NguoiButPhe')->load('DonViXuLy')->load('NguoiXyLy');
                $vanban->DsGiaoViec->load('NguoiGiaoViec')->load('NguoiXuLy');
                if (isset($vanban->NhomNguoiDung)) {
                    $vanban->NhomNguoiDung->load('users');
                }
            }
            return $this->responseJson([
                'status' => 'ok',
                'ds_van_ban' => $lstVanBan
            ]);
        }catch(Exception $e) {
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'ds_van_ban' => null
                ]);
        }
    }
    //using 
    public function getDsTrinhKy(Request $request){
        $isTruongPho = false;
        $isKyDuyet = false;
        if (auth()->user() != null && isset(auth()->user()->roles)) {
            foreach(auth()->user()->roles as $role) {
                if ($role->code == 'egov_KY_DUYET_VAN_BAN') {
                    $isKyDuyet = true;
                }
                if ($role->code == 'egov_TRUONG_DON_VI'
                    || $role->code == 'egov_PHO_DON_VI') {
                    $isTruongPho = true;
                }
            }
        }
        $list_trangthai = [VanBanConstants::TRINH_KY,
            VanBanConstants::TU_CHOI_KY,
            VanBanConstants::DA_KY,
            VanBanConstants::DA_SUA_TU_CHOI_KY
            ];
        try{
            $lstVanBan = [];
            if ($isKyDuyet) {
                $lstVanBan = VanBanService::getDsVanBanAll(
                    VanBanConstants::PHAN_LOAI_VAN_BAN_PHAT_HANH,
                    $list_trangthai); 
            } else {
                if ($isTruongPho) {
                    $donvi_id = (auth()->user() != null && isset(auth()->user()->donvis) && count(auth()->user()->donvis) > 0) ?
                        auth()->user()->donvis[0]->id : 0;
                    $lstVanBan = VanBanService::getDsVanBanDonViPhatHanh(
                        VanBanConstants::PHAN_LOAI_VAN_BAN_PHAT_HANH,
                        $list_trangthai, $donvi_id, auth()->user()->id); 
                } else {
                    $lstVanBan = VanBanService::getDsVanBanNguoiTao(
                        VanBanConstants::PHAN_LOAI_VAN_BAN_PHAT_HANH,
                        $list_trangthai, auth()->user()->id); 
                }
            }
            $user = User::find(auth()->user()->id);
            $user->load('groups');
            foreach ($user->groups as $group) {
                $group->load('vanban');
                if (isset($group->vanban)) {
                    if (in_array($group->vanban->trangthai,$list_trangthai)) {
                        $group->vanban->load('NguoiDangKy')
                        ->load('SoPhatHanh')
                        ->load('LoaiVanBan')
                        ->load('DoKhanVanBan')
                        ->load('DoMatVanBan')
                        ->load('CoQuanPhatHanhVanBan')   //Lay theo nguoi thuoc don vi
                        ->load('attachedFiles')
                        ->load('DsButPhe')
                        ->load('DoKhanVanBan')
                        ->load('DoMatVanBan')
                        ->load('DsGiaoViec')
                        ->load('DsNguoiDuocGiaoViec')
                        ->load('DsXinYKien')
                        ->load('NguoiPheDuyet')
                        ->load('DonViPhatHanh')
                        ->load('DsComment')
                        ->load('DsSoVanBan');
                        array_push($lstVanBan, $group->vanban);
                    }
                }
            }
            return $this->responseJson([
                'status' => 'ok',
                'ds_van_ban' => $lstVanBan
            ]);
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'ds_van_ban' => null
                ]);
        }
    }
    //using TODO
    public function getDsPhatHanh(Request $request){
    
        $isTruongPho = false;
        $isAllPermision = false;
        if (auth()->user() != null && isset(auth()->user()->roles)) {
            foreach(auth()->user()->roles as $role) {
                if ($role->code == 'egov_THU_THU') {
                    $isAllPermision = true;
                }
                if ($role->code == 'egov_TRUONG_DON_VI'
                    || $role->code == 'egov_PHO_DON_VI') {
                    $isTruongPho = true;
                }
            }
        }
        $list_trangthai = [VanBanConstants::VAO_SO_BAN_HANH, 
	        VanBanConstants::DA_KY, 
	        VanBanConstants::DA_THU_HOI_SO_PHAT_HANH, 
            VanBanConstants::PHAT_HANH,
            VanBanConstants::HUY_PHAT_HANH,
            VanBanConstants::DA_HOAN_THANH_PHAT_HANH
            ];
        try{
            
            if ($isAllPermision) {
                $lstVanBan = VanBanService::getDsVanBanAll(
                    VanBanConstants::PHAN_LOAI_VAN_BAN_PHAT_HANH,
                    $list_trangthai); 
            } else {
                if ($isTruongPho) {
                    $donvi_id = (auth()->user() != null && isset(auth()->user()->donvis) && count(auth()->user()->donvis) > 0) ?
                        auth()->user()->donvis[0]->id : 0;
                        error_log('0-00000000000000000000000000---'.$donvi_id);
                    $lstVanBan = VanBan::where('phanloai', VanBanConstants::PHAN_LOAI_VAN_BAN_PHAT_HANH)
                        ->wherein('trangthai', $list_trangthai)
                        ->where(function ($query) use($donvi_id)  {
                            $query->where('donvi_phathanh_id', $donvi_id)
                                ->orwhere('nguoi_phathanh_id', auth()->user()->id);
                        })
                        ->orderBy('updated_at', 'desc')
                        ->get()
                        ->load('NguoiDangKy')
                        ->load('SoPhatHanh')
                        ->load('LoaiVanBan')
                        ->load('CoQuanPhatHanhVanBan')   //Lay theo nguoi thuoc don vi
                        ->load('attachedFiles')
                        ->load('DsButPhe')
                        ->load('DoKhanVanBan')
                        ->load('DoMatVanBan')
                        ->load('DsGiaoViec')
                        ->load('DsNguoiDuocGiaoViec')
                        ->load('DsXinYKien')
                        ->load('NguoiPheDuyet')
                        ->load('DonViPhatHanh')
                        ->load('DsComment')
                        ->load('DsSoVanBan');
                } else {
                    $lstVanBan = VanBan::where('phanloai', VanBanConstants::PHAN_LOAI_VAN_BAN_PHAT_HANH)
                        ->wherein('trangthai', $list_trangthai)
                        ->where(function ($query)  {
                            $query->where('created_by', auth()->user()->id)
                                ->orwhere('nguoi_phathanh_id', auth()->user()->id);
                        })
                        ->orderBy('updated_at', 'desc')
                        ->get()
                        ->load('NguoiDangKy')
                        ->load('SoPhatHanh')
                        ->load('LoaiVanBan')
                        ->load('CoQuanPhatHanhVanBan')   //Lay theo nguoi thuoc don vi
                        ->load('attachedFiles')
                        ->load('DsButPhe')
                        ->load('DoKhanVanBan')
                        ->load('DoMatVanBan')
                        ->load('DsGiaoViec')
                        ->load('DsNguoiDuocGiaoViec')
                        ->load('DsXinYKien')
                        ->load('NguoiPheDuyet')
                        ->load('DonViPhatHanh')
                        ->load('DsComment')
                        ->load('DsSoVanBan');
                      
                }
            }
            $user = User::find(auth()->user()->id);
            $user->load('groups');
            foreach ($user->groups as $group) {
                $group->load('vanban');
                if (isset($group->vanban)) {
                    if (in_array($group->vanban->trangthai,$list_trangthai)) {
                        $group->vanban->load('NguoiDangKy')
                        ->load('SoPhatHanh')
                        ->load('LoaiVanBan')
                        ->load('DoKhanVanBan')
                        ->load('DoMatVanBan')
                        ->load('CoQuanPhatHanhVanBan')   //Lay theo nguoi thuoc don vi
                        ->load('attachedFiles')
                        ->load('DsButPhe')
                        ->load('DoKhanVanBan')
                        ->load('DoMatVanBan')
                        ->load('DsGiaoViec')
                        ->load('DsNguoiDuocGiaoViec')
                        ->load('DsXinYKien')
                        ->load('NguoiPheDuyet')
                        ->load('DonViPhatHanh')
                        ->load('DsComment')
                        ->load('DsSoVanBan');
                        array_push($lstVanBan, $group->vanban);
                    }
                }
            }
            return $this->responseJson([
                'status' => 'ok',
                'ds_van_ban' => $lstVanBan
            ]);
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'ds_van_ban' => null
                ]);
        }
    }

    //using TODO
    public function getDsThucHienVanBan(Request $request){
    
        $hasAllPermision = false;
        $isTruongPho = false;
        if (auth()->user() != null && isset(auth()->user()->roles)) {
            foreach(auth()->user()->roles as $role) {
                if ($role->code == 'egov_THU_THU') {
                    $hasAllPermision = true;
                }
                if ($role->code == 'egov_TRUONG_DON_VI'
                    || $role->code == 'egov_PHO_DON_VI') {
                    $isTruongPho = true;
                }
            }
        }
        $list_trangthai = [
            VanBanConstants::PHAT_HANH, VanBanConstants::DA_HOAN_THANH_PHAT_HANH,
        ];
        try{
            $lstVanBan = VanBanService::getDsVanBanAll(
                VanBanConstants::PHAN_LOAI_VAN_BAN_PHAT_HANH,
                $list_trangthai);
            if (!$hasAllPermision) {
                if ($isTruongPho) {
                    $donvi_id = (auth()->user() != null && isset(auth()->user()->donvis) && count(auth()->user()->donvis) > 0) ? 
                    auth()->user()->donvis[0]->id : 0;
                    $lstVanBan = $lstVanBan->filter(function ($vanban) use($donvi_id) {
                            return ($vanban->DsNoiNhan->filter(function($noinhan) use($donvi_id) {
                                return $noinhan->donvi_id == $donvi_id;
                            })->count() > 0) || ($vanban->DsGiaoViec->filter(function($giaoviec) use($donvi_id) {
                                return $giaoviec->donvi_xuly_id == $donvi_id;
                            })->count() > 0);
                    });
                } else {
                    $lstVanBan = $lstVanBan->filter(function ($vanban) {
                        $user_id = (auth()->user() != null) ? auth()->user()->id : 0;
                        return ($vanban->DsNoiNhan->filter(function($noinhan) use($user_id) {
                            return $noinhan->user_id == $user_id;
                        })->count() > 0) || ($vanban->DsGiaoViec->filter(function ($giaoviec) use($user_id) {
                            return $giaoviec->nguoi_xuly_id == $user_id;
                        })->count() > 0);
                    });
                }
            }
            $user = User::find(auth()->user()->id);
            $user->load('groups');
            foreach ($user->groups as $group) {
                $group->load('vanban');
                if (isset($group->vanban)) {
                    if (in_array($group->vanban->trangthai,$list_trangthai)) {
                        $group->vanban->load('NguoiDangKy')
                        ->load('SoPhatHanh')
                        ->load('LoaiVanBan')
                        ->load('DoKhanVanBan')
                        ->load('DoMatVanBan')
                        ->load('CoQuanPhatHanhVanBan')   //Lay theo nguoi thuoc don vi
                        ->load('attachedFiles')
                        ->load('DsButPhe')
                        ->load('DoKhanVanBan')
                        ->load('DoMatVanBan')
                        ->load('DsGiaoViec')
                        ->load('DsNguoiDuocGiaoViec')
                        ->load('DsXinYKien')
                        ->load('NguoiPheDuyet')
                        ->load('DonViPhatHanh')
                        ->load('DsComment')
                        ->load('DsSoVanBan');
                        array_push($lstVanBan, $group->vanban);
                    }
                }
            }
            return $this->responseJson([
                'status' => 'ok',
                'ds_van_ban' => $lstVanBan
            ]);
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'ds_van_ban' => null
                ]);
        }
    }

    //using
    //Get Văn bản by ID
    public function getVanBan(Request $request){
        error_log($request->vanban_id);
        try{
            $vanBan = VanBan::find($request->vanban_id);
            if (isset($vanBan)) {
                //check quyen
                
                $hasPermision = false;
                $isThuThu = false;
                $isTruongPho = false;
                $isPhapChe = false;
                if (auth()->user() != null && isset(auth()->user()->roles)) {
                    foreach (auth()->user()->roles as $role) {
                        if ($role->code == 'egov_THU_THU') {
                            $isThuThu = true;
                        }
                        if ($role->code == 'egov_TRUONG_DON_VI'
                        || $role->code == 'egov_PHO_DON_VI') {
                            $isTruongPho = true;
                        }
                        if ($role->code == 'egov_PHAP_CHE') {
                            $isPhapChe = true;
                        }
                    }
                }
                if ($isThuThu) {
                    if ($vanBan->phanloai == VanBanConstants::PHAN_LOAI_VAN_BAN_DEN) {
                        $hasPermision = true;
                    } else {
                        if ($vanBan->trangthai != VanBanConstants::DU_THAO
                            && $vanBan->trangthai != VanBanConstants::LAY_Y_KIEN) {
                            $hasPermision = true;
                        }
                    }
                }
                if (!$hasPermision 
                    && $isPhapChe 
                    && $vanBan->phanloai == VanBanConstants::PHAN_LOAI_VAN_BAN_PHAT_HANH) {
                        if ($vanBan->trangthai == VanBanConstants::KIEM_TRA_THE_THUC
                            || $vanBan->trangthai == VanBanConstants::KIEM_TRA_THE_THUC
                            || $vanBan->trangthai == VanBanConstants::KIEM_TRA_THE_THUC) {
                                $hasPermision = true;
                        }
                    
                }
                $vanBan->load('DsButPhe')
                    ->load('DsXinYKien')
                    ->load('DsNguoiDuocGiaoViec');
                    
                // truong pho
                if (!$hasPermision && $isTruongPho) {
                    if ($vanBan->created_by == auth()->user()->id
                        || $vanBan->updtaed_by == auth()->user()->id
                        || $vanBan->nguoi_phathanh_id = auth()->user()->id
                        ) {
                        $hasPermision = true;
                    }
                    if (!$hasPermision) {
                        $donvi_id = (auth()->user() != null && isset(auth()->user()->donvis) && count(auth()->user()->donvis) > 0) ?
                        auth()->user()->donvis[0]->id : 0;
                        if ($vanBan->phanloai == VanBanConstants::PHAN_LOAI_VAN_BAN_DEN) {
                            foreach ($vanBan->DsButPhe as $butphe) {
                                if ($butphe->donvi_xuly_id == $donvi_id
                                    || $butphe->nguoi_xuly_id == auth()->user()->id) {
                                    $hasPermision = true;
                                }
                            }
                            if (!$hasPermision) {
                                foreach ($vanBan->DsNguoiDuocGiaoViec as $nguoiduocgiaoviec) {
                                    if ($nguoiduocgiaoviec->donvi_xuly_id == $donvi_id
                                        || $nguoiduocgiaoviec->nguoi_xuly_id == auth()->user()->id) {
                                        $hasPermision = true;
                                    }
                                }
                            }
                        } else {
                            if ($vanBan->donvi_phathanh_id == $donvi_id
                                || $vanBan->nguoi_phathanh_id = auth()->user()->id) {
                                $hasPermision = true;
                            }
                            if (!$hasPermision) {
                                foreach ($vanBan->DsXinYKien as $xinykien) {
                                    if ($xinykien->donvi_id == $donvi_id
                                        || $xinykien->user_id == auth()->user()->id) {
                                        $hasPermision = true;
                                    }
                                }
                            }
                        }
                    }
                   
                }
                
                //user
                if (!$hasPermision) {
                    if ($vanBan->created_by == auth()->user()->id
                        || $vanBan->updtaed_by == auth()->user()->id
                        || $vanBan->nguoi_phathanh_id = auth()->user()->id
                        ) {
                        $hasPermision = true;
                    }
                    if (!$hasPermision) {
                        if ($vanBan->phanloai == VanBanConstants::PHAN_LOAI_VAN_BAN_DEN) {
                            foreach ($vanBan->DsButPhe as $butphe) {
                                if ($butphe->nguoi_xuly_id == auth()->user()->id) {
                                    $hasPermision = true;
                                }
                            }
                            if (!$hasPermision) {
                                foreach ($vanBan->DsNguoiDuocGiaoViec as $nguoiduocgiaoviec) {
                                    if ($nguoiduocgiaoviec->nguoi_xuly_id == auth()->user()->id) {
                                        $hasPermision = true;
                                    }
                                }
                            }
                        } else {
                            if ($vanBan->nguoi_phathanh_id = auth()->user()->id) {
                                $hasPermision = true;
                            }
                            if (!$hasPermision) {
                                foreach ($vanBan->DsXinYKien as $xinykien) {
                                    if ($xinykien->user_id == auth()->user()->id) {
                                        $hasPermision = true;
                                    }
                                }
                            }
                        }
                    }
                    
                }
                
                if (!$hasPermision) {
                    return $this->responseJson([
                        'status' => 'ng',
                        'err_code' => 2,
                        'message' => 'Không có quyền',
                        'van_ban' => null
                        ]);
                }
                $vanBan->load('NguoiDangKy')
                    ->load('SoVanBan')
                    ->load('NhomNguoiDung')
                    ->load('CoQuanPhatHanhVanBan')
                    ->load('SoPhatHanh')
                    ->load('LoaiVanBan')
                    ->load('DoKhanVanBan')
                    ->load('DsHoatDong')
                    ->load('DoMatVanBan')
                    ->load('DsNoiNhan')
                    ->load('NguoiPheDuyet')
                    ->load('dsComment')
                    ->load('attachedFiles')
                    ->load('DsVanBanThayThe')
                    ->load('DsButPhe')
                    ->load('DsGiaoViec')
                    ->load('DsXinYKien')
                    ->load('NguoiKiemTraThucThe')
                    ->load('NguoiTrinhKy')
                    ->load('NguoiPhatHanh')
                    ->load('DonViPhatHanh')
                    ->load('NguoiXacNhanHoanThanh')
                    ->load('NguoiTrinhChuyen')
                    ->load('DsSoVanBan');
                    $vanBan->LoaiVanBan->load('NhomVanBan');
                    $vanBan->DsVanBanThayThe->load('SoPhatHanh');
                    $vanBan->DsNoiNhan->load('DonViNhanVanBan')->load('NguoiNhanVanBan');
                    $vanBan->DsXinYKien->load('DonViDuocXinYKien')->load('NguoiDuocXinYKien');
                    $vanBan->dsButPhe->load('NguoiButPhe')->load('DonViXuLy')->load('NguoiXyLy');
                    $vanBan->DsGiaoViec->load('NguoiGiaoViec')->load('NguoiXuLy');
                    if (isset($vanBan->NhomNguoiDung)) {
                        $vanBan->NhomNguoiDung->load('users');
                    }
                }
            return $this->responseJson([
                'status' => 'ok',
                'van_ban' => $vanBan
                ]);
                
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'van_ban_den' => null
                ]);
        }
        
    }

   
    //using
    public function delete(Request $request){
        $van_ban = VanBan::find($request->id);
        
        if (!isset($van_ban)) {
            return $this->responseJson(array(
                'status'=>'ok',
                'message' => 'Văn bản không tồn tại hoặc bị xóa'
            ));
        }
        DB::transaction(function () use ($van_ban) {
            if (($van_ban->phanloai == VanBanConstants::PHAN_LOAI_VAN_BAN_PHAT_HANH
                || $van_ban->phanloai == VanBanConstants::PHAN_LOAI_VAN_BAN_DEN) &&
                ($van_ban->trangthai == VanBanConstants::DU_THAO
                || $van_ban->trangthai == VanBanConstants::DANG_KY)) {
                $van_ban->delete();
            }
        },3);
        
        return $this->responseJson(array(
            'status'=>'ok'
        ));
    }
    //using
    public function TrinhChuyenVanBan(Request $request){
        $vanBan = VanBan::where('id',$request->id)
                        ->where('phanloai',VanBanConstants::PHAN_LOAI_VAN_BAN_DEN)
                        ->first();
        if (!isset($vanBan)) {
            return $this->responseJson(array(
                'status'=>'ng',
                'message' => 'Văn bản không tồn tại hoặc bị xóa'
            ));
        }
        $vanBan->nguoi_trinh_chuyen_id = auth()->user()->id;
        $vanBan->trangthai = VanBanConstants::DANG_KY;
        $vanBan->ngay_trinh_chuyen = Carbon::now();
        $vanBan->save();
        return $this->responseJson(array(
            'status'=>'ok',
            'van_ban' => $vanBan
        ));
    }
    //using
    public function insertOrUpdateButPhe(Request $request){
        error_log($request->vanban_id);
        try {
            $vanban = VanBan::find($request->vanban_id);
            if (!isset($vanban)) {
                return $this->responseJson(array(
                    'status'=>'ng',
                    'message' => 'Văn bản không tồn tại hoặc bị xóa'
                ));
            }
            $phan_loai_xu_ly = isset($request->phan_loai_xu_ly) && $request->phan_loai_xu_ly > 0 ? $request->phan_loai_xu_ly : 0;
            
            $lstHoTroIds = $request->hotro_ids;
            if (isset($lstHoTroIds)) {
                if (!is_array($lstHoTroIds)) {
                    $lstHoTroIds = explode(',', $lstHoTroIds);
                }
            } 
            
            
            if (!isset($request->chiutrachnhiem_id)
                && ($phan_loai_xu_ly == VanBanConstants::PHAN_LOAI_XU_LY_CO_THUC_HIEN
                    || VanBanConstants::PHAN_LOAI_XU_LY_CO_THUC_HIEN_VA_PHUC_DAP)){
                return $this->responseJson(array(
                    'status'=>'ng',
                    'message' => 'Không có danh sách đơn vị, cá nhân được phân công xử lý'
                ));
            }
            
            //Don Vi
            $lstButPhe = array();
            $lstDonvi_hotro_ids = array();
            $ds_donvi_thuchien = array();
            $ds_nguoi_thuchien = array();
            $donvi_main_id = null;
            $user_main_id = null;
            
            if (strstr($request->chiutrachnhiem_id, '_', true) == 'dv') {
                $donvi_main_id = explode('_', $request->chiutrachnhiem_id)[1];
            } else {
                $user_main_id = $request->chiutrachnhiem_id;
            }
            if ($donvi_main_id == null && $user_main_id == null) {
                return $this->responseJson(array(
                    'status'=>'ng',
                    'message' => 'Không có đơn vị hoặc người chịu trách nhiệm chính!'
                ));
            }
            $csdt_id = 1;
            //danh sach ho tro thuc hien
            if (count($lstHoTroIds) > 0) {
                if ($lstHoTroIds[0] == 'ADV') {
                    error_log('All Đơn vị');
                    $lstDonviIds = DonVi::where('ma_donvi', '!=', '0000')
                    ->where('csdt_id', 1)->get();
                    foreach ($lstDonviIds as $donvi) {
                        array_push($ds_donvi_thuchien, $donvi->id);
                    }
                } else {
                    $lstDonvi_tranfer_mas = array_filter($lstHoTroIds, function ($tranfer_id) {
                        return ((count(explode('_', $tranfer_id)) > 1) && explode('_', $tranfer_id)[0] == 'dv');
                    });
                    $ds_donvi_thuchien = array_map(function ($tranfer_id) {
                        if (strstr($tranfer_id, '_', true) == 'dv') {
                            $donvi_Id = explode('_', $tranfer_id)[1];
                        }
                        error_log("explode donvi id: ".$donvi_Id);
                        return $donvi_Id;
                    }, $lstDonvi_tranfer_mas);
                
                    $ds_nguoi_thuchien = array_filter($lstHoTroIds, function ($tranfer_id) {
                        return (strstr($tranfer_id, '_', true) != 'dv');
                    });
                
                    error_log("so lương donvi : ".strval(count($ds_donvi_thuchien)));
                    error_log("so lương user : ".strval(count($ds_nguoi_thuchien)));
                }
            }
            
            if ($donvi_main_id != null) {
                array_push($ds_donvi_thuchien, $donvi_main_id);
            } elseif ($user_main_id != null) {
                array_push($ds_nguoi_thuchien, $user_main_id);
            }
            error_log(count($ds_donvi_thuchien).'------'.$donvi_main_id.'-----'.count($ds_nguoi_thuchien));
            DB::transaction(function () use ($ds_donvi_thuchien,
                $ds_nguoi_thuchien, $vanban, $donvi_main_id, $user_main_id, $request,$phan_loai_xu_ly) {
                //xóa các donvi, nguoi  cu
                $lst_VanBanButPhe = VanBan_ButPhe::where('vanban_id', $vanban->id)
                    ->where('trangthai', VanBanConstants::BUT_PHE_CHUYEN_GIAO)
                    // ->where(function ($query) use ($ds_donvi_thuchien, $ds_nguoi_thuchien) {
                    //     $query->where(function ($query1) use ($ds_donvi_thuchien) {
                    //         $query1->whereNull('nguoi_xuly_id')
                    //             ->wherein('donvi_xuly_id', $ds_donvi_thuchien);
                    //     })
                    //     ->orwhere(function ($query2) use ($ds_nguoi_thuchien) {
                    //         $query2->whereNull('donvi_xuly_id')
                    //             ->wherein('nguoi_xuly_id', $ds_nguoi_thuchien);
                    //     });
                    // })
                    ->get()
                    ->load('DsNguoiDuocGiaoViec');
                    error_log('abcdfef'.count($lst_VanBanButPhe));
                    foreach ($lst_VanBanButPhe as $vb_butphe) {
                        $vb_butphe->DsNguoiDuocGiaoViec()->detach();
                        $vb_butphe->forceDelete();
                    }
                        //xu ly cho don vi
                    foreach ($ds_donvi_thuchien as $donvi_id) {
                        VanBan_ButPhe::updateOrCreate([
                        'vanban_id' => $vanban->id,
                        'donvi_xuly_id' => $donvi_id,
                        'nguoi_xuly_id' => null], [
                        'is_main' => ($donvi_main_id != null && $donvi_main_id == $donvi_id),
                        'execute_date_start_plan' => $request->execute_date_start_plan,
                        'execute_date_end_plan' => $request->execute_date_end_plan,
                    ]);
                }
            
                //xu ly cho ca nhan
                foreach ($ds_nguoi_thuchien as $user_id) {
                    VanBan_ButPhe::updateOrCreate([
                        'vanban_id' => $vanban->id,
                        'donvi_xuly_id' => null,
                        'nguoi_xuly_id' => $user_id], [
                        'is_main' => ($user_main_id != null && $user_main_id == $user_id),
                        'execute_date_start_plan' => $request->execute_date_start_plan,
                        'execute_date_end_plan' => $request->execute_date_end_plan,
                    ]);
                }
                $vanban->trangthai = (isset($request->has_process) && $request->has_process == true) ?
                    VanBanConstants::CHUYEN_XU_LY : VanBanConstants::KHONG_CAN_XU_LY;
                
                $vanban->nguoi_pheduyet_id = auth()->user()->id;
                $vanban->ngay_pheduyet = Carbon::now();
                $vanban->phan_loai_xu_ly = $phan_loai_xu_ly;
                $vanban->save();
            }, 3);
            
            return $this->responseJson([
                'status' => 'ok'
            ]);
        }catch(Exception $e) {
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }
    //using
    public function rejectButPhe(Request $request) {
        error_log($request->vanban_id);
        try{
            $vanban = VanBan::find($request->vanban_id)->load('DsComment');
            if (!isset($vanban)) {
                return $this->responseJson(array(
                    'status'=>'ng',
                    'message' => 'Văn bản không tồn tại hoặc bị xóa'
                ));
            }
            $comment_id = null;
            foreach ($vanban->dsComment as $comment)  {
                if ($comment -> phan_loai_comment == VanBanConstants::TU_CHOI_BUT_PHE) {
                    $comment_id = $comment->id;
                }
            }
            DB::transaction(function () use ($vanban, $request,$comment_id) {
                $vanban->trangthai_truoc = $vanban->trangthai;
                $vanban->trangthai = VanBanConstants::TU_CHOI_BUT_PHE;
                $vanban->nguoi_pheduyet_id = auth()->user()->id;
                $vanban->save();
                
                VanBanService::insertOrUpdateComment($vanban->id, 
                    $comment_id != null ? $comment_id : -1, 
                    VanBanConstants::TU_CHOI_BUT_PHE,
                    $request->noi_dung);
            }, 3);
            return $this->responseJson([
                'status' => 'ok'
            ]);
        }catch(Exception $e) {
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }
    //using
    public function dsButPhe(Request $request){
        $butphes = VanBan_ButPhe::where('vanban_id',$request->vanban_id)
            ->orderBy('is_main','desc')
            ->get()
            ->load('DsNguoiDuocGiaoViec')
            ->load('DonViXuLy');
        return $this->responseJson([
            'status' => 'ok',
            'butphes' => $butphes
        ]);
    }
    //using
    public function XacNhanHoanThanhButPhe (Request $request) {
        $vanban_id = $request->vanban_id;
        $vanBan = VanBan::where('id',$vanban_id)
                        ->where('phanloai',VanBanConstants::PHAN_LOAI_VAN_BAN_DEN)
                        ->first();
        if (!isset($vanBan)) {
            return $this->responseJson(array(
                'status'=>'ng',
                'message' => 'Văn bản không tồn tại hoặc bị xóa'
            ));
        }
        $vanban_butphe = VanBan_ButPhe::where('id',$request->id)
                                ->first();
        if (!isset($vanban_butphe)) {
            return $this->responseJson(array(
                'status'=>'ng',
                'message' => 'Văn bản bút phê giao việc không tồn tại hoặc bị xóa'
            ));
        }
        if ($request->trangthai == VanBanConstants::BUT_PHE_XAC_NHAN) {
            $vanban_butphe->nguoi_xacnhan_id = auth()->user()->id;
            $vanban_butphe->ngay_xacnhan = Carbon::now();
        }
        $vanban_butphe->trangthai = $request->trangthai;
        $vanban_butphe->save();

        $donvi_id = (auth()->user() != null && isset(auth()->user()->donvis) && count(auth()->user()->donvis) > 0) ? 
                    auth()->user()->donvis[0]->id : 0;
        $user_id = auth()->user() != null ? auth()->user()->id : 0;

        $isTruongPho = false;
        if (auth()->user() != null && isset(auth()->user()->roles)) {
            foreach(auth()->user()->roles as $role) {
                if ($role->code == 'egov_TRUONG_DON_VI' || $role->code == 'egov_PHO_DON_VI') {
                    $isTruongPho = true;
                }
            }
        }
        if ($vanban_butphe->is_main) {
            $vanBan->trangthai = VanBanConstants::DA_THUC_HIEN;
            $vanBan->save();
        }
        return $this->responseJson(array(
            'status'=>'ok',
            'vanban_butphe' => $vanban_butphe
        ));
    }
    //using
    public function insertOrUpdateGiaoViec(Request $request){
        $donvi_id = 0;
        if (isset($request->donvi_id)) {
            $donvi_id = $request->donvi_id;
        } else {
            if (count(auth()->user()->donvis) > 0) {
                $donvi_id = auth()->user()->donvis[0]->id;
            }
        }
        error_log($donvi_id);
        if ($donvi_id == 0 ) {
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => 1,
                'message' => 'Không tìm thấy đơn vị của người giao việc.'
            ]);
        }
        $vanban = VanBan::find($request->vanban_id);
        if (!isset($vanban)) {
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => 2,
                'message' => 'Không tìm thấy văn bản.'
            ]);
        }
        $lst_user_ids = isset($request->user_ids) ? $request->user_ids : [];
        $lst_user_ids = is_array($lst_user_ids) ? $lst_user_ids : explode(',', $lst_user_ids);
        if (count($lst_user_ids) == 0) {
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => 3,
                'message' => 'Không có danh sách người được giao việc.'
            ]);
        }
        $butphe = VanBan_ButPhe::where('vanban_id', $vanban->id)
            ->where('donvi_xuly_id', $donvi_id)->first();
        if (!isset($butphe)) {
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => 4,
                'message' => 'Không tìm thấy bút phê giap việc cho đơn vị.'
            ]);
        }
        DB::transaction(function () use ($donvi_id, $vanban, $butphe, $lst_user_ids, $request) {
        foreach ($lst_user_ids as $user_id) {
            error_log('vanban_id:'.$vanban->id);
            error_log('butphe_id:'.$butphe->id);
            error_log('nguoi_xuly_id:'.$user_id);
            VanBan_ButPhe_GiaoViec::updateOrCreate([
                    'butphe_id' => $butphe->id,
                    'vanban_id' => $vanban->id,
                    'nguoi_xuly_id' => $user_id,
                ],
                [                       
                    
                    'donvi_xuly_id' => $donvi_id,
                    'nguoi_giaoviec_id' => auth()->user()->id,
                    'ngay_giaoviec' => Carbon::now(),
                    'execute_date_start_plan' => $request->execute_date_start_plan,
                    'execute_date_end_plan' => $request->execute_date_end_plan,
                    'ghichu' => $request->ghichu
                ]);
            }
            if (($vanban->trangthai == VanBanConstants::BUT_PHE
                || $vanban->trangthai == VanBanConstants::CHUYEN_XU_LY)) {
                    $vanban->trangthai = VanBanConstants::DANG_THUC_HIEN;
                    $vanban->save();
            }
        }, 3);
        return $this->responseJson([
            'status' => 'ok'
        ]);
    }
    //using
    public function deleteGiaoViec(Request $request){
        $vanban = VanBan::find($request->vanban_id);
        if (!isset($vanban)) {
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => 2,
                'message' => 'Không tìm thấy văn bản.'
            ]);
        }
        $giaoviec_butphe = VanBan_ButPhe_GiaoViec::find($request->giaoviec_id);
        if (!isset($giaoviec_butphe)) {
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => 2,
                'message' => 'Không tìm thấy đối tượng được giao việc.'
            ]);
        }
        $giaoviec_butphe->delete();
        return $this->responseJson([
            'status' => 'ok'
        ]);
    }
    //using
    public function insertOrUpdateGiaoViecPhatHanh(Request $request){
        $donvi_id = 0;
        if (isset($request->donvi_id)) {
            $donvi_id = $request->donvi_id;
        } else {
            if (count(auth()->user()->donvis) > 0) {
                $donvi_id = auth()->user()->donvis[0]->id;
            }
        }
        error_log($donvi_id);
        if ($donvi_id == 0 ) {
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => 1,
                'message' => 'Không tìm thấy đơn vị của người giao việc.'
            ]);
        }
        $vanban = VanBan::find($request->vanban_id);
        if (!isset($vanban)) {
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => 2,
                'message' => 'Không tìm thấy văn bản.'
            ]);
        }
        $lst_user_ids = isset($request->user_ids) ? $request->user_ids : [];
        $lst_user_ids = is_array($lst_user_ids) ? $lst_user_ids : explode(',', $lst_user_ids);
        if (count($lst_user_ids) == 0) {
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => 3,
                'message' => 'Không có danh sách người được giao việc.'
            ]);
        }
        DB::transaction(function () use ($donvi_id, $vanban, $lst_user_ids, $request) {
        foreach ($lst_user_ids as $user_id) {
            VanBan_ButPhe_GiaoViec::updateOrCreate([
                    'butphe_id' => 0,
                    'vanban_id' => $vanban->id,
                    'nguoi_xuly_id' => $user_id,
                ],
                [                       
                    'donvi_xuly_id' => $donvi_id,
                    'nguoi_giaoviec_id' => auth()->user()->id,
                    'ngay_giaoviec' => Carbon::now(),
                    'execute_date_start_plan' => $request->execute_date_start_plan,
                    'execute_date_end_plan' => $request->execute_date_end_plan,
                    'ghichu' => $request->ghichu
                ]);
            }
            if (($vanban->trangthai == VanBanConstants::BUT_PHE
                || $vanban->trangthai == VanBanConstants::CHUYEN_XU_LY)) {
                    $vanban->trangthai = VanBanConstants::DANG_THUC_HIEN;
                    $vanban->save();
            }
        }, 3);
        return $this->responseJson([
            'status' => 'ok'
        ]);
    }
    //using
    public function dsNguoiDuocGiaoViec(Request $request){
        $ds_nguoi_duoc_giao_viec = VanBan_ButPhe_GiaoViec::where('vanban_id',$request->vanban_id)
            ->get()
            ->load('VanBanButPhe')
            ->load('fileKetQua')
            ->load('NguoiXuLy')
            ->load('NguoiGiaoViec')
            ->load('DonViXuLy');
            /* 
            ->load('DsNguoiDuocGiaoViec')
            ->load('DsNguoiGiaoViec')
        foreach ($giaoviecs as $giaoviec) {
            $giaoviec->DsNguoiDuocGiaoViec->unique('id');
            $giaoviec->DsNguoiGiaoViec->unique('id');
        } */
        /* foreach ($giaoviecs as $giaoviec) {
            if ($giaoviec->trangthai == VanBanConstants:: || $giaoviec->trangthai == 4) {
                $giaoviec->load('file_ketquas');
            }
        } */
        return $this->responseJson([
            'status' => 'ok',
            'ds_nguoi_duoc_giao_viec' => $ds_nguoi_duoc_giao_viec
        ]);
    }
    //using
    public function XacNhanThucHienGiaoViec (Request $request) {
        $vanban_id = $request->vanban_id;
        $vanBan = VanBan::where('id',$vanban_id)
                        ->where('phanloai',$request->phanloai)
                        ->first();
        if (!isset($vanBan)) {
            return $this->responseJson(array(
                'status'=>'ng',
                'message' => 'Văn bản không tồn tại hoặc bị xóa'
            ));
        }
        $vanban_butphe_giaoviec = VanBan_ButPhe_GiaoViec::where('id',$request->id)
                                ->first();
        if (!isset($vanban_butphe_giaoviec)) {
            return $this->responseJson(array(
                'status'=>'ng',
                'message' => 'Văn bản bút phê giao việc không tồn tại hoặc bị xóa'
            ));
        }
        $vanban_butphe_giaoviec->trangthai = $request->trangthai;
        if ($vanban_butphe_giaoviec->trangthai == VanBanConstants::BUT_PHE_GIAO_VIEC_DA_HOAN_THANH) {
            $vanban_butphe_giaoviec->ngay_xacnhan = Carbon::now();
        }
        $vanban_butphe_giaoviec->save();
        return $this->responseJson(array(
            'status'=>'ok',
            'butphe_giaoviec' => $vanban_butphe_giaoviec
        ));
    }
    //using
    public function XacNhanHoanThanhThucHienVanBan (Request $request){
        $vanBan = VanBan::where('id',$request->id)
                        ->where('phanloai',VanBanConstants::PHAN_LOAI_VAN_BAN_DEN)
                        ->first();
        if (!isset($vanBan)) {
            return $this->responseJson(array(
                'status'=>'ng',
                'message' => 'Văn bản không tồn tại hoặc bị xóa'
            ));
        }
        $vanBan->trangthai = VanBanConstants::HOAN_THANH_THUC_HIEN;
        $vanBan->nguoi_xacnhan_hoanthanh_id = auth()->user()->id;
        $vanBan->ngay_xacnhan_hoanthanh = Carbon::now();
        $vanBan->save();
        return $this->responseJson(array(
            'status'=>'ok',
            'van_ban' => $vanBan
        ));
    }
    //using
    public function XacNhanThucHienPhucDap (Request $request){
        $vanBan = VanBan::where('id',$request->id)
                        ->where('phanloai',VanBanConstants::PHAN_LOAI_VAN_BAN_DEN)
                        ->first();
        if (!isset($vanBan)) {
            return $this->responseJson(array(
                'status'=>'ng',
                'message' => 'Văn bản không tồn tại hoặc bị xóa'
            ));
        }
        $vanBan->trangthai = VanBanConstants::DANG_BAO_CAO_PHAN_HOI;
        $vanBan->save();
        return $this->responseJson(array(
            'status'=>'ok',
            'van_ban' => $vanBan
        ));
    }
    //using
    //Xin ý kiến
    public function insertOrUpdateXinYKien(Request $request){
        
        $vanban = VanBan::where('id', $request->vanban_id)
            ->where('phanloai', VanBanConstants::PHAN_LOAI_VAN_BAN_PHAT_HANH)
            ->first();
        if (!isset($vanban)) {
            return $this->responseJson(array(
                'status'=>'ng',
                'message' => 'Văn bản không tồn tại hoặc bị xóa'
            ));
        }
        
        $ds_batbuoc_ids = $request->ds_batbuoc_ids;
        if (isset($ds_batbuoc_ids)) {
            if (!is_array($ds_batbuoc_ids)) {
                $ds_batbuoc_ids = explode(',', $ds_batbuoc_ids);
            }
        } 
        $ds_khongbatbuoc_ids = $request->ds_khongbatbuoc_ids;
        if (isset($ds_khongbatbuoc_ids)) {
            if (!is_array($ds_khongbatbuoc_ids)) {
                $ds_khongbatbuoc_ids = explode(',', $ds_khongbatbuoc_ids);
            }
        } 
        if (count($ds_batbuoc_ids) == 0 && count($ds_khongbatbuoc_ids) == 0) {
            return $this->responseJson(array(
                'status'=>'ng',
                'message' => 'Không có danh sách đơn vị, cá nhân được xin ý kiến'
            ));
        }
        
        //Don Vi
        $lst_donvi_batbuoc_ids = array();
        $lst_user_batbuoc_ids = array();
        $lst_donvi_khongbatbuoc_ids = array();
        $lst_user_khongbatbuoc_ids = array();
        
        
        $csdt_id = 1;
        //danh sach xin y kien bat buoc
        if (count($ds_batbuoc_ids) > 0) {
            if ($ds_batbuoc_ids[0] == 'ADV') {
                error_log('All Đơn vị');
                $lstDonviIds = DonVi::where('ma_donvi', '!=', '0000')
                    ->where('csdt_id', 1)->get();
                foreach ($lstDonviIds as $donvi) {
                    array_push($lst_donvi_batbuoc_ids, $donvi->id);
                }
            } else {
                $lstDonvi_batbuoc = array_filter($ds_batbuoc_ids, function ($tranfer_id) {
                    return ((count(explode('_', $tranfer_id)) > 1) && explode('_', $tranfer_id)[0] == 'dv');
                });
                $lst_donvi_batbuoc_ids = array_map(function ($tranfer_id) {
                    if (strstr($tranfer_id, '_', true) == 'dv') {
                        $donvi_Id = explode('_', $tranfer_id)[1];
                    }
                    return $donvi_Id;
                }, $lstDonvi_batbuoc);
                
                $lst_user_batbuoc_ids = array_filter($ds_batbuoc_ids, function ($tranfer_id) {
                    return (strstr($tranfer_id, '_', true) != 'dv');
                });
                
                error_log("so lương donvi bat buoc : ".strval(count($lst_donvi_batbuoc_ids)));
                error_log("so lương user bat buoc: ".strval(count($lst_user_batbuoc_ids)));
            }
        }
        
        //danh sach xin y kien khong bat buoc
        if (count($ds_khongbatbuoc_ids) > 0) {
            if ($ds_khongbatbuoc_ids[0] == 'ADV') {
                error_log('All Đơn vị');
                $lstDonviIds = DonVi::where('ma_donvi', '!=', '0000')
                    ->where('csdt_id', 1)->get();
                foreach ($lstDonviIds as $donvi) {
                    array_push($lst_donvi_khongbatbuoc_ids, $donvi->id);
                }
            } else {
                $lstDonvi_khongbatbuoc = array_filter($ds_khongbatbuoc_ids, function ($tranfer_id) {
                    return ((count(explode('_', $tranfer_id)) > 1) && explode('_', $tranfer_id)[0] == 'dv');
                });
                $lst_donvi_khongbatbuoc_ids = array_map(function ($tranfer_id) {
                    if (strstr($tranfer_id, '_', true) == 'dv') {
                        $donvi_Id = explode('_', $tranfer_id)[1];
                    }
                    return $donvi_Id;
                }, $lstDonvi_khongbatbuoc);
                
                $lst_user_khongbatbuoc_ids = array_filter($ds_khongbatbuoc_ids, function ($tranfer_id) {
                    return (strstr($tranfer_id, '_', true) != 'dv');
                });
                
            }
        }
        DB::transaction(function () use ($lst_donvi_batbuoc_ids, $lst_user_batbuoc_ids,
            $lst_donvi_khongbatbuoc_ids, $lst_user_khongbatbuoc_ids,$vanban,$request) {
            //xóa các donvi, nguoi  cu
            // $lst_VanBanXinYKien = VanBan_XinYKien::where('vanban_id', $vanban->id)
            //         ->where(function ($query) use ($lst_donvi_batbuoc_ids, $lst_user_batbuoc_ids,
            //                 $lst_donvi_khongbatbuoc_ids, $lst_user_khongbatbuoc_ids) {
            //             $query->where(function ($query1) use ($lst_donvi_batbuoc_ids, $lst_donvi_khongbatbuoc_ids) {
            //                 $query1->whereNull('user_id')
            //                     ->where(function ($query3)use($lst_donvi_batbuoc_ids, $lst_donvi_khongbatbuoc_ids) {
            //                         $query3->wherein('donvi_id', $lst_donvi_batbuoc_ids)
            //                             ->orwherein('donvi_id', $lst_donvi_khongbatbuoc_ids);
            //                     });
                                
            //             })
            //             ->orwhere(function ($query2) use ($lst_user_batbuoc_ids, $lst_user_khongbatbuoc_ids) {
            //                 $query2->whereNull('donvi_id')
            //                     ->where(function($query4)use ($lst_user_batbuoc_ids, $lst_user_khongbatbuoc_ids){
            //                         $query4->wherein('user_id', $lst_user_batbuoc_ids)
            //                             ->orwherein('user_id', $lst_user_khongbatbuoc_ids);
            //                     });
            //             });
            //         })
            //         ->delete();
            
        //xu ly cho don vi
            foreach ($lst_donvi_batbuoc_ids as $donvi_id) {
                VanBan_XinYKien::updateOrCreate([
                    'vanban_id' => $vanban->id,
                    'donvi_id' => $donvi_id,
                    'user_id' => null],[
                    'is_require' => true,
                    'date_start_plan' => $request->date_start_plan,
                    'date_end_plan' => $request->date_end_plan,
                ]);
            }
            
            //xu ly cho ca nhan
            foreach ($lst_user_batbuoc_ids as $user_id) {
                VanBan_XinYKien::updateOrCreate([
                    'vanban_id' => $vanban->id,
                    'donvi_id' => null,
                    'user_id' => $user_id],[
                    'is_require' => true,
                    'date_start_plan' => $request->date_start_plan,
                    'date_end_plan' => $request->date_end_plan,
                ]);
            }
            //xu ly cho don vi
            foreach ($lst_donvi_khongbatbuoc_ids as $donvi_id) {
                VanBan_XinYKien::updateOrCreate([
                    'vanban_id' => $vanban->id,
                    'donvi_id' => $donvi_id,
                    'user_id' => null],[
                    'is_require' => false,
                    'date_start_plan' => $request->date_start_plan,
                    'date_end_plan' => $request->date_end_plan,
                ]);
            }
        error_log(print_r($lst_user_khongbatbuoc_ids,true));
            
            //xu ly cho ca nhan
            foreach ($lst_user_khongbatbuoc_ids as $user_id) {
                error_log($vanban->id);
                VanBan_XinYKien::updateOrCreate([
                    'vanban_id' => $vanban->id,
                    'donvi_id' => null,
                    'user_id' => $user_id],[
                        'is_require' => false,
                        'date_start_plan' => $request->date_start_plan,
                        'date_end_plan' => $request->date_end_plan,
                ]);
            }
        $vanban->trangthai = VanBanConstants::LAY_Y_KIEN;
            $vanban->ngay_ket_thuc_xin_y_kien_plan = $request->date_end_plan;
        $vanban->save();
    }, 3);
        return $this->responseJson([
            'status' => 'ok'
        ]);      
    }
    
    //using
    public function choYKien(Request $request) {
        $vanban = VanBan::where('id', $request->vanban_id)
            ->where('phanloai', VanBanConstants::PHAN_LOAI_VAN_BAN_PHAT_HANH)
            ->first();
        if (!isset($vanban)) {
            return $this->responseJson(array(
                'status'=>'ng',
                'message' => 'Văn bản không tồn tại hoặc bị xóa'
            ));
        }
        
        $comment = VanBanService::insertOrUpdateComment(
            $vanban->id,
            (isset($request->id) ? $request->id : null),
            VanBanConstants::LAY_Y_KIEN,
            $request->noi_dung
        );
        return $this->responseJson(array(
            'status'=> ($comment != null ? 'ok' : 'ng'),
            'vanban_comment' => $comment
        ));
    }
    //using
    public function traLoiYKien(Request $request) {
        $vanban = VanBan::where('id', $request->vanban_id)
            ->where('phanloai', VanBanConstants::PHAN_LOAI_VAN_BAN_PHAT_HANH)
            ->first();
        if (!isset($vanban)) {
            return $this->responseJson(array(
                'status'=>'ng',
                'message' => 'Văn bản không tồn tại hoặc bị xóa'
            ));
        }
        
        $comment = VanBanService::insertOrUpdateComment(
            $vanban->id,
            (isset($request->id) ? $request->id : null),
            VanBanConstants::LAY_Y_KIEN,
            $request->noi_dung,
            $request->for_comment_id
        );
        return $this->responseJson(array(
            'status'=> ($comment != null ? 'ok' : 'ng'),
            'vanban_comment' => $comment
        ));
    }
    
    //using
    public function yeuCauKiemTraTheThuc(Request $request) {
        try{
            return VanBanService::chuyenTrangThaiPhatHanh(
                $request->vanban_id, 
                (isset($request->id) ? $request->id : null),
                $request->noi_dung,
                VanBanConstants::KIEM_TRA_THE_THUC,
                VanBanConstants::KIEM_TRA_THE_THUC,
                false);
        } catch(Exception $e) {
            return $this->responseJson(array(
                'status'=>'ng',
                'message' => $e->getMessage()
            ));
        }
    }
    
    //using
    public function kiemTraTheThuc(Request $request) {
        try{
            $vanban = VanBan::where('id', $request->vanban_id)
                ->where('phanloai', VanBanConstants::PHAN_LOAI_VAN_BAN_PHAT_HANH)
                ->first();
            if (!isset($vanban)) {
                return $this->responseJson(array(
                    'status'=>'ng',
                    'message' => 'Văn bản không tồn tại hoặc bị xóa'
                ));
            }
            $accepted = isset($request->is_accepted) ? $request->is_accepted : false;
            $trangthai = $accepted ? VanBanConstants::DONG_Y_THE_THUC : VanBanConstants::TU_CHOI_THE_THUC;
            $phanloai = ($accepted ? VanBanConstants::DONG_Y_THE_THUC : VanBanConstants::TU_CHOI_THE_THUC);
        
            if (!($vanban->trangthai = VanBanConstants::KIEM_TRA_THE_THUC
            || $vanban->trangthai = VanBanConstants::TU_CHOI_THE_THUC
            || $vanban->trangthai = VanBanConstants::DONG_Y_THE_THUC
            || $vanban->trangthai = VanBanConstants::DA_SUA_TU_CHOI_THE_THUC)) {
                return $this->responseJson(array(
                    'status'=>'ng',
                    'message' => 'Không hủy bỏ xác nhận pháp quy'
                ));
            }
            
            return VanBanService::chuyenTrangThaiPhatHanh(
                $request->vanban_id, 
                (isset($request->id) ? $request->id : null),
                $request->noi_dung,
                $phanloai,
                $trangthai,
                false);
        } catch(Exception $e) {
            return $this->responseJson(array(
                'status'=>'ng',
                'message' => $e->getMessage()
            ));
        }
    }
    //using
    public function trinhKyPhatHanh(Request $request) {
        try{
            return VanBanService::chuyenTrangThaiPhatHanh(
                $request->vanban_id, 
                (isset($request->id) ? $request->id : null),
                $request->noi_dung,
                VanBanConstants::TRINH_KY,
                VanBanConstants::TRINH_KY,
                false);
        } catch(Exception $e) {
            return $this->responseJson(array(
                'status'=>'ng',
                'message' => $e->getMessage()
            ));
        }
    }
    //using
    public function kyPhatHanh(Request $request) {
        
        error_log('aksjdlaksjdklsajdklsad');
        try{
            return VanBanService::chuyenTrangThaiPhatHanh(
                $request->vanban_id, 
                (isset($request->id) ? $request->id : 0),
                $request->noi_dung,
                VanBanConstants::DA_KY,
                VanBanConstants::DA_KY,
                false);
        } catch(Exception $e) {
            return $this->responseJson(array(
                'status'=>'ng',
                'message' => $e->getMessage()
            ));
        }
    }
    
    //using
    public function tuChoiKyPhatHanh(Request $request) {
        try{
            $vanban = VanBan::where('id', $request->vanban_id)
                ->where('phanloai', VanBanConstants::PHAN_LOAI_VAN_BAN_PHAT_HANH)
                ->first();
            if (!isset($vanban)) {
                return $this->responseJson(array(
                    'status'=>'ng',
                    'message' => 'Văn bản không tồn tại hoặc bị xóa'
                ));
            }
            if (!($vanban->trangthai = VanBanConstants::TRINH_KY
            || $vanban->trangthai = VanBanConstants::TU_CHOI_KY
            || $vanban->trangthai = VanBanConstants::DA_KY
            || $vanban->trangthai = VanBanConstants::DA_SUA_TU_CHOI_KY)) {
                return $this->responseJson(array(
                    'status'=>'ng',
                    'message' => 'Không hủy bỏ được do đã chuyển sang phát hành'
                ));
            }
            return VanBanService::chuyenTrangThaiPhatHanh(
                $request->vanban_id, 
                (isset($request->id) ? $request->id : 0),
                $request->noi_dung,
                VanBanConstants::TU_CHOI_KY,
                VanBanConstants::TU_CHOI_KY,
                true);
        } catch(Exception $e) {
            return $this->responseJson(array(
                'status'=>'ng',
                'message' => $e->getMessage()
            ));
        }
    }
    //using
    public function capSoPhatHanh(Request $request) {
        try{
            $vanban = VanBan::where('id', $request->vanban_id)
                ->where('phanloai', VanBanConstants::PHAN_LOAI_VAN_BAN_PHAT_HANH)
                ->first();
            if (!isset($vanban)) {
                return array(
                    'status'=>'ng',
                    'message' => 'Văn bản không tồn tại hoặc bị xóa'
                );
            }
            DB::transaction(function () use ($vanban, $request) {
                $trangthai = VanBanConstants::DA_CAP_SO_PHAT_HANH;
                //ghi comment
                // ghi vào bảng quan hệ sổ văn bản - văn bản
                //cap nhan sử dụng số-ký hiệu - begin
                //xoa bo trc
                if ($vanban->phanloai == VanBanConstants::PHAN_LOAI_VAN_BAN_PHAT_HANH){
                    SoPhatHanhVanBan::where('vanban_sudung_id', $vanban->id)
                    ->where('id', '!=', $request->sophathanhvanban_id)
                    ->update(
                        ['vanban_sudung_id'=>null,
                            'ngay_sudung' => null,
                            'nguoi_sudung_id' => null
                        ]
                    );
                    $sovanban = SoPhatHanhVanBan::find($request->sophathanhvanban_id);
                        if (!isset($sovanban) 
                        || ($sovanban->vanban_sudung_id != null 
                            && $sovanban->vanban_sudung_id != $vanban->id)) {
                        $err_code = 10;
                        throw new Exception('Số/ký hiệu đã bị sử dụng bởi băn bản khác');
                        }
                        if (!isset($sovanban) || $sovanban->donvi_id != $vanban->donvi_phathanh_id) {
                            $err_code = 11;
                            throw new Exception('Số/ký hiệu đang cung cấp cho đơn vị khác: '.$vanban->donvi_phathanh_id."--".$sovanban->donvi_id);
                        }
                    $sovanban->vanban_sudung_id = $vanban->id;
                    $sovanban->ngay_sudung = Carbon::now();
                    $sovanban->nguoi_sudung_id = auth()->user()->id;
                    $sovanban->save();
                    $vanban->trangthai = $trangthai;
                    $vanban->sophathanhvanban_id = $sovanban->id;
                    $vanban->save();
                }
            }, 3);
            return $this->responseJson(array(
                'status'=>'ok',
            ));
        return $this->responseJson(array(
            'status'=>'ok',
            'vanban' => $vanban
        ));
        
        } catch(Exception $e) {
            return $this->responseJson(array(
                'status'=>'ng',
                'message' => $e->getMessage()
            ));
        }
    }
    //using
    public function vaoSoPhatHanh(Request $request) {
        try{
            $vanban = VanBan::where('id', $request->vanban_id)
                ->where('phanloai', $request->phanloai_vanban)
                ->first()->load('dsComment');
            if (!isset($vanban)) {
                return array(
                    'status'=>'ng',
                    'message' => 'Văn bản không tồn tại hoặc bị xóa'
                );
            }
            $comment_id = null;
            foreach ($vanban->dsComment as $comment)  {
                if ($comment -> phan_loai_comment == VanBanConstants::TU_CHOI_BUT_PHE) {
                    $comment_id = $comment->id;
                }
            }
            DB::transaction(function () use ($vanban, $request,$comment_id) {
                $trangthai = $request->phanloai_vanban == 1 ? VanBanConstants::VAO_SO_BAN_HANH : VanBanConstants::VAO_SO_VAN_BAN_DEN;
                //ghi comment
                $comment = VanBanService::insertOrUpdateComment(
                    $vanban->id,
                    $comment_id,
                    $trangthai,
                    $request->noi_dung
                );
                // ghi vào bảng quan hệ sổ văn bản - văn bản
                $vanban->load('DsSoVanBan');
                $isUpdated = false;
                if (count($vanban->DsSoVanBan) > 0) {
                    
                    foreach($vanban->DsSoVanBan as $sovanban) {
                        if ($sovanban->id == $request->sovanban_id) {
                            // cập nhật số trang và tổng số
                            VanBanService::updateSoVanBan($sovanban->id);
                            $isUpdated = true;
                        } else {
                            $vanban->DsSoVanBan()->detach($sovanban->id);
                            VanBanService::updateSoVanBan($sovanban->id);
                        }
                    }
                }
                if(!$isUpdated) {
                    SoVanBan_VanBan::updateOrCreate(
                        [
                    'vanban_id' => $vanban->id
                    ],
                        ['sovanban_id' => $request->sovanban_id,
                    'vanban_id' => $vanban->id]
                    );
                    // cập nhật số trang và tổng số
                    VanBanService::updateSoVanBan($request->sovanban_id);
                }
                $vanban->trangthai_truoc = $vanban->trangthai;
                $vanban->trangthai = $trangthai;
                $vanban->save();
            }, 3);return $this->responseJson(array(
            'status'=>'ok',
        ));
        return $this->responseJson(array(
            'status'=>'ok',
            'vanban' => $vanban
        ));
        
        } catch(Exception $e) {
            return $this->responseJson(array(
                'status'=>'ng',
                'message' => $e->getMessage()
            ));
        }
    }
    //using
    public function thucHienPhatHanh(Request $request) {
        $vanban = VanBan::where('id', $request->id)
            ->where('phanloai', VanBanConstants::PHAN_LOAI_VAN_BAN_PHAT_HANH)
            ->first();
        if (!isset($vanban)) {
            return $this->responseJson(array(
                'status'=>'ng',
                'message' => 'Văn bản không tồn tại hoặc bị xóa'
            ));
        }
        $donvi_phat_hanh_id = 0;
        error_log(auth()->user()->donvis[0]->id);
        if (isset($request->donvi_id) && $request->donvi_id > 0) {
            $donvi_phat_hanh_id = $request->donvi_id;
        } else if(auth()->user() != null
            && isset(auth()->user()->donvis) 
            && count(auth()->user()->donvis) > 0) {
            $donvi_phat_hanh_id = auth()->user()->donvis[0]->id;
        }
        
        $vanban->trangthai = VanBanConstants::PHAT_HANH;
        $vanban->ngayphathanh = Carbon::now();
        $vanban->donvi_phathanh_id = $donvi_phat_hanh_id;
        $vanban->nguoi_phathanh_id = auth()->user()->id;
        $vanban->minhchung = true;
        $vanban->save();
        return $this->responseJson(array(
            'status'=>'ok',
        ));
    }
    //using
    public function huyThucHienPhatHanh(Request $request) {
        $vanban = VanBan::where('id', $request->id)
            ->where('phanloai', VanBanConstants::PHAN_LOAI_VAN_BAN_PHAT_HANH)
            ->first();
        if (!isset($vanban)) {
            return $this->responseJson(array(
                'status'=>'ng',
                'message' => 'Văn bản không tồn tại hoặc bị xóa'
            ));
        }
        $donvi_phat_hanh_id = 0;
        if (isset($request->donvi_id) && $request->donvi_id > 0) {
            $donvi_phat_hanh_id = $request->donvi_id;
        } else if(auth()->user() != null
            && isset(auth()->user()->donvis) 
            && count(auth()->user()->donvis) > 0) {
            $donvi_phat_hanh = auth()->user()->donvis[0]->id;
        }
        if (!($vanban->trangthai = VanBanConstants::PHAT_HANH
            || $vanban->trangthai = VanBanConstants::VAO_SO_BAN_HANH
            || $vanban->trangthai = VanBanConstants::HUY_PHAT_HANH)) {
            return $this->responseJson(array(
                'status'=>'ng',
                'message' => 'Không hủy bỏ được do đã thực sự phát hành'
            ));
        }
        $vanban->trangthai = VanBanConstants::HUY_PHAT_HANH;
        
        $vanban->ngayphathanh = Carbon::now();
        $vanban->donvi_phathanh_id = $donvi_phat_hanh_id;
        $vanban->nguoi_phathanh_id = auth()->user()->id;
        $vanban->minhchung = true;
        $vanban->save();
        return $this->responseJson(array(
            'status'=>'ok',
        ));
    }
    //using
    public function xacNhanHoanThanhPhatHanh(Request $request) {
        $vanban = VanBan::find($request->id);
        
        if (!isset($vanban)) {
            return $this->responseJson(array(
                'status'=>'ng',
                'message' => 'Văn bản không tồn tại hoặc bị xóa'
            ));
        }
        $vanban->trangthai = $vanban->phanloai == VanBanConstants::PHAN_LOAI_VAN_BAN_PHAT_HANH ? VanBanConstants::DA_HOAN_THANH_PHAT_HANH : VanBanConstants::HOAN_THANH;
        $vanban->nguoi_xacnhan_hoanthanh_id = auth()->user()->id;
        $vanban->ngay_xacnhan_hoanthanh = Carbon::now();
        $vanban->save();
        return $this->responseJson(array(
            'status'=>'ok',
        ));
    }
    //using
    
    
    //xac nhan nhan van ban
    //using
    public function xacNhanNhanVanBanPheDuyet(request $request) {
        $vanban = VanBan::find($request->id);
        if (!isset($vanban)) {
            return $this->responseJson(array(
                'status'=>'ng',
                'message' => 'Văn bản không tồn tại hoặc bị xóa'
            ));
        }
        
        $vanban->nguoi_nhanpheduyet_id = auth()->user()->id;
        $vanban->ngay_nhanpheduyet = Carbon::now();
        $vanban->save();
        return $this->responseJson(array(
            'status'=>'ok',
        ));
    }
    
    public function xacNhanNhanVanBanButPhe(request $request) {
             
        $vanban_butphe = VanBanService::getButPheByDonViOrUser(
            $request->vanban_id, $request->donvi_id);
        
        if (!isset($vanban_butphe)) {
            return $this->responseJson(array(
                'status'=>'ng',
                'message' => 'Bút phê không tồn tại hoặc bị xóa'
            ));
        }
        if ($vanban_butphe->received_at == null 
        || $vanban_butphe->nguoi_nhan_id == null ) {
            $vanban_butphe->nguoi_nhan_id = auth()->user()->id;
            $vanban_butphe->trangthai = VanBanConstants::BUT_PHE_NHAN;
            $vanban_butphe->received_at = Carbon::now();
            $vanban_butphe->save();
        }
        
        
        return $this->responseJson(array(
            'status'=>'ok',
        ));
    }
    public function xacNhanNhanVanBanButPheGiaoViec(request $request) {
        $vanban_butphe_giaoviec = VanBanService::getButPheGiaoViecByDonViOrUser(
            $request->vanban_id, $request->donvi_id);
        if (!isset($vanban_butphe_giaoviec)) {
            return $this->responseJson(array(
                'status'=>'ng',
                'message' => 'Bút phê - giao việc không tồn tại hoặc bị xóa'
            ));
        }
        if ($vanban_butphe_giaoviec->received_at == null 
        || $vanban_butphe_giaoviec->nguoi_nhan_id == null ) {
                $vanban_butphe_giaoviec->nguoi_nhan_id = auth()->user()->id;
                $vanban_butphe_giaoviec->trangthai = VanBanConstants::BUT_PHE_GIAO_VIEC_DA_NHAN;
                $vanban_butphe_giaoviec->received_at = Carbon::now();
                $vanban_butphe_giaoviec->save();
            }
        return $this->responseJson(array(
            'status'=>'ok',
        ));
    }
    //TODO
    public function ThongKeSoVanBanDen(request $request){
        $ngaytu = $request->ngaytu;
        $ngayden = $request->ngayden;
        $coquanphathanhvanban_ids = $request->coquanphathanh_ids;
        $dokhan_ids = $request->dokhan_ids;
        $hoatdongvanban_ids = $request->hoatdongvanban_ids;
        $loaivanban_ids = $request->loaivanban_ids;
        $tuso = $request->tuso;
        $denso = $request->denso;
        $sovanban_ids = $request->sovanban_ids; 
        $ds_van_ban = VanBan::where('phanloai',VanBanConstants::PHAN_LOAI_VAN_BAN_DEN)
                            ->whereBetween('ngayden',[$ngaytu,$ngayden])
                            ->where(function ($query) use($coquanphathanhvanban_ids){
                                if (isset($coquanphathanhvanban_ids)) {
                                    $query->where('coquanphathanhvanban_id',$coquanphathanhvanban_ids);
                                }else {
                                    return true;
                                }
                            })->where(function ($query) use($dokhan_ids){
                                if ($dokhan_ids[0] != 'ALL') {
                                    $query->whereIn('dokhanvanban_id',$dokhan_ids);
                                }else {
                                    return true;
                                }
                            })->where(function ($query) use($loaivanban_ids){
                                if ($loaivanban_ids[0] != 'ALL') {
                                    $query->whereIn('loaivanban_id',$loaivanban_ids);
                                }else {
                                    return true;
                                }
                            })->where(function ($query) use($tuso,$denso){
                                if (isset($tuso) && isset($denso)) {
                                    $query->whereBetween ('sothutu',[$tuso,$denso]);
                                }else if(isset($tuso)){
                                    $query->where('sothutu','>=',$tuso);
                                }else if (isset($denso)) {
                                    $query->where('sothutu','<=',$denso);
                                }else {
                                    return true;
                                }
                            })->get()->load('DsSoVanBan')->load('DsHoatDong')->load('NguoiDangKy')->load('CoQuanPhatHanhVanBan');
        if (isset($sovanban_ids)) {
            $ds_van_ban=$ds_van_ban->filter(function($vanban)use($sovanban_ids){
                return $vanban->DsSoVanBan->filter(function($so_vb)use($sovanban_ids){
                    return in_array($so_vb->id,$sovanban_ids);
                })->count()>0;
            });
        }
        if (isset($hoatdongvanban_ids) && $hoatdongvanban_ids[0] != 'ALL') {
            $ds_van_ban=$ds_van_ban->filter(function($vanban)use($hoatdongvanban_ids){
                return $vanban->DsHoatDong->filter(function($hd_vb)use($hoatdongvanban_ids){
                    return in_array($hd_vb->id,$hoatdongvanban_ids);
                })->count()>0;
            });
        }
        return $this->responseJson(array(
            'status'=>'ok',
            'ds_van_ban' => $ds_van_ban
        ));
    }
    public function ThongKeSoVanBanPhatHanh(request $request){
        $list_trangthai = [     VanBanConstants::DA_KY,
                                VanBanConstants::VAO_SO_BAN_HANH, 
                                VanBanConstants::DA_KY, 
                                VanBanConstants::DA_CAP_SO_PHAT_HANH, 
                                VanBanConstants::PHAT_HANH,
                                VanBanConstants::DA_HOAN_THANH_PHAT_HANH];
        $ngaytu = $request->ngaytu;
        $ngayden = $request->ngayden;
        $dokhan_ids = $request->dokhan_ids;
        $hoatdongvanban_ids = $request->hoatdongvanban_ids;
        $loaivanban_ids = $request->loaivanban_ids;
        $tuso = $request->tuso;
        $denso = $request->denso;
        $sovanban_ids = $request->sovanban_ids; 
        $ds_van_ban = VanBan::where('phanloai',VanBanConstants::PHAN_LOAI_VAN_BAN_PHAT_HANH)
                            ->whereBetween('ngayphathanh',[$ngaytu,$ngayden])
                            ->wherein('trangthai', $list_trangthai)
                            ->where(function ($query) use($dokhan_ids){
                                if ($dokhan_ids[0] != 'ALL') {
                                    $query->whereIn('dokhanvanban_id',$dokhan_ids);
                                }else {
                                    return true;
                                }
                            })->where(function ($query) use($loaivanban_ids){
                                if ($loaivanban_ids[0] != 'ALL') {
                                    $query->whereIn('loaivanban_id',$loaivanban_ids);
                                }else {
                                    return true;
                                }
                            })->where(function ($query) use($tuso,$denso){
                                if (isset($tuso) && isset($denso)) {
                                    $query->whereBetween ('sothutu',[$tuso,$denso]);
                                }else if(isset($tuso)){
                                    $query->where('sothutu','>=',$tuso);
                                }else if (isset($denso)) {
                                    $query->where('sothutu','<=',$denso);
                                }else {
                                    return true;
                                }
                            })
                            ->get()->load('DsSoVanBan')->load('DsHoatDong')->load('NguoiDangKy')->load('SoPhatHanh')->load('NguoiPheDuyet')->load('DsNoiNhan');
        if (isset($sovanban_ids)) {
            $ds_van_ban=$ds_van_ban->filter(function($vanban)use($sovanban_ids){
                return $vanban->DsSoVanBan->filter(function($so_vb)use($sovanban_ids){
                    return in_array($so_vb->id,$sovanban_ids);
                })->count()>0;
            });
        }
        if (isset($hoatdongvanban_ids) && $hoatdongvanban_ids[0] != 'ALL') {
            $ds_van_ban=$ds_van_ban->filter(function($vanban)use($hoatdongvanban_ids){
                return $vanban->DsHoatDong->filter(function($hd_vb)use($hoatdongvanban_ids){
                    return in_array($hd_vb->id,$hoatdongvanban_ids);
                })->count()>0;
            });
        }
        foreach ($ds_van_ban as $vanBan) {
            $vanBan->DsNoiNhan->load('DonViNhanVanBan')->load('NguoiNhanVanBan');
        }
        return $this->responseJson(array(
            'status'=>'ok',
            'ds_van_ban' => $ds_van_ban
        ));
    }
}
