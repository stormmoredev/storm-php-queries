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

    public function insertQuery(string $table):InsertQuery
    {
        $query = new InsertQuery($this->connection);
        $query->into($table);
        return $query;
    }

    public function insert($table, array $record): int
    {
        $query = new InsertQuery($this->connection);
        $query->into($table);
        $query->setRecord($record);
        return $query->execute();
    }

    public function insertMany($table, $records = array()): void
    {
        $insertQuery = new InsertQuery($this->connection);
        $insertQuery->into($table);
        $insertQuery->setRecords($records);
        $insertQuery->execute();
    }

    public function updateQuery(string $table): UpdateQuery
    {
        $query = new UpdateQuery($this->connection);
        $query->update($table);
        return $query;
    }

    public function update($table, string|array $where = '', mixed ... $parameters): void
    {
        $query = new UpdateQuery($this->connection);
        $query->update($table);

        $values = array();
        if (count($parameters) and is_array(end($parameters))) {
            $values = array_pop($parameters);
        }
        if (is_array($where) and count($where)) {
            foreach($where as $field => $value) {
                $query->where($field, $value);
            }
        }
        if (is_string($where) and !empty($where)) {
            $query->where($where, $parameters);
        }
        if (count($values)) {
            $query->setValues($values);
        }

        $query->execute();
    }

    public function deleteQuery(string $table): DeleteQuery
    {
        $query = new DeleteQuery($this->connection);
        $query->from($table);
        return $query;
    }

    public function delete($table, string|array $where, mixed ...$parameters): void
    {
        $query = new DeleteQuery($this->connection);
        $query->from($table);
        if (is_array($where) and count($where)) {
            foreach ($where as $field => $value) {
                $query->where($field, $value);
            }
        }
        if (is_string($where) and !empty($where)) {
            $query->whereString($where, $parameters);
        }
        $query->execute();
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

    public function find(string $table, string|array $where, mixed ... $parameters): ?object
    {
        return $this->getBasicFindQuery("*", $table, $where, $parameters)->find();
    }

    public function findAll(string $table, string|array $where, mixed ... $parameters): array
    {
        return $this->getBasicFindQuery("*", $table, $where, $parameters)->findAll();
    }

    private function getBasicFindQuery(string $select, string $table, string|array $where, array $parameters): SelectQuery
    {
        $map = null;
        if (count($parameters) and $parameters[count($parameters) - 1] instanceof Map) {
            $map = array_pop($parameters);
        }
        $selectQuery = new SelectQuery($this->connection);
        $selectQuery->select($select);
        $selectQuery->from($table, $map);
        if(is_array($where)) {
            foreach($where as $field => $value) {
                $selectQuery->where($field, $value);
            }
        }
        if(is_string($where)) {
            $selectQuery->whereString($where, $parameters);
        }
        return $selectQuery;
    }

    public function exist(string $table, string|array $where, mixed ...$parameters): bool
    {
        $query = new SelectQuery($this->connection);
        $query->select('1');
        $query->from($table);
        if(is_array($where)) {
            foreach($where as $field => $value) {
                $query->where($field, $value);
            }
        }
        if(is_string($where)) {
            $query->whereString($where, $parameters);
        }
        $item = $query->find();
        return $item != null;
    }


}