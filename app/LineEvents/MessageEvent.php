<?php

namespace App\LineEvents;

use App\Exceptions\LineApiException;
use App\MagazineDelivery;
use App\MediaFile;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\AccountMessage;
use App\AccountMessageAttachment;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Utility;
use App\Services\MessageFormatManager;

//models
use App\Account;
use App\MessageModel;
use App\LineUserManegerSetting;
use App\FriendsModel;
use App\ScenarioDelivery;
use App\Magazine;
use App\Survey;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use DateTime;

//line
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\AudioMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\VideoMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;

use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use Log;
use ReflectionClass;
use ReflectionException;
use stdClass;

class MessageEvent
{
    /**
     * @var LineUtils
     */
    private $lineUtils;

    public function __construct()
    {
        $this->lineUtils = new LineUtils();
    }

    public function index(array $data)
    {
        $this->receiveMessage($data);

        // 自動応答メッセージ処理
        if ($data["message_type"] == "text") {
            $AutoReplyMessageEvent = new AutoReplyMessageEvent();
            $AutoReplyMessageEvent->index($data);
        }
        return;
    }

    public function replyLineMessage(array $data)
    {
        $lineBotSetting = LineUserManegerSetting::where('id', Auth::id()) ->get(); //line bot settings
        $lineUserToReply = FriendsModel::where('id', $data['userToReply'])->get();
        $lineUserMsgs = MessageModel::where('soruce_userId', $lineUserToReply[0]->source_user_id)
            ->where('channelId', $lineBotSetting[0]->channel_id)
            ->get()
            ->last();

        $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($lineBotSetting[0]->channel_access_token);
        $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $lineBotSetting[0]->channel_secret]);

        $textMessageBuilder = new TextMessageBuilder($data['Message']);
        $response = $bot->pushMessage($lineUserMsgs->soruce_userId, $textMessageBuilder);

        $msgResArr = ['messageStatusCode'=>$response->getHTTPStatus(), "messageRaw"=>$response->getRawBody()];

        return $msgResArr;
    }

    protected function receiveMessage(array $data)
    {
        $accountMessage = new AccountMessage();
        $accountMessage->account_id = $data['account_id'];
        $accountMessage->destination = $data['destination'];
        $accountMessage->reply_token = $data['replyToken'];
        $accountMessage->type = $data['message_type'];
        $accountMessage->timestamp = $data['timestamp'];
        $accountMessage->source_type = $data['source_type'];
        $accountMessage->source_user_id = $data['source_user_id'];
        $accountMessage->message_id = $data['message_id'];
        $accountMessage->message_type = $data['message_type'];
        $accountMessage->message_json_data = $data['message'];
        $accountMessage->save();

        switch ($data['message_type']) {
            case 'image':
                $accountMessageAttachment = new AccountMessageAttachment();
                $accountMessageAttachment->account_id = $data['account_id'];
                $accountMessageAttachment->account_message_id = $accountMessage->id;
                $accountMessageAttachment->message_id = $data['message_id'];
                $accountMessageAttachment->file_name = $data['file_name'];
                $accountMessageAttachment->file_url = $data['file_url'];
                $accountMessageAttachment->preview_file_url = $data['preview_file_url'];
                $accountMessageAttachment->file_size = $data['file_size'];
                $accountMessageAttachment->save();
        }
    }

    // public function sendSenarios()
    // {
    //     $lineBotSetting = LineUserManegerSetting::where('id', Auth::id()) ->get(); //line bot settings
    //     $lineUserToReply = FriendsModel::where('id', $data['userToReply'])->get();
    //     $lineUserMsgs = MessageModel::where('soruce_userId', $lineUserToReply[0]->source_user_id)
    //         ->where('channelId', $lineBotSetting[0]->channel_id)
    //         ->get()
    //         ->last();

    //     $httpClient = new CurlHTTPClient($lineBotSetting[0]->channel_access_token);
    //     $bot = new LINEBot($httpClient, ['channelSecret' => $lineBotSetting[0]->channel_secret]);

    //     $textMessageBuilder = new TextMessageBuilder($data['Message']);
    //     $response = $bot->pushMessage($lineUserMsgs->soruce_userId, $textMessageBuilder);

    //     $msgResArr = ['messageStatusCode'=>$response->getHTTPStatus(), "messageRaw"=>$response->getRawBody()];

    //     return $msgResArr;
    // }

    private function sendMulticastMsg($to_Send, $content_type, $message, $creator_id)
    {
        $lineBotSetting = Account::where('id', $creator_id)->get(); //line bot settings
        $httpClient = new CurlHTTPClient($lineBotSetting[0]->channel_access_token);
        $bot = new LINEBot($httpClient, ['channelSecret' => $lineBotSetting[0]->channel_secret]);

        $message_Builder = $this->createMsgBuilder($content_type, $message);
        $response = $bot->multicast($to_Send, $message_Builder);

        return [
            'messageStatusCode' => $response->getHTTPStatus(),
            "messageRaw" => $response->getRawBody(),"sendSuccess"=>$response->isSucceeded()
        ];
    }

    /**
     * @param $content
     * @param $bot
     * @param $line_id
     */
    private function senderPerMsg($content, $bot, $line_id)
    {
        if (strlen($content->formatted_message) > 0) {
            $message_Builder = $this->createMsgBuilder($content->content_type, $content);
            $response = $bot->pushMessage($line_id, $message_Builder);
            print_r($response);
            // echo $response->isSucceeded(); //getHTTPStatus()
        }

        if (sizeof($content->attachment)>0) {
            foreach ($content->attachment as $attachment) {
                $message_Builder_Attach = $this->createMsgBuilder($content->content_type, $attachment);
                $response_Attach = $bot->pushMessage($line_id, $message_Builder_Attach);
                echo $response_Attach->isSucceeded(); //getHTTPStatus()
            }
        }
    }

    private function createMsgBuilder($content_Type, $data, $follower = null, $typeDelivery = null, $send_type = null)
    {
        // 引数 $send_type は test送信時のみ、'test'が入る想定。
        // テスト配信アンケートを回答した際には集計を行わないために以下クエリパラメータを追記する。inatomi
        $testParam = $send_type == 'test' ? '&test=true' :'';

        $utils = new Utility();
        switch ($content_Type) {
            case "message":
                $convertMsg = $utils->imageEmoToLineUnicode($data->formatted_message);
                return new TextMessageBuilder($convertMsg);
            case "survey": //アンケート送信時
                if ($typeDelivery == 'magazine') {
                    $survey = Survey::where('magazine_id', $data->id)->first();
                } elseif ($typeDelivery == 'scenario') {
                    $survey = Survey::where('scenario_message_id', $data->id)->first();
                } else {
                    return;
                }
                $actions = [];
                for ($i=1; $i<=4; $i++) {
                    if ($survey->{'action_'.$i.'_type'} != null) {
                        if ($survey->{'action_'.$i.'_behavior'} == 'none') { // アクションなしの場合は「postback」
                            $survey->{'action_'.$i.'_auto_reply'} = $survey->{'action_'.$i.'_auto_reply'} == '' ? null : $survey->{'action_'.$i.'_auto_reply'};
                            $action = new PostbackTemplateActionBuilder(
                                /* ボタンラベル */     $survey->{'action_'.$i.'_label'},
                                /* 渡すデータ  */      'survey_id='.$survey->id.'&answer_no='.$i.$testParam,
                                /* 自動返信    */      $survey->{'action_'.$i.'_auto_reply'}
                            );
                        } else {
                            $action = new UriTemplateActionBuilder( // アクションなし以外の場合「uri」
                                /* ボタンラベル */     $survey->{'action_'.$i.'_label'}, //TODO:⬇URLをなおす
                                /* 渡すデータ  */      url('/').'/survey/answer?survey_id='.$survey->id.'&answer_no='.$i.'&user_id='.$testParam
                                /* 渡すデータ  */      //'http://10.0.1.9:3000/survey/answer?survey_id='.$survey->id.'&answer_no='.$i.'&user_id='.$testParam
                            );
                        }
                        $actions[] = $action;
                    }
                }
                $btn_template = new ButtonTemplateBuilder('アンケート', $survey->text, '', $actions);
                return          new TemplateMessageBuilder($survey->notification_message, $btn_template);

            case "image":
                /** @var MediaFile $data */
                return new ImageMessageBuilder($data->url, $data->featured_url);
            case "audio":
                /** @var MediaFile $data */
                // audioのdurationはミリ秒 mediaFileが持っているのは秒
                return new AudioMessageBuilder($data->url, $data->duration * 1000);
            case "video":
                /** @var MediaFile $data */
                return new VideoMessageBuilder($data->url, $data->featured_url);
            case "sticker":
                /** @var MediaFile $data */
                return new StickerMessageBuilder($data->package_id, $data->name);
            default:
                Log::alert('Unknown content type: ' . $content_Type);
                Log::alert('Unknown content type: ' . $data);
                $convertMsg = $utils->imageEmoToLineUnicode($data->formatted_message);
                return new TextMessageBuilder($convertMsg);
        }
    }


    public function sendScenarios()
    {
        $scenarioDeliveries = ScenarioDelivery::where('is_sent', 0)->get();
        foreach ($scenarioDeliveries as $scenarioDelivery) {
            if ($scenarioDelivery->schedule_date <= Carbon::now()) {
                $scenarioMessage = $scenarioDelivery->scenarioMessage;
                $account  = $scenarioMessage->scenario->account;
                $httpClient = new CurlHTTPClient($account->channel_access_token);
                $bot = new LINEBot($httpClient, ['channelSecret' => $account->channel_secret]);
                $messageBags = [];

                $message_format_manager =
                    new MessageFormatManager(
                        $account->id,
                        'scenario',
                        $scenarioMessage->formatted_message,
                        $scenarioMessage->id
                    );
                $scenarioMessage->formatted_message =
                    $message_format_manager->messageBuild($scenarioDelivery->pfUser->accountFollower);

                if (!empty($scenarioMessage->formatted_message)) {
                    $scenarioMessage->formatted_message =
                        $this->formatBrackets($scenarioMessage->formatted_message, $scenarioDelivery->pfUser);
                    $msg_builder = $this->createMsgBuilder(
                        $scenarioMessage->content_type,
                        $scenarioMessage,
                        $scenarioDelivery->pfUser->accountFollower,
                        'scenario'
                    );
                    $messageBags[] = [
                        'builder' => $msg_builder,
                        'attachment' => null
                    ];
                }

                if ($scenarioDelivery->is_attachment == 1) {
                    foreach ($scenarioMessage->messageAttachments as $attachment) {
                        $mediaFile = $attachment->mediaFile;
                        $attachment_msg_builder = $this->createMsgBuilder($mediaFile->type, $mediaFile);
                        $messageBags[] = [
                            'builder' => $attachment_msg_builder,
                            'attachment' => $mediaFile
                        ];
                    }
                }

                $response = null;
                foreach ($messageBags as $msgBag) {
                    if ($this->pushMessage($bot, $scenarioMessage->content_type, $msgBag, $scenarioDelivery)) {
                        $scenarioDelivery->is_sent = 1;
                        $scenarioDelivery->save();
                        $this->createAccountMessageForBag(
                            $account->id,
                            $scenarioDelivery->pfUser->accountFollower->source_user_id,
                            $msgBag
                        );
                    }
                }

                // delivery単位に送ってるということは、一人ずつ配信。
                $message_format_manager->end(1);
            }
        }

        return false;
    }

    public function sendMagazines()
    {
        try {
            DB::beginTransaction();
            /** @var Collection $magazines */
            $magazines = Magazine::where('schedule_at', '<=', Carbon::now())->get();
            $magazines = $magazines->filter(function ($m) {
                /** @var Magazine $m */
                return $m->magazineDeliveries()->where('is_sent', 0)->count();
            });

            foreach ($magazines as $magazine) {
                $magazineDeliveries = $magazine->magazineDeliveries->where('is_sent', 0)->all();
                if (count($magazineDeliveries) == 0) {
                    continue;
                }

                $message_format_manager = new MessageFormatManager(
                    $magazine->account->id,
                    'magazine',
                    $magazine->formatted_message,
                    $magazine->id
                );

                /** @var MagazineDelivery $magazineDelivery */
                foreach ($magazineDeliveries as $magazineDelivery) {
                    $account = $magazine->account;
                    $httpClient = new CurlHTTPClient($account->channel_access_token);
                    $bot = new LINEBot($httpClient, ['channelSecret' => $account->channel_secret]);
                    $messageBags = [];

                    $magazine->formatted_message =
                        $message_format_manager->messageBuild($magazineDelivery->pfUser->accountFollower);
                    if (!empty($magazine->formatted_message)) {
                        $magazine->formatted_message =
                            $this->formatBrackets($magazine->formatted_message, $magazineDelivery->pfUser);
                        $msg_builder = $this->createMsgBuilder(
                            $magazine->content_type,
                            $magazine,
                            $magazineDelivery->pfUser->accountFollower,
                            'magazine'
                        );
                        $messageBags[] = [
                            'builder' => $msg_builder,
                            'attachment' => null
                        ];
                    }

                    if ($magazineDelivery->is_attachment == 1) {
                        foreach ($magazine->magazineAttachments as $attachment) {
                            $mediaFile = $attachment->mediaFile;
                            $attachment_msg_builder = $this->createMsgBuilder($mediaFile->type, $mediaFile);
                            $messageBags[] = [
                                'builder' => $attachment_msg_builder,
                                'attachment' => $mediaFile
                            ];
                        }
                    }

                    $response = null;
                    foreach ($messageBags as $msgBag) {
                        if ($this->pushMessage($bot, $magazine->content_type, $msgBag, $magazineDelivery)) {
                            $magazineDelivery->is_sent = 1;
                            $magazineDelivery->save();
                            $this->createAccountMessageForBag(
                                $account->id,
                                $magazineDelivery->pfUser->accountFollower->source_user_id,
                                $msgBag
                            );
                        }
                    }

                    DB::commit();
                }

                $message_format_manager->end(count($magazineDeliveries));
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
        }
    }

    /**
     * 一斉配信、シナリオのメッセージ送信
     * @param LINEBot $bot
     * @param string $content_type
     * @param $msgBag
     * @param MagazineDelivery|ScenarioDelivery $deliveryRecord
     * @return boolean
     */
    private function pushMessage($bot, $content_type, $msgBag, $deliveryRecord)
    {
        $msgBuilder = $msgBag['builder'];
        if ($content_type  === 'survey') {
            $msgBuilder = $this->setUserId($msgBuilder, $deliveryRecord);
        }
        // メッセージ送信
        $response = $bot->pushMessage(
            $deliveryRecord->pfUser->accountFollower->source_user_id,
            $msgBuilder
        );

        if (is_null($response)) {
            return false;
        }

        return $response->isSucceeded();
    }

    private function formatBrackets($contentMessage, $pfUser)
    {
        return preg_replace('/\[you\]|\[フレンド名\]/', $pfUser->display_name, $contentMessage);
    }

    /**
     * @param $msgBuilder
     * @param $magazineDelivery
     * @return mixed
     */
    public function setUserId($msgBuilder, $magazineDelivery)
    {
        try {
            /*
            * urlクエリパラメーターのuser_idに値をセットするための記述
            */
            $reflection_msgBuilder      = new ReflectionClass($msgBuilder);
            $templateBuilder            = $reflection_msgBuilder->getProperty('templateBuilder');
            $templateBuilder->setAccessible(true);                                                      // アクセス許可
            $templateBuilder_value      = $templateBuilder->getValue($msgBuilder);                      // 値の取得

            $reflection_templateBuilder = new ReflectionClass($templateBuilder_value);
            $actionBuilders             = $reflection_templateBuilder->getProperty('actionBuilders');
            $actionBuilders->setAccessible(true);                                                       // アクセス許可
            $actionBuilders_array       = $actionBuilders->getValue($templateBuilder_value);            // 値の取得

            foreach ($actionBuilders_array as $key => $UriTemplateActionBuilder) {
                $reflection_UriTemplateActionBuilder = new ReflectionClass($UriTemplateActionBuilder);
                if ($reflection_UriTemplateActionBuilder->hasProperty('uri')) {
                    $uri            = $reflection_UriTemplateActionBuilder->getProperty('uri');
                    $uri->setAccessible(true);                                                             // アクセス許可
                    $uri_value      = $uri->getValue($UriTemplateActionBuilder);                      // 値の取得
                    $uri->setValue($UriTemplateActionBuilder, $uri_value.$magazineDelivery->pfUser->accountFollower->source_user_id);  // 値の上書き
                }
            }
            $actionBuilders->setValue($msgBuilder, $actionBuilders_array);
        } catch (ReflectionException $e) {
            Log::error($e);
        }

        return $msgBuilder;
    }


    /**
     * テスト配信対象に指定されているフォロワーに対してメッセージを送信する。
     * messageの構造は下記の通り
     *   { content_message: 'Send Message Test', ... ,
     *     attachments: [
     *       { id : 201, type: 'image', ... , },
     *       { id : 203, type: 'video', ... , },
     *     ]
     *   }
     * @param stdClass message
     * @return JsonResponse
     * @throws LineApiException
     */
    public function sendMsgToTester($message, $type)
    {
        // 引数 $type は magazine or scenario が入る
        $account    = Auth::user()->account;
        $testers    = $account->accountFollowers()->where('is_tester', 1)->get();
        $httpClient = new CurlHTTPClient($account->channel_access_token);
        $bot        = new LINEBot($httpClient, ['channelSecret' => $account->channel_secret]);

        //$message->content_message = $this->formatMessage($message);
        if (!$testers) {
            return response()->json([], Response::HTTP_NOT_FOUND);
        }
        
        $message->formatted_message = $type == 'auto_answer' ? $message->content_message : $message->formatted_message;
        $message_format_manager =
            new MessageFormatManager($account->id, $type, $message->formatted_message, $message->id);
        
        $results = [];
        foreach ($testers as $tester) {
            $message->formatted_message = $message_format_manager->messageBuild($tester);
    
            $message->formatted_message = $this->formatBrackets($message->formatted_message, $tester);
            $msgBuilder = $this->createMsgBuilder($message->content_type, $message, $tester, $type, 'test');
            /* メッセージ送信 */
            $response = $bot->pushMessage($tester->source_user_id, $msgBuilder);
            $body = $response->getJSONDecodedBody();
            
            if (isset($body['message'])) {
                $results[] = $body['message'];
            }
            if (!$response->isSucceeded()) {
                throw new LineApiException($response);
            }

            $this->createAccountMessage($account->id, $tester->source_user_id, $msgBuilder);

            if ($message->content_type == 'message') {
                if (property_exists($message, 'attachments')) {
                    $msgAttachments = $message->attachments;
                    foreach ($msgAttachments as $msgAttachment) {
                        try {
                            $mediaFile = \App\MediaFile::findOrFail($msgAttachment->id);
                            $messageBuilder = $this->createMsgBuilder($msgAttachment->type, $mediaFile);
                            
                            $response = $bot->pushMessage($tester->source_user_id, $messageBuilder);
                            $body = $response->getJSONDecodedBody();
                            
                            if (isset($body['message'])) {
                                $results[] = $body['message'];
                            }
                            if ($response->isSucceeded()) {
                                $this->createAccountMessage($account->id, $tester->source_user_id, $messageBuilder, $mediaFile);
                            }
                        } catch (ModelNotFoundException $e) {
                            $results[] = $e->getMessage();
                        }
                    }
                }
            }
        }

        $message_format_manager->end(count($testers));
        return response()->json($results, Response::HTTP_OK);
    }

    public function sendChatMsg($account, $follower, $message)
    {
        $httpClient = new CurlHTTPClient($account->channel_access_token);
        $bot = new LINEBot($httpClient, ['channelSecret' => $account->channel_secret]);
        // 送り先
        $destination = $follower->source_user_id;
        $contentType = 'text';
        $message_format_manager = new MessageFormatManager($account->id, 'talk', $message->formatted_message);
        $message->formatted_message = $message_format_manager->messageBuild($follower);

        $messageBuilder = $this->createMsgBuilder($contentType, $message, $follower);
        
        $response = $bot->pushMessage($destination, $messageBuilder);
        if (!$response->isSucceeded()) {
            throw new LineApiException($response);
        }
      
        $accountMessage = $this->createAccountMessage($account->id, $destination, $messageBuilder);
        $message_format_manager->end(1, $accountMessage->id);
    }

    public function sendChatMediaMsg($account, $follower, $contentType, $media_file_id)
    {
        $httpClient = new CurlHTTPClient($account->channel_access_token);
        $bot = new LINEBot($httpClient, ['channelSecret' => $account->channel_secret]);
        // 送り先
        $destination = $follower->source_user_id;
        $mediaFile = \App\MediaFile::findOrFail($media_file_id);
        $messageBuilder = $this->createMsgBuilder($contentType, $mediaFile);
        
        $response = $bot->pushMessage($destination, $messageBuilder);
        if (!$response->isSucceeded()) {
            throw new LineApiException($response);
        }
        $accountMessage = $this->createAccountMessage($account->id, $destination, $messageBuilder, $mediaFile);
    }

    private function createAccountMessageForBag($account_id, $destination, Array $messageBag)
    {
        $messageBuilder = $messageBag['builder'];
        $mediaFile = $messageBag['attachment'];
        $this->createAccountMessage($account_id, $destination, $messageBuilder, $mediaFile);
    }

    /**
     * 送信時のAccountMessageテーブルにレコードを生成する。
     * 呼び出し前には送信が成功したとこを確認すること。
     * 必要に応じてAccountMessageAttachmentテーブルも生成する。
     * @param int $account 送信したアカウントのaccount_id
     * @param string $destination 送信先UserId U4af4980629...
     * @param Array $messageBuilder送信したMessageBuilder
     * @param MediaFile $mediaFile 送信したメディアファイルモデル
     * @return AccountMessage
     */
    private function createAccountMessage($account_id, $destination, $messageBuilder, $mediaFile = null)
    {
        $send_message = $messageBuilder->buildMessage();
        $message_type = $send_message[0]['type'];

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
        $message->message_json_data = json_encode($send_message);
        
        $message->save();

        if (isset($mediaFile)) {
            $attachment = new AccountMessageAttachment();
            $attachment->account_id = $account_id;
            $attachment->message_id = $message->message_id;
            // TODO: $mediaFile->nameはハッシュ値になっているから意味ないかも
            $attachment->file_name = $mediaFile->name;
            $attachment->file_url = $mediaFile->url;
            $attachment->preview_file_url = $mediaFile->featured_url;
            if (isset($mediaFile->size)) {
                $attachment->file_size = $mediaFile->size;
            }
            $message->accountMessageAttachments()->save($attachment);
        }

        return $message;
    }
}
