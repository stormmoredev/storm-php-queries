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

    public function testMapSingleRecordWithProperties(): void
    {
        $customer = $this->queries->from('customers', 'customer_id = ?', 7, Map::select([
            'customer_id' => 'id',
            'customer_name' => 'name'
        ]))
        ->find();

        $this->assertEquals([7, "Blondel père et fils"], [$customer->id, $customer->name]);
    }

    public function testIndexedArrayMixedWithAssociativeArrayAsMap(): void
    {
        $customer = $this->queries->from('customers', 'customer_id = ?', 7, Map::select([
            'customer_id' => 'id',
            'customer_name' => 'name',
            'city',
            'country'
        ]))
        ->find();

        $this->assertEquals(
            [7, "Blondel père et fils", "Strasbourg", "France"],
            [$customer->id, $customer->name, $customer->city, $customer->country]);
    }

    public function testMapSingleRecordToUserClass(): void
    {
        $customer = $this->queries->from('customers', 'customer_id = ?', 7, Map::select(class: CustomerSimple::class))->find();

        $this->assertInstanceOf(CustomerSimple::class, $customer);
        $this->assertEquals([7, "Blondel père et fils"], [$customer->customer_id, $customer->customer_name]);
    }

    public function testMapSingleRecordToStdClass(): void
    {
        $customer = $this->queries->from('customers', 'customer_id = ?', 7, Map::select())->find();

        $this->assertInstanceOf(stdClass::class, $customer);
        $this->assertEquals([7, "Blondel père et fils", '67000'], [$customer->customer_id, $customer->customer_name, $customer->postal_code]);
    }

    public function testMapSingleRecordToUserClassWithProperties(): void
    {
        $customer = $this->queries->from('customers', 'customer_id = ?', 7, Map::select([
            'customer_id' => 'id',
            'customer_name' => 'name'
        ], Customer::class))
        ->find();

        $this->assertEquals([7, "Blondel père et fils"], [$customer->id, $customer->name]);
    }

    public function testMapSingleRecordToUserClassWithKeyAndProperties(): void
    {
        $customer = $this->queries->from('customers', 'customer_id = ?', 7, Map::select([
            'customer_id' => 'id',
            'customer_name' => 'name'
        ], Customer::class, 'customer_id'))
        ->find();

        $this->assertInstanceOf(Customer::class, $customer);
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

    public function testMapRecordsSetWithProperties(): void
    {
        $customers = $this->queries->from('customers', 'customer_id < ?', 8, Map::select([
            'customer_id' => 'id',
            'customer_name' => 'name'
        ]))
        ->findAll();

        $this->assertCount(7, $customers);
        $this->assertEquals([7, "Blondel père et fils"], [$customers[6]->id, $customers[6]->name]);
    }

    public function testRecordsSetToUserClass(): void
    {
        $customers = $this->queries->from('customers', 'customer_id < ?', 8, Map::select(class: CustomerSimple::class))->findAll();

        $this->assertCount(7, $customers);
        $this->assertInstanceOf(CustomerSimple::class, $customers[0]);
        $this->assertEquals([7, "Blondel père et fils"], [$customers[6]->customer_id, $customers[6]->customer_name]);
    }

    public function testMapRecordsSetToUserClassWithProperties(): void
    {
        $customers = $this->queries->from('customers', 'customer_id < ?', 8, Map::select([
            'customer_id' => 'id',
            'customer_name' => 'name'
        ], Customer::class))
        ->findAll();

        $this->assertCount(7, $customers);
        $this->assertInstanceOf(Customer::class, $customers[0]);
        $this->assertEquals([7, "Blondel père et fils"], [$customers[6]->id, $customers[6]->name]);
    }

    public function testRecordsSetToUserClassWithKeyAndProperties(): void
    {
        $customers = $this->queries->from('customers', 'customer_id < ?', 8, Map::select([
            'customer_id' => 'id',
            'customer_name' => 'name'
        ], Customer::class, 'customer_id'))
        ->findAll();

        $this->assertCount(7, $customers);
        $this->assertInstanceOf(Customer::class, $customers[0]);
        $this->assertEquals([7, "Blondel père et fils"], [$customers[6]->id, $customers[6]->name]);
    }

    public function setUp(): void
    {
        $this->queries = ConnectionProvider::getStormQueries();
    }
}