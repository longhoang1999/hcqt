<?php

namespace App\Http\Controllers;

use App\Models\AppUser;
use App\Models\AppUser_Message;
use App\Models\AppUser_Prekey;
use App\Models\AppUser_SignedPreKey;
use App\Models\AppUser_PK;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SignalProtocolController extends Base\BaseController
{
    public function __construct(){

    }
    
    public function registerUserDevice(Request $request){
        try{
            $appUser = null;
            

            DB::transaction(function () use ($request, &$appUser) {
                
                $registrationId = $request->registrationId;
                $deviceId = $request->deviceId;
                $pKey_type = '123';
                $appUser = AppUser::updateOrCreate(
                    ['deviceId' => $deviceId,
                    'userId' => $request->userId],
                    ['registrationId'=> $registrationId,
                    'identityKey' => $request->identityKey,
                    'prKey' => $pKey_type]
                );
                error_log('registerUserDevice ------------- : '.$appUser->id);
                error_log('registerUserDevice ------------- : '.$registrationId);
                //luu bang presign
                if (isset($appUser) && isset($request->signedPreKey)) {
                    
                    AppUser_SignedPreKey::updateOrCreate(
                        ['appuser_id' => $appUser->id],
                        ['keyId' => $request->signedPreKey['keyId'],
                        'publicKey' => $request->signedPreKey['publicKey'],
                        'privKey' => $request->signedPreKey['privKey'],
                        'signature' => $request->signedPreKey['signature']
                        ]
                    );
                }

                if (isset($appUser) && isset($request->preKey)) {
                    AppUser_Prekey::updateOrCreate(
                        ['appuser_id' => $appUser->id],
                        ['keyId' => $request->preKey['keyId'],
                        'publicKey' => $request->preKey['publicKey'],
                        'privKey' => $request->preKey['privKey']
                        ]
                    );
                }
                if (isset($appUser) && isset($request->preKey)) {
                    AppUser_PK::updateOrCreate(
                        ['appuser_id' => $appUser->id],
                        [
                        'pKey' => trim($request->privKey).'_ps-'.$pKey_type.trim($request->preKey['privKey']).'_ps-'.$pKey_type.trim($request->signedPreKey['privKey'])
                        ]
                    );
                }
            }, 3);
            if (isset($appUser)) {
                //$appUser->load('userInf');
                $appUser->load('preKey');
                $appUser->load('signedPreKey');
                //$appUser->load('pKey');
                
                return $this->responseJson([
                    'status' => 'ok',
                    'app_user' => $appUser
                ]);
            }
            return $this->responseJson([
                'status' => 'ng',
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => 'Đăng ký không thành công!',
                'app_user' => null
            ]);
                
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => '9999', 
                'err_message' => $e->getMessage(),
                'app_user' => null
                ]);
        }
        
    }
    public function getUserDevice(Request $request){
        try{
            $deviceId = isset($request->deviceId) ? $request->deviceId: 1;
            $appUser = AppUser::where('deviceId', $deviceId)
                ->where('userId', $request->userId)
                ->first();
            if (isset($appUser)) {
                //$appDevice->load('userInf');
                $appUser->load('preKey');
                $appUser->load('signedPreKey');
                if ($request->andMe) {
                    $appUser->load('pKey');
                }
                return $this->responseJson([
                    'status' => 'ok',
                    'app_user' => $appUser
                ]);
            }
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => '1', 
                'err_message' => 'Không tìm thấy key',
                'app_user' => null
            ]);
                
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => '9999', 
                'err_message' => $e->getMessage(),
                'app_user' => null
            ]);
        }
        
    }
    public function sendMessage(Request $request){
        try{
            $appDevice_Message = null;
            DB::transaction(function () use ($request, &$appDevice_Message) {
                error_log('sendMessage');
                $ciphertextMessage = $request->ciphertextMessage;
                $sender_deviceId = isset($request->sender_deviceId) ? $request->sender_deviceId : 1;
                $receiver_deviceId = isset($request->receiver_deviceId) ? $request->receiver_deviceId : 1;

                $appDevice_Message = AppUser_Message::Create(
                   
                    [
                        'senderId' => $request->senderId,
                        'receiverId' => $request->receiverId,
                        'sender_deviceId' =>$sender_deviceId,
                        'receiver_deviceId' =>$receiver_deviceId,

                        'body' => $ciphertextMessage['body'],
                        'type' => $ciphertextMessage['type'],
                        'registrationId' => $ciphertextMessage['registrationId']
                    ]
                );
                

            }, 3);
            if (isset($appDevice_Message)) {
                
                return $this->responseJson([
                    'status' => 'ok',
                    'app_device_message' => $appDevice_Message
                ]);
            }
            return $this->responseJson([
                'status' => 'ng',
                'status' => 'error',
                'err_code' => '1', 
                'err_message' => 'Đăng ký message không thành công!',
                'app_device' => null
            ]);
                
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => '9999', 
                'err_message' => $e->getMessage(),
                'app_device' => null
                ]);
        }
        
    }
    public function receiveMessage(Request $request){
        try{
            $receiverId = isset($request->receiverId) ? $request->receiverId : 1;

            $appdevice_message = AppUser_Message::where('receiverId', $receiverId)
                ->where('senderId', $request->senderId)
                //->orderBy('created_at', 'desc')
                ->get();

            if (isset($appdevice_message)) {
               
                return $this->responseJson([
                    'status' => 'ok',
                    'app_device_message' => $appdevice_message
                ]);
            }
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => '1', 
                'err_message' => 'Không tìm thấy key',
                'app_device_message' => null
            ]);
                
                
        }catch(Exception $e){
            error_log($e->getMessage());            
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => '9999', 
                'err_message' => $e->getMessage(),
                'app_device_message' => null
            ]);
        }
        
    }

}
