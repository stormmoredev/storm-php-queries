<?php

namespace Stormmore\Queries\Queries;

use Exception;
use InvalidArgumentException;
use Stormmore\Queries\Mapper\Map;
use Stormmore\Queries\Mapper\Mapper;

class QueryMapper
{
    private ?Map $from = null;

    public function addJoinMap(Map $map, string|array $on): void
    {
        $this->from?->getTable()->hasAlias() or throw new InvalidArgumentException("Join table {$map->getTable()->table} doesn't have alias");
        $map->getTable()->hasAlias() or throw new InvalidArgumentException("Join table {$map->getTable()->table} doesn't have alias");
        if ($map->isSelectMap()) {
            $this->from->addMapColumns($map);
            return;
        }

        $alias = $this->getJoinedTableAlias($map, $on);

        $this->addMapToParentMapByAlias($map, $alias);
    }

    private function getJoinedTableAlias(Map $map, string|array $on): string
    {
        if (is_string($on)) {
            $onAsArray = [];
            $conditions = preg_split("/\s(and|or)\s/i", $on);
            foreach($conditions as $condition) {
                list($l, $r) = explode('=', $condition);
                $l = trim($l);
                $r = trim($r);
                $onAsArray[$l] = $r;
            }
            $on = $onAsArray;
        }

        foreach($on as $l => $r) {
            if (str_contains($r, '.') and str_contains($l, '.')) {
                list($lAlias,) = explode('.', trim($l));
                list($rAlias,) = explode('.', trim($r));
                return $map->getTable()->alias == $lAlias  ? $rAlias : $lAlias;
            }
        }
        throw new Exception("Alias for {$map->getTable()->expression} not found.");
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