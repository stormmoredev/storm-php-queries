<?php

namespace unit\queries\select;

use PHPUnit\Framework\TestCase;
use Storm\Query\IConnection;
use Storm\Query\Queries\SelectQuery;

class LimitOffsetTest extends TestCase
{
    private SelectQuery $selectQuery;
    public function testLimit(): void
    {
        $this->selectQuery->select('customer_name', 'address', 'city');
        $this->selectQuery->from('customers');
        $this->selectQuery->limit(10);
        $this->selectQuery->offset(15);

        $query = $this->selectQuery->getSql();
        $limit = get_nth_line($query, 2);

        $this->assertEquals("LIMIT 10", $limit);
    }

    public function testOffset(): void
    {
        $this->selectQuery->select('customer_name', 'address', 'city');
        $this->selectQuery->from('customers c');
        $this->selectQuery->limit(10);
        $this->selectQuery->offset(15);

        $query = $this->selectQuery->getSql();
        $offset = get_nth_line($query, 3);

        $this->assertEquals("OFFSET 15", $offset);
    }

    protected function setUp(): void
    {
        $mock = $this->createMock(IConnection::class);
        $this->selectQuery = new SelectQuery($mock);
    }
}