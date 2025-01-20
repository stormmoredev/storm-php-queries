<?php declare(strict_types=1);

namespace unit\where;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Storm\Query\IConnection;
use Storm\Query\Queries\SelectQuery;

final class WhereTest extends TestCase
{
    private SelectQuery $selectBuilder;

    public function testWhereWithInvalidOperator(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->selectBuilder->where('customer_name', "xyz", 'Micheal');

        $this->selectBuilder->getSql();
    }

    public function testWhereArgumentIsNotFunction(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->selectBuilder->where('customer_id = 9');
    }

    public function testWhereWithDefaultOperator(): void
    {
        $this->selectBuilder->where('customer_name', 'Micheal');

        $query = $this->selectBuilder->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE customer_name = ?", $where);
    }

    public function testWhereWithEqualOperator(): void
    {
        $this->selectBuilder->where('customer_name', '=', 'Micheal');

        $query = $this->selectBuilder->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE customer_name = ?", $where);
    }

    public function testWhereWithNotEqual(): void
    {
        $this->selectBuilder->where('customer_name', '<>', 'Micheal');

        $query = $this->selectBuilder->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE customer_name <> ?", $where);
    }

    public function testWhereWithNotWordEqual(): void
    {
        $this->selectBuilder->where('customer_name', 'NOT', 'Micheal');

        $query = $this->selectBuilder->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE NOT customer_name = ?", $where);
    }

    public function testWhereIn(): void
    {
        $this->selectBuilder->where('customer_name', 'IN', ['Micheal', 'Martin']);

        $query = $this->selectBuilder->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE customer_name IN (?,?)", $where);
    }

    public function testWhereNotInFunction(): void
    {
        $this->selectBuilder->where('customer_name', 'NOT IN', ['Micheal', 'Martin']);

        $query = $this->selectBuilder->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE customer_name NOT IN (?,?)", $where);
    }

    public function testWhereInIsNotArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->selectBuilder->where('customer_name', 'NOT IN', 'das');

        $this->selectBuilder->getSql();
    }

    public function testWhereLike(): void
    {
        $this->selectBuilder->where('customer_name', 'LIKE', 'Micheal');

        $query = $this->selectBuilder->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE customer_name LIKE ?", $where);
    }

    public function testWherePercentLike(): void
    {
        $this->selectBuilder->where('customer_name', 'LIKE', '%Micheal');

        $query = $this->selectBuilder->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE customer_name LIKE ?", $where);
    }

    public function testWhereLikePercent(): void
    {
        $this->selectBuilder->where('customer_name', 'LIKE', 'Micheal%');

        $query = $this->selectBuilder->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE customer_name LIKE ?", $where);
    }

    public function testWherePercentLikePercent(): void
    {
        $this->selectBuilder->where('customer_name', 'LIKE', '%Micheal%');

        $query = $this->selectBuilder->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE customer_name LIKE ?", $where);
    }

    public function testWhereGreater(): void
    {
        $this->selectBuilder->where('customer_id', '>', '7');

        $query = $this->selectBuilder->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE customer_id > ?", $where);
    }

    public function testWhereGreaterEqual(): void
    {
        $this->selectBuilder->where('customer_id', '>=', '7');

        $query = $this->selectBuilder->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE customer_id >= ?", $where);
    }

    public function testWhereLess(): void
    {
        $this->selectBuilder->where('customer_id', '<', '7');

        $query = $this->selectBuilder->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE customer_id < ?", $where);
    }

    public function testWhereLessEqual(): void
    {
        $this->selectBuilder->where('customer_id', '<=', '7');

        $query = $this->selectBuilder->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE customer_id <= ?", $where);
    }

    public function testWhereBetweenEqual(): void
    {
        $this->selectBuilder->where('customer_id', 'BETWEEN', 7, 9);

        $query = $this->selectBuilder->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE customer_id BETWEEN ? AND ?", $where);
    }

    public function testWhereIsNull(): void
    {
        $this->selectBuilder->where('customer_id', 'IS NULL');

        $query = $this->selectBuilder->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE customer_id IS NULL", $where);
    }

    public function testWhereIsNotNull(): void
    {
        $this->selectBuilder->where('customer_id', 'IS NOT NULL');

        $query = $this->selectBuilder->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE customer_id IS NOT NULL", $where);
    }

    public function testNestedWhere(): void
    {
        $this->selectBuilder->where('city', 'New York');
        $this->selectBuilder->Where(function($query) {
            $query->where('customer_name', 'Micheal');
            $query->orWhere('customer_name', 'Martin');
        });

        $query = $this->selectBuilder->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE city = ? AND (customer_name = ? OR customer_name = ?)", $where);
    }

    public function testWhereConjunction(): void
    {
        $this->selectBuilder->where('customer_name', 'Micheal');
        $this->selectBuilder->orWhere('contact_name', 'Micheal');
        $this->selectBuilder->where('city', 'New york');

        $query = $this->selectBuilder->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE customer_name = ? OR contact_name = ? AND city = ?", $where);
    }

    protected function setUp(): void
    {
        $mock = $this->createMock(IConnection::class);
        $this->selectBuilder = new SelectQuery($mock);
        $this->selectBuilder->select('*');
        $this->selectBuilder->from('customers');
    }
}