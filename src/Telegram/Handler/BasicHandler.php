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

namespace Telegram\Handler;

use Telegram\Exception\HandlerException;
use Telegram\Objects\Message\Entity;
use Telegram\Telegram;

/**
 * Class BasicHandler
 * @package Telegram\Handler
 */
class BasicHandler extends AbstractHandler
{
    public function start()
    {
        $botCommand =   false;
        $entity =   $this->message->entities[0] ?? null;
        if($entity instanceof Entity) {
            if($entity->type    === "bot_command") {
                $botCommand =   true;
            }
        }

        if(!$botCommand) {
            throw new HandlerException('Failed to verify "start" as a command');
        }

        $this->sendReply("You've successfully connected to this telegram bot.");
        $this->sendReply(sprintf(
            'We are using "%s" v%s package to make it possible for you interact with our App via Telegram.',
            "furqansiddiqui/telegram",
            Telegram::VERSION
        ));
    }

    public function help()
    {
        $this->sendReply(sprintf(
            'We are using "%s" v%s package to make it possible for you interact with our App via Telegram.',
            "furqansiddiqui/telegram",
            Telegram::VERSION
        ));
    }

    public function chat()
    {
        try {
            $message    =   strtolower($this->message->text);

            if(preg_match('/^(hi|hello|hey)$/i', $message)) {
                throw new HandlerException('Hello there! Good day to you too!');
            }

            throw new HandlerException(
                'Sorry! I have not yet been polished to talk like a robot. You may use Use /help for list of commands'
            );
        } catch (HandlerException $e) {
            $this->sendReply($e->getMessage());
        }
    }
}