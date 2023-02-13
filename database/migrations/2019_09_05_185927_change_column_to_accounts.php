<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnToAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('link_token')->nullable()->change();
            $table->string('line_follow_link')->nullable()->change();
            $table->string('line_add')->nullable()->change();
            $table->string('profile_image')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('link_token')->nullable(false)->change();
            $table->string('line_follow_link')->nullable(false)->change();
            $table->string('line_add')->nullable(false)->change();
            $table->string('profile_image')->nullable(false)->change();
        });
    }
}
