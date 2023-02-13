<?php

namespace App\Services;

use App\ClickrateItem;
use App\ClickrateMessageRecord;
use App\ClickrateFollowerRecord;
use App\Services\MessageService;

/**
 * メッセージ本文に含まれる送信時痴漢文字列を適切に置換するためのクラス。
 * このインスタンスは１つの同報送信を管理する。異なるメッセージを送信する場合は、インスタンスを再生性すること。
 * 1. 送信先ごとにmessageBuildメソッドを呼び出し、メッセージの書き換え処理を行う。
 * 2. すべての同報送信が完了後endメソッドを呼び出し、確定する。
 * 対象：クリック計測用URL、コンバージョン用URL、URLクリック時のアクション
 */
class MessageFormatManager
{
    /**
     * @param int $record_type  集計タイプ　1:シナリオ 2:一斉 3:個別
     */
    public $record_type;
    /**
     * @param int $method  送信方法　1:シナリオ 2:一斉 3:個別 4:自動返信
     */
    public $method;

    /**
     * @param int $sourceMessageId  送信方法が指すテーブルの参照先id
     */
    public $sourceMessageId;

    /**
     * @param Array $clickrateMessageRecords  管理中のURL記録レコード
     */
    protected $clickrateMessageRecords = [];

    /**
     * @param Array $original  書き換え前のメッセージ
     */
    protected $original = '';
    /**
     * コンストラクタ
     * @param ClickrateItem $clickrateItem 送信するURL情報
     * @param string $method 送信方法 シナリオ：scenario 一斉：magazine トーク：talk 自動返信：auto_answer
     * @param string $message メッセージ本文
     * @param int $source_messageId 送信方法が指すテーブルの参照先id (シナリオ:scenario_messages, 一斉:magazines, 個別:account_messages, 自動返信:auto_answers)
     */
    public function __construct($acount_id, $send_method, $message, $sourceMessageId = 0)
    {
        switch ($send_method) {
            case 'scenario':
                $this->method = 1;
                $this->record_type = 1;
                break;
            case 'magazine':
                $this->method = 2;
                $this->record_type = 2;
                break;
            case 'talk':
                $this->method = 3;
                $this->record_type = 3;
                // トークはフォーマットをかけていないのでここでかける
                $message = MessageFormatManager::plainFormat($acount_id, $message);
                break;
            case 'auto_answer':
                $this->method = 4;
                // 自動返信は個別として集計するため3
                $this->record_type = 3;

                // 自動返信はフォーマットをかけていないのでここでかける
                $message = MessageFormatManager::plainFormat($acount_id, $message);
                break;
            default:
                throw new Exception('not supported send method '.$send_method);
                break;
        }
        $this->sourceMessageId = $sourceMessageId;
        $this->original = $message;
    }
    
    public function messageBuild($follower)
    {
        //・URLクリック時のアクション設定用URL（embeddedurlタグで括られている。）
        // 正規表現は App\Services\MessageService#formatMessageで書き換えるタグ仕様で決め打ちしている
        // URLクリック時のアクション設定用
        $res = preg_replace_callback(
            '/<embeddedurl class="clickrate" cid="(?P<cid>.+?)"\/>/i',
            function ($matches) use ($follower) {
                $cid = $matches['cid'];

                $item = ClickrateItem::find($cid);
                if (!isset($item)) {
                    // 不正なID
                    return ;
                };
        
                if (!array_key_exists($cid, $this->clickrateMessageRecords)) {
                    $messageRecord = new ClickrateMessageRecord();
                    $messageRecord->record_type = $this->record_type;
                    $messageRecord->method = $this->method;
                    $messageRecord->source_message_id = $this->sourceMessageId;
                    $messageRecord->send_count = 0;
                    $messageRecord->send_at = \Carbon\Carbon::now();
                    $item->clickrateMessageRecords()->save($messageRecord);
                    $this->clickrateMessageRecords[$cid] = $messageRecord;
                }
        
                $messageRecord = $this->clickrateMessageRecords[$cid];
        
                $followerRecord = ClickrateFollowerRecord::firstOrCreate(
                    [
                        'clickrate_message_record_id' => $messageRecord->id,
                        'clickrate_item_id' => $item->id,
                        'account_follower_id' => $follower->id
                    ]
                );

                return route('clickrate.route', ['token' => $item->clickrate_token, 'mid' => $messageRecord->id, 'fid' => $follower->id ]);
            },
            $this->original
        );
        if ($this->record_type == 3) {
            foreach ($this->clickrateMessageRecords as $record) {
                $record->message = $res;
                $record->save();
            }
        }

        // その他のURL変換
        $res = $this->convertUrl($res, $follower->id);
        return $res;
    }

    public function end($count, $sourceMessageId = null)
    {
        foreach ($this->clickrateMessageRecords as $record) {
            if (isset($sourceMessageId)) {
                $record->source_message_id = $sourceMessageId;
            }
            $record->send_count = $count;
            $record->send_at = \Carbon\Carbon::now();
            $record->save();
        }
    }

    private static function plainFormat($acount_id, $plain_message)
    {
        $appUrl = url('/'); // "http://APPURL"
        // "click"で始まるパス=>clickrate "cv"で始まるパス=>conversion
        $message = preg_replace_callback(
            "~$appUrl\S+~i",
            function ($matches) {
                return '<span class="internalurl">'. $matches[0]. '</span>';
            },
            $plain_message
        );
        $msgservice = new MessageService();
        return $msgservice->formatMessage($acount_id, $message);
    }

    /**
     * メッセージ内に埋め込まれているURLに対し、フォロワーを特定するパラメータを付与する。
     * ・URLクリック時のアクション
     * ・クリック計測用URL
     */
    private static function convertUrl($string, $follower_identifier)
    {
        // 正規表現は App\Services\MessageService#formatMessageで書き換えるタグ仕様で決め打ちしている
        // URLクリック時のアクション設定用
        $result = preg_replace_callback(
            '/<embeddedurl class="action" cid="(?P<cid>.+?)"\/>/i',
            function ($matches) use ($follower_identifier) {
                 return route('urlclick.route', ['cid' => $matches['cid'], 'fid' => $follower_identifier ]);
            },
            $string
        );

        // コンバージョン用
        $result = preg_replace_callback(
            '/<embeddedurl class="conversion" cid="(?P<cid>.+?)" token="(?P<token>.+?)"\/>/i',
            function ($matches) use ($follower_identifier) {

                return route(
                    'conversion.route',
                    ['token' => $matches['token'],
                     'cid' => $matches['cid'],
                     'fid' => $follower_identifier ]
                );
            },
            $result
        );
        return $result;
    }
}
