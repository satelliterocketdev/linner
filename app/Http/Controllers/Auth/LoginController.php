<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Role;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Utility;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Support\MessageBag;
use Hash;

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
     *
     * @return void
     */
    public function __construct(Utility $utils)
    {
        $this->utils  = $utils;
        $this->middleware('guest', ['except' => 'logout']);
    }
    
    public function showLoginForm()
    {
        return view(
            'auth.login',
            [
                'randomKey' =>  $this->utils->randomKey(25),
            ]
        );
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $request->email)->first();
        $errors = new MessageBag;
        if ($user != null) {
            if (Hash::check($request->password, $user->password)) {
                auth()->login($user);
                return redirect('/');
            }

            $errors->add('password', __('auth.invalid_password'));
            return view('auth.login', ['errors' => $errors]);
        }

        $errors->add('email', __('auth.invalid_email'));
        return view('auth.login', ['errors' => $errors]);
    }
}
