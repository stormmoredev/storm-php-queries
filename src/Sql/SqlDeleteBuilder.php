<?php

namespace Storm\Query\Sql;

use Storm\Query\Sql\Clauses\ConditionalClause;

class SqlDeleteBuilder
{
    private string $tableName;
    private ConditionalClause $whereClause;

    public function __construct()
    {
        $this->whereClause = new ConditionalClause("WHERE");
    }

    public function from(string $tableName): SqlDeleteBuilder
    {
        $this->tableName = $tableName;
        return $this;
    }
    public function whereString(string $condition, array $parameters): SqlDeleteBuilder
    {
        $this->whereClause->whereString($condition, $parameters);
        return $this;
    }

    public function where(): SqlDeleteBuilder
    {
        call_user_func_array([$this->whereClause, 'where'], func_get_args());
        return $this;
    }

    public function orWhere(): SqlDeleteBuilder
    {
        call_user_func_array([$this->whereClause, 'orWhere'], func_get_args());
        return $this;
    }

    public function toSql(): string
    {
        $statement = [];
        $statement[] = "DELETE FROM {$this->tableName}";
        $statement[] = $this->whereClause->toString();

        return implode(" ", array_filter($statement, function($element) { return !empty($element); }));
    }

    public function getParameters(): array
    {
        return $this->whereClause->getParameters();
    }
}