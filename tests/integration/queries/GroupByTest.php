<?php

namespace integration\queries;

use data\ConnectionProvider;
use PHPUnit\Framework\TestCase;

final class GroupByTest extends TestCase
{
    public function testGroupBy(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $items = $queries
            ->select('customers', 'country, city, count(*)')
            ->groupBy('country, city')
            ->findAll();

        $this->assertCount(69, $items);
    }
}