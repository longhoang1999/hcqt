<?php

namespace App\Http\Controllers;

use App\Models\SoVanBan;
use Exception;
use Illuminate\Http\Request;

class SoVanBanController extends Base\BaseController
{
    public function __construct(){

    }
    
    public function getAlls(Request $request){
        try{
            $lstSoVanBans = SoVanBan::orderBy('created_at', 'DESC')
            ->get()
            ->load('Organization')
            ->load('DsVanBan')
            ->load('creator')
            ;
            
            return $this->responseJson([
                'status' => 'ok',
                'ds_so_van_ban' => $lstSoVanBans
                ]);
                
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'ds_so_van_ban' => null
            ]);
        }
    }
    public function getAllsByLoaiVanBan(Request $request){
        try{
            $lstSoVanBans = SoVanBan::where('FileType', $request->loaivanban)
                ->orderBy('created_at', 'DESC')
                ->get()
                ->load('Organization')
                ->load('creator');
            return $this->responseJson([
                'status' => 'ok',
                'ds_so_van_ban' => $lstSoVanBans
                ]);
                
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'ds_so_van_ban' => null
            ]);
        }
    }
    public function getSoVanBan(Request $request){
        try{
            $soVanBan = SoVanBan::find($request->id);
            if (isset($soVanBan)) {
                $soVanBan->load('Organization')
                ->load('creator')->load('DsVanBan');
                $soVanBan->dsVanBan->load('NguoiDangKy')->load('attachedFiles');
            }
            foreach ($soVanBan->dsVanBan as $vanban) {
                $vanban->load('SoPhatHanh');
            }
            return $this->responseJson([
                'status' => 'ok',
                'so_van_ban' => $soVanBan
                ]);
                
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'so_van_ban' => null
                ]);
        }
        
    }
    public function getDsSoVanBanByDsId(Request $request){
        try{
            if (isset($request->sovanban_ids))
                $ids = is_array($request->ids) ? $request->ids : explode(',', $request->ids);
                if (is_array($request->ids)) {
                $dsSoVanBan = SoVanBan::wherein('id', $ids)->get()
                    ->load('Organization')
                    ->load('creator');
            } else {
                $dsSoVanBan = [];
            }
            return $this->responseJson([
                'status' => 'ok',
                'ds_so_van_ban' => $dsSoVanBan
            ]);
            
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'ds_so_van_ban' => null
            ]);
        }
        
    }

    public function insertOrUpdate(Request $request){
        if (isset($request->id)) {
            $soVanBan = SoVanBan::find($request->id);
        } 
        if (!isset($soVanBan)) 
        {
            $soVanBan = SoVanBan::where('FileCode', $request->FileCode)
                ->first();
            if (isset($soVanBan)) {
                return $this->responseJson(array(
                    'status'=>'ng',
                    'err_code' => 1,
                    'message' => 'Trùng mã số'
                ));
            }
            $soVanBan = new SoVanBan();
        }
        
        $soVanBan->FileCode = $request->FileCode;
        $soVanBan->OrganId = $request->OrganId;
        $soVanBan->FileCatalog = $request->FileCatalog;
        $soVanBan->FileNotation = $request->FileNotation;
        
        $soVanBan->Title = $request->Title;
        $soVanBan->Maintenance = $request->Maintenance;
        $soVanBan->Rights = $request->Rights;
        $soVanBan->ClosedDate = $request->ClosedDate;
        $soVanBan->CloserId = $request->CloserId;
        $soVanBan->Language = $request->Language;
        
        $soVanBan->StartDate = $request->StartDate;
        $soVanBan->EndDate = $request->EndDate;
        $soVanBan->UseStatus = 1;
        $soVanBan->FileType = $request->FileType;
        $soVanBan->Description = $request->Description;
        
        $soVanBan->save();
        
        return $this->responseJson(array(
            'status'=>'ok',
            'so_van_ban' => $soVanBan
        ));
    }
    
    public function delete(Request $request){
        if (isset($request->id)) {
            $sovanban = SoVanBan::find($request->id);
            if (isset($sovanban)) {
                $sovanban->load('DsVanBan');
                if (count($sovanban->DsVanBan)) {
                    return $this->responseJson(array(
                        'status' => 'ng',
                        'err_code' => 1,
                        'message' => 'Sổ đang được sử dụng. Không thể xóa!'
                    ));
                }
                $sovanban->delete();
            }
        } 
        return $this->responseJson(array(
            'status'=>'ok'
        ));
    }
    
}
