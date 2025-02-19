<?php

namespace integration\queries;

use data\ConnectionProvider;
use PHPUnit\Framework\TestCase;
use Stormmore\Queries\StormQueries;

final class WhereTest extends TestCase
{
    private static StormQueries $queries;

    public function testDefault(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $items = $queries
            ->select('customers')
            ->where('customer_id', 77)
            ->findAll();

        $this->assertCount(1, $items);
    }

    public function testEqual(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $items = $queries
            ->select('customers')
            ->where('customer_id', '=', 77)
            ->findAll();

        $this->assertCount(1, $items);
    }

    public function testWhereWithNotEqual(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $items = $queries
            ->select('customers')
            ->where('country', '<>', 'USA')
            ->findAll();

        $this->assertCount(78, $items);
    }

    public function testWhereWithNotWordEqual(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $items = $queries
            ->select('customers')
            ->where('country', 'NOT', 'USA')
            ->findAll();

        $this->assertCount(78, $items);
    }

    public function testWhereIn(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $items = $queries
            ->select('customers')
            ->where('country', 'IN', ['USA', 'Germany'])
            ->findAll();

        $this->assertCount(24, $items);
    }

    public function testWhereNotIn(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $items = $queries
            ->select('customers')
            ->where('country', 'NOT IN', ['Germany', 'USA'])
            ->findAll();

        $this->assertCount(67, $items);
    }

    public function testWhereGreater(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $items = $queries
            ->select('customers')
            ->where('customer_id', '>', 89)
            ->findAll();

        $this->assertCount(2, $items);
    }

    public function testWhereGreaterEqual(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $items = $queries
            ->select('customers')
            ->where('customer_id', '>=', 89)
            ->findAll();

        $this->assertCount(3, $items);
    }

    public function testWhereLess(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $items = $queries
            ->select('customers')
            ->where('customer_id', '<', 5)
            ->findAll();

        $this->assertCount(4, $items);
    }

    public function testWhereLessEqual(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $items = $queries
            ->select('customers')
            ->where('customer_id', '<=', 5)
            ->findAll();

        $this->assertCount(5, $items);
    }

    public function testWherePercentLike(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $items = $queries
            ->select('customers')
            ->where('customer_name', 'LIKE', '%a')
            ->findAll();

        $this->assertCount(7, $items);
    }

    public function testWhereLikePercent(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $items = $queries
            ->select('customers')
            ->where('customer_name', 'LIKE', 'A%')
            ->findAll();

        $this->assertCount(4, $items);
    }

    public function testWherePercentLikePercent(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $items = $queries
            ->select('customers')
            ->where('customer_name', 'LIKE', '%z%')
            ->findAll();

        $this->assertCount(5, $items);
    }

    public function testWhereIsNull(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $items = $queries
            ->select('shippers')
            ->where('phone', 'IS NULL')
            ->findAll();

        $this->assertCount(1, $items);
    }

    public function testWhereIsNotNull(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $items = $queries
            ->select('shippers')
            ->where('phone', 'IS NOT NULL')
            ->findAll();

        $this->assertCount(3, $items);
    }

    public function testWhereBetween(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $items = $queries
            ->select('customers')
            ->where('customer_id', 'BETWEEN', 10 , 20)
            ->findAll();

        $this->assertCount(11, $items);
    }

    public function testStringWhere(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $items = $queries
            ->select('customers')
            ->where('customer_name LIKE ? and country = ?', ['%a', 'Brazil'])
            ->findAll();

        $this->assertCount(3, $items);
    }

    public function testWhereArray(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $items = $queries
            ->select('customers')
            ->where(['country' => 'France', 'city' => 'Paris'])
            ->findAll();

        $this->assertCount(2, $items);
    }
}