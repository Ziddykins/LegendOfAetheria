<?php
namespace Game\Account\Enums;

enum Privileges: int {
    public static function name_to_value(string $name): string {
        foreach (self::cases() as $privilege) {
            if ($name === $privilege->name){
                return $privilege->value;
            }
        }
        throw new \ValueError("$name is not a valid backing value for enum " . self::class);
    }
    
    public static function name_to_enum(string $name) {
        foreach (self::cases() as $privilege) {
            if ($name === $privilege->name){
                return $privilege;
            }
        }
        throw new \ValueError("$name is not a valid backing value for enum " . self::class);
    }

    case BANNED = 1;
    case MUTED = 2;
    case UNREGISTERED = 4;
    case UNVERIFIED = 8;
    case USER = 16;
    case MODERATOR = 32;
    case SUPER_MODERATOR = 64;
    case ADMINISTRATOR = 128;
    case GLOBAL_ADMINISTRATOR = 256;
    case OWNER = 512;
    case ROOTED = 1024;
}