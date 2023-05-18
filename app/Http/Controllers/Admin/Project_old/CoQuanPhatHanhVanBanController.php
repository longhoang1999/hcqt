<?php

namespace App\Http\Controllers;

use App\Models\CoQuanPhatHanhVanBan;
use Exception;
use Illuminate\Http\Request;

class CoQuanPhatHanhVanBanController extends Base\BaseController
{
    public function __construct(){

    }
    
    public function getAlls(Request $request){
        try{
            $lstCoQuanPhatHanhVanBan = CoQuanPhatHanhVanBan::orderBy('ten')->get();
            return $this->responseJson([
                'status' => 'ok',
                'ds_co_quan_phat_hanh_van_ban' => $lstCoQuanPhatHanhVanBan
            ]);
                
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'ds_co_quan_phat_hanh_van_ban' => null
                ]);
        }
        
    }
    public function getCoQuanPhatHanhVanBan(Request $request){
        try{
            $coQuanPhatHanhVanBan = CoQuanPhatHanhVanBan::find($request->coquanphathanhvanban_id);
            return $this->responseJson([
                'status' => 'ok',
                'co_quan_phat_hanh_van_ban' => $coQuanPhatHanhVanBan
                ]);
                
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'co_quan_phat_hanh_van_ban' => null
                ]);
        }
        
    }
    public function getDsCoQuanPhatHanhVanBanByDsId(Request $request){
        try{
            if (isset($request->coquanphathanhvanban_ids) && is_array($request->coquanphathanhvanban_ids)) {
                $dsCoQuanPhatHanhVanBan = CoQuanPhatHanhVanBan::wherein($request->coquanphathanhvanban_ids)
                ->orderBy('ten')
                ->get();
            } else {
                $dsCoQuanPhatHanhVanBan = [];
            }
            return $this->responseJson([
                'status' => 'ok',
                'ds_co_quan_phat_hanh_van_ban' => $dsCoQuanPhatHanhVanBan
                ]);
                
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'ds_co_quan_phat_hanh_van_ban' => null
                ]);
        }
        
    }

    public function insertOrUpdate(Request $request){
        $countMaSo = count(CoQuanPhatHanhVanBan::where('maso',$request->maso)->get());
        if ($countMaSo > 0) {
            return $this->responseJson(array(
                'status' => 'ng',
                'err_code' => '1',
                'message' => 'Mã số đã tồn tại'
            ));
        }
        if (isset($request->coquanphathanhvanban_id)) {
            $coQuanPhatHanhVanBan = CoQuanPhatHanhVanBan::find($request->coquanphathanhvanban_id);
        } 
        if (!isset($coQuanPhatHanhVanBan)) 
        {
            $coQuanPhatHanhVanBan = new CoQuanPhatHanhVanBan();
        }
        $coQuanPhatHanhVanBan->ten = $request->ten;
        $coQuanPhatHanhVanBan->maso = $request->maso;
        $coQuanPhatHanhVanBan->mota = $request->mota;
        $coQuanPhatHanhVanBan->save();
        return $this->responseJson(array(
            'status'=>'ok',
            'co_quan_phat_hanh_van_ban' => $coQuanPhatHanhVanBan
        ));
    }
    public function delete(Request $request){
        if (!isset($request->id)) {
            return $this->responseJson(array(
                'status' => 'ng',
                'err_code' => '1',
                'message' => 'Không tìm thấy cơ quan phát hành'
            ));
        } 
            CoQuanPhatHanhVanBan::where('id',$request->id)->delete();
        return $this->responseJson(array(
            'status'=>'ok'
        ));
    }
    public function deleteByLstIds(Request $request){
        if (isset($request->coquanphathanhvanban_ids) && is_array($request->coquanphathanhvanban_ids)) {
            CoQuanPhatHanhVanBan::wherein($request->coquanphathanhvanban_ids)->delete();
        } 
        return $this->responseJson(array(
            'status'=>'ok'
        ));
    }
}
