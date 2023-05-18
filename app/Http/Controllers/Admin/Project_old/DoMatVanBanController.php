<?php

namespace App\Http\Controllers;

use App\Models\DoMatVanBan;
use App\Models\MoMatVanBan;
use Exception;
use Illuminate\Http\Request;

class DoMatVanBanController extends Base\BaseController
{
    public function __construct(){

    }
    
    public function getAlls(Request $request){
        try{
            $lstDoMatVanBan = DoMatVanBan::orderBy('ten')->get();
            return $this->responseJson([
                'status' => 'ok',
                'ds_do_mat_van_ban' => $lstDoMatVanBan
            ]);
                
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'ds_do_mat_van_ban' => null
                ]);
        }
        
    }
    public function getDoMatVanBan(Request $request){
        try{
            $doMatVanBan = DoMatVanBan::find($request->domatvanban_id);
            return $this->responseJson([
                'status' => 'ok',
                'do_mat_van_ban' => $doMatVanBan
                ]);
                
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'do_mat_van_ban' => null
                ]);
        }
        
    }
    public function getDsDoMatVanBanByDsId(Request $request){
        try{
            if (isset($request->domatvanban_ids) && is_array($request->domatvanban_ids)) {
                $dsDoMatVanBan = DoMatVanBan::wherein($request->domatvanban_ids)
                ->orderBy('ten')
                ->get();
            } else {
                $dsDoMatVanBan = [];
            }
            return $this->responseJson([
                'status' => 'ok',
                'ds_do_mat_van_ban' => $dsDoMatVanBan
                ]);
                
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'ds_do_mat_van_ban' => null
                ]);
        }
        
    }

    public function insertOrUpdate(Request $request){
        if (isset($request->domatvanban_id)) {
            $doMatVanBan = DoMatVanBan::find($request->domatvanban_id);
        } 
        if (!isset($doMatVanBan)) 
        {
            $doMatVanBan = new DoMatVanBan();
        }
        $doMatVanBan->ten = $request->ten;
        $doMatVanBan->maso = $request->maso;
        $doMatVanBan->mota = $request->mota;
        $doMatVanBan->save();
        return $this->responseJson(array(
            'status'=>'ok',
            'do_mat_van_ban' => $doMatVanBan
        ));
    }
    public function delete(Request $request){
        if (isset($request->domatvanban_id)) {
            DoMatVanBan::destroy($request->domatvanban_id);
        } 
        return $this->responseJson(array(
            'status'=>'ok'
        ));
    }
    public function deleteByLstIds(Request $request){
        if (isset($request->domatvanban_ids) && is_array($request->domatvanban_ids)) {
            DoMatVanBan::wherein($request->domatvanban_ids)->delete();
        } 
        return $this->responseJson(array(
            'status'=>'ok'
        ));
    }
}
