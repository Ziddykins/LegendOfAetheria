<?php
namespace Game\Traits\PropSuite;

use ReflectionClass;
use ReflectionProperty;
use ReflectionException;
use ReflectionType;
use ReflectionNamedType;
use ReflectionUnionType;
use ReflectionIntersectionType;



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
        $visited = [];
        return json_encode($this->dumpObject($this, $visited));
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
    /**
     * Normalize a ReflectionType into a string representation.
     */
    private function getReflectionTypeName(?ReflectionType $type): string {
        if (!$type) {
            return 'mixed';
        }

        if ($type instanceof ReflectionNamedType) {
            return $type->getName();
        }

        if ($type instanceof ReflectionUnionType || $type instanceof ReflectionIntersectionType) {
            $names = [];
            foreach ($type->getTypes() as $t) {
                if ($t instanceof ReflectionNamedType) {
                    $names[] = $t->getName();
                } else {
                    $names[] = (string)$t;
                }
            }
            return implode('|', $names);
        }

        return (string)$type;
    }

    /**
     * Internal recursive method to dump an object tree.
     * Handles circular references via the $visited map.
     *
     * @param object $obj Object to dump
     * @param array<string,int> $visited Map of spl_object_hash => ref id
     * @return array Associative array with __class and properties keys
     */
    private function dumpObject(object $obj, array &$visited = []): array {
        global $log;
        $objId = spl_object_hash($obj);
        if (isset($visited[$objId])) {
            return ['__ref' => $visited[$objId]];
        }

        $refId = count($visited) + 1;
        $visited[$objId] = $refId;

        $reflection = new ReflectionClass($obj);
        $data = [
            '__class' => $reflection->getName(),
            '__id' => $refId,
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
                    'type' => $this->getReflectionTypeName($type),
                    'value' => null
                ];

                if ($value !== null) {
                    if (is_object($value)) {
                        // Handle nested objects
                        if ($value instanceof \UnitEnum) {
                            $propertyData['value'] = [
                                'enum' => true,
                                'name' => $value->name,
                                'backed' => ($value instanceof \BackedEnum),
                                'value' => ($value instanceof \BackedEnum) ? $value->value : null
                            ];
                        } else {
                            $propertyData['value'] = $this->dumpObject($value, $visited);
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
    /**
     * Internal method to recursively restore an object from dumped data
     * Supports reference resolution via $refs map
     *
     * @param array $data
     * @param array<int,object> $refs
     * @return object|null
     */
    private function restoreObject(array $data, array &$refs = []): object|null {
        if (!$data) {
            return null;
        }

        if (isset($data['__ref'])) {
            $refId = $data['__ref'];
            return $refs[$refId] ?? null;
        }

        $className = $data['__class'];

        if (!class_exists($className)) {
            throw new \RuntimeException("Cannot restore class $className - class not found");
        }

        // Create new instance without calling constructor
        $reflection = new ReflectionClass($className);
        $instance   = $reflection->newInstanceWithoutConstructor();

        $refId = $data['__id'] ?? null;
        if ($refId !== null) {
            $refs[$refId] = $instance;
        }

        foreach ($data['properties'] as $name => $propertyData) {
            try {
                if (!$reflection->hasProperty($name)) {
                    continue;
                }

                $property = $reflection->getProperty($name);
                $property->setAccessible(true);

                $value = $propertyData['value'];

                // Nested object or reference
                if (is_array($value) && isset($value['__class'])) {
                    $nested = $this->restoreObject($value, $refs);
                    $property->setValue($instance, $nested);
                    continue;
                }

                if (is_array($value) && isset($value['__ref'])) {
                    $nested = $this->restoreObject($value, $refs);
                    $property->setValue($instance, $nested);
                    continue;
                }

                // Enum handling
                if (is_array($value) && isset($value['enum']) && $value['enum'] === true) {
                    $typeName = $propertyData['type'] ?? null;
                    if ($typeName && enum_exists($typeName)) {
                        $enumClass = $typeName;
                        if (!empty($value['backed'])) {
                            // backed enum: restore from scalar value
                            $property->setValue($instance, $enumClass::from($value['value']));
                        } else {
                            // unit enum: restore by case name
                            $property->setValue($instance, $enumClass::{$value['name']});
                        }
                        continue;
                    }
                }

                // Primitive or array
                $property->setValue($instance, $value);
            } catch (ReflectionException $e) {
                // Reflection failures are non-fatal for restore; log and continue
                if (isset($GLOBALS['log'])) {
                    $GLOBALS['log']->error("PropRestore: Reflection error on $name - " . $e->getMessage());
                }
                continue;
            } catch (\TypeError $e) {
                // Typed property mismatch - log and continue
                if (isset($GLOBALS['log'])) {
                    $GLOBALS['log']->error("PropRestore: TypeError setting $name on $className - " . $e->getMessage());
                }
                continue;
            } catch (\Throwable $e) {
                // Any other error - log and continue
                if (isset($GLOBALS['log'])) {
                    $GLOBALS['log']->error("PropRestore: Unexpected error on $name - " . $e->getMessage());
                }
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
