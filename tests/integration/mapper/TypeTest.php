<?php

namespace integration\mapper;

use data\ConnectionProvider;
use data\models\mapperTypeTest\DetailWithoutProps;
use data\models\mapperTypeTest\DetailWithProps;
use data\models\mapperTypeTest\DetailWithTypedInitProps;
use data\models\mapperTypeTest\DetailWithTypedProps;
use data\models\mapperTypeTest\OrderWithoutProps;
use data\models\mapperTypeTest\OrderWithProps;
use data\models\mapperTypeTest\OrderWithTypedInitProps;
use data\models\mapperTypeTest\OrderWithTypedProps;
use data\models\mapperTypeTest\ProductWithoutProps;
use data\models\mapperTypeTest\ProductWithProps;
use data\models\mapperTypeTest\ProductWithTypedInitProps;
use data\models\mapperTypeTest\ProductWithTypedProps;
use data\models\mapperTypeTest\ShipperWithoutProps;
use data\models\mapperTypeTest\ShipperWithProps;
use data\models\mapperTypeTest\ShipperWithTypedInitProps;
use data\models\mapperTypeTest\ShipperWithTypedProps;
use DateTime;
use PHPUnit\Framework\TestCase;
use stdClass;
use Stormmore\Queries\Mapper\Map;
use Stormmore\Queries\StormQueries;

final class TypeTest extends TestCase
{
    private StormQueries $queries;

    public function testMappingToStdClass(): void
    {
        $order = $this->getOrder(stdClass::class, stdClass::class, stdClass::class, stdClass::class);

        $this->assertInstanceOf(stdClass::class, $order);
        $this->assertInstanceOf(stdClass::class, $order->details[0]);
        $this->assertInstanceOf(stdClass::class, $order->details[0]->product);
        $this->assertInstanceOf(stdClass::class, $order->shipper);
        $this->assertEquals(10389, $order->id);
        $this->assertEquals('1996-12-20', $order->date);
        $this->assertCount(4, $order->details);
        $this->assertEquals(10, $order->details[0]->product->id);
        $this->assertEquals('Ikura', $order->details[0]->product->name);
        $this->assertEquals(16, $order->details[0]->quantity);
        $this->assertEquals(2, $order->shipper->id);
        $this->assertEquals('United Package', $order->shipper->name);
    }

    public function testMappingToUserClassWithoutProperties(): void
    {
        $order = $this->getOrder(OrderWithoutProps::class, DetailWithoutProps::class, ProductWithoutProps::class, ShipperWithoutProps::class);

        $this->assertInstanceOf(OrderWithoutProps::class, $order);
        $this->assertInstanceOf(DetailWithoutProps::class, $order->details[0]);
        $this->assertInstanceOf(ProductWithoutProps::class, $order->details[0]->product);
        $this->assertInstanceOf(ShipperWithoutProps::class, $order->shipper);
        $this->assertEquals(10389, $order->id);
        $this->assertEquals('1996-12-20', $order->date);
        $this->assertCount(4, $order->details);
        $this->assertEquals(10, $order->details[0]->product->id);
        $this->assertEquals('Ikura', $order->details[0]->product->name);
        $this->assertEquals(16, $order->details[0]->quantity);
        $this->assertEquals(2, $order->shipper->id);
        $this->assertEquals('United Package', $order->shipper->name);
    }


    public function testMappingToUserClassWithProperties(): void
    {
        $order = $this->getOrder(OrderWithProps::class, DetailWithProps::class, ProductWithProps::class, ShipperWithProps::class);

        $this->assertInstanceOf(OrderWithProps::class, $order);
        $this->assertInstanceOf(DetailWithProps::class, $order->details[0]);
        $this->assertInstanceOf(ProductWithProps::class, $order->details[0]->product);
        $this->assertInstanceOf(ShipperWithProps::class, $order->shipper);
        $this->assertEquals(10389, $order->id);
        $this->assertEquals('1996-12-20', $order->date);
        $this->assertCount(4, $order->details);
        $this->assertEquals(10, $order->details[0]->product->id);
        $this->assertEquals('Ikura', $order->details[0]->product->name);
        $this->assertEquals(16, $order->details[0]->quantity);
        $this->assertEquals(2, $order->shipper->id);
        $this->assertEquals('United Package', $order->shipper->name);
    }

    public function testMappingToUserClassWithTypedProperties(): void
    {
        $order = $this->getOrder(OrderWithTypedProps::class, DetailWithTypedProps::class,  ProductWithTypedProps::class, ShipperWithTypedProps::class);

        $this->assertInstanceOf(OrderWithTypedProps::class, $order);
        $this->assertInstanceOf(DetailWithTypedProps::class, $order->details[0]);
        $this->assertInstanceOf(ProductWithTypedProps::class, $order->details[0]->product);
        $this->assertInstanceOf(ShipperWithTypedProps::class, $order->shipper);
        $this->assertEquals(10389, $order->id);
        $this->assertEquals(new DateTime('1996-12-20'), $order->date);
        $this->assertCount(4, $order->details);
        $this->assertEquals(10, $order->details[0]->product->id);
        $this->assertEquals('Ikura', $order->details[0]->product->name);
        $this->assertEquals(16, $order->details[0]->quantity);
        $this->assertEquals(2, $order->shipper->id);
        $this->assertEquals('United Package', $order->shipper->name);
    }

    public function testMappingToUserClassWithTypedAndInitializedProperties(): void
    {
        $order = $this->getOrder(OrderWithTypedInitProps::class, DetailWithTypedInitProps::class, ProductWithTypedInitProps::class, ShipperWithTypedInitProps::class);

        $this->assertInstanceOf(OrderWithTypedInitProps::class, $order);
        $this->assertInstanceOf(DetailWithTypedInitProps::class, $order->details[0]);
        $this->assertInstanceOf(ProductWithTypedInitProps::class, $order->details[0]->product);
        $this->assertInstanceOf(ShipperWithTypedInitProps::class, $order->shipper);
        $this->assertEquals(10389, $order->id);
        $this->assertEquals(new DateTime('1996-12-20'), $order->date);
        $this->assertCount(4, $order->details);
        $this->assertEquals(10, $order->details[0]->product->id);
        $this->assertEquals('Ikura', $order->details[0]->product->name);
        $this->assertEquals(16, $order->details[0]->quantity);
        $this->assertEquals(2, $order->shipper->id);
        $this->assertEquals('United Package', $order->shipper->name);
    }

    private function getOrder(string $orderClassName, string $detailsClassName, string $productClassName, string $shipperClassName): mixed
    {
        return ConnectionProvider::getStormQueries()
            ->select('orders o', Map::select([
                'order_id' => 'id',
                'order_date' => 'date'
            ], $orderClassName))
            ->leftJoin('shippers sh', 'sh.shipper_id = o.shipper_id', Map::one('shipper', [
                'shipper_id' => 'id',
                'shipper_name' => 'name'
            ], $shipperClassName))
            ->leftJoin('order_details od', 'od.order_id = o.order_id', Map::many('details', [
                'order_detail_id' => 'id',
                'quantity' => 'quantity'
            ], $detailsClassName))
            ->leftJoin('products p', 'p.product_id = od.product_id', Map::one('product', [
                'product_id' => 'id',
                'product_name' => 'name',
                'price' => 'price'
            ], $productClassName))
            ->where('o.order_id', 10389)
            ->orderByAsc('p.product_id')
            ->find();
    }
}