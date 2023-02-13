<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnBlockedAccountFollowersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_followers', function (Blueprint $table) {
            $table->boolean('is_blocked')->default(0);
            // is_testerを誤ってintergerで作っていたのでbooleanに修正
            $table->boolean('is_tester')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('account_followers', function (Blueprint $table) {
            $table->dropColumn('is_blocked');
            $table->integer('is_tester')->change();
        });
    }
}
