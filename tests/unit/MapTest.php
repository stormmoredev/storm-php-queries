<?php

namespace unit;

use data\models\Customer;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Stormmore\Queries\Mapper\Map;

class MapTest extends  TestCase
{
    public function testThrowExceptionWhenClassDoesNotExist(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Map::from([
            'user_id' => 'id',
            'user_name' => 'name'
        ], 'NotExistingClass');
    }

    public function testMapFromWithArrayParameters(): void
    {
        $map = Map::from([
            'user_id' => 'id',
            'user_name' => 'name'
        ]);

        $this->assertInstanceOf(Map::class, $map);
        $this->assertEquals(['user_id' => 'id', 'user_name' => 'name'], $map->getColumns());
    }

    public function testMapFromWithClassName(): void
    {
        $map = Map::from(class: Customer::class);

        $this->assertInstanceOf(Map::class, $map);
        $this->assertEquals('data\models\Customer', $map->getClassName());
    }

    public function testMapFromWithClassNameAndParameters(): void
    {
        $map = Map::from([
            'user_id' => 'id'
        ], Customer::class);

        $this->assertInstanceOf(Map::class, $map);
        $this->assertEquals('data\models\Customer', $map->getClassName());
        $this->assertEquals(['user_id' => 'id'], $map->getColumns());
    }

    public function testMapFromWithClassNameKeyAndParameters(): void
    {
        $map = Map::from([
            'user_id' => 'id'
        ], Customer::class, 'user_id');

        $this->assertInstanceOf(Map::class, $map);
        $this->assertEquals('data\models\Customer', $map->getClassName());
        $this->assertEquals(['user_id' => 'id'], $map->getColumns());
        $this->assertEquals('user_id', $map->getId());
    }
}