<?php

namespace App\Http\Controllers;

use App\Models\AccessHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Broadcasting\BroadcastController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Tymon\JWTAuth\Facades\JWTAuth;

class SettingController extends Base\ResourceBaseController
{
    public function __construct(){

    }

    public function getLogs(Request $request){
        $logs = AccessHistory::orderBy('created_at','desc')->get()->load('user');
        return $this->responseJson([
            'logs' => $logs,
            'status' => 'ok'
        ]);
    }
}
