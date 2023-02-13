<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColumnAutoAnswersColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('auto_answers', function (Blueprint $table) {
            $table->dropColumn('from_at');
            $table->dropColumn('to_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('auto_answers', function (Blueprint $table) {
            $table->datetime('from_at')->default(false);
            $table->datetime('to_at')->default(false);
        });
    }
}
