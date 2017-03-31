<?php
declare(strict_types=1);

namespace Telegram;

use Telegram\Exception\APIException;

/**
 * Class WebHooks
 * @package Telegram
 */
class WebHooks
{
    /** @var Telegram */
    private $telegram;

    /**
     * WebHooks constructor.
     * @param Telegram $telegram
     */
    public function __construct(Telegram $telegram)
    {
        $this->telegram =   $telegram;
    }

    /**
     * @param string $url
     * @param int $maxConnections
     * @return bool
     * @throws APIException
     */
    public function setWebHook(string $url, int $maxConnections = 40) : bool
    {
        $payload    =   [
            "url"   =>  $url,
            "max_connections"   =>  $maxConnections
        ];

        $setHook    =   $this->telegram->sendAPI("setWebHook", $payload);
        if(!is_bool($setHook)) {
            throw APIException::BadResultType("setWebHook", gettype($setHook), "Boolean");
        }

        return $setHook;
    }
}