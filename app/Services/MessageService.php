<?php

namespace App\Services;

use \App\MessageUrl;
use \App\User;
use \App\Account;

class MessageService
{
    /**
     * HTMLタグを含むメッセージを送信用の文に変換する。
     * messageUrlsはオプション。actionurlの変換を行う場合はMessageUrlの生成が済んでいること。
     * ・一行分の情報はdivタグが表現できているものとしてbrタグは無視する。
     * ・divタグは除去し、文末に\nを追加。
     * ・URLクリックアクション用のspanタグ(class actionurl)は、embeddedurlタグに変換する。
     * @param int $acount_id アカウントID
     * @param string $message メッセージ本文
     * @param Illuminate\Database\Eloquent\Collection messageUrls:MessageUrlモデルのコレクション
     */
    public function formatMessage($acount_id, $message, $messageUrls = null)
    {
        $dom = new \DOMDocument;
        // htmlとして解析する
        $content =  '<?xml encoding="utf-8" ?><body>'.$message.'</body>';
        $content = str_replace('<br>', '<br/>', $content);
        
        $dom->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        if (isset($messageUrls) && !$messageUrls->isEmpty()) {
            $this->convertEmbeddedUrl($dom, $messageUrls);
        }

        $this->convertInternalUrl($acount_id, $dom);

        $result = '';
        $this->recursiveOutput($dom, $dom->documentElement, $result);
        return $result;
    }

    private function convertEmbeddedUrl($dom, $messageUrls)
    {
        $xpath = new \DOMXPath($dom);
        $nodeList = $xpath->query('//*[@class="actionurl"]');
        $c = $nodeList->length;

        for ($i = 0; $i < $c; $i++) {
            $urlNode = $nodeList[$i];
            $messageurl = $messageUrls[$i];
            $actions = $messageurl->messageUrlActions()->get();
            if (count($actions) > 0) {
                // 要 書き換えのurl
                $element = $dom->createElement('embeddedurl');
                $element->setAttribute('class', 'action');
                $element->setAttribute('cid', $messageurl->id);
                $ee = $urlNode->parentNode->replaceChild($element, $urlNode);
            }
        }
    }

    private function convertInternalUrl($acount_id, $dom)
    {
        $xpath = new \DOMXPath($dom);
        $nodeList = $xpath->query('//*[@class="internalurl"]');
        $clickratePath = route('clickrate.route', ['token' => '']) .'/'; // "http://APPURL/click/"
        $conversionPath = route('conversion.route', ['token' => '']) .'/';  // "http://APPURL/cv/"
        // "click"で始まるパス=>clickrate "cv"で始まるパス=>conversion
        foreach ($nodeList as $urlNode) {
            $urltext = trim($urlNode->textContent);
            // 一応チェック
            if (strpos($urltext, $clickratePath) === 0) {
                $subPath = substr($urltext, strlen($clickratePath));
                if (strlen($subPath)) {
                    $item = Account::find($acount_id)->clickrateItems()->where('clickrate_token', $subPath)->first();
                    if (isset($item)) {
                        $element = $dom->createElement('embeddedurl');
                        $element->setAttribute('class', 'clickrate');
                        $element->setAttribute('cid', $item->id);
                        $ee = $urlNode->parentNode->replaceChild($element, $urlNode);
                    }
                }
            } elseif (strpos($urltext, $conversionPath) === 0) {
                $subPath = substr($urltext, strlen($conversionPath));
                if (strlen($subPath)) {
                    // アカウントに紐づく全ユーザのコンバージョン情報が対象
                    // 1 select * from `users` where `account_id` = ?'
                    $users = User::where('account_id', $acount_id)->get();
                    // 2 select * from `conversions` where `conversions`.`user_id` in (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    //   and `conversion_token` = ? and `conversions`.`deleted_at` is null'
                    $item = $users->load([
                        'conversions' => function ($query) use ($subPath) {
                            $query->where('conversion_token', $subPath);
                        }
                    ])->first();
            
                    // ->conversions()->where('conversion_token', $subPath)->first();
                    if (isset($item)) {
                        $element = $dom->createElement('embeddedurl');
                        $element->setAttribute('class', 'conversion');
                        $element->setAttribute('cid', $item->id);
                        $element->setAttribute('token', $subPath);
                        $ee = $urlNode->parentNode->replaceChild($element, $urlNode);
                    }
                }
            }
        }
    }

    private function recursiveOutput($dom, $elm, &$result)
    {
        if ($elm->nodeType == XML_ELEMENT_NODE && $elm->tagName == 'div') {
            // 改行コードはダブルクォーテーションで括る
            $pre = $elm->previousSibling;
            if (isset($pre) && $pre->nodeType == XML_TEXT_NODE) {
                $result .= "\n";
            }
            if (isset($elm->nextSibling)) {
                $elm->appendChild($dom->createTextNode("\n"));
            }
        }

        if (!$elm->hasChildNodes()) {
            // TODO: 絵文字に使用しているimgタグはここで絵文字Unicodeに変換した方が良さそう
            if ($elm->nodeType == XML_ELEMENT_NODE) {
                if (in_array($elm->tagName, ['img', 'embeddedurl'])) {
                    $result .= $elm->ownerDocument->saveXML($elm);
                    return;
                }
            }
            $result .= $elm->textContent;
            return;
        }

        foreach ($elm->childNodes as $node) {
            $this->recursiveOutput($dom, $node, $result);
        }
    }
}
