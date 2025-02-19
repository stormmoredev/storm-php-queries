<?php

namespace integration\mapper;

use data\ConnectionProvider;
use data\models\Customer;
use data\models\CustomerSimple;
use PHPUnit\Framework\TestCase;
use stdClass;
use Stormmore\Queries\Mapper\Map;
use Stormmore\Queries\StormQueries;

class FromMapTest extends TestCase
 {
    private StormQueries $queries;

    public function testMapSingleRecordWithPropertiesWithWhereOutsideFromClause(): void
    {
        $customer = $this->queries->from('customers', Map::select([
            'customer_id' => 'id',
            'customer_name' => 'name'
        ]))
        ->where('customer_id', 7)
        ->find();

        $this->assertEquals([7, "Blondel père et fils"], [$customer->id, $customer->name]);
    }

    public function testMapRecordsSetWithPropertiesWithWhereOutsideFromClause(): void
    {
        $customers = $this->queries->from('customers', Map::select([
            'customer_id' => 'id',
            'customer_name' => 'name'
        ]))
        ->where('customer_id', '<', 8)
        ->findAll();

        $this->assertCount(7, $customers);
        $this->assertEquals([7, "Blondel père et fils"], [$customers[6]->id, $customers[6]->name]);
    }

    public function setUp(): void
    {
        $this->queries = ConnectionProvider::getStormQueries();
    }
}