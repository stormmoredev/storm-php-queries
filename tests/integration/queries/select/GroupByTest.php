<?php

namespace integration\queries\select;

use data\ConnectionProvider;
use PHPUnit\Framework\TestCase;
use Stormmore\Queries\StormQueries;

final class GroupByTest extends TestCase
{
    private static StormQueries $queries;

    public function testGroupBY(): void
    {
        $items = self::$queries
            ->selectQuery('country, city, count(*)')
            ->from('customers')
            ->groupBy('country, city')
            ->findAll();

        $this->assertCount(69, $items);
    }

    public static function setUpBeforeClass(): void
    {
        self::$queries = ConnectionProvider::getStormQueries();
    }
}