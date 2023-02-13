<?php

namespace App\Http\Controllers;

use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Survey;
use App\SurveyAnswer;
use App\AccountFollower;
use App\PfUserTagManagement;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SurveyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('survey');
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
        return response()->json(Auth::user()->surveys()->create($request->all()), Response::HTTP_OK);
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

    public function lists()
    {
        $surveys = Survey::with('survey_answers')->where('account_id',Auth::user()->account_id)->get();

        foreach ($surveys as $key => $survey) {
            $answers_array = [];
            $surveys[$key]->answer_count = 0;
            $answer_count                = 0; 
            for ($i=1; $i<=4; $i++) { 
                $answers = ''; 
                if($survey->{'action_'.$i.'_type'} != null){
                    $answers_array[$i]['label']   = $survey->{'action_'.$i.'_label'};
                    $answers                      = SurveyAnswer::where('survey_id',$survey->id)
                                                                  ->where('answer_no',$i)
                                                                  ->orderBy('created_at', 'desc')
                                                                  ->get();
                    $answers_array[$i]['answers'] = $answers;
                    $answer_count                 = $answer_count + $answers->count();
                }
            }
            $surveys[$key]->answer_count = $answer_count;
            usort($answers_array, function ($a, $b) {
                return count($a['answers']) < count($b['answers']); // 回答数降順
            });
            $surveys[$key]->answers = $answers_array;
        }
        return response()->json($surveys, Response::HTTP_OK);
    }
    
    public function answer(Request $request)
    {
        $setData               = [];
        $survey                = Survey::find($request->input('survey_id'));
        $setData['survey_id']  = $request->input('survey_id');
        $setData['answer_no']  = $request->input('answer_no');

        // -- テスト配信アンケートからの回答時はuser_idをセットしていないので、それをフラグに集計を回避-- //
        if($request->input('user_id') == ''){
            $setData['auto_reply'] = $survey->{'action_'.$setData['answer_no'].'_auto_reply'};
            $setData['behavior']   = $survey->{'action_'.$setData['answer_no'].'_behavior'};
            $setData['data']   = $survey->{'action_'.$setData['answer_no'].'_data'};
            return view('survey_answer', ['setData' => $setData]);
        }

        $accountFollower       = AccountFollower::where('source_user_id',$request->input('user_id'))->first();
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

        $setData['auto_reply'] = $survey->{'action_'.$setData['answer_no'].'_auto_reply'};
        $setData['behavior']   = $survey->{'action_'.$setData['answer_no'].'_behavior'};
        $setData['data']   = $survey->{'action_'.$setData['answer_no'].'_data'};

        return view('survey_answer', ['setData' => $setData]);

    }
}
