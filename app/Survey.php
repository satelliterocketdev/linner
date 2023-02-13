<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Survey
 *
 * @property int $id
 * @property int $account_id
 * @property string|null $notification_message 通知文面
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string $type_delivery 配信タイプ（magazines,scenarios）
 * @property string $type_select_restriction 選択制限タイプ（no_limit,per_survey,per_all）
 * @property int|null $magazine_id 一斉配信ID
 * @property int|null $scenario_message_id ステップ配信メッセージID
 * @property string|null $text 質問内容
 * @property string|null $action_1_type アクション1_タイプ（ postback・message・uri 等 LINE API側の仕様上の選択。基本はpostbackを使う方向が良いかも）
 * @property string|null $action_1_behavior アクション1_挙動（ なし,URLを開く,電話を発信,メールを送信,回答フォーム,シナリオ移動 ）
 * @property string|null $action_1_label アクション1_回答内容
 * @property string|null $action_1_data アクション1_データ（URL等）
 * @property string|null $action_1_auto_reply アクション1_自動返信テキスト
 * @property array $action_1_tag_add アクション1_追加タグ
 * @property array $action_1_tag_delete アクション1_除外タグ
 * @property string|null $action_2_type アクション2_タイプ（ postback・message・uri 等 LINE API側の仕様上の選択。基本はpostbackを使う方向が良いかも）
 * @property string|null $action_2_behavior アクション2_挙動（ なし,URLを開く,電話を発信,メールを送信,回答フォーム,シナリオ移動 ）
 * @property string|null $action_2_label アクション2_回答内容
 * @property string|null $action_2_data アクション2_データ（URL等）
 * @property string|null $action_2_auto_reply アクション2_自動返信テキスト
 * @property array $action_2_tag_add アクション2_追加タグ
 * @property array $action_2_tag_delete アクション2_除外タグ
 * @property string|null $action_3_type アクション3_タイプ（ postback・message・uri 等 LINE API側の仕様上の選択。基本はpostbackを使う方向が良いかも）
 * @property string|null $action_3_behavior アクション3_挙動（ なし,URLを開く,電話を発信,メールを送信,回答フォーム,シナリオ移動 ）
 * @property string|null $action_3_label アクション3_回答内容
 * @property string|null $action_3_data アクション3_データ（URL等）
 * @property string|null $action_3_auto_reply アクション3_自動返信テキスト
 * @property array $action_3_tag_add アクション3_追加タグ
 * @property array $action_3_tag_delete アクション3_除外タグ
 * @property string|null $action_4_type アクション4_タイプ（ postback・message・uri 等 LINE API側の仕様上の選択。基本はpostbackを使う方向が良いかも）
 * @property string|null $action_4_behavior アクション4_挙動（ なし,URLを開く,電話を発信,メールを送信,回答フォーム,シナリオ移動 ）
 * @property string|null $action_4_label アクション4_回答内容
 * @property string|null $action_4_data アクション4_データ（URL等）
 * @property string|null $action_4_auto_reply アクション4_自動返信テキスト
 * @property array $action_4_tag_add アクション4_追加タグ
 * @property array $action_4_tag_delete アクション4_除外タグ
 * @property-read \App\Magazine|null $magazine
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\SurveyAnswer[] $survey_answers
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereAction1AutoReply($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereAction1Behavior($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereAction1Data($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereAction1Label($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereAction1TagAdd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereAction1TagDelete($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereAction1Type($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereAction2AutoReply($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereAction2Behavior($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereAction2Data($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereAction2Label($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereAction2TagAdd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereAction2TagDelete($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereAction2Type($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereAction3AutoReply($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereAction3Behavior($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereAction3Data($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereAction3Label($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereAction3TagAdd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereAction3TagDelete($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereAction3Type($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereAction4AutoReply($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereAction4Behavior($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereAction4Data($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereAction4Label($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereAction4TagAdd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereAction4TagDelete($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereAction4Type($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereMagazineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereNotificationMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereScenarioMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereTypeDelivery($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereTypeSelectRestriction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Survey whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Survey extends Model
{
  protected $guarded = [];

  protected $casts = [
    'action_1_tag_add' => 'array',
    'action_1_tag_delete' => 'array',
    'action_2_tag_add' => 'array',
    'action_2_tag_delete' => 'array',
    'action_3_tag_add' => 'array',
    'action_3_tag_delete' => 'array',
    'action_4_tag_add' => 'array',
    'action_4_tag_delete' => 'array'
  ];

    public function magazine()
    {
        return $this->belongsTo(Magazine::class);
    }

    public function survey_answers()
    {
        return $this->hasMany(SurveyAnswer::class);
    }

}
