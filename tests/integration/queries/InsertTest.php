<?php

namespace integration\queries;

use data\ConnectionProvider;
use DateTime;
use PHPUnit\Framework\TestCase;
use Storm\Query\StormQueries;

final class InsertTest extends TestCase
{
    private static StormQueries $queries;

    public function testInsert(): void
    {
        $query = self::$queries
            ->insert('insert_test')
            ->setRecord([
                'name' => 'first'
            ]);

        $query->execute();

        $this->assertEquals(1, $query->getLastInsertedId());
    }

    public function testInsertWithOneInvoke(): void
    {
        $id = self::$queries->insert('insert_test', ['name' => 'second'])->execute();

        $this->assertEquals(2, $id);
    }

    public function testInsertMany(): void
    {
        self::$queries->insertMany('insert_test', [
            ['name' => 'name1'],
            ['name' => 'name2'],
            ['name' => 'name3']
        ])->execute();

        $this->assertEquals(5, self::$queries->from('insert_test')->count());
    }

    public static function setUpBeforeClass(): void
    {
        self::$queries = ConnectionProvider::getStormQueries();
    }
}