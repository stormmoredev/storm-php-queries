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
        $query = self::$queries
            ->update('update_test')
            ->where('id', 1)
            ->setValues([
                'name' => 'first-u'
            ]);
        $query->execute();
        $item = self::$queries->select('*')->from('update_test')->where('id', 1)->find();

        $this->assertEquals('first-u', $item->name);
    }

    public function testUpdateWithOneInvoke(): void
    {
        self::$queries->update('update_test', ['name' => 'second-u'])->where('id', 2)->execute();

        $item = self::$queries->select('*')->from('update_test')->where('id', 2)->find();

        $this->assertEquals('second-u', $item->name);
    }

    public static function setUpBeforeClass(): void
    {
        self::$queries = ConnectionProvider::getStormQueries();
    }
}