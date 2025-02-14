<?php

namespace Stormmore\Queries\Mapper;

class ColumnDescription
{
    public string $alias;

    private string $expression;

    public function __construct(public string $name, public string $fieldName, public string $prefix = "")
    {
        if ($this->isPrefixedWithTableAlias($name)) {
            $this->expression = $name;
            $this->alias = str_replace('.', '_', $name);
        }
        else {
            $this->alias = $prefix . '_' . $name;
            $this->expression = $this->prefix . '.' . $this->name;
        }
    }

    private function isPrefixedWithTableAlias($name): bool
    {
        return str_contains($name, '.');
    }

    public function getColumnAliasExpression(): string
    {
        return $this->expression . ' as ' . $this->alias;
    }
}