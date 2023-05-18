<?php

namespace App\Http\Controllers;

use App\Common\Constant\NotificationConstants;
use App\Models\Notification;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class NotificationController extends Base\BaseController
{
    public function __construct(){

    }
    
    public function getAllNotificationByUser(Request $request){
        if (!auth()->user()) {
            return $this->responseJson([
                'status' => 'ng',
                'err_code' => 1,
                'message' => 'Bạn cần đăng nhập vào hệ thống!'
            ]);
        }
        
        $lstNotification = Notification::where('receiver_id', auth()->user()->id)
            ->where('receiver_type', NotificationConstants::RECEIVER_TYPE_USER)
            ->orderBy('created_at', 'DESC')
            ->get();
            
        return $this->responseJson([
            'status' => 'ok',
            'lst_notification' => $lstNotification
        ]);
    }
    
    public function delete(Request $request){
        $notification = Notification::find($request->id);
        if ($notification) {
            $notification->delete();
        }
        return $this->responseJson([
            'status' => 'ok'
        ]);
    }

    public function read(Request $request) {
        $notification = Notification::find($request->id);
        if ($notification) {
            $notification->read_at = Carbon::now();
            $notification->save();
        }
        return $this->responseJson([
            'status' => 'ok'
        ]);
    }
    
   

}
