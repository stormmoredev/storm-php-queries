<?php

namespace Stormmore\Queries\Queries;

use Stormmore\Queries\IConnection;
use Stormmore\Queries\ParameterNormalizer;

class UnionQuery
{
    private array $selects;

    public function __construct(private readonly IConnection $connection, array $selects)
    {
        $this->selects = $selects;
    }

    public function find(): array
    {
        $sql = $this->getSql();
        $params = [];
        foreach($this->selects as $select) {
            $params = array_merge($params, ParameterNormalizer::normalize($select->getParameters()));
        }
        return $this->connection->query($sql, $params);
    }

    public function getSql(): string
    {
        $queries = [];
        foreach ($this->selects as $query) {
            $queries[] = $query->getSql();
        }
        return implode(PHP_EOL . 'UNION' . PHP_EOL, $queries);
    }
}