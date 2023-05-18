<?php

namespace App\Http\Controllers;

use App\Models\Csdt;
use Exception;
use Illuminate\Http\Request;

class CoSoDaoTaoController extends Base\BaseController
{
    public function getAlls(Request $request){
        try{
            $lstCoSoDaoTao = Csdt::orderBy('ten_csdt')->get();
            return $this->responseJson([
                'status' => 'ok',
                'ds_co_so' => $lstCoSoDaoTao
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
}
