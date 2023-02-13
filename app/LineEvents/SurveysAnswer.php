<?php

namespace App\LineEvents;

use DB;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use App\Survey;
use App\SurveyAnswer;
use App\AccountFollower;
use App\PfUserTagManagement;

use Illuminate\Support\Facades\Log;

class SurveysAnswer
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function saveSurveysAnswer($event)
    {
        if(isset($event->postback->data)){
            parse_str($event->postback->data);
        }else{
            return;
        }

        if(isset($event->source->userId)){
            $userId = $event->source->userId;
        }else{
            return;
        }


        if(isset($test)){ //test時
            return;
        }

        $setData               = [];
        $survey                = Survey::find($survey_id);
        $setData['survey_id']  = $survey_id;
        $setData['answer_no']  = $answer_no;
        $accountFollower       = AccountFollower::where('source_user_id',$userId)->first();
        $setData['pf_user_id'] = $accountFollower['pf_user_id'];

        $tagIdList = PfUserTagManagement::where('pf_user_id',$setData['pf_user_id'])->pluck('tag_managements_id')->toArray();
        $addList    = $survey->{'action_'.$setData['answer_no'].'_tag_add'};
        $deleteList = $survey->{'action_'.$setData['answer_no'].'_tag_delete'};

        DB::beginTransaction();
        try {
        
            switch ($survey['type_select_restriction']){
                case 'no_limit':
                    SurveyAnswer::create($setData);
                    break;
                case 'per_choice':
                    $surveyAnswer = SurveyAnswer::where('survey_id',$setData['survey_id'])
                                ->where('answer_no',$setData['answer_no'])
                                ->where('pf_user_id',$setData['pf_user_id'])
                                ->first();
                    if ( $surveyAnswer == null) {
                        SurveyAnswer::create($setData);
                    }
                    break;
                case 'per_questionnaire':
                    $surveyAnswer = SurveyAnswer::where('survey_id',$setData['survey_id'])
                                ->where('pf_user_id',$setData['pf_user_id'])
                                ->first();
                    if ( $surveyAnswer == null) {
                        SurveyAnswer::create($setData);
                    }
                    break;
            }

            // -- タグ追加 -- //
            if(is_array($addList)){
                $addList = array_diff($addList, $tagIdList);
                foreach ($addList as $key => $addId) {
                    PfUserTagManagement::create([
                                'pf_user_id'=> $setData['pf_user_id'],
                                'tag_managements_id' => $addId
                    ]);
                }
            }

            // -- タグ除外 -- //
            if(is_array($deleteList)){
                $deleteList = array_intersect($deleteList, $tagIdList);
                foreach ($deleteList as $key => $deleteId) {
                    $deleteItem = PfUserTagManagement::where('pf_user_id',$setData['pf_user_id'])
                                         ->where('tag_managements_id',$deleteId)->first();
                    PfUserTagManagement::find($deleteItem->id)->delete();
                }
            }

            DB::commit();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
