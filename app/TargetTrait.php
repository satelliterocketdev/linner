<?php

namespace App;

use Auth;
use App\Services\PfUserService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait TargetTrait
{
    /**
     * 〜targetsを指すhasManyオブジェクトを返す。
     * @return HasMany
     */
    abstract protected function getTargets();

    /**
     * タグに対するターゲットレコードを作成
     * @param array $tags
     * @param string $option
     * @param int $index
     * @param int $isExclude
     * @param bool $isMany
     * @param int $account_id
     */
    private function storeTagTarget($tags, $option, $index, $isExclude = 0, $isMany = false, $account_id = null)
    {
        if ($isMany) {
            $tagsFolders = TagsFolders::where('account_id', $account_id)->get();
        } else {
            $tagsFolders = Auth::user()->account->tagsFolders;
        }

        $tagManagementIds = collect($tagsFolders)->flatMap->tagManagements->whereIn('title', $tags)->pluck('id');

        foreach ($tagManagementIds as $tagManagementId) {
            $this->getTargets()->create([
                'tag_management_id' => $tagManagementId,
                'is_exclude' => $isExclude,
                'index' => $index,
                'option' => $option
            ]);
        }
    }

    /**
     * @param $data
     * @param $option
     * @param $index
     * @param int $isExclude
     * @param bool $isMany
     * @param null $account_id
     */
    private function storeScenarioTarget($data, $option, $index, $isExclude = 0, $isMany = false, $account_id = null)
    {
        $scenarios = $data;

        if (!$isMany) {
            $account_id = Auth::user()->account->id;
        }

        $scenarioTargetIds = Scenario::where('account_id', $account_id)->whereIn('name', $scenarios)->pluck('id');

        foreach ($scenarioTargetIds as $scenarioTargetId) {
            $this->getTargets()->create([
                'scenario_id' => $scenarioTargetId,
                'is_exclude' => $isExclude,
                'index' => $index,
                'option' => $option
            ]);
        }
    }

    /**
     * @param array $data
     * @param int $index
     * @param int $isExclude
     */
    private function storeDateTarget($data, $index, $isExclude = 0)
    {
        $date = $data;
        if (!empty($date['from']) && !empty($date['to'])) {
            $this->getTargets()->create([
                'start_at' => $date['from'],
                'end_at' => $date['to'],
                'is_exclude' => $isExclude,
                'index' => $index
            ]);
        }
    }


    public function createStoreTargets($targets)
    {
        if (isset($targets['tags'])) {
            $tags = $targets['tags'];
            $tagsServes = $tags['serves'];
            $tagsExcludes = $tags['excludes'];
            $i = 0;
            foreach ($tagsServes as $serve) {
                if (count($serve['value']) > 0) {
                    $this->storeTagTarget($serve['value'], $serve['option'], $i);
                    $i++;
                }
            }
            $i = 0;
            foreach ($tagsExcludes as $serve) {
                if (count($serve['value']) > 0) {
                    $this->storeTagTarget($serve['value'], $serve['option'], $i, 1);
                    $i++;
                }
            }
        }

        if (isset($targets['scenarios'])) {
            $scenarios = $targets['scenarios'];
            $scenariosServes = $scenarios['serves'];
            $scenariosExcludes = $scenarios['excludes'];
            $i = 0;
            foreach ($scenariosServes as $serve) {
                if (count($serve['value']) > 0) {
                    $this->storeScenarioTarget($serve['value'], $serve['option'], $i);
                    $i++;
                }
            }
            $i = 0;
            foreach ($scenariosExcludes as $serve) {
                if (count($serve['value']) > 0) {
                    $this->storeScenarioTarget($serve['value'], $serve['option'], $i, 1);
                    $i++;
                }
            }
        }

        if (isset($targets['dates'])) {
            $dates = $targets['dates'];
            $dateServes = $dates['serves'];
            $dateExcludes = $dates['excludes'];
            $i = 0;
            foreach ($dateServes as $serve) {
                if (count($serve['value']) > 0) {
                    $this->storeDateTarget($serve['value'], $i);
                    $i++;
                }
            }
            foreach ($dateExcludes as $serve) {
                if (count($serve['value']) > 0) {
                    $this->storeDateTarget($serve['value'], $i, 1);
                    $i++;
                }
            }
        }
    }

    public function getPfUsers($tags, $scenarios, $registeredDates)
    {
        $pfUserIds = $this->getPfUserIds(
            $tags,
            $scenarios,
            $registeredDates,
            'serves'
        );

        $excludePfUserIds = $this->getPfUserIds(
            $tags,
            $scenarios,
            $registeredDates,
            'excludes'
        );

        $pfUsersToSend = array_diff($pfUserIds, $excludePfUserIds);

        return PfUser::findMany($pfUsersToSend);
    }

    private function getPfUserIds($tags, $scenarios, $registeredDates, $isServes = null)
    {
        $user = Auth::user();

        $tagManagementIds = [];
        if (!empty($tags[0]['value'])) {
            for ($i = 0; $i < count($tags); $i++) {
                $tagManagementIds[$i][] = collect($user->account->tagsFolders)->flatMap->tagManagements
                    ->whereIn('title', $tags[$i]['value'])->pluck('id');
                $tagManagementIds[$i]['option'] = $tags[$i]['option'];
            }
        }

        $scenarioIds = [];
        if (!empty($scenarios[0]['value'])) {
            for ($i = 0; $i < count($scenarios); $i++) {
                $scenarioIds[$i][] = $user->account->scenarios->whereIn('name', $scenarios[$i]['value'])->pluck('id');
                $scenarioIds[$i]['option'] = $scenarios[$i]['option'];
            }
        }

        $dates = [];
        if (!empty($registeredDates[0]['value']['from']) && !empty($registeredDates[0]['value']['to'])) {
            for ($i = 0; $i < count($registeredDates); $i++) {
                $dates[$i] = $registeredDates[$i]['value'];
            }
        }

        $pfUserIds             = [];
        $pfUserIds['tag']      = [];
        $pfUserIds['scenario'] = [];
        $pfUserIds['date']     = [];

        foreach ($tagManagementIds as $tagManagementId) {
            $tagManagements = TagManagement::findMany($tagManagementId[0]);
            $pfUserTagManagements = collect($tagManagements)->flatMap->pfUserTagManagements;
            $pfUsers = collect($pfUserTagManagements)->map->pfUser;
            /** @var \Illuminate\Support\Collection $accountFollowers */
            $accountFollowers = collect($pfUsers)->map->accountFollower;
            $currentAccountPfUserIds = $accountFollowers
                ->where('account_id', $user->account_id)->pluck('pf_user_id');
            switch ($tagManagementId['option']) {
                case 'first':
                    //タグを一つ以上持っている人を含める
                    $selectedPfUserIds = collect($pfUserTagManagements)->map->pf_user_id;
                    $pfUserIds['tag'][] = $currentAccountPfUserIds->intersect($selectedPfUserIds);
                    break;
                case 'second':
                    //タグを全て持っている人を含める
                    $tagsForUser = [];
                    foreach ($pfUserTagManagements as $pfUserTagManagement) {
                        $tagsForUser[$pfUserTagManagement->pf_user_id][] = $pfUserTagManagement
                            ->tag_managements_id;
                    }
                    $allPfUserIds = [];
                    foreach ($tagsForUser as $key => $pfUserTags) {
                        if ($tagManagementId[0]->toArray() == $pfUserTags) {
                            $allPfUserIds[] = $key;
                        }
                    }
                    $pfUserIds['tag'][] = collect(array_intersect($allPfUserIds, $currentAccountPfUserIds->toArray()));
                    break;
                default:
                    break;
            }
        }

        if (empty($tagManagementIds) && $isServes == 'serves') {
            // 配信対象設定（タグ）なし => 全ユーザーを対象
            $pfUserIds['tag'][0] = AccountFollower::where('account_id', $user->account->id)->pluck('pf_user_id');
        } elseif (empty($tagManagementIds) && $isServes == 'excludes') {
            // 配信除外設定（タグ）なし => idはセットしない
            $pfUserIds['tag'][0] = collect([]);
        }

        foreach ($scenarioIds as $scenarioId) {
            $scenarios = Scenario::findMany($scenarioId[0]);
            $currentAccountPfUserIds = $user->account->accountFollowers->pluck('pf_user_id');
            $deliveries = collect($scenarios)->flatMap->scenarioMessages->flatMap->deliveries;
            switch ($scenarioId['option']) {
                case 'first':
                    //シナリオを一つ以上持っている人を含める
                    $selectedPfUserIds = $deliveries->pluck('pf_user_id');
                    $pfUserIds['scenario'][] = $currentAccountPfUserIds->intersect($selectedPfUserIds);
                    break;
                case 'second':
                    //シナリオを全て持っている人を含める
                    $scenariosForUser = [];
                    $scenarioMessages = collect($user->account->scenarios)->flatMap->scenarioMessages;
                    foreach ($deliveries as $delivery) {
                        $deliveryMessageId = $delivery->scenario_message_id;
                        $scenarioMessage = $scenarioMessages->first(function ($scenarioMessage) use ($deliveryMessageId) {
                            return $scenarioMessage->id == $deliveryMessageId;
                        });
                        $scenariosForUser[$delivery->pf_user_id][] = $scenarioMessage->scenario_id;
                    }

                    $scenarioForUserUniquified = [];
                    foreach ($scenariosForUser as $key => $scenarioForUser) {
                        $scenarioForUserUniquified[$key] = array_values(array_unique($scenarioForUser, SORT_REGULAR));
                    }

                    $allPfUserIds = [];
                    foreach ($scenarioForUserUniquified as $key => $pfUserScenarios) {
                        if ($scenarioId[0]->toArray() == $pfUserScenarios) {
                            $allPfUserIds[] = $key;
                        }
                    }

                    $pfUserIds['scenario'][] = collect(array_intersect($allPfUserIds, $currentAccountPfUserIds->toArray()));
                    break;
                default:
                    break;
            }
        }

        if (empty($scenarioIds) && $isServes == 'serves') {
            // 配信対象設定（シナリオ）なし => 全ユーザーを対象
            $pfUserIds['scenario'][0] = AccountFollower::where('account_id', $user->account->id)->pluck('pf_user_id');
        } elseif (empty($scenarioIds) && $isServes == 'excludes') {
            // 配信除外設定（シナリオ）なし => idはセットしない
            $pfUserIds['scenario'][0] = collect([]);
        }

        foreach ($dates as $date) {
            $pfUserIds['date'][] = AccountFollower::where('account_id', $user->account_id)
                ->whereBetween('timedate_followed', [$date['from'], $date['to']])
                ->pluck('pf_user_id');
        }

        if (empty($dates) && $isServes == 'serves') {
            // 信対象設定（登録日）なし => 全ユーザーを対象
            $pfUserIds['date'][0] = AccountFollower::where('account_id', $user->account->id)->pluck('pf_user_id');
        } elseif (empty($dates) && $isServes == 'excludes') {
            // 配信除外設定（登録日）なし => idはセットしない
            $pfUserIds['date'][0] = collect([]);
        }

        $pfUserIdsMerge = [];
        if ($isServes == 'serves') { //配信対象設定
            $pfUserIdsMerge = $pfUserIds['tag'][0]->intersect($pfUserIds['scenario'][0]);
            $pfUserIdsMerge = $pfUserIdsMerge->intersect($pfUserIds['date'][0])->toArray();
        } elseif ($isServes == 'excludes') { //除外対象設定
            $pfUserIdsMerge = $pfUserIds['tag'][0]->concat($pfUserIds['scenario'][0])->concat($pfUserIds['date'][0])->toArray();
        }

        $result = [];
        foreach ($pfUserIdsMerge as $pfUserId) {
            $result[] = $pfUserId;
        }

        return $result;
    }


//    /**
//     * @param array $tags
//     * @param array $scenarios
//     * @param array $registeredDates
//     * @param string $isServes
//     * @return array
//     */
//    public function getPfUserIds($tags, $scenarios, $registeredDates, $isServes = null)
//    {
//        $user = Auth::user();
//
//        $tagManagementIds = [];
//        if (!empty($tags[0]['value'])) {
//            for ($i = 0; $i < count($tags); $i++) {
//                $tagManagementIds[$i][] = collect($user->account->tagsFolders)->flatMap->tagManagements
//                    ->whereIn('title', $tags[$i]['value'])
//                    ->pluck('id');
//                $tagManagementIds[$i]['option'] = $tags[$i]['option'];
//            }
//        }
//
//        $scenarioIds = [];
//        if (!empty($scenarios[0]['value'])) {
//            for ($i = 0; $i < count($scenarios); $i++) {
//                $scenarioIds[$i][] = $user->account
//                    ->scenarios
//                    ->whereIn('name', $scenarios[$i]['value'])
//                    ->pluck('id');
//                $scenarioIds[$i]['option'] = $scenarios[$i]['option'];
//            }
//        }
//
//        $dates = [];
//        if (!empty($registeredDates[0]['value'])) {
//            for ($i = 0; $i < count($registeredDates); $i++) {
//                $dates[$i] = $registeredDates[$i]['value'];
//            }
//        }
//
//        $pfUserIds = [];
//        $pfUserIds['tag']      = [];
//        $pfUserIds['scenario'] = [];
//        $pfUserIds['date']     = [];
//
//        foreach ($tagManagementIds as $tagManagementId) {
//            $tagManagements = TagManagement::findMany($tagManagementId[0]);
//            $pfUserTagManagements = collect($tagManagements)->flatMap->pfUserTagManagements;
//            /** @var Collection|PfUser[] $pfUsers */
//            $pfUsers = collect($pfUserTagManagements)->map->pfUser;
//            /** @var Collection|AccountFollower[] $accountFollowers */
//            $accountFollowers = collect($pfUsers)->map->accountFollower;
//            $currentAccountPfUserIds = $accountFollowers
//                ->where('account_id', $user->account_id)->pluck('pf_user_id');
//            switch ($tagManagementId['option']) {
//                case 'first':
//                    //タグを一つ以上持っている人を含める
//                    $selectedPfUserIds = collect($pfUserTagManagements)->map->pf_user_id;
//                    $pfUserIds['tag'][] = $currentAccountPfUserIds->intersect($selectedPfUserIds);
//                    break;
//                case 'second':
//                    //タグを全て持っている人を含める
//                    $tagsForUser = [];
//                    foreach ($pfUserTagManagements as $pfUserTagManagement) {
//                        $tagsForUser[$pfUserTagManagement->pf_user_id][] = $pfUserTagManagement
//                            ->tag_managements_id;
//                    }
//                    $allPfUserIds = [];
//                    foreach ($tagsForUser as $key => $pfUserTags) {
//                        if ($tagManagementId[0]->toArray() == $pfUserTags) {
//                            $allPfUserIds[] = $key;
//                        }
//                    }
//                    $pfUserIds['tag'][] = array_intersect($allPfUserIds, $currentAccountPfUserIds->toArray());
//                    break;
//                default:
//                    break;
//            }
//        }
//
//        if (empty($tagManagementIds) && $isServes == 'serves') {
//            // 配信対象設定（タグ）なし => 全ユーザーを対象
//            $pfUserIds['tag'][0] = AccountFollower::where('account_id', $user->account->id)->pluck('pf_user_id');
//        } elseif (empty($tagManagementIds) && $isServes == 'excludes') {
//            // 配信除外設定（タグ）なし => idはセットしない
//            $pfUserIds['tag'][0] = collect([]);
//        }
//
//        foreach ($scenarioIds as $scenarioId) {
//            $scenarios = Scenario::findMany($scenarioId[0]);
//            $currentAccountPfUserIds = $user->account->accountFollowers->pluck('pf_user_id');
//            /** @var Collection|ScenarioDelivery[] $deliveries */
//            $deliveries = collect($scenarios)->flatMap->scenarioMessages->flatMap->deliveries;
//            switch ($scenarioId['option']) {
//                case 'first':
//                    //シナリオを一つ以上持っている人を含める
//                    $selectedPfUserIds = $deliveries->pluck('pf_user_id');
//                    $pfUserIds['scenario'][] = $currentAccountPfUserIds->intersect($selectedPfUserIds);
//                    break;
//                case 'second':
//                    //シナリオを全て持っている人を含める
//                    $scenariosForUser = [];
//                    /** @var Collection|ScenarioMessage[] $scenarioMessages */
//                    $scenarioMessages = collect($user->account->scenarios)->flatMap->scenarioMessages;
//                    foreach ($deliveries as $delivery) {
//                        $deliveryMessageId = $delivery->scenario_message_id;
//                        $scenarioMessage = $scenarioMessages->first(
//                            function ($scenarioMessage) use ($deliveryMessageId) {
//                                return $scenarioMessage->id == $deliveryMessageId;
//                            }
//                        );
//                        $scenariosForUser[$delivery->pf_user_id][] = $scenarioMessage->scenario_id;
//                    }
//
//                    $scenarioForUserUniquified = [];
//                    foreach ($scenariosForUser as $key => $scenarioForUser) {
//                        $scenarioForUserUniquified[$key] = array_values(array_unique($scenarioForUser, SORT_REGULAR));
//                    }
//
//                    $allPfUserIds = [];
//                    foreach ($scenarioForUserUniquified as $key => $pfUserScenarios) {
//                        if ($scenarioId[0]->toArray() == $pfUserScenarios) {
//                            $allPfUserIds[] = $key;
//                        }
//                    }
//
//                    $pfUserIds['scenario'][] = array_intersect($allPfUserIds, $currentAccountPfUserIds->toArray());
//                    break;
//                default:
//                    break;
//            }
//        }
//
//        if (empty($scenarioIds) && $isServes == 'serves') {
//            // 配信対象設定（シナリオ）なし => 全ユーザーを対象
//            $pfUserIds['scenario'][0] = AccountFollower::where('account_id', $user->account->id)->pluck('pf_user_id');
//        } elseif (empty($scenarioIds) && $isServes == 'excludes') {
//            // 配信除外設定（シナリオ）なし => idはセットしない
//            $pfUserIds['scenario'][0] = collect([]);
//        }
//
//        foreach ($dates as $date) {
//            $pfUserIds['date'][] = AccountFollower::where('account_id', $user->account_id)
//                ->whereBetween('timedate_followed', [$date['from'], $date['to']])
//                ->pluck('pf_user_id');
//        }
//
//        if (empty($dates) && $isServes == 'serves') {
//            // 信対象設定（登録日）なし => 全ユーザーを対象
//            $pfUserIds['date'][0] = AccountFollower::where('account_id', $user->account->id)->pluck('pf_user_id');
//        } elseif (empty($dates) && $isServes == 'excludes') {
//            // 配信除外設定（登録日）なし => idはセットしない
//            $pfUserIds['date'][0] = collect([]);
//        }
//
//        $pfUserIdsMerge = [];
//        if ($isServes == 'serves') { //配信対象設定
//            $pfUserIdsMerge = $pfUserIds['tag'][0]->intersect($pfUserIds['scenario'][0]);
//            $pfUserIdsMerge = $pfUserIdsMerge->intersect($pfUserIds['date'][0])->toArray();
//        } elseif ($isServes == 'excludes') { //除外対象設定
//            $pfUserIdsMerge = $pfUserIds['tag'][0]->concat($pfUserIds['scenario'][0])
//                ->concat($pfUserIds['date'][0])
//                ->toArray();
//        }
//
//        $result = [];
//        foreach ($pfUserIdsMerge as $pfUserId) {
//            $result[] = $pfUserId;
//        }
//
//        return $result;
//    }
}
