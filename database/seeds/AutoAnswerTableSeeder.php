<?php

use Illuminate\Database\Seeder;
use App\AutoAnswer;
use App\Account;

class AutoAnswerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $jsonWeekData0 = '{
            "0": {
                "label": "日",
                "value": true
            },
            "1": {
                "label": "月",
                "value": true
            },
            "2": {
                "label": "火",
                "value": true
            },
            "3": {
                "label": "水",
                "value": true
            },
            "4": {
                "label": "木",
                "value": true
            },
            "5": {
                "label": "金",
                "value": true
            },
            "6": {
                "label": "土",
                "value": true
            }
        }';

        $jsonWeekData1 = '{
            "0": {
                "label": "日",
                "value": false
            },
            "1": {
                "label": "月",
                "value": true
            },
            "2": {
                "label": "火",
                "value": false
            },
            "3": {
                "label": "水",
                "value": true
            },
            "4": {
                "label": "木",
                "value": false
            },
            "5": {
                "label": "金",
                "value": true
            },
            "6": {
                "label": "土",
                "value": false
            }
        }';

        $jsonWeekData2 = '{
            "0": {
                "label": "日",
                "value": true
            },
            "1": {
                "label": "月",
                "value": false
            },
            "2": {
                "label": "火",
                "value": true
            },
            "3": {
                "label": "水",
                "value": false
            },
            "4": {
                "label": "木",
                "value": true
            },
            "5": {
                "label": "金",
                "value": false
            },
            "6": {
                "label": "土",
                "value": true
            }
        }';

        AutoAnswer::create([
            "account_id" => "1",
            "title" => "自動応答1",
            "content_type" => "message",
            "content_message" => "有効パターン：アカウント無効：キーワードあり",
            "to_time" => null,
            "from_time" => null,
            "week" => $jsonWeekData0,
            "is_always" => 1,
            "is_draft" => "1",
        ]);
        AutoAnswer::create([
            "account_id" => "2",
            "title" => "自動応答2",
            "content_type" => "message",
            "content_message" => "無効パターン：日時指定範囲内",
            "to_time" => null,
            "from_time" => null,
            "week" => $jsonWeekData0,
            "is_always" => 1,
            "is_draft" => "0",
        ]);
        AutoAnswer::create([
            "account_id" => "2",
            "title" => "自動応答3",
            "content_type" => "message",
            "content_message" => "無効パターン：日時指定範囲外",
            "from_time" => "00:00:00.000000",
            "to_time" => "01:59:59.000000",
            "week" => $jsonWeekData0,
            "is_always" => 1,
            "is_draft" => "0",
        ]);
        AutoAnswer::create([
            "account_id" => "2",
            "title" => "自動応答4",
            "content_type" => "message",
            "content_message" => "有効パターン：アカウント有効：即時：キーワードなし",
            "to_time" => null,
            "from_time" => null,
            "week" => $jsonWeekData0,
            "is_always" => 1,
            "is_draft" => "1",
        ]);
        AutoAnswer::create([
            "account_id" => "2",
            "title" => "自動応答5",
            "content_type" => "message",
            "content_message" => "有効パターン：アカウント有効：即時：キーワードあり",
            "to_time" => null,
            "from_time" => null,
            "week" => $jsonWeekData0,
            "is_always" => 1,
            "is_draft" => "1",
        ]);
        AutoAnswer::create([
            "account_id" => "2",
            "title" => "自動応答8",
            "content_type" => "message",
            "content_message" => "有効パターン：アカウント有効：時間指定範囲外：キーワードあり",
            "from_time" => "00:00:00.000000",
            "to_time" => "01:59:59.000000",
            "week" => "",
            "is_always" => 0,
            "is_draft" => "1",
        ]);
        AutoAnswer::create([
            "account_id" => "2",
            "title" => "自動応答6",
            "content_type" => "message",
            "content_message" => "有効パターン：アカウント有効：時間指定：キーワードなし",
            "from_time" => "07:00:00.00000",
            "to_time" => "09:59:59.00000",
            "week" => $jsonWeekData1,
            "is_always" => 0,
            "is_draft" => "1",
        ]);
        AutoAnswer::create([
            "account_id" => "1",
            "title" => "自動応答7",
            "content_type" => "message",
            "content_message" => "有効パターン：アカウント有効：時間指定：キーワードあり",
            "from_time" => "07:00:00.00000",
            "to_time" => "09:59:59.00000",
            "week" => $jsonWeekData1,
            "is_always" => 0,
            "is_draft" => "1",
        ]);
        AutoAnswer::create([
            "account_id" => "1",
            "title" => "自動応答11",
            "content_type" => "message",
            "content_message" => "有効パターン：アカウント有効：日時指定　時間範囲外：キーワードあり",
            "from_time" => "00:00:00.000000",
            "to_time" => "01:59:59.000000",
            "week" => $jsonWeekData1,
            "is_always" => 0,
            "is_draft" => "1",
        ]);
        AutoAnswer::create([
            "account_id" => "1",
            "title" => "自動応答12",
            "content_type" => "message",
            "content_message" => "有効パターン：アカウント有効：日時指定　曜日範囲外：キーワードあり",
            "from_time" => "07:00:00.00000",
            "to_time" => "09:59:59.00000",
            "week" => $jsonWeekData2,
            "is_always" => 0,
            "is_draft" => "1",
        ]);
        AutoAnswer::create([
            "account_id" => "2",
            "title" => "自動応答9",
            "content_type" => "message",
            "content_message" => "有効パターン：アカウント有効：日時指定：キーワードなし",
            "from_time" => "07:00:00.00000",
            "to_time" => "09:59:59.00000",
            "week" => $jsonWeekData1,
            "is_always" => 0,
            "is_draft" => "1",
        ]);
        AutoAnswer::create([
            "account_id" => "2",
            "title" => "自動応答10",
            "content_type" => "message",
            "content_message" => "有効パターン：アカウント有効：日時指定：キーワードあり",
            "from_time" => "07:00:00.00000",
            "to_time" => "09:59:59.00000",
            "week" => $jsonWeekData1,
            "is_always" => 0,
            "is_draft" => "1",
        ]);
    }
}
