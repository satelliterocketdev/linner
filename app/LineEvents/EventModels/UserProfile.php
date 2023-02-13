<?php

namespace App\LineEvents\EventModels;

class UserProfile
{
    /** @var string */
    private $displayName;
    /** @var string */
    private $userId;
    /** @var string */
    private $pictureUrl;
    /** @var string */
    private $statusMessage;

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getPictureUrl(): string
    {
        return $this->pictureUrl;
    }

    /**
     * @return string
     */
    public function getStatusMessage(): string
    {
        return $this->statusMessage;
    }

    /**
     * UserProfile constructor.
     * @param string $json
     */
    public function __construct($json)
    {
        $data = json_decode($json, true);

        $this->displayName = isset($data['displayName']) ? $data['displayName'] : "";
        $this->pictureUrl = isset($data['pictureUrl']) ? $data['pictureUrl'] : "/img/user-admin.png";
        $this->userId = isset($data['userId']) ? $data['userId'] : "";
        $this->statusMessage = isset($data['statusMessage']) ? $data['statusMessage'] : "";
    }
}