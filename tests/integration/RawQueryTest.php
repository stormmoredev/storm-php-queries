<?php

use data\ConnectionProvider;
use PHPUnit\Framework\TestCase;

class RawQueryTest extends TestCase
{
    public function testSelect(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $items = $queries->query("SELECT * FROM customers WHERE customer_id = ?", [7]);

        $this->assertCount(1, $items);
        $this->assertEquals(7, $items[0]->customer_id);
        $this->assertEquals('Blondel père et fils', $items[0]->customer_name);
    }
}