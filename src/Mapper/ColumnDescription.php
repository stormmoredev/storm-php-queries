<?php

namespace Storm\Query\Mapper;

class ColumnDescription
{
    public string $alias;

    public function __construct(public string $name, public string $fieldName, public string $prefix = "")
    {
        $this->alias = $prefix . '_' . $name;
    }

    public function getColumnAliasExpression(): string
    {
        return $this->prefix . '.' . $this->name . ' as ' . $this->alias;
    }
}