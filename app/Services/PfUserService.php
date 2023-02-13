<?php

namespace App\Services;

use \App\PfUserTagManagement;
use \App\ScenarioDelivery;

class PfUserService
{
    private function afterTagged($pfUser, $action)
    {
        switch ($action->type) {
            case 0:// tag
                $tag = $action->tagManagement;
                if ($action->option == 'first') {
                    $this->addTag($pfUser, $tag->id);
                } elseif ($action->option == 'second') {
                    $this->removeTag($pfUser, $tag->id);
                }
                break;
            case 1:// scenario
                $scenario = $action->scenario;
                if ($action->option == 'first') {
                    $this->addScenario($pfUser, $scenario->id);
                } elseif ($action->option == 'second') {
                    $this->removeScenario($pfUser, $scenario->id);
                }
                break;
            default:
                break;
        }
    }

    /**
     * $pfUserに付与済みのタグを除去する
     * @param \App\PfUser $pfUser
     * @param int $tagId
     */
    public function removeTag($pfUser, $tagId)
    {
        $pf_tag = $pfUser->pfUserTagManagements()->where('tag_managements_id', $tagId)->get();
        foreach ($pf_tag as $utag) {
            // 単純に削除
            $utag->delete();
        }
    }

    /**
     * $pfUserにタグを付与する
     * @param \App\PfUser $pfUser
     * @param int $tagId
     */
    public function addTag($pfUser, $tagId)
    {
        $account = $pfUser->accountFollower->account;
        $tag = $account->tagManagements()->where('id', $tagId)->first();
        if (!isset($tag)) {
            return;
        }
        // すでに付いている場合は処理打ち切り
        if ($pfUser->pfUserTagManagements()->where('tag_managements_id', $tagId)->exists()) {
            return;
        }

        // タグ登録人数制限チェック
        if (!$tag->no_limit) {
            if ($tag->limit <= $tag->countTaggedUser()) {
                return;
            }
        }

        // PfUserTagManagementの生成
        $pf_tag = new PfUserTagManagement();
        $pf_tag->tag_managements_id = $tagId;
        $pfUser->pfUserTagManagements()->save($pf_tag);

        // タグ付け後アクションの実行
        // アクションはindexの順に行う。
        $tagActions = $tag->tagActions()->orderBy('type', 'asc')->orderBy('index', 'asc')->get();
        foreach ($tagActions as $action) {
            $this->afterTagged($pfUser, $action);
        }
    }
    
    /**
     * $pfUserに設定済みのシナリオを除去する
     * @param \App\PfUser $pfUser
     * @param int $scenarioId
     */
    public function removeScenario($pfUser, $scenarioId)
    {
        $deliveries = $pfUser->scenarioDeliveries()->get();
        foreach ($deliveries as $delivery) {
            if ($delivery->getScenarioId() == $scenarioId) {
                $delivery->delete();
            }
        }
    }

    private function createScenarioDelivery($pfUserId, $message)
    {
        $delivery = new ScenarioDelivery();
        // $delivery->type = ' ';// 不要
        // $delivery->content = ' '; // 不要
        $delivery->pf_user_id = $pfUserId;
        $delivery->schedule_date = $message->schedule_date != null ? $message->schedule_date : date('Y-m-d H:i:s');
        // $delivery->is_attachment = 0; // 不要
        // $delivery->media_file_id = 0; // 不要
        $delivery->is_sent = 0;

        $message->deliveries()->save($delivery);
    }

    private function existsMessage($deliveries, $messageId)
    {
        return $deliveries->contains(function ($v, $k) use ($messageId) {
            return $v->scenario_message_id ==  $messageId;
        });
    }

    /**
     * $pfUserにシナリオを設定する
     * @param \App\PfUser $pfUser
     * @param int $scenarioId
     */
    public function addScenario($pfUser, $scenarioId)
    {
        $account = $pfUser->accountFollower->account;
        $scenario = $account->scenarios()->where('id', $scenarioId)->first();
        if (!isset($scenario)) {
            return;
        }
        $pfUserId = $pfUser->id;
        $preDeliveries = $pfUser->scenarioDeliveries()->get();
        $scenarioMessages = $scenario->scenarioMessages()->where('is_active', 1)->where('is_draft', 0)->get();
        // シナリオに紐づくメッセージ分のScenario Delivery を生成する。
        foreach ($scenarioMessages as $message) {
            $messageId = $message->id;

            if ($this->existsMessage($preDeliveries, $messageId) == false) {
                $this->createScenarioDelivery($pfUserId, $message);
            }
        }
    }
}
