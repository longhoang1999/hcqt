<?php

namespace App\Http\Controllers;

use App\Common\Constant\MessageConstants;
use App\Common\Constant\NotificationConstants;
use App\Events\ChatEvent;
use App\Events\PrivateMessage;
use App\Models\DonVi_User;
use App\Models\Notification;
use App\Services\LichService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Broadcasting\BroadcastController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Tymon\JWTAuth\Facades\JWTAuth;

class TestController extends Base\ResourceBaseController
{
    public function __construct(){

    }

    public function updateDonviUser(Request $request){
        
        DB::transaction(function () use ($request) {
            $lstDonviUser = DonVi_User::get();
            $lstDonviUser = $lstDonviUser->map(function($donviuser) 
            {
                $donvi_id = $donviuser->user_id;
                $donviuser->user_id = $donviuser->donvi_id;
                $donviuser->donvi_id = $donvi_id;
                $donviuser->update();
                //DonVi_User::find($donviuser->id)->update($donviuser);
                return $donviuser;
            });
            //DonVi_User::get()->update($lstDonviUser->array);
        });
        return $this->responseJson([
            'status' => 'ok'
        ]);
    }
    public function getNotification(Request $request) {
      /// $user = JWTAuth::toUser($request->token);
       //error_log("user name: ".$user->id);
       //broadcast(new PrivateMessage('Welcome Notification message: '));
       broadcast(new PrivateMessage("Message: ".$request->message, 
            MessageConstants::MESSAGE_TYPE_MAIL,
            auth()->user(),
            1111
        ))->toOthers();
       //PrivateMessage::dispatch("Thong bao: ".$request->message, MessageConstants::MESSAGE_TYPE_THONGBAO);
      // broadcast(new ChatEvent(auth()->user(), "chatroom: ".$request->message, MessageConstants::MESSAGE_TYPE_CHAT));//->toOthers();
       //ChatEvent::dispatch(auth()->user(), "chatroom: ".$request->message, MessageConstants::MESSAGE_TYPE_CHAT);
       
       
       $notification = new Notification ;
       $notification->content = "Notification: ".$request->message;
       $notification->receiver_id = 4888;
       $notification->bigcategory = NotificationConstants::BIG_CATEGORY_LICH;
       $notification->save();
       
       $notification = new Notification ;
       $notification->content = "Notification: ".$request->message;
       $notification->receiver_id = 1111;
       $notification->bigcategory = NotificationConstants::BIG_CATEGORY_LICH;
       $notification->save();
       return response()->json(['status' => 'ok']);
    }
    
    public function sendNotification(Request $request) {
        $user = JWTAuth::toUser($request->token);
        error_log("user name: ".$user->id);
        event(new PrivateMessage('Welcome Notification message', 1, $user, $user->id));
 
        return response()->json(['status' => 'ok']);
     }
     
     public function importLichFromHu(Request $request) {
        $timeStart = Carbon::Now();
        $timeEnd =  Carbon::now()->addDays(30);
        error_log('Import lich tu he thong khac ');
        error_log('Start time: '.$timeStart);
        error_log('End time: '.$timeEnd);
        $result = LichService::importAllLichFromOtherSystem($timeStart, $timeEnd);
        return response()->json($result);
     }
     public function checkingLichTimeLine(Request $request) {
        
        LichService::CheckingTimeLine();
        return response()->json(['status' => 'ok']);
     }
}
