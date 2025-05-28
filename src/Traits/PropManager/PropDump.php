<?php
namespace Game\Traits\PropManager;

use ReflectionClass;
use ReflectionProperty;
use Game\Traits\PropManager\PropType;

trait PropDump {
    /**
     * Dumps an object and all its nested objects into a JSON structure
     * that preserves type information and null values.
     * 
     * @return string JSON representation of the object
     */
    private function dump(): string {
        return json_encode($this->dumpObject($this));
    }

    /**
     * Restores an object from its JSON dump
     * 
     * @param string $json The JSON dump to restore from
     * @return object The restored object
     */
    private function restore(string $json): object {
        $data = json_decode($json, true);
        return $this->restoreObject($data);
    }

    /**
     * Internal method to recursively dump an object and its properties
     */
    private function dumpObject(object $obj): array {
        $reflection = new ReflectionClass($obj);
        $data = [
            '__class' => $reflection->getName(),
            'properties' => []
        ];

        // Get all properties including private ones from parent classes
        $properties = [];
        do {
            $classProperties = $reflection->getProperties(
                ReflectionProperty::IS_PUBLIC | 
                ReflectionProperty::IS_PROTECTED | 
                ReflectionProperty::IS_PRIVATE
            );
            foreach ($classProperties as $prop) {
                $prop->setAccessible(true);
                $properties[$prop->getName()] = $prop;
            }
        } while ($reflection = $reflection->getParentClass());

        // Process each property
        foreach ($properties as $name => $prop) {
            try {
                $value = $prop->getValue($obj);
                $type = $prop->getType();
                
                $propertyData = [
                    'name' => $name,
                    'type' => $type ? $type->getName() : 'mixed',
                    'value' => null
                ];

                if ($value !== null) {
                    if (is_object($value)) {
                        // Handle nested objects
                        if ($value instanceof \UnitEnum) {
                            $propertyData['value'] = [
                                'name' => $value->name,
                                'value' => property_exists($value, 'value') ? $value->value : null
                            ];
                        } else {
                            $propertyData['value'] = $this->dumpObject($value);
                        }
                    } else {
                        $propertyData['value'] = $value;
                    }
                }

                $data['properties'][$name] = $propertyData;
            } catch (\ReflectionException $e) {
                continue;
            }
        }

        return $data;
    }

    /**
     * Internal method to recursively restore an object from dumped data
     */
    private function restoreObject(array $data): object {
        $className = $data['__class'];
        if (!class_exists($className)) {
            throw new \RuntimeException("Cannot restore class $className - class not found");
        }

        // Create new instance without calling constructor
        $reflection = new ReflectionClass($className);
        $instance = $reflection->newInstanceWithoutConstructor();

        foreach ($data['properties'] as $name => $propertyData) {
            try {
                $property = $reflection->getProperty($name);
                $property->setAccessible(true);

                $value = $propertyData['value'];
                if (is_array($value) && isset($value['__class'])) {
                    // Restore nested object
                    $value = $this->restoreObject($value);
                } elseif (is_array($value) && isset($value['name']) && $propertyData['type'] === 'UnitEnum') {
                    // Restore enum
                    $enumClass = $propertyData['type'];
                    $value = constant("$enumClass::{$value['name']}");
                }

                $property->setValue($instance, $value);
            } catch (\ReflectionException $e) {
                continue;
            }
        }

        return $instance;
    }

    /**
     * Dumps all properties as an array
     * @return array
     */
    public function dumpProps(): array {
        $props = get_object_vars($this);
        return array_filter($props, function($key) {
            return !str_starts_with($key, '_');
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Dumps a specific property value
     * @param string $propName
     * @return mixed
     */
    public function dumpProp(string $propName) {
        return $this->$propName ?? null;
    }
}
?>
