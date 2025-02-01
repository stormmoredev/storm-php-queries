<?php

namespace Storm\Query\Mapper;

use InvalidArgumentException;
use Storm\Query\Table;

class Map
{
    private Table $table;
    private array $columns = [];
    private ?string $className = null;
    private ?string $id = null;
    /**
     * @var Map[]
     */
    private array $oneToMany = [];
    /**
     * @var Map[]
     */
    private array $oneToOne = []
    /**
     * @var Map[]
     */;
    private array $plainJoins = [];
    private string $type = "";
    private string $property = "";

    public static function from(array $columns = [], string $class = 'stdClass', string $classId = 'id'): Map
    {
        $map = [];
        $map['columns'] = $columns;
        $map['class'] = $class;
        $map['id'] = $classId;
        return self::createFromArray($map);
    }

    public static function join(): Map
    {
        $map = [];
        $map['type'] = 'join';
        return self::createFromArray($map);
    }

    public static function many(string $property, array $columns, string $class = 'stdClass', string $classId = 'id'): Map
    {
        $map = [];
        $map['type'] = 'many';
        $map['property'] = $property;
        $map['columns'] = $columns;
        $map['class'] = $class;
        $map['id'] = $classId;
        return self::createFromArray($map);
    }

    public static function one(string $property, array $columns, string $class = 'stdClass', string $classId = 'id'): Map
    {
        $map = [];
        $map['type'] = 'one';
        $map['property'] = $property;
        $map['columns'] = $columns;
        $map['class'] = $class;
        $map['id'] = $classId;
        return self::createFromArray($map);
    }

    private static function createFromArray(array $map): Map
    {
        $name = self::getArrayValueOrDefault($map, 'table');
        $id = self::getArrayValueOrDefault($map, 'id');
        $class = self::getArrayValueOrDefault($map, 'class', 'stdClass');
        $columns = self::getArrayValueOrDefault($map, 'columns', []);
        $type = self::getArrayValueOrDefault($map, 'type', "");
        $property = self::getArrayValueOrDefault($map, 'property', "");

        class_exists($class) or throw new InvalidArgumentException("Class '$class' does not exist");

        $map = new Map();
        if ($name) {
            $map->setTable(new Table($name));
        }
        if ($property) {
            $map->property = $property;
        }
        $map->id = $id;
        $map->columns = $columns;
        $map->className = $class;
        $map->type = $type;

        return $map;
    }

    public function isRelationship(): bool
    {
        return $this->type === "one" or $this->type === 'many' or $this->type === 'join';
    }

    public function isPlainJoin(): bool
    {
        return $this->type === "join";
    }

    public function addRelationshipMap(Map $map): void
    {
        if ($map->type === "many") {
            $this->oneToMany[] = $map;
        }
        if ($map->type === "one") {
            $this->oneToOne[] = $map;
        }
        if ($map->type === "join") {
            $this->plainJoins[] = $map;
        }
    }

    public function setTable(Table $table): Map
    {
        $this->table = $table;
        return $this;
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getColumnsMap(): ColumnsMap
    {
        return new ColumnsMap($this->columns, $this->getTable());
    }

    /**
     * @return Map[]
     */
    public function getOneToOne(): array
    {
        return $this->oneToOne;
    }

    /**
     * @return Map[]
     */
    public function getOneToMany(): array
    {
        return $this->oneToMany;
    }

    /**
     * @return Map[]
     */
    public function getPlainJoins(): array
    {
        return $this->plainJoins;
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    /**
     * @return Map[]
     */
    public function getAllMaps(): array
    {
        return $this->findRecursivelyMaps($this);
    }

    /**
     * @param Map $map
     * @return Map[]
     */
    private function findRecursivelyMaps(Map $map): array
    {
        $maps = [];
        $maps[] = $map;

        foreach($this->oneToMany as $key => $oneToManyMap) {
            $maps = array_merge($maps, $oneToManyMap->findRecursivelyMaps($oneToManyMap));
        }

        foreach($this->oneToOne as $key => $oneToOneMap) {
            $maps = array_merge($maps, $oneToOneMap->findRecursivelyMaps($oneToOneMap));
        }

        foreach($this->plainJoins as $key => $joinMap) {
            $maps = array_merge($maps, $joinMap->findRecursivelyMaps($joinMap));
        }
        return $maps;
    }

    private static function getArrayValueOrDefault(array $array, mixed $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }
        return $default;
    }
}