<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Base\BaseController;
use App\Models\Csdt;
use App\Models\DonVi;
use App\Models\DonVi_User;
use App\Models\Role;
use App\Models\User;
use App\Services\LoginHistoryService;
use App\Utils\JwtAuthUtils;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
//use JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;




class AuthController extends BaseController
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'logout', 'refresh', 'apiLogin', 'vertifyToken']]);
    }

    public function login(Request $request)
    {
        error_log("Login: start");
        //check if login from outsite system
        if (isset($request->outsite_token) && !empty($request->outsite_token)) {
            $request->token = $request->outsite_token;
            return $this->apiLogin($request);
        }
        error_log("Login internal");
        $account_name = $request->accountname;
        $password = $request->password;
        /*
        //$credentials = $request->only('username', 'password');
        $credentials = [
            'username' => $account_name,
            'password' => $password,
        ];
        error_log('Login -----account_name--------: '.strval($account_name)."---pss: ".strval($password));
        $request->merge(['username' => $account_name]);
        $credentials = $request->only('email', 'password');
        
        if (!$token = JWTAuth::attempt($credentials)) {
            error_log('Login -----user name--------: fail');
            $credentials = [
                'email' => $account_name,
                'password' => $password,
            ];
        }
        if (!$token = JWTAuth::attempt($credentials)) {
            */
       
        //$login = $request->input('login');
        // $field = filter_var($account_name, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $field = 'email';
        $request->merge([$field => $account_name]);
        $credentials = $request->only($field, 'password');

      

        try {
            // attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                LoginHistoryService::loginHistory($account_name);
                return response([
                    'status' => 'error',
                    'error' => 'invalid.credentials',
                    'message' => 'Invalid Credentials.'
                ], 400);
            }
            
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            LoginHistoryService::loginHistory($account_name);

            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        //   }
    
        /*  if (!$token = JWTAuth::attempt($credentials)) {
            $credentials = [
                'mobile' => $account_name,
                'password' => $password,
            ];
        }
         */
        /* if (isset($request->email))
            $credentials = $request->only('email', 'password');
        else if (isset($request->mobile)) {
            //$credentials = $request->only('mobile', 'password');
        } */

        /* if (!$token = JWTAuth::attempt($credentials)) {
            
           // error_log('AuthController: login ------------ invalid: ');
            return response([
                'status' => 'ng',
                'error' => 'invalid.credentials',
                'message' => 'Invalid Credentials.'
            ], 400);
        } */
        //error_log('AuthController: login: '.$token);
        LoginHistoryService::loginHistory($account_name);
        return response([
            'status' => 'success',
            'token' => $token
        ]); //->header('Authorization', $token);
    }

    //login to other server via api
    public function apiLogin(Request $request)
    {
        error_log("User đăng nhập từ Đại học điện tử: ".$request->token);
        //Log::info("info","User đăng nhập từ Đại học điện tử: ".$request->has('token'));
       // return redirect()->intended('http://hcdt.idtech.edu.vn/auth/sign-in');
        if (!($request->has('token') || $request->has('outsite_token'))) { //} || !$request->has('csdt_id')) {
            return abort(404);
        }
        //error_log("User đăng nhập từ Đại học điện tử: 1");
        $csdt = Csdt::find($request->csdt_id || 1);
        if (!$csdt) {
            return abort(404);
        }
       // error_log("User đăng nhập từ Đại học điện tử: 2");
       // if ($request->id_csdt == 1) {
            
            $client = new Client([
                'base_uri' => \Config::get('env.outsite_base_url'), 
            ]);
            $response = $client->request('POST', '/post-id-v3', [
                'form_params' => [
                    'access_token' => $request->token,
                    'application_code' => 'egov',
                ]
            ]);

            
           // error_log("User đăng nhập từ Đại học điện tử: 3");
           error_log('----------return from HU---------start------------');
           error_log($response->getBody());
           error_log('----------return from HU---------end------------');
            $data = json_decode($response->getBody());
            

            if (!$data) return abort(422, "Access denied");
            if ($data->err != 0) return abort(422, "Access denied");
            if (!$data->data) return abort(422, "Access denied");
            if (!(is_array($data->data) && count($data->data) > 0)) {
                return abort(422, "Access denied");
            }
            $outsite_user = $data->data[0];
           // error_log("User đăng nhập từ Đại học điện tử: 4");
            $user = User::where('csdt_id', $csdt->id)
                ->where('email', $outsite_user->Username)->first();
            if (!$user) {
                error_log("User [$outsite_user->Username] chưa được đăng ký ở hệ thống!");
                $user = new User;
            }
            
            //update
            $user->csdt_id = $csdt->id;
            $user->email = $outsite_user->Username;
            $user->user_name = $outsite_user->Username;
            $user->first_name = $outsite_user->Firstname;
            $user->last_name = $outsite_user->Lastname;
            $user->save();
            
            //Quyền
            if (isset($outsite_user->ds_quyen)) {
                $ds_quyen = $outsite_user->ds_quyen;
                $user->load('roles');
                $lst_roles = $user->roles;
                
                foreach ($lst_roles as $role) {
                    $is_existed = false;
                    foreach ($ds_quyen as $quyen) {
                        if ($role->code == $quyen->ma_quyen) {
                            $is_existed = true;
                            break;
                        }
                    }
                    if (!$is_existed) {
                        $user->roles()->detach($role->id);
                    }
                    
                }
                
            // Inerror_log('----------Danh sách Quyền------------');
                foreach ($ds_quyen as $quyen) {
                    $user->load('roles');
                    error_log('---mã quyền 0: '.$quyen->ma_quyen);
                    $role = Role::where('code', $quyen->ma_quyen)->first();
                    if (!isset($role)) {
                        $role = new Role;
                        $role->code = $quyen->ma_quyen;
                        $role->save();
                    }
                    error_log('---mã quyền 1: '.$quyen->ma_quyen);
                    $lst_roles = $user->roles->filter(function ($rl) use($role) {
                        return $rl->id == $role->id;
                    });
                    if (!(isset($lst_roles) && count($lst_roles) > 0)) {
                        $user->roles()->attach($role->id);
                        $user->save();
                    }
                    error_log('---mã quyền 2: '.$quyen->ma_quyen);
                }
                //xử lý thêm với chức vụ là trưởng
                $user->load('roles');
                try {
                    error_log('---roles 0: '.$outsite_user->is_truongdv);
                    if ($outsite_user->is_truongdv == 1) {
                        $lst_roles = $user->roles->filter(function ($rl) {
                            return $rl->code == 'egov_TRUONG_DON_VI';
                        });
                        if (count($lst_roles) == 0) {
                            $role = Role::where('code', 'egov_TRUONG_DON_VI')->first();
                            if (!isset($role)) {
                                $role = new Role;
                                $role->code = 'egov_TRUONG_DON_VI';
                                $role->save();
                            }
                            if (isset($role)) {
                                $user->roles()->attach($role->id);
                            }
                        }
                    } else if ($outsite_user->is_truongdv == 2) {
                        error_log('---roles 1: '.$outsite_user->is_truongdv);
                        $lst_roles = $user->roles->filter(function ($rl) {
                            return $rl->code == 'egov_PHO_DON_VI';
                        });
                        if (count($lst_roles) == 0) {
                            $role = Role::where('code', 'egov_PHO_DON_VI')->first();
                            if (!isset($role)) {
                                $role = new Role;
                                $role->code = 'egov_PHO_DON_VI';
                                $role->save();
                            }
                            if (isset($role)) {
                                $user->roles()->attach($role->id);
                            }
                        }
                    } else {
                        error_log('---roles 2: '.$outsite_user->is_truongdv);
                        $lst_roles = $user->roles->filter(function ($rl) {
                            return $rl->code == 'egov_TRUONG_DON_VI' || $rl->code == 'egov_PHO_DON_VI';
                        });
                        foreach ($lst_roles as $role) {
                            $user->roles()->detach($role->id);
                        }
                    }
                    $user->save();
                    
                }catch(Exception $e) {
                    error_log($e->getMessage());
                }
                
            }

 error_log("User đăng nhập từ Đại học điện tử - 21");
            //login
            //Auth::login($users);
            $token = "";
            try {
                $credentials = ['email'=>$user->email, 'password'=>$user->password];
                // attempt to verify the credentials and create a token for the user
                if (!$token = auth()->login($user)) {
                    return response([
                        'status' => 'error',
                        'error' => 'invalid.credentials',
                        'message' => 'Invalid Credentials.'
                    ], 400);
                }
            } catch (JWTException $e) {
                // something went wrong whilst attempting to encode the token
                error_log($e->getMessage());
                return response()->json(['error' => 'could_not_create_token'], 500);
            }
            error_log("Xu ly don vi 1");
            try {
                $user->load('donvis');
                foreach ($user->donvis as $donvi) {
                    if ($donvi->ma_donvi != $outsite_user->DepartmentCode) {
                        $user->donvis->detach($donvi->id);
                    }
                }
                error_log("Xu ly don vi 2");
                $user->save();
                if ($outsite_user->DepartmentCode) {
                    error_log("Xu ly don vi 3");
                    //$user_donvi = DonVi_User::where('user_id', )
                    $donVi = DonVi::where('ma_donvi', $outsite_user->DepartmentCode)->first();
                    if (!isset($donVi)) {
                        $donVi = new DonVi;
                        $donVi->ma_donvi = $outsite_user->DepartmentCode;
                        $donVi->ten_donvi = $outsite_user->DepartmentName;
                        $donVi->ten_ngan = $outsite_user->DepartmentName;
                        $donVi->csdt_id = $request->csdt_id;
                        $donVi->save();
                    }
                    if ($outsite_user->is_truongdv == 1) {
                        $donVi->truong_dv = $user->id;
                    
                    }
                    error_log("Xu ly don vi 4");
                    $user->load('donvis');
                    $lst_donvi = $user->donvis->filter(function ($dv) use ($donVi) {
                        return $dv->ma_donvi == $donVi->ma_donvi;
                    });
                    if (count($lst_donvi) == 0) {
                        $chucvu_cd = "";
                        if ($outsite_user->is_truongdv == 1) {
                            $chucvu_cd = 'egov_TRUONG_DON_VI';
                        }else if ($outsite_user->is_truongdv == 2) {
                            $chucvu_cd = 'egov_PHO_DON_VI';
                        } else {
                            
                        }
                        $user->donvis()->attach($donVi, ['chucvu_cd'=> $chucvu_cd]);
                    }
                    error_log("Xu ly don vi 5");
                    
                    $user->save();
                }
            }catch (Exception $e) {
                error_log($e->getMessage());
            }
            error_log("User đăng nhập từ Đại học điện tử - 3");
            //return redirect()->intended(route('ban-tin.index'));
            /* $headers = [
                'Authorization' => $token,
                'outsite_token' => $request->token
            ];
            error_log("User đăng nhập từ Đại học điện tử - 4");
            return redirect()->intended('http://localhost:3015',302,$headers); */
            LoginHistoryService::loginHistory(isset($user) ? $user->name : "");
            return response([
                'status' => 'success',
                'token' => $token,
                'outsite_token' => $request->token,
                
            ])->header('Authorization', $token);
       // }
       // return abort(404);
    }

    /**
     * Log out
     * Invalidate the token, so user cannot use it anymore
     * They have to relogin to get a new token
     *
     * @param Request $request
     */
    public function logout(Request $request)
    {
        
        try {

            //$this->guard()->logout();

            //$token = JwtAuthUtils::getTokenFromHeader($request);

            $token = JWTAuth::getToken();

            JWTAuth::invalidate($token);
            
            LoginHistoryService::logoutHistory();
            
            return response([
                'status' => 'ok',
                'message' => 'You have successfully logged out.'
            ]);
            
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            
            //print_r($e);
            return response([
                'status' => 'ng',
                'message' => 'Failed to logout, please try again: '.$e->getMessage()
            ]);
        } catch (Exception $e) {
            return response([
                'status' => 'ng',
                'message' => 'Failed to logout, please try again: '.$e->getMessage()
            ]);
        }
    }

    public function vertifyToken(Request $request) {
        $token_ok = $request->token;
        error_log('vertifyToken');
        error_log('token_ok: '.$token_ok);
        if (!isset($token_ok) || $token_ok == '') {
            return response([
                    'status' => 'error',
                    'error' => 'Không có token',
                    'user' => null
                ]);
        }
        try {
            
            $token = JWTAuth::getToken();
            
            error_log('token: '.$token);
            if ($token_ok == $token) {
                $user = JWTAuth::toUser($token);
                return response([
                    'status' => 'success',
                    'user' => $user
                ]);
            } else {
                return response([
                    'status' => 'error',
                    'error' => 'Token is time out',
                    'user' => null
                ]);
            }
        } catch (Exception $e) {
            return response([
                    'status' => 'error',
                    'error' => $e->getMessage(),
                    'error_code' => $e->getCode(),
                    'user' => null
                ]);
        }
    }
    public function user(Request $request)
    {
        try {

            /* if (!$this->guard()->check()) {
                error_log('AuthController: user - guard -> uncheck');
                return;
            } */

            $user = JWTAuth::parseToken()->authenticate();
            if (isset($user)) {
                
               // error_log('AuthController: user -----1-------'.Auth::user()->id.'------------:'.$user->id);
                $user = User::find($user->id);
                if (isset($user)) {
                    $user->load('locale')
                    ->load('roles')
                    ->load('donvis')
                    ->load('avatar');
                   /*  ->load('groups')
                    ->load('groups')
                    ->load('grouproles')
                    ->load('dschucvu')
                    ; */
                }
               // error_log('AuthController: user ------3------'.Auth::user()->id);
            }
           
            return response([
                'status' => 'success',
                'user' => $user
            ]);
        } catch (Exception $e) {
            
            return response([
                'status' => 'error',
                'error' => $e->getMessage(),
                'user' => null
            ], $e->getCode());
        }
    }


    public function refresh(Request $request)
    {
       
        $token_old = JWTAuth::getToken();
        $token = "";
       
        try {
            // $token = JwtAutUtils::getTokenFromHeader($request);

            //da login hay chua
            /* if (!$this->guard()->check()) {
                //$this->guard()->logout();
                return;
            } */
            // error_log("refresh token: 32");
            //$token = $this->auth->refresh()
            //$user = JWTAuth::parseToken()->authenticate();


            //JWTAuth::setToken(JWTAuth::refresh());
            //$token = JWTAuth::refresh();

            //error_log("refresh new token 0: ".$token);
            /* $token = JWTAuth::parseToken()->refresh();

            $token = JWTAuth::refresh($token);
            error_log("refresh new token 1: ".$token);

            $token = JwtAuthUtils::getTokenFromHeader($request);

            error_log('JwtMiddleware handle 1: token: '.$token);

            $token = JWTAuth::getToken();
            error_log("refresh new token 2: ".$token);
            */
            // $token = Auth::guard()->refresh();
          
            error_log('refresh token');

            $token = JwtAuthUtils::getTokenFromHeader($request);
            error_log("refresh old token 1: ".$token);
            if(!$token){
                throw new Exception("Token not provided");
            }
            try{
                $token = JWTAuth::refresh($token);
                JWTAuth::setToken($token);
            }catch(TokenInvalidException $e){
                throw new Exception('The token is invalid');
            }
            
            error_log("refresh new token 0: ".$token);

            error_log("refresh new token 1: ".JWTAuth::getToken());


            if ($token) {
                //error_log("refresh token: ".$token);
                return response()
                    ->json([
                        'status' => 'successs',
                        'token' => $token
                    ], 200)
                    ->header('Authorization', 'Bearer '.$token);
            }
            return response()->json(['error' => 'refresh_token_error'], 467);
            // }catch (TokenExpiredException $e) {
            //     error_log("refresh error 234234 34: ".$e->getMessage());
        } catch (TokenExpiredException $e) {
           
            $errorInf = JwtAuthUtils::parseExecption($e);
            return response()->json(
                [
                    'data' => null,
                    'status' => false,
                    'error' => $errorInf
                ],
                467 //need to logout
            );
        } catch (TokenBlacklistedException $e) {
            
            //JWTAuth::invalidate($token_old);
            //Auth::guard()->logout();

            $errorInf = JwtAuthUtils::parseExecption($e);
            return response()->json(
                [
                    'data' => null,
                    'status' => false,
                    'error' => $errorInf
                ],
                $errorInf['code']
            );
        } catch (TokenInvalidException $e) {
           
            $errorInf = [
                'message' => 'Token Invalid',
                'code' => 462
            ];
            return response()->json(
                [
                    'data' => null,
                    'status' => false,
                    'error' => $errorInf
                ],
                $errorInf['code']
            );
        } catch (Exception $e) {
           

            $errorInf = JwtAuthUtils::parseExecption($e);
            if (isset($errorInf)) {
                
            }
            // $token = JwtAuthUtils::getTokenFromHeader($request);
            try {
                JWTAuth::invalidate($token);
                //$this->guard()->logout();
            } catch (Exception $e2) {
                
            }
            return response()->json(
                [
                    'data' => null,
                    'status' => false,
                    'error' => $errorInf
                ],
                $errorInf['code']
            );
            //return response()->json(['error' => 'refresh_token_error'], 601);
            // return $this->responseJson(['error' => 'refresh_token_error'], 601);

        }
    }
    /* private function guard()
    {
        return Auth::guard();
    }  */
    public function broadcastAuth(Request $request) {
        error_log('broadcastAuth: '.csrf_token().';---:'.Session::token());
        //Log::info('broadcastAuth::channel: message');
        Log::info($request->channel_name);
        
        if (\Auth::check()) {
            $user = JWTAuth::parseToken()->authenticate();
            $token = JWTAuth::getToken();
           //error_log('broadcastAuth sessionId '.session()->getId().';-----:'.csrf_token().';----:'.$user->name.";--:".$token);
           error_log('broadcastAuth csrf_token '.csrf_token());
            /* return response()->json($user, 200)
                    ->header('X-CSRF-Token', csrf_token()); */
           // return response()->json(['id' => $user->id, 'name' => $user->name], 200)->header('Authorization', 'Bearer '.$token);
           Log::info($user);
           
           return response()->json(['id' => $user->id, 'name' => $user->name], 200)->header('Authorization', 'Bearer '.$token);
        } else {
            return response()->json('UNAUTHENTICATED', 401);
        }
    }
}
