<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteUnnecessaryColumnsFromScenariosDeliveries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scenario_delivery', function (Blueprint $table) {
            $table->dropColumn('content');
            $table->dropColumn('media_file_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scenario_delivery', function (Blueprint $table) {
            $table->text('content');
            $table->integer('media_file_id');
        });
    }
}
