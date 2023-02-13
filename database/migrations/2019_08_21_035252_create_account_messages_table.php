<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_id');
            $table->string('destination');
            $table->string('reply_token');
            $table->string('type');
            $table->string('timestamp');
            $table->string('source_type');
            $table->string('source_user_id');
            $table->string('message_id');
            $table->string('message_type');
            $table->text('message_json_data');
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
        Schema::dropIfExists('account_messages');
    }
}
