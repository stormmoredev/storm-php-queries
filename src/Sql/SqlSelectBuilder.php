<?php

namespace Stormmore\Queries\Sql;

use InvalidArgumentException;
use Stormmore\Queries\Queries\SubQuery;
use Stormmore\Queries\Sql\Clauses\ConditionalClause;
use Stormmore\Queries\Sql\Clauses\JoinClause;
use Stormmore\Queries\Sql\Clauses\LimitOffsetClause;
use Stormmore\Queries\Sql\Clauses\OrderByClause;
use Stormmore\Queries\Sql\Clauses\SelectClause;

class SqlSelectBuilder
{
    private string $table = "";
    private SelectClause $selectClause;
    private JoinClause $joinClause;
    private ConditionalClause $whereClause;
    private ConditionalClause $havingClause;
    private OrderByClause $orderByClause;
    private LimitOffsetClause $limitOffsetClause;
    private array $groupBy = [];


    public function __construct(?string $sqlDialect = null)
    {
        $this->joinClause = new JoinClause();
        $this->whereClause = new ConditionalClause('WHERE');
        $this->havingClause = new ConditionalClause('HAVING');
        $this->orderByClause = new OrderByClause();
        $this->selectClause = new SelectClause();
        $this->limitOffsetClause = new LimitOffsetClause($sqlDialect);
    }

    public function from(string $table): SqlSelectBuilder
    {
        $this->table = $table;
        return $this;
    }

    public function select(mixed ...$fields): SqlSelectBuilder
    {
        $this->selectClause->add($fields);
        return $this;
    }

    public function clearSelect(): SqlSelectBuilder
    {
        $this->selectClause->clear();
        return $this;
    }

    public function join(string $type, string|SubQuery $set, string|array $on): SqlSelectBuilder
    {
        $this->joinClause->addJoin($type, $set, $on);
        return $this;
    }

    public function whereString(string $condition, array $parameters): SqlSelectBuilder
    {
        $this->whereClause->whereString($condition, $parameters);
        return $this;
    }

    public function where(): SqlSelectBuilder
    {
        call_user_func_array([$this->whereClause, 'where'], func_get_args());
        return $this;
    }

    public function orWhere(): SqlSelectBuilder
    {
        call_user_func_array([$this->whereClause, 'orWhere'], func_get_args());
        return $this;
    }

    public function orderBy(string $column, int $direction): SqlSelectBuilder
    {
        $this->orderByClause->add($column, $direction);
        return $this;
    }

    public function orderByDesc(string $column): SqlSelectBuilder
    {
        $this->orderByClause->add($column, -1);
        return $this;
    }

    public function orderByAsc(string $column): SqlSelectBuilder
    {
        $this->orderByClause->add($column, 1);
        return $this;
    }

    public function groupBy(string ...$fields): SqlSelectBuilder
    {
        $this->groupBy = $fields;
        return $this;
    }

    public function having(): SqlSelectBuilder
    {
        call_user_func_array([$this->havingClause, 'having'], func_get_args());
        return $this;
    }

    public function orHaving(): SqlSelectBuilder
    {
        call_user_func_array([$this->havingClause, 'orHaving'], func_get_args());
        return $this;
    }

    public function pagination(int $limit, int $offset): SqlSelectBuilder
    {
        $this->limitOffsetClause->setLimitOffset($limit, $offset);
        return $this;
    }

    public function toSql(): string
    {
        $statement = [];
        $statement[] = $this->selectClause->toString();
        $statement[] = $this->toFromClause();
        $statement[] = $this->joinClause->toString();
        $statement[] = $this->whereClause->toString();
        $statement[] = $this->toGroupByClause();
        $statement[] = $this->havingClause->toString();
        $statement[] = $this->orderByClause->toString();
        $statement[] = $this->limitOffsetClause->toString();

        return implode("\n", array_filter($statement, function($element) { return !empty($element); }));
    }

    public function getParameters(): array
    {
        return array_merge($this->whereClause->getParameters(), $this->havingClause->getParameters());
    }

    private function toFromClause(): string
    {
        !empty($this->table) or throw new InvalidArgumentException("Table is required");
        return "FROM " . "$this->table";
    }

    private function toGroupByClause(): string
    {
        if (!count($this->groupBy)) {
            return "";
        }
        $clause = "GROUP BY";
        foreach($this->groupBy as $group) {
                $clause .= ' ' . $group;
        }
        return $clause;
    }
}