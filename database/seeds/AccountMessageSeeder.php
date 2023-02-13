<?php

use App\Account;
use App\AccountMessage;
use App\AccountMessageAttachment;
use Illuminate\Database\Seeder;

class AccountMessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jsonString = "{
  \"destination\": \"xxxxxxxxxx\", 
  \"events\": [
    {
      \"replyToken\": \"0f3779fba3b349968c5d07db31eab56f\",
      \"type\": \"message\",
      \"timestamp\": 1462629479859,
      \"source\": {
        \"type\": \"user\",
        \"userId\": \"U4af4980629...\"
      },
      \"message\": {
        \"id\": \"325708\",
        \"type\": \"text\",
        \"text\": \"Hello, world\"
      }
    },
    {
      \"replyToken\": \"nHuyWiB7yP5Zw52FIkcQobQuGDXCTA\",
      \"type\": \"message\",
      \"timestamp\": 1462629479859,
      \"source\": {
        \"type\": \"user\",
        \"userId\": \"U4af4980629...\"
      },
      \"message\": {
        \"id\": \"325708\",
        \"type\": \"image\",
        \"contentProvider\": {
          \"type\": \"line\"
        }
      }
    },
    {
      \"replyToken\": \"nHuyWiB7yP5Zw52FIkcQobQuGDXCTA\",
      \"type\": \"message\",
      \"timestamp\": 1571894381189,
      \"source\": {
        \"type\": \"user\",
        \"userId\": \"U4af4980629...\"
      },
      \"message\": {
        \"id\": \"325708\",
        \"type\": \"video\",
        \"duration\": 60000,
        \"contentProvider\": {
          \"type\": \"external\",
          \"originalContentUrl\": \"http://techslides.com/demos/sample-videos/small.mp4\",
          \"previewImageUrl\": \"http://localhost:3000/img/stickers/brown-cony-and-sally/52002763.png\"
        }
      }
    },
    {
      \"replyToken\": \"nHuyWiB7yP5Zw52FIkcQobQuGDXCTA\",
      \"type\": \"message\",
      \"timestamp\": 1571894381189,
      \"source\": {
        \"type\": \"user\",
        \"userId\": \"U4af4980629...\"
      },
      \"message\": {
        \"id\": \"325708\",
        \"type\": \"file\",
        \"fileName\": \"file.txt\",
        \"fileSize\": 2138
      }
    },
    {
      \"replyToken\": \"nHuyWiB7yP5Zw52FIkcQobQuGDXCTA\",
      \"type\": \"message\",
      \"timestamp\": 1571894381189,
      \"source\": {
        \"type\": \"user\",
        \"userId\": \"U4af4980629...\"
      },
      \"message\": {
        \"id\": \"325708\",
        \"type\": \"location\",
        \"title\": \"my location\",
        \"address\": \"〒150-0002 東京都渋谷区渋谷２丁目２１−１\",
        \"latitude\": 35.65910807942215,
        \"longitude\": 139.70372892916203
      }
    },
    {
      \"replyToken\": \"0f3779fba3b349968c5d07db31eab32f\",
      \"type\": \"message\",
      \"timestamp\": 1566527113229,
      \"source\": {
        \"type\": \"user\",
        \"userId\": \"U4af4980630...\"
      },
      \"message\": {
        \"id\": \"325708\",
        \"type\": \"text\",
        \"text\": \"Cheers\"
      }
    }
  ]
}";
        $json = json_decode($jsonString);
        foreach ($json->events as $event) {
            $accountMessage = new AccountMessage();
            // TODO: AccountTableが作成されたら変更する
            $accountMessage->account_id = Account::all()->first()->id;
            $accountMessage->destination = $json->destination;

            $accountMessage->reply_token = $event->replyToken;
            $accountMessage->type = $event->type;
            $accountMessage->timestamp = $event->timestamp;
            $accountMessage->source_type = $event->source->type;
            $accountMessage->source_user_id = $event->source->userId;
            $accountMessage->message_id = $event->message->id;
            $accountMessage->message_type = $event->message->type;
            $accountMessage->message_json_data = json_encode($event->message);
            $accountMessage->save();

            if ($accountMessage->message_type === "image") {
                $accountMessageAttachment = new AccountMessageAttachment();
                $accountMessageAttachment->account_id = $accountMessage->account_id;
                $accountMessageAttachment->account_message_id = $accountMessage->id;
                $accountMessageAttachment->message_id = $accountMessage->message_id;
                $accountMessageAttachment->file_url = "http://localhost:3000/img/stickers/brown-cony-and-sally/52002762.png";
                $accountMessageAttachment->file_name = basename($accountMessageAttachment->file_url);
                $accountMessageAttachment->preview_file_url = "http://localhost:3000/img/stickers/brown-cony-and-sally/52002762.png";
                $accountMessageAttachment->save();
            } else if ($accountMessage->message_type === "video") {
                $accountMessageAttachment = new AccountMessageAttachment();
                $accountMessageAttachment->account_id = $accountMessage->account_id;
                $accountMessageAttachment->account_message_id = $accountMessage->id;
                $accountMessageAttachment->message_id = $accountMessage->message_id;
                $accountMessageAttachment->file_url = $event->message->contentProvider->originalContentUrl;
                $accountMessageAttachment->file_name = basename($accountMessageAttachment->file_url);
                $accountMessageAttachment->preview_file_url = $event->message->contentProvider->previewImageUrl;
                $accountMessageAttachment->save();
            } else if ($accountMessage->message_type === "file") {
                $accountMessageAttachment = new AccountMessageAttachment();
                $accountMessageAttachment->account_id = $accountMessage->account_id;
                $accountMessageAttachment->account_message_id = $accountMessage->id;
                $accountMessageAttachment->message_id = $accountMessage->message_id;
                $accountMessageAttachment->file_url = "http://localhost:3000/img/stickers/brown-cony-and-sally/52002762.png";
                $accountMessageAttachment->file_name = $event->message->fileName;
                $accountMessageAttachment->file_size = intval($event->message->fileSize);
//                $accountMessageAttachment->preview_file_url = $event->message->contentProvider->previewImageUrl;
                $accountMessageAttachment->save();
            }
        }
    }
}
