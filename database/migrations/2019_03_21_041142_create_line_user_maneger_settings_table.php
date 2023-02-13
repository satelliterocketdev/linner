<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLineUserManegerSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('line_user_maneger_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('channel_id')->nullable();
            $table->string('bot_dest_id')->nullable();// will be updated when link account has replied
            $table->string('link_token')->nullable();// will be update when bot link accout is clicked
            $table->string('channel_secret')->nullable();
            $table->text('channel_access_token')->nullable();
            $table->text('webhook_uRL')->nullable();
            $table->text('line_follow_link')->nullable();
            $table->text('lineAtId')->nullable();
            $table->text('account_name')->nullable();
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
        Schema::dropIfExists('line_user_maneger_settings');
    }
}
