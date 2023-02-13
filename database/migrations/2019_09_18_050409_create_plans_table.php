<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->string('type')->comment('タイプ[trial=>お試し,personal=>個人向け,corporation=>法人向け]');
            $table->string('name')->comment('プラン名 お試し,個人向け,法人向け 等');
            $table->string('description1')->comment('説明1');
            $table->string('description2')->comment('説明2');
            $table->string('level')->comment('レベル[free,light,standard,pro,expart,business,enterprise]');
            $table->integer('account_count')->comment('アカウント数');
            $table->integer('delivery_count')->comment('配信可能数')->nullable();
            $table->integer('price')->comment('価格')->nullable();
            $table->integer('is_active')->default(1); // 0 - Inactive | 1 - Active
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
        Schema::dropIfExists('plans');
    }
}
