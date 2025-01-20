# Storm PHP Queries

It's mix of query builder and ORM made to fetch data in most effective way.\
Intuitive, easy to learn light yet powerful tool.
If you work with ORMs you will notice that you don't have to configure nothing but still you 
have possilbity to build easilly queries and retrieve in hierarchical manner. 

Most interesting features
- hierarchical models (biggest advantage of ORM without disadvantages of over-configuration etc.)
- working with existing schema
- no need to change anything in your models (keep DDD aggregates clean)
- query builder supporting assembling queries (support criteria-finder pattern for modern architectures)
- handle sub queries
- no configuration needed
- fluent api
- short version of api for simple queries 
- supports various databases(tested with PostgreSql, MySql, MariaDB, MSSQL, SQLite)
- lightweight and tidy code (no additional dependencies)
- efficiency - it's really thin layer over simple query 
- developer friendly (profiling queries, overview of generated sql)
- intuitive and flexible. You can use it as query builder and assembly queries or use simple string conditions

<a name="index"></a>
* [1. Quick start](#quickstart)
* [2. Select query](#selectquery)
  * [Aggregation functions](#aggregatefunctions)
  * [Left join](#leftjoin)
  * [Where](#where)
  * [Sub query](#where)
  * [OrderBy](#orderby)
  * [GroupBy](#groupby)
  * [Having](#having)
  * [Mapping data to hierarchical model](#mapping)
  * [Assembling query and critera-finder pattern](#assemblingqueries)
* [3. ORM](#orm)
* [3. Insert query](#insertquery)
* [4. Update query](#updatequery)
* [5. Delete query](#deletequery)
* [5. Profiling nad logging queries](#profiling)
* [6. Notice](#notice)
* [7. Tests](#btests)
* [8. Examples](#examples)
* [9. Author](#author)
* [10. License ](#license)

<a name="quickstart"></a>
## Quick start

### Installation

You will need composer or use Storm PHP Framework (Storm PHP Queries are part of it)

```php
composer require stormmore/queries
```

### Establishing connection
Storm Queries use from PDO object
```php
use Storm\Query\ConnectionFactory;
use Storm\Query\StormQueries;

$connection = ConnectionFactory::createFromstring("dsn", "user", "password");
$queries = new StormQueries($connection);
```

### Full working example

```php
use Storm\Query\StormQueries;
use Storm\Query\Mapper\Map;
use Storm\Query\ConnectionFactory;

$connection = ConnectionFactory::createFromString(
    "mysql:host=localhost;port=7801;dbname=storm_test", "mysql", "mysql")
$queries = new StormQueries($connection);

$queries
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
     
foreach($customers as $customer) {
    print_customer($customer);
    foreach($customer->orders as $order) {
      print_order($order);  
      foreach($order->details as $detail) {
        print_detail($detail);
      }    
    }
}                   
```

<a name="block2"></a>
## Select query [↑](#index_block)

Build select query with `from` method

```php
$stormQueries->from('tableName', 'id = ?', 2)->findOne();
```
Build it with fluent API
```php
$stormQueries
    ->select("column1", "column2", "column3")
    ->from('tableName')
    ->where('id', 2);
    ->findOne();
```

StormQueries is made to handle assembling queries step by step in various scenarios so every time you invoke methods like\
`select`
`leftJoin`
`leftOuterJoin`
`where`
`orWhere`
`having`
`orHaving`
`orderBy`
`orderByAsc`
`orderByDesc`\
they add parameters instead replacing them to final query

```php
$stormQueries->select('columnA')->select('columnB')->select('columnC');
```
builds `SELECT columnA, columnB, columnC`

### Short select [↑](#index)
```php
$stormQueries->from('customers')->find();
$stormQueries->from('customers', 'name LIKE ? AND city = ?', 'M%' 'London')->find();
$stormQueries->from('customers', 'id = ?', 14]->findOne();
$stormQueries->from('customers')->where('id', 14)->findOne();
```

### Aggregation functions [↑](#index)

Do not use them in sub queries because they immediately fetch data from db.
 ```php
$stormQueries->from('products', 'category_id = ?', 7)->count();
$stormQueries->from('products', 'category_id = ?', 7)->min('price');
$stormQueries->from('products', 'category_id = ?', 7)->max('price');
$stormQueries->from('products', 'category_id = ?', 7)->sum('price');
$stormQueries->from('products', 'category_id = ?', 7)->avg('price');
 ```

### Left join [↑](#index)
```php
$stormQueries
    ->from('tableA')
    ->leftJoin('tableB', 'tableB.id', 'tableA.id')
    ->leftOuterJoin('tableC', 'tableC.id', 'tableA.id')
    ->find();
```

### Where [↑](#index)
```php
$stormQueries
    ->from('tableA')
    ->where('column', 'val1')
    ->where('column', '=', 'val1')
    ->where('column', 'IN', ['val2', 'val3'])
    ->where('column', 'LIKE', '%a%')
    ->where('column', '<>', 15)
    ->where('column', 'BETWEEN', 5, 10)
    ->where('column', '>', 1)
    ->where('column', '>=', 1)
    ->where('column', '<', 1)
    ->where('column', '<=', 1)
    ->where('column', 'IS NULL')
    ->where('column', 'IS NOT NULL')
```
Default conjunction is `AND`. To use `OR` use `orWhere`

```php
$stormQueries
    ->from('tableA')
    ->where('columnA', 'val1')
    ->orWhere('column', 'IN', ['val2', 'val3'])
```

#### Nested conditions
If you want to use group of conditions enclosed in parenthesis use closure
```php
$stormQueries
    ->from('tableA')
    ->where('columnA', 'val1')
    ->where(function($query) {
        $query->where('column', 'val2')->orWhere('column', 'val3')
    });
```

### Sub query [↑](#index)

You can pass select query as parameter of where making it sub query.
```php
$stormQueries
    ->select('*')
    ->from('users')
    ->where('fieldA', '=', 'val1')
    ->where('field4', 'IN', $this->queries->select('id')->from('table')->where('col', 14))
    ->where('field5', '=', 9);
```

### OrderBy [↑](#index)
```php
$stormQueries->from('table')->orderByDesc('column1');
$stormQueries->from('table')->orderByAsc('column1');
$stormQueries->from('table')->orderBy('column1', -1); //descending 
$stormQueries->from('table')->orderBy('column1', 1) //ascending
$stormQueries->from('table')->orderByDesc('column1')->orderByDesc('column2');
```
### GroupBy [↑](#index)
```php
$stormQueries->from('table')->groupBy('column1', 'column2');
$stormQueries->from('table')->groupBy('column1')->groupBy('column2')->groupBy('column3')
```
### Having [↑](#index)
Everything what refers to `where` or `orWhere` refers to `having` and `orHaving`
```php
$stormQueries
    ->select('country, city, count(*)')
    ->from('customers')
    ->groupBy('country, city')
    ->having('count(*)', '>', 1)
    ->having('city', 'LIKE', '%o%')
    ->find();
```

## Mapping data to hierarchical structure [↑](#index)

In my opinion most interesting part of StormQueries is mapping which allows to work with hierarchical data without any configuration 
(json, xml, attributes in classes). 
Only advantages ORMs over query builder I found,  was working with hierarchical models with (in my opinion, heavy) disadvantages of classic solution.\
This is the reason I decided to build StomQueries library. 

Concept is about creating hierarchical map where you define
- class you want to map data
- key to differentiate object id (it have not to be db primary ey)
- mapping columns to object property

and pass it to `find`/`findOne` method.

In case of e-commerce platform where we would like to fetch some customers with all their orders we could define map like:
```php
$map = Map::create(Customer::class, 'customer_id', [
    'customer_id' => 'id',
    'customer_name' => 'name',
    'address' => 'address',
    'city' => 'city',
    'postal_code' => 'postalCode',
    'country' => 'country'
])->hasMany('orders',
    Map::create(Order::class, 'order_id', [
        'order_id' => 'id',
        'order_date' => 'date',
    ])
    ->hasMany('products',
        Map::create(Product::class, 'product_id', [
            'product_id' => 'id',
            'product_name' => 'name',
            'quantity' => 'quantity'])
    )
    ->hasOne('shipper',
        Map::create(Shipper::class, 'shipper_id', [
            'shipper_id' => 'id',
            'shipper_name' => 'name'])
    )
);

$customers = $stormQueries
    ->select(
        'c.customer_id', 'c.customer_name', 'c.address', 'c.city', 'c.postal_code', 'c.country',
        'o.employee_id', 'o.order_id', 'o.order_date',
        's.shipper_name', 's.shipper_id',
        'od.quantity', 'od.product_id',
        'p.product_name')
    ->from('customers c')
    ->leftJoin('orders o', 'o.customer_id', 'c.customer_id')
    ->leftJoin('shippers s', 's.shipper_id', 'o.shipper_id')
    ->leftJoin('order_details od', 'od.order_id', 'o.order_id')
    ->leftJoin('products p', 'p.product_id', 'od.product_id')
    ->orderByAsc('c.customer_id')
    ->find($map);

foreach($customers as $customer) {
    print_customer($customer);
    foreach($customer->orders as $order) {
        print_order($order)
        foreach($order->products as $product) {
            print_order_products($product);
        }
    }
}
```

Code above is taken from integration test in `test/integration/MapperTest.php`

## Assembling query and critera-finder pattern [↑](#index)

```php 
function find_products($criteria): array
{
    $stormQueries = get_storm_queries();
    $map = get_product_map();
       
    $query = $stormQueries
        ->from('products')
        ->leftJoin('product_photos', 'product_photos.product_id', 'products.id')
        ->where('is_in_sale', true);
    if ($criteria->hasCategory()) {
        $query->where('category_id', $criteria->getCategoryId());
    }
    if ($criteria->hasOrder()) {
        $query->orderBy($criteria->getOrderField(), $criteria->getOrderDirection());
    }
    if ($criteria->hasSearchPhrase()) {
        $query->where('description', "LIKE", '%' . $criteria->getPhrase() . '%');
    }
    
    return $query->find($map);
}

$criteria = ProductCriteria::create()
    ->withCategoryId($request->get('category-id'))
    ->phrase($request->get('search-phrase'))
    ->orderBy($request->get('order-field'), $request->get('order-direction'))

$products = find_products($stormQueries, $criteria);
```

## ORM
opisac ze potrzeba bytu ktory pomapuje dane z zapytania do modelu hierarhicznego tam by stormqueries to wiedzial.
Dodac dodawanie dla tabela. 
- ZAprojektowac tak by w mapie byly tabela.columna 
- dodac mozliwosc dodania prostych mapperow do 

<a name="insertquery"></a>
## Insert query [↑](#index)

```php
$id = $stormQueries->insert('person', ['name' => 'Micheal'])->execute();
```
If you don't want to invoke `getLastInsertedId` on `PDO` pass `false` to `execute` method
```php
$stormQueries->insert('person', ['name' => 'Micheal'])->execute(false);
```
## Update query [↑](#index)
```php
$stormQueries->update('person', ['name' => 'Matthew'])->where('id', 2)->execute();
```
## Delete query [↑](#index)
```php
$stormQueries->delete('person')->where('id', 1)->execute();
$stormQueries->delete('person', 'id = ?', 1)->execute();
```
## Profiling nad logging queries [↑](#index)

To track what queries are going to database add `callback` to `IConnection` object.  
```php
$connection = ConnectionFactory::createFromString("...", "...", "...")

$connection->onSuccess(function(string $sql, DateInterval $interval) {
    //log here
});

$connection->onFailre(function(string $sql, DateInterval $interval, Exception $e) {
    //log here
})
```
## Notice [↑](#index)
StormQueries uses PDO and databases which support it.\
Tested with PostgreSQL, MySQL, MariaDB, SqlServer and Sqlite.
## Tests [↑](#index)

To run tests use `docker composer up` and run one of `run.*.cmd`

## Examples [↑](#index)

If you are looking for more use cases take a look at tests in `test` directory of project.

## Author [↑](#index)

Michał Czerski

If you have any question or ideas you want share with me contact me on GitHub.

## License [↑](#index)

StormQueries is licensed under MIT licence.