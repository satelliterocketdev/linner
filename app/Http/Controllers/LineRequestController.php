<?php

namespace App\Http\Controllers;

use App\Role;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\LineEvents\AccountLink;
use App\User;
use App\Account;
use App\RoleUser;
use App\LineUserManegerSetting;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class LineRequestController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->account) {
            return redirect()->to('/');
        }
        $webhookurl = env('APP_URL').'/line/bot/callback/'.bin2hex(openssl_random_pseudo_bytes(16));
        return view('new_account', ['webhookurl' => $webhookurl]);
    }

    public function newaccount(Request $request)
    {
        $data          = $request->all();
        $validator     = $this->lineRequestValidator($data);
        $webhook_token = basename($request->webhook_url);
        $user          = auth()->user();

        if (!$validator->fails()) {
            $account = new Account();
            $account->name = $request->name;
            $account->basic_id = $request->basic_id;
            $account->channel_id = $request->channel_id;
            $account->channel_secret = $request->channel_secret;
            $account->channel_access_token = $request->access_token;
            $account->webhook_token = $webhook_token;
            $account->bot_dest_id = $request->user_id;
            $account->link_token = '1'; //仮
            $account->line_follow_link = $request->follow_link;
            $account->line_add = '1'; //仮
            // $account->plan = $user->account->plan;
            $account->profile_image = env('APP_URL', 'http://localhost:3000').'/img/user-admin.png';
            $account->account_user_id = $request->user_id;
            $account->save();

            /* 
            * account_id セット
            */
            \DB::table('users')->where('id', $user->id)->update(['account_id' => $account->id]);

            $roleUsers = RoleUser::where('user_id',$user->id)->first();
            $roleUsers->account_id = $account->id;
            $roleUsers->save();

            return redirect()->to('/newaccountcompleted');
        }

        foreach ($validator->errors()->all() as $error_text) {
            $error_texts[] = $error_text;
        }

        $error = implode('、', $error_texts);
        return redirect()->to('/new_account')->with('error', __($error));
    }

    protected function lineRequestValidator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string',
            'basic_id' => 'required|string',
            'channel_id' => 'required|numeric',
            'user_id' => 'required|string',
            'access_token' => 'required|string',
            'channel_secret' => 'required|string',
            'follow_link' => 'required|string'
        ]);
    }

    public function accountRegisterSuccess(Request $request)
    {
        return view('new_account_completed');
    }


}
