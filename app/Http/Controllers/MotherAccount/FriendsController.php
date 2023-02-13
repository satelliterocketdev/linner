<?php

namespace App\Http\Controllers\MotherAccount;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Account;
use App\AccountFollower;
use DB;

class FriendsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('mother_account.friends');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function list()
    {
        $user = Auth::user();
        $accountIds = $user->roleUsers()->pluck('account_id');
        $accounts = Account::whereIn('id', $accountIds)->get();

        $accountFollowers = collect($accounts)->flatMap->accountFollowers
            ->groupBy('source_user_id')->flatMap(function ($followers) {
                $accountIds = $followers->pluck('account_id');
                return $followers->map(function ($follower) use ($accountIds) {
                    $pfUser = $follower->pfUsers;
                    if ($pfUser) {
                        $follower->pf_user_id = $pfUser->id;
                        $follower->pf_user_picture = $pfUser->picture;
                        $follower->pf_user_display_name = $pfUser->display_name;
                        $follower->conversion_title = 'TODO:Conv'.$pfUser->id;
                        $follower->accountIds = $accountIds;
                    }
                    return $follower;
                });
            });

        $uniquifiedFollowers = $accountFollowers->unique('source_user_id');

        foreach ($uniquifiedFollowers as $accountFollower) {
            $accountFollower->accounts = $accounts->whereIn('id', $accountFollower->accountIds)->pluck('name');
        }

        return response()->json($uniquifiedFollowers->values()->all(), Response::HTTP_OK);
    }

    public function changeVisibility(Request $request)
    {
        try {
            DB::beginTransaction();

            $account = Auth::user()->account;
            $followerIds = $request->followers_ids;
            $followers = AccountFollower::whereIn('id', $followerIds)->update(['is_visible' => $request->visibility]);

            DB::commit();
            return response()->json(null, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
