<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnExpireAtConversionTrackingTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('conversion_tracking_uuids', function (Blueprint $table) {
            $table->dateTime('expire_at');
        });

        Schema::table('conversion_tracking_records', function (Blueprint $table) {
            $table->dateTime('expire_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('conversion_tracking_records', function (Blueprint $table) {
            $table->dropColumn('expire_at');
        });

        Schema::table('conversion_tracking_uuids', function (Blueprint $table) {
            $table->dropColumn('expire_at');
        });
    }
}
