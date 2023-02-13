<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableClickrateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clickrate_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_id');
            $table->string('title');
            $table->text('redirect_url')->nullable(false);
            $table->string('clickrate_token');
            $table->integer('send_count')->default(0);
            $table->integer('access_count')->default(0);
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
        Schema::dropIfExists('clickrate_items');
    }
}
