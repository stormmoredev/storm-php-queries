<?php

namespace Stormmore\Queries\Mapper;

use AllowDynamicProperties;
use DateTime;
use ReflectionClass;

class ObjectReflection
{
    private ReflectionClass $reflection;
    private object $object;

    public function __construct($object)
    {
        $this->reflection = new ReflectionClass($object);
        $this->object = $object;
    }

    public function isInitialized($field): bool
    {
        if ($this->reflection->hasProperty($field)) {
            $property = $this->reflection->getProperty($field);
            if ($property->hasType()) {
                return $property->isInitialized($this->object);
            }
            else
            {
                return $property->getValue($this->object) != null;
            }
        }
        else if (property_exists($this->object, $field)) {
            return true;
        }
        return false;
    }

    public function setProperty(string $propName, mixed $propValue): void
    {
        if ($this->reflection->hasProperty($propName)) {
            $property = $this->reflection->getProperty($propName);
            $typeProperty = $property->hasType() ? $property->getType()->getName() : null;
            if ($typeProperty == 'DateTime') {
                $this->reflection->getProperty($propName)->setValue($this->object, new DateTime($propValue));
            }
            else {
                $this->reflection->getProperty($propName)->setValue($this->object, $propValue);
            }
        }
        else
        {
            if ($this->canDynamicallyCreateProp()) {
                $this->object->$propName = $propValue;
            }

        }
    }

    private function canDynamicallyCreateProp(): bool
    {
        if (!class_exists("AllowDynamicProperties")) {
            return true;
        }
        $attributes = $this->reflection->getAttributes(AllowDynamicProperties::class);
        return !empty($attributes);
    }
}