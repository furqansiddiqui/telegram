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
use Telegram\Objects\Message;
use Telegram\Telegram;
use Telegram\TelegramException;

/**
 * Class AbstractHandler
 * @package Telegram
 */
abstract class AbstractHandler
{
    /** @var Telegram */
    protected $telegram;
    /** @var bool */
    protected $sendAsHTML;
    /** @var Message */
    protected $message;

    /**
     * AbstractHandler constructor.
     * @param Telegram $telegram
     */
    public function __construct(Telegram $telegram)
    {
        $this->telegram =   $telegram;
        $this->sendAsHTML   =   false;
    }

    /**
     * @param bool|null $bool
     * @return bool
     */
    final public function sendAsHTML(bool $bool = null) : bool
    {
        if(is_null($bool)) {
            return $this->sendAsHTML;
        }

        $this->sendAsHTML   =   $bool;
        return $bool;
    }

    /**
     * @param Message $message
     * @return AbstractHandler
     * @throws HandlerException
     */
    final public function setMessage(Message $message) : self
    {
        if(!$message->text) {
            throw new HandlerException("Sorry! I don't understand non-text messages");
        }

        if($message->chat->type !== "private") {
            throw new HandlerException("Sorry! You can only communicate with me in a private channel");
        }

        $this->message  =   $message;
        return $this;
    }

    /**
     * @return Message
     */
    final public function message() : Message
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @throws TelegramException
     */
    final public function sendReply(string $message)
    {
        $this->telegram->sendMessage($this->message->chat->id, $message, $this->sendAsHTML());
    }

    /**
     * START command, Should validate user and save chat_id for future referencing
     * @return void
     */
    abstract public function start();

    /**
     * HELP command, Should send reply with list of available commands
     * @return void
     */
    abstract public function help();
}