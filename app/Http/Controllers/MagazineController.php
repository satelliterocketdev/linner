<?php

namespace App\Http\Controllers;

use App\MagazineAction;
use DateTime;
use DB;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Magazine;
use App\MagazineAttachment;
use App\TemplateMessage;
use App\TemplateMessageAttachment;
use App\Survey;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\TagManagement;
use App\TagsFolders;
use App\Scenario;
use App\PfUser;
use App\MagazineTarget;
use App\Services\MessageService;

use App\Role;
use Illuminate\View\View;
use Log;

class MagazineController extends Controller
{
    protected $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    /**
     * /magazine 初期表示
     * @return Factory|View
     */
    public function index()
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole(Role::ROLE_ACCOUNT_ADMINISTRATOR, $user->account->id);
        $canEdit = $isAdmin ? $isAdmin : $user
                    ->hasRole(Role::ROLE_SIMULTANEOUS_DISTRIBUTION_EDITING_IS_POSSIBLE, $user->account->id);

        return view('magazine')->with('canEdit', $canEdit);
    }

//    /**
//     * @param Magazine $magazine
//     * @param $data
//     * @param $option
//     * @param $index
//     */
//    private function storeTagAction($magazine, $data, $option, $index)
//    {
//        $tags = $data;
//        $tagManagementIds = TagManagement::whereIn('title', $tags)->pluck('id');
//
//        foreach ($tagManagementIds as $tagManagementId) {
//            $magazine->magazineActions()->create([
//                'magazine_id' => $magazine->id,
//                'type' => 0,
//                'tag_management_id' => $tagManagementId,
//                'index' => $index,
//                'option' => $option
//            ]);
//        }
//    }

//    /**
//     * @param Magazine $magazine
//     * @param $data
//     * @param $option
//     * @param $index
//     */
//    private function storeScenarioAction($magazine, $data, $option, $index)
//    {
//        $magazines = $data;
//        if (!empty($magazines)) {
//            $scenarioTargetId = Scenario::where('name', $magazines)->value('id');
//
//            if (!empty($scenarioTargetId)) {
//                $magazine->magazineActions()->create([
//                    'magazine_id' => $magazine->id,
//                    'type' => 1,
//                    'scenario_id' => $scenarioTargetId,
//                    'index' => $index,
//                    'option' => $option
//                ]);
//            }
//        }
//    }

    /**
     * @param Request $request
     * @param null $account_id
     * @param bool $isMany
     * @return JsonResponse
     */
    public function store(Request $request, $account_id = null, $isMany = false)
    {
        try {
            DB::beginTransaction();
            $user = Auth::user();
//            $account_followers = $user->account->accountFollowers;
            $data = $request->all();

            //全アカウント配信新規登録画面では、⬆引数 $account_id が設定されている。
            $account_id = $account_id == null ? $user->account_id : $account_id;

            $magazine = Magazine::create([
                'account_id' => $account_id,
                'title' => $data['title'],
                'content_message' => $data['content_message'],
                'formatted_message' => 'TMP',
                'content_type' => $data['content_type'],
                'schedule_at' => empty($data['schedule_at']) ?
                    null :
                    (new DateTime($data['schedule_at']))->format('Y-m-d H:i:s'),
                'is_draft' => $data['is_draft'],
            ]);

            // URL Action
            $magazine->recreateUrlAction($request->url_actions);
            $messageUrls = $magazine->messageUrls()->orderBy('id')->get();
            $formattedMessage =
                $this->messageService->formatMessage($account_id, $data['content_message'], $messageUrls);
            $magazine->formatted_message = $formattedMessage;
            $magazine->save();

            if ($attachments = $data['attachments']) {
                foreach ($attachments as $attachment) {
                    if ($attachment) {
                        MagazineAttachment::create([
                            'magazine_id' => $magazine->id,
                            'media_file_id' => $attachment['id']
                        ]);
                    }
                }
            }

            $targets = json_decode($request->target, true);
            $magazine->createStoreTargets($targets);

            // Deliveries
            if (isset($magazine->schedule_at)) {
                $pfUsers = $magazine->getPfUsers(
                    isset($tagsServes) ? $tagsServes : null,
                    isset($scenariosServes) ? $scenariosServes : null,
                    isset($dateServes) ? $dateServes : null
                );

                /** @var PfUser $pfUser */
                foreach ($pfUsers as $pfUser) {
                    $pfUser->magazineDeliveries()->create([
                        'magazine_id' => $magazine->id,
                        'is_attachment' => 0,
                        'is_sent' => 0
                    ]);

                    if ($request['content_type'] != 'survey') {
                        if ($magazine->magazineAttachments()->count()) {
                            $pfUser->magazineDeliveries()->update(['is_attachment' => 1]);
                        }
                    }
                }
            }

            $actions = json_decode($request->action, true);

            $magazine->createStoreActions($actions);

            // -- Surveys登録 --//
            if ($data['content_type'] == 'survey') {
                $this->saveSurveys($data, $magazine, $account_id);
            }

            DB::commit();
            return response()->json($magazine, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function storeMany(Request $request)
    {
        $data = $request->all();
        $isMany = true;
        foreach ($data['select_account'] as $key => $account_id) {
            $this->store($request, $account_id, $isMany);
        }
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function saveDraftMessage(Request $request)
    {
        return $this->store($request);
    }

    public function draftMessages()
    {
        return $this->lists(1);
    }

    /**
     * @param Request $request
     * @param $id
     * @return ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $isMany = $request->input('is_many');
            if ($isMany) { //全アカウント配信メッセージの編集時は編集対象メッセージに紐づくアカウントIDが必要
                $account_id = $request->input('account_id');
            } else {
                $user = Auth::user();
                $account_id = $user->account_id;
            }

            /** @var Magazine $magazine */
            $magazine = Magazine::findOrFail($id);

            if (isset($request->schedule_at)) {
                $date = new Datetime($request->schedule_at);
                $request->schedule_at = $date->format('Y-m-d H:i:s');
            } else {
                $request->schedule_at = null;
            }

            // URL Action
            $magazine->recreateUrlAction($request->url_actions);
            $messageUrls = $magazine->messageUrls()->orderBy('id')->get();
            $formattedMessage =
                $this->messageService->formatMessage($account_id, $request->content_message, $messageUrls);

            $magazine->fill([
                'account_id' => $account_id,
                'title' => $request->title,
                'content_message' => $request->content_message,
                'formatted_message' => $formattedMessage,
                'is_active' => $request->is_active,
                'is_draft' => $request->is_draft,
                'schedule_at' => $request->schedule_at,
                'content_type' => $request->content_type
            ])->save();

            $magazine->magazineAttachments()->delete();

            foreach ($request->attachments as $attachment) {
                $magazine->magazineAttachments()->create(['media_file_id' => $attachment['id']]);
            }

            // -- Actions更新 --//

            $magazine->magazineActions()->delete();
            $actions = json_decode($request->action, true);

            $magazine->createStoreActions($actions);

            // -- Targets更新 --//

            $magazine->magazineTargets()->delete();
            $target = json_decode($request->target, true);
            $magazine->createStoreTargets($target);

            // Deliveries
            if (isset($magazine->schedule_at)) {
                $pfUsers = $magazine->getPfUsers(
                    isset($targetTagsServes) ? $targetTagsServes : null,
                    isset($targetScenariosServes) ? $targetScenariosServes : null,
                    isset($dateServes) ? $dateServes : null
                );

                $magazine->magazineDeliveries()->where('is_sent', 0)->delete();
                /** @var PfUser $pfUser */
                foreach ($pfUsers as $pfUser) {
                    if ($pfUser->magazineDeliveries()->where('magazine_id', $magazine->id)->count() !== 0) {
                        continue;
                    }

                    $pfUser->magazineDeliveries()->create([
                        'magazine_id' => $magazine->id,
                        'is_attachment' => 0,
                        'is_sent' => 0
                    ]);

                    if ($request['content_type'] != 'survey') {
                        if ($magazine->magazineAttachments()->count()) {
                            $pfUser->magazineDeliveries()->update([
                                'is_attachment' => 1,
                            ]);
                        }
                    }
                }
            }
            
            // -- Surveys更新 --//
            if ($request['content_type'] == 'survey') {
                $this->saveSurveys($request, $magazine, $account_id);
            }

            DB::commit();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response(null, Response::HTTP_OK);
    }
    
    /**
     * @param $id
     * @return ResponseFactory|JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $ids = explode(',', $id);
            foreach ($ids as $id) {
                /** @var Magazine $magazine */
                $magazine = Magazine::findOrFail($id);
                $magazine->delete();

                $magazine->magazineDeliveries()->delete();
                $magazine->magazineTargets()->delete();
                $magazine->magazineActions()->delete();
                $magazine->magazineAttachments()->delete();
                $urls = $magazine->messageUrls()->get();
                foreach ($urls as $url) {
                    // deleting監視のため個別に削除
                    $url->delete();
                }
            }
            DB::commit();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response(null, Response::HTTP_OK);
    }

    /**
     * コピー
     * @param Request $request
     * @return JsonResponse
     */
    public function copy(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = Auth::user();

            $magazine = Magazine::findOrFail($request->input('id'));
            /** @var Magazine $copyMagazine */
            $copyMagazine = $magazine->replicate();
            $copyMagazine->title = $magazine->title . ' - copy';
            $copyMagazine->schedule_at = null;
            $copyMagazine->save();

            $magazineTargets = $magazine->magazineTargets;
            /** @var MagazineTarget $magazineTarget */
            foreach ($magazineTargets as $magazineTarget) {
                /** @var MagazineTarget $copyMagazineTarget */
                $copyMagazineTarget = $magazineTarget->replicate();
                $copyMagazineTarget->magazine_id = $copyMagazine->id;
                $copyMagazineTarget->save();
            }

            $magazineActions = $magazine->magazineActions;
            /** @var MagazineAction $magazineAction */
            foreach ($magazineActions as $magazineAction) {
                /** @var MagazineAction $copyMagazineAction */
                $copyMagazineAction = $magazineAction->replicate();
                $copyMagazineAction->magazine_id = $copyMagazine->id;
                $copyMagazineAction->save();
            }

            $magazineAttachments = $magazine->magazineAttachments;
            /** @var MagazineAttachment $magazineAttachment */
            foreach ($magazineAttachments as $magazineAttachment) {
                /** @var MagazineAttachment $copyMagazineAttachment */
                $copyMagazineAttachment = $magazineAttachment->replicate();
                $copyMagazineAttachment->magazine_id = $copyMagazine->id;
                $copyMagazineAttachment->save();
            }

            /** @var Survey $magazineSurvey */
            $magazineSurvey                           = $magazine->survey;
            if ($magazineSurvey != null) {
                $setSurvey                            = $magazineSurvey->replicate();
                $setSurvey['account_id']              = $magazine->account_id;
                $setSurvey['magazine_id']             = $copyMagazine->id;
                $setSurvey['type_delivery']           = 'magazines';
//                for ($i=1; $i<=4; $i++) {
//                    $setSurvey['action_'.$i.'_type']       = $magazineSurvey->{'action_'.$i.'_type'};
//                    $setSurvey['action_'.$i.'_behavior']   = $magazineSurvey->{'action_'.$i.'_behavior'};
//                    $setSurvey['action_'.$i.'_label']      = $magazineSurvey->{'action_'.$i.'_label'};
//                    $setSurvey['action_'.$i.'_data']       = $magazineSurvey->{'action_'.$i.'_data'};
//                    $setSurvey['action_'.$i.'_auto_reply'] = $magazineSurvey->{'action_'.$i.'_auto_reply'};
//                    $setSurvey['action_'.$i.'_tag_add']    = $magazineSurvey->{'action_'.$i.'_tag_add'};
//                    $setSurvey['action_'.$i.'_tag_delete'] = $magazineSurvey->{'action_'.$i.'_tag_delete'};
//                }
//                $copyMagazine->survey()->create($setSurvey);
                $setSurvey->save();
            }
            
            DB::commit();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json(null, Response::HTTP_OK);
    }

    /**
     * get /magazine/lists 一覧データ取得
     * @return JsonResponse
     */
    public function lists($isDraft = 0)
    {
        $user = Auth::user();

        // 一斉データの取得元 下書き保存を除外
        $magazines = $user->account->magazines->where('is_draft', $isDraft)->sortByDesc(function ($magazine) {
            return $magazine->schedule_at;
        })->values()->all();

        foreach ($magazines as $magazine) {

            // 送付対象を取得する（0: 全員、0以外: 指定あり）
            $magazine->target_count = $magazine->magazineTargets()->count();
            // 配信完了人数を取得する
            $magazine->sent_count = $magazine->magazineDeliveries()->isSent()->count();
            // ターゲットの取得
            // $magazine->targets = $magazine->magazineTargets()->orderBy('index');

            $magazine->attachments = collect($magazine->magazineAttachments)->map->mediaFile;

            // ---- Targets ---- //

            $serves = new \stdClass();
            $excludes = new \stdClass();
            $serveDates = new \stdClass();
            $excludeDates = new \stdClass();
            foreach ($magazine->magazineTargets as $target) {
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
                        $serveDates = new \stdClass();
                        $serveDates->from = $target->start_at;
                        $serveDates->to = $target->end_at;
                        $serves->date[$target->index]['value'] = $serveDates;
                    } else {
                        $excludeDates = new \stdClass();
                        $excludeDates->from  = $target->start_at;
                        $excludeDates->to = $target->end_at;
                        $excludes->date[$target->index]['value'] = $excludeDates;
                    }
                }
            }
            
            // --  Serves -- //

            $target = new \stdClass();
            $target->tags = new \stdClass();
            $target->tags->serves = [];
            $target->scenarios = new \stdClass();
            $target->scenarios->serves = [];
            $target->dates = new \stdClass();
            $target->dates->serves = [];


            if (isset($serves->tag_management_id)) {
                foreach ($serves->tag_management_id as $tagManagementId) {
                    $targetTagServeValues['value'] = TagManagement::findMany((array)$tagManagementId['value'])->pluck('title');
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
                    $targetScenarioServeValues['value'] = Scenario::findMany((array)$scenarioId['value'])->pluck('name');
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
                    $tagExcludeValues['value'] = TagManagement::findMany((array)$tagManagementId['value'])->pluck('title');
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
            
            $magazine->target = $target;

            // ---- Actions ---- //

            $actionServes = new \stdClass();
            $actionServes->tag_management_id = $magazine->magazineActions->pluck('tag_management_id');
            $actionServes->scenario_id = $magazine->magazineActions->pluck('scenario_id');

            $action = new \stdClass();
            $action->tags = new \stdClass();
            $action->tags->serves = [];
            $action->scenarios = new \stdClass();
            $action->scenarios->serves = [];

            // --  Serves -- //

            $tagServeValues['value'] = isset($actionServes->tag_management_id) ? TagManagement::findMany($actionServes->tag_management_id)->pluck('title') : [];
            $tagServeValues['option'] = 'first';
            $action->tags->serves[] = $tagServeValues;

            $scenarioServeValues['value'] = isset($actionServes->scenario_id) ? Scenario::findMany($actionServes->scenario_id)->pluck('name') : [];
            $scenarioServeValues['option'] = 'first';
            $scenarioServeValues['delivery'] = new \stdClass();
            $action->scenarios->serves[] = $scenarioServeValues;

            $magazine->action = $action;

            // ---- Url Actions ---- //
            $urls = $magazine->messageUrls()->orderBy('index', 'asc')->get();
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
            $magazine->url_actions = $url_actions;

            // --  Surveys_Questionnaire -- //

            if ($magazine->content_type == 'survey') {
                $surveyQuestionnaire                          = new \stdClass();
                $getSurvey                                    = Survey::where('magazine_id', $magazine->id)->first();
                $surveyQuestionnaire->text                    = $getSurvey->text;
                $surveyQuestionnaire->notification_message    = $getSurvey->notification_message;
                $surveyQuestionnaire->type_select_restriction = $getSurvey->type_select_restriction;
                $surveyQuestionnaire->actions = [];

                for ($i=1; $i <= 4; $i++) {
                    if ($getSurvey->{'action_'.$i.'_type'} != null) {
                        $surveyQuestionnaireActions             = new \stdClass();
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
                $magazine->surveyQuestionnaire = $surveyQuestionnaire;
            } else {
                $surveyQuestionnaire                          = new \stdClass();
                $surveyQuestionnaire->text                    = '';
                $surveyQuestionnaire->notification_message    = '';
                $surveyQuestionnaire->type_select_restriction = 'no_limit';
                $surveyQuestionnaireActions             = new \stdClass();
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
                $magazine->surveyQuestionnaire = $surveyQuestionnaire;
            }
            // -------- //   // -------- //
        }
        return response()->json($magazines, Response::HTTP_OK);
    }

    public function saveTemplate(Request $request)
    {
        try {
            $user = Auth::user();
            $data = $request->all();

            $template_message = TemplateMessage::create([
                'account_id' => $user->account_id,
                'title' => $data['title'],
                'content_type' => $data['content_type'],
                'content_message' => $data['content_message'],
                'is_active' => $data['is_active'],
                'is_draft' => $data['is_draft']
            ]);

            if ($attachments = $data['attachments']) {
                foreach ($attachments as $attachment) {
                    if ($attachment) {
                        $template_message_attachment = TemplateMessageAttachment::create([
                            'template_message_id' => $template_message->id,
                            'media_file_id' => $attachment['id']
                        ]);
                    }
                }
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(null, Response::HTTP_NOT_FOUND);
        }

        return response()->json(null, Response::HTTP_OK);
    }

    public function schedule(Request $request)
    {
        try {
            $magazine = Magazine::findOrFail($request->id);
            $magazine->schedule_at = $request->schedule_at;
            $magazine->save();
        } catch (ModelNotFoundException $e) {
            return response()->json(null, Response::HTTP_NOT_FOUND);
        }
        return response()->json(null, Response::HTTP_OK);
    }

    public function deleteDraftMessage($id)
    {
        try {
            DB::beginTransaction();
            $draftMessage = Auth::user()->account->magazines->first(function ($magazine, $key) use ($id) {
                return $magazine->id == $id;
            });
            $draftMessage->magazineAttachments()->delete();
            $draftMessage->messageUrls()->delete();
            $draftMessage->delete();

            DB::commit();
            return response()->json(null, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /*
    * アンケート保存
    */
    public function saveSurveys($data, $magazine, $account_id)
    {

            $surveyQuestionnaire                            = [];
            $surveyQuestionnaire['type_delivery']           = 'magazines';
            $surveyQuestionnaire['account_id']              = $account_id;
            $surveyQuestionnaire['magazine_id']             = $magazine->id;

            $surveyQuestionnaire['text']                    = $data['survey_questionnaire']['text'];
            $surveyQuestionnaire['type_select_restriction'] = $data['survey_questionnaire']['type_select_restriction'];
            $surveyQuestionnaire['notification_message']    = $data['survey_questionnaire']['notification_message'];

            for ($i=1; $i <=4; $i++) {
                $surveyQuestionnaire['action_'.$i.'_type']        = $data['survey_questionnaire']['actions'][$i-1]['type'] ?? null;
                $surveyQuestionnaire['action_'.$i.'_behavior']    = $data['survey_questionnaire']['actions'][$i-1]['behavior'] ?? null;
                $surveyQuestionnaire['action_'.$i.'_data']        = $data['survey_questionnaire']['actions'][$i-1]['data'] ?? null;
                $surveyQuestionnaire['action_'.$i.'_label']       = $data['survey_questionnaire']['actions'][$i-1]['label'] ?? null;
                $surveyQuestionnaire['action_'.$i.'_auto_reply']  = $data['survey_questionnaire']['actions'][$i-1]['auto_reply'] ?? null;
                $surveyQuestionnaire['action_'.$i.'_tag_add']     = $data['survey_questionnaire']['actions'][$i-1]['tag_add'] ?? null;
                $surveyQuestionnaire['action_'.$i.'_tag_delete']  = $data['survey_questionnaire']['actions'][$i-1]['tag_delete'] ?? null;
            }

            $survey = Survey::where('magazine_id', $magazine->id )->first();

            if ($survey == null) {
                Survey::create($surveyQuestionnaire);
            } else {
                $survey = Survey::find($survey->id);
                // Array to string conversion  が出るためupdateメソッドは使えないので
                // 代替えとしてsaveメソッドを使用するための記述
                foreach ($surveyQuestionnaire as $key => $value) {
                    $survey->{$key} = $value;
                }
                $survey->save();
            }
    }
}
