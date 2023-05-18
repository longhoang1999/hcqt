<?php

namespace App\Http\Controllers;

use App\Models\AppUser;
use App\Models\AppUser_Prekey;
use App\Models\AppUser_PK;
use App\Models\User;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppUserController extends Base\ResourceBaseController
{
    public function __construct(){

    }

    public function addNew($request){
        try{
            $appUser = $this->insertNewApp($request, true);
            if (isset($appUser)) {
                return $this->responseJson(array(
                    'status'=>'ok'
                ));
            } else {
                return $this->responseJson([
                    'status' => 'error',
                    'err_code' => '0', 
                    'err_message' => "Co loi xay ra"
                    ]);
            }
        
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '0', 
                'err_message' => $e->getMessage()
                ]);
        }
    }
    public function getPublicKeyAppUser(Request $request){
        
        try{            
            $lstAppUser = $this->getPublicKeysAppUser($request);
            if (count($lstAppUser) == 0) {
                $this->insertNewApp($request, false);
                //get lai
                $lstAppUser = $this->getPublicKeysAppUser($request);
            }

            return $this->responseJson([
                'status' => 'ok',
                'appUsers'=>$lstAppUser
            ]);
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '0', 
                'err_message' => $e->getMessage()
                ]);
        }
    }
    public function getAppUsers(Request $request){
        try{
            
            $lstAppUser = $this->getUserApp($request);
            if (count($lstAppUser) == 0) {
                $this->insertNewApp($request, true);
                //get lai
                $lstAppUser = $this->getUserApp($request);
            }

            return $this->responseJson([
                'status' => 'ok',
                'appUsers'=>$lstAppUser
            ]);
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'error',
                'err_code' => '0', 
                'err_message' => $e->getMessage()
                ]);
        }
    }

    private function insertNewApp($request, $isMe) {
        #error_log('insertNewApp: userId:'.strval($request->userId).";userName:".$request->userName.";deviceId:".strval($request->deviceId).";registrationId:".strval($request->registrationId).";");
        error_log('insertNewApp: userId:'.strval($request->userId).";userName:".$request->userName);
        if (!(isset($request->userId) && isset($request->userName) 
            && isset($request->deviceId) && isset($request->registrationId) && isset($request->identityKey)
            && isset($request->p1Key) && isset($request->p2Key))) {
            throw new \Exception('Khong dang ky duoc ung dung 1');
        }
        if ($isMe == true && !($request->userId == auth()->user()->id
        && auth()->user()->user_name == $request->userName)) {
            throw new \Exception('Khong dang ky duoc ung dung 2');
        }
        $user = User::find($request->userId);
        if (!(isset($user) && $user->user_name == $request->userName)) {
            throw new \Exception('Khong dang ky duoc ung dung 3');
        }
        
        $appUser = new AppUser();
        $appUser->registrationId = $request->registrationId;
        $appUser->userId = $request->userId;
        $appUser->deviceId = $request->deviceId;
        $appUser->identityKey = $request->identityKey;
        $appUser->prKey = $request->prKey;
        error_log('insertNewApp 1');
                
        $appuser_prekey = new AppUser_Prekey();
        $appuser_prekey->publicKey = $request->p1Key;
        error_log('insertNewApp 3');
        
        $appuser_pk = new AppUser_PK();
        $appuser_pk->pKey = $request->p2Key;
        error_log('insertNewApp 2');

        DB::transaction(function () use (&$appUser, $appuser_pk, $appuser_prekey) {
            if ($appUser->userId == 0) {
                $appUser->userId = auth()->user()->id;
            }
            $appUser->save();
            error_log("appUser id: ".strval($appUser->id));
            if (!isset($appUser->id)) throw new \Exception('Khong dang ky dc key 1');
            $appuser_pk->appuser_id = $appUser->id;
            $appuser_pk->save();
            if (!isset($appuser_pk->id)) throw new \Exception('Khong dang ky dc key 2');
            $appuser_prekey->appuser_id = $appUser->id;
            $appuser_prekey->save();
            if (!isset($appuser_prekey->id)) throw new \Exception('Khong dang ky dc key 3');
        }, 3);

        return $appUser;
    }
    private function getUserApp($request) {
        try{
            error_log('getUserApp');
            if (!(isset($request->userId) && isset($request->userName))) {
                throw new \Exception('Khong lay duoc thong tin ung dung');
            }
            if (!($request->userId == auth()->user()->id
            && auth()->user()->user_name == $request->userName)) {
                throw new \Exception('Khong lay duoc thong tin ung dung');
            }
            $user = User::find($request->userId);
            if (!(isset($user) && $user->user_name == $request->userName)) {
                throw new \Exception('Khong lay duoc thong tin ung dung');
            }
            $appUsers = AppUser::where('userId', $request->userId)
            ->get()
            ->load('preKey')
            ->load('pKey');
            error_log('getUserApp 1: '.strval(count($appUsers)));

            return $appUsers->filter(function($item) {
                return isset($item->preKey) && isset($item->preKey->publicKey)
                    && isset($item->pKey) && isset($item->pKey->pKey);
            });
        }catch(Exception $e){
            error_log($e->getMessage());
            return null;
        }
    }
    private function getPublicKeysAppUser($request) {
        error_log('getPublicKeyAppUser');
        if (!(isset($request->userId) && isset($request->userName))) {
            throw new \Exception('Khong lay duoc thong tin ung dung');
        }
        $user = User::find($request->userId);
        if (!(isset($user) && $user->user_name == $request->userName)) {
            throw new \Exception('Khong lay duoc thong tin ung dung');
        }
        
        $appUsers = AppUser::where('userId', $request->userId)
        ->get()
        ->load('preKey');

        error_log('getUserApp 1: '.strval(count($appUsers)));

        return $appUsers->filter(function($item) {
            return isset($item->preKey) && isset($item->preKey->publicKey);
        });
    }
    
}
