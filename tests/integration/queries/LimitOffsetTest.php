<?php

namespace integration\queries;

use data\ConnectionProvider;
use PHPUnit\Framework\TestCase;
use Stormmore\Queries\StormQueries;

class LimitOffsetTest extends TestCase
{
    private StormQueries $queries;

    public function testLimit(): void
    {
        $customers = $this->queries
            ->from('customers')
            ->orderByAsc('customer_id')
            ->limit(3)
            ->findAll();

        $this->assertCount(3, $customers);
        $this->assertEquals(1, $customers[0]->customer_id);
    }

    public function setUp(): void
    {
        $this->queries = ConnectionProvider::getStormQueries();
    }
}