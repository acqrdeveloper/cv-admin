<?php

namespace CVAdmin\Http\Controllers\Auth;

use CVAdmin\Http\Controllers\Controller;
use CVAdmin\User;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * HACER LOGIN
     */
    public function doLogin()
    {
        $rules = array(
            'email' => 'required|email', // make sure the email is an actual email
            'password' => 'required|min:3' // password can only be alphanumeric and has to be greater than 3 characters
        );
        $msg = [
            'email' => Input::get('email')
        ];
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return redirect()->route('login')
                ->withErrors($validator)// send back all errors to the login form
                ->withInput($msg); // send back the input (not the password) so that we can repopulate the form
        } else {
            $user = User::where('email', Input::get('email'))->where('contrasenia', Input::get('password'))->where('estado', 'A')->first();
            if (!is_null($user)) {
                Auth::login($user);
                return redirect()->to($this->redirectTo);
            } else {
                return redirect()->route('login')->withInput($msg);
            }
        }
    }

}
