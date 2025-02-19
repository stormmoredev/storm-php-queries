<?php

namespace integration;

use data\ConnectionProvider;
use Exception;
use PHPUnit\Framework\TestCase;
use Stormmore\Queries\StormQueries;

interface Notifier
{
    function onSuccess();
    function onFailure();
}

final class ConnectionEventTest  extends TestCase
{
    public function testQuerySuccess(): void
    {
        $called = false;
        $connection = ConnectionProvider::getConnection();
        $connection->onSuccess(function($sql, $interval) use (&$called) {
            $called = true;
        });

        $queries = new StormQueries($connection);
        $queries->selectQuery('*')
            ->from('customers')
            ->where('customer_id', 5)
            ->findAll();

        $this->assertTrue($called);
    }

    public function testOnFailure(): void
    {
        $called = false;
        $connection = ConnectionProvider::getConnection();
        $connection->onFailure(function($sql, $interval, $e) use (&$called) {
            $called = true;
        });
        $queries = new StormQueries($connection);
        try {
            $queries->selectQuery('*')
                ->from('customers')
                ->where('customer_i', 5)
                ->findAll();
        } catch(Exception) { }

        $this->assertTrue($called);
    }
}