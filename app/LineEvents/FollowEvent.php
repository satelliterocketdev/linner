<?php

namespace App\LineEvents;

use App\AccountFollower;
use App\LineEvents\EventModels\Follow;
use App\PfUser;

class FollowEvent extends LineEvent
{
    public function __construct()
    {
        parent::__construct(null, null);
    }

    /**
     * @param Follow $follow
     */
    public function userFollow(Follow $follow)
    {
        $this->setSecret($follow->getAccount()->channel_secret);
        $this->setToken($follow->getAccount()->channel_access_token);
        $this->createHttpClient();
        $this->createLineBot();

        if (AccountFollower::withTrashed()->where('source_user_id', $follow->getSourceUserId())->first()) {
            $this->updateFollowUser($follow);
        } else {
            $this->addFollowUser($follow);
        }
        return;
    }

    /**
     * @param Follow $follow
     */
    protected function addFollowUser($follow)
    {
        $userProfile = $this->lineUtils->userFollowProfile(
            $follow->getSourceUserId(),
            $follow->getAccount()->channel_access_token
        );

        $pfUser = new PfUser;
        $pfUser->display_name = $userProfile->getDisplayName();
        $pfUser->picture = $userProfile->getPictureUrl();
        $pfUser->status_message = $userProfile->getStatusMessage();

        $pfUser->save();

        $follower = new AccountFollower;
        $follower->account_id = $follow->getAccount()->id;
        $follower->channel_id = $follow->getAccount()->channel_id;
        $follower->pf_user_id = $pfUser->id;
        $follower->display_name = $userProfile->getDisplayName();
        $follower->event_type = $follow->getType();
        $follower->reply_token = $follow->getReplyToken();
        $follower->source_type = $follow->getSourceType();
        $follower->source_user_id = $follow->getSourceUserId();
        $follower->destination_user_id = $follow->getDestinationUserId();
        $follower->status = null; //TODO: 代入値不明
        $follower->timedate_followed = $follow->getTimestampToDate();

        $follower->save();

        return;
    }

    /**
     * @param Follow $follow
     */
    private function updateFollowUser($follow)
    {
        $userProfile = $this->lineUtils->userFollowProfile(
            $follow->getSourceUserId(),
            $follow->getAccount()->channel_access_token
        );

        $pfUserId = AccountFollower::withTrashed()
            ->where('source_user_id', $follow->getSourceUserId())
            ->value('pf_user_id');
        $pfUser = PfUser::withTrashed()->where('id', $pfUserId)->first();
        $pfUser->display_name = $userProfile->getDisplayName();
        $pfUser->picture = $userProfile->getPictureUrl();
        $pfUser->status_message = $userProfile->getStatusMessage();
        $pfUser->restore();

        $follower = AccountFollower::withTrashed()
            ->where('account_id', $follow->getAccount()->id)
            ->where('source_user_id', $follow->getSourceUserId())
            ->first();
        $follower->display_name = $pfUser->display_name;
        $follower->event_type = $follow->getType();
        $follower->reply_token = $follow->getReplyToken();
        $follower->source_type = $follow->getSourceType();
        $follower->source_user_id = $follow->getSourceUserId();
        $follower->destination_user_id = $follow->getDestinationUserId();
        $follower->timedate_followed = $follow->getTimestampToDate();
        $follower->restore();
    }
}
