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
 * Class Update
 * @package Telegram\Objects
 */
class Update
{
    /** @var int */
    public $id;
    /** @var string|null */
    public $type;
    /** @var null|Message */
    public $message;

    /**
     * @param array $data
     * @return Update
     * @throws UpdateException
     */
    public static function Parse(array $data) : self
    {
        $update =   new Update();
        $update->id =   $data["update_id"] ?? null;
        if(!is_int($update->id) ||  $update->id <   1) {
            throw new UpdateException(sprintf('Parsing object Update failed at line %d', __LINE__));
        }

        $message    =   $data["message"] ?? null;
        if(is_array($message)) {
            $update->type   =   "message";
            $update->message    =   Message::Parse($message);
        }

        if(!$update->type) {
            // Unsupported type
            throw new UpdateException(sprintf('Parsing object Update failed at line %d', __LINE__));
        }

        return $update;
    }
}