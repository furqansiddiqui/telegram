<?php
declare(strict_types=1);

namespace Telegram\Objects;

use Telegram\Exception\UpdateException;
use Telegram\Objects\Message\Entity;

/**
 * Class Message
 * @package Telegram\Objects
 */
class Message implements UpdateInterface
{
    /** @var int */
    public $id;
    /** @var Chat */
    public $chat;
    /** @var int */
    public $timeStamp;
    /** @var null|string */
    public $type;
    /** @var null|string */
    public $text;
    /** @var null|array */
    public $entities;
    /** @var null|User */
    public $from;

    /**
     * @param array $data
     * @return Message
     * @throws UpdateException
     */
    public static function Parse(array $data) : self
    {
        $message    =   new self();
        $message->id    =   $data["message_id"] ?? null;
        if(!is_int($message->id)    ||  $message->id    <   1) {
            throw new UpdateException(sprintf('Parsing object Message failed at line %d', __LINE__));
        }

        // Timestamp
        $message->timeStamp =   $data["date"] ?? null;
        if(!is_int($message->timeStamp) ||  $message->timeStamp <   1) {
            throw new UpdateException(sprintf('Parsing object Message failed at line %d', __LINE__));
        }

        // Chat
        $chat   =   $data["chat"] ?? null;
        if(!is_array($chat)) {
            throw new UpdateException(sprintf('Parsing object Message failed at line %d', __LINE__));
        }

        $message->chat  =   Chat::Parse($chat);

        // From
        $from   =   $data["from"] ?? null;
        if(is_array($from)) {
            $message->from  =   User::Parse($from);
        }

        // Type
        $text   =   $data["text"] ?? null;
        if(is_string($text) &&  strlen($text)) {
            $message->type  =   "text";
            $message->text  =   $text;

            // Entities
            $message->entities  =   [];
            $entities   =   $data["entities"] ?? null;
            if(is_array($entities)  &&  count($entities)) {
                foreach($entities as $entity) {
                    try {
                        $entity =   Entity::Parse($entity);
                        $message->entities[]    =   $entity;
                    } catch (\Exception $e) {
                        trigger_error($e->getMessage(), E_USER_WARNING);
                    }
                }
            }
        }

        if(!$message->type) {
            throw new UpdateException('Cannot parse message of such type');
        }

        return $message;
    }
}