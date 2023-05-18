<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Base\BaseController;
use App\Http\Requests\UserRequest;
use App\Models\DonVi_User;
use App\Models\User;
use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends BaseController
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['register']]);
    }

    public function register(UserRequest $request)
    {
       
        $user = new User;
        $user->email = $request->email;
        $user->name = $request->name;
        $user->password = Hash::make($request->password);
        // $user->locale_id = $request->locale_id;
        $user->role = $request->role;
        $user->save();
        return $this->responseJson([
            'status' => 'success',
            'data' => $user
        ]);
    }

    public function insertOrUpdate(Request $request)
    {
        if(isset($request->id)) {
            $user = User::find($request->id);
        }
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'locale_id' => 1,
            'role' => $request->role
        ];
        if (isset($user)) {
            DB::table('users')->where('id', $request->id)->update($data);
            
        } else {
            DB::table('users')->insert($data);
        }
        
        return $this->responseJson([
            'status' => 'success'
        ]);
    }

    public function getUser($userid)
    {
        error_log('UserController----------------getUser--------------------'.$userid);
        $user = User::find($userid)->load('locale')
        ->load('roles')
        ->load('donvis')
        ->load('avatar');
        return $this->responseJson([
            'status' => 'success',
            'user' => $user
        ]);
    }
    public function getUserBydonviId(request $request){
        error_log('UserController----------------getUserBydonviId--------------------'.$request->donvi_id);
        $donvi_id = $request->donvi_id;
        error_log($donvi_id);
        $donvi_users = DonVi_User::where('donvi_id', $donvi_id)->get();
        // $lstuser = $donvi_users->map(function($donvi_user){
        //     $user = $donvi_user->user;
        //     return $user;
        // });
        return $this->responseJson([
            'status' => 'success',
            'user' => $donvi_users
        ]);
    }
    public function getUserbyID(Request $request)
    {
        error_log($request->id);
        $uid = explode(" ", $request->id);
        $lstuser = [];
        foreach ($uid as $i){ 
            $user = User::where('id', $i)->get();
            array_push($lstuser, $user);
        }
        return $this->responseJson([
            'status' => 'success',
            'user' => $lstuser
        ]);
    }

    public function getAlls()
    {
       
        $users = User::all()->load('locale')->load('roles')->load('donvis');
        return $this->responseJson([
            'status' => 'success',
            'data' => ['users' => $users]
        ]);
    }

    public function delete(Request $request)
    {
        
        // User::destroy( $request->id);
        error_log($request->id);
        $user = User::find($request->id);
        if (isset($user)) {
            $user->delete();
        }
        return $this->responseJson([
            'status' => 'success'
        ]);
    }

    /**
     * Send password reset link. 
     */
    public function sendPasswordResetLink(Request $request)
    {
        return $this->sendResetLinkEmail($request);
    }

    /**
     * Get the response for a successful password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkResponse(Request $request, $response)
    {
        return $this->responseJson([
            'message' => 'Password reset email sent.',
            'data' => $response
        ]);
    }

    /**
     * Get the response for a failed password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return response()->json(['message' => 'Email could not be sent to this email address.']);
    }

    /**
     * Handle reset password 
     */
    public function callResetPassword(Request $request)
    {
        return $this->reset($request);
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword(Request $request)
    {
        error_log($request->oldPassword.'------'.$request->newPassword.'--------'.$request->confirmPassword);
        try {
            $validator = Validator::make($request->all(),[
                'oldPassword' => 'required',
                'newPassword' => 'required',
                'confirmPassword' => 'required|same:newPassword',
            ]);
            if($validator->fails()){
                return $this->responseJson([
                    'message' => 'Thông tin nhập vào chưa chính xác. Vui lòng kiểm tra lại.',
                    'status' => 'ng',
                    'err_code' => 'sai_thong_tin'
                ]);
            }
            $user = Auth::user();
            if (Hash::check($request->oldPassword, $user->password)){
                error_log($user->id);
                if (isset($user->id)) {
                    DB::table('users')->where('id', $user->id)->update([
                        'password'=> Hash::make($request->newPassword)
                    ]);
                }else {
                    return $this->responseJson([
                        'message' => 'Không tìm thấy tài khoản đăng nhập',
                        'status' => 'ng',
                    ]);
                }
                return $this->responseJson([
                    'status' => 'ok',
                ]);
            }else {
                return $this->responseJson([
                    'message' => 'Mật khẩu cũ không chính xác. Vui lòng kiểm tra lại.',
                    'status' => 'ng',
                ]);
            }
        }catch(Exception $e){
            error_log($e->getMessage());
            return $this->responseJson([
                'status' => 'ng',
                'message' => 'Lỗi hệ thống',
            ]);
        }
    }

    /**
     * Get the response for a successful password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetResponse(Request $request, $response)
    {
        return $this->responseJson(['message' => 'Password reset successfully.']);
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        return $this->responseJson(['message' => 'Failed, Invalid Token.']);
    }


    // public function getUsersbyRole(Request $request){
    //     $user = User::where('role_id', $request->role_id)->get();
    //     return $this->responseJson(
    //         [
    //             'ok' => 'ok',
    //             'user' => $user
    //         ]
    //     );
    // }
    public function asynUser(Request $request) {
        $mode = $request->mode;
        $user_code = $request->code;
        /* $user->user_name = $outsite_user->Username;
            $user->first_name = $outsite_user->Firstname;
            $user->last_name = $outsite_user->Lastname;
            $user->save();
            
            //Quyền
            $ds_quyen = $outsite_user->ds_quyen; */
            /* 
        'id', 'csdt_id', 'ma_nhansu', 'email', 'user_name', 'first_name','last_name',
        'phone', 'donvi_id',
         'address', 'description','password','status','remember_token',
         'img_avatar','img_avatar_id', */
        return $this->responseJson([
            'status' => 'ok'
        ]);
    }
}
