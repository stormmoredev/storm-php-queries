<?php

namespace Stormmore\Queries\Queries;

use Exception;
use InvalidArgumentException;
use Stormmore\Queries\Mapper\Map;
use Stormmore\Queries\Mapper\Mapper;

class QueryMapper
{
    private ?Map $from = null;

    public function addJoinMap(Map $map, $l, $r): void
    {
        $this->from?->getTable()->hasAlias() or throw new InvalidArgumentException("Join table {$map->getTable()->table} doesn't have alias");
        $map->getTable()->hasAlias() or throw new InvalidArgumentException("Join table {$map->getTable()->table} doesn't have alias");
        $alias = "";
        list($lAlias,) = explode('.', trim($l));
        list($rAlias,) = explode('.', trim($r));
        if ($lAlias == $map->getTable()->alias) {
            $alias = $rAlias;
        }
        if ($rAlias == $map->getTable()->alias) {
            $alias = $lAlias;
        }
        if ($map->isSelectMap()) {
            $this->from->addMapColumns($map);
        } else {
            $this->addMapToParentMapByAlias($map, $alias);
        }
    }

    private function addMapToParentMapByAlias(Map $map, string $alias): void
    {
        $found = $this->findMapByAlias($alias);
        if ($found->isPlainJoin()) {
            $alias = $found->getTable()->alias;
            $found = $this->findJoinParent($alias);
            $this->addMapToParentMapByAlias($map, $found->getTable()->alias);
        }
        else {
            $found->addJoinMap($map);
        }
    }

    private function findJoinParent(string $alias): Map
    {
        foreach($this->from->getAllMaps() as $parent) {
            foreach($parent->getPlainJoins() as $join) {
                if ($join->getTable()->alias == $alias) {
                    return $parent;
                }
            }
        }

        throw new InvalidArgumentException("Alias {$alias} doesn't refers any join table");
    }

    private function findMapByAlias(string $alias): Map
    {
        foreach($this->from->getAllMaps() as $m) {
            if ($m->getTable()->alias == $alias) {
                return $m;
            }
        }

        throw new Exception("Alias `{$alias}` has no mapped table");
    }

    public function addFromMap(Map $map): void
    {
        !$map->isRelationshipMap() or throw new Exception("Use Map::create to map root table");
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