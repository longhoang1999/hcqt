<?php

namespace App\Http\Controllers\Base;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as FwController;
use Illuminate\Support\Facades\Log;

class BaseController extends FwController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function responseJson($data, $status=200, array $headers = [], $options = JSON_UNESCAPED_UNICODE) {
        $headersR = array_merge(['Content-type'=> 'application/json; charset=utf-8'], $headers);
        return response()->json($data, $status, $headersR, $options);
    }
    public function getLoginedUser() {
        $user = User::find(auth()->user()->id);
        if (isset($user)) {
            $user->load('donvis');
            //$user->load('groups');
            $user->load('roles');
            $user->load('grouproles');
        }
        return $user;
    }
    
}
