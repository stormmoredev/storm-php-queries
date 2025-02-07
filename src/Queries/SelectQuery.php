<?php

namespace Stormmore\Queries\Queries;

use InvalidArgumentException;
use Stormmore\Queries\IConnection;
use Stormmore\Queries\Mapper\Map;
use Stormmore\Queries\ParameterNormalizer;
use Stormmore\Queries\Sql\SqlSelectBuilder;
use Stormmore\Queries\Table;

class SelectQuery
{
    private QueryMapper $queryMapper;
    private SqlSelectBuilder $selectQuery;

    public function __construct(readonly private IConnection $connection)
    {
        $this->queryMapper = new QueryMapper();
        $this->selectQuery = new SqlSelectBuilder();
    }

    public function from(string|SubQuery $set, Map $map = null): SelectQuery
    {
        if ($map !== null) {
            $map->setTable(new Table($set));
            $this->queryMapper->addFromMap($map);
        }
        if ($set instanceof SubQuery) {
            $this->selectQuery->from("(" . $set->query->getSql() . ")" . " as " . $set->alias);
        }
        else {
            $this->selectQuery->from($set);
        }

        return $this;
    }

    public function select(string ...$fields): SelectQuery
    {
        call_user_func_array([$this->selectQuery, 'select'], func_get_args());
        return $this;
    }

    public function leftJoin(string|SubQuery $set, string $l, string $r, Map $map = null): SelectQuery
    {
        $this->addRelationshipMap($set, $map, $l, $r);
        $this->selectQuery->leftJoin('INNER', $set, $l, $r);
        return $this;
    }

    public function leftOuterJoin(string|SubQuery  $set, string $l, string $r, Map $map = null): SelectQuery
    {
        $this->addRelationshipMap($set, $map, $l, $r);
        $this->selectQuery->leftJoin('OUTER', $set, $l, $r);
        return $this;
    }

    private function addRelationshipMap(string|SubQuery $set, ?Map $map, string $l, string $r): void
    {
        $rootMap = $this->queryMapper->getFromMap();
        !($map == null and $rootMap != null) or throw new InvalidArgumentException("Map for join is required");
        !($map != null and $rootMap == null) or throw new InvalidArgumentException("Map for root table (from) is required");

        if ($map !== null) {
            $map->setTable(new Table($set));
            $this->queryMapper->addRelationshipMap($map, $l, $r);
        }
    }

    public function whereString(string $whereCondition, array $parameters): SelectQuery
    {
        $this->selectQuery->whereString($whereCondition, $parameters);
        return $this;
    }

    public function where(): SelectQuery
    {
        call_user_func_array([$this->selectQuery, 'where'], func_get_args());
        return $this;
    }

    public function orWhere(): SelectQuery
    {
        call_user_func_array([$this->selectQuery, 'orWhere'], func_get_args());
        return $this;
    }

    public function having(): SelectQuery
    {
        call_user_func_array([$this->selectQuery, 'having'], func_get_args());
        return $this;
    }

    public function orHaving(): SelectQuery
    {
        call_user_func_array([$this->selectQuery, 'orHaving'], func_get_args());
        return $this;
    }

    public function orderByDesc(string $column): SelectQuery
    {
        $this->selectQuery->orderByDesc($column);
        return $this;
    }

    public function orderByAsc(string $column): SelectQuery
    {
        $this->selectQuery->orderByAsc($column);
        return $this;
    }

    public function orderBy(string $column, int $direction): SelectQuery
    {
        $this->selectQuery->orderBy($column, $direction);
        return $this;
    }

    public function groupBy(string ...$fields): SelectQuery
    {
        call_user_func_array([$this->selectQuery, 'groupBy'], func_get_args());
        return $this;
    }

    public function limit(int $limit): SelectQuery
    {
        $this->selectQuery->limit($limit);
        return $this;
    }

    public function offset(int $offset): SelectQuery
    {
        $this->selectQuery->offset($offset);
        return $this;
    }

    public function getSql(): string
    {
        if ($this->queryMapper->hasMaps()) {
            $this->selectQuery->clearSelect();
            foreach ($this->queryMapper->getSelect() as $select) {
                $this->selectQuery->select($select);
            }
        }
        return $this->selectQuery->toSql();
    }

    public function getParameters(): array
    {
        return $this->selectQuery->getParameters();
    }

    public function  find(): ?object
    {
        $results = $this->findAll();
        return count($results) > 0 ? $results[0] : null;
    }

    public function findAll(): array
    {
        $sql = $this->getSql();
        $parameters = ParameterNormalizer::normalize($this->selectQuery->getParameters());

        $results = $this->connection->query($sql, $parameters);
        if ($this->queryMapper->hasMaps()) {
            return $this->queryMapper->mapResults($results);
        }

        return $results;
    }

    public function min(string $column): float
    {
        $this->selectQuery->clearSelect();
        $this->selectQuery->select('min(' . $column . ') as _min');
        return $this->find()->_min;
    }

    public function max(string $column): float
    {
        $this->selectQuery->clearSelect();
        $this->selectQuery->select('max(' . $column . ') as _max');
        return $this->find()->_max;
    }

    public function count(): int
    {
        $this->selectQuery->clearSelect();
        $this->selectQuery->select('count(*) as _count');
        return $this->find()->_count;
    }

    public function avg(string $column): float
    {
        $this->selectQuery->clearSelect();
        $this->selectQuery->select('avg(' . $column . ') as _avg');
        return $this->find()->_avg;
    }

    public function sum(string $column): float
    {
        $this->selectQuery->clearSelect();
        $this->selectQuery->select('sum(' . $column . ') as _sum');
        return $this->find()->_sum;
    }
}