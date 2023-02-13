<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnSourceUserIdFromAccountFollowersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_followers', function (Blueprint $table) {
            $table->string('source_user_id')->nullable(false)->change();
            $table->boolean('is_visible')->default(false);
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
            $table->string('source_user_id')->nullable()->change();
            $table->dropColumn('is_visible');
        });
    }
}
