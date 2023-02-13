<?php
 namespace App\LineEvents;

use App\Account;
use App\LineEvents\EventModels\Follow;
use App\LineEvents\EventModels\Unfollow;
use App\LineEvents\LineUtils;
use Illuminate\Support\Facades\Response;
use App\LineEvents\UnFollowEvent;
use App\LineEvents\FollowEvent;
use App\LineEvents\MessageEvent;
use App\LineEvents\AccountLink;
use App\LineUserManegerSetting;

use Illuminate\Support\Facades\Log;

class EventHandler
{
    public function __construct()
    {
        $this->lineUtils = new LineUtils();
        $this->follow = new FollowEvent();
        $this->unfollow = new UnFollowEvent();
        $this->messageEvent = new MessageEvent();
        $this->accountLink = new AccountLink();
        $this->line_User_Man = new LineUserManegerSetting();
        $this->surveys_answer = new SurveysAnswer();
    }

    /** choice what is the appropirate event to execute
     * @param array of the line event
     *
     * @return \Illuminate\Http\JsonResponse|void
     * @throws \Exception
     */
    public function sellectEvent($data, $header, $webhook_token)
    {
        $eventData = json_decode($data);
        Log::info("lline log content :".json_encode($eventData));
        if (!$eventData->destination) {
            return;
        }
        foreach ($eventData->events as $event) {
            switch ($event->type) {
                case 'follow':
                    if (\App::environment() != 'local') {
                        if ($this->keyChecker($webhook_token, $data, $header)) {
                            $account = Account::where('webhook_token', $webhook_token)->first();
                        };
                    } else {
                        $account = Account::where('webhook_token', $webhook_token)->first();
                    }

                    if ($account) {
                        $follow = new Follow($account, $eventData->destination, $event);
                        $this->follow->userFollow($follow);
                        return Response::json([], 200);
                    }
                    break;
                case 'unfollow':
                    if (\App::environment() != 'local') {
                        if ($this->keyChecker($webhook_token, $data, $header)) {
                            $account = Account::where('webhook_token', $webhook_token)->first();
                        };
                    } else {
                        $account = Account::where('webhook_token', $webhook_token)->first();
                    }

                    if ($account) {
                        $unFollow = new Unfollow($account, $eventData->destination, $event);
                        $this->unfollow->userUnFollow($unFollow);
                        return Response::json([], 200);
                    }
                    break;
                case 'message':
                    if (\App::environment() != 'local') {
                        if ($this->keyChecker($webhook_token, $data, $header)) {
                            $account = Account::where('webhook_token', $webhook_token)->first();
                        };
                    } else {
                        $account = Account::where('webhook_token', $webhook_token)->first();
                    }
                    Log::info("lline log content adasd : " . print_r($account, true));

                    if ($account) {
                        $lineData['channel_access_token'] = $account->channel_access_token;
                        $lineData['account_id'] = $account['id'];
                        $lineData['channel_id'] = $account["channel_id"];
                        $lineData['replyToken'] = $event->replyToken;
                        $lineData['source_user_id'] = $event->source->userId;
                        $lineData['source_type'] = $event->source->type;
                        $lineData['message'] = json_encode($event->message, JSON_UNESCAPED_UNICODE);
                        $lineData['message_id'] = $event->message->id;
                        $lineData['message_type'] = $event->message->type;
                        switch ($event->message->type) {
                            case 'text':
                                $lineData['message_text'] = $event->message->text;
                                break;
                            case 'file':
                                $util = new LineUtils();
                                list($fileName, $fileUrl, $fileSize) = $util->getMessageContent($account->channel_access_token, $account->channel_secret, $event->message->id);
                                $lineData['file_name'] = $event->message->fileName;
                                $lineData['file_url'] = $fileUrl;
                                $lineData['preview_file_url'] = $fileUrl;
                                $lineData['file_size'] = $event->message->fileSize;
                                break;
                            case 'image':
                            case 'audio':
                            case 'video':
                                $util = new LineUtils();
                                list($fileName, $fileUrl, $fileSize) = $util->getMessageContent($account->channel_access_token, $account->channel_secret, $event->message->id);
                                $lineData['file_name'] = $fileName;
                                $lineData['file_url'] = $fileUrl;
                                $lineData['preview_file_url'] = $fileUrl;
                                $lineData['file_size'] = $fileSize;
                                break;
                            case 'location':
                            default:
                                break;
                        }
                        $lineData['destination'] = $eventData->destination;
                        $lineData['timestamp'] = $event->timestamp;
                        $this->messageEvent->index($lineData);
        
                        return Response::json([], 200);
                    }
                    break;
                case "postback":
                    $postback_Data = json_decode($event->postback->data);
                    Log::info("lline log content :".$event->postback->data);
                    if (isset($postback_Data->action)) {
                        switch ($postback_Data->action) {
                            case "link_account":
                                $line_User_Id = LineUserManegerSetting::where('link_token', $postback_Data->data)->firstOrFail();
                                if ($line_User_Id) {
                                    Log::info("lline log content :".$line_User_Id);
                                    $line_User_Id->bot_dest_id = $eventData->destination;
                                    $line_User_Id->link_token = null;
                                    $line_User_Id->save();
                                    //send reply token to user
                                } else {
                                    Log::info("lline log content : error 404");
                                }
                                break;
                        }
                    }

                    /*
                    * アンケート回答保存
                    */
                    $this->surveys_answer->saveSurveysAnswer($event);

                    return Response::json([], 200);
                    break;
                case 'linkAccountLoop':
                    $this->accountLink->accountLinkIndex();
                    break;
                default:
                    break;
            }
        }
        return Response::json([], 404);
    }

    /**
     * @param $eventData
    * @param $raw_data
    * @param $header
    * @return bool
    */
    private function keyChecker($webhook_token, $raw_data, $header)
    {
        $account = Account::where('webhook_token', $webhook_token)->first();
        Log::info("user_line_det: ".$account);
        if ($account) {
            $hash = hash_hmac('sha256', $raw_data, $account->channel_secret, true);
            $signature = base64_encode($hash);
            Log::info("lline header".$header ."==". $signature);
            return $header == $signature;
        }
        return false;
    }
}
