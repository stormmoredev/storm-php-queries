<?php

namespace unit\queries;

use PHPUnit\Framework\TestCase;
use Storm\Query\IConnection;
use Storm\Query\Queries\SelectQuery;
use InvalidArgumentException;

final class SelectTest extends TestCase
{
    private SelectQuery $selectBuilder;

    public function testQueryValidationWhereThereIsNoSelectAndFromClause(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->selectBuilder->getSql();
    }

    public function testQueryValidationWhereThereIsNoSelectClause(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->selectBuilder->from('users');

        $this->selectBuilder->getSql();
    }

    public function testQueryValidationWhereThereIsNoFromClause(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->selectBuilder->select('*');

        $this->selectBuilder->getSql();
    }

    public function testQueryWithSelectAndFrom(): void
    {
        $this->selectBuilder->select('*');
        $this->selectBuilder->from('users');
        $query = $this->selectBuilder->getSql();
        $query = trim(str_replace("\n", ' ', $query));

        $this->assertEquals("SELECT * FROM users", $query);
    }

    public function testCombiningSelectInvokes(): void
    {
        $this->selectBuilder->select('colA');
        $this->selectBuilder->select('colB');
        $this->selectBuilder->from('users');

        $query = $this->selectBuilder->getSql();
        $query = trim(str_replace("\n", ' ', $query));

        $this->assertEquals("SELECT colA, colB FROM users", $query);
    }

    protected function setUp(): void
    {
        $mock = $this->createMock(IConnection::class);
        $this->selectBuilder = new SelectQuery($mock);
    }
}