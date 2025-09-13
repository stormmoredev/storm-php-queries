<?php

namespace Stormmore\Queries\Sql\Clauses;

use Stormmore\Queries\Sql\SqlSelectBuilder;

class LimitOffsetClause
{
    private ?int $limitRecords = null;
    private ?int $offsetRecords = null;

    public function __construct(readonly private ?string $sqlDialect)
    {
    }

    public function setLimitOffset(int $limit, int $offset): void
    {
        $this->limitRecords = $limit;
        $this->offsetRecords = $offset;
    }

    public function toString(): string
    {
        if ($this->limitRecords === null or $this->offsetRecords === null) {
            return "";
        }

        if ($this->sqlDialect == 'sqlsrv') {
            return "OFFSET $this->offsetRecords ROWS FETCH NEXT $this->limitRecords ROWS ONLY";
        } else {
            return "LIMIT $this->limitRecords OFFSET $this->offsetRecords";
        }
    }
}