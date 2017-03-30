<?php
declare(strict_types=1);

namespace Telegram;

/**
 * Class Telegram
 * @package Telegram
 */
class Telegram
{
    /** @var string */
    private $apiKey;

    /**
     * Telegram constructor.
     * @param string $apiKey
     */
    public function __construct(string $apiKey)
    {
        $this->apiKey   =   $apiKey;
    }
}