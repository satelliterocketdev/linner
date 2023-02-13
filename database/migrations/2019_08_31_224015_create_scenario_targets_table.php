<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScenarioTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scenario_targets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('scenario_id');
            $table->integer('target_scenario_id');
            $table->integer('pf_use_id');
            $table->integer('tag_management_id');
            $table->boolean('is_exclude');
            $table->boolean('is_all');
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
        Schema::dropIfExists('scenario_targets');
    }
}
