<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClickrateFollowerRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 送信時にcreate, アクセスがあった時にaccess_atを更新
        // access_atが入っているレコードはクリック件数に計上する。
        Schema::create('clickrate_follower_records', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('clickrate_message_record_id');
            $table->integer('clickrate_item_id');
            $table->integer('account_follower_id');
            $table->dateTime('access_at')->nullable();
            $table->timestamps(); // 送信日時はcreate_date
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clickrate_follower_records');
    }
}
