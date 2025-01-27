<?php

namespace Storm\Query\Sql\Clauses;

use Storm\Query\Queries\SubQuery;

class JoinClause
{
    private array $joins = [];

    public function addLeftJoin(string $type, string|SubQuery $set, string $columnA, string $columnB): void
    {
        $this->joins[] = [$type, $set, $columnA, $columnB];
    }

    public function hasJoines(): bool
    {
        return count($this->joins);
    }

    public function toString(): string
    {
        if (!count($this->joins)) {
            return "";
        }

        $joins = [];
        foreach($this->joins as $join) {
            $type = strtoupper($join[0]);
            $joinString = "LEFT ";
            if ($type == "OUTER") {
                $joinString .= "OUTER ";
            }
            $joinString .= "JOIN ";
            if ($join[1] instanceof SubQuery) {
                $joinString .= "(" . $join[1]->query->getSql() . ") " . $join[1]->alias . " ON $join[2] = $join[3]";
            }
            else {
                $joinString .= "$join[1] ON $join[2] = $join[3]";
            }
            $joins[] = $joinString;
        }
        return implode("\n", $joins);
    }
}