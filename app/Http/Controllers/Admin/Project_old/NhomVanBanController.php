<?php

namespace App\Http\Controllers;

use App\Models\NhomVanBan;
use Exception;
use Illuminate\Http\Request;

class NhomVanBanController extends Base\BaseController
{
    public function __construct(){

    }
    
    public function getAlls(Request $request){
        try{
            $lstNhomVanBan = NhomVanBan::orderBy('maso')->get()->load('LoaiVanBan');
            return $this->responseJson([
                'status' => 'ok',
                'ds_nhom_van_ban' => $lstNhomVanBan
            ]);
                
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'ds_nhom_van_ban' => null
                ]);
        }
        
    }
    public function getNhomVanBan(Request $request){
        try{
            $NhomVanBan = NhomVanBan::find($request->NhomVanBan_id);
            return $this->responseJson([
                'status' => 'ok',
                'linh_vuc_van_ban' => $NhomVanBan
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
    public function getDsNhomVanBanByDsId(Request $request){
        try{
            if (isset($request->NhomVanBan_ids) && is_array($request->NhomVanBan_ids)) {
                $dsNhomVanBan = NhomVanBan::wherein($request->NhomVanBan_ids)
                ->orderBy('ten')
                ->get();
            } else {
                $dsNhomVanBan = [];
            }
            return $this->responseJson([
                'status' => 'ok',
                'ds_linh_vuc_van_ban' => $dsNhomVanBan
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
        error_log('insertOrUpdate------------');
        if (isset($request->id)) {
            $nhomVanBan = NhomVanBan::find($request->id);
            $maso = $request->maso;
        } 
        if (!isset($nhomVanBan)) 
        {
            $nhomVanBan = new NhomVanBan();
            $nhomVB = NhomVanBan::orderBy('maso', 'desc')->first();
            if (isset($nhomVB)) {
                $maso = $nhomVB->maso+1;
                if ($maso<10) {
                    $maso = '0'.$maso;
                }
            }else {
                $maso = '01';
            }
        }
        $nhomVanBan->ten = $request->ten;
        $nhomVanBan->tenngan = $request->tenngan;
        $nhomVanBan->maso = $maso;
        $nhomVanBan->mota = $request->mota;
        $nhomVanBan->tenngan = $request->tenngan;
        $nhomVanBan->save();
        return $this->responseJson(array(
            'status'=>'ok',
            'nhom_van_ban' => $nhomVanBan
        ));
    }
    public function delete(Request $request){
        error_log($request->NhomVanBan_id);
        if (isset($request->NhomVanBan_id)) {
            NhomVanBan::find($request->NhomVanBan_id)->delete();
        } 
        return $this->responseJson(array(
            'status'=>'ok'
        ));
    }
    public function deleteByLstIds(Request $request){
        if (isset($request->NhomVanBan_ids) && is_array($request->NhomVanBan_ids)) {
            NhomVanBan::wherein($request->NhomVanBan_ids)->delete();
        } 
        return $this->responseJson(array(
            'status'=>'ok'
        ));
    }
}
