<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnFollowerUserIdInScenarioDelivery extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scenario_delivery', function (Blueprint $table) {
            $table->renameColumn('follower_user_id', 'pf_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scenario_delivery', function (Blueprint $table) {
            $table->renameColumn('pf_user_id', 'follower_user_id');
        });
    }
}
