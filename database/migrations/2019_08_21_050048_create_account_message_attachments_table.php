<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountMessageAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_message_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_id');
            $table->integer('account_message_id');
            $table->string('message_id');
            $table->string('file_name')->nullable();
            $table->string('file_url')->nullable();
            $table->string('preview_file_url')->nullable();
            $table->integer('file_size')->default(0);
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
        Schema::dropIfExists('account_message_attachments');
    }
}
