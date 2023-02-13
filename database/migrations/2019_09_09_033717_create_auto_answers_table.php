<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAutoAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auto_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_id');
            $table->string('title');
            $table->string('content_type');
            $table->text('content_message');
            $table->string('message_type');
            $table->time('from_time')->nullable();
            $table->time('to_time')->nullable();
            $table->dateTime('from_at')->nullable();
            $table->dateTime('to_at')->nullable();
            $table->integer('is_draft')->default(0);
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
        Schema::dropIfExists('auto_answers');
    }
}
