<?php

namespace integration\queries;

use data\ConnectionProvider;
use PHPUnit\Framework\TestCase;
use Stormmore\Queries\StormQueries;

final class InsertTest extends TestCase
{
    private StormQueries $queries;

    public function testInsert(): void
    {
        $id = $this->queries->insert('insert_test', ['name' => 'first']);

        $this->assertEquals(1, $id);
    }

    public function testInsertMany(): void
    {
        $this->queries->insertMany('insert_test', [
            ['name' => 'name1'],
            ['name' => 'name2'],
            ['name' => 'name3']
        ]);

        $this->assertEquals(4, $this->queries->count('insert_test'));
    }

    public function setUp(): void
    {
        $this->queries = ConnectionProvider::getStormQueries();
    }
}