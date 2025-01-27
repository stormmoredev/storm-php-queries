<?php

namespace Storm\Query\Queries;

class SubQuery
{
    public static function create(SelectQuery $selectQuery, $alias): SubQuery
    {
        return new SubQuery($selectQuery, $alias);
    }

    public function __construct(public SelectQuery $query, public string $alias)
    {
    }
}