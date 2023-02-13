<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsDeliveryCountToAutoAnswers extends Migration
{
    public function up()
    {
        Schema::table('auto_answers', function (Blueprint $table) {
            $table->integer('delivery_count')->default(0)->comment('応答回数');
        });
    }

    function down()
    {
        Schema::table('auto_answers', function (Blueprint $table) {
            $table->dropColumn('delivery_count');
        });
    }
}
