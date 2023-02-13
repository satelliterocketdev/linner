<?php

namespace App;

use Auth;
use App\Services\PfUserService;
use Illuminate\Database\Eloquent\Relations\HasMany;
use stdClass;

/**
 * 〜actions系テーブルの情報をクライアント側のインターフェイスに構築し直す。
 * 使用の際にはgetActionsを定義すること。
 */
trait ActionTrait
{
    /**
     * 〜actionsを指すhasManyオブジェクトを返す。
     * @return HasMany
     */
    abstract public function getActions();

    public function getFormattedActions()
    {
        $actions = $this->getActions()->orderBy('type', 'asc')->orderBy('index', 'asc')->get();
        $d_action = ['tag' => [], 'scenario' => []];
        if (!isset($actions)) {
            return $d_action;
        }
        
        $curType = null;
        $curIdx = -1;

        $indexObj = null;
        foreach ($actions as $action) {
            switch ($action->type) {
                case 0:
                    $type = 'tag';
                    break;
                case 1:
                    $type = 'scenario';
                    break;
                default:
                    $type = 'undefined';
                    break;
            }

            if ($curType != $type) {
                $curType = $type;
                $curIdx = -1;
            }

            $idx = $action->index;
            if ($curIdx != $idx) {
                $curIdx = $idx;
                $indexObj = new stdClass();
                $indexObj->value = [];
                $indexObj->option = $action->option;

                // TODO: 暫定　timingとnumberは削除予定
                if ($type == 'scenario') {
                    $indexObj->delivery = ['timing' => '', 'number' => '' ];
                }

                $d_action[$curType][] = $indexObj;
            }
            $indexObj->value[] = $type == 'tag' ? $action->tagTitle : $action->scenario_Name;
        }

        return $d_action;
    }



    /**
     * タグに対するアクションレコードを作成
     * @param array $tags
     * @param string $option
     * @param int $index
     */
    private function storeTagAction($tags, $option, $index)
    {
        $tagManagementIds = Auth::user()->account->tagManagements()->whereIn('title', $tags)->pluck('id');

        foreach ($tagManagementIds as $tagManagementId) {
            $this->getActions()->create([
                'type' => 0,
                'tag_management_id' => $tagManagementId,
                'index' => $index,
                'option' => $option
            ]);
        }
    }

    /**
     * シナリオに対するアクションレコードを作成
     * @param array $scenarios
     * @param string $option
     * @param int $index
     */
    private function storeScenarioAction($scenarios, $option, $index)
    {
        $scenarioTargetIds = Auth::user()->account->scenarios()->whereIn('name', $scenarios)->pluck('id');

        foreach ($scenarioTargetIds as $scenarioTargetId) {
            $this->getActions()->create([
                'type' => 1,
                'scenario_id' => $scenarioTargetId,
                'index' => $index,
                'option' => $option
            ]);
        }
    }

    public function createStoreActions($actions)
    {
        if (isset($actions['tags'])) {
            $tags = $actions['tags'];
            $tagsServes = $tags['serves'];
            $i = 0;
            foreach ($tagsServes as $serve) {
                if (count($serve['value']) > 0) {
                    $this->storeTagAction($serve['value'], $serve['option'], $i);
                    $i++;
                }
            }
        }

        if (isset($actions['scenarios'])) {
            $scenarios = $actions['scenarios'];
            $scenariosServes = $scenarios['serves'];
            $i = 0;
            foreach ($scenariosServes as $serve) {
                if (count($serve['value']) > 0) {
                    $this->storeScenarioAction($serve['value'], $serve['option'], $i);
                    $i++;
                }
            }
        }
    }

    public function executeActions($pfUsers)
    {
        $pfUserService = new PfUserService();
        $actions = $this->getActions()->orderBy('type', 'asc')->orderBy('index', 'asc')->get();
        foreach ($actions as $action) {
            switch ($action->type) {
                case 0:
                    $targetId = $action->tag_management_id;
                    if ($action->option == 'first') {
                        $pfUserService->addTag($pfUsers, $targetId);
                    } elseif ($action->option == 'second') {
                        $pfUserService->removeTag($pfUsers, $targetId);
                    }
                    break;
                case 1:
                    $targetId = $action->scenario_id;
                    if ($action->option == 'first') {
                        $pfUserService->addScenario($pfUsers, $targetId);
                    } elseif ($action->option == 'second') {
                        $pfUserService->removeScenario($pfUsers, $targetId);
                    }
                    break;
                default:
                    $type = 'undefined';
                    break;
            }
        }
    }
}
