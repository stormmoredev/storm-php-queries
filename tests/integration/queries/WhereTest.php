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
        $items = self::$queries
            ->select('*')
            ->from('customers')
            ->where('customer_id', 77)
            ->findAll();

        $this->assertCount(1, $items);
    }

    public function testEqual(): void
    {
        $items = self::$queries
            ->select('*')
            ->from('customers')
            ->where('customer_id', '=', 77)
            ->findAll();

        $this->assertCount(1, $items);
    }

    public function testWhereWithNotEqual(): void
    {
        $items = self::$queries
            ->select('*')
            ->from('customers')
            ->where('country', '<>', 'USA')
            ->findAll();

        $this->assertCount(78, $items);
    }

    public function testWhereWithNotWordEqual(): void
    {
        $items = self::$queries
            ->select('*')
            ->from('customers')
            ->where('country', 'NOT', 'USA')
            ->findAll();

        $this->assertCount(78, $items);
    }

    public function testWhereIn(): void
    {
        $items = self::$queries
            ->select('*')
            ->from('customers')
            ->where('country', 'IN', ['USA', 'Germany'])
            ->findAll();

        $this->assertCount(24, $items);
    }

    public function testWhereNotIn(): void
    {
        $items = self::$queries
            ->select('*')
            ->from('customers')
            ->where('country', 'NOT IN', ['Germany', 'USA'])
            ->findAll();

        $this->assertCount(67, $items);
    }

    public function testWhereGreater(): void
    {
        $items = self::$queries
            ->select('*')
            ->from('customers')
            ->where('customer_id', '>', 89)
            ->findAll();

        $this->assertCount(2, $items);
    }

    public function testWhereGreaterEqual(): void
    {
        $items = self::$queries
            ->select('*')
            ->from('customers')
            ->where('customer_id', '>=', 89)
            ->findAll();

        $this->assertCount(3, $items);
    }

    public function testWhereLess(): void
    {
        $items = self::$queries
            ->select('*')
            ->from('customers')
            ->where('customer_id', '<', 5)
            ->findAll();

        $this->assertCount(4, $items);
    }

    public function testWhereLessEqual(): void
    {
        $items = self::$queries
            ->select('*')
            ->from('customers')
            ->where('customer_id', '<=', 5)
            ->findAll();

        $this->assertCount(5, $items);
    }

    public function testWherePercentLike(): void
    {
        $items = self::$queries
            ->select('*')
            ->from('customers')
            ->where('customer_name', 'LIKE', '%a')
            ->findAll();

        $this->assertCount(7, $items);
    }

    public function testWhereLikePercent(): void
    {
        $items = self::$queries
            ->select('*')
            ->from('customers')
            ->where('customer_name', 'LIKE', 'A%')
            ->findAll();

        $this->assertCount(4, $items);
    }

    public function testWherePercentLikePercent(): void
    {
        $items = self::$queries
            ->select('*')
            ->from('customers')
            ->where('customer_name', 'LIKE', '%z%')
            ->findAll();

        $this->assertCount(5, $items);
    }

    public function testWhereIsNull(): void
    {
        $items = self::$queries
            ->select('*')
            ->from('shippers')
            ->where('phone', 'IS NULL')
            ->findAll();

        $this->assertCount(1, $items);
    }

    public function testWhereIsNotNull(): void
    {
        $items = self::$queries
            ->select('*')
            ->from('shippers')
            ->where('phone', 'IS NOT NULL')
            ->findAll();

        $this->assertCount(3, $items);
    }

    public function testWhereBetween(): void
    {
        $items = self::$queries
            ->select('*')
            ->from('customers')
            ->where('customer_id', 'BETWEEN', 10 , 20)
            ->findAll();

        $this->assertCount(11, $items);
    }

    public function testStringWhere(): void
    {
        $items = self::$queries
            ->select('*')
            ->from('customers')
            ->where('customer_name LIKE ? and country = ?', ['%a', 'Brazil'])
            ->findAll();

        $this->assertCount(3, $items);
    }

    public function testWhereArray(): void
    {
        $items = self::$queries
            ->select('*')
            ->from('customers')
            ->where(['country' => 'France', 'city' => 'Paris'])
            ->findAll();

        $this->assertCount(2, $items);
    }

    public static function setUpBeforeClass(): void
    {
        self::$queries = ConnectionProvider::getStormQueries();
    }
}