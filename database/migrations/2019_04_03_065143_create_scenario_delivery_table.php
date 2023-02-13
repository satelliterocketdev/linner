<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScenarioDeliveryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scenario_delivery', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('scenario_message_id');
            $table->string('type');
            $table->text('content');
            // $table->integer('follower_user_id');
            $table->text('follower_user_id');
            $table->datetime('schedule_date');
            $table->integer('is_attachment')->default(0);
            $table->integer('media_file_id')->default(0);
            $table->integer('is_sent')->default(0);
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
        Schema::dropIfExists('scenario_delivery');
    }
}
