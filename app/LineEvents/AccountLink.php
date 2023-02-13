<?php
  namespace App\LineEvents;

  use Illuminate\Support\Facades\Auth;
  use App\LineUserManegerSetting;
  use App\LineAccountDetails;
  use App\LineEvents\MessageEvent;
  // use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
  // use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
  use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
  use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
  use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
  
  class AccountLink {
    public function accountLinkIndex(){

    }

    public function sendLinkAccountRequest(){
      //get token request
      $token = $this->getLinkToken();
      //send push messgae usng token
      $pushMsg = $this->sendPushMsg($token);
      return $pushMsg;
    }

    private function getLinkToken(){
      $line_User_Id = LineAccountDetails::where('user_id', Auth::id())->first();
      $line_Manager_Id = LineUserManegerSetting::where('user_id', Auth::id())->first();

      $curl = curl_init();

      curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.line.me/v2/bot/user/".$line_User_Id->line_user_id."/linkToken",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "",
        CURLOPT_HTTPHEADER => array(
          "Authorization: Bearer ".$line_Manager_Id->channel_access_token,
          "cache-control: no-cache"
        ),
      ));

      $response = curl_exec($curl);
      $err = curl_error($curl);

      curl_close($curl);

      if ($err) {
        return "cURL Error #:" . $err;
      } else { 
        $json_Decoded = json_decode($response);
        $line_Manager_Id->link_token =$json_Decoded->linkToken;
        $line_Manager_Id->save();
        return $response;
      }
    }

    public function sendPushMsg($token){
      $json_Decoded = json_decode($token);
      $line_User_Id = LineAccountDetails::where('user_id', Auth::id())->first();
      $line_Manager_Id = LineUserManegerSetting::where('user_id', Auth::id())->first();
      $base_Url = url("");

      $confirmMessageBuilder = new ConfirmTemplateBuilder(
        "Confirm Link Account",
        [
          new PostbackTemplateActionBuilder('Yes',json_encode(['action'=>'link_account', 'data'=>$json_Decoded->linkToken]),'Confirmed Link Account'),
          new MessageTemplateActionBuilder('No', 'Account Link Denied!'),
        ]
      );
      $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($line_Manager_Id->channel_access_token);
      $bot = new \LINE\LINEBot($httpClient, ['channelSecret' =>  $line_Manager_Id->channel_secret]);
      $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder("Account Link",$confirmMessageBuilder);
      $response = $bot->pushMessage($line_User_Id->line_user_id, $textMessageBuilder);
      echo $response->isSucceeded();// . ' ' . $response->getRawBody();
      if($response->isSucceeded()){
        echo "Success";
      }else{
        echo "Failed";
      }
    }
  }
?>

