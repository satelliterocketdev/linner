<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTemplateMessagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('template_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_id');
            $table->string('title');
            $table->integer('content_type');
            $table->string('content_message');
            $table->integer('is_active');
            $table->boolean('is_draft');
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
        Schema::dropIfExists('template_messages');
    }
}
