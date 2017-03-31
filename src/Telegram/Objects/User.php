<?php
declare(strict_types=1);

namespace Telegram\Objects;

use Telegram\Exception\UpdateException;

/**
 * Class User
 * @package Telegram\Objects
 */
class User
{
    /** @var int */
    public $id;
    /** @var string */
    public $firstName;
    /** @var null|string */
    public $lastName;
    /** @var null|string */
    public $username;

    /**
     * @param array $data
     * @return User
     * @throws UpdateException
     */
    public static function Parse(array $data) : self
    {
        $user   =   new self();
        $user->id   =   $data["id"] ?? null;
        if(!is_int($user->id)   ||  $user->id   <   1) {
            throw new UpdateException(sprintf('Parsing object User failed at line %d', __LINE__));
        }

        $user->firstName    =   $data["first_name"] ?? null;
        if(!is_string($user->firstName) ||  !strlen($user->firstName)) {
            throw new UpdateException(sprintf('Parsing object User failed at line %d', __LINE__));
        }

        $user->username =   $data["username"] ?? null;
        $user->lastName =   $data["last_name"] ?? null;

        return $user;
    }
}