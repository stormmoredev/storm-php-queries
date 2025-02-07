<?php

namespace Stormmore\Queries\Mapper;

use Stormmore\Queries\Table;

class ColumnsMap
{
    private Table $table;
    /**
     * @var ColumnDescription[]
     */
    private array $columns = [];

    public function __construct(array $columns, Table $table)
    {
        $this->table = $table;

        foreach($columns as $columnName => $fieldName) {
            $this->columns[] = new ColumnDescription($columnName, $fieldName, $this->table->getPrefix());
        }
    }

    public function isEmpty(): bool
    {
        return empty($this->columns);
    }

    public function getColumnByField(string $fieldName) : ?ColumnDescription
    {
        foreach ($this->columns as $columnDescription) {
            if ($columnDescription->fieldName === $fieldName) {
                return $columnDescription;
            }
        }
        return null;
    }


    /**
     * @return ColumnDescription[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }
}