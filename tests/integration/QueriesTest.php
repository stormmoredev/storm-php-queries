<?php

namespace integration;

use data\ConnectionProvider;
use PHPUnit\Framework\TestCase;
use Stormmore\Queries\Mapper\Map;
use Stormmore\Queries\StormQueries;

class QueriesTest extends TestCase
{
    private StormQueries $queries;

    public function testFind(): void
    {
        $customer = $this->queries->find('customers', 'customer_id = ?', 7);

        $this->assertEquals(7, $customer->customer_id);
        $this->assertEquals('Blondel père et fils', $customer->customer_name);
    }

    public function testFindMap(): void
    {
        $customer = $this->queries->find('customers', 'customer_id = ?', 7, Map::select([
            'customer_id' => 'id',
            'customer_name' => 'name'
        ]));

        $this->assertEquals(7, $customer->id);
        $this->assertEquals('Blondel père et fils', $customer->name);
    }

    public function testFindAll(): void
    {
        $customers = $this->queries->findAll('customers', 'country = ? and customer_name LIKE ?', 'France', '%La%');

        $this->assertCount(2, $customers);
        $this->assertEquals(40, $customers[0]->customer_id);
        $this->assertEquals("La corne d'abondance", $customers[0]->customer_name);
    }

    public function testFindAllMap(): void
    {
        $customers = $this->queries->findAll('customers', 'country = ? and customer_name LIKE ?', 'France', '%La%', Map::select([
            'customer_id' => 'id',
            'customer_name' => 'name'
        ]));

        $this->assertCount(2, $customers);
        $this->assertEquals(40, $customers[0]->id);
        $this->assertEquals("La corne d'abondance", $customers[0]->name);
    }

    public function setUp(): void
    {
        $this->queries = ConnectionProvider::getStormQueries();
    }
}