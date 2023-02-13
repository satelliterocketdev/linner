<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Role
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\RoleUser[] $roleUsers
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Role extends Model
{
    /** @var int アカウント管理者 */
    public const ROLE_ACCOUNT_ADMINISTRATOR = 1; // アカウント管理者

    public const ROLE_FRIEND = 1000000;
    public const ROLE_MESSAGE = 2000000;
    public const ROLE_OTHER = 3000000;

    // 大分類/中分類/項目
    // 1    /010  /010
    // 友達管理
    public const ROLE_FRIEND_FRIEND_INFORMATION_CAN_BE_CHANGED = 1010010; // 友だち情報変更可
    public const ROLE_FRIEND_MESSAGE_CAN_BE_SENT = 1020010; // メッセージ送信可
    public const ROLE_FRIEND_SCENARIO_CHANGE_POSSIBLE = 1030010; // シナリオ変更可
    public const ROLE_FRIEND_RENAMEABLE = 1040010; // 名前変更可
    public const ROLE_FRIEND_CORRESPONDENCE_MARK_AND_DISPLAY_CAN_BE_CHANGED = 1050010; // 対応マーク・表示変更可
    public const ROLE_FRIEND_INDIVIDUAL_NOTES_CAN_BE_CHANGED = 1060010; // 個別メモ変更可
    public const ROLE_FRIEND_TAGS_CAN_BE_CHANGED = 1070010; // タグ変更可
    public const ROLE_FRIEND_RICH_MENU_CAN_BE_CHANGED = 1080010; // リッチメニュー変更可
    public const ROLE_FRIEND_ACTION_APPLICABLE = 1090010; // アクション適用可
    public const ROLE_FRIEND_CSV_EXPORT = 1100010; // CSVエクスポート
    public const ROLE_FRIEND_CSV_IMPORT = 1110010; // CSVインポート
    public const ROLE_FRIEND_MAIL_INVITE = 1120010; // メール招待
    public const ROLE_FRIEND_TALK_LIST = 1130010; // トーク一覧

    // シナリオ配信
    public const ROLE_SCENARIO_DISTRIBUTION_EDITABLE = 2010010; // シナリオ配信編集可能
    // 一斉配信
    public const ROLE_SIMULTANEOUS_DISTRIBUTION_EDITING_IS_POSSIBLE = 2020010; // 編集可能
    // 自動応答
    public const ROLE_AUTOMATIC_RESPONSE_EDITABLE = 2030010; // 編集可能
    // テンプレート
    public const ROLE_TEMPLATE_EDITING_IS_POSSIBLE = 2040010; // 編集可能
    // 回答フォーム
    public const ROLE_RESPONSE_FORM_EDITABLE = 2050010; // 編集可能
    // リマインダ
    public const ROLE_REMINDER_EDITABLE = 2060010; // 編集可能
    // 友だち追加設定
    public const ROLE_FRIEND_ADDITION_SETTINGS_CAN_BE_EDITED = 2070010; // 編集可能
    // アクション管理
    public const ROLE_ACTION_MANAGEMENT_AVAILABLE = 2080010; // 利用可能
    // エラーメッセージ処理
    public const ROLE_ERROR_MESSAGE_HANDLING_AVAILABLE = 2090010; // 利用可能

    // タグ管理
    public const ROLE_TAG_MANAGEMENT_AVAILABLE = 3010010; // 利用可能
    // 友だち情報管理
    public const ROLE_FRIEND_INFORMATION_MANAGEMENT_AVAILABLE = 3020010; // 利用可能
    // カスタム検索管理
    public const ROLE_CUSTOM_SEARCH_MANAGEMENT_AVAILABLE = 3030010; // 利用可能
    // コンバージョン
    public const ROLE_CONVERSION_AVAILABLE = 3040010; // 利用可能
    // URLクリック測定
    public const ROLE_URL_CLICK_MEASUREMENT_AVAILABLE = 3050010; // 利用可能
    // リッチメニュー
    public const ROLE_RICH_MENU_AVAILABLE = 3060010;
    // アンケート結果
    public const ROLE_SURVEYS_RESULT_AVAILABLE = 3070010; // 利用可能
    // サイトスクリプト
    public const ROLE_SITE_SCRIPT_AVAILABLE = 4030010; // 利用可能

    public function roleUsers()
    {
        return $this->hasMany(RoleUser::class);
    }
}
