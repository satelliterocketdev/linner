<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnsFromScenarioTargets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scenario_targets', function (Blueprint $table) {
            $table->integer('pf_user_id')->nullable()->change();
            $table->integer('tag_management_id')->nullable()->change();
            $table->integer('target_scenario_id')->nullable()->change();
            $table->date('start_at')->nullable();
            $table->date('end_at')->nullable();
            $table->dropColumn('is_all');
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
            $table->integer('pf_user_id')->nullable(false)->change();
            $table->integer('tag_management_id')->nullable(false)->change();
            $table->integer('target_scenario_id')->nullable(false)->change();
            $table->dropColumn('start_at');
            $table->dropColumn('end_at');
            $table->boolean('is_all');
        });
    }
}
