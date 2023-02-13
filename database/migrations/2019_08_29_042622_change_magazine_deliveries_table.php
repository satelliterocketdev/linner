<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeMagazineDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magazine_deliveries', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('content');
            $table->dropColumn('schedule_date');
            $table->dropColumn('is_attachment');
            $table->boolean('is_sent')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('magazine_deliveries', function (Blueprint $table) {
            $table->integer('type');
            $table->string('content');
            $table->dateTime('schedule_date');
            $table->boolean('is_attachment');
            $table->dropColumn('is_sent');
        });
    }
}
