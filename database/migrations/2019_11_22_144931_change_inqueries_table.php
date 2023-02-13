<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeInqueriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inqueries', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('user_id');
            $table->text('answer')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inqueries', function (Blueprint $table) {
            $table->integer('user_id');
            $table->string('title');
            $table->text('answer')->nullable(false)->change();
        });
    }
}
