<?php

use Illuminate\Database\Seeder;
use App\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            // 大分類/中分類/項目
            // 1    /010  /010

            "管理者" => [
                "" => [
                    Role::ROLE_ACCOUNT_ADMINISTRATOR => "アカウント管理者"
                ]
            ],
            "友だち" => [
                "友だち操作" => [
                    Role::ROLE_FRIEND_MESSAGE_CAN_BE_SENT => "メッセージ送信可",
                    Role::ROLE_FRIEND_SCENARIO_CHANGE_POSSIBLE => "シナリオ変更可",
                    Role::ROLE_FRIEND_RENAMEABLE => "名前変更可",
                    Role::ROLE_FRIEND_CORRESPONDENCE_MARK_AND_DISPLAY_CAN_BE_CHANGED => "対応マーク・表示変更可",
                    Role::ROLE_FRIEND_INDIVIDUAL_NOTES_CAN_BE_CHANGED => "個別メモ変更可",
                    Role::ROLE_FRIEND_TAGS_CAN_BE_CHANGED => "タグ変更可",
                    Role::ROLE_FRIEND_FRIEND_INFORMATION_CAN_BE_CHANGED => "友だち情報変更可",
                    Role::ROLE_FRIEND_RICH_MENU_CAN_BE_CHANGED => "リッチメニュー変更可",
                    Role::ROLE_FRIEND_ACTION_APPLICABLE => "アクション適用可",
                    Role::ROLE_FRIEND_MAIL_INVITE => "メール招待",
                    Role::ROLE_FRIEND_TALK_LIST => "トーク一覧",
                ],
                "CSV操作" => [
                    Role::ROLE_FRIEND_CSV_EXPORT => "CSVエクスポート",
                    Role::ROLE_FRIEND_CSV_IMPORT => "CSVインポート",
                ],

            ],

            "メッセージ" => [
                "シナリオ配信" => [
                    Role::ROLE_SCENARIO_DISTRIBUTION_EDITABLE => "編集可能",
                ],
                "一斉配信" => [
                    Role::ROLE_SIMULTANEOUS_DISTRIBUTION_EDITING_IS_POSSIBLE => "編集可能",
                ],
                "自動応答" => [
                    Role::ROLE_AUTOMATIC_RESPONSE_EDITABLE => "編集可能",
                ],
                "テンプレート" => [
                    Role::ROLE_TEMPLATE_EDITING_IS_POSSIBLE => "編集可能",
                ],
                "回答フォーム" => [
                    Role::ROLE_RESPONSE_FORM_EDITABLE => "編集可能",
                ],
                "リマインダ" => [
                    Role::ROLE_REMINDER_EDITABLE => "編集可能",
                ],
                "友だち追加設定" => [
                    Role::ROLE_FRIEND_ADDITION_SETTINGS_CAN_BE_EDITED => "編集可能",
                ],
                "アクション管理" => [
                    Role::ROLE_ACTION_MANAGEMENT_AVAILABLE => "利用可能",
                ],
                "エラーメッセージ処理" => [
                    Role::ROLE_ERROR_MESSAGE_HANDLING_AVAILABLE => "利用可能",
                ],
            ],

            "友だち属性" => [
                "タグ管理" => [
                    Role::ROLE_TAG_MANAGEMENT_AVAILABLE => "利用可能",
                ],
                "友だち情報管理" => [
                    Role::ROLE_FRIEND_INFORMATION_MANAGEMENT_AVAILABLE => "利用可能",
                ],
                "カスタム検索管理" => [
                    Role::ROLE_CUSTOM_SEARCH_MANAGEMENT_AVAILABLE => "利用可能",
                ],
                "リッチメニュー" => [
                    Role::ROLE_RICH_MENU_AVAILABLE => "利用可能",
                ]
            ],

            "統計情報" => [
                "URLクリック測定" => [
                    Role::ROLE_URL_CLICK_MEASUREMENT_AVAILABLE => "利用可能",
                ],
                "コンバージョン" => [
                    Role::ROLE_CONVERSION_AVAILABLE => "利用可能",
                ],
                "サイトスクリプト" => [
                    Role::ROLE_SITE_SCRIPT_AVAILABLE => "利用可能",
                ],
                "アンケート結果" => [
                    Role::ROLE_SURVEYS_RESULT_AVAILABLE => "利用可能",
                ],
            ],
        ];

        foreach ($roles as $majorItem => $mediumItems) {
            foreach ($mediumItems as $mediumItem => $items) {
                foreach ($items as $key => $description) {
                    $role = new Role();
                    $role->id = $key;
                    if (empty($mediumItem)) {
                        $role->name = $majorItem;
                    } else {
                        $role->name = $majorItem . "/" . $mediumItem;
                    }
                    $role->description = $description;
                    $role->save();
                }
            }
        }
    }
}
