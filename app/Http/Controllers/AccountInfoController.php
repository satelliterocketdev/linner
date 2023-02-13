<?php

namespace App\Http\Controllers;

use App\Plan;
use App\Role;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;
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
use App\Services\MediaFileService;

class AccountInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $account = $user->account;

        $roleUser = new RoleUser;
        $accountIds = $roleUser->where('user_id', $user->id)->pluck('account_id');
        $accounts = Account::whereIn('id', $accountIds)->get();

        return view("accountinfo", $request)
            ->with('accounts', $accounts)
            ->with('account', $account)
            ->with('user_id', $user->id)
            ->with('isAdmin', $user->hasRole(Role::ROLE_ACCOUNT_ADMINISTRATOR, $user->account_id))
            ->with('follower_count', $user->account->accountFollowers()->count())
            ->with('account_count_num', $accounts->count())
            ->with('account_count_denom', $user->plan->account_count)
            ->with('plan', $user->plan->level)
            ->with('new_webhook_token', null);
    }

    public function changeAccount($account_id)
    {
        if (!empty($account_id)) {
            $user = Auth::user();
            $user->account_id = $account_id;
            $user->save();
        }

        return redirect()->to('/accountinfo');
    }

    public function lists()
    {
        $user = Auth::user();
        if ($user->hasRole(Role::ROLE_ACCOUNT_ADMINISTRATOR, $user->account->id)) {
            $userIds = $user->account->roleUsers()->pluck('user_id');
            $users = User::whereIn('id', $userIds)->orderBy('name', 'asc')->get();
    
            foreach ($users as $user) {
                $user->roles = collect($user->roleUsers()->where('account_id', $user->account->id)->whereNotNull("role_id")->get())->map->role;
            }
            return response()->json($users, Response::HTTP_OK);
        }
        $users[] = $user;
        return response()->json($users, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255',
                'password' => 'required|confirmed|min:6',
            ]);
        } catch (ValidationException $e) {
            return response()->json($e->validator->getMessageBag()->toArray(), Response::HTTP_BAD_REQUEST);
        }

        try {
            DB::beginTransaction();
            $authUser = Auth::user();
            $customUser = User::where('email', $request['email'])->first();
            if (!$customUser) {
                $user = User::create([
                    'name' => $request['name'],
                    'email' => $request['email'],
                    'password' => bin2hex(openssl_random_pseudo_bytes(16)),
                    'register_token' => '1',
                    'admin' => 0,
                    'plan_id' => null,
                    'account_id' => $authUser->account->id
                ]);
                $user->setPassword($request['password']);
                $user->save();
            }

            $customUser->roleUsers()
                ->where('account_id', $authUser->account_id)
                ->where('role_id', '!=', Role::ROLE_ACCOUNT_ADMINISTRATOR)
                ->delete();
            foreach ($request['roles'] as $role) {
                RoleUser::create([
                    'role_id' => $role,
                    'user_id' => $customUser->id,
                    'account_id' => $authUser->account_id
                ]);
            }
            if ($customUser->roleUsers()->where('account_id', $authUser->account_id)->count() === 0) {
                RoleUser::create([
                    'role_id' => null,
                    'user_id' => $customUser->id,
                    'account_id' => $authUser->account_id
                ]);
            }

            // TODO: メール送信
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(["error" => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * @throws Exception
     */
    public function updateSecret(Request $request, $id = null)
    {
        // secret更新
        DB::beginTransaction();
        try {
            $this->validate(
                $request,
                [
                    'channel_secret' => 'required|string|max:255'
                    ]
            );
            if (empty($id)) {
                $account = Auth::user()->account;
            } else {
                $account = User::find($id)->account;
            }
            $account->channel_secret = $request->input('channel_secret', '');
            $account->save();
            DB::commit();
            return response()->json(null, Response::HTTP_OK);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json($e->validator->getMessageBag()->first(), Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function updateAccessToken(Request $request, $id = null)
    {
        // access token 更新
        DB::beginTransaction();
        try {
            $this->validate(
                $request,
                [
                    'channel_access_token' => 'required|string|max:255'
                    ]
            );
            if (empty($id)) {
                $account = Auth::user()->account;
            } else {
                $account = User::find($id)->account;
            }
            $account->channel_access_token = $request->input('channel_access_token');
            $account->save();
            DB::commit();
            return response()->json(null, Response::HTTP_OK);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json($e->validator->getMessageBag()->first(), Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function updateName(Request $request)
    {
        // アカウント名 更新
        DB::beginTransaction();
        try {
            $account = Auth::user()->account;
            $account->name = $request->input('name');
            $account->save();
            DB::commit();
            return response()->json(null, Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function updateFollowLink(Request $request)
    {
        // 友達追加URL 更新
        DB::beginTransaction();
        try {
            $account = Auth::user()->account;
            $account->line_follow_link = $request->input('line_follow_link');
            $account->save();
            DB::commit();
            return response()->json(null, Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function updateProfileImage(Request $request)
    {
        // プロフィール画像 更新
        try {
            $this->validate(
                $request,
                [
                    'file' => 'image|max:3000'
                ]
            );

            $mediaFileService = new MediaFileService();
            $account = Auth::user()->account;

            $mediaFile = $mediaFileService->createImageMedia($request->file('file'));
            $mediaFileService->deleteImageMedia($account->profile_image);
            
            $account->update(['profile_image' => $mediaFile->url]);

            DB::commit();
            return response()->json(null, Response::HTTP_OK);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json($e->validator->getMessageBag()->first(), Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        // DB::beginTransaction();
        // try {
        //     $account = Auth::user()->account;
        //     $account->profile_image = $request->input('line_follow_link');
        //     $account->save();
        //     DB::commit();
        //     return response()->json(null, Response::HTTP_OK);
        // } catch (Exception $e) {
        //     DB::rollBack();
        //     throw $e;
        // }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     * @throws Exception
     */
    public function destroy($id)
    {
        try {
            $authUser = Auth::user();
            /** @var User $customUser */
            $customUser = User::findOrFail($id);
            $customUser->roleUsers()->where('account_id', $authUser->account_id)->delete();
            if ($customUser->roleUsers()->count() === 0) {
                $customUser->delete();
            } else {
                if ($customUser->account_id === $authUser->account_id) {
                    if ($roleUser = $customUser->roleUsers()->first()) {
                        $customUser->account_id = $roleUser->account_id;
                        $customUser->save();
                    }
                }
            }

            DB::commit();
            return response()->json(null, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(null, Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function edit($id)
    {
        $authUser = Auth::user();
        /** @var User $user */
        $user = User::findOrFail($id);
        $roleIds = $user->roleUsers()
            ->where('account_id', $authUser->account_id)
            ->pluck('role_id');
        $friendValues = [];
        $messageValues = [];
        $otherValues = [];
        foreach ($roleIds as $roleId) {
            $role = $roleId - Role::ROLE_FRIEND;
            if ($role > 0 && $role < Role::ROLE_FRIEND) {
                $friendValues[] = $roleId;
                continue;
            }
            $role = $roleId - Role::ROLE_MESSAGE;
            if ($role > 0 && $role < Role::ROLE_FRIEND) {
                $messageValues[] = $roleId;
                continue;
            }
            $role = $roleId - Role::ROLE_OTHER;
            if ($role > 0 && $role < Role::ROLE_FRIEND) {
                $otherValues[] = $roleId;
                continue;
            }
        }

        return response()->json(
            [
                'user' => $user,
                'friendValues' => $friendValues,
                'messageValues' => $messageValues,
                'otherValues' => $otherValues
            ]
        );
    }

    public function userUpdate(Request $request, $id)
    {
        \Log::info('message', ['re' => $request->roles]);
        try {
            DB::beginTransaction();

            $this->validate($request, [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255'
            ]);

            $user = Auth::user();

            $updatedUser = User::findOrFail($id);
            $updatedUser->update([
                'name' => $request->name,
                'email' => $request->email
            ]);

            if (isset($request->password)) {
                $updatedUser->password = bcrypt($request->password);
                $updatedUser->save();
            }

            $updatedUser->roleUsers()
                        ->where('account_id', $user->account_id)
                        ->where('role_id', '!=', Role::ROLE_ACCOUNT_ADMINISTRATOR)
                        ->delete();

            foreach ($request->roles as $roleId) {
                $updatedUser->roleUsers()->create([
                    'role_id' => $roleId,
                    'account_id' => $user->account_id
                ]);
            }

            if ($user->roleUsers()->where('account_id', $user->account_id)->count() === 0) {
                $user->roleUsers()->create([
                    'role_id' => null,
                    'account_id' => $user->account_id
                ]);
            }
            
            DB::commit();
            return response()->json($user, Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @deprecated
     * @return \Illuminate\Http\JsonResponse
     */
    public function line()
    {
        // dd(Auth::user()->lineInfo()->first()->get());
        return response()->json(Auth::user()->lineInfo()->get(), Response::HTTP_OK);
    }

    /**
     * @deprecated
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function link()
    {
        return view(
            'auth.login',
            [
                'randomKey' =>  $this->utils->randomKey(25),
            ]
        );
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function newAccount()
    {
        $webhookurl = env('APP_URL').'/line/bot/callback/'.bin2hex(openssl_random_pseudo_bytes(16));
        return view('new_account', ['webhookurl' => $webhookurl]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function registerAddAccount(Request $request)
    {
        DB::beginTransaction();
        try {
            $this->validate(
                $request,
                [
                    'name' => 'required|string|max:255',
                    'basic_id' => 'required|string|max:255',
                    'channel_id' => 'required|integer',
                    'secret_token' => 'required|string|max:255',
                    'access_token' => 'required|string|max:255',
                    'line_user_id' => 'required|string|max:255',
                ]
            );

            $int_channel_id = (int)$request->channel_id;
            $int_channel_id2 = intval($request->channel_id);
            $account = new Account();
            $account->name = $request->name;
            $account->basic_id = $request->basic_id;
            $account->channel_id = $int_channel_id2;
            $account->channel_secret = $request->secret_token;
            $account->channel_access_token = $request->access_token;
            $account->account_user_id = $request->line_user_id;
            $account->profile_image =  null;
            $account->save();

            $roleUser = new RoleUser;
            $roleUser->role_id = Role::ROLE_ACCOUNT_ADMINISTRATOR;
            $roleUser->account_id = $account->id;
            $roleUser->user_id = Auth::id();
            $roleUser->save();
    
            DB::commit();
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json($e->validator->getMessageBag()->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        header("Access-Control-Allow-Origin: *");  //CORS
        header("Access-Control-Allow-Headers: Origin, X-Requested-With");

        return response()->json(null, Response::HTTP_OK);
    }

    public function registerAddUser(Request $request)
    {
        try {
            DB::beginTransaction();

            $this->validate($request, [
                'name' => 'required|string|max:255|unique:users',
                'email' => 'required|email|string|max:128|unique:users',
                'password' => 'required|string|max:255'
            ]);

            $user = Auth::user();
            $newUser = User::create([
                'name' => $request->name,
                'password' => bcrypt($request->password),
                'admin' => 0,
                'account_id' => Auth::user()->account_id,
                'email' => $request->email,
                'plan_id' => Plan::FREE,
                'register_token' => '1'
            ]);

            if (count($request->roles) > 0) {
                foreach ($request->roles as $role) {
                    $newUser->roleUsers()->create([
                        'role_id' => $role,
                        'account_id' => $newUser->account_id,
                    ]);
                }
            }

            // role_id = null のレコードを作成 （ユーザーの権限を全て外した時のために必要なので）
            $newUser->roleUsers()->create([
                'account_id' => $newUser->account_id,
            ]);

            DB::commit();
            return response()->json(null, Response::HTTP_OK);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json($e->validator->getMessageBag()->first(), Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getRoles(Request $request)
    {
        $roleUser = new RoleUser;
        return $roleUser->get();
    }

    /**
     * @deprecated
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function accountRegisterSuccess()
    {
        return view('new_account_completed');
    }

    /**
     * @deprecated
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function registerNewAccount(Request $request)
    {
        $data = $request->all();
        $validator = $this->newAccountValidator($data);

        $webhook_token = basename($request->webhook_url);
        $user = auth()->user();

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
            $account->plan = $user->account->plan;
            $account->profile_image = env('APP_URL', 'http://localhost:3000').'/img/user-admin.png';
            $account->account_user_id = $request->user_id;
            $account->save();
            return redirect()->to('/newaccountcompleted');
        }

        foreach ($validator->errors()->all() as $error_text) {
            $error_texts[] = $error_text;
        }

        $error = implode('、', $error_texts);
        return redirect()->to('/newaccount')->with('error', __($error));
    }

    /**
     * @deprecated
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function newAccountValidator(array $data)
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
}
