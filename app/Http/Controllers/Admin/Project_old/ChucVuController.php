<?php

namespace App\Http\Controllers;

use App\Models\ChucVu;
use Exception;
use Illuminate\Http\Request;

class ChucVuController extends Base\BaseController
{
    public function __construct(){

    }
    
    public function getAlls(Request $request){
        try{
            $lstChucVu = ChucVu::orderBy('ten')->get();
            return $this->responseJson([
                'status' => 'ok',
                'ds_chuc_vu' => $lstChucVu
            ]);
                
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'ds_chuc_vu' => null
                ]);
        }
        
    }
    public function getChucVu(Request $request){
        try{
            $chucVu = ChucVu::find($request->id);
            return $this->responseJson([
                'status' => 'ok',
                'chuc_vu' => $chucVu
                ]);
                
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'chuc_vu' => null
                ]);
        }
        
    }
    public function getDsChucVuByDsId(Request $request){
        try{
            if (isset($request->chucvu_ids) && is_array($request->chucvu_ids)) {
                $dsChucVu = ChucVu::wherein($request->chucvu_ids)
                ->orderBy('ten')
                ->get();
            } else {
                $dsChucVu = [];
            }
            return $this->responseJson([
                'status' => 'ok',
                'ds_chuc_vu' => $dsChucVu
                ]);
                
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'ds_chuc_vu' => null
                ]);
        }
        
    }

    public function insertOrUpdate(Request $request){
        if (isset($request->chucvu_id)) {
            $chucVu = ChucVu::find($request->chucvu_id);
        } 
        if (!isset($chucVu)) 
        {
            $chucVu = new ChucVu();
        }
        $chucVu->ten = $request->ten;
        $chucVu->chucvu_cd = $request->chucvu_cd;
        $chucVu->mota = $request->mota;
        $chucVu->save();
        return $this->responseJson(array(
            'status'=>'ok',
            'chuc_vu' => $chucVu
        ));
    }
    public function delete(Request $request){
        if (isset($request->chucvu_id)) {
            ChucVu::destroy($request->chucvu_id);
        } 
        return $this->responseJson(array(
            'status'=>'ok'
        ));
    }
    public function deleteByLstIds(Request $request){
        if (isset($request->chucvu_ids) && is_array($request->chucvu_ids)) {
            ChucVu::wherein($request->chucvu_ids)->delete();
        } 
        return $this->responseJson(array(
            'status'=>'ok'
        ));
    }
}
