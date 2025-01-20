<?php

namespace Storm\Query\Queries;

use Storm\Query\IConnection;
use Storm\Query\Sql\SqlDeleteBuilder;

class DeleteQuery
{
    private SqlDeleteBuilder $deleteBuilder;

    public function __construct(private readonly IConnection $connection)
    {
        $this->deleteBuilder = new SqlDeleteBuilder();
    }

    public function from(string $table): DeleteQuery
    {
        $this->deleteBuilder->from($table);
        return $this;
    }
    public function whereString(string $whereCondition, array $parameters): DeleteQuery
    {
        $this->deleteBuilder->whereString($whereCondition, $parameters);
        return $this;
    }
    public function where(): DeleteQuery
    {
        call_user_func_array([$this->deleteBuilder, 'where'], func_get_args());
        return $this;
    }

    public function orWhere(): DeleteQuery
    {
        call_user_func_array([$this->deleteBuilder, 'orWhere'], func_get_args());
        return $this;
    }

    public function getSql(): string
    {
        return $this->deleteBuilder->toSql();
    }

    public function getParameters(): array
    {
        return $this->deleteBuilder->getParameters();
    }

    public function execute(): void
    {
        $this->connection->execute($this->getSql(), $this->getParameters());
    }
}