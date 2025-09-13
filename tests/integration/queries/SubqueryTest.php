<?php

namespace integration\queries;

use data\ConnectionProvider;
use PHPUnit\Framework\TestCase;
use Stormmore\Queries\Mapper\Map;
use Stormmore\Queries\Queries\SubQuery;

class SubqueryTest extends TestCase
{
    public function testWhereSubquery(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $items = $queries
            ->select("products")
            ->where("category_id", 1)
            ->where('price', '<=',
                $queries
                    ->select("products", "avg(price)")
                    ->where("category_id", 1)
            )
            ->findAll();

        $this->assertCount(10, $items);
    }

    public function testFromSubquery(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $items = $queries
            ->select(SubQuery::create($queries->select('products'), 'p'))
            ->orderByAsc('p.product_id')
            ->findAll();

        $this->assertCount(77, $items);
        $this->assertEquals("Chais", $items[0]->product_name);
        $this->assertEquals("Original Frankfurter grüne Soße", $items[76]->product_name);
    }

    public function testFromSubqueryWithJoin(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $items = $queries
            ->select(SubQuery::create($queries->select('products'), 'p'))
            ->leftJoin('suppliers s', 's.supplier_id = p.supplier_id')
            ->orderByAsc('p.product_id')
            ->findAll();

        $this->assertCount(77, $items);
        $this->assertEquals("Chais", $items[0]->product_name);
        $this->assertEquals("Exotic Liquid", $items[0]->supplier_name);
        $this->assertEquals("Original Frankfurter grüne Soße", $items[76]->product_name);
        $this->assertEquals("Plutzer Lebensmittelgroßmärkte AG", $items[76]->supplier_name);
    }

    public function testFromSubqueryWithSubqueryJoin(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $items = $queries
            ->select(SubQuery::create($queries->select('products'), 'p'))
            ->leftJoin(SubQuery::create($queries->select('suppliers'), 's'), 's.supplier_id = p.supplier_id')
            ->orderByAsc('p.product_id')
            ->findAll();

        $this->assertCount(77, $items);
        $this->assertEquals("Chais", $items[0]->product_name);
        $this->assertEquals("Exotic Liquid", $items[0]->supplier_name);
        $this->assertEquals("Original Frankfurter grüne Soße", $items[76]->product_name);
        $this->assertEquals("Plutzer Lebensmittelgroßmärkte AG", $items[76]->supplier_name);
    }

    public function testFromWithSubqueryJoin(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $items = $queries
            ->select('products p')
            ->leftJoin(SubQuery::create($queries->select('suppliers'), 's'), 's.supplier_id = p.supplier_id')
            ->orderByAsc('p.product_id')
            ->findAll();

        $this->assertCount(77, $items);
        $this->assertEquals("Chais", $items[0]->product_name);
        $this->assertEquals("Exotic Liquid", $items[0]->supplier_name);
        $this->assertEquals("Original Frankfurter grüne Soße", $items[76]->product_name);
        $this->assertEquals("Plutzer Lebensmittelgroßmärkte AG", $items[76]->supplier_name);
    }

    public function testFromSubqueryWithMap(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $items = $queries
            ->select(SubQuery::create($queries->select('products'), 'p'), Map::select([
                'product_id' => 'id',
                'product_name' => 'name'
            ]))
            ->findAll();

        $this->assertCount(77, $items);
        $this->assertEquals("Chais", $items[0]->name);
    }

    public function testLeftJoinSubqueryWithMap(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $items = $queries
            ->select(SubQuery::create($queries->select('orders'), 'o'), Map::select([
                'order_id' => 'id',
                'order_date' => 'date'
            ]))
            ->leftJoin('order_details od', 'od.order_id = o.order_id', Map::many('details', [
                'order_detail_id' => 'id',
                'quantity' => 'quantity'
            ]))
            ->orderByAsc('od.order_detail_id')
            ->findAll();

        $order = array_find($items, function($order) {
            return $order->id == 10410;
        });

        $this->assertCount(196, $items);
        $this->assertCount(2, $order->details);
        $this->assertEquals(49, $order->details[0]->quantity);
        $this->assertEquals(16, $order->details[1]->quantity);
    }

    public function testFromSubqueryWithSubqueryJoinAndMap(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $items = $queries
            ->select(SubQuery::create($queries->select('orders'), 'o'), Map::select([
                'order_id' => 'id',
                'order_date' => 'date'
            ]))
            ->leftJoin(SubQuery::create($queries->select('order_details'), 'od'), 'od.order_id = o.order_id', Map::many('details', [
                'order_detail_id' => 'id',
                'quantity' => 'quantity'
            ]))
            ->orderByAsc('od.order_detail_id')
            ->findAll();

        $order = array_find($items, function($order) {
            return $order->id == 10410;
        });

        $this->assertCount(196, $items);
        $this->assertCount(2, $order->details);
        $this->assertEquals(49, $order->details[0]->quantity);
        $this->assertEquals(16, $order->details[1]->quantity);
    }
}