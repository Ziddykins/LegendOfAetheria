<?php
namespace Game\Traits\PropSuite;

use ReflectionClass;
use ReflectionProperty;
use ReflectionException;

/**
 * Provides deep serialization and deserialization for game objects.
 * Handles nested objects, enums, and complex property types while preserving type information.
 * 
 * Used for:
 * - Saving complex objects to database TEXT/JSON columns
 * - Session storage of game state
 * - Deep copying objects with nested structures
 * 
 * Supports:
 * - Nested objects (recursive serialization)
 * - Enums (both unit and backed enums)
 * - Null values
 * - Private/protected properties from parent classes
 */
trait PropDump { 
    /**
     * Serializes this object and all nested objects to JSON.
     * Preserves type information and handles circular references.
     * 
     * @return string JSON representation with type metadata
     */
    private function propDump(): string {
        return json_encode($this->dumpObject($this));
    }

    /**
     * Restores an object from its JSON dump
     * 
     * @param string $json The JSON dump to restore from
     * @return object The restored object
     */
    private function propRestore($json): object|null {
        if (!$json) {
            return null;
        }

        $data = json_decode($json, true);
        return $this->restoreObject($data);
    }

    /**
     * Internal recursive method to dump an object tree.
     * Extracts all properties (including private from parents) and recursively serializes nested objects.
     * 
     * @param object $obj Object to dump
     * @return array Associative array with __class and properties keys
     */
    private function dumpObject(object $obj): array {
        global $log;
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
            } catch (ReflectionException $e) {
                $log->error("PropDump: Failed to dump property $name - " . $e->getMessage());
            }
        }

        return $data;
    }

    /**
     * Internal method to recursively restore an object from dumped data
     */
    private function restoreObject(array $data): object|null {
        if (!$data) {
            return null;
        }

        $className = $data['__class'];

        if (!class_exists($className)) {
            throw new \RuntimeException("Cannot restore class $className - class not found");
        }

        // Create new instance without calling constructor
        $reflection = new ReflectionClass($className);
        $instance   = $reflection->newInstanceWithoutConstructor();

        foreach ($data['properties'] as $name => $propertyData) {
            try {
                $property = $reflection->getProperty($name);
                $property->setAccessible(true);

                $value = $propertyData['value'];
                if (is_array($value) && isset($value['__class'])) {
                    // Restore nested object
                    $value = $this->restoreObject($value);
                } elseif (is_array($value) && isset($value['name'])) {
                    $propertyType = $property->getType();
                    if ($propertyType && enum_exists($propertyType->getName())) {
                        // Restore enum using the actual enum class from property type
                        $enumClass = $propertyType->getName();
                        $value = constant("$enumClass::{$value['name']}");
                    }
                }

                $property->setValue($instance, $value);
            } catch (ReflectionException $e) {
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
