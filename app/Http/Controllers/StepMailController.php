<?php

namespace App\Http\Controllers;

use App\MessageUrl;
use App\TargetTrait;
use Carbon\Carbon;
use Datetime;
use DB;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Scenario;
use App\ScenarioMessage;
use App\ScenarioDelivery;
use App\ScenarioTarget;
use App\ScenarioAction;
use App\ScenarioMessageAttachment;
use App\AccountFollower;
use App\PfUser;
use App\RoleUser;
use App\TagManagement;
use App\Survey;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Services\MessageService;

use App\Role;

use stdClass; //this will change

class StepmailController extends Controller
{
    protected $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    public function index()
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole(Role::ROLE_ACCOUNT_ADMINISTRATOR, $user->account->id);
        $canEdit = $isAdmin ? $isAdmin : $user
                    ->hasRole(Role::ROLE_SCENARIO_DISTRIBUTION_EDITABLE, $user->account->id);

        return view('stepmail')->with('canEdit', $canEdit);
    }

    public function view($generatedId)
    {
        try {
            $messageCollection = ScenarioMessage::where('id', $generatedId);
            if (!$messageCollection->count()) {
                return response()->json(null, Response::HTTP_NOT_FOUND);
            }

            foreach ($messageCollection->get() as $message) {
                $message->attachment = $message->messageAttachments()->get();
            }
            return response()->json($message, Response::HTTP_OK);
        } catch (Exception $e) {
            var_dump($e->getMessage());
        }
    }

    public function changeActivity(Request $request)
    {
        try {
            DB::beginTransaction();
            $scenario = Scenario::findOrFail($request->id);
            $scenario->update(['is_active' => $request->is_active]);
            DB::commit();
            return response()->json([], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = Auth::user();

            /** @var Scenario $scenario */
            $scenario = $user->account->scenarios()->create([
                'name' => $request->name,
                'is_active' => $request->is_active
                ]);

            $targets = json_decode($request->target, true);
            $scenario->createStoreTargets($targets);

            $actions = json_decode($request->action, true);

            $scenario->createStoreActions($actions);

            if ($messages = json_decode($request->message, true)) {

                foreach ($messages as $message) {
                    $scenarioMessageAttachment = [];
                    $message['account_id'] = Auth::id();
                    $message['is_draft'] = 0;
                    $message['formatted_message'] = 'TMP';
                    $message['source_scenario_id'] = $scenario->id;
                    if (isset($message['schedule_date']) && isset($message['schedule_time'])) {
                        $date = new Datetime($message['schedule_date']);
                        $time = new Datetime($message['schedule_time']);
                        $message['schedule_date'] = sprintf('%s %s', $date->format('Y-m-d'), $time->format('H:i:s'));
                    } else {
                        $message['schedule_date'] = date('Y-m-d H:i:s');
                    }

                    /** @var ScenarioMessage $scenarioMessage */
                    $scenarioMessage = $scenario->scenarioMessages()->create($message);

                    $scenarioMessage->recreateUrlAction($message['url_actions']);
                    $messageUrls = $scenarioMessage->messageUrls()->orderBy('id')->get();
                    $formattedMessage = $this->messageService->formatMessage($user->account_id, $message['content_message'], $messageUrls);
                    $scenarioMessage->formatted_message = $formattedMessage;
                    $scenarioMessage->save();

                    if ($attachments = $message['attachments']) {
                        foreach ($attachments as $attachment) {
                            if ($attachment) {
                                $attachment['media_file_id'] = $attachment['id'];
                                $scenarioMessage->messageAttachments()->create($attachment);
                                $scenarioMessageAttachment[] = $attachment;
                            }
                        }
                    }

                    if (isset($scenarioMessage->schedule_number) || isset($scenarioMessage->schedule_date)) {
                        $pfUsers = $scenario->getPfUsers(
                            isset($tagsServes) ? $tagsServes : null,
                            isset($scenariosServes) ? $scenariosServes : null,
                            isset($dateServes) ? $dateServes : null
                        );

                        /** @var PfUser $pfUser */
                        foreach ($pfUsers as $pfUser) {
                            $schedule_date = new Datetime($scenarioMessage->schedule_date);
                            $pfUser->scenarioDeliveries()->create([
                                'scenario_message_id' => $scenarioMessage->id,
                                'type' => $scenarioMessage->content_type,
                                'schedule_date' => $schedule_date->format('Y-m-d H:i:s'),
                                'is_attachment' => $scenarioMessage->messageAttachments()->count() > 0,
                                'is_sent' => 0
                            ]);
                        }
                        if ($message['content_type'] != 'survey') {
                            foreach ($scenarioMessageAttachment as $attachment) {
                                if ($attachment) {
                                    foreach ($pfUsers as $pfUser) {
                                        $followerMessageData['attachment'] = $attachment;
                                        $followerMessageData['message'] = $scenarioMessage;
                                        $followerMessageData['pf_user_id'] = $pfUser->id;
                                        ScenarioDelivery::preCreate($followerMessageData);
                                    }
                                }
                            }
                        }
                    }

                    // -- Surveys登録 --//
                    if ($message['content_type'] == 'survey') {
                        $this->saveSurveys($message, $scenarioMessage, $user);
                    }
                }
            }

            DB::commit();
            return response()->json($scenario, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $scenario = Scenario::findOrFail($id);
            $scenario->users = $scenario->users();
            $scenario->scenarioMessages = $scenario->scenarioMessages()->get();
        } catch (ModelNotFoundException $e) {
            return response()->json(null, Response::HTTP_NOT_FOUND);
        }
        return response()->json($scenario, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param $id
     * @return ResponseFactory|JsonResponse|\Symfony\Component\HttpFoundation\Response
     * @throws Exception
     */
    public function update(Request $request, $id)
    {
        try {
            $users = AccountFollower::all()->toArray();

            /** @var Scenario $scenario */
            $scenario = Scenario::findOrFail($id);
            $scenario->fill([
                'name' => $request->name,
                'is_active' => isset($request->is_active) ? $request->is_active : $scenario->is_active
            ])->save();

            $messages = json_decode($request->message);

            // -- Actions更新 --//

            $scenario->scenarioActions()->delete();
            $actions = json_decode($request->action, true);

            $scenario->createStoreActions($actions);

            // -- Targets更新 --//

            $scenario->scenarioTargets()->delete();
            $targets = json_decode($request->target, true);
            $scenario->createStoreTargets($targets);

            if ($messages) {
                foreach ($messages as $message) {
                    $message->account_id = Auth::id();
                    $message->is_draft = 0;
                    $message->scenario_id = $scenario->id;
                    $message->formatted_message = 'TMP';
                    
                    if (isset($message->id) &&
                        $scenarioMessage = ScenarioMessage::find($message->id)) {
                        $scenarioMessage->fill((array) $message)->save();
                    } else {
                        $scenarioMessage = $scenario->scenarioMessages()->create((array) $message);
                    }

                    $urlactions = json_decode(json_encode($message->url_actions), true);
                    $scenarioMessage->recreateUrlAction($urlactions);
                    $messageUrls = $scenarioMessage->messageUrls()->orderBy('id')->get();
                    $scenarioMessage->formatted_message =
                        $this->messageService->formatMessage(
                            Auth::user()->account_id,
                            $message->content_message,
                            $messageUrls
                        );
                    $scenarioMessage->save();

                    $scenarioMessage->messageAttachments()->delete();

                    foreach ($message->attachments as $attachment) {
                        $scenarioMessage->messageAttachments()->create(['media_file_id' => $attachment->id]);
                    }

                    if (isset($message->schedule_date) && isset($message->schedule_time)) {
                        $date = new Datetime($message->schedule_date);
                        $time = new Datetime($message->schedule_time);
                        $message->schedule_date = sprintf('%s %s', $date->format('Y-m-d'), $time->format('H:i:s'));
                    } else {
                        $message->schedule_date = date('Y-m-d H:i:s');
                    }

                    $pfUsers = $scenario->getPfUsers(
                        isset($targetTagsServes) ? $targetTagsServes : null,
                        isset($targetScenariosServes) ? $targetScenariosServes : null,
                        isset($dateServes) ? $dateServes : null
                    );

                    $scenarioMessage->deliveries()->where('is_sent', 0)->delete();
                    /** @var PfUser $pfUser */
                    foreach ($pfUsers as $pfUser) {
                        if ($pfUser->scenarioDeliveries()
                                ->where('scenario_message_id', $scenarioMessage->id)->count() !== 0) {
                            continue;
                        }
//                        $schedule_date = new Datetime($scenarioMessage->schedule_date);
                        $pfUser->scenarioDeliveries()->create([
                            'scenario_message_id' => $scenarioMessage->id,
                            'type' => $scenarioMessage->content_type,
                            'schedule_date' => $message->schedule_date,
                            'is_attachment' => 0,
                            'is_sent' => 0
                        ]);
                    }

                    if ($message->content_type != 'survey') {
                        foreach ($message->attachments as $attachment) {
                            if ($attachment) {
                                foreach ($pfUsers as $pfUser) {
                                    $followerMessageData['attachment'] = json_decode(json_encode($attachment), true);
                                    $followerMessageData['message'] = $scenarioMessage;
                                    $followerMessageData['pf_user_id'] = $pfUser->id;
                                    ScenarioDelivery::preCreate($followerMessageData);
                                }
                            }
                        }
                    }

                    // -- Surveys登録 --//
                    $user = Auth::user();
                    $message = (array)$message;
                    if ($message['content_type'] == 'survey') {
                        $this->saveSurveys($message, $scenarioMessage, $user);
                    }

                }
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        // // //louie
        // $sched_Events = new MessageEvent();
        // $sched_Events->sendMulticastMsg($scenario->id);
        // //end louie
        return response(null, Response::HTTP_OK);
    }

    /**
     * @param $id
     * @return ResponseFactory|JsonResponse|\Symfony\Component\HttpFoundation\Response
     * @throws Exception
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $ids = explode(',', $id);
            foreach ($ids as $id) {
                /** @var Scenario $scenario */
                $scenario = Scenario::findOrFail($id);
                $scenario->delete();

                $scenario->scenarioTargets()->delete();
                $scenario->scenarioActions()->delete();

                /** @var ScenarioMessage $scenarioMessage */
                foreach ($scenario->scenarioMessages as $scenarioMessage) {
                    $scenarioMessage->deliveries()->delete();
                    $scenarioMessage->messageAttachments()->delete();

                    $urls = $scenarioMessage->messageUrls;
                    foreach ($urls as $url) {
                        // deleting監視のため個別に削除
                        $url->delete();
                    }
                }

                $scenario->scenarioMessages()->delete();
            }
            DB::commit();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response(null, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return ResponseFactory|JsonResponse|\Symfony\Component\HttpFoundation\Response
     * @throws Exception
     */
    public function copy(Request $request)
    {
        DB::beginTransaction();
        try {
//            $user = Auth::user();

            /** @var Scenario $scenario */
            $scenario = Scenario::findOrFail($request->input('id'));
            /** @var Scenario $copyScenario */
            $copyScenario = $scenario->replicate();
            $copyScenario->name = $scenario->name .' copy';
            $copyScenario->save();

            $scenarioTargets = $scenario->scenarioTargets;
            foreach ($scenarioTargets as $scenarioTarget) {
                /** @var ScenarioTarget $copyScenarioTarget */
                $copyScenarioTarget = $scenarioTarget->replicate();
                $copyScenarioTarget->scenario_id = $copyScenario->id;
                $copyScenarioTarget->save();
            }

            $scenarioActions = $scenario->scenarioActions;
            foreach ($scenarioActions as $scenarioAction) {
                /** @var ScenarioAction $copyScenarioAction */
                $copyScenarioAction = $scenarioAction->replicate();
                $copyScenarioAction->scenario_id = $copyScenario->id;
                $copyScenarioAction->save();
            }

            $scenarioMessages = $scenario->scenarioMessages;
            foreach ($scenarioMessages as $scenarioMessage) {
                /** @var ScenarioMessage $copyScenarioMessage */
                $copyScenarioMessage = $scenarioMessage->replicate();
                $copyScenarioMessage->scenario_id = $copyScenario->id;
                $copyScenarioMessage->save();

                $scenarioDeliveries = $scenarioMessage->deliveries;
                foreach ($scenarioDeliveries as $scenarioDelivery) {
                    /** @var ScenarioDelivery $copyScenarioDelivery */
                    $copyScenarioDelivery = $scenarioDelivery->replicate();
                    $copyScenarioDelivery->scenario_message_id = $copyScenarioMessage->id;
                    $copyScenarioDelivery->save();
                }

                $scenarioAttachments = $scenarioMessage->messageAttachments;
                foreach ($scenarioAttachments as $scenarioAttachment) {
                    /** @var ScenarioMessageAttachment $copyScenarioAttachment */
                    $copyScenarioAttachment = $scenarioAttachment->replicate();
                    $copyScenarioAttachment->scenario_message_id = $copyScenarioMessage->id;
                    $copyScenarioAttachment->save();
                }

                $scenarioMessageSurvey                    = $scenarioMessage->survey;
                if ($scenarioMessageSurvey != null) {
                    $setSurvey                            = $scenarioMessageSurvey->replicate();
                    $setSurvey['account_id']              = $scenario->account_id;
                    $setSurvey['scenario_message_id']     = $copyScenarioMessage->id;
                    $setSurvey['type_delivery']           = 'scenarios';
                    $setSurvey->save();
                }
            }

            DB::commit();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response(null, Response::HTTP_OK);
    }

    public function destroyMessage($id)
    {
        try {
            $ids = explode(',', $id);
            foreach ($ids as $id) {
                /** @var ScenarioMessage $message */
                $message = ScenarioMessage::findOrFail($id);
                $message->delete();
                $message->deliveries()->delete();
                $message->messageAttachments()->delete();

                $urls = $message->messageUrls;
                /** @var MessageUrl $url */
                foreach ($urls as $url) {
                    // deleting監視のため個別に削除
                    $url->delete();
                }
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(null, Response::HTTP_NOT_FOUND);
        }
        return response(null, Response::HTTP_OK);
    }


    public function lists()
    {
        $min = 1;
        $max = 999;
        $scenarios = Auth::user()->account->scenarios->where('is_draft', 0)->values()->all();

        /** @var Scenario $scenario */
        foreach ($scenarios as $scenario) {
            $scenarioMessagesIds =
                ScenarioMessage::with('deliveries')->where('scenario_id', $scenario->id)->pluck('id');

            // 配信完了 pf_user_id取得
            $scenarioDeliveriesIsSent         = ScenarioDelivery::whereIn('scenario_message_id', $scenarioMessagesIds)
                                                                ->where('is_sent', 1)
                                                                ->groupBy('pf_user_id')
                                                                ->pluck('pf_user_id')
                                                                ->toArray();
            // 配信未完了 pf_user_id取得
            $scenarioDeliveriesIsNoSent         = ScenarioDelivery::whereIn('scenario_message_id', $scenarioMessagesIds)
                                                                ->where('is_sent', 0)
                                                                ->groupBy('pf_user_id')
                                                                ->pluck('pf_user_id')
                                                                ->toArray();

            // 配信完了人数（全てのメッセージのis_sent = 1のユーザー数）
            $scenario->sent_count = count(array_diff($scenarioDeliveriesIsSent, $scenarioDeliveriesIsNoSent));
            // 購読中人数（メッセージに1つでもis_sent = 0 が存在するユーザー数）
            $scenario->subscription_count = count($scenarioDeliveriesIsNoSent);

            // 配信後アクション タグ設定があるかチェック
            $tagAction =
                ScenarioAction::where('source_scenario_id', $scenario->id)
                    ->where('tag_management_id', '!=', null)
                    ->get();
            $scenario->tag_action      = empty($tagAction->toArray()) ? false : true ;

            // 配信後アクション シナリオ設定があるかチェック
            $scenarioAction =
                ScenarioAction::where('source_scenario_id', $scenario->id)
                    ->where('scenario_id', '!=', null)
                    ->get();
            $scenario->scenario_action = empty($scenarioAction->toArray()) ? false : true ;

            /** @var ScenarioMessage messages */
            $scenario->messages = $scenario->scenarioMessages()
                ->get()
                ->sort(
                    // 以下の順番にソートする
                    // 0: 登録直後
                    // 2: 経過時間指定
                    // 1: 登録後時間指定
                    // 3: 日時指定
                    function ($a, $b) {
                        /** @var ScenarioMessage $a */
                        /** @var ScenarioMessage $b */
                        $a_sort_key = $a->schedule_type;
                        $b_sort_key = $b->schedule_type;

                        // スケジュールタイプが一致する場合は時間でソートを行う
                        if ($a_sort_key === $b_sort_key) {
                            switch ($a_sort_key) {
                                // 登録後時間指定
                                case ScenarioMessage::SCHEDULE_TYPE_TIME_SPECIFIED_AFTER_REGISTRATION:
                                    $aDateTime = (new Carbon($a->schedule_date))
                                        ->setDate(1900, 1, 1)
                                        ->addDay($a->time_after);
                                    $bDateTime = (new Carbon($b->schedule_date))
                                        ->setDate(1900, 1, 1)
                                        ->addDay($b->time_after);
                                    return $aDateTime->lt($bDateTime) ? -1 : 1;
                                // 経過時間指定
                                case ScenarioMessage::SCHEDULE_TYPE_ELAPSED_TIME:
                                    $aDateTime = Carbon::today()
                                        ->addHours(explode(":", $a->time_after)[0])
                                        ->addMinutes(explode(":", $a->time_after)[1]);
                                    $bDateTime = Carbon::today()
                                        ->addHours(explode(":", $b->time_after)[0])
                                        ->addMinutes(explode(":", $b->time_after)[1]);

                                    return $aDateTime->toTimeString() < $bDateTime->toTimeString() ? -1 : 1;
                                // 日時指定
                                case ScenarioMessage::SCHEDULE_TYPE_TIME_SPECIFICATION:
                                    $aDateTime = (new Carbon($a->schedule_date));
                                    $bDateTime = (new Carbon($b->schedule_date));
                                    return $aDateTime->lt($bDateTime) ? -1 : 1;
                            }

                            $aTimeString = (new Carbon($a->schedule_date))->toTimeString();
                            $bTimeString = (new Carbon($b->schedule_date))->toTimeString();
                            return $aTimeString < $bTimeString ? -1 : 1;
                        }

                        if ($a_sort_key === ScenarioMessage::SCHEDULE_TYPE_TIME_SPECIFIED_AFTER_REGISTRATION) {
                            $a_sort_key = ScenarioMessage::SCHEDULE_TYPE_ELAPSED_TIME;
                        } elseif ($a_sort_key === ScenarioMessage::SCHEDULE_TYPE_ELAPSED_TIME) {
                            $a_sort_key = ScenarioMessage::SCHEDULE_TYPE_TIME_SPECIFIED_AFTER_REGISTRATION;
                        }
                        if ($b_sort_key === ScenarioMessage::SCHEDULE_TYPE_TIME_SPECIFIED_AFTER_REGISTRATION) {
                            $b_sort_key = ScenarioMessage::SCHEDULE_TYPE_ELAPSED_TIME;
                        } elseif ($b_sort_key === ScenarioMessage::SCHEDULE_TYPE_ELAPSED_TIME) {
                            $b_sort_key = ScenarioMessage::SCHEDULE_TYPE_TIME_SPECIFIED_AFTER_REGISTRATION;
                        }

                        return $a_sort_key < $b_sort_key ? -1 : 1;
                    }
                )
                ->values(); // 配列のキーを振り直す
            foreach ($scenario->messages as $key => $message) {
                $message->attachments = collect($message->messageAttachments)->map->mediaFile;
                
                // ---- Url Actions ---- //
                /** @var MessageUrl $urls */
                $urls = $message->messageUrls()->orderBy('index', 'asc')->get();
                $url_actions = [];
                foreach ($urls as $url) {
                    $act = $url->getFormattedActions();
                    $d_action = ['tags' => [], 'scenarios' => []];
                    $d_action['tags']['serves'] = $act['tag'];
                    $d_action['scenarios']['serves'] = $act['scenario'];
                    $url_actions[] = [
                        'id' => $url->id,
                        'url' => $url->url ,
                        'actions' => $d_action];
                }
                $message->url_actions = $url_actions;

                // --- Surveys --- //
                $survey = $this->getSurvey($message);
                $scenario->scenarioMessages[$key]->surveyQuestionnaire = $survey;

                $originalScheduleTime = date_create($message->schedule_date);
                $message->schedule_date = date_format($originalScheduleTime, 'Y-m-d');
                $message->schedule_time = date_format($originalScheduleTime, 'H:i:s');
                $message->is_edit = true;
            }

            // ---- Targets ---- //
            $serves = new stdClass();
            $excludes = new stdClass();
            $serveDates = new stdClass();
            $excludeDates = new stdClass();
            foreach ($scenario->scenarioTargets as $target) {
                if (isset($target->tag_management_id)) {
                    if ($target->is_exclude == 0) {
                        $serves->tag_management_id[$target->index]['value'][] = $target->tag_management_id;
                        $serves->tag_management_id[$target->index]['option'] = $target->option;
                    } else {
                        $excludes->tag_management_id[$target->index]['value'][] = $target->tag_management_id;
                        $excludes->tag_management_id[$target->index]['option'] = $target->option;
                    }
                } elseif (isset($target->scenario_id)) {
                    if ($target->is_exclude == 0) {
                        $serves->scenario_id[$target->index]['value'][] = $target->scenario_id;
                        $serves->scenario_id[$target->index]['option'] = $target->option;
                    } else {
                        $excludes->scenario_id[$target->index]['value'][] = $target->scenario_id;
                        $excludes->scenario_id[$target->index]['option'] = $target->option;
                    }
                } elseif (isset($target->start_at) && isset($target->end_at)) {
                    if ($target->is_exclude == 0) {
                        $serveDates = new stdClass();
                        $serveDates->from = $target->start_at;
                        $serveDates->to = $target->end_at;
                        $serves->date[$target->index]['value'] = $serveDates;
                    } else {
                        $excludeDates = new stdClass();
                        $excludeDates->from  = $target->start_at;
                        $excludeDates->to = $target->end_at;
                        $excludes->date[$target->index]['value'] = $excludeDates;
                    }
                }
            }
            // --  Serves -- //

            $target = new stdClass();
            $target->tags = new stdClass();
            $target->tags->serves = [];
            $target->scenarios = new stdClass();
            $target->scenarios->serves = [];
            $target->dates = new stdClass();
            $target->dates->serves = [];

            if (isset($serves->tag_management_id)) {
                foreach ($serves->tag_management_id as $tagManagementId) {
                    $targetTagServeValues['value'] =
                        TagManagement::findMany((array)$tagManagementId['value'])->pluck('title');
                    $targetTagServeValues['option'] = $tagManagementId['option'];
                    $target->tags->serves[] = $targetTagServeValues;
                }
            } else {
                $targetTagServeValues['value'] = [];
                $targetTagServeValues['option'] = 'first';
                $target->tags->serves[] = $targetTagServeValues;
            }

            if (isset($serves->scenario_id)) {
                foreach ($serves->scenario_id as $scenarioId) {
                    $targetScenarioServeValues['value'] =
                        Scenario::findMany((array)$scenarioId['value'])->pluck('name');
                    $targetScenarioServeValues['option'] = $scenarioId['option'];
                    $targetScenarioServeValues['day'] = 0;
                    $target->scenarios->serves[] = $targetScenarioServeValues;
                }
            } else {
                $targetScenarioServeValues['value'] = [];
                $targetScenarioServeValues['option'] = 'first';
                $target->scenarios->serves[] = $targetScenarioServeValues;
            }

            if (isset($serves->date)) {
                foreach ($serves->date as $date) {
                    $target->dates->serves[] = $date;
                }
            } else {
                $serveTargetDateValues['value'] = [];
                $target->dates->serves[] = $serveTargetDateValues;
            }

            // --  Excludes -- //

            $target->tags->excludes = [];
            $target->scenarios->excludes = [];
            $target->dates->excludes = [];

            if (isset($excludes->tag_management_id)) {
                foreach ($excludes->tag_management_id as $tagManagementId) {
                    $tagExcludeValues['value'] =
                        TagManagement::findMany((array)$tagManagementId['value'])->pluck('title');
                    $tagExcludeValues['option'] = $tagManagementId['option'];
                    $target->tags->excludes[] = $tagExcludeValues;
                }
            } else {
                $tagExcludeValues['value'] = [];
                $tagExcludeValues['option'] = 'first';
                $target->tags->excludes[] = $tagExcludeValues;
            }

            if (isset($excludes->scenario_id)) {
                foreach ($excludes->scenario_id as $scenarioId) {
                    $scenarioExcludeValues['value'] = Scenario::findMany((array)$scenarioId['value'])->pluck('name');
                    $scenarioExcludeValues['option'] = $scenarioId['option'];
                    $target->scenarios->excludes[] = $scenarioExcludeValues;
                }
            } else {
                $scenarioExcludeValues['value'] = [];
                $scenarioExcludeValues['option'] = 'first';
                $target->scenarios->excludes[] = $scenarioExcludeValues;
            }

            if (isset($excludes->date)) {
                foreach ($excludes->date as $date) {
                    $target->dates->excludes[] = $date;
                }
            } else {
                $excludeTargetDateValues['value'] = [];
                $target->dates->excludes[] = $excludeTargetDateValues;
            }

            $scenario->target = $target;

            // ---- Actions ---- //
            $actionServes = new stdClass();
            foreach ($scenario->scenarioActions as $scenarioAction) {
                if (isset($scenarioAction->tag_management_id)) {
                    $actionServes->tag_management_id[$scenarioAction->index]['value'][] = $scenarioAction->tag_management_id;
                    $actionServes->tag_management_id[$scenarioAction->index]['option'] = $scenarioAction->option;
                } elseif (isset($scenarioAction->scenario_id)) {
                    $actionServes->scenario_id[$scenarioAction->index]['value'][] = $scenarioAction->scenario_id;
                    $actionServes->scenario_id[$scenarioAction->index]['option'] = $scenarioAction->option;
                }
            }

            $action = new stdClass();
            $action->tags = new stdClass();
            $action->tags->serves = [];
            $action->scenarios = new stdClass();
            $action->scenarios->serves = [];

            if (isset($actionServes->tag_management_id)) {
                foreach ($actionServes->tag_management_id as $tagManagementId) {
                    $tagServeValues['value'] = TagManagement::findMany($tagManagementId['value'])->pluck('title');
                    $tagServeValues['option'] = $tagManagementId['option'];
                    $action->tags->serves[] = $tagServeValues;
                }
            } else {
                $tagServeValues['value'] = [];
                $tagServeValues['option'] = 'first';
                $action->tags->serves[] = $tagServeValues;
            }

            if (isset($actionServes->scenario_id)) {
                foreach ($actionServes->scenario_id as $scenarioId) {
                    $scenarioServeValues['value'] = Scenario::findMany($scenarioId['value'])->pluck('name');
                    $scenarioServeValues['option'] = $scenarioId['option'];
                    $scenarioServeValues['delivery'] = new stdClass();
                    $action->scenarios->serves[] = $scenarioServeValues;
                }
            } else {
                $scenarioServeValues['value'] = [];
                $scenarioServeValues['option'] = 'first';
                $scenarioServeValues['delivery'] = new stdClass();
                $action->scenarios->serves[] = $scenarioServeValues;
            }

            $scenario->action = $action;
        }
        return response()->json($scenarios, Response::HTTP_OK);
    }


    public function listsAllAccount(Request $request)
    {
        if ($request->input('type') == "New") {
            $user         = Auth::user();
            $accoundIds   = RoleUser::where('user_id', $user->id)->where('role_id', Role::ROLE_ACCOUNT_ADMINISTRATOR)->pluck('account_id');
            $scenarios    = Scenario::whereIn('account_id', $accoundIds)->get();
        } else {
            $scenarios = Scenario::where('account_id', $request->input('account_id'))->get();
        }

        return response()->json($scenarios, Response::HTTP_OK);
    }

    public function saveDraftMessage(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $message = $request->message;

            $scenario = $user->account->scenarios()->create([
                'name' => 'draft',
                'is_active' => $message['is_active'],
                'is_draft' => 1
            ]);

            $scenarioMessageAttachment = [];
            $message['account_id'] = $user->id;
            $message['is_draft'] = 1;
            $message['formatted_message'] = 'TMP';
            if (isset($message['schedule_date']) && isset($message['schedule_time'])) {
                $date = new Datetime($message['schedule_date']);
                $time = new Datetime($message['schedule_time']);
                $message['schedule_date'] = sprintf('%s %s', $date->format('Y-m-d'), $time->format('H:i:s'));
            } else {
                $message['schedule_date'] = date('Y-m-d H:i:s');
            }

            /** @var ScenarioMessage $scenarioMessage */
            $scenarioMessage = $scenario->scenarioMessages()->create($message);

            $scenarioMessage->recreateUrlAction($message['url_actions']);
            $messageUrls = $scenarioMessage->messageUrls()->orderBy('id')->get();
            $formattedMessage =
                $this->messageService->formatMessage($user->account_id, $message['content_message'], $messageUrls);
            $scenarioMessage->formatted_message = $formattedMessage;
            $scenarioMessage->save();

            if ($attachments = $message['attachments']) {
                foreach ($attachments as $attachment) {
                    if ($attachment) {
                        $attachment['media_file_id'] = $attachment['id'];
                        $scenarioMessage->messageAttachments()->create($attachment);
                        $scenarioMessageAttachment[] = $attachment;
                    }
                }
            }

            DB::commit();
            return response()->json(null, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            \Log::error($e);
            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error($e);
            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function draftMessages()
    {
        $draftScenarios = Auth::user()->account->scenarios->where('is_draft', 1)->all();

        foreach ($draftScenarios as $draftScenario) {
            foreach ($draftScenario->scenarioMessages as $scenarioMessage) {
                $scenarioMessage->attachments = collect($scenarioMessage->messageAttachments)->map->mediaFile;
            }
        }
        
        return response()->json(collect($draftScenarios)->flatMap->scenarioMessages, Response::HTTP_OK);
    }

    public function deleteDraftMessage($id)
    {
        try {
            DB::beginTransaction();
            /** @var ScenarioMessage $draftMessage */
            $draftMessage = collect(Auth::user()->account->scenarios)
                ->flatMap
                ->scenarioMessages
                ->first(function ($scenarioMessage, $key) use ($id) {
                    return $scenarioMessage->id == $id;
                });
            $draftMessage->messageAttachments()->delete();
            $draftMessage->messageUrls()->delete();
            $draftMessage->scenario()->delete();
            $draftMessage->delete();
            DB::commit();
            return response()->json(null, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            \Log::error($e);
            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error($e);
            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /*
    * アンケート保存
    */
    public function saveSurveys($data, $scenarioMessage, $user)
    {
        $surveyQuestionnaire                            = [];
        $surveyQuestionnaire['type_delivery']           = 'scenarios';
        $surveyQuestionnaire['account_id']              = $user->account_id;
        $surveyQuestionnaire['scenario_message_id']    = $scenarioMessage->id;

        $data['surveyQuestionnaire'] = (array)$data['surveyQuestionnaire'];
        $surveyQuestionnaire['text']                    = $data['surveyQuestionnaire']['text'];
        $surveyQuestionnaire['type_select_restriction'] = $data['surveyQuestionnaire']['type_select_restriction'];
        $surveyQuestionnaire['notification_message']    = $data['surveyQuestionnaire']['notification_message'];

        $data['surveyQuestionnaire']['actions'] = (array)$data['surveyQuestionnaire']['actions'];

        for ($i=1; $i <=4; $i++) {
            if (isset($data['surveyQuestionnaire']['actions'][$i-1])) {
                $data['surveyQuestionnaire']['actions'][$i-1] = (array)$data['surveyQuestionnaire']['actions'][$i-1];
            }
            $surveyQuestionnaire['action_'.$i.'_type']        = $data['surveyQuestionnaire']['actions'][$i-1]['type'] ?? null;
            $surveyQuestionnaire['action_'.$i.'_behavior']    = $data['surveyQuestionnaire']['actions'][$i-1]['behavior'] ?? null;
            $surveyQuestionnaire['action_'.$i.'_data']        = $data['surveyQuestionnaire']['actions'][$i-1]['data'] ?? null;
            $surveyQuestionnaire['action_'.$i.'_label']       = $data['surveyQuestionnaire']['actions'][$i-1]['label'] ?? null;
            $surveyQuestionnaire['action_'.$i.'_auto_reply']  = $data['surveyQuestionnaire']['actions'][$i-1]['auto_reply'] ?? null;
            $surveyQuestionnaire['action_'.$i.'_tag_add']     = $data['surveyQuestionnaire']['actions'][$i-1]['tag_add'] ?? null;
            $surveyQuestionnaire['action_'.$i.'_tag_delete']  = $data['surveyQuestionnaire']['actions'][$i-1]['tag_delete'] ?? null;
        }
        
        $survey = Survey::where('scenario_message_id', $scenarioMessage->id)->first();
        if ($survey) { //idを持つデータであれば、編集
            $survey->fill($surveyQuestionnaire)->save();
        } else { //シナリオメッセージidを持たないデータであれば、新規追加
            Survey::create($surveyQuestionnaire);
        }
    }


    /*
    * アンケート取得
    */
    public function getSurvey($message)
    {
        if ($message->content_type == 'survey') {
            $surveyQuestionnaire                          = new stdClass();
            $getSurvey                                    = Survey::where('scenario_message_id', $message->id)->first();
            if ($getSurvey) {
                $surveyQuestionnaire->id                      = $getSurvey->id;
                $surveyQuestionnaire->text                    = $getSurvey->text;
                $surveyQuestionnaire->notification_message    = $getSurvey->notification_message;
                $surveyQuestionnaire->type_select_restriction = $getSurvey->type_select_restriction;
                $surveyQuestionnaire->actions = [];
                for ($i=1; $i <= 4; $i++) {
                    if ($getSurvey->{'action_'.$i.'_type'} != null) {
                        $surveyQuestionnaireActions             = new stdClass();
                        $surveyQuestionnaireActions->action_no  = $i;
                        $surveyQuestionnaireActions->type       = $getSurvey->{'action_'.$i.'_type'};
                        $surveyQuestionnaireActions->behavior   = $getSurvey->{'action_'.$i.'_behavior'};
                        $surveyQuestionnaireActions->label      = $getSurvey->{'action_'.$i.'_label'};
                        $surveyQuestionnaireActions->data       = $getSurvey->{'action_'.$i.'_data'};
                        $surveyQuestionnaireActions->auto_reply = $getSurvey->{'action_'.$i.'_auto_reply'};
                        $surveyQuestionnaireActions->tag_add    = $getSurvey->{'action_'.$i.'_tag_add'};
                        $surveyQuestionnaireActions->tag_delete = $getSurvey->{'action_'.$i.'_tag_delete'};
                        if ($i==1) {
                            $surveyQuestionnaireActions->select = true ;
                        } else {
                            $surveyQuestionnaireActions->select = false ;
                        }
                        $surveyQuestionnaire->actions[] = $surveyQuestionnaireActions;
                    }
                }
            }
        } else {
            $surveyQuestionnaire                          = new stdClass();
            $surveyQuestionnaire->text                    = '';
            $surveyQuestionnaire->notification_message    = '';
            $surveyQuestionnaire->type_select_restriction = 'no_limit';
            $surveyQuestionnaireActions             = new stdClass();
            $i=1;
            $surveyQuestionnaireActions->action_no  = $i;
            $surveyQuestionnaireActions->type       = 'postback';
            $surveyQuestionnaireActions->behavior   = 'none';
            $surveyQuestionnaireActions->label      = '';
            $surveyQuestionnaireActions->data       = '';
            $surveyQuestionnaireActions->auto_reply = '';
            $surveyQuestionnaireActions->tag_add    = [];
            $surveyQuestionnaireActions->tag_delete = [];
            $surveyQuestionnaire->actions[] = $surveyQuestionnaireActions ;
        }
        return $surveyQuestionnaire;
    }
}
