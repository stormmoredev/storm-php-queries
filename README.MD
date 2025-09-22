# StormPHP Queries

A lightweight **query builder** and **ORM** for PHP.
⚡ Fast, intuitive, and flexible — without heavy configuration or boilerplate.

* 🚀 **Hierarchical models** — benefit from ORM features without over-configuration
* 🔎 **Fluent query builder** with modern criteria-finder pattern
* 📦 **Zero configuration** — no need to describe DB schema
* 🗄️ Works with multiple databases (PostgreSQL, MySQL, MariaDB, MSSQL, SQLite)
* 🧹 **Lightweight** — tidy code, no extra dependencies
* 🛠️ Developer-friendly — SQL profiling & logging

---

## Why StormPHP Queries?

Most ORMs are either too heavy or too limited. StormPHP Queries strikes the balance:

* ✅ **No configuration required** — just connect and start querying
* ✅ **Clean domain models** — keep your DDD aggregates free from persistence concerns
* ✅ **Fluent and flexible API** — build simple or complex queries step by step
* ✅ **Lightweight and dependency-free** — fast to learn, easy to maintain

If you need the power of an ORM combined with the clarity of a query builder — StormPHP Queries is for you.

---

## Quick Start

### Installation

```bash
composer require stormmore/queries
```

### Establishing a Connection

StormPHP Queries uses PDO:

```php
use Stormmore\Queries\ConnectionFactory;
use Stormmore\Queries\StormQueries;

$connection = ConnectionFactory::createFromString("dsn", "user", "password");
$queries = new StormQueries($connection);
```

### Minimal Example

```php
// Select
$product = $queries->find('products', ['id' => 5]);

// Insert
$id = $queries->insert('products', [
    'name' => 'Golden watch',
    'price' => 465
]);

// Update
$queries->update('products', ['id' => $id], ['name' => 'Renamed product']);

// Delete
$queries->delete('products', ['id' => $id]);
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

Count products:

```php
$count = $queries->count('products', ['in_sale' => true]);
```

Check existence:

```php
$exists = $queries->exist('products', ['id' => 5]);
```

Map records to a custom class:

```php
use Stormmore\Queries\Mapper\Map;

$product = $queries->find('products', ['id' => 5], Map::select([
    'product_id'   => 'id',
    'product_name' => 'name'
], UserProduct::class));
```

---

## ORM

StormPHP Queries supports mapping query results to classes. Example:

```php
$customers = $queries
    ->select('customers c', Map::select([
        'customer_id'   => 'id',
        'customer_name' => 'name'
    ]))
    ->leftJoin('orders o', 'o.customer_id = c.customer_id', Map::many("orders", [
        'order_id' => 'id'
    ]))
    ->findAll();
```

For the ORM to work without additional configuration:
 - each record must have a unique identifier (by default id, but this can be changed using the classId parameter),
 - tables in the query must have aliases.

This allows StormQueries to map records to user-defined objects, even if the key fields differ from the standard id.

```php
$customer = $this->queries
    ->select('customers c', Map::select([
        'customer_id',
        'customer_name'
    ], Customer::class, classId: 'customer_id'))
    ->leftJoin('orders o', 'o.customer_id = c.customer_id', Map::many("orders", [
        'order_id'
    ], Order::class, classId: 'order_id'))
    ->where('c.customer_id', 90)
    ->find();

foreach ($customer->orders as $order) {
    echo $customer->customer_name . " - " . $order->id;
}

```

---

## Guide

Looking for more?
The full guide includes detailed sections on **Select Queries, ORM, Subqueries, Insert/Update/Delete, Union, Profiling, and more.**

👉 Read here: [Full Documentation – docs/guide.md](https://stormmoredev.github.io/storm-php-queries/)

---

## Profiling

You can log queries using callbacks:

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

* StormPHP Queries uses **PDO**.
* Tested with **PostgreSQL, MySQL, MariaDB, SQL Server, SQLite**.
* Requires **PHP 8.0+**.

---

## Tests

Run tests using Docker:

```bash
docker-compose up
./run.mysql.cmd
```

---

## Examples

Check the `tests` directory for more detailed examples.

---

## Contributing

Contributions are welcome!

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/your-feature`)
3. Commit your changes (`git commit -m "Add new feature"`)
4. Push to your branch (`git push origin feature/your-feature`)
5. Open a Pull Request

---

## Author

**Michał Czerski**
If you have questions or ideas, feel free to reach out on GitHub.

---

## License

StormPHP Queries is licensed under the **MIT License**.