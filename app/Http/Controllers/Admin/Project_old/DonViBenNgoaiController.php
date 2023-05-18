<?php

namespace App\Http\Controllers;

use App\Models\DonVi;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Http\Request;

class DonViBenNgoaiController extends Base\BaseController
{
    public function __construct(){

    }
    
    public function getAlls(Request $request){
        try{
            $ds_donvibenngoai = DonVi::where('csdt_id', 0)->get()->load('users');
            return $this->responseJson([
                'status' => 'ok',
                'ds_donvibenngoai' => $ds_donvibenngoai
            ]);
                
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => '1', 
                'err_message' => $e->getMessage(),
                'ds_donvibenngoai' => null
                ]);
        }
        
    }
    
    //don vi ben ngoai hieu nhu la member (user) và thuộc DonVi có mã số '0000' và csdt_id = 0
    public function insertOrUpdate(Request $request){
        error_log('------------------');
        if (isset($request->id)) {
            $donViBenNgoai = User::find($request->id);
            if (!isset($donViBenNgoai)) {
                return $this->responseJson(array(
                    'status' => 'ng',
                    'err_code' => '1',
                    'message' => 'Không tìm thấy đơn vị cũ'
                ));
            }
        } else {
            $donViBenNgoai = new User();
            
        }
        //kiem tra trùng
        $lst_trung = User::where('id', '!=', $request->id)
            ->where(function($query) use ($request){
                $query->where('user_name',$request->user_name)
                    ->orwhere('email',$request->email)
                    ->orwhere('first_name',$request->first_name);
            })
            ->get();
    
        if (count($lst_trung) > 0) {
            return $this->responseJson(array(
                'status' => 'ng',
                'err_code' => '2',
                'message' => 'Tên ngắn, email hoặc tên bị trùng',
                'lst_trung' => $lst_trung
            ));
        }
        
        try{
            DB::transaction(function () use (&$donViBenNgoai, $request) {
        
                $donViBenNgoai->csdt_id = 0;
                $donViBenNgoai->user_name = $request->user_name;
                $donViBenNgoai->email = $request->email;
                $donViBenNgoai->password = $request->user_name."123456789_ps";
                $donViBenNgoai->first_name = $request->first_name;
                $donViBenNgoai->address = $request->address;
                $donViBenNgoai->phone = $request->phone;
                $donViBenNgoai->description = $request->description;
                $donViBenNgoai->status = 'active';
                $donViBenNgoai->img_avatar_id = 0;
                $donViBenNgoai->save();
                //ma số 0000 cho đơn vị bên ngoài
                $donvi = DonVi::where('ma_donvi', '0000')
                ->where('csdt_id', 0)->first();
                if (isset($donvi)) {
                    $donViBenNgoai->load('donvis');
                    $lstDonVi = $donViBenNgoai->donvis->filter(function($ex_donvi) use($donvi){
                        return $donvi->id == $ex_donvi->id;
                    });
                    if (count($lstDonVi) <= 0) {
                        $donViBenNgoai->donvis()->attach($donvi->id);
                        $donViBenNgoai->save();
                    }
                    
                }
            }, 3);
            return $this->responseJson(array(
                'status'=>'ok',
                'don_vi_ben_ngoai' => $donViBenNgoai
            ));
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'ng',
                'message' => $e->getMessage()
            ]);
        }
        
    }
    public function delete(Request $request){
        if (!isset($request->id)) {
            return $this->responseJson(array(
                'status' => 'ng',
                'err_code' => '1',
                'message' => 'Id không hợp lệ'
            ));
        } 
        User::where('id', $request->id)->delete();
        return $this->responseJson(array(
            'status'=>'ok'
        ));
    }
    
}
