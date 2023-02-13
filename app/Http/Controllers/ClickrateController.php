<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\ClickrateItem;
use App\ClickrateMessageRecord;
use App\ClickrateFollowerRecord;
use App\ScenarioMessage;
use App\Magazine;
use App\AccountFollower;
use App\AccountMessage;
use Auth;
use DB;
use Log;

class ClickrateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('clickrate');
    }

    public function generateToken()
    {
        $token = md5(uniqid('CLICK'. mt_rand(), true));
        $data = [
            'clickrate_token' => $token,
            'url' => route('clickrate.route', ['token' => $token])
        ];

        return response()->json($data, Response::HTTP_OK);
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
        $account = Auth::user()->account;

        $clickrate = new ClickrateItem();
        $clickrate->title = $request->title;
        $clickrate->clickrate_token = $request->clickrate_token;
        $clickrate->redirect_url = $request->redirect_url;

        $account->clickrateItems()->save($clickrate);
        return response()->json($clickrate);
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
        $item_data = Auth::user()->account->clickrateItems()->where('id', $id)->first();
        
        if (isset($item_data)) {
            $d_item = $item_data->toArray();
            $d_item['url'] = route('clickrate.route', ['token' => $item_data->clickrate_token]);
            return response()->json($d_item);
        }

        return response();
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
        $item_data = Auth::user()->account->clickrateItems()->where('id', $id)->first();
        if (!isset($item_data)) {
            return response()->json(null, Response::HTTP_NOT_FOUND);
        }
        
        $item_data->title = $request->title;
        $item_data->redirect_url = $request->redirect_url;
        $item_data->save();

        return response()->json($item_data);
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

    public function batchDelete(Request $request)
    {
        $itemIds = $request->input('item_ids');
        foreach ($itemIds as $itemId) {
            $item = Auth::user()->account->clickrateItems()->where('id', $itemId)->first();
            if (!isset($item)) {
                continue;
            }
            $item->delete();
        }

        return response()->json(null, Response::HTTP_OK);
    }

    public function lists()
    {
        $items = Auth::user()->account->clickrateItems()->get();

        $data = [];
        foreach ($items as $item) {
            // URL送信数、訪問数は、clickrate_message_recordsテーブルの合計を表示する。
            $clickrateMessageRecord = $item->clickrateMessageRecords();
            if (isset($clickrateMessageRecord)) {
                $item->send_count = $clickrateMessageRecord->sum('send_count');
                $item->access_count = $clickrateMessageRecord->sum('access_count');
            } else {
                $item->send_count = 0;
                $item->access_count = 0;
            }

            $d_item = $item->toArray();
            // TODO: 必要なものを追加 貼り付け用url, redirecturl
            $d_item['url'] = route('clickrate.route', ['token' => $item->clickrate_token]);
            // $d_item['redirect_surl'] = route('clickrate.route', ['token' => $item->clickrate_token]);
            $data[] = $d_item;
        }
        
        return response()->json($data, Response::HTTP_OK);
    }

    private function senarioAggregates($item_data)
    {
        // シナリオはsource_message_id単位で集計する。
        $result = [];
        $scenarioAggregates = $item_data->clickrateMessageRecords()
        ->select(DB::raw('source_message_id, sum(send_count) as send_count, sum(access_count) as access_count, max(send_at) last_date'))
        ->where('record_type', '1')
        ->groupby('source_message_id')
        ->orderby('last_date', 'desc')
        ->get();

        foreach ($scenarioAggregates as $scenarioAggregate) {
            $scenarioMessage = ScenarioMessage::find($scenarioAggregate->source_message_id);
            if (isset($scenarioMessage)) {
                // TODO: 配信タイミングの文言生成
                $timing = '';
                $scheduleDate = Carbon::parse($scenarioMessage->schedule_date);

                // 送信数
                $send_count = $scenarioAggregate->send_count;
                // 述べアクセス数
                $access_count = $scenarioAggregate->access_count;
                // 送信人数（重複はカウントしない）
                $followerAggregate1 = ClickrateFollowerRecord::select(DB::raw('count(distinct(account_follower_id)) as send_followers'))
                ->join('clickrate_message_records', function ($join) use ($scenarioAggregate) {
                    $join->on('clickrate_message_record_id', '=', 'clickrate_message_records.id')
                    ->where('clickrate_message_records.record_type', 1)
                    ->where('clickrate_message_records.source_message_id', $scenarioAggregate->source_message_id);
                })
                ->first();
                // 訪問数
                $followerAggregate2 = ClickrateFollowerRecord::select(DB::raw('count(distinct(account_follower_id)) as visitors'))
                ->join('clickrate_message_records', function ($join) use ($scenarioAggregate) {
                    $join->on('clickrate_message_record_id', '=', 'clickrate_message_records.id')
                    ->where('clickrate_message_records.record_type', 1)
                    ->where('clickrate_message_records.source_message_id', $scenarioAggregate->source_message_id);
                })
                ->whereNotNull('access_at')
                ->first();

                $result[] = [
                    'title' => $scenarioMessage->title,
                    'message' => $scenarioMessage->content_message,
                    'timing' => $timing,
                    'send_count' => $send_count,
                    'access_count' => $access_count,
                    'send_people' => $followerAggregate1->send_followers,
                    'visitors' => $followerAggregate2->visitors,
                ];
            }
        }
        return $result;
    }

    private function magazineAggregates($item_data)
    {
        // 一斉配信は送信単位で集計する。
        $result = [];
        $magazineAggregates = $item_data->clickrateMessageRecords()
        ->where('record_type', 2)
        ->orderby('send_at', 'desc')
        ->get();

        foreach ($magazineAggregates as $magazineAggregate) {
            $magazineMessage = Magazine::find($magazineAggregate->source_message_id);
            if (isset($magazineMessage)) {
                // 訪問数
                $followerAggregate = ClickrateFollowerRecord::select(DB::raw('count(*) as send_followers, count(access_at) as visitors'))
                ->where('clickrate_message_record_id', $magazineAggregate->id)->first();

                $result[] = [
                    'title' => $magazineMessage->title,
                    'message' => $magazineMessage->content_message,
                    'timing' => Carbon::parse($magazineAggregate->send_at)->toDateTimeString(),
                    'send_count' => $magazineAggregate->send_count,
                    'access_count' => $magazineAggregate->access_count,
                    'send_people' => $followerAggregate->send_followers,
                    'visitors' => $followerAggregate->visitors,
                ];
            }
        }
        return $result;
    }

    private function individualAggregates($item_data)
    {
        // 個別＆自動返信はフォロワー単位で集計する。
        $result = [];

        DB::enableQueryLog();
        $individualAggregates = ClickrateFollowerRecord::select(DB::raw('account_follower_id, count(*) as send_count, count(access_at) as access_count, max(clickrate_message_records.send_at) as last_date'))
        ->join('clickrate_message_records', function ($join) use ($item_data) {
            $join->on('clickrate_message_record_id', '=', 'clickrate_message_records.id')
            ->where('clickrate_message_records.record_type', 3)
            ->where('clickrate_message_records.clickrate_item_id', $item_data->id);
        })->groupby('account_follower_id')
        ->orderby('last_date', 'desc')
        ->get();

        $log = DB::getQueryLog();
        Log::debug($log);
        foreach ($individualAggregates as $individualAggregate) {
            $accountFollower = AccountFollower::find($individualAggregate->account_follower_id);
            

            // IDの一番大きいものを最新の送信メッセージと判断する。
            $latest_message_record = ClickrateMessageRecord::select('clickrate_message_records.id', 'message')
            ->join('clickrate_follower_records', function ($join) use ($accountFollower) {
                $join->on('clickrate_message_record_id', '=', 'clickrate_message_records.id')
                ->where('clickrate_follower_records.account_follower_id', $accountFollower->id);
            })
            ->where('clickrate_message_records.record_type', 3)
            ->orderBy('id', 'desc')
            ->first();


            $latest_message = '';
            if (isset($latest_message_record)) {
                $latest_message = $latest_message_record->message;
            }

            $result[] = [
                'name' => $accountFollower->pfUsers->display_name,
                'send_date_time' => $individualAggregate->last_date,
                'send_count' => $individualAggregate->send_count,
                'access_count' => $individualAggregate->access_count,
                'message' => $latest_message,
            ];
        }
        return $result;
    }

    public function showDetail($id)
    {
        $account =  Auth::user()->account;
        $item_data = $account->clickrateItems()->where('id', $id)->first();
        if (!isset($item_data)) {
            return response()->json(null, Response::HTTP_NOT_FOUND);
        }

        $messageAggregate = ClickrateMessageRecord::select(
            DB::raw('sum(send_count) as total_send_count, sum(access_count) as total_access_count')
        )->where('clickrate_item_id', $id)->first();
        
        $total_send_count = $messageAggregate->total_send_count != null ? $messageAggregate->total_send_count : 0;
        $total_access_count = $messageAggregate->total_access_count != null ? $messageAggregate->total_access_count : 0;

        $scenario = $this->senarioAggregates($item_data);
        $magazine = $this->magazineAggregates($item_data);
        $individual = $this->individualAggregates($item_data);

        $followerAggregate1 = ClickrateFollowerRecord::select(DB::raw('count(distinct(account_follower_id)) as send_followers'))
            ->where('clickrate_item_id', $id)
            ->first();

        $followerAggregate2 = ClickrateFollowerRecord::select(DB::raw('count(distinct(account_follower_id)) as visitors'))
            ->where('clickrate_item_id', $id)
            ->whereNotNull('access_at')
            ->first();
        
        $data = [
            'title' => $item_data->title,
            'total_send_count' => $total_send_count,// 送信した延べ人数
            'total_access_count' => $total_access_count,// アクセスした延べ人数
            'send_people' => $followerAggregate1->send_followers, // 送信した人数
            'visitors' => $followerAggregate2->visitors, // 訪問人数
            'friends' => $account->accountFollowers()->count(), // 現在のアカウント数
            'scenario_data' => $scenario,
            'magazine_data' => $magazine,
            'individual_data' => $individual,
        ];

        return response()->json($data, Response::HTTP_OK);
    }
}
