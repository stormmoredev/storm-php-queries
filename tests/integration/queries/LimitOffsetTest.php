<?php

namespace integration\queries;

use data\ConnectionProvider;
use PHPUnit\Framework\TestCase;
use Stormmore\Queries\StormQueries;

class LimitOffsetTest extends TestCase
{
    private StormQueries $queries;

    public function testLimitOffset(): void
    {
        $customers = $this->queries
            ->select('customers')
            ->orderByAsc('customer_id')
            ->pagination(3, 5)
            ->findAll();

        $this->assertCount(3, $customers);
        $this->assertEquals(6, $customers[0]->customer_id);
    }

    public function setUp(): void
    {
        $this->queries = ConnectionProvider::getStormQueries();
    }
}