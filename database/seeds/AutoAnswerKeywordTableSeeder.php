<?php

use Illuminate\Database\Seeder;
use App\AutoAnswerKeyword;

class AutoAnswerKeywordTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $accounts = New AutoAnswerKeyword();

        // $accounts->auto_answer_id = '2';
        // $accounts->keyword = "交通情報";
        // $accounts->save();

        // $accounts->auto_answer_id = '2';
        // $accounts->keyword = "天候";
        // $accounts->save();

        AutoAnswerKeyword::create([
            "auto_answer_id" => "1",
            "keyword" => "即時：天候",
        ]);
        AutoAnswerKeyword::create([
            "auto_answer_id" => "1",
            "keyword" => "即時：交通情報",
        ]);
        AutoAnswerKeyword::create([
            "auto_answer_id" => "3",
            "keyword" => "無効：即時：天候",
        ]);
        AutoAnswerKeyword::create([
            "auto_answer_id" => "3",
            "keyword" => "無効：即時：交通情報",
        ]);
        AutoAnswerKeyword::create([
            "auto_answer_id" => "3",
            "keyword" => "無効：範囲外：天候",
        ]);
        AutoAnswerKeyword::create([
            "auto_answer_id" => "3",
            "keyword" => "無効：範囲外：交通情報",
        ]);
        AutoAnswerKeyword::create([
            "auto_answer_id" => "4",
            "keyword" => "即時：その他",
        ]);
        AutoAnswerKeyword::create([
            "auto_answer_id" => "5",
            "keyword" => "即時：天候",
        ]);
        AutoAnswerKeyword::create([
            "auto_answer_id" => "5",
            "keyword" => "即時：交通情報",
        ]);
        AutoAnswerKeyword::create([
            "auto_answer_id" => "6",
            "keyword" => "時間指定：天候",
        ]);
        AutoAnswerKeyword::create([
            "auto_answer_id" => "6",
            "keyword" => "時間指定：交通情報",
        ]);
        AutoAnswerKeyword::create([
            "auto_answer_id" => "7",
            "keyword" => "時間指定：その他",
        ]);
        AutoAnswerKeyword::create([
            "auto_answer_id" => "8",
            "keyword" => "時間指定：天候",
        ]);
        AutoAnswerKeyword::create([
            "auto_answer_id" => "8",
            "keyword" => "時間指定：交通情報",
        ]);
        AutoAnswerKeyword::create([
            "auto_answer_id" => "9",
            "keyword" => "日時指定：天候",
        ]);
        AutoAnswerKeyword::create([
            "auto_answer_id" => "9",
            "keyword" => "日時指定：交通情報",
        ]);
        AutoAnswerKeyword::create([
            "auto_answer_id" => "10",
            "keyword" => "日時指定：天候",
        ]);
        AutoAnswerKeyword::create([
            "auto_answer_id" => "10",
            "keyword" => "日時指定：交通情報",
        ]);
        AutoAnswerKeyword::create([
            "auto_answer_id" => "11",
            "keyword" => "日時指定：その他",
        ]);
        AutoAnswerKeyword::create([
            "auto_answer_id" => "12",
            "keyword" => "日時指定：天候",
        ]);
        AutoAnswerKeyword::create([
            "auto_answer_id" => "12",
            "keyword" => "日時指定：交通情報",
        ]);
    }
}