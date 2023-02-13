<?php

namespace App\Exceptions;

use Exception;
use LINE\LINEBot\Response;

class LineException extends Exception
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * LineApiException constructor.
     *
     * @param Response  $response
     */
    public function __construct($response)
    {
        $this->response = $response;
        $jsonBody = $response->getJSONDecodedBody();
        $this->message = isset($jsonBody["message"]) ? $jsonBody["message"] : null;
        if (isset($jsonBody['details'])) {
            $this->message .= "\n" . var_export($jsonBody['details'], true);
        }

        $this->code = $response->getHTTPStatus();
    }

    /**
     * Returns HTTP status code of response.
     *
     * @return int HTTP status code of response.
     */
    public function getStatusCode()
    {
        return $this->response->getHTTPStatus();
    }

    /**
     * Returns response body as array (it means, returns JSON decoded body).
     *
     * @return array Request body that is JSON decoded.
     */
    public function getLineMessage()
    {
        return $this->response->getJSONDecodedBody();
    }
}
