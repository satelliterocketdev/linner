<?php

namespace App\Http\Controllers\Auth;

use App\Role;
use App\RoleUser;
use App\User;
use App\Http\Controllers\Controller;
use DB;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use Illuminate\Validation\ValidationException;
use Lang;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
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

    public function sendMail(Request $request)
    {
        try {
            DB::beginTransaction();
            $this->validate($request, [
                'email' => 'required|string|email|max:255',
                'password' => 'required|string|min:6|confirmed|alpha_num',
            ]);
            $user = User::where('email', $request['email'])->first();
            if (is_null($user)) {
                $user = $this->createUser($request);
            } else {
                if ($user->register_token !== '1') {
                    $user->delete();
                    $user = $this->createUser($request);
                } else {
                    return response()->redirectTo('/register')
                        ->withErrors(
                            Lang::get(
                                'validation.unique',
                                ['attribute' => Lang::get('validation.attributes.email')]
                            )
                        );
                }
                if (User::where('email', $request['email'])->where('register_token', '!=', '1')->exists()) {
                    User::where('email', $request['email'])->where('register_token', '!=', '1')->delete();
                }
            }

            Mail::send('auth.confirm_email', ['user' => $user], function ($message) use ($user) {
                $message->to($user->email, $user->name)->subject(Lang::get('register.thanks_for_registering'));
            });
            DB::commit();
            return view('auth.email_sent');
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->redirectTo('/register')
                ->withErrors($e->validator);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->redirectTo('auth.register')
                ->withErrors(Lang::get('register.register_fails'));
        }
    }

    public function register($token)
    {
        //$failsResponse = redirect()->to('/login')->with('alert', __(Lang::get('register.register_fails')));
        $failsResponse = redirect()->to('/login');
        if (is_null($token)) {
            return $failsResponse;
        }

        $user = User::where('register_token', $token)->first();
        if (is_null($user)) {
            return $failsResponse;
        }
        try {
            DB::beginTransaction();
            $user->register_token = "1";
            $user->save();
            auth()->login($user);
            DB::commit();
            return view('auth.register_complete');
        } catch (Exception $e) {
            DB::rollBack();
            return $failsResponse;
        }
    }

    public function registerComplete()
    {
        return view('auth.register_complete');
    }

    private function createUser(Request $request)
    {
        $user = User::create([
            'name' => $request['email'],
            'email' => $request['email'],
            'password' => bcrypt(\request()['password']),
            'register_token' => bin2hex(openssl_random_pseudo_bytes(16)),
            'admin' => 0,
            'account_id' => null
        ]);

        $roleUser = new RoleUser();
        $roleUser->account_id = null;
        $roleUser->role_id = Role::ROLE_ACCOUNT_ADMINISTRATOR;
        $user->roleUsers()->save($roleUser);

        return $user;
    }
}
