<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

use App\User;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    protected $redirectTo = '/';
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $errors = [
            'name.required' => __('auth.name_required'),
            'email.required' => __('auth.email_required'),
            'name.exists' => __('auth.name_not_found'),
            'email.exists' => __('auth.email_not_found')
        ];
        $validator = \Validator::make($request->all(), ['name' => 'required|exists:users', 'email' => 'required|exists:users'], $errors);

        if (!$validator->fails()) {
            $user = User::where('name', $request->name)->where('email', $request->email)->first();

            if ($user != null) {
                $response = $this->broker()->sendResetLink($request->only('email'));
        
                if ($response === Password::RESET_LINK_SENT) {
                    return view('auth.passwords.reset_email_sent')->with('status', trans($response));
                }
            }
        }
        
        return view('auth.passwords.reset_email_sent')->withErrors($validator);
    }
}
