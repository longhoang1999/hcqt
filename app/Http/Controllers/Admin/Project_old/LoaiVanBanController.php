<?php

namespace App\Http\Controllers;

use App\Models\LoaiVanBan;
use App\Models\NhomVanBan;
use Exception;
use Illuminate\Http\Request;

class LoaiVanBanController extends Base\BaseController
{
    public function __construct(){

    }
    
    public function getAlls(Request $request){
        try{
            $lstLoaiVanBan = LoaiVanBan::orderBy('nhomvanban_id')->get()->load('NhomVanBan');
            
            return $this->responseJson([
                'status' => 'ok',
                'ds_loai_van_ban' => $lstLoaiVanBan
            ]);
                
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'ds_loai_van_ban' => null
                ]);
        }
        
    }
    public function getLoaiVanBan(Request $request){
        try{
            $loaiVanBan = LoaiVanBan::find($request->loaivanban_id);
            return $this->responseJson([
                'status' => 'ok',
                'loai_van_ban' => $loaiVanBan
                ]);
                
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'loai_van_ban' => null
                ]);
        }
        
    }
    public function getDsLoaiVanBanByDsId(Request $request){
        try{
            if (isset($request->loaivanban_ids) && is_array($request->loaivanban_ids)) {
                $dsLoaiVanBan = LoaiVanBan::wherein($request->loaivanban_ids)
                ->orderBy('ten')
                ->get();
            } else {
                $dsLoaiVanBan = [];
            }
            return $this->responseJson([
                'status' => 'ok',
                'ds_loai_van_ban' => $dsLoaiVanBan
                ]);
                
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'ds_loai_van_ban' => null
                ]);
        }
        
    }

    public function insertOrUpdate(Request $request){
        if (isset($request->id)) {
            $LoaiVanBan = LoaiVanBan::find($request->id);
        }
        if (!isset($LoaiVanBan)) 
        {
            $LoaiVanBan = new LoaiVanBan();
        }
        $nhomvanban = NhomVanBan::where('id', $request->nhomvanban_id)->first();
        $loaivb = LoaiVanBan::where('nhomvanban_id',$request->nhomvanban_id)->orderBy('maso','desc')->first();
        error_log('insertOrUpdate-----------2');
        $leng_nhomvanban = strlen($nhomvanban->maso);
        if (!isset($loaivb)) {
            $maso = $nhomvanban->maso.'01';
        }else {
            $leng_loaivb = strlen($loaivb->maso);
            $maHD = substr($loaivb->maso,$leng_nhomvanban,$leng_loaivb-$leng_nhomvanban);
            error_log($maHD);
            $maHD = $maHD+1;
            if ($maHD < 10) {
                $maHD = '0'.$maHD;
            }
            $maso = $nhomvanban->maso.$maHD;
        }
        error_log($maso);
        if (isset($request->id)) {
            $LoaiVanBan->maso = $request->maso;
        }else {
            $LoaiVanBan->maso = $maso;
        }
        $LoaiVanBan->ten = $request->ten;
        $LoaiVanBan->tenngan = $request->tenngan;
        $LoaiVanBan->mota = $request->mota;
        $LoaiVanBan->nhomvanban_id = $request->nhomvanban_id;
        $LoaiVanBan->save();
        return $this->responseJson(array(
            'status'=>'ok',
            'loai_van_ban' => $LoaiVanBan
        ));
    }
    public function delete(Request $request){
        if (isset($request->loaivanban_id)) {
            LoaiVanBan::find($request->loaivanban_id)->delete();
        } 
        return $this->responseJson(array(
            'status'=>'ok'
        ));
    }
    public function deleteByLstIds(Request $request){
        if (isset($request->loaivanban_ids) && is_array($request->loaivanban_ids)) {
            LoaiVanBan::wherein($request->loaivanban_ids)->delete();
        } 
        return $this->responseJson(array(
            'status'=>'ok'
        ));
    }
}
