<?php

namespace App\LineEvents\EventModels;

use App\Account;

class Follow
{
    /** @var Account */
    private $account;
    /** @var string */
    private $type;
    /** @var string */
    private $replyToken;
    /** @var string */
    private $sourceType;
    /** @var string */
    private $sourceUserId;
    /** @var string */
    private $destinationUserId;
    /** @var int */
    private $timestamp;

    /**
     * Follow constructor.
     * @param Account $account
     * @param string $destination
     * @param mixed $followData
     */
    public function __construct($account, $destination, $followData)
    {
        $this->account = $account;
        $this->type = $followData->type;
        $this->replyToken = $followData->replyToken;
        $this->sourceType = $followData->source->type;
        $this->sourceUserId = $followData->source->userId;
        $this->destinationUserId = $destination;
        $this->timestamp = $followData->timestamp;
    }

    /**
     * @return Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getReplyToken()
    {
        return $this->replyToken;
    }

    /**
     * @return mixed
     */
    public function getSourceType()
    {
        return $this->sourceType;
    }

    /**
     * @return mixed
     */
    public function getSourceUserId()
    {
        return $this->sourceUserId;
    }

    /**
     * @return mixed
     */
    public function getDestinationUserId()
    {
        return $this->destinationUserId;
    }

    /**
     * @return false|string
     */
    public function getTimestampToDate()
    {
        if (!empty($this->timestamp) && is_numeric($this->timestamp)) {
            return date('Y-m-d H:i:s', $this->timestamp / 1000);
        }
        return date('Y-m-d H:i:s');
    }
}
