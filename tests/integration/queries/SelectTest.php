<?php

namespace integration\queries;

use data\ConnectionProvider;
use PHPUnit\Framework\TestCase;
use Stormmore\Queries\StormQueries;

final class SelectTest extends TestCase
{
    private static StormQueries $queries;

    public function testFindOne(): void
    {
        $item = self::$queries
            ->select('customers')
            ->where('city', 'London')
            ->find();

        $this->assertNotNull($item);
    }

    public function testFindAll(): void
    {
        $items = self::$queries
            ->select('customers')
            ->where('city', 'London')
            ->findAll();

        $this->assertCount(6, $items);
    }

    public function testMinFunction(): void
    {
        $max = self::$queries->select('products')->min('price');

        $this->assertEquals(2.50, $max);
    }

    public function testMaxFunction(): void
    {
        $max = self::$queries->select('products')->max('price');

        $this->assertEquals(263.50, $max);
    }

    public function testCountFunction(): void
    {
        $count = self::$queries->select('products')->count();

        $this->assertEquals(77, $count);
    }

    public function testSumFunction(): void
    {
        $count = self::$queries->select('products')->sum('price');

        $this->assertEquals(2222.71, $count);
    }

    public function testAvgFunction(): void
    {
        $avg = self::$queries->select('products')->avg('price');

        $this->assertEquals(28.86636, round($avg, 5));
    }

    public static function setUpBeforeClass(): void
    {
        self::$queries = ConnectionProvider::getStormQueries();
    }
}