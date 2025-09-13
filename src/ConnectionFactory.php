<?php

namespace Stormmore\Queries;

class ConnectionFactory
{
    public static function createFromString(string $connection, string $user, string $password): IConnection
    {
        return new Connection($connection, $user, $password);
    }
}