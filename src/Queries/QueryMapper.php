<?php

namespace Storm\Query\Queries;

use Exception;
use Storm\Query\Mapper\Map;
use Storm\Query\Mapper\Mapper;

class QueryMapper
{
    private ?Map $from = null;

    public function addRelationshipMap(Map $map, $l, $r): void
    {
        $map->isRelationship() or throw new Exception("Map doesn't describe relationship. Use Map::many or Map::one");
        $alias = "";
        list($lAlias,) = explode('.', trim($l));
        list($rAlias,) = explode('.', trim($r));
        if ($lAlias == $map->getTable()->alias) {
            $alias = $rAlias;
        }
        if ($rAlias == $map->getTable()->alias) {
            $alias = $lAlias;
        }
        foreach($this->from->getAllMaps() as $m) {
            if ($m->getTable()->alias == $alias) {
                $m->addRelationshipMap($map);
            }
        }
    }

    public function addFromMap(Map $map): void
    {
        $this->from  = $map;
    }

    public function getFromMap(): ?Map
    {
        return $this->from;
    }

    public function hasJoin(): bool
    {
        return count($this->from->getOneToMany()) or count($this->from->getOneToOne());
    }

    public function getSelect(): array
    {
        $columns = [];
        foreach ($this->from->getAllMaps() as $map) {
            foreach($map->getColumnsMap()->getColumns() as $column) {
                $columns[] = $column->getColumnAliasExpression();
            }
        }
        if (empty($columns)) {
            return ["*"];
        }
        return $columns;
    }

    public function mapResults(array $results): array
    {
        if ($this->hasJoin()) {
            return Mapper::mapJoin($results, $this->getFromMap());
        }
        else {
            return Mapper::mapSingle($results, $this->getFromMap());
        }
    }

    public function hasMaps(): bool
    {
        return $this->from != null;
    }
}