<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeMagazineTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magazine_targets', function (Blueprint $table) {
            $table->integer('magazine_id')->nullable()->change();
            $table->integer('scenario_id')->nullable();
            $table->integer('pf_user_id')->nullable()->change();
            $table->integer('tab_management_id')->nullable()->change();
            $table->boolean('is_exclude')->default(false)->comment('除外判定');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('magazine_targets', function (Blueprint $table) {
            $table->integer('magazine_id')->nullable(false)->change();
            $table->dropColumn('scenario_id');
            $table->integer('pf_user_id')->nullable(false)->change();
            $table->integer('tab_management_id')->nullable(false)->change();
            $table->dropColumn('is_exclude');
        });
    }
}
