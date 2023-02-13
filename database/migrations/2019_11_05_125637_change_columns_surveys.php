<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnsSurveys extends Migration
{

    public function up()
    {
        Schema::table('surveys', function (Blueprint $table) {
            $table->string('type_delivery')->comment('配信タイプ（magazines,scenarios）'); 
            $table->string('type_select_restriction')->comment('選択制限タイプ（no_limit,per_survey,per_all）');
            $table->integer('magazine_id')->nullable()->comment('一斉配信ID');
            $table->integer('scenario_message_id')->nullable()->comment('ステップ配信メッセージID');
            $table->text('text')->nullable()->comment('質問内容');
            for ($i=1; $i <= 4 ; $i++) {
                $table->string('action_'.$i.'_type')->nullable()->comment('アクション'.$i.'_タイプ（ postback・message・uri 等 LINE API側の仕様上の選択。基本はpostbackを使う方向が良いかも）');
                $table->string('action_'.$i.'_behavior')->nullable()->comment('アクション'.$i.'_挙動（ なし,URLを開く,電話を発信,メールを送信,回答フォーム,シナリオ移動 ）');
                $table->string('action_'.$i.'_label')->nullable()->comment('アクション'.$i.'_回答内容');
                $table->string('action_'.$i.'_data')->nullable()->comment('アクション'.$i.'_データ（URL等）');
                $table->string('action_'.$i.'_auto_reply')->nullable()->comment('アクション'.$i.'_自動返信テキスト');
                $table->string('action_'.$i.'_tag_add')->nullable()->comment('アクション'.$i.'_追加タグ');
                $table->string('action_'.$i.'_tag_delete')->nullable()->comment('アクション'.$i.'_除外タグ');
            }
            $table->text('notification_message')->nullable()->comment('通知文面')->change();
            $table->dropColumn('title');
            $table->dropColumn('option');
            $table->dropColumn('intro_message');

        });
    }


    public function down()
    {
        Schema::table('surveys', function (Blueprint $table) {
            $table->dropColumn('type_delivery');
            $table->dropColumn('type_select_restriction');
            $table->dropColumn('magazine_id');
            $table->dropColumn('scenario_id');
            $table->dropColumn('text');
            for ($i=1; $i <= 4 ; $i++) {
                $table->dropColumn('action_'.$i.'_behavior');
                $table->dropColumn('action_'.$i.'_type');
                $table->dropColumn('action_'.$i.'_label');
                $table->dropColumn('action_'.$i.'_data');
                $table->dropColumn('action_'.$i.'_auto_reply');
            }
            $table->text('notification_message')->comment('')->change();
            $table->string('title');
            $table->text('option');
            $table->text('intro_message');
        });
    }
}
