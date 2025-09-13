<?php

namespace Stormmore\Queries;

use InvalidArgumentException;
use Stormmore\Queries\Queries\SubQuery;

class Table
{
    public string $expression;
    public string $table;
    public string $alias = "";

    public function __construct(string|SubQuery $expression)
    {
        if ($expression instanceof SubQuery) {
            $this->alias = $expression->alias;
            return;
        }
        $this->expression = $expression = trim($expression);
        $spaces = substr_count($expression, ' ');
        $spaces < 2 or throw new InvalidArgumentException("Invalid table name '$this->expression'");
        if ($spaces === 0) {
            $this->table = $expression;
        }
        if ($spaces === 1) {
            list($this->table, $this->alias) = explode(' ', $expression);
        }
    }

    public function getPrefix(): string
    {
        return $this->hasAlias() ? $this->alias : $this->table;
    }

    public function hasAlias(): bool
    {
        return !empty($this->alias);
    }
}