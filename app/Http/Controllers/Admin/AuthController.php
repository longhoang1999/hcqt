<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\JoshController;
use App\Http\Requests\ConfirmPasswordRequest;
use App\Mail\Register;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Mail;
use Reminder;
use Sentinel;
use Lang;
use URL;
use Validator;
use View;
use DB;
use Hash;
use App\Http\Requests\UserRequest;
use App\Http\Requests\ForgotRequest;
use stdClass;
use App\Mail\ForgotPassword;

class AuthController extends JoshController
{
    /**
     * Account sign in.
     *
     * @return View
     */

    private $user_activation = false;


    public function getLoginToken(Request $req) {
        
        
        $post = array("access_token" => $req->token);
        $ch = curl_init(env('TOKEN_AUTH','https://accounts.demo-hict.dschool.edu.vn?m=post-id'));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        // execute!
        $response = curl_exec($ch);
        // close the connection, release resources used
        curl_close($ch); 

        $dat = json_decode($response);

        if($dat->err == 0){
            $data = $dat->data[0];
            Sentinel::logout();
            $check = DB::table('users')->select('id')->where('email',$data->Username)->first();
            if($check){
                $user = Sentinel::findById($check->id);
                Sentinel::login($user);
            }else{
                $str = rand() . date('Y-m-d H:i:s');
                $pass = Hash::make($str);
                $user = Sentinel::register(
                    [
                    'email'         => $data->Username,
                    'name'          => $data->Firstname  . ' ' . $data->Lastname,    
                    'first_name'         => $data->Firstname,
                    'last_name'            => $data->Lastname,
                    'password'            => $pass,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                    ],
                    true
                );
                //add user to 'User' role
                $role = Sentinel::findRoleById(4);
                if ($role) {
                    $role->users()->attach($user);
                }
                Sentinel::login($user);
            }
            return redirect('admin');
        }
        return redirect('admin.login')->with('error','Vui lòng xác thực một cửa tại hệ thống trường học số.');
    }



    public function getSignin()
    {
        // Is the user logged in?
        if (Sentinel::check()) {
            // return Redirect::route('admin.dashboard');
            return Redirect::route("admin.lichcongtac.lichcongtactuan.index");
        }

        // Show the page
        return view('admin.login');
    }

    /**
     * Account sign in form processing.
     *
     * @param  Request $request
     * @return Redirect
     */
    public function postSignin(Request $request)
    {
        try {
            $remember = (bool) $request->get('remember-me', false);
            if ($user = Sentinel::authenticate($request->only(['email', 'password']), $remember)) {
                activity($user->full_name)
                    ->performedOn($user)
                    ->causedBy($user)
                    ->log('LoggedIn');
                return Redirect::route("admin.lichcongtac.lichcongtactuan.index")->with('success', trans('auth/message.signin.success'));
            } else {
                $this->messageBag->add('email', Lang::get('project/Common/title.tkmkw'));
            }
        } catch (NotActivatedException $e) {
            $this->messageBag->add('email', Lang::get('project/Common/title.tkckh'));
        } catch (ThrottlingException $e) {
            $delay = $e->getDelay();
            $this->messageBag->add('email', trans('project/Common/title.account_suspended', compact('delay')));
        }
        // Ooops.. something went wrong
        return Redirect::back()->withInput()->withErrors($this->messageBag);

    }

    /**
     * Account sign up form processing.
     *
     * @return Redirect
     */
    public function postSignup(UserRequest $request)
    {
        $activate = $this->user_activation;

        try {
            // Register the user
            $user = Sentinel::register(
                [
                'first_name' => $request->get('first_name'),
                'last_name' => $request->get('last_name'),
                'email' => $request->get('email'),
                'password' => $request->get('password'),
                ],
                $activate
            );
            // login user automatically
            $role = Sentinel::findRoleById(2);
            //add user to 'User' role

            $role->users()->attach($user);
            if (!$activate) {
                // Data to be used on the email view

                $data=[
                    'user_name' => $user->first_name .' '. $user->last_name,
                    'activationUrl' => URL::route('activate', [$user->id, Activation::create($user)->code]),
                ];
                // Send the activation code through email
                Mail::to($user->email)
                    ->send(new Register($data));

                //Redirect to login page
                return redirect('admin.login')->with('success', trans('auth/message.signup.success'));
            }
            // Log the user in
            Sentinel::login($user, false);
            //Activity log

            activity($user->full_name)
                ->performedOn($user)
                ->causedBy($user)
                ->log('Registered');
            //activity log ends
            // Redirect to the home page with success menu
            return Redirect::route("admin.dashboard")->with('success', trans('auth/message.signup.success'));
        } catch (UserExistsException $e) {
            $this->messageBag->add('email', trans('auth/message.account_already_exists'));
        }

        // Ooops.. something went wrong
        return Redirect::back()->withInput()->withErrors($this->messageBag);
    }

    /**
     * User account activation page.
     *
     * @param  number $userId
     * @param  string $activationCode
     * @return
     */
    public function getActivate($userId, $activationCode = null)
    {
        // Is user logged in?
        if (Sentinel::check()) {
            return Redirect::route('admin.dashboard');
        }

        $user = Sentinel::findById($userId);
        $activation = Activation::create($user);

        if (Activation::complete($user, $activation->code)) {
            // Activation was successful
            // Redirect to the login page
            return Redirect::route('signin')->with('success', trans('auth/message.activate.success'));
        } else {
            // Activation not found or not completed.
            $error = trans('auth/message.activate.error');
            return Redirect::route('signin')->with('error', $error);
        }
    }

    /**
     * Forgot password form processing page.
     *
     * @param Request $request
     *
     * @return Redirect
     */
    public function postForgotPassword(ForgotRequest $request)
    {
        $data = new stdClass();

        try {
            // Get the user password recovery code
            $user = Sentinel::findByCredentials(['email' => $request->get('email')]);

            if (!$user) {
                return back()->with('error', trans('auth/message.account_email_not_found'));
            }
            $activation = Activation::completed($user);
            if (!$activation) {
                return back()->with('error', trans('auth/message.account_not_activated'));
            }
            $reminder = Reminder::exists($user) ?: Reminder::create($user);
            // Data to be used on the email view

            $data->user_name = $user->first_name .' ' .$user->last_name;
            $data->forgotPasswordUrl = URL::route('forgot-password-confirm', [$user->id, $reminder->code]);

            // Send the activation code through email

            Mail::to($user->email)
                ->send(new ForgotPassword($data));
        } catch (UserNotFoundException $e) {
            // Even though the email was not found, we will pretend
            // we have sent the password reset code through email,
            // this is a security measure against hackers.
        }

        //  Redirect to the forgot password
        return back()->with('success', trans('auth/message.forgot-password.success'));
    }

    /**
     * Forgot Password Confirmation page.
     *
     * @param  number $userId
     * @param  string $passwordResetCode
     * @return View
     */
    public function getForgotPasswordConfirm($userId, $passwordResetCode = null)
    {
        // Find the user using the password reset code
        if (!$user = Sentinel::findById($userId)) {
            // Redirect to the forgot password page
            return Redirect::route('forgot-password')->with('error', trans('auth/message.account_not_found'));
        }
        if ($reminder = Reminder::exists($user)) {
            if ($passwordResetCode == $reminder->code) {
                return view('admin.auth.forgot-password-confirm');
            } else {
                return 'code does not match';
            }
        } else {
            return 'does not exists';
        }

        // Show the page
        // return View('admin.auth.forgot-password-confirm');
    }

    /**
     * Forgot Password Confirmation form processing page.
     *
     * @param  Request $request
     * @param  number  $userId
     * @param  string  $passwordResetCode
     * @return Redirect
     */
    public function postForgotPasswordConfirm(ConfirmPasswordRequest $request, $userId, $passwordResetCode = null)
    {

        // Find the user using the password reset code
        $user = Sentinel::findById($userId);
        if (!$reminder = Reminder::complete($user, $passwordResetCode, $request->get('password'))) {
            // Ooops.. something went wrong
            return Redirect::route('signin')->with('error', trans('auth/message.forgot-password-confirm.error'));
        }

        // Password successfully reseted
        return Redirect::route('signin')->with('success', trans('auth/message.forgot-password-confirm.success'));
    }

    /**
     * Logout page.
     *
     * @return Redirect
     */
    public function getLogout()
    {

        if (Sentinel::check()) {
            //Activity log
            $user = Sentinel::getuser();
            activity($user->full_name)
                ->performedOn($user)
                ->causedBy($user)
                ->log('LoggedOut');
            // Log the user out
            Sentinel::logout();
        }


        // Redirect to the users page
        return redirect('admin/signin')->with('success', 'You have successfully logged out!');
    }

    /**
     * Account sign up form processing for register2 page
     *
     * @param Request $request
     *
     * @return Redirect
     */
    public function postRegister2(UserRequest $request)
    {

        try {
            // Register the user
            $user = Sentinel::registerAndActivate(
                [
                'first_name' => $request->get('first_name'),
                'last_name' => $request->get('last_name'),
                'email' => $request->get('email'),
                'password' => $request->get('password'),

                ]
            );

            //add user to 'User' role
            $role = Sentinel::findRoleById(2);
            $role->users()->attach($user);

            // Log the user in
            Sentinel::login($user, false);

            // Redirect to the home page with success menu
            return Redirect::route("admin.dashboard")->with('success', trans('auth/message.signup.success'));
        } catch (UserExistsException $e) {
            $this->messageBag->add('email', trans('auth/message.account_already_exists'));
        }

        // Ooops.. something went wrong
        return Redirect::back()->withInput()->withErrors($this->messageBag);
    }
}
