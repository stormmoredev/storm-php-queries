<?php

namespace integration\queries;

use data\ConnectionProvider;
use PHPUnit\Framework\TestCase;

final class HavingTest extends TestCase
{
    public function testHaving(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $items = $queries
            ->select('customers', 'country, city, count(*)')
            ->groupBy('country, city')
            ->having('count(*)', '>', 1)
            ->having('city', 'LIKE', '%o%')
            ->findAll();

        $this->assertCount(7, $items);
    }
}