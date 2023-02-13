<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnAccountIdNullableRoleUsers extends Migration
{
    public function up()
    {
        Schema::table('role_users', function (Blueprint $table) {
            $table->integer('account_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('role_users', function (Blueprint $table) {
            $table->integer('account_id')->nullable(false)->change();
        });
    }
}
