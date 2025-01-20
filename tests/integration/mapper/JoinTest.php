<?php

namespace integration\mapper;

use data\ConnectionProvider;
use PHPUnit\Framework\TestCase;
use Storm\Query\Mapper\Map;
use Storm\Query\StormQueries;


/*
 * TODO
 * Map:oneToOne
 * Map:OneToMany
 * Map:manyToMany
 * wyjątek gdy from nie ma mapy a join ma
 * wyjatek gdy from ma mape a join nie ma
 * wyjatki czy sa aliasy (wymagane i prowadza do tabeli)
 */

class JoinTest extends TestCase
{
    private StormQueries $queries;

    public function testDeepJoin(): void
    {
        $customer = $this->queries
            ->from('customers c',  Map::create([
                'customer_id' => 'id',
                'customer_name' => 'name'
            ]))
            ->leftJoin('orders o', 'o.customer_id', 'c.customer_id', Map::many("orders", [
                    'order_id' => 'id'
            ]))
            ->leftJoin('shippers sh', 'sh.shipper_id', 'o.shipper_id', Map::one('shipper', [
                    'shipper_id' => 'id',
                    'shipper_name' => 'name'
            ]))
            ->leftJoin('order_details od', 'od.order_id', 'o.order_id', Map::many('details', [
                'order_detail_id' => 'id',
                'quantity' => 'quantity'
            ]))
            ->leftJoin('products p', 'p.product_id', 'od.product_id', Map::one('product', [
                'product_id' => 'id',
                'product_name' => 'name',
                'price' => 'price'
            ]))
            ->where('c.customer_id', 7)
            ->find();


        $this->assertEquals([7, "Blondel père et fils"], [$customer->id, $customer->name]);
        $this->assertCount(4, $customer->orders);

        $order = array_find($customer->orders, function($customer) {
            return $customer->id == 10436;
        });
        $this->assertEquals("United Package", $order->shipper->name);
        $this->assertCount(4, $order->details);

        $detail = array_find($order->details, function($detail) {
           return $detail->id == 497;
        });
        $this->assertEquals(5, $detail->quantity);
        $this->assertEquals("Spegesild", $detail->product->name);
    }

    public function setUp(): void
    {
        $this->queries = ConnectionProvider::getStormQueries();
    }
}