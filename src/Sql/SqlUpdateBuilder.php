<?php

namespace Stormmore\Queries\Sql;
use Stormmore\Queries\Sql\Clauses\ConditionalClause;

class SqlUpdateBuilder
{
    private string $tableName;
    private array $values;
    private ConditionalClause $whereClause;

    public function __construct()
    {
        $this->whereClause = new ConditionalClause("WHERE");
    }

    public function update(string $tableName): SqlUpdateBuilder
    {
        $this->tableName = $tableName;
        return $this;
    }

    public function where(): SqlUpdateBuilder
    {
        call_user_func_array([$this->whereClause, 'where'], func_get_args());
        return $this;
    }

    public function orWhere(): SqlUpdateBuilder
    {
        call_user_func_array([$this->whereClause, 'orWhere'], func_get_args());
        return $this;
    }

    public function values(array $values): SqlUpdateBuilder
    {
        $this->values = $values;
        return $this;
    }

    public function toSql(): string
    {
        $statement = [];
        $statement[] = "UPDATE {$this->tableName}";
        $statement[] =  $this->toSetString();
        $statement[] = $this->whereClause->toString();

        return implode(" ", array_filter($statement, function($element) { return !empty($element); }));
    }

    public function getParameters(): array
    {
        return array_merge(array_values($this->values), $this->whereClause->getParameters());
    }

    private function toSetString(): string
    {
        $set = [];
        $keys = array_keys($this->values);
        foreach($keys as $key) {
            $set[] = "$key = ?";
        }
        return "SET " . implode(", ", $set);
    }
}