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
    /** @var null|string */
    protected $command;
    /** @var array */
    protected $params;

    /**
     * AbstractHandler constructor.
     * @param Telegram $telegram
     */
    public function __construct(Telegram $telegram)
    {
        $this->telegram =   $telegram;
        $this->sendAsHTML   =   false;
        $this->params   =   [];
    }

    /**
     * @param Message $message
     * @return AbstractHandler
     * @throws HandlerException
     */
    final public function _crunchMessage(Message $message) : self
    {
        if(!$message->text) {
            throw new HandlerException("Sorry! I don't understand non-text messages");
        }

        if($message->chat->type !== "private") {
            throw new HandlerException("Sorry! You can only communicate with me in a private channel");
        }

        $this->message  =   $message;

        // Split in pieces
        $pieces =   preg_split('/\s/', preg_replace('/\s+/', ' ', $this->message->text));
        $command    =   strtolower(strval($pieces[0] ?? ""));
        if(preg_match('/^\/[a-zA-Z0-9\_]+$/', $command)) {
            // Command
            $command    =   preg_split("/\_/", substr($command, 1), 0, PREG_SPLIT_NO_EMPTY);
            $command    =   implode("", array_map(function ($piece) {
                return ucfirst($piece);
            }, $command));
            $command    =   lcfirst($command);
            $this->command  =   $command; // Exists

            // Params
            unset($pieces[0]);
            foreach($pieces as $param) {
                $this->params[] =   preg_replace('/[^a-zA-Z0-9\_]/', '', $param);
            }
        } else {
            $this->command  =   "chat";
        }

        // Check if command method found
        if(!method_exists($this, $this->command)) {
            throw new HandlerException(
                sprintf('Sorry! I cannot handle "%s" command. Use /help for list of commands', $this->command)
            );
        }

        return $this;
    }

    /**
     * Execute requested command
     */
    final public function _execute()
    {

        call_user_func([$this, $this->command]);
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
     * @return Message
     */
    final public function message() : Message
    {
        return $this->message;
    }


    /**
     * Default chat function
     */
    public function chat()
    {
        try {
            throw new HandlerException(
                'Sorry! I have not yet been polished to talk like a robot. You may use Use /help for list of commands'
            );
        } catch (HandlerException $e) {
            $this->sendReply($e->getMessage());
        }
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