<?php

namespace Stormmore\Queries\Queries;

use Stormmore\Queries\IConnection;
use Stormmore\Queries\ParameterNormalizer;
use Stormmore\Queries\Sql\SqlInsertBuilder;

class InsertQuery
{
    private SqlInsertBuilder $insertBuilder;

    public function __construct(private readonly IConnection $connection)
    {
        $this->insertBuilder = new SqlInsertBuilder();
    }

    public function into(string $table): InsertQuery
    {
        $this->insertBuilder->into($table);
        return $this;
    }

    public function setRecord(array $values): InsertQuery
    {
        $normalized = ParameterNormalizer::normalize($values);
        $this->insertBuilder->record($normalized);
        return $this;
    }

    public function setRecords(array $records): InsertQuery
    {
        foreach($records as $record){
            $this->setRecord($record);
        }
        return $this;
    }

    public function getSql(): string
    {
        return $this->insertBuilder->toSql();
    }

    public function getParameters(): array
    {
        return $this->insertBuilder->getParameters();
    }

    public function execute($returnLastInsertedId = true): int
    {
        $this->connection->execute($this->getSql(), $this->getParameters());
        if ($returnLastInsertedId) {
            return $this->connection->getLastInsertedId();
        }
        return 0;
    }

    public function getLastInsertedId(): int
    {
        return $this->connection->getLastInsertedId();
    }

    public function getLastInsertedIdAsString(): string
    {
        return $this->connection->getLastInsertedId();
    }
}