<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\LineEvents\EventHandler;

use App\MessageModel;
use App\Account;
use App\AccountFollower;

class EventHandlerTest extends TestCase
{
    public function __construct()
    {
        $this->eventHandler = new EventHandler();
        $this->messageModel = new MessageModel();
        $this->account = new Account();
    }

    /**
     * LINEAPIイベント用テスト
     *
     * @return void
     */
    public function testEventHandler()
    {
        $account_data = Account::where('account_user_id', 'U90950684759136bc5088d9f5df298d3b')->first();
        if (!$account_data) {
            // EventHandler->keyChecker()用テストデータ(初回のみ)
            $this->account->name = "PHPUnitテストアカウント";
            $this->account->channel_id = '1613536414';
            $this->account->channel_secret = 'f404435437029d1168cfa3aec23a851c';
            $this->account->channel_access_token = 'FkV6zqwcHknPnwel1YvLtD8XZGpQb4dxvZYMH1JbqoYO0pl7nqPluGJdOLAzvwWC+6cKHuu6criViSSyObJEjWuAYdaR6qJkitfaixVLPUd+ybUKlzXddkED8tdBhW0gn5307uHUrcFNHaJFiwfG5QdB04t89/1O/w1cDnyilFU=';
            $this->account->webhook_token = 'aaaaaaaaa';
            $this->account->bot_dest_id = '1';
            $this->account->link_token = '1';
            $this->account->line_follow_link = 'http://localhost:3000';
            $this->account->line_add = '1';
            $this->account->description = 'PHPUnitテスト用';
            $this->account->profile_image = 'https://profile.line-scdn.net/0h7uYuwFlSaFpkDELJujsXDVhJZjcTIm4SHGJ1PxQLPm8cOyxZWW0lbEFbMGsePi4OUTkgPkAOMWIb';
            $this->account->account_user_id = 'U90950684759136bc5088d9f5df298d3b';
            $this->account->basic_id = 'aaaaaaaaa';
            $this->account->save();
        }

        $rand_user_id = "U".$this->generateCode(16).$this->generateCode(16);
        $array_data_text = [
            "destination" => "U90950684759136bc5088d9f5df298d3b",
            "events" => [[
                "replyToken" => "f44625ddb08c4a268efb00fb35cf23c1",
                "source" => ["type" => "user", "userId" => "U36c407334dac3c967c6ece012c05e65b"],
                "type" => "message",
                // "message" => ["id" => "10479986944630", "text" => "テストメッセージ", "type" => "text"],
                "message" => ["id" => $this->generateCode(14), "text" => "テストメッセージの中に即時：交通情報があれば自動応答を返します", "type" => "text"],
                "timestamp" => "1.567128100845E12"]]
        ];
        $array_data_follow = [
            "destination" => "U90950684759136bc5088d9f5df298d3b",
            "events" => [[
                "replyToken" => "235b724ef050418191f63b80299109f7",
                "source" => ["type" => "user", "userId" => $rand_user_id],
                // "source" => ["type" => "user", "userId" => "U36c407334dac3c967c6ece012c05e65b"],
                "type" => "follow",
                "timestamp" => "1.567128448688E12"]]
        ];
        $array_data_unfollow = [
            "destination" => "U90950684759136bc5088d9f5df298d3b",
            "events" => [[
                "source" => ["type" => "user", "userId" => $rand_user_id],
                //"source" => ["type" => "user", "userId" => "U36c407334dac3c967c6ece012c05e65b"],
                "type" => "unfollow",
                "timestamp" => "1.567128411886E12"]]
        ];

        $content_text = json_encode($array_data_text);
        $content_follow = json_encode($array_data_follow);
        $content_unfollow = json_encode($array_data_unfollow);

        $hash = hash_hmac('sha256', $content_text, "f404435437029d1168cfa3aec23a851c", true);
        $header = base64_encode($hash);

        // testcase: 即時
        $response_data = $this->eventHandler->sellectEvent($content_text, $header, "1613536414");
        $json_data = json_decode($response_data->content(), true);
        // $this->assertEquals($json_data['type'], "message");
        // $this->assertEquals($json_data['text'], "有効パターン：アカウント有効：即時：キーワードあり");
        // testcase: 時間指定
        $array_data_text2 = [
            "destination" => "U90950684759136bc5088d9f5df298d3b",
            "events" => [[
                "replyToken" => "f44625ddb08c4a268efb00fb35cf23c1",
                "source" => ["type" => "user", "userId" => "U36c407334dac3c967c6ece012c05e65b"],
                "type" => "message",
                "message" => ["id" => $this->generateCode(14), "text" => "テストメッセージの中に時間指定：交通情報があれば自動応答を返します", "type" => "text"],
                "timestamp" => strtotime('2019-08-20 07:59:59')]]
        ];
        $content_text2 = json_encode($array_data_text2);
        $hash2 = hash_hmac('sha256', $content_text2, "f404435437029d1168cfa3aec23a851c", true);
        $header2 = base64_encode($hash2);
        $response_data2 = $this->eventHandler->sellectEvent($content_text2, $header2, "1613536414");
        $json_data2 = json_decode($response_data2->content(), true);
        // $this->assertEquals($json_data2['type'], "message");
        // $this->assertEquals($json_data2['text'], "有効パターン：アカウント有効：時間指定：キーワードあり");
        // testcase: 曜日・時間指定
        $array_data_text3 = [
            "destination" => "U90950684759136bc5088d9f5df298d3b",
            "events" => [[
                "replyToken" => "f44625ddb08c4a268efb00fb35cf23c1",
                "source" => ["type" => "user", "userId" => "U36c407334dac3c967c6ece012c05e65b"],
                "type" => "message",
                "message" => ["id" => $this->generateCode(14), "text" => "テストメッセージの中に日時指定：交通情報があれば自動応答を返します", "type" => "text"],
                "timestamp" => strtotime('2019-08-21 07:59:59')]]
        ];
        $content_text3 = json_encode($array_data_text3);
        $hash3 = hash_hmac('sha256', $content_text3, "f404435437029d1168cfa3aec23a851c", true);
        $header3 = base64_encode($hash3);
        $response_data3 = $this->eventHandler->sellectEvent($content_text3, $header3, "1613536414");
        $json_data3 = json_decode($response_data3->content(), true);
        // $this->assertEquals($json_data3['type'], "message");
        // $this->assertEquals($json_data3['text'], "有効パターン：アカウント有効：日時指定：キーワードあり");
    }

    private function generateCode(int $length)
    {
        $max = pow(10, $length) - 1;
        $rand = random_int(0, $max);
        $code = sprintf('%0'. $length. 'd', $rand);

        return $code;
    }
}
