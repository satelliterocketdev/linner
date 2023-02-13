<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnsFromScenarioActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scenario_actions', function (Blueprint $table) {
            $table->integer('tag_management_id')->nullable()->change();
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
        Schema::table('scenario_actions', function (Blueprint $table) {
            $table->integer('tag_management_id')->nullable(false)->change();
            $table->integer('scenario_id')->nullable(false)->change();
        });
    }
}
