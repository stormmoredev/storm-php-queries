<?php

namespace Stormmore\Queries;

interface IConnection
{
    function begin(): void;

    public function commit(): void;

    public function rollback(): void;

    public function query(string $query, array $parameters = []): array;
    public function execute(string $query, array $parameters = []): bool;

    public function getLastInsertedId(): int;

    public function executeCommands($content): void;

    public function getDatabaseType(): string;

    public function onSuccess(callable $callback): void;

    public function onFailure(callable $callback): void;

    function getSqlDialect();
}