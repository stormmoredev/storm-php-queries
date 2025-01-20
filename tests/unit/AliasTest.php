<?php

namespace unit;

use PHPUnit\Framework\TestCase;
use Storm\Query\IConnection;
use Storm\Query\Queries\SelectQuery;

final class AliasTest extends TestCase
{
    private SelectQuery $selectQuery;

    public function testSelectBuilder(): void
    {
        $this->selectQuery->select('customer_name', 'address', 'city');
        $this->selectQuery->from('customers');

        $query = $this->selectQuery->getSql();
        $select = get_nth_line($query, 0);

        $this->assertEquals("SELECT customer_name, address, city", $select);
    }

    public function testSelectWithAliasesBuilder(): void
    {
        $this->selectQuery->select('customer_name as cn', 'address as a', 'city c');
        $this->selectQuery->from('customers');

        $query = $this->selectQuery->getSql();
        $select = get_nth_line($query, 0);

        $this->assertEquals("SELECT customer_name as cn, address as a, city c", $select);
    }

    protected function setUp(): void
    {
        $mock = $this->createMock(IConnection::class);
        $this->selectQuery = new SelectQuery($mock);
    }
}