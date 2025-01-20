<?php

namespace Storm\Query\Sql\Clauses;

use InvalidArgumentException;
use Storm\Query\Queries\SelectQuery;

class BoolStatement
{
    private array $parameters = [];
    private string $statement;
    private array $operators = [
        'like',
        '=', '>=', '>', '<', '<=', '<>',
        'not', 'in', 'not in',
        'between',
        'is null', 'is not null'];

    public function __construct(array $arguments)
    {
        $this->statement = $this->buildStatement($arguments);
    }

    private function buildStatement($arguments): string
    {
        $count = count($arguments);
        $field = $arguments[0];
        if ($count == 2) {
            $op_upper = strtoupper($arguments[1]);
            if (in_array($op_upper, ['IS NULL', 'IS NOT NULL'])) {
                return "$field $op_upper";
            }
            $this->pushParameter($arguments[1]);
            return "$field = ?";
        }
        $op = $arguments[1];
        $op_upper = strtoupper($op);
        $op_lower = strtolower($op);
        $parameter = $arguments[2];
        in_array($op_lower, $this->operators) or throw new InvalidArgumentException("Invalid operator $op");

        if ($op_lower == 'not') {
            $this->pushParameter($arguments[2]);
            return "NOT $field = ?";
        }
        if ($op_lower === 'between') {
            ($count > 3) or throw new InvalidArgumentException("Between statements should have proceeding 2 parameters");
            $this->pushParameter($parameter);
            $this->pushParameter($arguments[3]);
            return "$field BETWEEN ? AND ?";
        }
        if ($op_lower == 'not in' || $op_lower == 'in') {
            if (is_array($parameter)) {
                $marks = str_repeat('?,', count($parameter) - 1) . '?';
                $this->pushParameter($parameter);
                return $field . " $op_upper ".  "(" . $marks . ")";
            }
            if ($parameter instanceof SelectQuery) {
                $this->pushParameter($parameter->getParameters());
                return $field . " $op " . "(" . $parameter->getSql() . ")";
            }
            throw new InvalidArgumentException("After IN or NOT IN array or SelectQuery is expected");
        }

        if ($parameter instanceof SelectQuery) {
            $this->pushParameter($parameter->getParameters());
            return $field . " $op " . '(' . $parameter->getSql() . ')';
        }

        $this->pushParameter($parameter);
        return "$field $op_upper ?";
    }

    private function pushParameter($parameter): void
    {
        if (is_array($parameter)) {
            $this->parameters = array_merge($this->parameters, $parameter);
        }
        else {
            $this->parameters[] = $parameter;
        }
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function toString() : string
    {
        return $this->statement;
    }
}