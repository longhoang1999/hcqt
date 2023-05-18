<?php

namespace App\Http\Controllers;

use App\Models\SoPhatHanhVanBan;
use App\Models\LinhVucVanBan;
use Carbon\Carbon;
use Exception;

use Illuminate\Http\Request;

class SoPhatHanhVanBanController extends Base\BaseController
{
    public function __construct(){

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
    
    public function getAlls(Request $request){
        $hasPermission = false;
        if (auth()->user() && auth()->user()->roles) {
            foreach(auth()->user()->roles as $role) {
                if ($role->code == 'egov_THU_THU') {
                    $hasPermission = true;
                }
            }
        }
        if (!$hasPermission) {
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => '1', 
                'err_message' => 'Không có quyền',
                'ds_so_phat_hanh' => []
                ]);
        }
        try{
            $lstSoPhatHanhVanBan = SoPhatHanhVanBan::orderBy('loaivanban_id', 'ASC')
            ->get()
            ->load('loaivanban')
            ->load('csdt');
            
            return   $this->responseJson([
                'status'=>'ok',
                'ds_so_phat_hanh'=>$lstSoPhatHanhVanBan
            ]);
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => '2', 
                'err_message' => $e->getMessage(),
                'ds_so_phat_hanh' => []
                ]);
        }
        
    }
    public function getAllsByDonVi(Request $request) {
        try{
            $lstSoPhatHanhVanBan = SoPhatHanhVanBan::where('donvi_id', $request->donvi_id)
            ->orderBy('loaivanban_id', 'ASC')
            ->get()
            ->load('loaivanban')
            ->load('csdt');
            
            return   $this->responseJson([
                'status'=>'ok',
                'ds_so_phat_hanh'=>$lstSoPhatHanhVanBan
            ]);
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'ds_so_phat_hanh' => []
                ]);
        }
    }
    public function getAllsCurrentYear(Request $request){
        try{
            $currentYear = date("Y");
            $lstSoPhatHanhVanBan = SoPhatHanhVanBan::where('namphathanh',$currentYear) 
            ->get()
            ->load('linhvuc')
            ->load('donvi');
            return   $this->responseJson([
                'status'=>'ok',
                'ds_so_phat_hanh'=>$lstSoPhatHanhVanBan
            ]);
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'ds_so_phat_hanh' => null
                ]);
        }
        
    }

    public function insertOrUpdate(Request $request){
        try {
            if (isset($request->id)) {      
                $soPhatHanh = SoPhatHanhVanBan::find($request->id);
                $soPhatHanh->maso = $request->maso;
                $soPhatHanh->namphathanh = $request->namphathanh;
                $soPhatHanh->loaivanban_id = $request->loaivanban_id;
                $soPhatHanh->csdt_id = $request->csdt_id;
                $soPhatHanh->so_kyhieu = $request->so_kyhieu;
                $soPhatHanh->save();
                return $this->responseJson(array(
                    'sophathanh'=>$soPhatHanh,
                    'status'=>'ok',
                ));
            }

            $lstSoPhatHanhVanBan = array();
            $soPhatHanhMax = SoPhatHanhVanBan::where('namphathanh',$request->namphathanh)
                ->orderByRaw('CONVERT(maso, SIGNED) desc')
                ->first();
            $maso = isset($soPhatHanhMax) ? $soPhatHanhMax->maso + 1 : 1;
            
            error_log('so phat hanh insertOrUpdate: '.$request->donvi_id.";--:".$request->nguoi_xin_id);
            
            for ($i = 0; $i < $request->soluong; $i++) {
            
               $masoInsert = (($maso + $i) < 10 ) ? ('0'.($maso + $i)) : ($maso+$i);
                
                $so_kyhieu = str_replace('xx/',$masoInsert.'/',$request->so_kyhieu);
                
                $lstSoPhatHanhVanBan[] = [
                    'maso' => $maso+$i,
                    'csdt_id' => $request->csdt_id,
                    'so_kyhieu' => $so_kyhieu,
                    'loaivanban_id'=> $request->loaivanban_id,
                    'namphathanh'=> $request->namphathanh,
                    'donvi_id' => $request->donvi_id,
                    'nguoi_xin_id' => $request->nguoi_xin_id,
                    'ngay_xin' =>  (isset($request->nguoi_xin_id) && $request->nguoi_xin_id > 0) ? Carbon::now() : null,
                    'ngay_cap' =>  (isset($request->donvi_id) && $request->donvi_id > 0) ? Carbon::now() : null,
                    'nguoi_cap_id' =>  (isset($request->donvi_id) && $request->donvi_id > 0) ? auth()->user()->id : null,
                ];
            }
            SoPhatHanhVanBan::insert($lstSoPhatHanhVanBan);
            return $this->responseJson(array(
                    'sophathanh'=>$lstSoPhatHanhVanBan,
                    'status'=>'ok',
            ));
        }
            // if (!isset($soPhatHanh)) 
            // {
            //     $soPhatHanh = new SoPhatHanhVanBan();
            //     $soPhatHanhMax = SoPhatHanhVanBan::where('namphathanh','2021')->orderByRaw('CONVERT(maso, SIGNED) desc')->first();
            //     $maso = isset($soPhatHanhMax) ? $soPhatHanhMax->maso + 1 : 1;
            // }
            
            
        catch(Exception $e){
            return $this->responseJson(array(
                'status'=>'ng',
                'err_code' => 'err',
                'message' => $e->getMessage(),
            ));
        }
    }
    public function delete(Request $request){
        error_log($request->linhvucvanban_id);
        if (isset($request->linhvucvanban_id)) {
            SoPhatHanhVanBan::where('id', $request->linhvucvanban_id)->delete();
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
