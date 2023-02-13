<?php

namespace App\Http\Controllers;

use App\Role;
use App\RoleUser;
use App\Settlement;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Account;
use Carbon\Carbon;
use App\Magazine;
use App\ScenarioMessage;

class DashboardController extends Controller
{
    private $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if ($this->user->hasRole(Role::ROLE_ACCOUNT_ADMINISTRATOR, $this->user->account_id)) { //管理者IDの場合の制限
            //メール送信後 メール確認が済んでいない
            if ($this->user->register_token != 1) {
                return redirect()->to('/logout');
            }

            //メール認証後 プラン選択されていない
            if ($this->user->plan_id == null) {
            // if (is_null($user->plan_id) && !RoleUser::where('user_id', $user->id)->exists()) {
                return redirect()->to('/plan');
            }

            //最新の決済情報取得
            $newSettlement = Settlement::MyNewSettlement();

            //プラン選択後 決済終わっていない
            if ($this->user->plan->price !== 0) {
                if (is_null($this->user->settlement_id) || $newSettlement->status === Settlement::STATUS_UNSETTLED) {
                    return redirect()->to('/paymentmethod');
                }
            }

            if ($this->user->account_id == null) {
                return redirect()->to('/newaccount');
            }
        }

        return view('dashboard');
    }

    public function list()
    {
        $accountIds = $this->user->roleUsers()->distinct()->pluck('account_id');
        $accounts = Account::whereIn('id', $accountIds)->get();

        // 配信データの取得
        $scenarioDeliveries = collect($accounts)->flatMap->scenarios->flatMap->scenarioMessages->flatMap->deliveries;
        $magazineDeliveries = collect($accounts)->flatMap->magazines->flatMap->magazineDeliveries;

        // 送信済みのデータのみ取得
        $sentScenarios = $scenarioDeliveries->filter(function ($scenario, $key) {
            return $scenario->is_sent == 1;
        });

        $sentMagazines = $magazineDeliveries->filter(function ($magazine, $key) {
            return $magazine->is_sent == 1;
        });

        // 選択中のプランを取得
        $plan = $this->user->plan;

        // 残通数を計算
        $remainingDeliveries = $plan->delivery_count - (count($sentScenarios) + count($sentMagazines));

        $plan['remaining_deliveries'] = $remainingDeliveries;

        // フォロワーの指定日時までの合計人数を集計する
        $summaryData = new \stdClass();
        $summaryData->dayData = $this->registeredFollowersPerDay();
        $summaryData->weekData = $this->registeredFollowersPerWeek();
        $summaryData->monthData = $this->registeredFollowersPerMonth();
        $summaryData->yearData = $this->registeredFollowersPerYear();
        $summaryData->totalRegisteredFollowers = count($this->user->account->accountFollowers);

        //　配信数
        $sentDeliveries = new \stdClass();

        // 全てのdeliveriesの取得
        $magazines = $this->user->account->magazines;
        $magazineDelivieries = collect($magazines)->flatMap->magazineDeliveries;
        $scenarioMessages = collect($this->user->account->scenarios)->flatMap->scenarioMessages;
        $scenarioDeliveries = collect($scenarioMessages)->flatMap->deliveries;

        // 送信済みのdeliveriesの取得
        $sentDeliveries = new \stdClass();
        $sentMagazinesDeliveries = $this->sentDeliveries($magazineDelivieries);
        $sentScenariosDeliveries = $this->sentDeliveries($scenarioDeliveries);
        $sentAccountMessages = $this->user->account->accountMessages;

        $sentDeliveries->dayData = $this->sentDeliveriesPerDay($sentMagazinesDeliveries, $sentScenariosDeliveries, $sentAccountMessages);
        $sentDeliveries->weekData = $this->sentDeliveriesPerWeek($sentMagazinesDeliveries, $sentScenariosDeliveries, $sentAccountMessages);
        $sentDeliveries->monthData = $this->sentDeliveriesPerMonth($sentMagazinesDeliveries, $sentScenariosDeliveries, $sentAccountMessages);
        $sentDeliveries->yearData = $this->sentDeliveriesPerYear($sentMagazinesDeliveries, $sentScenariosDeliveries, $sentAccountMessages);

        //　ブロックされたユーザー集計のデータの取得
        $blockedUsers =  new \stdClass();
        $blockedUsers->dayData = $this->blockedUsersPerDay();
        $blockedUsers->weekData = $this->blockedUsersPerWeek();
        $blockedUsers->monthData = $this->blockedUsersPerMonth();
        $blockedUsers->yearData = $this->blockedUsersPerYear();
        $blockedUsers->totalBlockedUsers = $this->user->account->accountFollowers->filter(function ($follower) {
            return $follower->is_blocked;
        })->count();

        //　アクティブユーザーの取得
        $activities = new \stdClass();
        $activities->dayData = $this->activitiesPerDay();
        $activities->weekData = $this->activitiesPerWeek();
        $activities->monthData = $this->activitiesPerMonth();
        $activities->yearData = $this->activitiesPerYear();
        // TODO
        /*
            URLクリック（可能ならメッセージ開封）
            カルーセルクリック
            アンケート回答
        */
        $activities->totalActivities = count($this->user->account->accountMessages);

        //アカウント管理者権限フラグ
        $isAdmin = $this->user->hasRole(Role::ROLE_ACCOUNT_ADMINISTRATOR, $this->user->account_id);

        $data = [
            'plan' => $plan,
            'summaryData' => $summaryData,
            'sentDeliveries' => $sentDeliveries,
            'blockedUsers' => $blockedUsers,
            'activityCount' => $activities,
            'isAdmin'=> $isAdmin
        ];

        return response()->json($data, Response::HTTP_OK);
    }

    private function sentDeliveries($deliveries)
    {
        return $deliveries->filter(function ($delivery, $key) {
            return $delivery->is_sent == 1;
        });
    }

    private function sentDeliveriesPerDay($magazineDeliveries, $scenarioDeliveries, $accountMessages)
    {
        // magazine, scneario, accountmessageのscheduleのカラム名が全部同じだったらこんな長いコードにならなかっただろうに
        $sentMagazinesIds = $magazineDeliveries->pluck('magazine_id');
        $sentMagazines = Magazine::findMany($sentMagazinesIds);

        $sentScenarioMessagesIds = $scenarioDeliveries->pluck('scenario_message_id');
        $sentScenariosMessages = ScenarioMessage::findMany($sentScenarioMessagesIds);

        $lastWeekSentMagazinesObj = $sentMagazines->filter(function ($sentMagazine, $key) {
            return $sentMagazine->schedule_at >= Carbon::today()->subWeek();
        });

        $lastWeekSentMagazines = array_values($lastWeekSentMagazinesObj->toArray());

        $lastWeekSentScenarioMessagesObj = $sentScenariosMessages->filter(function ($sentScenarioMessage, $key) {
            return $sentScenarioMessage->schedule_date >= Carbon::today()->subWeek();
        });

        $lastWeekSentScenarioMessages = array_values($lastWeekSentScenarioMessagesObj->toArray());

        $lastWeekSentAccountMessagesObj = $accountMessages->filter(function ($accountMessage, $key) {
            return $accountMessage->created_at >= Carbon::today()->subWeek();
        });

        $lastWeekSentAccountMessages = array_values($lastWeekSentAccountMessagesObj->toArray());

        $today = new Carbon();
        $oneWeek = 6;

        $weekData = [];
        for ($i = $oneWeek; $i > -1; $i--) {
            $currentDay = $today->copy()->subDay($i);

            $sentDeliveriesPerDay = [];
            for ($j = 0; $j < count($lastWeekSentMagazines); $j++) {
                if (isset($lastWeekSentMagazines[$j]['schedule_at'])) {
                    if (Carbon::parse($lastWeekSentMagazines[$j]['schedule_at'])->format('Y-m-d') == Carbon::parse($currentDay)->format('Y-m-d')) {
                        $sentDeliveriesPerDay[] = $lastWeekSentMagazines[$j];
                    }
                }
            }

            for ($j = 0; $j < count($lastWeekSentScenarioMessages); $j++) {
                if (isset($lastWeekSentScenarioMessages[$j]['schedule_date'])) {
                    if (Carbon::parse($lastWeekSentScenarioMessages[$j]['schedule_date'])->format('Y-m-d') == Carbon::parse($currentDay)->format('Y-m-d')) {
                        $sentDeliveriesPerDay[] = $lastWeekSentScenarioMessages[$j];
                    }
                }
            }

            for ($j = 0; $j < count($lastWeekSentAccountMessages); $j++) {
                if (isset($lastWeekSentAccountMessages[$j]['created_at'])) {
                    if (Carbon::parse($lastWeekSentAccountMessages[$j]['created_at'])->format('Y-m-d') == Carbon::parse($currentDay)->format('Y-m-d')) {
                        $sentDeliveriesPerDay[] = $lastWeekSentAccountMessages[$j];
                    }
                }
            }

            $weekData[] = [
                'day' => $currentDay,
                'sentDeliveries' => count($sentDeliveriesPerDay)
            ];
        }

        return $weekData;
    }

    private function sentDeliveriesPerWeek($magazineDeliveries, $scenarioDeliveries, $accountMessages)
    {
        // magazine, scneario, accountmessageのscheduleのカラム名が全部同じだったらこんな長いコードにならなかっただろうに
        $sentMagazinesIds = $magazineDeliveries->pluck('magazine_id');
        $sentMagazines = Magazine::findMany($sentMagazinesIds);

        $sentScenarioMessagesIds = $scenarioDeliveries->pluck('scenario_message_id');
        $sentScenariosMessages = ScenarioMessage::findMany($sentScenarioMessagesIds);

        $lastSixWeeksSentMagazinesObj = $sentMagazines->filter(function ($sentMagazine, $key) {
            return $sentMagazine->schedule_at >= Carbon::today()->subWeek(6);
        });

        $lastSixWeeksSentMagazines = array_values($lastSixWeeksSentMagazinesObj->toArray());

        $lastSixWeeksSentScenarioMessagesObj = $sentScenariosMessages->filter(function ($sentScenarioMessage, $key) {
            return $sentScenarioMessage->schedule_date >= Carbon::today()->subWeek(6);
        });

        $lastSixWeeksSentScenarioMessages = array_values($lastSixWeeksSentScenarioMessagesObj->toArray());

        $lastSixWeeksSentAccountMessagesObj = $accountMessages->filter(function ($accountMessage, $key) {
            return $accountMessage->created_at >= Carbon::today()->subWeek(6);
        });

        $lastSixWeeksSentAccountMessages = array_values($lastSixWeeksSentAccountMessagesObj->toArray());

        $today = new Carbon();

        $weekData = [];
        for ($i = 6; $i > -1; $i--) {
            $currentWeek = $today->copy()->subWeek($i);

            $sentDeliveriesPerWeek = [];
            for ($j = 0; $j < count($lastSixWeeksSentMagazines); $j++) {
                if (isset($lastSixWeeksSentMagazines[$j]['schedule_at'])) {
                    if ($lastSixWeeksSentMagazines[$j]['schedule_at'] <= $currentWeek &&
                        $lastSixWeeksSentMagazines[$j]['schedule_at'] >= $currentWeek->copy()->subWeek(1)) {
                        $sentDeliveriesPerWeek[] = $lastSixWeeksSentMagazines[$j];
                    }
                }
            }

            for ($j = 0; $j < count($lastSixWeeksSentScenarioMessages); $j++) {
                if (isset($lastSixWeeksSentScenarioMessages[$j]['schedule_date'])) {
                    if ($lastSixWeeksSentScenarioMessages[$j]['schedule_date'] <= $currentWeek &&
                        $lastSixWeeksSentScenarioMessages[$j]['schedule_date'] >= $currentWeek->copy()->subWeek(1)) {
                        $sentDeliveriesPerWeek[] = $lastSixWeeksSentScenarioMessages[$j];
                    }
                }
            }

            for ($j = 0; $j < count($lastSixWeeksSentAccountMessages); $j++) {
                if (isset($lastSixWeeksSentAccountMessages[$j]['created_at'])) {
                    if ($lastSixWeeksSentAccountMessages[$j]['created_at'] <= $currentWeek &&
                        $lastSixWeeksSentAccountMessages[$j]['created_at'] >= $currentWeek->copy()->subWeek(1)) {
                        $sentDeliveriesPerWeek[] = $lastSixWeeksSentAccountMessages[$j];
                    }
                }
            }

            $weekData[] = [
                'week' => $currentWeek,
                'sentDeliveries' => count($sentDeliveriesPerWeek)
            ];
        }

        return $weekData;
    }

    private function sentDeliveriesPerMonth($magazineDeliveries, $scenarioDeliveries, $accountMessages)
    {
        // magazine, scneario, accountmessageのscheduleのカラム名が全部同じだったらこんな長いコードにならなかっただろうに
        $sentMagazinesIds = $magazineDeliveries->pluck('magazine_id');
        $sentMagazines = Magazine::findMany($sentMagazinesIds);

        $sentScenarioMessagesIds = $scenarioDeliveries->pluck('scenario_message_id');
        $sentScenariosMessages = ScenarioMessage::findMany($sentScenarioMessagesIds);

        $lastSixMonthsSentMagazinesObj = $sentMagazines->filter(function ($sentMagazine, $key) {
            return $sentMagazine->schedule_at >= Carbon::today()->subMonth(6);
        });

        $lastSixMonthsSentMagazines = array_values($lastSixMonthsSentMagazinesObj->toArray());

        $lastSixMonthsSentScenarioMessagesObj = $sentScenariosMessages->filter(function ($sentScenarioMessage, $key) {
            return $sentScenarioMessage->schedule_date >= Carbon::today()->subMonth(6);
        });

        $lastSixMonthsSentScenarioMessages = array_values($lastSixMonthsSentScenarioMessagesObj->toArray());

        $lastSixMonthsSentAccountMessagesObj = $accountMessages->filter(function ($accountMessage, $key) {
            return $accountMessage->created_at >= Carbon::today()->subMonth(6);
        });

        $lastSixMonthsSentAccountMessages = array_values($lastSixMonthsSentAccountMessagesObj->toArray());

        $today = new Carbon();

        $monthData = [];
        for ($i = 6; $i > -1; $i--) {
            $currentMonth = $today->copy()->subMonth($i);

            $sentDeliveriesPerMonth = [];
            for ($j = 0; $j < count($lastSixMonthsSentMagazines); $j++) {
                if (isset($lastSixMonthsSentMagazines[$j]['schedule_at'])) {
                    if ($lastSixMonthsSentMagazines[$j]['schedule_at'] <= $currentMonth &&
                        $lastSixMonthsSentMagazines[$j]['schedule_at'] >= $currentMonth->copy()->subMonth(1)) {
                        $sentDeliveriesPerMonth[] = $lastSixMonthsSentMagazines[$j];
                    }
                }
            }

            for ($j = 0; $j < count($lastSixMonthsSentScenarioMessages); $j++) {
                if (isset($lastSixMonthsSentScenarioMessages[$j]['schedule_date'])) {
                    if ($lastSixMonthsSentScenarioMessages[$j]['schedule_date'] <= $currentMonth &&
                        $lastSixMonthsSentScenarioMessages[$j]['schedule_date'] >= $currentMonth->copy()->subMonth(1)) {
                        $sentDeliveriesPerMonth[] = $lastSixMonthsSentScenarioMessages[$j];
                    }
                }
            }

            for ($j = 0; $j < count($lastSixMonthsSentAccountMessages); $j++) {
                if (isset($lastSixMonthsSentAccountMessages[$j]['created_at'])) {
                    if ($lastSixMonthsSentAccountMessages[$j]['created_at']<= $currentMonth &&
                        $lastSixMonthsSentAccountMessages[$j]['created_at'] >= $currentMonth->copy()->subMonth(1)) {
                        $sentDeliveriesPerMonth[] = $lastSixMonthsSentAccountMessages[$j];
                    }
                }
            }

            $monthData[] = [
                'month' => $currentMonth,
                'sentDeliveries' => count($sentDeliveriesPerMonth)
            ];
        }

        return $monthData;
    }

    private function sentDeliveriesPerYear($magazineDeliveries, $scenarioDeliveries, $accountMessages)
    {
        // magazine, scneario, accountmessageのscheduleのカラム名が全部同じだったらこんな長いコードにならなかっただろうに
        $sentMagazinesIds = $magazineDeliveries->pluck('magazine_id');
        $sentMagazines = Magazine::findMany($sentMagazinesIds);

        $sentScenarioMessagesIds = $scenarioDeliveries->pluck('scenario_message_id');
        $sentScenariosMessages = ScenarioMessage::findMany($sentScenarioMessagesIds);

        $lastSixYearsSentMagazinesObj = $sentMagazines->filter(function ($sentMagazine, $key) {
            return $sentMagazine->schedule_at >= Carbon::today()->subYear(6);
        });

        $lastSixYearsSentMagazines = array_values($lastSixYearsSentMagazinesObj->toArray());

        $lastSixYearsSentScenarioMessagesObj = $sentScenariosMessages->filter(function ($sentScenarioMessage, $key) {
            return $sentScenarioMessage->schedule_date >= Carbon::today()->subYear(6);
        });

        $lastSixYearsSentScenarioMessages = array_values($lastSixYearsSentScenarioMessagesObj->toArray());

        $lastSixYearsSentAccountMessagesObj = $accountMessages->filter(function ($accountMessage, $key) {
            return $accountMessage->created_at >= Carbon::today()->subYear(6);
        });

        $lastSixYearsSentAccountMessages = array_values($lastSixYearsSentAccountMessagesObj->toArray());

        $today = new Carbon();

        $yearData = [];
        for ($i = 6; $i > -1; $i--) {
            $currentYear = $today->copy()->subYear($i);

            $sentDeliveriesPerYear = [];
            for ($j = 0; $j < count($lastSixYearsSentMagazines); $j++) {
                if (isset($lastSixYearsSentMagazines[$j]['schedule_at'])) {
                    if ($lastSixYearsSentMagazines[$j]['schedule_at'] <= $currentYear &&
                        $lastSixYearsSentMagazines[$j]['schedule_at'] >= $currentYear->copy()->subYear(1)) {
                        $sentDeliveriesPerYear[] = $lastSixYearsSentMagazines[$j];
                    }
                }
            }

            for ($j = 0; $j < count($lastSixYearsSentScenarioMessages); $j++) {
                if (isset($lastSixYearsSentScenarioMessages[$j]['schedule_date'])) {
                    if ($lastSixYearsSentScenarioMessages[$j]['schedule_date'] <= $currentYear &&
                        $lastSixYearsSentScenarioMessages[$j]['schedule_date'] >= $currentYear->copy()->subYear(1)) {
                        $sentDeliveriesPerYear[] = $lastSixYearsSentScenarioMessages[$j];
                    }
                }
            }

            for ($j = 0; $j < count($lastSixYearsSentAccountMessages); $j++) {
                if (isset($lastSixYearsSentAccountMessages[$j]['created_at'])) {
                    if ($lastSixYearsSentAccountMessages[$j]['created_at'] <= $currentYear &&
                        $lastSixYearsSentAccountMessages[$j]['created_at'] >= $currentYear->copy()->subYear(1)) {
                        $sentDeliveriesPerYear[] = $lastSixYearsSentAccountMessages[$j];
                    }
                }
            }

            $yearData[] = [
                'year' => $currentYear,
                'sentDeliveries' => count($sentDeliveriesPerYear)
            ];
        }

        return $yearData;
    }

    private function registeredFollowersPerDay()
    {
        $accountFollowers = $this->user->account->accountFollowers;
        $lastWeekRegisteredAccountFollowersObj = $accountFollowers->filter(function ($accountFollower, $key) {
            // １週間前から今日までの登録人数を取得している...はず
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

            $weekData[] = [
                'day' => $currentDay,
                'registeredFollowers' => count($registeredFollowersPerDay)
            ];
        }

        return $weekData;
    }

    private function registeredFollowersPerWeek()
    {
        $accountFollowers = $this->user->account->accountFollowers;
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

            $sevenWeeksData[] = [
                'week' => $currentWeek,
                'registeredFollowers' => count($registeredFollowersPerWeek)
            ];
        }

        return $sevenWeeksData;
    }

    private function registeredFollowersPerMonth()
    {
        $accountFollowers = $this->user->account->accountFollowers;
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

            $sevenMonthsData[] = [
                'month' => $currentMonth,
                'registeredFollowers' => count($registeredFollowersPerMonth)
            ];
        }

        return $sevenMonthsData;
    }

    private function registeredFollowersPerYear()
    {
        $accountFollowers = $this->user->account->accountFollowers;
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

            $sevenYearsData[] = [
                'year' => $currentYear,
                'registeredFollowers' => count($registeredFollowersPerYear)
            ];
        }

        return $sevenYearsData;
    }

    private function blockedUsersPerDay()
    {
        $accountFollowers = $this->user->account->accountFollowers;
        $lastWeekBlockedUsers = $accountFollowers->filter(function ($accountFollower, $key) {
            return $accountFollower->blocked_date >= Carbon::today()->subWeek();
        });
        
        $lastSevenDaysBlockedUsers = array_values($lastWeekBlockedUsers->toArray());

        $today = new Carbon();
        $oneWeek = 6;

        $weekData = [];
        for ($i = $oneWeek; $i > -1; $i--) {
            $currentDay = $today->copy()->subDay($i);

            $blockeduserPerDay = [];
            for ($j = 0; $j < count($lastSevenDaysBlockedUsers); $j++) {
                if (isset($lastSevenDaysBlockedUsers[$j]['blocked_date'])) {
                    if ($currentDay->isSameDay(Carbon::parse($lastSevenDaysBlockedUsers[$j]['blocked_date']))) {
                        $blockeduserPerDay[] = $lastSevenDaysBlockedUsers[$j];
                    }
                }
            }

            $weekData[] = [
                'day' => $currentDay,
                'blockedUsers' => count($blockeduserPerDay)
            ];
        }

        return $weekData;
    }

    private function blockedUsersPerWeek()
    {
        $accountFollowers = $this->user->account->accountFollowers;
        $lastWeekBlockedUsersObj = $accountFollowers->filter(function ($accountFollower, $key) {
            return $accountFollower->blocked_date >= Carbon::today()->subWeek(6);
        });
        
        $lastWeekBlockedUsers = array_values($lastWeekBlockedUsersObj->toArray());

        $today = new Carbon();

        $weekData = [];
        for ($i = 6; $i > -1; $i--) {
            $currentWeek = $today->copy()->subWeek($i);

            $blockedUsersPerWeek = [];
            for ($j = 0; $j < count($lastWeekBlockedUsers); $j++) {
                if (isset($lastWeekBlockedUsers[$j])) {
                    if ($lastWeekBlockedUsers[$j]['blocked_date'] <= $currentWeek &&
                        $lastWeekBlockedUsers[$j]['blocked_date'] >= $currentWeek->copy()->subWeek(1)) {
                        $blockedUsersPerWeek[] = $lastWeekBlockedUsers[$j];
                    }
                }
            }

            $weekData[] = [
                'week' => $currentWeek,
                'blockedUsers' => count($blockedUsersPerWeek)
            ];
        }

        return $weekData;
    }

    private function blockedUsersPerMonth()
    {
        $accountFollowers = $this->user->account->accountFollowers;
        $lastSevenMonthsBlockedUsersObj = $accountFollowers->filter(function ($accountFollower, $key) {
            return $accountFollower->blocked_date >= Carbon::today()->subMonth(6);
        });

        $lastSevenMonthsBlockedUsers = array_values($lastSevenMonthsBlockedUsersObj->toArray());

        $today = new Carbon();

        $sevenMonthsData = [];
        for ($i = 6; $i > -1; $i--) {
            $currentMonth = $today->copy()->subMonth($i);

            $blockedusersPerMonth = [];
            for ($j = 0; $j < count($lastSevenMonthsBlockedUsers); $j++) {
                if (isset($lastSevenMonthsBlockedUsers[$j])) {
                    if ($lastSevenMonthsBlockedUsers[$j]['blocked_date'] <= $currentMonth &&
                        $lastSevenMonthsBlockedUsers[$j]['blocked_date'] >= $currentMonth->copy()->subMonth(1)) {
                        $blockedusersPerMonth[] = $lastSevenMonthsBlockedUsers[$j];
                    }
                }
            }

            $sevenMonthsData[] = [
                'month' => $currentMonth,
                'blockedUsers' => count($blockedusersPerMonth)
            ];
        }

        return $sevenMonthsData;
    }

    private function blockedUsersPerYear()
    {
        $accountFollowers = $this->user->account->accountFollowers;
        $lastSevenYearsBlockedUsersObj = $accountFollowers->filter(function ($accountFollower, $key) {
            return $accountFollower->blocked_date >= Carbon::today()->subYear(6);
        });

        $lastSevenYearsBlockedUsers = array_values($lastSevenYearsBlockedUsersObj->toArray());

        $today = new Carbon();

        $sevenYearsData = [];
        for ($i = 6; $i > -1; $i--) {
            $currentYear = $today->copy()->subYear($i);

            $blockedUsersPerYear = [];
            for ($j = 0; $j < count($lastSevenYearsBlockedUsers); $j++) {
                if (isset($lastSevenYearsBlockedUsers[$j])) {
                    if ($lastSevenYearsBlockedUsers[$j]['blocked_date'] <= $currentYear &&
                        $lastSevenYearsBlockedUsers[$j]['blocked_date'] >= $currentYear->copy()->subYear(1)) {
                        $blockedUsersPerYear[] = $lastSevenYearsBlockedUsers[$j];
                    }
                }
            }

            $sevenYearsData[] = [
                'year' => $currentYear,
                'blockedUsers' => count($blockedUsersPerYear)
            ];
        }

        return $sevenYearsData;
    }

    private function activitiesPerDay()
    {
        // TODO
        /*
            URLクリック（可能ならメッセージ開封）
            カルーセルクリック
            アンケート回答
        */
        $account = $this->user->account;
        $lastSevenDaysReceivedMsgObj = $account->accountMessages->filter(function ($accountMessage) {
            // created_atを使ってもいいかな
            return Carbon::createFromTimestamp($accountMessage->timestamp / 1000)->toFormattedDateString() >= Carbon::today()->subWeek();
        });

        $lastSevenDaysReceivedMsg = array_values($lastSevenDaysReceivedMsgObj->toArray());

        $today = new Carbon();

        $weekData = [];
        for ($i = 6; $i > -1; $i--) {
            $currentDay = $today->copy()->subDay($i);

            $sourceUserMsgs = [];
            for ($j = 0; $j < count($lastSevenDaysReceivedMsg); $j++) {
                if (isset($lastSevenDaysReceivedMsg[$j]['timestamp'])) {
                    if ($currentDay->isSameDay(Carbon::parse(Carbon::createFromTimestamp($lastSevenDaysReceivedMsg[$j]['timestamp'] / 1000)))) {
                        $resourceUserMsgs[] = $lastSevenDaysReceivedMsg[$j];
                    }
                }
            }

            $weekData[] = [
                'day' => $currentDay,
                'sourceUsers' => count($sourceUserMsgs)
            ];
        }

        return $weekData;
    }

    private function activitiesPerWeek()
    {
        // TODO
        /*
            URLクリック（可能ならメッセージ開封）
            カルーセルクリック
            アンケート回答
        */
        $account = $this->user->account;
        $lastSevenWeeksReceivedMsgObj = $account->accountMessages->filter(function ($accountMessage) {
            // created_atを使ってもいいかな
            return Carbon::createFromTimestamp($accountMessage->timestamp / 1000)->toFormattedDateString() >= Carbon::today()->subWeek(6);
        });
        
        $lastSevenWeeksReceivedMsg = array_values($lastSevenWeeksReceivedMsgObj->toArray());

        $today = new Carbon();

        $weekData = [];
        for ($i = 6; $i > -1; $i--) {
            $currentWeek = $today->copy()->subWeek($i);

            $sourceUserMsgs = [];
            for ($j = 0; $j < count($lastSevenWeeksReceivedMsg); $j++) {
                if (isset($lastSevenWeeksReceivedMsg[$j])) {
                    if (Carbon::parse(Carbon::createFromTimestamp($lastSevenWeeksReceivedMsg[$j]['timestamp'] / 1000)) <= $currentWeek &&
                        Carbon::parse(Carbon::createFromTimestamp($lastSevenWeeksReceivedMsg[$j]['timestamp'] / 1000)) >= $currentWeek->copy()->subWeek(1)) {
                        $sourceUserMsgs[] = $lastSevenWeeksReceivedMsg[$j];
                    }
                }
            }

            $weekData[] = [
                'week' => $currentWeek,
                'sourceUsers' => count($sourceUserMsgs)
            ];
        }

        return $weekData;
    }

    private function activitiesPerMonth()
    {
        // TODO
        /*
            URLクリック（可能ならメッセージ開封）
            カルーセルクリック
            アンケート回答
        */
        $account = $this->user->account;
        $lastSevenMonthsReceivedMsgObj = $account->accountMessages->filter(function ($accountMessage) {
            // created_atを使ってもいいかな
            return Carbon::createFromTimestamp($accountMessage->timestamp / 1000)->toFormattedDateString() >= Carbon::today()->subMonth(6);
        });

        $lastSevenMonthsReceivedMsg = array_values($lastSevenMonthsReceivedMsgObj->toArray());

        $today = new Carbon();

        $sevenMonthsData = [];
        for ($i = 6; $i > -1; $i--) {
            $currentMonth = $today->copy()->subMonth($i);

            $sourceUserMsgs = [];
            for ($j = 0; $j < count($lastSevenMonthsReceivedMsg); $j++) {
                if (isset($lastSevenMonthsReceivedMsg[$j])) {
                    if (Carbon::parse(Carbon::createFromTimestamp($lastSevenMonthsReceivedMsg[$j]['timestamp'] / 1000)) <= $currentMonth &&
                        Carbon::parse(Carbon::createFromTimestamp($lastSevenMonthsReceivedMsg[$j]['timestamp'] / 1000)) >= $currentMonth->copy()->subMonth(1)) {
                        $sourceUserMsgs[] = $lastSevenMonthsReceivedMsg[$j];
                    }
                }
            }

            $sevenMonthsData[] = [
                'month' => $currentMonth,
                'sourceUsers' => count($sourceUserMsgs)
            ];
        }

        return $sevenMonthsData;
    }

    private function activitiesPerYear()
    {
        // TODO
        /*
            URLクリック（可能ならメッセージ開封）
            カルーセルクリック
            アンケート回答
        */
        $account = $this->user->account;
        $lastSevenYearsReceivedMsgObj = $account->accountMessages->filter(function ($accountMessage) {
            // created_atを使ってもいいかな
            return Carbon::createFromTimestamp($accountMessage->timestamp / 1000)->toFormattedDateString() >= Carbon::today()->subYear(6);
        });

        $lastSevenYearsReceivedMsg = array_values($lastSevenYearsReceivedMsgObj->toArray());

        $today = new Carbon();

        $sevenYearsData = [];
        for ($i = 6; $i > -1; $i--) {
            $currentYear = $today->copy()->subYear($i);

            $sourceUserMsgs = [];
            for ($j = 0; $j < count($lastSevenYearsReceivedMsg); $j++) {
                if (isset($lastSevenYearsReceivedMsg[$j])) {
                    if (Carbon::parse(Carbon::createFromTimestamp($lastSevenYearsReceivedMsg[$j]['timestamp'] / 1000)) <= $currentYear &&
                        Carbon::parse(Carbon::createFromTimestamp($lastSevenYearsReceivedMsg[$j]['timestamp'] / 1000)) >= $currentYear->copy()->subYear(1)) {
                        $sourceUserMsgs[] = $lastSevenYearsReceivedMsg[$j];
                    }
                }
            }

            $sevenYearsData[] = [
                'year' => $currentYear,
                'sourceUsers' => count($sourceUserMsgs)
            ];
        }

        return $sevenYearsData;
    }
}
