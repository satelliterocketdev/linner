<?php

namespace App\LineEvents;

use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;

class LineEvent
{
    /** @var string */
    private $secret;

    /** @var string */
    private $token;

    /** @var LineUtils */
    protected $lineUtils;

    /** @var CurlHTTPClient */
    protected $httpClient = null;

    /** @var LINEBot */
    protected $lineBot = null;

    /**
     * @param string $secret
     */
    public function setSecret(string $secret): void
    {
        $this->secret = $secret;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * LineEvent constructor.
     * @param string $secret
     * @param string $token
     */
    public function __construct($secret, $token)
    {
        $this->lineUtils = new LineUtils();

        $this->secret = $secret;
        $this->token = $token;

        $this->createHttpClient();
        $this->createLineBot();
    }

    /**
     * @return bool
     */
    protected function createHttpClient()
    {
        if (empty($this->token)) {
            return false;
        }
        $this->httpClient = new CurlHTTPClient($this->token);
        return true;
    }

    /**
     * @return bool
     */
    protected function createLineBot()
    {
        if (empty($this->secret)) {
            return false;
        }

        $this->lineBot = new LINEBot($this->httpClient, ['channelSecret' => $this->secret]);
        return true;
    }
}
