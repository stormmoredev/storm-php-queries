<?php

namespace integration\queries;

use data\ConnectionProvider;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class UnionTest extends TestCase
{
    public function testUnionQuery(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $suppliers = $queries->select('suppliers', 'contact_name');
        $customers = $queries->select('customers', 'customer_name');

        $items = $queries->union($suppliers, $customers)->find();

        Assert::assertCount(120, $items);
    }

    public function testUnionQueryWithWhereClause(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $suppliers = $queries->select('suppliers', 'contact_name')->where('contact_name', 'LIKE', '%Michael%');
        $customers = $queries->select('customers', 'contact_name')->where('contact_name', 'LIKE', '%Maria%');

        $items = $queries->union($suppliers, $customers)->find();

        Assert::assertCount(3, $items);
    }
}