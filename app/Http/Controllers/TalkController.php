<?php

namespace App\Http\Controllers;

use App\Exceptions\LineApiException;
use DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\LineEvents\MessageEvent;
use App\Services\MessageService;

class TalkController extends Controller
{
    protected $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    /**
     * コントローラ固有のタイムゾーン.
     * @var string
     */
    protected $timezone = 'Asia/Tokyo';

    /**
     * 曜日を表す文字
     */
    protected $weekday = ['日', '月', '火', '水', '木', '金', '土'];

    public function index($followerId = null)
    {
        if (!is_null($followerId)) {
            $follower = Auth::user()->account->accountFollowers->where('id', $followerId)->first();
            if ($follower) {
                return view('talk', ['selectedFromOutside' => $follower]);
            }
        }

        return view('talk', ['selectedFromOutside' => null]);
    }

    /**
     * @param  Carbon $dt
     * @return String
     */
    private function relativeDateFormatting($dt)
    {
        if ($dt->isToday()) {
            return 'relativeToday';
        } elseif ($dt->isYesterday()) {
            return 'relativeYesterday';
        } elseif ($dt->isCurrentYear()) {
            return $dt->format('m/d').' ('.$this->weekday[$dt->dayOfWeek].')';
        }

        return $dt->format('Y/m/d').' ('.$this->weekday[$dt->dayOfWeek].')';
    }

    public function downloadAttachment($attachmentsId)
    {
        $attachment =  Auth::user()->account->accountMessageAttachments()->Where('id', $attachmentsId)->first();
        if (isset($attachment)) {
            $title = $attachment->file_name;
            $path = $attachment->file_url;
            $file_path =  Storage::path($path);
        
            if (isset($file_path)) {
                $headers = [
                    'Content-Type' => Storage::mimeType($path),
                ];
                return response()->download($file_path, $title, $headers);
            }
            throw \Exception();
        }
    }

    /**
     * システムが受信したメッセージに付随するAttachmentに対するCalloutContentを生成する。
     * AttachmentはPrivateファイルを指している。
     */
    private function createReceivedAttachmentContent($messageType, $attachment)
    {
        $url = $attachment->preview_file_url;
        $attachmentId = $attachment->id;
        $content = 'File Not Found';
        try {
            switch ($messageType) {
                case 'image':
                case 'video':
                    // (default Storage)
                    $preview_binary = base64_encode(Storage::get($url));
                    $mimeType = Storage::mimeType($url);
                    // ファイル拡張子の取得
                    $arr = explode('.', $url);
                    $ext = end($arr);
                    $content = sprintf('<a href="%s" class="attachment" download><img src=data:%s;base64,%s alt="%s"/></a>', route('talk.download', ['id' => $attachment->id]), $mimeType, $preview_binary, $attachment->file_name);
                    break;
                case 'audio':
                    $content = sprintf('<a href="%s" class="attachment" download>[音声ファイル]</a>', route('talk.download', ['id' => $attachment->id]));
                    break;
                case 'file':
                    $content = sprintf('<a href="%s" class="attachment" download>%s</a>', route('talk.download', ['id' => $attachment->id]), $attachment->file_name);
                    break;
            }
        } finally {
            return $content;
        }
    }

    /**
     * システムから送信したメッセージに付随するAttachmentに対するCalloutContentを生成する。
     * AttachmentはPublicファイルを指している。
     */
    private function createSentAttachmentContent($messageType, $attachment)
    {
        $url = $attachment->preview_file_url;
        $attachmentId = $attachment->id;
        $content = 'File Not Found';
        try {
            switch ($messageType) {
                case 'image':
                case 'video':
                    $content = sprintf('<a href="%s" class="attachment" download><img src="%s" alt="%s"/></a>', $attachment->file_url, $attachment->preview_file_url, $attachment->file_name);
                    break;
                case 'audio':
                    $content = sprintf('<a href="%s" class="attachment" download>[音声ファイル]</a>', $attachment->file_url);
                    break;
            }
        } finally {
            return $content;
        }
    }

    private function createCallout($message)
    {
        /**
         * message_json_data を使用する際の注意点
         * 受信したメッセージの場合は1つのオブジェクト {"type":"text",... }
         * 送信したメッセージの場合は配列 [{"type":"text",... }]
         */
        $content = '';
        $isReceivedMessage = $message->isReceivedMessage();

        if ($message->message_type == 'text') {
            if ($message->message_body != null) {
                $content = $message->message_body;
            } else {
                $text = json_decode($message->message_json_data);
                if (is_array($text)) {
                    foreach ($text as $t) {
                        // 改行の再現のために改行コードをbrに変換
                        $content = \nl2br($t->text);
                    }
                } else {
                    // 改行の再現のために改行コードをbrに変換
                    $content = \nl2br($text->text);
                }
            }
        } elseif ($message->message_type == 'sticker') {
            $text = json_decode($message->message_json_data, true);
            $stickerId = $isReceivedMessage ? $text['stickerId'] : $text[0]['stickerId'];
            $sticker = \App\MediaFile::where('type', 'sticker')->where('name', $stickerId)->first();
            if ($sticker) {
                $content = sprintf('<img src="%s"></a>', $sticker->url);
            } else {
                $content = sprintf('<div>sticker</div><div>packageId&nbsp;:&nbsp;%s<br>stickerId&nbsp;:&nbsp;%s</div>', $text['packageId'], $text['stickerId']);
            }
        } elseif ($message->message_type == 'location') {
            // TODO: 送信未対応
            $location = json_decode($message->message_json_data, true);
            $content = sprintf('<div>緯度:%s<br> 経度:%s <br> 住所:%s</div>', $location['latitude'], $location['longitude'], $location['address']) ;
        } elseif ($message->message_type == 'template') {
            $survey = json_decode($message->message_json_data)[0];
            $template = $survey->template;
            $content = '<div class="survey-caption">';
            $content.= sprintf('<div class="survey-title"><span>%s</span></div>', $template->title);
            $content.= sprintf('<div class="survey-text"><span>%s</span></div>', $template->text);
            $content.= '</div>';
            $content.= '<div class="survey-actions">';
            foreach ($template->actions as $action) {
                $content.= sprintf('<div class="survey-action"><span>%s</span></div>', $action->label);
            }
            $content.='</div>';
        } else {
            $attachment = $message->accountMessageAttachments()->first();
            if (isset($attachment)) {
                if ($isReceivedMessage) {
                    $content = $this->createReceivedAttachmentContent($message->message_type, $attachment);
                } else {
                    $content = $this->createSentAttachmentContent($message->message_type, $attachment);
                }
            }
        }
        return $content;
    }

    public function message($followerId)
    {
        $follower = \App\AccountFollower::find($followerId);
        // timestampを参照し、古い順にメッセージを取得する。
        $messages =  Auth::user()->account->accountMessages()
            ->Where('source_user_id', $follower->source_user_id)
            ->orderBy('timestamp', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $chatlog = [];
        $pre_date = null;
        
        foreach ($messages as $message) {
            $cur_date = Carbon::createFromTimestampMs($message->timestamp, $this->timezone);
            $relative_date = $this->relativeDateFormatting($cur_date);

            $chatlog[] = [
                'content' => $this->createCallout($message),
                'message_type' => $message['message_type'],
                'is_send' => !$message->isReceivedMessage(),
                'date' => $relative_date,
                'time' => $cur_date->format('H:i'),
                'souldRenderDate' => $pre_date != $relative_date,
            ];
            $pre_date = $relative_date;
        }

        // 既読にする
        $follower->message_status = 1;
        $follower->save();

        $data = [
            'messages' => $chatlog,
        ];

        return response()->json($data, Response::HTTP_OK);
    }

    private function createShortCallout($message, $format)
    {
        $attachment = '';
        switch ($message->message_type) {
            case 'image':
                $attachment = '画像';
                break;
            case 'video':
                $attachment = '動画';
                break;
            case 'audio':
                $attachment = '音声ファイル';
                break;
            case 'file':
                $attachment = 'ファイル';
                break;
            case 'location':
                $attachment = '位置情報';
                break;
            case 'template':
                // アンケートは代替テキストを表示
                $survey = json_decode($message->message_json_data)[0];
                return $survey->altText;
            default:
                // text,stickerは通常版
                return $this->createCallout($message);
        }
        return sprintf($format, $attachment);
    }

    public function list()
    {
        $account = Auth::user()->account;

        // timestampを参照し、直近のメッセージを取得する。
        // （条件の：OR 句は同一timestampが存在する場合の回避策）
        //DB::enableQueryLog();
        $latest_messages = Auth::user()->account->accountMessages()
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('account_messages AS ref')
                    ->whereRaw('account_messages.account_id = ref.account_id
                        AND account_messages.source_user_id = ref.source_user_id
                        AND (account_messages.timestamp < ref.timestamp 
                            OR (account_messages.timestamp = ref.timestamp AND account_messages.id < ref.id)
                        )');
            })->orderBy('timestamp', 'desc')->get();
        //$log =   DB::getQueryLog();

        $latest_list = [];
        foreach ($latest_messages as $latest_message) {
            $account_follower = $account->accountFollowers()
                ->Where('source_user_id', $latest_message->source_user_id)
                ->first();
            if (!isset($account_follower)) {
                continue;
            }
            $pf_user = $account_follower->pfUsers;
            
            $latest_message_date = Carbon::createFromTimestampMs($latest_message->timestamp, $this->timezone);
            
            $isSendMessage = $latest_message['destination'] ==  $latest_message['source_user_id'];
            $short_callout = '';
            if ($isSendMessage) {
                $format = '%sを送信しました';
            } else {
                $format = $pf_user->display_name. 'が%sを送信しました';
            }
            $short_callout = $this->createShortCallout($latest_message, $format);

            $latest_list[] = [
                'id' => $account_follower->id,
                'pf_user_id' => $pf_user->id,
                'pf_user_picture' => $pf_user->picture,
                'pf_user_display_name' => $pf_user->display_name,
                'status' => $account_follower->status,
                'message_status' => $account_follower->message_status,
                'latest_message' => $short_callout,
                'timestamp' => $latest_message->timestamp,
                'latest_message_date' => $latest_message_date->format('Y/m/d'),
                'latest_message_time' => $latest_message_date->format('H:i:s'),
            ];
        }
        
        $data = [
            'latest_list' => $latest_list,
        ];

        return response()->json($data, Response::HTTP_OK);
    }

    public function markUnread(Request $request)
    {
        $account = Auth::user()->account;
        $data = $request->all();

        foreach ($data as $record) {
            $follower = $account->accountFollowers()
                ->where('id', $record['follower_id'])
                ->first();

            if (!isset($follower)) {
                continue;
            }
            $follower->message_status = 0;
            $follower->save();
        }
        return response(null, Response::HTTP_OK);
    }

    public function markRead(Request $request)
    {
        $account = Auth::user()->account;
        $data = $request->all();

        foreach ($data as $record) {
            $follower = $account->accountFollowers()
                ->where('id', $record['follower_id'])
                ->first();

            if (!isset($follower)) {
                continue;
            }
            $follower->message_status = 1;
            $follower->save();
        }
        return response(null, Response::HTTP_OK);
    }

    private function markSupported($code, Request $request)
    {
        $account = Auth::user()->account;
        $data = $request->all();
        foreach ($data as $record) {
            $follower = $account->accountFollowers()
                ->where('id', $record['follower_id'])
                ->first();

            if (!isset($follower)) {
                continue;
            }
            $follower->status = $code;
            $follower->save();
        }
        return response(null, Response::HTTP_OK);
    }

    public function markSupportedNone(Request $request)
    {
        return $this->markSupported('0', $request);
    }

    public function markSupportedRequired(Request $request)
    {
        return $this->markSupported('1', $request);
    }

    public function delete(Request $request)
    {
        $account = Auth::user()->account;
        $data = $request->all();

        foreach ($data as $record) {
            $follower = $account->accountFollowers()
                ->where('id', $record['follower_id'])
                ->first();

            if (!isset($follower)) {
                continue;
            }
            // DB::enableQueryLog();
            $count = $account->accountMessages()
                ->where('source_user_id', $follower->source_user_id)
                ->delete();
            // DB::getQueryLog();
            /*getQueryLog:"query":
                "delete from `account_messages`
                where `account_messages`.`account_id` = ?
                and `account_messages`.`account_id` is not null
                and `source_user_id` = ?"
            */
        }
        return response(null, Response::HTTP_OK);
    }

    public function sendMessage($followerId, Request $request)
    {
        $account = Auth::user()->account;
        $messageType = $request['messageType'];
        $body = $request['body'];
        $follower = \App\AccountFollower::find($followerId);

        $lineMsg = new MessageEvent();
        if ($messageType == 'text') {
            $msgBag = new \stdClass();
            $msgBag->content_message = $body;
            $msgBag->formatted_message = $msgBag->content_message;
            $lineMsg->sendChatMsg($account, $follower, $msgBag);
        } else {
            $lineMsg->sendChatMediaMsg($account, $follower, $messageType, $body['id']);
        }
        return response(null, Response::HTTP_OK);
    }
}
