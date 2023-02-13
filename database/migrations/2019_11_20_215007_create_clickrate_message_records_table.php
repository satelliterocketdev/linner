<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClickrateMessageRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clickrate_message_records', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('clickrate_item_id');
            // 集計種別 1:シナリオ 2:一斉 3:個別
            $table->integer('record_type');
            // 送信方法 1:シナリオ 2:一斉 3:個別 4:自動返信
            $table->integer('method');
            // 送信方法が指すテーブルの参照先id
            // シナリオ:scenario_messages, 一斉:magazines, 個別:account_messages, 自動返信:auto_answers
            $table->integer('source_message_id');
            // 送信した数（送信先フォロワー数）
            $table->integer('send_count');
            // アクセス数（アクセスされた時にカウントアップ）
            $table->integer('access_count');
            // 送信日時
            $table->dateTime('send_at');
            // 送信したテキストメッセージ(プレビュー表示用)
            $table->text('message')->nullable();

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
        Schema::dropIfExists('clickrate_message_records');
    }
}
