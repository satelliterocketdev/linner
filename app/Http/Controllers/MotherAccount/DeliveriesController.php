<?php

namespace App\Http\Controllers\MotherAccount;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

use App\Account;
use App\TagManagement;
use App\Scenario;
use App\Magazine;
use App\Role;
use App\RoleUser;
use App\Survey;

class DeliveriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("mother_account.deliveries");
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
        try {
            $ids = explode(',', $id);
            foreach ($ids as $id) {
                $magazine = Magazine::findOrFail($id);
                $magazine->delete();
            }
            // TODO targets, actions, deliveriesのデータも削除すること
        } catch (ModelNotFoundException $e) {
            return response()->json(null, Response::HTTP_NOT_FOUND);
        }
        return response(null, Response::HTTP_OK);
    }

    public function list()
    {
        $user         = Auth::user();
        $accoundIds   = RoleUser::where('user_id', $user->id)->where('role_id', Role::ROLE_ACCOUNT_ADMINISTRATOR)->pluck('account_id');
        $magazines    = Magazine::with('account')->whereIn('account_id', $accoundIds)->get();
        $accountsList = Account::whereIn('id', $accoundIds)->select('id', 'name')->get();

        foreach ($magazines as $magazine) {
            $magazine->sent_count = $magazine->magazineDeliveries()->isSent()->count();

            //　添付
            $atts = [];
            foreach ($magazine->magazineAttachments as $attachment) {
                $media = $attachment->mediaFile()->get();
                if (count($media) > 0) {
                    array_push($atts, $media[0]);
                }
            }

            $magazine->attachments = $atts;

            // ターゲット
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
                        $serves->scenario_id[$target->index]['value'] = $target->scenario_id;
                        $serves->scenario_id[$target->index]['option'] = $target->option;
                    } else {
                        $excludes->scenario_id[$target->index]['value'] = $target->scenario_id;
                        $excludes->scenario_id[$target->index]['option'] = $target->option;
                    }
                } elseif (isset($target->start_at) && isset($target->start_at)) {
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

            $target = new \stdClass();
            $target->tags = new \stdClass();
            $target->tags->serves = [];
            $target->scenarios = new \stdClass();
            $target->scenarios->serves = [];
            $target->dates = new \stdClass();
            $target->dates->serves = [];


            if (isset($serves->tag_management_id)) {
                foreach ($serves->tag_management_id as $tagManagementId) {
                    $targetTagServeValues['value'] = TagManagement::findMany((array)$tagManagementId['value'])
                                                                    ->pluck('title');
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
                    $targetScenarioServeValues['value'] = Scenario::findMany((array)$scenarioId['value'])
                                                                    ->pluck('name');
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

            $target->tags->excludes = [];
            $target->scenarios->excludes = [];
            $target->dates->excludes = [];

            if (isset($excludes->tag_management_id)) {
                foreach ($excludes->tag_management_id as $tagManagementId) {
                    $tagExcludeValues['value'] = TagManagement::findMany((array)$tagManagementId['value'])
                                                                ->pluck('title');
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

            // アクション
            $actionServes = new \stdClass();
            $actionServes->tag_management_id = $magazine->magazineActions->pluck('tag_management_id');
            $actionServes->scenario_id = $magazine->magazineActions->pluck('scenario_id');

            $action = new \stdClass();
            $action->tags = new \stdClass();
            $action->tags->serves = [];
            $action->scenarios = new \stdClass();
            $action->scenarios->serves = [];

            $tagServeValues['value'] = isset($actionServes->tag_management_id) ?
                                        TagManagement::findMany($actionServes->tag_management_id)
                                        ->pluck('title') : [];
            $tagServeValues['option'] = 'first';
            $action->tags->serves[] = $tagServeValues;

            $scenarioServeValues['value'] = isset($actionServes->scenario_id) ?
                                            Scenario::findMany($actionServes->scenario_id)
                                            ->pluck('name') : [];
            $scenarioServeValues['option'] = 'first';
            $scenarioServeValues['delivery'] = new \stdClass();
            $action->scenarios->serves[] = $scenarioServeValues;

            $magazine->action = $action;

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
        }

        $user = Auth::user();
        $isAdmin = $user->hasRole(Role::ROLE_ACCOUNT_ADMINISTRATOR, $user->account->id);
        $canEdit = $isAdmin ? $isAdmin : $user
                    ->hasRole(Role::ROLE_SIMULTANEOUS_DISTRIBUTION_EDITING_IS_POSSIBLE, $user->account->id);

        $data = [
            'canEdit' => $canEdit,
            'magazines' => $magazines,
            'accountsList' => $accountsList
        ];

        return response()->json($data, Response::HTTP_OK);
    }

    public function cancelSchedules(Request $request, $id)
    {
        try {
            $ids = explode(',', $id);
            foreach ($ids as $id) {
                $magazine = Magazine::findOrFail($id);
                $magazine->schedule_at = null;
                $magazine->save();
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response(null, Response::HTTP_OK);
    }
}
