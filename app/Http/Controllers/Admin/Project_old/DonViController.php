<?php

namespace App\Http\Controllers;
use App\Models\DonVi;
use App\Models\DonVi_User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DonViController extends Base\BaseController
{
    public function __construct(){

    }
    
    public function getAlls(Request $request){
        try{
            $lstDonVis = DonVi::get()->load('users');
            foreach ($lstDonVis as $donvi) {
                //$donvi->users->load('roles');
                $donvi->users->load('donvis');
            }
            return $this->responseJson([
                'status' => 'ok',
                'ds_don_vi' => $lstDonVis
                ]);
                
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'message' => $e->getMessage(),
                'ds_don_vi' => null
                ]);
        }
        
    }
    public function getDonVi(Request $request){
        try{
            $donvi = DonVi::find($request->donvi_id);
            if (isset($donvi)) {
                $donvi->load('users');
            }
            return $this->responseJson([
                'status' => 'ok',
                'don_vi' => $donvi
                ]);
                
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'don_vi' => null
                ]);
        }
        
    }
    public function getDonViByMaDonVi(Request $request){
        try{
            $donvi = DonVi::where('ma_donvi',$request->ma_donvi)->first();
            if (isset($donvi)) {
                $donvi->load('users');
            }
            return $this->responseJson([
                'status' => 'ok',
                'don_vi' => $donvi
                ]);
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'don_vi' => null
                ]);
        }
       
    }
    public function getUserByDonViId (Request $request){
        error_log('------------------------------------');
        $donvi_id = $request->donvi_id;
        error_log($donvi_id);
        $donvi_users = DonVi_User::where('donvi_id', $donvi_id)->orderby('user_id')->get()->load('user');
        $lstuser = $donvi_users->map(function($donvi_user){
            $user = $donvi_user->user;
            return $user;
        });
        return $this->responseJson([
            'status' => 'success',
            'users' => $lstuser
        ]);
    }
    public function getDsDonViByLstId(Request $request){
        try{
            $donvi_ids = explode(',', $request->donvi_ids);
            $donvis = DonVi::find($donvi_ids);
            return $this->responseJson([
                'status' => 'ok',
                'ds_don_vi' => $donvis
                ]);
                
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'ds_don_vi' => null
                ]);
        }
        
    }
    public function insertOrUpdate(Request $request){
        if (isset($request->donvi_id)) {
            $donvi = DonVi::find($request->donvi_id);
        } 
        if (!isset($donvi)) 
        {
            $donvi = new DonVi();
        }
        $donvi->ten_donvi = $request->ten_donvi;
        $donvi->ten_ngan = $request->ten_ngan;
        $donvi->ma_donvi = $request->ma_donvi;
        $donvi->dia_chi = $request->dia_chi;
        $donvi->mo_ta = $request->mo_ta;
        $donvi->truong_dv = $request->truong_dv;
        $donvi->canbo_dbcl = $request->canbo_dbcl;
        $donvi->nguoi_tao = $request->nguoi_tao;
        $donvi->csdt_id = $request->csdt_id;
        $donvi->trang_thai = $request->trang_thai;

        $donvi->save();
        return $this->responseJson(array(
            'status'=>'ok',
            'don_vi' => $donvi
        ));
    }
    public function delete(Request $request){
        if (isset($request->donvi_id)) {
            DonVi::destroy($request->donvi_id);
        } 
        return $this->responseJson(array(
            'status'=>'ok'
        ));
    }
    public function deleteByLstIds(Request $request){
        if (isset($request->donvi_id_ids) && is_array($request->donvi_id_ids)) {
            DonVi::wherein($request->donvi_id_ids)->delete();
        } 
        return $this->responseJson(array(
            'status'=>'ok'
        ));
    }
    
    public function getDonVibyUser(Request $request){
        $donvi = DonVi_User::where('user_id',auth()->user()->id)->get()
                            ->load('donvi')
                            ->load('user');
        return $this->responseJson([
            'donvi' => $donvi
        ]);
    }
    public function getDonVibyUserId(Request $request){
        $donvi = DonVi_User::where('user_id',auth()->user()->id)->get()
                            ->load('donvi')
                            ->load('user');
                $donvi = $donvi->map(function ($dv){
                    $donvi = $dv->donvi;
                    return $donvi;
                });
        return $this->responseJson([
            'donvi' => $donvi
        ]);
    }
    public function asynUserDonvi(Request $request) {
        error_log('asynUserDonvi: '.$request->ma_nhansu."; ----:".count($request->ds_quyen_them)."; ----:".count($request->ds_quyen_xoa).";----:".$request->department_code);
        return $this->responseJson([
            'status' => 'ok',
            
            ]);
    }
    public function asynDonvi(Request $request) {
        error_log('asynDonvi: '.$request->department_code.";---:".$request->department_name);
        error_log('asynDonvi: '.count($request->ds_quyen_them).";---:".count($request->ds_quyen_xoa));
        return $this->responseJson([
            'status' => 'ok',
            
            ]);
    }
}
