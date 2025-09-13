<?php

namespace Stormmore\Queries\Sql\Clauses;

use Stormmore\Queries\Queries\SubQuery;

class JoinClause
{
    private array $joins = [];

    public function addJoin(string $type, string|SubQuery $set, string|array $columns): void
    {
        $this->joins[] = [$type, $set, $columns];
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
            $joinString = "JOIN ";
            if ($type == "LEFT") {
                $joinString = "LEFT JOIN ";
            }
            if ($join[1] instanceof SubQuery) {
                $joinString .= "(" . $join[1]->query->getSql() . ") " . $join[1]->alias . " ON " . $this->toOnClause($join[2]);
            }
            else {
                $joinString .= "$join[1] ON " . $this->toOnClause($join[2]);
            }
            $joins[] = $joinString;
        }
        return implode("\n", $joins);
    }

    private function toOnClause(string|array $columns): string
    {
        if (is_string($columns)) {
            return $columns;
        }

        $onClause = [];
        foreach($columns as $l => $r) {
            $onClause[] = "$l = $r";
        }
        return implode(" AND ", $onClause);
    }
}