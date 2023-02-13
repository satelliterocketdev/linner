<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Request;

//DB
use App\Account;
use App\Plan;
use App\RoleUser;
use App\Role;
use App\Magazine;
use App\MagazineDelivery;
use App\ScenarioMessage;
use App\ScenarioDelivery;
use App\Settlement;

use Lang;

class PlanController extends Controller
{
    public function index()
    {
        return view('plan');
    }

    public function detail()
    {
        return view('plan_detail');
    }

    public function list()
    {
        $plans = DB::table('plans')
                    ->select(DB::raw('count(*) as plan_count, type, name, description1, description2'))
                    ->groupBy('type')
                    ->orderBy('id')
                    ->get();
        return response()->json($plans, Response::HTTP_OK);
    }

    public function detailList()
    {
        $plans = DB::table('plans')->get();
        
        return response()->json($plans, Response::HTTP_OK);
    }

    public function myData()
    {
        $user               = Auth::user();
        $myData['plan_id']  = $user->plan_id;

        /*
        * アカウント管理者が管理する全LINE@アカウントのID取得
        */
        $accountIds = RoleUser::where('user_id', $user->id)
                               ->where('role_id', Role::ROLE_ACCOUNT_ADMINISTRATOR) //アカウント管理者指定
                               ->pluck('account_id');
        $accounts = Account::whereIn('id', $accountIds)->get();
        $myData['account_count'] = count($accountIds);

        return response()->json($myData, Response::HTTP_OK);
    }

    //新規登録
    public function register(Request $request)
    {
        $planId  = Request::input('plan_id');
        $user    = Auth::user();

        /*
        * plan_id更新
        */
        \DB::table('users')->where('id', $user->id)->update(['plan_id'=>$planId]);
        $plan = Plan::find($planId);
        if ($plan->price == 0) { //フリープランの場合にもSettlementにデータを登録する
            $settlement = new Settlement();
            $settlement->user_id = $user->id;
            $settlement->plan_id = $planId ;
            $settlement->status = Settlement::STATUS_SETTLED;
            $settlement->amount = 0;
            $settlement->save();
        }

        /*
        * 決済情報ナシ = 決済画面、決済情報アリ = LINEアカウント申請画面
        */
        if ($user->settlement_id == null) {
            return response()->redirectTo('paymentmethod');
        } else {
            return response()->redirectTo('newaccount');
        }
    }

    //変更
    public function update(Request $request)
    {
        $planId  = Request::input('plan_id');
        $user    = Auth::user();

        //ユーザーにプランセット
        if ($user->plan_id < $planId) {
            //アップグレード
            //決済しなければならないため、request_plan_idに保存する
            //plan_id更新
            \DB::table('users')->where('id', $user->id)->update(['request_plan_id'=>$planId]);
            return response()->redirectTo('paymentmethod');
        } elseif ($user->plan_id >= $planId) {
            //ダウングレード
            //plan_id更新
            \DB::table('users')->where('id', $user->id)->update(['plan_id'=>$planId]);

            //settlementsにamount0 status1 で 変更後のplan_idを登録
            // 決済テーブルにデータを登録し、IDを取得する
            $settlement = new Settlement();
            $settlement->user_id = $user->id;
            $settlement->plan_id = $planId ;
            $settlement->status = Settlement::STATUS_SETTLED;
            $settlement->amount = 0;
            $settlement->save();

            return redirect()->action('DashboardController@index');
        }
    }


    private function sentDeliveries($deliveries)
    {
        return $deliveries->filter(function ($delivery, $key) {
            return $delivery->is_sent == 1;
        });
    }

}
