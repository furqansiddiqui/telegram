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