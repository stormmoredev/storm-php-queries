<?php

namespace integration;

use data\ConnectionProvider;
use PHPUnit\Framework\TestCase;
use Storm\Query\StormQueries;

class SubqueryTest extends TestCase
{
    private static StormQueries $queries;

    public function testAveragePrice(): void
    {
        $items = self::$queries
            ->select("*")
            ->from("products")
            ->where("category_id", 1)
            ->where('price', '<=',
                self::$queries
                    ->select("avg(price)")
                    ->from("products")
                    ->where("category_id", 1)
            )
            ->findAll();

        $this->assertCount(10, $items);
    }

    public static function setUpBeforeClass(): void
    {
        self::$queries = ConnectionProvider::getStormQueries();
    }
}