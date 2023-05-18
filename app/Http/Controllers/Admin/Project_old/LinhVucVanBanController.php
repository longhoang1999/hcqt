<?php

namespace App\Http\Controllers;

use App\Models\LinhVucVanBan;
use Exception;
use Illuminate\Http\Request;

class LinhVucVanBanController extends Base\BaseController
{
    public function __construct(){

    }
    
    public function getAlls(Request $request){
        try{
            $lstLinhVucVanBan = LinhVucVanBan::orderBy('maso')->get()->load('donviphutrach')->load('hoatdongvanban');
            return $this->responseJson([
                'status' => 'ok',
                'ds_linh_vuc_van_ban' => $lstLinhVucVanBan
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
            $linhVucVanBan = LinhVucVanBan::find($request->id);
            $maso = $request->maso;
        } 
        if (!isset($linhVucVanBan)) 
        {
            $linhVucVanBan = new LinhVucVanBan();
            $LinhVuc = LinhVucVanBan::orderBy('maso', 'desc')->first();
            if (isset($LinhVuc)) {
                $maso = $LinhVuc->maso+1;
                if ($maso<10) {
                    $maso = '0'.$maso;
                }
            }else {
                $maso = '01';
            }
        }
        $linhVucVanBan->ten = $request->ten;
        $linhVucVanBan->tenngan = $request->tenngan;
        $linhVucVanBan->maso = $maso;
        $linhVucVanBan->mota = $request->mota;
        $linhVucVanBan->donviphutrach_id = $request->donviphutrach_id;
        $linhVucVanBan->save();
        return $this->responseJson(array(
            'status'=>'ok',
            'linh_vuc_van_ban' => $linhVucVanBan
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
