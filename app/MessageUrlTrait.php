<?php

namespace App;

use Auth;
use App\Services\PfUserService;

/**
 * 〜MessageUrlとの連携持つテーブルで使用する
 */
trait MessageUrlTrait
{
    /**
     * messageUrlsを指すhasManyオブジェクトを返す。
     */
    abstract public function messageUrls();

    public function recreateUrlAction($url_actions = [])
    {
        if ($url_actions == null) {
            $url_actions = [];
        }

        $oldUrls = $this->messageUrls()->get();

        $oldIds = $oldUrls->pluck('id')->toArray();
        $newIds = array_column($url_actions, 'id');

        // oldにあってnewにない => 削除対象
        $delIds =  array_diff($oldIds, $newIds);
        if (!empty($delIds)) {
            $delUrls = $oldUrls->whereIn('id', $delIds);
            foreach ($delUrls as $delUrl) {
                // deleting監視のため個別に削除
                $delUrl->delete();
            }
        }
        // 新規か更新のデータ
        // indexは振り直し
        $index = 0;
        foreach ($url_actions as $url_action) {
            $murl = MessageUrl::find($url_action['id']);
            if (!isset($murl)) {
                $murl =  new MessageUrl();
                $murl->account_id = Auth::user()->account->id;
                $murl->url = $url_action['url'];
                $this->messageUrls()->save($murl);
            } else {
                // actionテーブルは一旦削除
                $actions = $murl->messageUrlActions;
                foreach ($actions as $action) {
                    $action->delete();
                }
            }
            $murl->index = $index;
            $murl->save();
            $murl->createStoreActions($url_action['actions']);
            $index++;
        }
    }
}
