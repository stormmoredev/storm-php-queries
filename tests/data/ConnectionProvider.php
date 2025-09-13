<?php

namespace data;

use Stormmore\Queries\Connection;
use Stormmore\Queries\ConnectionFactory;
use Stormmore\Queries\IConnection;
use Stormmore\Queries\StormQueries;

class ConnectionProvider
{
    private static ?Connection $connection = null;

    public static function getConnection(): IConnection
    {
        if (self::$connection === null) {
            self::$connection = ConnectionFactory::createFromString(CONNECTION_STRING, CONNECTION_USER, CONNECTION_PASS);
        }
        return self::$connection;
    }

    public static function getStormQueries(): StormQueries
    {
        return new StormQueries(self::getConnection());
    }
}