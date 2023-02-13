<?php namespace App;
  /*
  * This is the global function is kept
  */

  class Utility {

    public function randomKey($randLen){
      $char =  "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-_";
      $randString = "";
      for($i=0;$i<$randLen;$i++){
        $randString .=$char[rand(0, strlen($char)-1)];
      }
      $randKey = mt_rand(1000000, 9999999).mt_rand(1000000, 9999999).$randString;
      return str_shuffle($randKey);
    }

    public function imageEmoToLineUnicode($string){
      $re = '/<img.+?src=[\'"](?P<src>.+?)[\'"].data-type="emoji".*?>/i';
      $result = preg_replace_callback(
        $re,
        function($matches){
          return $this->matchReplaceCallback($matches['src']);
        },
        $string 
      );
      return $result;
    }

    private function matchReplaceCallback($imgeToconvert){
      $re = '/0x(\w+)/i';
      preg_match($re, $imgeToconvert, $matches1);
      $code = $matches1[1]; // emoji code remove the 0x
      $bin = hex2bin(str_repeat('0', 8 - strlen($code)) . $code);
      $emoticon =  mb_convert_encoding($bin, 'UTF-8', 'UTF-32BE');
      return $emoticon;
    }

  }
 