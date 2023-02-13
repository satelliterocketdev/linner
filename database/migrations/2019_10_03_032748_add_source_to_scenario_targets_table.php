<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSourceToScenarioTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scenario_targets', function (Blueprint $table) {
            $table->integer('source_scenario_id');
            $table->integer('index');
            $table->string('option');
            $table->integer('scenario_id')->nullable()->change();
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
            $table->dropColumn('source_scenario_id');
            $table->dropColumn('index');
            $table->dropColumn('option');
            $table->integer('scenario_id')->nullable(false)->change();
        });
    }
}
