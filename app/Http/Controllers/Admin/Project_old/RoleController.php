<?php

namespace App\Http\Controllers;

use App\Helpers\RoleHelper;
use App\Models\Role;
use App\Models\User;
use App\Models\User_Role;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class RoleController extends Base\BaseController
{
    public function __construct(){

    }
    
    public function insertOrUpdate(){

    }

    public function getAll(Request $request){
        try {
            $roles = Role::orderBy('code')->get();
            return $this->responseJson([
            'status' => 'ok',
            'roles'=>$roles]);
        }catch(Exception $e) {
            error_log($e->getMessage());
            throw $e;
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => -1, //danh sach to (receivers) bi rong
                'message' => $e->getMessage(),
                'roles'=> null
                ]);
        }
    }
    
    public function importRoles(Request $request) {
        try {
            $resul = RoleHelper::importRoles();
            return $this->responseJson($resul);
        }catch(Exception $e) {
            error_log($e->getMessage());
            throw $e;
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => -1,
                'message' => $e->getMessage(),
                'roles'=> null
                ]);
        }
    }
    public function addOrUpdateRole(Request $request) {
        try {
            error_log('addOrUpdateRole: '.$request->code."; ----:".$request->name);
            if (!isset($request->code) || empty($request->code)) {
                return $this->responseJson([
                    'status' => 'ng',
                    'err_code' => 10,
                    'message' =>'Chưa khai báo Mã quyền',
                    'role'=> null
                    ]);
            }
            // --------------Không import qua ĐHĐT------------------//

            $role = Role::where('code',$request->code)->first();
            if (isset($role) && !isset($request->id)) {
                return $this->responseJson([
                    'status' => 'ng',
                    'err_code' => 10,
                    'message' =>'Mã quyền đã tồn tại',
                    'role'=> null
                    ]);
            }
            if (!isset($request->id)) {
                $role = new Role();
            }

            $role->outsite_role_id = 0;
            $role->code = $request->code;
            $role->name = $request->name;
            DB::transaction(function () use ($role) {
                $role->save();
                
            });
            return $this->responseJson([
                'status' => 'ok',
                'role'=> $role
            ]);
            // -------------------Import qua ĐHĐT----------------------------------//
            $resul = RoleHelper::registerRole($request->id, $request->code, $request->name, $request->description);
            return $this->responseJson($resul);
        }catch(Exception $e) {
            error_log($e->getMessage());
            throw $e;
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => -1, 
                'message' => $e->getMessage(),
                'role'=> null
                ]);
        }
    }
    public function deleteRole(Request $request) {
        error_log('deleteRole: '.$request->code."; ----:".$request->name);
        try {
            if (!isset($request->code) ||empty($request->code)) {
                return $this->responseJson([
                    'status' => 'ng',
                    'err_code' => 10,
                    'message' =>'Chưa khai báo Mã quyền',
                    'role'=> null
                    ]);
            }
            $resul = RoleHelper::deleteRole($request->code, $request->name);
            return $this->responseJson($resul);
        }catch(Exception $e) {
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => -1, 
                'message' => $e->getMessage(),
                'role'=> null
                ]);
        }
    }
    public function PhanQuyen (Request $request) {
        error_log('PhanQuyen: '.$request->user_id."; ----:".count($request->roles));
        $user=User::find($request->user_id)->load('donvis');
        $donvi_id = isset($user->donvis) ? $user->donvis[0]->id : 0;
        User_Role::where('user_id',$request->user_id)->whereNotIn('role_id',$request->roles)->forceDelete();
        $lstUser_role = array();
        foreach ($request->roles as $role) {
            $user_role = User_Role::where('user_id',$request->user_id)->where('role_id',$role)->get();
            error_log(count($user_role));
            if(isset($user_role) && count($user_role)>0) {
                continue;
            }
            
            $lstUser_role[]= [
                'user_id'=>$user->id,
                'donvi_id'=>$donvi_id,
                'role_id'=>$role
            ];
            error_log(count($lstUser_role));
        }
        User_Role::insert($lstUser_role);
        return $this->responseJson([
            'status' => 'ok',
            'message' => 'Success',
            ]);
    }
    public function asynUserRole(Request $request) {
        error_log('asynUserRole: '.$request->ma_nhansu."; ----:".count($request->ds_quyen_them)."; ----:".count($request->ds_quyen_xoa));
        return $this->responseJson([
            'status' => 'ok',
            
            ]);
    }
}
