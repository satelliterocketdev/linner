<?php

namespace App\LineEvents;

use App;
use App\Account;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use Storage;
use Illuminate\Support\Facades\Log;

class LineUtils
{

    public function accountProfile(Account $account)
    {
        $ch = curl_init();
//    curl_setopt($ch,CURLOPT_URL,"https://api.line.me/v2/profile/");
        curl_setopt($ch, CURLOPT_URL, "https://api.line.me/v2/bot/profile/" . $account->account_user_id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer '. $account->channel_access_token
        ]);
        $userProfile = curl_exec($ch);
        curl_close($ch);
        // Log::info($userProfile);
        return json_decode($userProfile);// $userProfile;
    }

    public function userFollowProfile($userId, $accessToken)
    {
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.line.me/v2/bot/profile/".$userId);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer '.$accessToken
        ]);
        $userProfile = curl_exec($ch);
        curl_close($ch);
        // Log::info($userProfile);

        return new App\LineEvents\EventModels\UserProfile($userProfile);
    }

    public function getMessageContent($accessToken, $channelSecret, $messageId)
    {
        // https://developers.line.biz/ja/reference/messaging-api/#get-content
        $httpClient = new CurlHTTPClient($accessToken);
        $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $channelSecret]);
        $response = $bot->getMessageContent($messageId);
        if ($response->isSucceeded()) {
            if (App::environment() !== 'production') {
                // https://www.ritolab.com/entry/7
                $fileName = hash('sha256', uniqid()) . '.' . substr($response->getHeader('Content-Type'), 6);
                $filePath = 'images/'. $fileName;
                $disk = Storage::disk('local');
                $disk->put($filePath, $response->getRawBody());
                return [$fileName, $disk->url($filePath), $response->getHeader('Content-Length')];
            } else {
                // https://qiita.com/tiwu_official/items/ecb115a92ebfebf6a92f
            }
        } else {
            error_log($response->getHTTPStatus() . ' ' . $response->getRawBody());
        }

        return['', ''];
    }

    public function replyAutoMessage($data, $reply_message)
    {
        $headers = [
            'Authorization: Bearer ' . $data['channel_access_token'],
            'Content-Type: application/json; charset=utf-8',
        ];

        $post = [
            'replyToken' => $data['replyToken'],
            'messages' => [
                [
                    'type' => $data['message_type'],
                    'text' => $reply_message
                ],
            ],
        ];
        Log::info("replyJson : " . json_encode($post));

        $ch = curl_init('https://api.line.me/v2/bot/message/reply');
        $options = [
            CURLOPT_POST => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_POSTFIELDS => json_encode($post),
        ];
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        Log::info("reply result : " . $result);
        curl_close($ch);

        return $post;
    }
}
