<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessageUrlActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_url_actions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('message_url_id');
            $table->integer('type');
            $table->integer('index');
            $table->string('option');
            $table->integer('tag_management_id');
            $table->integer('scenario_id');
            $table->string('message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('message_url_actions');
    }
}
