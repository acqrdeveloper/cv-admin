<?php
/**
 * Created by PhpStorm.
 * User: QuispeRoque
 * Date: 03/04/17
 * Time: 11:58
 */

namespace CVAdmin\Http\Controllers\Auth;

use CVAdmin\User;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;

class GAuthLoginController
{

    function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    function handleProviderCallback()
    {
        try {
            $user = Socialite::driver('google')->user();

            if (User::where('email', $user->getEmail())->where('estado', 'A')->first()) {
                Auth::login(User::where('email', $user->getEmail())->where('estado', 'A')->first());
                return redirect()->to('/');
            } else {
                Session::flash('alert-class', 'alert-danger');
                return redirect()->to('/login')->with(['message' => 'este usuario no esta habilitado!, contacte con el ADMINISTRADOR.']);
            }

        } catch (\Exception $e) {
            //dd($e);
            return redirect()->to('/login')->with(['message' => $e->getMessage()]);
            //return redirect()->to('/login');/**/
        }


    }
}