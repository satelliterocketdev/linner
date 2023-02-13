<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColumAutoAnswersTable extends Migration
{
    public function up()
    {
        Schema::table('auto_answers', function (Blueprint $table) {
            $table->dropColumn('message_type');
        });
    }

    public function down()
    {
        Schema::table('auto_answers', function (Blueprint $table) {
            $table->string('message_type');
        });
    }
}
