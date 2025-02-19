<?php

namespace integration\queries;

use data\ConnectionProvider;
use PHPUnit\Framework\TestCase;
use Stormmore\Queries\StormQueries;

final class AggregatesTest extends TestCase
{
    public function testMinFunction(): void
    {
        $queries = ConnectionProvider::getStormQueries();
        $max = $queries->select('products')->min('price');

        $this->assertEquals(2.50, $max);
    }

    public function testMaxFunction(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $max = $queries->select('products')->max('price');

        $this->assertEquals(263.50, $max);
    }

    public function testCountFunction(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $count = $queries->select('products')->count();

        $this->assertEquals(77, $count);
    }

    public function testSumFunction(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $count = $queries->select('products')->sum('price');

        $this->assertEquals(2222.71, $count);
    }

    public function testAvgFunction(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $avg = $queries->select('products')->avg('price');

        $this->assertEquals(28.86636, round($avg, 5));
    }
}