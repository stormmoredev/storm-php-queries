<?php

namespace integration\queries;

use data\ConnectionProvider;
use PHPUnit\Framework\TestCase;
use Stormmore\Queries\StormQueries;

final class UpdateTest extends TestCase
{
    private static StormQueries $queries;

    public function testUpdate(): void
    {
        self::$queries
            ->update('update_test')
            ->where('id', 1)
            ->setValues(array('name' => 'first-u'))
            ->execute();
        $item = self::$queries->select('*')->from('update_test')->where('id', 1)->find();

        $this->assertEquals('first-u', $item->name);
    }

    public function testQuickUpdate(): void
    {
        self::$queries->update('update_test', 'id = ?', 2, ['name' => 'second-u'])->execute();

        $item = self::$queries->select('*')->from('update_test')->where('id', 2)->find();

        $this->assertEquals('second-u', $item->name);
    }

    public function testWithSet(): void
    {
        self::$queries->update('update_test', 'id = ?', 3)->set('name = ?', '3')->execute();
        $item = self::$queries->select('*')->from('update_test')->where('id', 3)->find();

        $this->assertEquals('3', $item->name);
    }

    public static function setUpBeforeClass(): void
    {
        self::$queries = ConnectionProvider::getStormQueries();
    }
}