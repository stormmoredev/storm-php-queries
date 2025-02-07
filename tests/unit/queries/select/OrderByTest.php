<?php

namespace unit\queries\select;

use PHPUnit\Framework\TestCase;
use Stormmore\Queries\IConnection;
use Stormmore\Queries\StormQueries;

final class OrderByTest extends TestCase
{
    private StormQueries $queries;

    public function testOrderByAsc(): void
    {
        $query = $this->queries
            ->select('*')
            ->from('customers')
            ->orderByAsc('customer_id');

        $line = get_nth_line($query->getSql(), 2);

        $this->assertEquals("ORDER BY customer_id ASC", $line);
    }

    public function testOrderByDesc(): void
    {
        $query = $this->queries
            ->select('*')
            ->from('customers')
            ->orderByDesc('customer_id');

        $line = get_nth_line($query->getSql(), 2);

        $this->assertEquals("ORDER BY customer_id DESC", $line);
    }

    public function testOrderByAscThenOrderByDesc(): void
    {
        $query = $this->queries
            ->select('*')
            ->from('customers')
            ->orderByAsc('customer_id')
            ->orderByDesc('customer_name');

        $line = get_nth_line($query->getSql(), 2);

        $this->assertEquals("ORDER BY customer_id ASC, customer_name DESC", $line);
    }

    protected function setUp(): void
    {
        $mock = $this->createMock(IConnection::class);
        $this->queries = new StormQueries($mock);
    }
}