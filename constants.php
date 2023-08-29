<?php

    define('MAX_ASSIGNABLE_AP', 40);
    
    enum Races {
        public static function random(): self {
            $count = count(self::cases()) - 1;
            return self::cases()[rand(0, $count)];
        }
        
        case Angel;
        case Demon;
        case Dwarf;
        case Elf;
        case Gnome;
        case Halfling;
        case Human;
        case Orc;
        case Troll;
        case Undead;
        case Vampire;
        case Default;
    }

    enum UserPrivileges: int {
        public static function name_to_value(string $name): string {
            foreach (self::cases() as $privilege) {
                if ($name === $privilege->name){
                    return $privilege->value;
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
    };

    enum Weather: int {
        public static function random(): self {
            $count = count(self::cases()) - 1;
            return self::cases()[rand(0, $count)];
        }
        public static function name_to_value(string $name): string {
            foreach (self::cases() as $weather) {
                if ($name === $weather->name){
                    return $weather->value;
                }
            }
            throw new \ValueError("$name is not a valid backing value for enum " . self::class);
        }
        public function icon(): string {
            return match($this) {
                Weather::CLOUDY =>  '<i class="bi bi-cloud-fill"></i>',
                Weather::SUNNY  =>  '<i class="bi bi-brightness-high-fill"></i>',
                Weather::HAILING => '<i class="bi bi-cloud-hail-fill"></i>',
                Weather::SNOWING => '<i class="bi bi-cloud-snow-fill"></i>',
                Weather::RAINING => '<i class="bi bi-cloud-lightning-rain-fill"></i>'
            };
        }

        case SUNNY = 1;
        case RAINING = 2;
        case HAILING = 4;
        case CLOUDY = 8;
        case SNOWING = 16;
    }
?>


