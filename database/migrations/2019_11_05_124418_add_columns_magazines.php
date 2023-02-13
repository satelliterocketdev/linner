<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsMagazines extends Migration
{

    public function up()
    {
        Schema::table('magazines', function (Blueprint $table) {
            $table->string('content_type')->comment('message（メッセージ）,surveys（アンケート）');
        });
    }

    public function down()
    {
        Schema::table('magazines', function (Blueprint $table) {
            $table->dropColumn('content_type');
        });
    }
}
