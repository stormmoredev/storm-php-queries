<?php

namespace integration\queries;

use data\ConnectionProvider;
use PHPUnit\Framework\TestCase;
use Storm\Query\StormQueries;

final class DeleteTest extends TestCase
{
    private static StormQueries $queries;

    public function testDelete(): void
    {
        self::$queries
            ->delete('delete_test')
            ->where('id', 1)
            ->execute();

        $count = self::$queries->from('delete_test')->count();

        $this->assertEquals(1, $count);
    }

    public function testShortDelete(): void
    {
        self::$queries
            ->delete('delete_test', 'id = ?', 2)
            ->execute();

        $count = self::$queries->from('delete_test')->count();

        $this->assertEquals(0, $count);
    }

    public static function setUpBeforeClass(): void
    {
        self::$queries = ConnectionProvider::getStormQueries();
    }
}