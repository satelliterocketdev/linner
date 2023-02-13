<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMagazineDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magazine_deliveries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('magazine_id');
            $table->integer('type');
            $table->string('content');
            $table->integer('pf_user_id');
            $table->dateTime('schedule_date');
            $table->tinyInteger('is_attachment');
            $table->tinyInteger('is_sent');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('magazine_deliveries');
    }
}
