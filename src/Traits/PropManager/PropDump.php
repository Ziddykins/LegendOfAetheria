<?php
namespace Game\Traits\PropManager;

use ReflectionClass;
use ReflectionEnum;
use BackedEnum;

trait PropDump {
    public static function class_to_json(object $object): string {
        return json_encode(self::objectToArray($object));
    }

    private static function objectToArray(object $object): array {
        $reflection = new ReflectionClass($object);
        $result = [
            'type' => get_class($object),
            'dtype' => 'class',
            'properties' => []
        ];

        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($object);
            $name = $property->getName();

            if (is_null($value)) {
                $result['properties'][$name] = null;
                continue;
            }

            if (is_object($value)) {
                if ($value instanceof BackedEnum) {
                    $result['properties'][$name] = [
                        'type' => get_class($value),
                        'name' => $name,
                        'value' => $value->value,
                        'dtype' => 'enum'
                    ];
                } elseif ((new ReflectionClass($value))->isEnum()) {
                    $result['properties'][$name] = [
                        'type' => get_class($value),
                        'name' => $name,
                        'value' => $value->name,
                        'dtype' => 'enum'
                    ];
                } else {
                    $result['properties'][$name] = self::objectToArray($value);
                }
            } else {
                $result['properties'][$name] = [
                    'type' => gettype($value),
                    'name' => $name,
                    'value' => $value,
                    'dtype' => 'primitive'
                ];
            }
        }

        return $result;
    }
}