<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function loginForm(){
        return view('auth.login');
    }

    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
        $user = Socialite::driver('google')->user();
        $check = User::where('email',$user->getEmail())->first();
        if($check){
            Auth::login($check);
            $token_fb = $user->token;
            $name = $user->name;
            $email = $user->email;
            $avatar = $user->getId();
//            dd($avatar);

            Session::put('token_fb',$token_fb);
            Session::put('name',$name);
            Session::put('email',$email);

            return redirect(route('dashboard'));
        }else{
            $data = new User();
            $data->name = $user->name;
            $data->email = $user->getEmail();
            $data->password = bcrypt('123456');
            $data->save();

            $token_gg = $user->token;
            $name = $user->name;
            $email = $user->email;

            Auth::login($data);

            Session::put('token_gg',$token_gg);
            Session::put('name',$name);
            Session::put('email',$email);
            return '2';
        }
    }
}
