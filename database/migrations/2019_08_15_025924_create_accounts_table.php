<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('channel_id');
            $table->string('channel_secret');
            $table->string('channel_access_token');
            $table->string('webhook_token');
            $table->integer('bot_dest_id');
            $table->string('link_token');
            $table->string('line_follow_link');
            $table->string('line_add');
            $table->string('description');
            $table->string('plan');
            $table->string('profile_image');
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
        Schema::dropIfExists('accounts');
    }
}
