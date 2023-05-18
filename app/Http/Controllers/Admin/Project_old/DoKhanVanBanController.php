<?php

namespace App\Http\Controllers;

use App\Models\DoKhanVanBan;
use Exception;
use Illuminate\Http\Request;

class DoKhanVanBanController extends Base\BaseController
{
    public function __construct(){

    }
    
    public function getAlls(Request $request){
        try{
            $lstDoKhanVanBan = DoKhanVanBan::orderBy('ten')->get();
            return $this->responseJson([
                'status' => 'ok',
                'ds_do_khan_van_ban' => $lstDoKhanVanBan
            ]);
                
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'ds_do_khan_van_ban' => null
                ]);
        }
        
    }
    public function getDoKhanVanBan(Request $request){
        try{
            $doKhanVanBan = DoKhanVanBan::find($request->dokhanvanban_id);
            return $this->responseJson([
                'status' => 'ok',
                'do_khan_van_ban' => $doKhanVanBan
                ]);
                
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'do_khan_van_ban' => null
                ]);
        }
        
    }
    public function getDsDoKhanVanBanByDsId(Request $request){
        try{
            if (isset($request->dokhanvanban_ids) && is_array($request->dokhanvanban_ids)) {
                $dsDoKhanVanBan = DoKhanVanBan::wherein($request->dokhanvanban_ids)
                ->orderBy('ten')
                ->get();
            } else {
                $dsDoKhanVanBan = [];
            }
            return $this->responseJson([
                'status' => 'ok',
                'ds_do_khan_van_ban' => $dsDoKhanVanBan
                ]);
                
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'ds_do_khan_van_ban' => null
                ]);
        }
        
    }

    public function insertOrUpdate(Request $request){
        if (isset($request->dokhanvanban_id)) {
            $doKhanVanBan = DoKhanVanBan::find($request->dokhanvanban_id);
        } 
        if (!isset($doKhanVanBan)) 
        {
            $doKhanVanBan = new DoKhanVanBan();
        }
        $doKhanVanBan->ten = $request->ten;
        $doKhanVanBan->maso = $request->maso;
        $doKhanVanBan->mota = $request->mota;
        $doKhanVanBan->save();
        return $this->responseJson(array(
            'status'=>'ok',
            'do_khan_van_ban' => $doKhanVanBan
        ));
    }
    public function delete(Request $request){
        if (isset($request->dokhanvanban_id)) {
            DoKhanVanBan::destroy($request->dokhanvanban_id);
        } 
        return $this->responseJson(array(
            'status'=>'ok'
        ));
    }
    public function deleteByLstIds(Request $request){
        if (isset($request->dokhanvanban_ids) && is_array($request->dokhanvanban_ids)) {
            DoKhanVanBan::wherein($request->dokhanvanban_ids)->delete();
        } 
        return $this->responseJson(array(
            'status'=>'ok'
        ));
    }
}
