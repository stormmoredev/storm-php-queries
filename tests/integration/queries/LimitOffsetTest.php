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
            ->from('customers')
            ->orderByAsc('customer_id')
            ->limit(3)
            ->offset(5)
            ->findAll();

        $this->assertCount(3, $customers);
        $this->assertEquals(6, $customers[0]->customer_id);
    }

    public function testOffset(): void
    {
        $customers = $this->queries
            ->from('customers')
            ->orderByAsc('customer_id')
            ->offset(70)
            ->findAll();

        $this->assertCount(21, $customers);
        $this->assertEquals(71, $customers[0]->customer_id);
    }

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