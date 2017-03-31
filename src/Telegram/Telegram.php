<?php
declare(strict_types=1);

namespace Telegram;

use Telegram\Exception\APIException;
use Telegram\Exception\UpdateException;
use Telegram\Objects\User;

/**
 * Class Telegram
 * @package Telegram
 */
class Telegram
{
    /** @var string */
    private $apiKey;
    /** @var bool */
    private $apiCheckSSL;

    /** @var WebHooks */
    private $webHooks;

    /**
     * Telegram constructor.
     * @param string $apiKey
     */
    public function __construct(string $apiKey)
    {
        $this->apiKey   =   $apiKey;
        $this->apiCheckSSL  =   true;

        $this->webHooks =   new WebHooks($this);
    }

    /**
     * @return WebHooks
     */
    public function webHooks() : WebHooks
    {
        return $this->webHooks;
    }

    /**
     * @param string $endPoint
     * @return string
     */
    private function apiEndPoint(string $endPoint) : string
    {
        return sprintf(
            'https://api.telegram.org/bot%1$s/%2$s',
            $this->apiKey,
            $endPoint
        );
    }

    /**
     * @param string $endPoint
     * @param array|null $data
     * @return mixed|null
     * @throws APIException
     */
    public function sendAPI(string $endPoint, array $data = null)
    {
        try {
            // Prepare request
            $request    =   \HttpClient::Post($this->apiEndPoint($endPoint))
                ->checkSSL($this->apiCheckSSL)
                ->accept("json");

            // Attach payload?
            if(is_array($data)) {
                $request->payload($data);
            }

            // Send request
            $response   =   $request->send();
        } catch (\HttpClientException $e) {
            throw APIException::ConnectionError($e->getCode(), $e->getMessage(), $endPoint);
        }

        // HTTP response code
        if($response->responseCode()    !== 200) {
            throw APIException::ResponseCode($endPoint, $response->responseCode());
        }

        // Response type
        $response   =   $response->getBody();
        if(!is_array($response)) {
            throw APIException::BadResponseType($endPoint, gettype($response));
        }

        // Validate Response
        $success    =   $response["ok"] ?? false;

        if($success !== true) {
            $errorMessage   =   $response["description"] ?? "Unknown error occurred";
            $errorCode  =   $response["error_code"] ?? 0;
            throw APIException::Failed($endPoint, $errorMessage, intval($errorCode));
        }

        return $response["result"] ?? null;
    }

    /**
     * @return User
     * @throws APIException
     * @throws TelegramException
     */
    public function getMe() : User
    {
        $me =   $this->sendAPI("getMe");
        if(!is_array($me)) {
            throw APIException::BadResultType("getMe", gettype($me), "Array");
        }

        return User::Parse($me);
    }

}