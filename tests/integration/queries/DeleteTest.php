<?php

namespace integration\queries;

use data\ConnectionProvider;
use PHPUnit\Framework\TestCase;
use Stormmore\Queries\StormQueries;

final class DeleteTest extends TestCase
{
    public function testDelete(): void
    {
        $queries = ConnectionProvider::getStormQueries();
        $queries->delete('delete_test', ['id' => 1]);

        $count = $queries->from('delete_test')->count();

        $this->assertEquals(2, $count);
    }

    public function testDeleteSqlWhere(): void
    {
        $queries = ConnectionProvider::getStormQueries();
        $queries->delete('delete_test', 'id = ?', 2);

        $count = $queries->from('delete_test')->count();

        $this->assertEquals(1, $count);
    }
}