<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountFollowersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_followers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('account_id');
            $table->string('channel_id');
            $table->string('pf_user_id');
            $table->string('display_name')->nullable();
            // $table->text('picture')->nullable();
            // $table->text('status_message')->nullable();
            $table->string('event_type');
            $table->string('reply_token')->nullable();
            $table->string('source_type')->nullable();
            $table->string('source_user_id')->nullable();
            $table->string('destination_user_id')->nullable();
            $table->string('status')->nullable();
            $table->string('timedate_followed');
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
        Schema::dropIfExists('account_followers');
    }
}
