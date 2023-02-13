<?php

namespace App\LineEvents;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\LineEvents\LineUtils;
use App\Services\MessageFormatManager;

//models
use App\AutoAnswer;
use App\AutoAnswerKeyword;
use App\AccountFollower;
use App\AccountMessage;

//line
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\AudioMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\VideoMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;

class AutoReplyMessageEvent
{
    public function __construct()
    {
    }

    public function index(array $data)
    {
        //TODO:現在はテキストのみだが、一斉配信やステップ配信のようにスタンプや絵文字が発生する恐れあり
        //auto_answersテーブルのcontent_typeカラムはそのために残してある。
        
        $account_id = $data['account_id'];
        $autoAnswers = AutoAnswer::with(['account'])->where('account_id', $account_id)->get();

        if (!$autoAnswers->isEmpty()) {
            foreach ($autoAnswers as $autoAnswer) {
                $chkKeyword = false ;
                if ($autoAnswer->is_active) { //有効
                    if ($autoAnswer->is_always) { //条件設定なし
                        $chkKeyword = $this->keywordChecker($autoAnswer->id, $data['message_text']);
                    } else { //条件設定あり
                        $get_date = getdate($data['timestamp'] / 1000); //LineAPIのタイムスタンプ取得（ミリ秒で送られてくるので1000で割る）
                        //曜日チェック
                        $autoAnswer['week'] = json_decode($autoAnswer['week'], true);
                        $weekCheck          = $autoAnswer['week'][$get_date['wday']]['value'];
                        if ($weekCheck) { //曜日OK
                            if (is_null($autoAnswer['from_time']) && is_null($autoAnswer['to_time'])) {
                                //時刻指定ナシ
                                $chkKeyword = $this->keywordChecker($autoAnswer->id, $data['message_text']);
                            } elseif (!is_null($autoAnswer['from_time']) && !is_null($autoAnswer['to_time'])) {
                                //時刻指定アリ
                                $time       = new Carbon(date($get_date['hours'] . ':' . $get_date['minutes'] . ':' . $get_date['seconds'], $data['timestamp']));
                                //時刻チェック
                                $timeCheck  = $this->timeBetween($time, $autoAnswer);
                                $chkKeyword = $timeCheck ? $this->keywordChecker($autoAnswer->id, $data['message_text']) : false ;
                            }
                        }
                    }
                }
                //条件に合致した際、キーワードチェックがはいり、trueならメッセージ送信
                // TODO::リプライtokenは30秒以内で有効。1回のリプライtokenで1メッセージしか送ることができない
                if ($chkKeyword) {
                    $util = new LineUtils();
                    $follower = AccountFollower::where('account_id', $account_id)->where('source_user_id', $data['source_user_id'])->first();
                    $message_format_manager = new MessageFormatManager($account_id, 'auto_answer', $autoAnswer->content_message);
                    if (isset($follower)) {
                        $autoAnswer->content_message = $message_format_manager->messageBuild($follower);
                    }
                    
                    $post_data = $util->replyAutoMessage($data, $autoAnswer->content_message);
                    Log::info("replyAutoMessage : " . $autoAnswer->content_message);

                    //応答回数カウント
                    $auto_answer = AutoAnswer::where('id', $autoAnswer->id)->update(['delivery_count' => $autoAnswer->delivery_count + 1]);

                    //TODO: AccountMessageを作ったらIDを渡す
                    $this->createAccountMessage($account_id, $data['source_user_id'], $post_data);
                    $message_format_manager->end(1);
                    return;
                }
            }
        }
        return;
    }

    private function keywordChecker($id, $send_message)
    {
        $keywords = AutoAnswerKeyword::where('auto_answer_id', $id)->get();
        if ($keywords->isEmpty()) { //キーワードなし：全てに応答 => true
            return true;
        } else { //キーワードあり：
            foreach ($keywords as $keyword) {
                if (strpos($send_message, $keyword->keyword) !== false) {
                    return true;
                }
            }
            return false;
        }
    }

    private function timeBetween($time, $autoAnswer)
    {
        $from_time = new Carbon($autoAnswer->from_time);
        $to_time   = new Carbon($autoAnswer->to_time);
        return $time->between($from_time, $to_time);
    }

    private function createAccountMessage($account_id, $destination, $post_data)
    {
        $messages = $post_data['messages'];
        $message_type = $messages[0]['type'];

        $message = new AccountMessage();
        $message->account_id = $account_id;
        $message->destination = $destination;
        // TODO: 受信時と合わせたけど。
        $message->type = $message_type;
        // 受信時timestampに合わせてミリ秒
        $message->timestamp = (Carbon::now()->timestamp) * 1000;
        $message->source_type = 'user';
        $message->source_user_id = $destination;
        // TODO: 何か入れるべき？
        $message->message_id = ' ';

        $message->message_type = $message_type;
        $message->message_json_data = json_encode($messages);
        
        $message->save();

        return $message;
    }
}
