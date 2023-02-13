<?php

namespace App\Services;

use \App\LineEvents\RichMenuEvent;
use \App\Services\PfUserService;
use \App\RichMenuItem;

/**
 * Auth が使える環境で利用する
 */
class PfUserAuthService extends PfUserService
{
    /**
     * $pfUserにリッチメニューを設定する。
     * すでに他のリッチメニューがついていた場合、付け替える
     */
    public function setRichMenu($pfUser, $richmenuId)
    {
        $richMenuEvent = new RichMenuEvent();

        // hasOne
        $menu_delivery = $pfUser->richMenuDeliveries;
        if (isset($menu_delivery)) {
            if ($menu_delivery->rich_menu_item_id == $richmenuId) {
                // すでに指定のものがついている
                return;
            } else {
                // 別のメニューが付いているので外す
                $this->unlinkRichMenu($pfUser);
            }
        }

        // 指定されたメニューを付ける
        $richmenu = RichMenuItem::find($richmenuId);
        if (isset($richmenu)) {
            $richMenuEvent->linkRichMenu($pfUser->accountFollower->source_user_id, $richmenu->rich_menu_id);
            $pfUser->richMenuDeliveries()->create([
                'rich_menu_item_id' => $richmenu->id
            ]);
            $richmenu->update([
                'is_active' => true
            ]);
        }
    }

    /**
     * $pfUserからリッチメニューをはずす。
     * 指定と異なるリッチメニューが付いている場合は処理しない。
     */
    public function removeRichMenu($pfUser, $richmenuId)
    {
        $menu_delivery = $pfUser->richMenuDeliveries;
        if (!isset($menu_delivery)) {
            // 何も付いてない
            return;
        }

        if ($menu_delivery->rich_menu_item_id != $richmenuId) {
            // 別のメニューが付いている
            return;
        }

        $this->unlinkRichMenu($pfUser);
    }

    private function unlinkRichMenu($pfUser)
    {
        $richMenuEvent = new RichMenuEvent();
        
        $menu_delivery = $pfUser->richMenuDeliveries;
        if (!isset($menu_delivery)) {
            return;
        }

        $richMenuEvent->unlinkRichMenu($pfUser->accountFollower->source_user_id);
        $oldMenuitem = $menu_delivery->richMenuItem;
        $menu_delivery->delete();

        $oldMenuitem->update([
            'is_active' => $oldMenuitem->richMenuDeliveries->count() > 0 ? true : false
        ]);
    }
}
