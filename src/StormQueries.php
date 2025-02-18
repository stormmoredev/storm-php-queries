<?php

namespace Stormmore\Queries;

use Stormmore\Queries\Mapper\Map;
use Stormmore\Queries\Queries\DeleteQuery;
use Stormmore\Queries\Queries\InsertQuery;
use Stormmore\Queries\Queries\SelectQuery;
use Stormmore\Queries\Queries\SubQuery;
use Stormmore\Queries\Queries\UpdateQuery;

readonly class StormQueries
{
    public function __construct(private IConnection $connection)
    {
    }

    public function insert($table, $record = array()): InsertQuery
    {
        $query = new InsertQuery($this->connection);
        $query->into($table);
        if (count($record)) {
            $query->setRecord($record);
        }
        return $query;
    }

    public function insertMany($table, $records = array()): InsertQuery
    {
        $insertQuery = new InsertQuery($this->connection);
        $insertQuery->into($table);
        $insertQuery->setRecords($records);
        return $insertQuery;
    }

    public function select(...$fields): SelectQuery
    {
        $selectQuery = new SelectQuery($this->connection);
        call_user_func_array([$selectQuery, 'select'], func_get_args());
        return $selectQuery;
    }

    public function from(string|SubQuery $set, mixed ...$parameters): SelectQuery
    {
        $map = null;
        $whereString = null;
        $count = count($parameters);
        if ($count and $parameters[$count - 1] instanceof Map) {
            $map = array_pop($parameters);
            $count--;
        }
        if ($count and is_string($parameters[0])) {
            $whereString  = array_shift($parameters);
        }

        $selectQuery = new SelectQuery($this->connection);
        $selectQuery->select('*');
        $selectQuery->from($set, $map);
        if (!empty($whereString)) {
            $selectQuery->whereString($whereString, $parameters);
        }
        return $selectQuery;
    }

    public function update($table, string $where = '', mixed ... $parameters): UpdateQuery
    {


        $query = new UpdateQuery($this->connection);
        $query->update($table);

        if (!empty($where)) {
            $values = array();
            if (count($parameters) and is_array($parameters[count($parameters) - 1])) {
                $values = array_pop($parameters);
            }
            $query->where($where, $parameters);
            if (count($values)) {
                $query->setValues($values);
            }
        }

        return $query;
    }

    public function delete($table, string $where = '', ...$parameters): DeleteQuery
    {
        $query = new DeleteQuery($this->connection);
        $query->from($table);
        if (!empty($where)) {
            $query->whereString($where, $parameters);
        }
        return $query;
    }

    public function find(string $table, string $where, mixed ... $parameters): ?object
    {
        return $this->createSelectQuery($table, $where, $parameters)->find();
    }

    public function findAll(string $table, string $where, mixed ... $parameters): array
    {
        return $this->createSelectQuery($table, $where, $parameters)->findAll();
    }

    private function createSelectQuery(string $table, string $where, array $parameters): SelectQuery
    {
        $map = null;
        if (count($parameters) and $parameters[count($parameters) - 1] instanceof Map) {
            $map = array_pop($parameters);
        }
        $selectQuery = new SelectQuery($this->connection);
        $selectQuery->select('*');
        $selectQuery->from($table, $map);
        $selectQuery->whereString($where, $parameters);
        return $selectQuery;
    }
}