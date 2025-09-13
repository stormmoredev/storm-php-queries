<?php

namespace Stormmore\Queries\Sql\Clauses;

class OrderByClause
{
    private array $clauses = [];

    public function add(string $column, int $direction): void
    {
        $this->clauses[] = [$column, $direction];
    }

    public function toString(): string
    {
        $columns = [];
        foreach ($this->clauses as $clause) {
            $direction = $clause[1] > 0 ? "ASC": "DESC";
            $columns[] = $clause[0] . ' ' . $direction;
        }
        if (!count($columns)) {
            return "";
        }

        return "ORDER BY " . implode(", ", $columns);
    }
}