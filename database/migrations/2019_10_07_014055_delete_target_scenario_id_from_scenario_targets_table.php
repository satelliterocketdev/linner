<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteTargetScenarioIdFromScenarioTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scenario_targets', function (Blueprint $table) {
            $table->dropColumn('target_scenario_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scenario_targets', function (Blueprint $table) {
            $table->integer('target_scenario_id')->nullable();
        });
    }
}
