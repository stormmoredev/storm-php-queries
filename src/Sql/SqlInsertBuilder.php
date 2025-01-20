<?php

namespace Storm\Query\Sql;

class SqlInsertBuilder
{
    private string $tableName;
    private array $values = [];

    public function into(string $tableName): SqlInsertBuilder
    {
        $this->tableName = $tableName;
        return $this;
    }

    public function record(array $values): SqlInsertBuilder
    {
        $this->values[] = $values;
        return $this;
    }

    public function toSql(): string
    {
        if (count($this->values)) {
            $columns = "(" . implode(', ', array_keys($this->values[0])) . ")";

            $parameters = [];
            foreach ($this->values as $v) {
                $parameters[] = "(" . str_repeat('?,', count($this->values[0]) - 1) . '?' . ")";
            }
            $valuesClause = implode(', ', $parameters);

            return "INSERT INTO {$this->tableName} {$columns} VALUES {$valuesClause}";
        }
        return "";
    }

    public function getParameters(): array
    {
        $records = [];
        foreach($this->values as $record) {
            $records = array_merge($records, array_values($record));
        }
        return $records;
    }
}