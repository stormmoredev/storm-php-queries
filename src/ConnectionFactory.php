<?php

namespace Storm\Query;

use PDO;

class ConnectionFactory
{
    public static function createFromString(string $connection, string $user, string $password): IConnection
    {
        return new Connection($connection, $user, $password);
    }
}