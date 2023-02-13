<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTypeConversionActions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('conversion_actions', function (Blueprint $table) {
            $table->integer('type');
            $table->integer('index');
            $table->string('option');
            $table->integer('tag_management_id');
            $table->integer('scenario_id');
            $table->string('message');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('conversion_actions', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('index');
            $table->dropColumn('option');
            $table->dropColumn('tag_management_id');
            $table->dropColumn('scenario_id');
            $table->dropColumn('message');
        });
    }
}
