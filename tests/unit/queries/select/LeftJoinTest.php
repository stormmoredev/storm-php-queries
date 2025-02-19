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
            ->select('categories')
            ->leftJoin('products', 'category_id = category_id');

        $sql = remove_new_lines($query->getSql());

        $this->assertEquals("SELECT * FROM categories LEFT JOIN products ON category_id = category_id", $sql);
    }

    public function testLeftOutJoin(): void
    {
        $query = $this->queries
            ->select('categories')
            ->join('products', ['category_id' => 'category_id']);

        $sql = remove_new_lines($query->getSql());

        $this->assertEquals("SELECT * FROM categories JOIN products ON category_id = category_id", $sql);
    }

    public function testTwoLeftJoin(): void
    {
        $query = $this->queries
            ->select('categories')
            ->leftJoin('tab1', 'a = aa')
            ->join('tab2', 'b = bb');

        $sql = remove_new_lines($query->getSql());

        $this->assertEquals("SELECT * FROM categories LEFT JOIN tab1 ON a = aa JOIN tab2 ON b = bb", $sql);
    }

    protected function setUp(): void
    {
        $mock = $this->createMock(IConnection::class);
        $this->queries = new StormQueries($mock);
    }
}