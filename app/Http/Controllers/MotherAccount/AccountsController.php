<?php

namespace App\Http\Controllers\MotherAccount;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Account;
use Carbon\Carbon;
use DB;

class AccountsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("mother_account.accounts");
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
        try {
            DB::beginTransaction();
            $this->validate($request, [
                'name' => 'required|string',
                'basic_id' => 'required|string',
                'channel_id' => 'required|string',
                'account_user_id' => 'required|string',
                'channel_access_token' => 'required|string',
                'channel_secret' => 'required|string',
                'line_follow_link' => 'required|string'
            ]);

            $request['profile_image'] = env('APP_URL', 'http://localhost:3000').'/img/user-admin.png';
            $request['webhook_token'] = env('APP_URL').'/line/bot/callback/'.bin2hex(openssl_random_pseudo_bytes(16));
            $request['bot_dest_id'] = $request->account_user_id;

            $user = Auth::user();
            $roleUsers = $user->roleUsers->first();
            $account = $roleUsers->account()->create($request->all());
            $account->roleUsers()->create([
                'user_id' => $user->id
            ]);
            DB::commit();
            return response()->json(null, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
        try {
            DB::beginTransaction();

            $this->validate($request, [
                'name' => 'required|string',
                'channel_access_token' => 'required|string',
                'channel_secret' => 'required|string',
                'line_follow_link' => 'required|string'
            ]);

            Account::findOrFail($id)->fill($request->all())->save();

            DB::commit();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
        $accountCount = 0;
        foreach ($accounts as $account) {
            // 登録人数
            $account->totalRegisteredUsers = count($account->accountFollowers);
            // ブロック数
            $account->totalBlockedUsers = $account->accountFollowers->filter(function ($follower) {
                return $follower->is_blocked;
            })->count();
            // 有効登録人数
            $account->availableFollowers = $account->totalRegisteredUsers - $account->totalBlockedUsers;
            // 月間新規登録人数
            $account->monthlyRegisteredUsers = $account->accountFollowers->filter(function ($accountFollower) {
                return $accountFollower->timedate_followed >= Carbon::today()->subMonth();
            })->count();
            // クリック数/率
            $account->clickCount = $account->clickrateItems->sum(function ($ci) {
                return $ci->clickrateMessageRecords->sum('access_count');
            });
            if ($account->clickCount === 0) {
                $account->clickRate = 0;
            } else {
                $clickRateMessageCount = $account->clickrateItems->sum(function ($ci) {
                    return $ci->clickrateMessageRecords->sum('send_count');
                });
                $account->clickRate =  round(($account->clickCount / $clickRateMessageCount * 100) - 0.05, 2);
            }
            $accountCount++;
        }

        $userParams = new \stdClass();
        $userParams->canAddNewAccount =  $accountCount < $user->plan->account_count;
        $userParams->accountCount =  $accountCount;
        $userParams->addableAccountCount = $user->plan->account_count;
        $userParams->planName = $user->plan->level;

        $data = [
            'accounts' => $accounts,
            'user' => $userParams
        ];

        return response()->json($data, Response::HTTP_OK);
    }

    public function analysisIndex()
    {
        return view('mother_account.accounts_analysis');
    }

    public function analysisList()
    {
        $user = Auth::user();
        $accountIds = $user->roleUsers()->distinct()->pluck('account_id');
        $accounts = Account::whereIn('id', $accountIds)->get();

        // 登録者数と登録者推移
        foreach ($accounts as $account) {
            $accountFollowers = $account->accountFollowers;
            $account->all_followers_count = count($accountFollowers);
            $account->monthly_followers = $accountFollowers->filter(function ($accountFollower) {
                return $accountFollower->timedate_followed >= Carbon::today()->subMonth();
            })->count();
        }

        $summaryData = new \stdClass();
        $summaryData->dayData = $this->registeredFollowersPerDay($accounts);
        $summaryData->weekData = $this->registeredFollowersPerWeek($accounts);
        $summaryData->monthData = $this->registeredFollowersPerMonth($accounts);
        $summaryData->yearData = $this->registeredFollowersPerYear($accounts);

        // 配信可能通数
        $scenarioDeliveries = collect($accounts)->flatMap->scenarios->flatMap->scenarioMessages->flatMap->deliveries
            ->filter(function ($scenario, $key) {
                return $scenario->is_sent == 1;
            });

        $magazineDeliveries = collect($accounts)->flatMap->magazines->flatMap->magazineDeliveries
            ->filter(function ($magazine, $key) {
                return $magazine->is_sent == 1;
            });

        $plan = $user->plan;

        $plan->deliveries_left = $plan->delivery_count - (
            count($scenarioDeliveries) + count($magazineDeliveries)
        );

        // 登録者推移

        $data = [
            'plan' => $plan,
            'accounts' => $accounts,
            'summaryData' => $summaryData
        ];

        return response()->json($data, Response::HTTP_OK);
    }

    private function registeredFollowersPerDay($accounts)
    {
        $index = 0;
        $allAccountsRegisteredFollowers = [];
        foreach ($accounts as $account) {
            $accountFollowers = $account->accountFollowers;
            $lastWeekRegisteredAccountFollowersObj = $accountFollowers->filter(function ($accountFollower, $key) {
                return $accountFollower->timedate_followed >= Carbon::today()->subWeek();
            });

            $lastWeekRegisteredAccountFollowers = array_values($lastWeekRegisteredAccountFollowersObj->toArray());

            $today = new Carbon();
            $oneWeek = 6;

            $weekData = [];
            for ($i = $oneWeek; $i > -1; $i--) {
                $currentDay = $today->copy()->subDay($i);

                $registeredFollowersPerDay = [];
                for ($j = 0; $j < count($lastWeekRegisteredAccountFollowers); $j++) {
                    if (isset($lastWeekRegisteredAccountFollowers[$j]['timedate_followed'])) {
                        if (Carbon::parse($lastWeekRegisteredAccountFollowers[$j]['timedate_followed'])->format('Y-m-d') == Carbon::parse($currentDay)->format('Y-m-d')) {
                            $registeredFollowersPerDay[] = $lastWeekRegisteredAccountFollowers[$j];
                        }
                    }
                }

                $weekData[$index][] = [
                    'day' => $currentDay,
                    'registeredFollowers' => count($registeredFollowersPerDay)
                ];
            }
            $allAccountsRegisteredFollowers[] = $weekData[$index++];
        }

        $data = array();

        foreach ($allAccountsRegisteredFollowers as $accountIndex) {
            for ($i = 0; $i < 7; $i++) {
                if (!isset($data[$i]['day'])) {
                    $data[$i]['day'] = $accountIndex[$i]['day'];
                }
                $data[$i]['registeredFollowers'] = isset($data[$i]['registeredFollowers']) ?
                    $data[$i]['registeredFollowers'] + $accountIndex[$i]['registeredFollowers'] :
                    $accountIndex[$i]['registeredFollowers'];
            }
        }

        return $data;
    }

    private function registeredFollowersPerWeek($accounts)
    {
        $index = 0;
        $allAccountsRegisteredFollowers = [];
        foreach ($accounts as $account) {
            $accountFollowers = $account->accountFollowers;
            $lastSevenWeeksRegisteredFollowersObj = $accountFollowers->filter(function ($accountFollower, $key) {
                return $accountFollower->timedate_followed >= Carbon::today()->subWeek(6);
            });

            $lastSevenWeeksRegisteredFollowers = array_values($lastSevenWeeksRegisteredFollowersObj->toArray());

            $today = new Carbon();

            $sevenWeeksData = [];
            for ($i = 6; $i > -1; $i--) {
                $currentWeek = $today->copy()->subWeek($i);

                $registeredFollowersPerWeek = [];
                for ($j = 0; $j < count($lastSevenWeeksRegisteredFollowers); $j++) {
                    if (isset($lastSevenWeeksRegisteredFollowers[$j])) {
                        if ($lastSevenWeeksRegisteredFollowers[$j]['timedate_followed'] <= $currentWeek &&
                            $lastSevenWeeksRegisteredFollowers[$j]['timedate_followed'] >= $currentWeek->copy()->subWeek(1)) {
                            $registeredFollowersPerWeek[] = $lastSevenWeeksRegisteredFollowers[$j];
                        }
                    }
                }

                $sevenWeeksData[$index][] = [
                    'week' => $currentWeek,
                    'registeredFollowers' => count($registeredFollowersPerWeek)
                ];
            }
            $allAccountsRegisteredFollowers[] = $sevenWeeksData[$index++];
        }

        $data = array();

        foreach ($allAccountsRegisteredFollowers as $accountIndex) {
            for ($i = 0; $i < 7; $i++) {
                if (!isset($data[$i]['week'])) {
                    $data[$i]['week'] = $accountIndex[$i]['week'];
                }
                $data[$i]['registeredFollowers'] = isset($data[$i]['registeredFollowers']) ?
                    $data[$i]['registeredFollowers'] + $accountIndex[$i]['registeredFollowers'] :
                    $accountIndex[$i]['registeredFollowers'];
            }
        }

        return $data;
    }

    private function registeredFollowersPerMonth($accounts)
    {
        $index = 0;
        $allAccountsRegisteredFollowers = [];
        foreach ($accounts as $account) {
            $accountFollowers = $account->accountFollowers;
            $lastSevenMonthsRegisteredFollowersObj = $accountFollowers->filter(function ($accountFollower, $key) {
                return $accountFollower->timedate_followed >= Carbon::today()->subMonth(6);
            });

            $lastSevenMonthsRegisteredFollowers = array_values($lastSevenMonthsRegisteredFollowersObj->toArray());

            $today = new Carbon();

            $sevenMonthsData = [];
            for ($i = 6; $i > -1; $i--) {
                $currentMonth = $today->copy()->subMonth($i);

                $registeredFollowersPerMonth = [];
                for ($j = 0; $j < count($lastSevenMonthsRegisteredFollowers); $j++) {
                    if (isset($lastSevenMonthsRegisteredFollowers[$j])) {
                        if ($lastSevenMonthsRegisteredFollowers[$j]['timedate_followed'] <= $currentMonth &&
                            $lastSevenMonthsRegisteredFollowers[$j]['timedate_followed'] >= $currentMonth->copy()->subMonth(1)) {
                            $registeredFollowersPerMonth[] = $lastSevenMonthsRegisteredFollowers[$j];
                        }
                    }
                }

                $sevenMonthsData[$index][] = [
                    'month' => $currentMonth,
                    'registeredFollowers' => count($registeredFollowersPerMonth)
                ];
            }
            $allAccountsRegisteredFollowers[] = $sevenMonthsData[$index++];
        }

        $data = array();

        foreach ($allAccountsRegisteredFollowers as $accountIndex) {
            for ($i = 0; $i < 7; $i++) {
                if (!isset($data[$i]['month'])) {
                    $data[$i]['month'] = $accountIndex[$i]['month'];
                }
                $data[$i]['registeredFollowers'] = isset($data[$i]['registeredFollowers']) ?
                    $data[$i]['registeredFollowers'] + $accountIndex[$i]['registeredFollowers'] :
                    $accountIndex[$i]['registeredFollowers'];
            }
        }

        return $data;
    }

    private function registeredFollowersPerYear($accounts)
    {
        $index = 0;
        $allAccountsRegisteredFollowers = [];
        foreach ($accounts as $account) {
            $accountFollowers = $account->accountFollowers;
            $lastSevenYearsRegisteredFollowersObj = $accountFollowers->filter(function ($accountFollower, $key) {
                return $accountFollower->timedate_followed >= Carbon::today()->subYear(6);
            });

            $lastSevenYearsRegisteredFollowers = array_values($lastSevenYearsRegisteredFollowersObj->toArray());

            $today = new Carbon();

            $sevenYearsData = [];
            for ($i = 6; $i > -1; $i--) {
                $currentYear = $today->copy()->subYear($i);

                $registeredFollowersPerYear = [];
                for ($j = 0; $j < count($lastSevenYearsRegisteredFollowers); $j++) {
                    if (isset($lastSevenYearsRegisteredFollowers[$j])) {
                        if ($lastSevenYearsRegisteredFollowers[$j]['timedate_followed'] <= $currentYear &&
                            $lastSevenYearsRegisteredFollowers[$j]['timedate_followed'] >= $currentYear->copy()->subYear(1)) {
                            $registeredFollowersPerYear[] = $lastSevenYearsRegisteredFollowers[$j];
                        }
                    }
                }

                $sevenYearsData[$index][] = [
                    'year' => $currentYear,
                    'registeredFollowers' => count($registeredFollowersPerYear)
                ];
            }
            $allAccountsRegisteredFollowers[] = $sevenYearsData[$index++];
        }

        $data = array();

        foreach ($allAccountsRegisteredFollowers as $accountIndex) {
            for ($i = 0; $i < 7; $i++) {
                if (!isset($data[$i]['year'])) {
                    $data[$i]['year'] = $accountIndex[$i]['year'];
                }
                $data[$i]['registeredFollowers'] = isset($data[$i]['registeredFollowers']) ?
                    $data[$i]['registeredFollowers'] + $accountIndex[$i]['registeredFollowers'] :
                    $accountIndex[$i]['registeredFollowers'];
            }
        }

        return $data;
    }
}
