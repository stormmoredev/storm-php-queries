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

    public function insertQuery(string $table, array $record):InsertQuery
    {
        $query = new InsertQuery($this->connection);
        $query->into($table);
        $query->setRecord($record);
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

    public function select(string|SubQuery $table, mixed ...$parameters): SelectQuery
    {
        $map = null;
        if (count($parameters)) {
            if ($parameters[0] instanceof Map) {
                $map = $parameters[0];
                array_shift($parameters);
            }
        }
        $selectQuery = new SelectQuery($this->connection);
        $selectQuery->from($table, $map);
        if (count($parameters)) {
            $selectQuery->select($parameters);
        } else {
            $selectQuery->select("*");
        }
        return $selectQuery;
    }

    public function find(string $table, string|array $where, mixed ... $parameters): ?object
    {
        return $this->getBasicFindQuery("*", $table, $where, $parameters)->find();
    }

    public function findAll(string $table, null|string|array $where = null, mixed ... $parameters): array
    {
        return $this->getBasicFindQuery("*", $table, $where, $parameters)->findAll();
    }

    public function count(string $table, string|array $where = '', mixed ...$parameters): int
    {
        return $this->getBasicFindQuery('count(*) as count', $table, $where, $parameters)->find()->count;
    }

    public function exist(string $table, string|array $where, mixed ...$parameters): bool
    {
        return $this->getBasicFindQuery('1', $table, $where, $parameters)->find() != null;
    }

    private function getBasicFindQuery(string $select, string $table, null|string|array $where, array $parameters): SelectQuery
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
        if(is_string($where) and !empty($where)) {
            $selectQuery->whereString($where, $parameters);
        }
        return $selectQuery;
    }
}