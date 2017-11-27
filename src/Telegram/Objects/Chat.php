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

namespace Telegram\Objects;

use Telegram\Exception\UpdateException;

/**
 * Class Chat
 * @package Telegram\Objects
 */
class Chat
{
    /** @var int */
    public $id;
    /** @var string */
    public $type;
    /** @var null|string */
    public $username;
    /** @var null|string */
    public $firstName;
    /** @var null|string */
    public $lastName;

    /**
     * @param array $data
     * @return Chat
     * @throws UpdateException
     */
    public static function Parse(array $data) : self
    {
        $chat   =   new self();
        $chat->id   =   $data["id"] ?? null;
        if(!is_int($chat->id)   ||  $chat->id   <   1) {
            throw new UpdateException(sprintf('Parsing object Chat failed at line %d', __LINE__));
        }

        $chat->type =   $data["type"] ?? null;
        if(!is_string($chat->type)  ||  !in_array($chat->type, ["private","group","supergroup","channel"])) {
            throw new UpdateException(sprintf('Parsing object Chat failed at line %d', __LINE__));
        }

        $chat->username =   $data["username"] ?? null;
        $chat->firstName    =   $data["first_name"] ?? null;
        $chat->lastName =   $data["last_name"] ?? null;

        return $chat;
    }
}