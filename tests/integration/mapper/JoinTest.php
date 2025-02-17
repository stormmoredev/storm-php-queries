<?php

namespace integration\mapper;

use data\ConnectionProvider;
use data\models\Customer;
use data\models\Details;
use data\models\Order;
use data\models\Product;
use data\models\Shipper;
use data\models\Tag;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Stormmore\Queries\Mapper\Map;
use Stormmore\Queries\StormQueries;


class JoinTest extends TestCase
{
    private StormQueries $queries;

    public function testThrowExceptionWhereFromHasNoAlias(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->queries
            ->from('customers',  Map::select([
                'customer_id' => 'id',
                'customer_name' => 'name'
            ]))
            ->leftJoin('orders o', 'o.customer_id = c.customer_id', Map::many("orders", [
                'order_id' => 'id'
            ]));
    }

    public function testThrowExceptionWhereJoinHasNoAlias(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->queries
            ->from('customers c',  Map::select([
                'customer_id' => 'id',
                'customer_name' => 'name'
            ]))
            ->leftJoin('orders', 'orders.customer_id = c.customer_id', Map::many("orders", [
                'order_id' => 'id'
            ]));
    }

    public function testThrowExceptionWhenFromHasNoMap(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->queries
            ->from('customers c')
            ->leftJoin('orders', 'orders.customer_id = c.customer_id', Map::many("orders", [
                'order_id' => 'id'
            ]));
    }

    public function testManyToManyJoin(): void
    {
        $products = $this->queries
            ->from('products p', Map::select([
                'product_id' => 'id',
                'product_name' => 'name'
            ]))
            ->leftJoin('products_tags pt', 'pt.product_id = p.product_id', Map::join())
            ->leftJoin('tags t', 't.tag_id = pt.tag_id', Map::many("tags", [
                'tag_id' => 'id',
                'name' => 'name'
            ]))
            ->where('p.product_id', 'in', [1,2,3,4])
            ->orderByAsc('p.product_id')
            ->findAll();

        $this->assertCount(4, $products);
        $this->assertCount(3, $products[1]->tags);
        $this->assertCount(1, $products[2]->tags);
        $this->assertEquals("premium", $products[2]->tags[0]->name);
    }

    public function testOneToOneWithoutHierarchicalRelationJoin(): void
    {
        $products = $this->queries
            ->from('products p', Map::select([
                'p.product_id' => 'id',
                'p.product_name' => 'name',
            ]))
            ->leftJoin('categories c', 'c.category_id = p.category_id', Map::select([
                'c.category_name' => 'categoryName'
            ]))
            ->leftJoin('suppliers s', 's.supplier_id = p.supplier_id', Map::select([
                's.supplier_name' => 'supplierName',
            ]))
            ->findAll();

        $this->assertCount(77, $products);
        $product = array_find($products, function($product) {
            return $product->id == 7;
        });
        $this->assertEquals("Uncle Bob's Organic Dried Pears", $product->name);
        $this->assertEquals("Produce", $product->categoryName);
        $this->assertEquals("Grandma Kelly's Homestead", $product->supplierName);
    }

    public function testOneToOneWithMultipleJoinClause(): void
    {
        $products = $this->queries
            ->from('products p', Map::select([
                'p.product_id' => 'id',
                'p.product_name' => 'name',
                'pt.tag_id' => 'tag_id'
            ]))
            ->leftJoin('products_tags pt', 'pt.tag_id = 1 and pt.product_id = p.product_id')
            ->where('pt.tag_id', 'IS NOT NULL')
            ->findAll();

        $this->assertCount(2, $products);
    }

    public function testAllJoinTypesInOneQuery(): void
    {
        $customer = $this->queries
            ->from('customers c',  Map::select([
                'customer_id' => 'id',
                'customer_name' => 'name'
            ]))
            ->leftJoin('orders o', 'o.customer_id = c.customer_id', Map::many("orders", [
                    'order_id' => 'id'
            ]))
            ->leftJoin('shippers sh', 'sh.shipper_id = o.shipper_id', Map::one('shipper', [
                    'shipper_id' => 'id',
                    'shipper_name' => 'name'
            ]))
            ->leftJoin('order_details od', 'od.order_id = o.order_id', Map::many('details', [
                'order_detail_id' => 'id',
                'quantity' => 'quantity'
            ]))
            ->leftJoin('products p', 'p.product_id = od.product_id', Map::one('product', [
                'product_id' => 'id',
                'product_name' => 'name',
                'price' => 'price'
            ]))
            ->leftJoin('products_tags pt', 'pt.product_id = p.product_id', Map::join())
            ->leftJoin('tags t', 't.tag_id = pt.tag_id', Map::many("tags", [
                'tag_id' => 'id',
                'name' => 'name'
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
        $this->assertCount(1, $detail->product->tags);
        $this->assertEquals("cheap", $detail->product->tags[0]->name);
    }

    public function testAllJoinTypesInOneQueryWithTypes(): void
    {
        $customer = $this->queries
            ->from('customers c',  Map::select([
                'customer_id' => 'id',
                'customer_name' => 'name'
            ], Customer::class))
            ->leftJoin('orders o', 'o.customer_id = c.customer_id', Map::many("orders", [
                'order_id' => 'id'
            ], Order::class))
            ->leftJoin('shippers sh', 'sh.shipper_id = o.shipper_id', Map::one('shipper', [
                'shipper_id' => 'id',
                'shipper_name' => 'name'
            ], Shipper::class))
            ->leftJoin('order_details od', 'od.order_id = o.order_id', Map::many('details', [
                'order_detail_id' => 'id',
                'quantity' => 'quantity'
            ], Details::class))
            ->leftJoin('products p', 'p.product_id = od.product_id', Map::one('product', [
                'product_id' => 'id',
                'product_name' => 'name',
                'price' => 'price'
            ], Product::class))
            ->leftJoin('products_tags pt', 'pt.product_id = p.product_id', Map::join())
            ->leftJoin('tags t', 't.tag_id = pt.tag_id', Map::many("tags", [
                'tag_id' => 'id',
                'name' => 'name'
            ], Tag::class))
            ->where('c.customer_id', 7)
            ->find();

        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertEquals([7, "Blondel père et fils"], [$customer->id, $customer->name]);
        $this->assertCount(4, $customer->orders);

        $order = array_find($customer->orders, function($customer) {
            return $customer->id == 10436;
        });
        $this->assertInstanceOf(Order::class, $order);
        $this->assertInstanceOf(Shipper::class, $order->shipper);
        $this->assertEquals("United Package", $order->shipper->name);
        $this->assertCount(4, $order->details);

        $detail = array_find($order->details, function($detail) {
            return $detail->id == 497;
        });
        $this->assertInstanceOf(Details::class, $detail);
        $this->assertEquals(5, $detail->quantity);
        $this->assertInstanceOf(Product::class, $detail->product);
        $this->assertEquals("Spegesild", $detail->product->name);
        $this->assertCount(1, $detail->product->tags);
        $this->assertInstanceOf(Tag::class, $detail->product->tags[0]);
        $this->assertEquals("cheap", $detail->product->tags[0]->name);;
    }

    public function setUp(): void
    {
        $this->queries = ConnectionProvider::getStormQueries();
    }
}