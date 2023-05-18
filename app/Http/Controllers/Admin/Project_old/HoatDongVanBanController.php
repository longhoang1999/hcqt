<?php

namespace App\Http\Controllers;

use App\Models\LinhVucVanBan;
use App\Models\HoatDongVanBan;
use Exception;
use Illuminate\Http\Request;

class HoatDongVanBanController extends Base\BaseController
{
    public function __construct(){

    }
    public function getAlls(Request $request){
        try{
            $lstHoatDongVanBan = HoatDongVanBan::orderBy('linhvuc_id')->get()->load('linhvuc');
            return $this->responseJson([
                'status' => 'ok',
                'ds_hoat_dong_van_ban' => $lstHoatDongVanBan
            ]);
                
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'ds_hoat_dong_van_ban' => null
                ]);
        }
        
    }
    public function getLinhVucVanBan(Request $request){
        try{
            $linhVucVanBan = LinhVucVanBan::find($request->linhvucvanban_id);
            return $this->responseJson([
                'status' => 'ok',
                'linh_vuc_van_ban' => $linhVucVanBan
                ]);
                
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'linh_vuc_van_ban' => null
                ]);
        }
        
    }
    public function getDsLinhVucVanBanByDsId(Request $request){
        try{
            if (isset($request->linhvucvanban_ids) && is_array($request->linhvucvanban_ids)) {
                $dsLinhVucVanBan = LinhVucVanBan::wherein($request->linhvucvanban_ids)
                ->orderBy('ten')
                ->get();
            } else {
                $dsLinhVucVanBan = [];
            }
            return $this->responseJson([
                'status' => 'ok',
                'ds_linh_vuc_van_ban' => $dsLinhVucVanBan
                ]);
                
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'ds_linh_vuc_van_ban' => null
                ]);
        }
        
    }

    public function insertOrUpdate(Request $request){
        if (isset($request->id)) {
            $hoatDongVanBan = HoatDongVanBan::find($request->id);
        }
        if (!isset($hoatDongVanBan)) 
        {
            $hoatDongVanBan = new HoatDongVanBan();
        }
        $linhvuc = LinhVucVanBan::where('id', $request->linhvuc_id)->first();
        $hoatdong = HoatDongVanBan::where('linhvuc_id',$request->linhvuc_id)->orderBy('maso','desc')->first();
        error_log('insertOrUpdate-----------2');
        $leng_linhvuc = strlen($linhvuc->maso);
        if (!isset($hoatdong)) {
            $maso = $linhvuc->maso.'01';
        }else {
            $leng_hoatdong = strlen($hoatdong->maso);
            $maHD = substr($hoatdong->maso,$leng_linhvuc,$leng_hoatdong-$leng_linhvuc);
            error_log($maHD);
            $maHD = $maHD+1;
            if ($maHD < 10) {
                $maHD = '0'.$maHD;
            }
            $maso = $linhvuc->maso.$maHD;
        }
        if (isset($request->id)) {
            $hoatDongVanBan->maso = $request->maso;
        }else {
            $hoatDongVanBan->maso = $maso;
        }
        $hoatDongVanBan->ten = $request->ten;
        $hoatDongVanBan->tenngan = $request->tenngan;
        $hoatDongVanBan->mota = $request->mota;
        $hoatDongVanBan->linhvuc_id = $request->linhvuc_id;
        $hoatDongVanBan->save();
        return $this->responseJson(array(
            'status'=>'ok',
            'hoat_dong_van_ban' => $hoatDongVanBan
        ));
    }
    public function delete(Request $request){
        error_log($request->linhvucvanban_id);
        if (isset($request->linhvucvanban_id)) {
            LinhVucVanBan::find($request->linhvucvanban_id)->delete();
        } 
        return $this->responseJson(array(
            'status'=>'ok'
        ));
    }
    public function deleteByLstIds(Request $request){
        if (isset($request->linhvucvanban_ids) && is_array($request->linhvucvanban_ids)) {
            LinhVucVanBan::wherein($request->linhvucvanban_ids)->delete();
        } 
        return $this->responseJson(array(
            'status'=>'ok'
        ));
    }
}
