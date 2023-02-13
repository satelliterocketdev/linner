<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsIsAlwaysToAutoAnswers extends Migration
{

    public function up()
    {
        Schema::table('auto_answers', function (Blueprint $table) {
            $table->integer('is_always')->default(1)->comment('0=条件指定,1=常に');
        });
    }

    function down()
    {
        Schema::table('auto_answers', function (Blueprint $table) {
            $table->dropColumn('is_always');
        });
    }
}
