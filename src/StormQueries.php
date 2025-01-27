<?php

namespace Storm\Query;

use Storm\Query\Mapper\Map;
use Storm\Query\Queries\DeleteQuery;
use Storm\Query\Queries\InsertQuery;
use Storm\Query\Queries\SelectQuery;
use Storm\Query\Queries\SubQuery;
use Storm\Query\Queries\UpdateQuery;

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

    public function update($table, $values = array()): UpdateQuery
    {
        $query = new UpdateQuery($this->connection);
        $query->update($table);
        if (count($values)) {
            $query->setValues($values);
        }
        return $query;
    }

    public function delete($table, string $where ='', ...$parameters): DeleteQuery
    {
        $query = new DeleteQuery($this->connection);
        $query->from($table);
        if (!empty($where)) {
            $query->whereString($where, $parameters);
        }
        return $query;
    }
}