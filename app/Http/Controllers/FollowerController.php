<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\FriendsModel;
use App\LineUserManegerSetting;
use App\Scenario;
// use App\FriendStatus;
// use App\FriendsSenarioList;
// use App\SenarioMessages;
use App\LineEvents\LineUtils;
use Illuminate\Support\Facades\Auth;
use App\LineEvents\MessageEvent;
use App\Services\PfUserAuthService;
use DB;
use DateTime;

class FollowerController extends Controller
{
    /**
     * pfUserロジック.Auth版
     */
    protected $pfUserService;

    /**
     * Create a new controller instance.
     *
     * @param  PfUserAuthService  $pfUserService
     * @return void
     */
    public function __construct(PfUserAuthService $pfUserService)
    {
        $this->pfUserService = $pfUserService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('followers');
        
        // $channelId = LineBotSettings::where('user_id', Auth::id()) ->get();
        // $status  = FriendStatus::all();
        // $frnds = FriendsModel::where('channelId', $channelId[0]->channel_id)->get();
        
        // foreach ($frnds as $frnd) {
        //     $statusTitle = FriendStatus::where('id', $frnd->status) ->get();
        //     $frnd->statusTitle = $statusTitle[0]->title;
        // }
        
        // return view('content',[
        //     'followers'=> $frnds,
        //     'status'=> $status,
        //     'success'=>NULL,
        //     'url' => 'followers',
        //     'content'=>'followers'
        // ]);
    }

    // temporary removed by aldrin papa
    // public function lists(){
    //     $channelId = LineBotSettings::where('user_id', Auth::id()) ->get();
    //     $status  = FriendStatus::all();
    //     $frnds = FriendsModel::where('channelId', $channelId[0]->channel_id)->get();
        
    //     foreach ($frnds as $frnd) {
    //         $statusTitle = FriendStatus::where('id', $frnd->status) ->get();
    //         $frnd->statusTitle = $statusTitle[0]->title;
    //     }

    //     $data=['followers'=>$frnds];
    //     return response()->json($data);
    // }

    public function lists(Request $request)
    {
        $mode = $request->get('mode');
        $account = Auth::user()->account;

        // Account-followerをベースに必要な情報だけ取得する。
        $account_followers = null;
        if ($mode == '9') {
            // ブロックリスト
            $account_followers = $account->accountFollowers->where('is_blocked', '1');
        } else {
            $account_followers = $account->accountFollowers->where('is_blocked', '0');
            if ($mode == 'Z') {
                // テストユーザー
                $account_followers = $account_followers->where('is_tester', '1');
            }
        }

        $followers = [];
        foreach ($account_followers as $account_follower) {
            // followerとpfUserは1対1
            $pf_user = $account_follower->pfUsers;
            $pf_user_tag_managements = $pf_user->pfUserTagManagements;

            // タグ
            $tags = [];
            if (isset($pf_user_tag_managements)) {
                foreach ($pf_user_tag_managements as $pf_user_tag_management) {
                    $tags_management = $pf_user_tag_management->tagManagement;
                    if ($tags_management) {
                        $tags[] = $tags_management->title;
                    }
                }
            }

            // 配信状態
            $scenario_delivery = $pf_user->scenarioDeliveries()->where('is_sent', 1)->orderBy('updated_at', 'desc')->first();
            $scenario_title = '';
            if (isset($scenario_delivery)) {
                $scenario_title = $scenario_delivery->scenarioMessage->title;
            }
            
            $followers[] = [
                'id' => $account_follower->id,
                'pf_user_id' => $pf_user->id,
                'pf_user_picture' => $pf_user->picture,
                'pf_user_display_name' => $pf_user->display_name,
                'timedate_followed' => date('Y-m-d', strtotime($account_follower->timedate_followed)),
                'source_user_id' => $account_follower->source_user_id,
                'message_status' => $account_follower->message_status,
                'scenario_name' => $scenario_title,
                'tags' => implode('；', $tags),
            ];
        }

        $data = [
            'followers' => $followers,
        ];

        return response()->json($data, Response::HTTP_OK);
    }

    public function userInfo($followerId)
    {
        $account_follower = Auth::user()->account->accountFollowers->where('id', $followerId)->first();
        if (isset($account_follower)) {
            $pf_user = $account_follower->pfUsers;

            // タグ
            $tags = [];
            $pf_user_tag_managements = $pf_user->pfUserTagManagements;

            if (isset($pf_user_tag_managements)) {
                foreach ($pf_user_tag_managements as $pf_user_tag_management) {
                    $tags_management = $pf_user_tag_management->tagManagement;
                    if ($tags_management) {
                        $tags[] = $tags_management->title;
                    }
                }
            }
            
            $delivered = DB::table('scenario_delivery')
                ->join('scenario_messages', 'scenario_message_id', '=', 'scenario_messages.id')
                ->where('pf_user_id', $pf_user->id)
                ->groupBy('scenario_messages.scenario_id')
                ->selectRaw('scenario_id, MIN(is_sent) as min, MAX(is_sent) as max')->get();

            $delivery_status = [];
            foreach ($delivered as $d) {
                $name = Scenario::findOrFail($d->scenario_id)->name;
                $min = $d->min;
                $max = $d->max;
                $status = '0';
                if ($min == 1 && $max == 1) {
                    // 全レコードが1なので配信完了
                    $status = '1';
                }
                $delivery_status[] = ['id' => $d->scenario_id, 'name' => $name, 'status' => $status ];
            }

            $data = [
                'id' => $followerId,
                'avator_picture' => $pf_user->picture,
                'display_name' => $pf_user->display_name,
                'source_user_id' => $account_follower->source_user_id,
                'timedate_followed' => date('Y-m-d', strtotime($account_follower->timedate_followed)),
                'tags' => implode('；', $tags),
                'notes' => $account_follower->notes,
                'delivery_status' => $delivery_status,
                'is_tester' => $account_follower->is_tester,
            ];
            return response()->json($data, Response::HTTP_OK);
        }

        throw \Exception();
    }

    public function update(Request $request, $followerId)
    {
        $account_follower = Auth::user()->account->accountFollowers->where('id', $followerId)->first();
        if (!isset($account_follower)) {
            throw \Exception();
        }
        $data = $request->all();
        if (array_key_exists('notes', $data)) {
            $account_follower->notes = $data['notes'];
        }
        if (array_key_exists('is_tester', $data)) {
            $account_follower->is_tester = $data['is_tester'];
        }
        $account_follower->save();
        return response()->json($data, Response::HTTP_OK);
    }

    public function addTags(Request $request)
    {
        $account = Auth::user()->account;
        $follower_ids = $request->get('followerIds');
        $remove_tags = $request->get('removeTags');
        $add_tags = $request->get('addTags');
        DB::transaction(function () use ($account, $follower_ids, $remove_tags, $add_tags) {
            foreach ($follower_ids as $follower_id) {
                $follower = $account->accountFollowers()
                    ->where('id', $follower_id)
                    ->first();

                if (!isset($follower)) {
                    continue;
                }

                if ($remove_tags != null && count($remove_tags)) {
                    $removeTagIds = Auth::user()->account->tagManagements()->whereIn('title', $remove_tags)->pluck('id');

                    foreach ($removeTagIds as $removeTagId) {
                        $this->pfUserService->removeTag($follower->pfUsers, $removeTagId);
                    }
                }

                if ($add_tags != null && count($add_tags)) {
                    $addTagIds = Auth::user()->account->tagManagements()->whereIn('title', $add_tags)->pluck('id');

                    foreach ($addTagIds as $addTagId) {
                        $this->pfUserService->addTag($follower->pfUsers, $addTagId);
                    }
                }
            }
        }, 5);
        return response(null, Response::HTTP_OK);
    }

    public function addScenarios(Request $request)
    {
        $account = Auth::user()->account;
        $follower_ids = $request->get('followerIds');
        $remove_scenarios = $request->get('removeScenarios');
        $add_scenarios = $request->get('addScenarios');
        DB::transaction(function () use ($account, $follower_ids, $remove_scenarios, $add_scenarios) {
            foreach ($follower_ids as $follower_id) {
                $follower = $account->accountFollowers()
                    ->where('id', $follower_id)
                    ->first();

                if (!isset($follower)) {
                    continue;
                }
                
                if ($remove_scenarios != null && count($remove_scenarios)) {
                    $removeScenarioIds = Auth::user()->account->scenarios()->whereIn('name', $remove_scenarios)->pluck('id');

                    foreach ($removeScenarioIds as $removeScenarioId) {
                        $this->pfUserService->removeScenario($follower->pfUsers, $removeScenarioId);
                    }
                }

                if ($add_scenarios != null && count($add_scenarios)) {
                    $addScenarioIds = Auth::user()->account->scenarios()->whereIn('name', $add_scenarios)->pluck('id');

                    foreach ($addScenarioIds as $addScenarioId) {
                        $this->pfUserService->addScenario($follower->pfUsers, $addScenarioId);
                    }
                }
            }
        }, 5);
        return response(null, Response::HTTP_OK);
    }

    public function addRichMenu(Request $request)
    {
        $account = Auth::user()->account;
        $follower_ids = $request->get('followerIds');
        $remove_menu = $request->get('removeMenu');
        $add_menu = $request->get('addMenu');
        DB::transaction(function () use ($account, $follower_ids, $remove_menu, $add_menu) {
            foreach ($follower_ids as $follower_id) {
                $follower = $account->accountFollowers()
                    ->where('id', $follower_id)
                    ->first();

                if (!isset($follower)) {
                    continue;
                }

                if ($remove_menu != null) {
                    $targetMenu = Auth::user()->account->richMenuItems()->where('title', $remove_menu)->first();

                    if (isset($targetMenu)) {
                        $this->pfUserService->removeRichMenu($follower->pfUsers, $targetMenu->id);
                    }
                }

                if ($add_menu != null) {
                    $targetMenu = Auth::user()->account->richMenuItems()->where('title', $add_menu)->first();
                    if (isset($targetMenu)) {
                        $this->pfUserService->setRichMenu($follower->pfUsers, $targetMenu->id);
                    }
                }
            }
        }, 5);
        return response(null, Response::HTTP_OK);
    }

    public function block(Request $request)
    {
        $account = Auth::user()->account;
        $follower_ids = $request->get('followerIds');
        DB::transaction(function () use ($account, $follower_ids) {
            foreach ($follower_ids as $follower_id) {
                $follower = $account->accountFollowers()->where('id', $follower_id)->first();
                if (!isset($follower)) {
                    continue;
                }

                $follower->is_blocked = 1;
                $follower->blocked_date = new DateTime();
                $follower->save();

                // 各種テーブルの削除
                // pf_user_tag_managements
                $follower->pfUsers->pfUserTagManagements()->delete();
                // delete from `pf_user_tag_managements` where `pf_user_tag_managements`.`pf_user_id` = ? and `pf_user_tag_managements`.`pf_user_id` is not null

                // magazine_deliveries
                $follower->pfUsers->magazineDeliveries()->delete();
                // delete from `magazine_deliveries` where `magazine_deliveries`.`pf_user_id` = ? and `magazine_deliveries`.`pf_user_id` is not null

                // scenario_deliveries
                $follower->pfUsers->scenarioDeliveries()->delete();
                // delete from `scenario_delivery` where `scenario_delivery`.`pf_user_id` = ? and `scenario_delivery`.`pf_user_id` is not null

                // account_messages
                if (isset($follower->source_user_id)) {
                    $count = $account->accountMessages()
                    ->where('source_user_id', $follower->source_user_id)
                    ->delete();
                    // delete from `account_messages` where `account_messages`.`account_id` = ? and `account_messages`.`account_id` is not null and `source_user_id` = ?
                }
            }
        });
    }

    public function followersList()
    {
        return response()->json(Auth::user()->account->accountFollowers, Response::HTTP_OK);
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
