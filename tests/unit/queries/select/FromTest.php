<?php

namespace unit\queries\select;

use PHPUnit\Framework\TestCase;
use Storm\Query\IConnection;
use Storm\Query\Queries\SelectQuery;

class FromTest extends TestCase
{
    private SelectQuery $selectQuery;
    public function testFromClause(): void
    {
        $this->selectQuery->select('customer_name', 'address', 'city');
        $this->selectQuery->from('customers');

        $query = $this->selectQuery->getSql();
        $from = get_nth_line($query, 1);

        $this->assertEquals("FROM customers", $from);
    }

    public function testFromWithAliasClause(): void
    {
        $this->selectQuery->select('customer_name', 'address', 'city');
        $this->selectQuery->from('customers c');

        $query = $this->selectQuery->getSql();
        $from = get_nth_line($query, 1);

        $this->assertEquals("FROM customers c", $from);
    }

    protected function setUp(): void
    {
        $mock = $this->createMock(IConnection::class);
        $this->selectQuery = new SelectQuery($mock);
    }
}