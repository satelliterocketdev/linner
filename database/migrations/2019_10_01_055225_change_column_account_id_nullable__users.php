<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnAccountIdNullableUsers extends Migration
{

    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('account_id')->default(null)->nullable()->change();
        });
    }


    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('account_id')->nullable(false)->change();
        });
    }
}
