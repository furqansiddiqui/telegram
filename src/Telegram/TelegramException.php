<?php
declare(strict_types=1);

namespace Telegram;

/**
 * Class TelegramException
 * @package Telegram
 */
class TelegramException extends \Exception
{
    /**
     * @param string $method
     * @param int $line
     * @param string $message
     * @param int|null $code
     * @return TelegramException
     */
    public static function Method(string $method, int $line, string $message, int $code = null) : self
    {
        return new self(sprintf('[%1$s:%2$d] %3$s', $method, $line, $message), $code);
    }
}