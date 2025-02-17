<?php

namespace unit\queries\select;

use PHPUnit\Framework\TestCase;
use Stormmore\Queries\IConnection;
use Stormmore\Queries\StormQueries;

final class LeftJoinTest extends TestCase
{
    private StormQueries $queries;

    public function testLeftJoin(): void
    {
        $query = $this->queries
            ->select('*')
            ->from('categories')
            ->leftJoin('products', 'category_id = category_id');

        $sql = remove_new_lines($query->getSql());

        $this->assertEquals("SELECT * FROM categories LEFT JOIN products ON category_id = category_id", $sql);
    }

    public function testLeftOutJoin(): void
    {
        $query = $this->queries
            ->select('*')
            ->from('categories')
            ->leftOuterJoin('products', ['category_id' => 'category_id']);

        $sql = remove_new_lines($query->getSql());

        $this->assertEquals("SELECT * FROM categories LEFT OUTER JOIN products ON category_id = category_id", $sql);
    }

    public function test2LeftJoin(): void
    {
        $query = $this->queries
            ->select('*')
            ->from('categories')
            ->leftJoin('tab1', 'a = aa')
            ->leftOuterJoin('tab2', 'b = bb');

        $sql = remove_new_lines($query->getSql());

        $this->assertEquals("SELECT * FROM categories LEFT JOIN tab1 ON a = aa LEFT OUTER JOIN tab2 ON b = bb", $sql);
    }

    protected function setUp(): void
    {
        $mock = $this->createMock(IConnection::class);
        $this->queries = new StormQueries($mock);
    }
}