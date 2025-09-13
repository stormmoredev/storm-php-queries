<?php

namespace unit;

use data\ConnectionProvider;
use PHPUnit\Framework\TestCase;
use Stormmore\Queries\StormQueries;

final class SubqueryQueryTest extends TestCase
{
    private StormQueries $queries;

    public function testParameters(): void
    {
        $query = $this->queries
            ->select('users')
            ->where('fieldA', '=', 'val1')
            ->where('field4', 'IN',
                $this->queries
                    ->select('table')
                    ->where('FieldB', '=', 5))
                    ->orWhere('FieldC', '=', 'valB')
            ->where('field5', '=', 9);

        $parameters = $query->getParameters();

        $this->assertEquals(['val1', 5, 'valB', 9], $parameters);
    }

    public function testSql(): void
    {
        $query = $this->queries
            ->select('users')
            ->where('fieldA', '=', 'val1')
            ->where('field4', 'IN',
                $this->queries
                    ->select('table')
                    ->where('fieldB', '=', 5))
            ->orWhere('fieldC', '=', 'valB')
            ->where('field5', '=', 9);

        $sql = $query->getSql();
        $sql = remove_new_lines($sql);

        $expected = "SELECT * FROM users WHERE fieldA = ? AND field4 IN (SELECT * FROM table WHERE fieldB = ?) OR fieldC = ? AND field5 = ?";

        $this->assertEquals($sql, $expected);
    }

    protected function setUp(): void
    {
        $this->queries = ConnectionProvider::getStormQueries();
    }
}