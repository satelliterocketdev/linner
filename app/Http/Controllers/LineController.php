<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Utility;
use App\UserLineToken;
use App\LineUserManegerSetting;
use App\LineAccountDetails;
use App\Role;
use \Firebase\JWT\JWT;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMailCred;
use App\LineEvents\LineUtils;

class LineController extends Controller
{
    use RedirectsUsers;
    
    public function __construct(Request $request, Utility $util, LineUtils $lineUtils)
    {
        $this->utils = $util;
        $this->lineUtils = $lineUtils;
    }

    public function index(Request $request)
    {
        if ($request->has('state') && $request->has('code')) { //ilagay to sa middleware
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.line.me/oauth2/v2.1/token");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type:application/x-www-form-urlencoded',
            ]);
            $arrData = array(
                                'grant_type'=>'authorization_code',
                                'code'=>$request->input('code'),
                                'redirect_uri'=>env('LINE_CALLBACK_URL'),
                                'client_id'=>env('LINE_CLIENT_ID'),
                                'client_secret'=>env('LINE_CHANNEL_SECRECT')
            );
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($arrData));
            $server_output = curl_exec($ch);
            curl_close($ch);

            try {
                $jsonObjServer=json_decode($server_output);
                $token = $jsonObjServer->id_token;

                //decode jwt for
                JWT::$leeway = 5;
                $decoded = JWT::decode($token, env('LINE_CHANNEL_SECRECT'), array('HS256'));
                $lineAccountDetails = $this->lineUtils->accountProfile($jsonObjServer->access_token);
                //create random password
                $randomPassword = $this->utils->randomKey(8);

                //user data
                $userData['name']=$decoded->name;
                $userData['email']=$decoded->email;
                $userData['password']=$randomPassword;

                $userExist = User::where('email', '=', $userData['email'])->first();
                if ($userExist === null) {
                    event(new Registered($user = $this->create($userData)));
                    // $user = $this->create($userData);
                    $this->guard()->login($user);
                    // print_r("hahaha".$user);
                    // return;
                    //user token data
                    $userLineAcctDet['user_id']=Auth::id();
                    $userLineAcctDet['line_user_id']=$lineAccountDetails->userId;
                    $userLineAcctDet['line_user_displayname']=$lineAccountDetails->displayName;
                    $userLineAcctDet['line_user_pic']=isset($lineAccountDetails->pictureUrl) ? $lineAccountDetails->pictureUrl : null;
                    $userLineAcctDet['line_user_msgstatus']=isset($lineAccountDetails->statusMessage) ? $lineAccountDetails->statusMessage : null;
                    $userLineAcctDet['access_token']=$jsonObjServer->access_token;
                    $userLineAcctDet['token_type']=$jsonObjServer->token_type;
                    $userLineAcctDet['scope']=$jsonObjServer->scope;
                    $userLineAcctDet['refresh_token']=$jsonObjServer->refresh_token;
                    $userLineAcctDet['expires_in']=$jsonObjServer->expires_in;

                    // print_r($userLineAcctDet);
                    //return ;//;$userLineAcctDet;
                    //insert to database tabla token
                    // $this->initTag($userLineAcctDet);
                    // $this->initTemplate($userLineAcctDet);
                    $this->lineToken($userLineAcctDet);
                    $this->lineAccountProfile($userLineAcctDet);
                    $this->botChannelSettings($userLineAcctDet);

                    Mail::to($decoded->email)->send(new SendMailCred($userData));
                } else {
                    return redirect()->route('login')->with(['data'=>"Exsiting User"]);
                }
            } catch (Exception $e) {
                return redirect()->route('/');
            }
            return $this->registered($request, $user)
                            ?: redirect($this->redirectPath())->with(['oauth' => true, 'email'=>$decoded->email]);
        } else {
            return redirect()->route('/');
        }
        return;
        //insert somedate to the database if the line token is taken else return login page.. make a middleware to come here first before laravel auth
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $role_user = Role::where('name', 'user')->first();
        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = bcrypt($data['password']);
        $user->save();
        $user->roles()->attach($role_user);
        return $user;
    }

    protected function lineToken(array $data)
    {
        // print_r($data);
        // return;
        $userToken = new UserLineToken();
        $userToken->user_id = $data['user_id'];
        $userToken->access_token = $data['access_token'];
        $userToken->token_type = $data['token_type'];
        $userToken->scope = $data['scope'];
        $userToken->save();
    }

    protected function lineAccountProfile(array $data)
    {
        $lineAccountDetails = new LineAccountDetails();
        $lineAccountDetails->user_id = $data['user_id'];
        $lineAccountDetails->line_user_id = $data['line_user_id'];
        $lineAccountDetails->display_name = $data['line_user_displayname'];
        $lineAccountDetails->picture = $data['line_user_pic'];
        $lineAccountDetails->status_message = $data['line_user_msgstatus'];
        $lineAccountDetails->save();
    }

    protected function botChannelSettings(array $data)
    {
        $lineSettingMan = new LineUserManegerSetting();
        $lineSettingMan->user_id = $data['user_id'];
        $lineSettingMan->channel_id = null;
        $lineSettingMan->channel_secret = null;
        $lineSettingMan->channel_access_token = null;
        $lineSettingMan->webhook_uRL = url('').'/line/bot/callback/';
        $lineSettingMan->save();
    }

    // protected function initTag(array $data){
    //   return DB::table('tag_managements')->insert([
    //     'user_id'=>$data['user_id'],
    //     'data'=>'[{"parent":"Uncategorized","child":[]}]',
    //   ]);
    // }
    // protected function initTemplate(array $data){
    //   return DB::table('templates')->insert([
    //     'user_id'=>$data['user_id'],
    //     'data'=>'[{"parent":"Uncategorized","child":[]}]', //[{'parent':'someparentname',child:[somechildId]}]
    //   ]);
    // }

    public function lineuserprofile(String $token)
    {
        //
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        //
    }
}
