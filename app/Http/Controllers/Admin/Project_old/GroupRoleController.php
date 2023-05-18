<?php

namespace App\Http\Controllers;

use App\Helpers\GroupRoleHelper;
use App\Helpers\RoleHelper;
use App\Models\GroupRole;
use App\Models\Role;
use Exception;
use Illuminate\Http\Request;

class GroupRoleController extends Base\BaseController
{
    public function __construct(){

    }
    
    public function insertOrUpdate(){

    }

    public function getAll(Request $request){
        try {
            $grouproles = GroupRole::orderBy('code')
            ->orderBy('name')
            ->get()->load('roles');
            return $this->responseJson([
            'status' => 'ok',
            'grouproles'=>$grouproles]);
        }catch(Exception $e) {
            error_log($e->getMessage());
            throw $e;
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => -1, //danh sach to (receivers) bi rong
                'message' => $e->getMessage(),
                'grouproles'=> null
                ]);
        }
    }
    
    public function importGroupRoles(Request $request) {
        try {
            $resule = GroupRoleHelper::importGroupRoles();
            return $this->responseJson($resule);
        }catch(Exception $e) {
            error_log($e->getMessage());
            throw $e;
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => -1,
                'message' => $e->getMessage(),
                'grouproles'=> null
                ]);
        }
    }
    public function addOrUpdateGroupRole(Request $request) {
        try {
            if (!isset($request->name) || empty($request->name)) {
                return $this->responseJson([
                    'status' => 'ng',
                    'err_code' => 10,
                    'message' =>'Chưa khai báo tên nhóm quyền',
                    'grouprole'=> null
                    ]);
            }
            $resule = GroupRoleHelper::registerGroupRole($request->id, $request->code,
                $request->name, $request->description, $request->roles);
            return $this->responseJson($resule);
        }catch(Exception $e) {
            error_log($e->getMessage());
            throw $e;
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => -1, 
                'message' => $e->getMessage(),
                'grouprole'=> null
                ]);
        }
    }
    public function deleteGroupRole(Request $request) {
        error_log('deleteGroupRole 0.1');
        try {
            if (!isset($request->code) ||empty($request->code)) {
                return $this->responseJson([
                    'status' => 'ng',
                    'err_code' => 10,
                    'message' =>'Chưa khai báo Mã quyền',
                    'role'=> null
                    ]);
            }
            $resule = GroupRoleHelper::deleteGroupRole($request->id);
            return $this->responseJson($resule);
        }catch(Exception $e) {
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => -1, 
                'message' => $e->getMessage(),
                'grouprole'=> null
                ]);
        }
    }
}
