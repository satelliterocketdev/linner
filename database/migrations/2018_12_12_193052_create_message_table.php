<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_delivery', function (Blueprint $table) {
            $table->increments('id');
            $table->string('channelId');
            $table->string('replyToken');
            $table->string('soruce_userId');
            $table->string('soruce_type');
            $table->string('message_id');
            $table->text('message_type')->nullable();
            $table->string('message_text');
            $table->text('destination');
            $table->string('timestamp');
            $table->integer('message_flag'); // 1 = send, 0 = recieve
            $table->string('status');
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
        Schema::dropIfExists('message_delivery');
    }
}
