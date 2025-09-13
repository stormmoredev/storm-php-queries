<?php

namespace unit\queries\select;

use PHPUnit\Framework\TestCase;
use Stormmore\Queries\IConnection;
use Stormmore\Queries\StormQueries;

final class GroupByTest extends TestCase
{
    private StormQueries $queries;

    public function testGroupBy(): void
    {
        $query = $this->queries
            ->select('customers', 'country, city, count(*)')
            ->groupBy('country, city, count(*)');

        $sql = $query->getSql();
        $sql = remove_new_lines($sql);

        $expected = "SELECT country, city, count(*) FROM customers GROUP BY country, city, count(*)";
        $this->assertEquals($expected, $sql);
    }

    protected function setUp(): void
    {
        $mock = $this->createMock(IConnection::class);
        $this->queries = new StormQueries($mock);
    }
}