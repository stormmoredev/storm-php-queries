<?php

namespace integration\queries;

use data\ConnectionProvider;
use PHPUnit\Framework\TestCase;
use Stormmore\Queries\StormQueries;

class LimitOffsetTest extends TestCase
{
    public function testLimitOffset(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $customers = $queries
            ->select('customers')
            ->orderByAsc('customer_id')
            ->pagination(3, 5)
            ->findAll();

        $this->assertCount(3, $customers);
        $this->assertEquals(6, $customers[0]->customer_id);
    }
}