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

namespace Telegram\Exception;

use Telegram\TelegramException;

/**
 * Class APIException
 * @package Telegram\Exception
 */
class APIException extends TelegramException
{
    /**
     * @param int $code
     * @param string $message
     * @param string $endPoint
     * @return APIException
     */
    public static function ConnectionError(int $code, string $message, string $endPoint) : self
    {
        return new self(
            sprintf('[HttpClientException][#%1$d]: [%2$s] %3$s', $code, $endPoint, $message)
        );
    }

    /**
     * @param string $endPoint
     * @param int $got
     * @param int $expected
     * @return APIException
     */
    public static function ResponseCode(string $endPoint, int $got, int $expected = 200) : self
    {
        return new self(
            sprintf(
                'Got HTTP response code %2$d for endpoint "%1$s", expecting %3$d',
                $endPoint,
                $got,
                $expected
            )
        );
    }

    /**
     * @param string $endPoint
     * @param string $type
     * @return APIException
     */
    public static function BadResponseType(string $endPoint, string $type) : self
    {
        return new self(sprintf('Bad response type "%2$s" for endpoint "%1$s"', $endPoint, $type));
    }

    /**
     * @param string $endPoint
     * @param string $got
     * @param string $expected
     * @return APIException
     */
    public static function BadResultType(string $endPoint, string $got, string $expected) : self
    {
        return new self(
            sprintf(
                'Bad result type "%2$s", expected "%3$s" for endpoint "%1$s"',
                $endPoint,
                ucfirst(strtolower($got)),
                ucfirst(strtolower($expected))
            )
        );
    }

    /**
     * @param string $endPoint
     * @param string $message
     * @param int $code
     * @return APIException
     */
    public static function Failed(string $endPoint, string $message, int $code) : self
    {
        return new self(sprintf('[%1$s][%3$d] %2$s', $endPoint, $message, $code));
    }
}