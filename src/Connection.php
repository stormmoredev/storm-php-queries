<?php

namespace Stormmore\Queries;

use DateTime;
use Exception;
use PDO;
use PDOStatement;

class Connection implements IConnection
{

    private string $connectionString;
    private array $successCallback = [];
    private array $failCallbacks = [];
    private PDO $pdo;
    private string $sqlDialect;

    public function __construct(string $connection, string $username, string $password)
    {
        list($this->sqlDialect) = explode(':', $connection);
        $this->sqlDialect = strtolower($this->sqlDialect);
        $this->connectionString = $connection;
        $this->pdo = new PDO($connection, $username, $password);
    }

    public function begin(): void
    {
         $this->pdo->beginTransaction();
    }

    public function commit(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->commit();
        }
    }

    public function rollback(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollback();
        }
    }

    public function onSuccess(callable $callback): void
    {
        $this->successCallback[] = $callback;
    }

    public function onFailure(callable $callback): void
    {
        $this->failCallbacks[] = $callback;
    }

    public function query(string $query, array $parameters = []): array
    {
        $started = new DateTime();
        try
        {
            $stmt = $this->pdo->prepare($query);
            $this->bindValues($stmt, $parameters);
            $stmt->execute();
            $result = [];
            while ($row = $stmt->fetchObject()) {
                $result[] = $row;
            }
            $this->runOnSuccess($query, $started);
            return $result;
        }
        catch(Exception $exception) {
            $this->runOnFailure($query, $started, $exception);
            throw $exception;
        }
    }

    public function execute(string $query, array $parameters = []): bool
    {
        $started = new DateTime();
        try
        {
            $stmt = $this->pdo->prepare($query);
            $this->bindValues($stmt, $parameters);
            $result = $stmt->execute($parameters);
            $this->runOnSuccess($query, $started);
            return $result;
        }
        catch(Exception $exception) {
            $this->runOnFailure($query, $started, $exception);
            throw $exception;
        }
    }

    public function getLastInsertedId(): int
    {
        return $this->pdo->lastInsertId();
    }

    public function executeCommands($content): void
    {
        $started = new DateTime();
        try {
            $this->pdo->exec($content);
            $this->runOnSuccess("...", $started);
        }
        catch(Exception $exception) {
            $this->runOnFailure("...", $started, $exception);
            throw $exception;
        }
    }

    private function runOnSuccess(string $sql, DateTime $started): void
    {
        $interval = date_diff(new DateTime(), $started);
        foreach ($this->successCallback as $callback) {
            $callback($sql, $interval);
        }
    }

    private function runOnFailure(string $sql, DateTime $started, Exception $exception): void
    {
        $interval = date_diff(new DateTime(), $started);
        foreach ($this->failCallbacks as $callback) {
            $callback($sql, $interval, $exception);
        }
    }

    private function bindValues(PDOStatement $statement, array $parameters): void
    {
        foreach ($parameters as $index => $value) {
            if (is_int($value)) {
                $type = PDO::PARAM_INT;
            }
            else if (is_bool($value)) {
                $type = PDO::PARAM_BOOL;
            }
            else {
                $type = PDO::PARAM_STR;
            }

            $statement->bindValue($index + 1, $value, $type);
        }
    }

    public function getDatabaseType(): string
    {
        return substr($this->connectionString, 0, strpos($this->connectionString, ':'));
    }

    function getSqlDialect(): string
    {
        return $this->sqlDialect;
    }
}