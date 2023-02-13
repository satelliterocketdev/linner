<?php

namespace App\Log;

use \Illuminate\Log\Writer;
use \Illuminate\Support\Facades\App;

class LinnerWriter extends Writer
{
    /**
     * Write a message to Monolog.
     *
     * @param  string  $level
     * @param  string  $message
     * @param  array  $context
     * @return void
     */
    protected function writeLog($level, $message, $context)
    {
        parent::writeLog($level, $message, $context);
//        $this->sendChatWork($message);
        $this->sendLogMessage($message);
    }

    public function sendLogMessage($data) {
        $this->sendTeams($data);
    }

    /**
     * @param $data
     */
    private function sendChatWork($data)
    {
        try {
            $chatToken = env('CHAT_WORK_TOKEN');
            $chatGroupId = env('CHAT_WORK_GROUP_ID');
            if (!empty($chatToken) && !empty($chatGroupId)) {

                $message = $this->formatSendMessage($data);

                $headers = [
                    'X-ChatWorkToken: ' . $chatToken
                ];

                $option = [
                    'body' => App::environment() . "\n[code]" . implode("\n", $message) . "[/code]",
                    'self_unread' => '1'
                ];

                $ch = curl_init('https://api.chatwork.com/v2/rooms/' . $chatGroupId . '/messages');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($option));
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $response = curl_exec($ch);
                curl_close($ch);
            }

        } catch (\Exception $e) {
            parent::error($e);
        }
    }

    /**
     * @param $data
     */
    private function sendTeams($data) {
        $webhookUrl = "https://outlook.office.com/webhook/95ce0990-9090-4adc-a0c4-0597931942d0@32ca5d30-5c89-44fc-93ef-637bfeeda703/IncomingWebhook/736684429835443d8ea79fcaa2f54e4e/d9d50dbc-1570-42dd-8d61-b110e6d87c99";

        $message = $this->formatSendMessage($data);

        $json = json_encode($message);
        $headers = [
            'Content-Type:application/json'
        ];

        $option = [
            'text' => App::environment() . "\n<pre>" . implode("\n", $message) . "</pre>"
//            'text' => "hello world"
        ];

        $ch = curl_init($webhookUrl);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($option));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
    }

    private function formatSendMessage($data) {
        $message = [];

        if (array_key_exists('REMOTE_ADDR', $_SERVER)) {
            $message[] = $_SERVER['REMOTE_ADDR'];
            $message[] = "";
        }

        if (array_key_exists('REQUEST_SCHEME', $_SERVER)) {
            $message[] = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $message[] = "";
        }

        if ($data instanceof \Exception) {
            $message = array_merge($message, $this->formatException($data));
        } elseif (is_array($data)) {
            $message = array_merge($message, $data);
        } else {
            $message[] = $data;
        }

        return $message;
    }

    private function formatException(\Exception $exception)
    {
        $message = [];
        $message[] = get_class($exception);
        $message[] = $exception->getMessage();
        $message[] = "in " . $exception->getFile() . " line " . $exception->getLine();
        $message[] = "Stack trace";
        $count = 0;
        foreach ($exception->getTrace() as $ary) {
            $m = "";
            if (array_key_exists("file", $ary)) {
                $m .= $ary["file"] . "(" . $ary["line"] . "): ";
            }

            if (array_key_exists("class", $ary)) {
                $m .= $ary["class"];
            }
            if (array_key_exists("type", $ary)) {
                $m .= $ary["type"];
            }
            if (array_key_exists("function", $ary)) {
                $m .= $ary["function"];
            }

            $m = "#" . $count++ . " " . $m;
            $message[] = $m;
        }

        return $message;
    }
}
