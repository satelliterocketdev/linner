<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSurveyAnswers extends Migration
{
    public function up()
    {
        Schema::create('survey_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('survey_id')->comment('アンケートID');
            $table->integer('answer_no')->comment('回答結果No');
            $table->integer('pf_user_id')->comment('回答者ID');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('survey_answers');
    }
}
