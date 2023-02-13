<?php
  namespace App\LineEvents;

use App\AccountFollower;
use App\LineEvents\EventModels\Unfollow;
use App\PfUser;
use Exception;

class UnFollowEvent
{

    public function __construct()
    {
        //
    }

    /**
     * @param Unfollow $unFollow
     * @throws Exception
     */
    public function userUnFollow($unFollow)
    {
        $this->deleteFollowUser($unFollow);
    }

    /**
     * @param Unfollow $unFollow
     * @throws Exception
     */
    protected function deleteFollowUser($unFollow)
    {
        $sourceUserId = $unFollow->getSourceUserId();

        /** @var AccountFollower $accountFollower */
        $accountFollower = $unFollow->getAccount()->accountFollowers()
            ->where('source_user_id', $sourceUserId)
            ->first();

        if (!is_null($accountFollower)) {
            $accountFollower->event_type = $unFollow->getType();
            $accountFollower->save();
            $accountFollower->delete();

            if (AccountFollower::where('source_user_id', $sourceUserId)->count() == 0) {
                $accountFollower->pfUsers()->delete();
            }
        } else {
            $accountFollower = AccountFollower::onlyTrashed()
                ->where('account_id', $unFollow->getAccount()->id)
                ->where('source_user_id', $sourceUserId)
                ->first();

            if (is_null($accountFollower)) {
                return;
            }

            $accountFollower->pfUsers()->delete();
        }
    }
}
