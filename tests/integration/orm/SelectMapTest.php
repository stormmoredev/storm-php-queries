<?php

namespace integration\orm;

use data\ConnectionProvider;
use data\models\Customer;
use data\models\CustomerSimple;
use PHPUnit\Framework\TestCase;
use stdClass;
use Stormmore\Queries\Mapper\Map;
use Stormmore\Queries\StormQueries;

class SelectMapTest extends TestCase
 {
    public function testMapFind(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $customer = $queries->select('customers', Map::select([
            'customer_id' => 'id',
            'customer_name' => 'name'
        ]))
        ->where('customer_id', 7)
        ->find();

        $this->assertEquals([7, "Blondel père et fils"], [$customer->id, $customer->name]);
    }

    public function testMapFindAll(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $customers = $queries->select('customers', Map::select([
            'customer_id' => 'id',
            'customer_name' => 'name'
        ]))
        ->where('customer_id', '<', 8)
        ->findAll();

        $this->assertCount(7, $customers);
        $this->assertEquals([7, "Blondel père et fils"], [$customers[6]->id, $customers[6]->name]);
    }
}