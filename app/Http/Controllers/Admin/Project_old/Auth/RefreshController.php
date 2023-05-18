<?php


namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Mpdf\Tag\THead;
use Tymon\JWTAuth\JWTAuth;

class RefreshController extends Controller
{

    protected $auth;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
    }

    public function refresh(Request  $request)
    {
        $this->auth->setRequest($request);
        $arr = $this->auth->getToken();
        $arr = $this->auth->refresh();
        $this->auth->setToken($arr);
        return response()->json([
            'success' => true,
            'data' => $request->user(),
            'token' => $arr
        ], 200);    }

}