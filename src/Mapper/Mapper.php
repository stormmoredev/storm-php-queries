<?php

namespace Stormmore\Queries\Mapper;

use Exception;
use ReflectionClass;
use ReflectionException;

class Mapper
{
    public static function mapJoin(array $records, Map $map): array
    {
        if (!count($records)) {
            return $records;
        }

        $array = [];
        foreach($records as $record) {
            self::mapMany($array, $record, $map);
        }
        return $array;
    }

    private static function mapMany(array &$array, $record, Map $map): void
    {
        $columnMap = $map->getColumnsMap();
        $id = $map->getId();
        $pkColumn = $columnMap->getColumnByField($id);
        $pkColumn or throw new Exception("Id field '$id' doesn't exist");
        property_exists($record, $pkColumn->alias) or throw new Exception("Id column'$pkColumn->name' does not exist in result set");

        $value = $record->{$pkColumn->alias};
        if ($value == null) {
            return;
        }

        $object = self::getByPk($array, $pkColumn->fieldName, $value);
        if ($object == null) {
            $object = self::createObject($map->getClassName());
            $array[] = $object;
        }

        self::mapObject($object, $record, $map);
    }

    private static function mapObject(object $object, $record, Map $map): void
    {
        $reflection = new ObjectReflection($object);
        self::copyColumnsToFields($reflection, $record, $map);
        foreach($map->getOneToMany() as $manyMap) {
            if (!$reflection->isInitialized($manyMap->getProperty())) {
                $object->{$manyMap->getProperty()} = array();
            }
            self::mapMany($object->{$manyMap->getProperty()}, $record, $manyMap);
        }

        foreach($map->getOneToOne() as $oneMap) {
            if (!$reflection->isInitialized($oneMap->getProperty())) {
                $object->{$oneMap->getProperty()} = self::createObject($oneMap->getClassName());
            }
            self::mapObject($object->{$oneMap->getProperty()}, $record, $oneMap);
        }
    }

    public static function mapSingle(array $records,  Map $map): array
    {
        $array = [];
        foreach($records as $record) {
            $object = self::createObject($map->getClassName());
            $array[] = $object;
            $reflection = new ObjectReflection($object);
            self::copyColumnsToFields($reflection, $record, $map);
        }
        return $array;
    }

    private static function copyColumnsToFields(ObjectReflection $reflection, object $record, Map $map): void
    {
        $columnMap = $map->getColumnsMap();
        if (!$columnMap->isEmpty()) {
            foreach($columnMap->getColumns() as $column) {
                $reflection->setProperty($column->fieldName, $record->{$column->alias});
            }
        }
        else {
            foreach(get_object_vars($record) as $name => $value) {
                $reflection->setProperty($name, $value);
            }
        }
    }

    private static function getByPk(array $mapped, $column, $value): ?object
    {
        foreach ($mapped as $record) {
            if ($record->$column == $value) {
                return $record;
            }
        }

        return null;
    }

    private static function createObject(string $className): object
    {
        try {
            $reflect  = new ReflectionClass($className);
        }
        catch (ReflectionException $exception){
            throw new Exception("Class '$className' does not exist");
        }

        return $reflect->newInstance();
    }
}