<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            // 下書きフラグを追加。
            $table->boolean('is_draft')->default(true);
            // 掲載開始日をTimestampに変更。
            $table->dateTime('start_at')->nullable()->change();
            // 掲載終了日をTimestampに変更。
            $table->dateTime('end_at')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn('is_draft');
            $table->date('start_at')->nullable();
            $table->date('end_at')->nullable();
        });
    }
}
