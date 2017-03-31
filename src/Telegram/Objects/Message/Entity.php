<?php
declare(strict_types=1);

namespace Telegram\Objects\Message;
use Telegram\Exception\UpdateException;

/**
 * Class Entity
 * @package Telegram\Objects\Message
 */
class Entity
{
    /** @var string */
    public $type;
    /** @var int */
    public $offset;
    /** @var int */
    public $length;

    /**
     * @param array $data
     * @return Entity
     * @throws UpdateException
     */
    public static function Parse(array $data) : self
    {
        $entity =   new self();
        $entity->type   =   $data["type"] ?? null;

        $validTypes =   [
            "mention",
            "hashtag",
            "bot_command",
            "url",
            "email",
            "bold",
            "italic",
            "code",
            "pre",
            "text_link",
            "text_mention"
        ];
        if(!is_string($entity->type)    ||  !in_array($entity->type, $validTypes)) {
            throw new UpdateException(sprintf('Parsing object Message/Entity failed at line %d', __LINE__));
        }

        $entity->offset =   $data["offset"] ?? null;
        if(!is_int($entity->offset)) {
            throw new UpdateException(sprintf('Parsing object Message/Entity failed at line %d', __LINE__));
        }

        $entity->length =   $data["length"] ??  null;
        if(!is_int($entity->length)) {
            throw new UpdateException(sprintf('Parsing object Message/Entity failed at line %d', __LINE__));
        }

        return $entity;
    }
}