<?php
/**
 * This file is a part of "furqansiddiqui/telegram" package.
 * https://github.com/furqansiddiqui/telegram
 *
 * Copyright (c) 2017. Furqan A. Siddiqui <hello@furqansiddiqui.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code or visit following link:
 * https://github.com/furqansiddiqui/telegram/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Telegram;

use Telegram\Exception\APIException;
use Telegram\Exception\HandlerException;
use Telegram\Handler\AbstractHandler;
use Telegram\Handler\BasicHandler;
use Telegram\Objects\Message;
use Telegram\Objects\Update;
use Telegram\Objects\User;

/**
 * Class Telegram
 * @package Telegram
 */
class Telegram
{
    const VERSION   =   "0.3.2";

    /** @var string */
    private $apiKey;
    /** @var bool */
    private $apiCheckSSL;
    /** @var WebHooks */
    private $webHooks;
    /** @var AbstractHandler */
    private $handler;

    /**
     * Telegram constructor.
     * @param string $apiKey
     */
    public function __construct(string $apiKey)
    {
        $this->apiKey   =   $apiKey;
        $this->apiCheckSSL  =   true;

        $this->webHooks =   new WebHooks($this);
        $this->setHandler(new BasicHandler($this));
    }

    /**
     * @param AbstractHandler $handler
     * @return Telegram
     */
    public function setHandler(AbstractHandler $handler) : self
    {
        $this->handler  =   $handler;
        return $this;
    }

    /**
     * @param array $data
     * @throws HandlerException
     */
    public function handle(array $data)
    {
        $update =   Update::Parse($data);
        if(!$update->message instanceof Message) {
            throw new HandlerException(sprintf('Handling of update type "%s" not supported', $update->type ?? "NULL"));
        }

        try {
            $this->handler->_crunchMessage($update->message);
        } catch (HandlerException $e) {
            $this->handler->sendReply($e->getMessage());
            throw $e;
        }

        $this->handler->_execute();
    }

    /**
     * Alias method for "handle"
     * @param array $data
     */
    public function listen(array $data)
    {
        $this->handle($data);
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

    /**
     * @param int $chatId
     * @param string $message
     * @param bool $html
     * @return Message
     * @throws APIException
     */
    public function sendMessage(int $chatId, string $message, bool $html = false) : Message
    {
        $payload    =   [
            "chat_id"   =>  $chatId,
            "text"  =>  $message
        ];
        if($html    === true) {
            $payload["parse_mode"]  =   "HTML";
        }

        $send   =   $this->sendAPI("sendMessage", $payload);
        if(!is_array($send)) {
            throw APIException::BadResultType("sendMessage", gettype($send), "Array");
        }

        return Message::Parse($send);
    }
}