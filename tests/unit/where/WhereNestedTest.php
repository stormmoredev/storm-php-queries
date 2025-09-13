<?php
declare(strict_types=1);

namespace unit\where;

use PHPUnit\Framework\TestCase;
use Stormmore\Queries\IConnection;
use Stormmore\Queries\Queries\SelectQuery;

final class WhereNestedTest extends TestCase
{
    private SelectQuery $selectBuilder;

    public function testNestedWhere(): void
    {
        $this->selectBuilder->where('city', 'New York');
        $this->selectBuilder->Where(function ($query) {
            $query->where('customer_name', 'Micheal');
            $query->orWhere('customer_name', 'Martin');
        });
        $query = $this->selectBuilder->getSql();
        $where = get_nth_line($query, 2);
        $this->assertEquals("WHERE city = ? AND (customer_name = ? OR customer_name = ?)", $where);
    }

    public function testNestedOrWhere(): void
    {
        $this->selectBuilder->where('city', 'New York');
        $this->selectBuilder->orWhere(function ($query) {
            $query->where('customer_name', 'Micheal');
            $query->orWhere('customer_name', 'Martin');
        });

        $query = $this->selectBuilder->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE city = ? OR (customer_name = ? OR customer_name = ?)", $where);
    }

    public function testNestedWereInMiddle(): void
    {
        $this->selectBuilder->where('city', 'New York');
        $this->selectBuilder->orWhere(function ($query) {
            $query->where('customer_name', 'Micheal');
            $query->orWhere('customer_name', 'Martin');
        });
        $this->selectBuilder->where('address', 'Times');

        $query = $this->selectBuilder->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE city = ? OR (customer_name = ? OR customer_name = ?) AND address = ?", $where);
    }

    public function testRecursiveNestedWhere(): void
    {
        $this->selectBuilder->where('field1', 'value1');
        $this->selectBuilder->orWhere(function ($query) {
            $query->where('field2', 'value2');
            $query->orWhere(function($query) {
                $query->where('field3', 'value3');
                $query->where('field4', 'value4');
            });
        });
        $this->selectBuilder->where('field5', 'value5');

        $query = $this->selectBuilder->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE field1 = ? OR (field2 = ? OR (field3 = ? AND field4 = ?)) AND field5 = ?", $where);
    }

    protected function setUp(): void
    {
        $mock = $this->createMock(IConnection::class);
        $this->selectBuilder = new SelectQuery($mock);
        $this->selectBuilder->select('*');
        $this->selectBuilder->from('customers');
    }
}