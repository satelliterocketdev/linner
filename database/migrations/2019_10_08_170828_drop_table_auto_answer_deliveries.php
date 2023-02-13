<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropTableAutoAnswerDeliveries extends Migration
{
    public function up()
    {
        Schema::dropIfExists('auto_answer_deliveries');
    }

    public function down()
    {
        Schema::create('auto_answer_deliveries', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->integer('auto_answer_id')->comment('自動応答ID');
            $table->integer('pf_user_id');
            $table->boolean('is_attachment');
            $table->boolean('is_sent')->default(false);
            $table->timestamps();
        });
    }
}
