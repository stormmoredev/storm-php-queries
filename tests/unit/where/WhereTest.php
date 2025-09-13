<?php declare(strict_types=1);

namespace unit\where;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Stormmore\Queries\IConnection;
use Stormmore\Queries\Queries\SelectQuery;

final class WhereTest extends TestCase
{
    private SelectQuery $selectQuery;

    public function testWhereWithInvalidOperator(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->selectQuery->where('customer_name', "xyz", 'Micheal');

        $this->selectQuery->getSql();
    }

    public function testWhereArgumentIsNotFunction(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->selectQuery->where('customer_id = 9');
    }

    public function testWhereWithDefaultOperator(): void
    {
        $this->selectQuery->where('customer_name', 'Micheal');

        $query = $this->selectQuery->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE customer_name = ?", $where);
    }

    public function testWhereWithEqualOperator(): void
    {
        $this->selectQuery->where('customer_name', '=', 'Micheal');

        $query = $this->selectQuery->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE customer_name = ?", $where);
    }

    public function testWhereWithNotEqual(): void
    {
        $this->selectQuery->where('customer_name', '<>', 'Micheal');

        $query = $this->selectQuery->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE customer_name <> ?", $where);
    }

    public function testWhereWithNotWordEqual(): void
    {
        $this->selectQuery->where('customer_name', 'NOT', 'Micheal');

        $query = $this->selectQuery->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE NOT customer_name = ?", $where);
    }

    public function testWhereIn(): void
    {
        $this->selectQuery->where('customer_name', 'IN', ['Micheal', 'Martin']);

        $query = $this->selectQuery->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE customer_name IN (?,?)", $where);
    }

    public function testWhereNotInFunction(): void
    {
        $this->selectQuery->where('customer_name', 'NOT IN', ['Micheal', 'Martin']);

        $query = $this->selectQuery->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE customer_name NOT IN (?,?)", $where);
    }

    public function testWhereInIsNotArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->selectQuery->where('customer_name', 'NOT IN', 'das');

        $this->selectQuery->getSql();
    }

    public function testWhereLike(): void
    {
        $this->selectQuery->where('customer_name', 'LIKE', 'Micheal');

        $query = $this->selectQuery->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE customer_name LIKE ?", $where);
    }

    public function testWherePercentLike(): void
    {
        $this->selectQuery->where('customer_name', 'LIKE', '%Micheal');

        $query = $this->selectQuery->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE customer_name LIKE ?", $where);
    }

    public function testWhereLikePercent(): void
    {
        $this->selectQuery->where('customer_name', 'LIKE', 'Micheal%');

        $query = $this->selectQuery->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE customer_name LIKE ?", $where);
    }

    public function testWherePercentLikePercent(): void
    {
        $this->selectQuery->where('customer_name', 'LIKE', '%Micheal%');

        $query = $this->selectQuery->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE customer_name LIKE ?", $where);
    }

    public function testWhereGreater(): void
    {
        $this->selectQuery->where('customer_id', '>', '7');

        $query = $this->selectQuery->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE customer_id > ?", $where);
    }

    public function testWhereGreaterEqual(): void
    {
        $this->selectQuery->where('customer_id', '>=', '7');

        $query = $this->selectQuery->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE customer_id >= ?", $where);
    }

    public function testWhereLess(): void
    {
        $this->selectQuery->where('customer_id', '<', '7');

        $query = $this->selectQuery->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE customer_id < ?", $where);
    }

    public function testWhereLessEqual(): void
    {
        $this->selectQuery->where('customer_id', '<=', '7');

        $query = $this->selectQuery->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE customer_id <= ?", $where);
    }

    public function testWhereBetweenEqual(): void
    {
        $this->selectQuery->where('customer_id', 'BETWEEN', 7, 9);

        $query = $this->selectQuery->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE customer_id BETWEEN ? AND ?", $where);
    }

    public function testWhereIsNull(): void
    {
        $this->selectQuery->where('customer_id', 'IS NULL');

        $query = $this->selectQuery->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE customer_id IS NULL", $where);
    }

    public function testWhereIsNotNull(): void
    {
        $this->selectQuery->where('customer_id', 'IS NOT NULL');

        $query = $this->selectQuery->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE customer_id IS NOT NULL", $where);
    }

    public function testNestedWhere(): void
    {
        $this->selectQuery->where('city', 'New York');
        $this->selectQuery->Where(function($query) {
            $query->where('customer_name', 'Micheal');
            $query->orWhere('customer_name', 'Martin');
        });

        $query = $this->selectQuery->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE city = ? AND (customer_name = ? OR customer_name = ?)", $where);
    }

    public function testWhereConjunction(): void
    {
        $this->selectQuery->where('customer_name', 'Micheal');
        $this->selectQuery->orWhere('contact_name', 'Micheal');
        $this->selectQuery->where('city', 'New york');

        $query = $this->selectQuery->getSql();
        $where = get_nth_line($query, 2);

        $this->assertEquals("WHERE customer_name = ? OR contact_name = ? AND city = ?", $where);
    }

    protected function setUp(): void
    {
        $mock = $this->createMock(IConnection::class);
        $this->selectQuery = new SelectQuery($mock);
        $this->selectQuery->select('*');
        $this->selectQuery->from('customers');
    }
}