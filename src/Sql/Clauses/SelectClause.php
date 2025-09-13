<?php

namespace Stormmore\Queries\Sql\Clauses;

use InvalidArgumentException;

class SelectClause
{
    private array $columns = [];

    public function clear(): void
    {
        $this->columns = [];
    }

    public function add(array $parameters): void
    {
        foreach($parameters as $key => $parameter) {
            if (is_string($parameter)) {
                if (is_numeric($key)) {
                    $this->columns[] = $parameter;
                }
                else {
                    $this->columns[] = "$key as \"$parameter\"";
                }

            }
            if (is_array($parameter)) {
                $this->add($parameter);
            }
        }
    }

    public function toString(): string
    {
        count($this->columns) or throw new InvalidArgumentException("Columns must not be empty");

        return "SELECT " . implode(", ", $this->columns);
    }
}