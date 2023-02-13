<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Exception;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\AutoAnswer;
use APP\AutoAnswerKeyword;

use App\Role;

class AutoAnswerSettingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole(Role::ROLE_ACCOUNT_ADMINISTRATOR, $user->account->id);
        $canEdit = $isAdmin ? $isAdmin : $user
                    ->hasRole(Role::ROLE_SIMULTANEOUS_DISTRIBUTION_EDITING_IS_POSSIBLE, $user->account->id);

        return view('auto_answer_setting')->with('canEdit', $canEdit);
    }

    public function lists()
    {
        $user    = Auth::user();
        $auto_answers = AutoAnswer::with(['AutoAnswerKeyword'])->where('account_id', $user->account_id )->get();

        /*
        * キーワードセット
        */ 
        foreach ($auto_answers as $key => $auto_answer) {
            $set_keywords = '';
            foreach ($auto_answer->AutoAnswerKeyword as $keyword) {
                $set_keywords .= $keyword['keyword'] . ',' ;
            }
            $set_keywords = rtrim($set_keywords, ",");
            $auto_answers[$key]->keyword = $set_keywords ;
        }

        /*
        * 曜日設定セットjson文字列からデコード
        */
        foreach ($auto_answers as $key => $auto_answer) {
            $auto_answers[$key]->week = json_decode($auto_answer->week,true);
            //LOG::debug($auto_answers[$key]->week);
        }

        /*
        * 時刻データセット
        */
        foreach ($auto_answers as $key => $auto_answer) {
            $auto_answers[$key]->from_time = mb_substr($auto_answer->from_time,0,5);
            $auto_answers[$key]->to_time  = mb_substr($auto_answer->to_time,0,5 );
        }

        return response()->json($auto_answers, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     */
    public function store(Request $request)
    {
        $user                = Auth::user();
        $form_data           = $request->all();
        $request->account_id = $user->account->id;

        /*
        * 曜日指定のオブジェクトデータをセット
        */
        $form_data['week'] = $this->weekSet($form_data);

        /*
        * 常に or 条件指定があるか のフラグ
        */
        $form_data['is_always'] = $form_data['condition'] == 'specified' ? 0 : 1;

        /*
        * キーワード設定（キーワードが空でなければ、配列として格納）
        */
        if($form_data['keyword'] != null and $form_data['keyword'] != ''){
            $key_words = explode(',',$form_data['keyword']);
        }else{
            $key_words = null ;
        }
        unset($form_data['keyword']);

        /*
        * 開始時間・終了時間の設定
        */
        $time_set               = $this->timeSet($form_data);
        $form_data['from_time'] = $time_set['from_time'];
        $form_data['to_time']   = $time_set['to_time'];
        unset($form_data['is_timeset']);
        unset($form_data['condition']);

        DB::beginTransaction();
        try {
            /*
            * autoanswerテーブル保存
            */
            $autoanswer = $user->account->autoAnswers()->create($form_data);

            if(!$autoanswer){
                throw new Exception('Failed to save');
            }

            /* 
            * キーワード登録
            */
            if(is_array($key_words)){
                foreach ($key_words as $key_word) {
                    $keyword = \App\AutoAnswerKeyword::create(['auto_answer_id'=> $autoanswer->id ,'keyword'=> $key_word ]);
                    if(!$keyword){
                        throw new Exception('Failed to save');
                    }
                }
            }

            DB::commit();

        } catch (\PDOException $e) {
            Log::error($e);
            DB::rollBack();
            throw $e;
        }
        return response()->json($autoanswer, Response::HTTP_OK);
    }



    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $form_data = $request->all();
        $request->account_id = $user->account->id;

        /*
        * 曜日指定のオブジェクトデータをセット
        */
        $form_data['week'] = $this->weekSet($form_data);
        
        /*
        * 常に or 条件指定があるか のフラグ
        */
        $form_data['is_always'] = $form_data['condition'] == 'specified' ? 0 : 1;

        /*
        * 開始時間・終了時間の設定
        */
        $time_set               = $this->timeSet($form_data);
        $form_data['from_time'] = $time_set['from_time'];
        $form_data['to_time']   = $time_set['to_time'];
        unset($form_data['is_timeset']);
        unset($form_data['condition']);


        /*
        * キーワード設定
        */
        if($form_data['keyword'] != null and $form_data['keyword'] != ''){
            $key_words = explode(',',$form_data['keyword']);
        }else{
            $key_words = null ;
        }
        unset($form_data['keyword']);

        DB::beginTransaction();
        try {

            /*
            * autoanswerテーブル保存
            */
            $auto_answer = AutoAnswer::findOrFail($id);
            if(!$auto_answer->fill($form_data)->save()){
                throw new Exception('Failed to save');
            }

            /* 
            * キーワード登録
            */
            DB::table('auto_answer_keywords')->where('auto_answer_id',$id )->delete(); //一旦すべてを削除
            if(is_array($key_words)){
                foreach ($key_words as $key_word) {
                    $keyword = \App\AutoAnswerKeyword::create(['auto_answer_id'=> $id ,'keyword'=> $key_word ]);
                    if(!$keyword){
                        throw new Exception('Failed to save');
                    }
                }
            }
            DB::commit();

        } catch (ModelNotFoundException $e) {
            Log::error($e);
            DB::rollBack();
            throw $e;
        }
        return response(null, Response::HTTP_OK);
    }


    public function changeEnable(Request $request)
    {
        /*
        * 有効/無効の切り替え
        */
        $auto_answer = AutoAnswer::findOrFail($request->input('id'));
        $set_data = $auto_answer->is_active == 1 ? 0 : 1 ; // 状態によってセットするデータをきりかえる
        AutoAnswer::where('id', $request->input('id'))->update(['is_active' => $set_data]);
        return response(null, Response::HTTP_OK);
    }

    public function destroy($id)
    {
        try {
            $ids = explode(',', $id);
            foreach ($ids as $id) {
                $auto_answer = AutoAnswer::findOrFail($id);
                $auto_answer->delete();
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(null, Response::HTTP_NOT_FOUND);
        }
        return response(null, Response::HTTP_OK);
    }

    public function copy(Request $request)
    {

        $user = Auth::user();
        $auto_answer = AutoAnswer::findOrFail($request->input('id'));
        $copy_auto_answer_att = $auto_answer->toArray();
        $copy_auto_answer_att["title"] .= ' - copy';
        $auto_answer_id = $copy_auto_answer_att["id"];
        unset($copy_auto_answer_att["id"]);

        try {

            /*
            * autoAnswers（自動応答メッセージ）テーブルコピー
            */
            $copy_auto_answer = $user->account->autoAnswers()->create($copy_auto_answer_att);
            if(!$copy_auto_answer){
                throw new Exception('Failed to save');
            }

            /*
            * AutoAnswerKeyword（キーワード）テーブルコピー
            */
            $auto_answer_keywords = \App\AutoAnswerKeyword::where('auto_answer_id',$auto_answer_id)->get();
            foreach ($auto_answer_keywords as $keyword) {
                $keyword_array = $keyword->toArray();
                $keyword_array['auto_answer_id'] = $copy_auto_answer->id;
                unset($keyword_array['id']);
                $set_keyword = \App\AutoAnswerKeyword::create($keyword_array);
                if(!$set_keyword){
                    throw new Exception('Failed to save');
                }
            }

        } catch (ModelNotFoundException $e) {
            Log::error($e);
            DB::rollBack();
            throw $e;
        }
        return response()->json(null, Response::HTTP_OK);
    }


    public function weekSet($form_data)
    {
        /*
        * 曜日データをオブジェクトとしてDBに格納
        */
        $form_set_weeks = $form_data['week'];
        $week_json = [];
        $weeks= [ '日','月','火','水','木','金','土'];

        foreach ($form_set_weeks as $key => $week) {
            $week_json[$key]['label'] = $week['label'];
            if($form_data['condition'] == 'specified'){ 
                $week_json[$key]['value'] = $week['value'];
            }elseif($form_data['condition'] == 'always'){
                $week_json[$key]['value'] = true ;
            }
        }

        return json_encode($week_json, JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE);
    }

    public function timeSet($form_data)
    {
        if(
            $form_data['is_timeset'] == 1 && 
            $form_data['condition'] == 'specified' && 
            $form_data['from_time'] != null && 
            $form_data['to_time'] != null
        ){
            $set_time['from_time'] = date("H:i:00",strtotime($form_data['from_time']));
            $set_time['to_time']   = date("H:i:00",strtotime($form_data['to_time']));
        }else{
            $set_time['from_time'] = null;
            $set_time['to_time']   = null;
        }
        return $set_time;
    }
    
}
