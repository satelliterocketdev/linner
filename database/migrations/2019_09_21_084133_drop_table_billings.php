<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropTableBillings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('billings');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('billings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_id');
            $table->integer('amount');
            $table->integer('status');
            $table->timestamps();
        });
    }
}
