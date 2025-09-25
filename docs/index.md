
StormPHP Queries is a combination of a **query builder** and an **Object-Relational Mapping (ORM)** library designed to fetch data effectively.
It’s **intuitive, lightweight, and flexible** — with zero configuration required.

- Hierarchical models (ORM advantages without over-configuration)
- Fluent query builder (supports criteria-finder pattern for modern architectures)
- Subqueries
- No need to describe the DB schema
- Lightweight, tidy code (no extra dependencies)
- Supports multiple databases (tested with PostgreSQL, MySQL, MariaDB, MSSQL, SQLite)
- Fluent API
- Keep your domain models clean (DDD-friendly)
- Developer-friendly (profiling queries, inspecting generated SQL)

---

## Table of Contents
1. [Quick Start](#quick-start)
2. [Basic Queries](#basic-queries)
3. [Select Query](#select-query)
- [Aggregation Functions](#aggregation-functions)
- [Join / Left Join](#join-left-join)
- [Where](#where)
- [OrderBy](#orderby)
- [GroupBy](#groupby)
- [Having](#having)
4. [ORM](#orm)
5. [Subqueries](#subqueries)
6. [Assembling Queries](#assembling-queries)
7. [Insert](#insert)
8. [Update](#update)
9. [Delete](#delete)
10. [Union](#union)
11. [Transactions](#transactions)
12. [RawQueries](#rawqueries)
13. [Profiling and Logging](#profiling-and-logging)
14. [Notice](#notice)
15. [Tests](#tests)
16. [Examples](#examples)
17. [Author](#author)
18. [License](#license)

---

## Quick Start

### Installation
```bash
composer require stormmore/queries
````

### Establishing a Connection

StormPHP Queries uses PDO:

```php
use Stormmore\Queries\ConnectionFactory;
use Stormmore\Queries\StormQueries;

$connection = ConnectionFactory::createFromString("dsn", "user", "password");
$queries = new StormQueries($connection);
```

---

## Basic Queries

Find product by ID:

```php
$product = $queries->find('products', ['id' => 5]);
```

Find all products in a category:

```php
$products = $queries->findAll('products', ['category_id' => 10]);
```

Find products by complex criteria
```php
$queries->findAll('customers', 'country = ? and customer_name LIKE ?', 'France', '%La%');
```

Find products and map
```php
$products = $queries->find('products', ['category_id' => 7], Map::select([
    'product_it' => 'id',
    'product_name' => 'name'
], Product::class));
```
Insert:

```php
$id = $queries->insert('products', [
'name' => 'Golden watch',
'price' => 756
]);
```

Update:

```php
$queries->update('products', ['id' => $id], ['name' => 'Renamed product']);
```

Delete:

```php
$queries->delete('products', ['id' => $id]);
```

Count:

```php
$count = $queries->count('products', ['in_sale' => true]);
```

Existence check:

```php
$exists = $queries->exist('products', ['id' => 5]);
```

Map to class:

```php
use Stormmore\Queries\Mapper\Map;

$product = $queries->find('products', ['id' => 5], Map::select([
'product_id'   => 'id',
'product_name' => 'name'
], UserProduct::class));
```

---

## Select Query

Build a query step by step with a fluent API:

```php
$queries
->select("table", ["column1", "column2"])
->where('id', 2)
->find();
```

Using aliases
```php
$queries
->select("table", ["column1" => 'colA', "column2"])
->where('id', 2)
->find();
```


Each method (`select`, `join`, `leftJoin`, `where`, `orderBy`, etc.) **adds** parameters instead of replacing them.

```php
$queries->select('columnA')->select('columnB')->select('columnC');
```

### Aggregation Functions

```php
$queries->select('products')->count();
$queries->select('products')->min('price');
$queries->select('products')->max('price');
$queries->select('products')->sum('price');
$queries->select('products')->avg('price');
$queries->select('customers')->distinct('country');
```

### Join / Left Join

```php
$queries
->select('tableA')
->join('tableB', ['tableB.id' => 'tableA.id')
->find();
//or
$queries
->select('tableA')
->join('tableB', ['tableB.id' => 'tableA.id'])
->find();
```

```php
$queries
->select('tableA')
->leftJoin('tableB', ['tableB.id' => 'tableA.id'])
->find();
//or
$queries
->select('tableA')
->leftJoin('tableB', ['tableB.id' => 'tableA.id'])
->find();
```

### Where

Supports many operators:

```php
$queries
->select('tableA')
->where('column', 'val1')
->where('column', '=', 'val1')
->where('column', 'IN', ['val2', 'val3'])
->where('column', 'LIKE', '%a%')
->where('column', '<>', 15)
->where('column', 'BETWEEN', 5, 10)
->where('column', 'IS NULL')
->where('columnA = ? and columnB = ?', ['valA', 'valB']);
```

Default conjunction is `AND`. Use `orWhere` for `OR`.

Nested conditions:

```php
$queries
->select('tableA')
->where('columnA', 'val1')
->where(function($q) {
    $q->where('column', 'val2')->orWhere('column', 'val3');
});
```

### OrderBy

```php
$queries->select('table')->orderByDesc('column1');
$queries->select('table')->orderByAsc('column1');
```

### GroupBy

```php
$queries->select('table')->groupBy('column1', 'column2');
```

### Having

```php
$queries
->select('customers', 'country, city, count(*)')
->groupBy('country, city')
->having('count(*)', '>', 1)
->find();
```

---

## ORM

Define mappings for query results:

```php
$orders = $queries
->select('orders o', Map::select("orders", [
'order_id'   => 'id',
'order_date' => 'date'
]))
->leftJoin('shippers sh', ['sh.shipper_id' => 'o.shipper_id'], Map::one('shipper', [
    'shipper_id'   => 'id',
    'shipper_name' => 'name'
]))
->findAll();
```

For the ORM to work without additional configuration:

- each record must have a **unique identifier** (by default `id`, but this can be changed using the `classId` parameter),
- tables in the query must have **aliases**.

This allows StormQueries to map records to user-defined objects, even if the key fields differ from the standard `id`.

Supports:

* One-to-one
* One-to-many
* Many-to-many
* Nested hierarchical mapping

---

## Subqueries

From:

```php
$queries
->select(SubQuery::create($queries->select('products'), 'p'))
->where('p.product_id', 7)
->find();
```

Left Join:

```php
$queries
->select(SubQuery::create($queries->from('products'), 'p'))
->leftJoin(SubQuery::create($queries->from('suppliers'), 's'), ['s.supplier_id' => 'p.supplier_id'])
->findAll();
```

Where with subquery:

```php
$queries
->select("products")
->where("category_id", 1)
->where('price', '<=', $queries
    ->select("avg(price)")
    ->from("products")
    ->where("category_id", 1)
)
->findAll();
```

---

## Assembling Queries

```php
$query = $queries
->select('products')
->join('product_photos', ['product_photos.product_id' => 'products.id'])
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

$products = $query->findAll();
```

---

## Insert

```php
$id = $queries->insert('person', ['name' => 'Michael']);
```

Insert many:

```php
$queries
->insertMany('person', [
    ['name' => 'Michael'],
    ['name' => 'Kevin'],
    ['name' => 'Martin']
])
->execute();
```

---

## Update

```php
$queries->update('person', ['id' => 2], ['name' => 'Matthew']);
```

Or with query builder:

```php
$queries->updateQuery('products')
->where('id', 3)
->set('price = price + 5')
->execute();
```

---

## Union
```php
$suppliers = $queries->select('suppliers', 'contact_name')->where('contact_name', 'LIKE', '%Michael%');
$customers = $queries->select('customers', 'contact_name')->where('contact_name', 'LIKE', '%Maria%');
$items = $queries->union($suppliers, $customers)->find();
```
---

## Delete

```php
$queries->delete('person', ['id' => 1]);
```

Or with query builder:

```php
$queries->deleteQuery('person')->where('id', 1)->execute();
```
---
## Transactions
```php
try {
    $queries->begin();
    // your queries
    $queries->commit();
}
catch(Throwable $throwable) {
    $queries->rollback();
    throw $throwable;
}
```
---

## Raw queries
```php
$queries->execute("CREATE SCHEMA storm_test");

$items = $queries->query("SELECT * FROM customers WHERE customer_id = ?", [7]);
```
---

## Profiling and Logging

```php
$connection->onSuccess(function(string $sql, DateInterval $interval) {
// log successful queries
});

$connection->onFailure(function(string $sql, DateInterval $interval, Exception $e) {
// log failed queries
});
```

---

## Notice

* Requires **PHP 8.0+**
* Uses **PDO**
* Tested with PostgreSQL, MySQL, MariaDB, SQL Server, SQLite

---

## Tests

Run tests with Docker:

```bash
docker-compose up
./run.mysql.cmd
```

---

## Examples

See the `tests` directory for more detailed use cases.

---

## Author

**Michał Czerski**

---

## License

StormPHP Queries is licensed under the **MIT License**.
