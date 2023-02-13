<?php

namespace App\LineEvents\EventModels;

use App\Account;

class Unfollow
{
    /** @var Account */
    private $account;
    /** @var string */
    private $type;
    /** @var int */
    private $timestamp;
    /** @var string */
    private $sourceUserId;
    /** @var string */
    private $destinationUserId;

    /**
     * Unfollow constructor.
     * @param Account $account
     * @param string $destination
     * @param mixed $data
     */
    public function __construct(Account $account, $destination, $data)
    {
        $this->account = $account;
        $this->type = $data->type;
        $this->timestamp = $data->timestamp;
        $this->sourceUserId = $data->source->userId;
        $this->destinationUserId = $destination;
    }


    /**
     * @return Account
     */
    public function getAccount(): Account
    {
        return $this->account;
    }

    /**
     * @return string
     */
    public function getSourceUserId(): string
    {
        return $this->sourceUserId;
    }

    /**
     * @return string
     */
    public function getDestinationUserId(): string
    {
        return $this->destinationUserId;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
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
