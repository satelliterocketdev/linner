<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagManagementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tag_managements', function (Blueprint $table) {
            $table->increments('id');
            //とりあえずnullableにする
            $table->integer('account_id')->nullable();;
            $table->integer('tag_folder_id')->nullable();;
            $table->string('title');
            $table->boolean('no_limit'); //false as default
            $table->text('action')->nullable();
            $table->integer('limit');
            $table->text('condition')->nullable();
            $table->text('followerslist')->nullable();
            // $table->text('data');
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
        Schema::dropIfExists('tag_managements');
    }
}
