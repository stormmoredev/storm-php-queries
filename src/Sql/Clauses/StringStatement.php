<?php

namespace Stormmore\Queries\Sql\Clauses;

readonly class StringStatement
{
    public function __construct(private string $condition, private array $parameters)
    {
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function toString() : string
    {
        return $this->condition;
    }
}