<?php

namespace Storm\Query\Sql\Clauses;

class JoinClause
{
    private array $joins = [];

    public function addLeftJoin(string $type, string $table, string $columnA, string $columnB): void
    {
        $this->joins[] = [$type, $table, $columnA, $columnB];
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
            $joinString .= "$join[1] ON $join[2] = $join[3]";
            $joins[] = $joinString;
        }
        return implode("\n", $joins);
    }
}