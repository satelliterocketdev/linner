<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScenarioMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scenario_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('scenario_id')->nullable();
            $table->string('title');
            $table->string('content_type');
            $table->text('content_message')->nullable();
            $table->string('schedule_type')->nullable(); // 1 - immediate | 2 - specific
            $table->integer('schedule_number')->nullable();
            $table->dateTime('schedule_date')->nullable();
            $table->integer('is_active')->default(1); // 0 - Inactive | 1 - Active
            $table->integer('is_draft')->default(0); // 0 - No | 1 - Yes
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
        Schema::dropIfExists('scenario_messages');
    }
}
