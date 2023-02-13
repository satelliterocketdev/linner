<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsIsActiveToAutoAnswers extends Migration
{

    public function up()
    {
        Schema::table('auto_answers', function (Blueprint $table) {
            $table->integer('is_active')->default(1)->comment('0=無効,1=有効');
        });
    }

    function down()
    {
        Schema::table('auto_answers', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
}
