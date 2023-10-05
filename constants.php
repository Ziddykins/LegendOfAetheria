<?php

    define('MAX_ASSIGNABLE_AP', 40);
    define('ENERGY_PER_TICK', 3);
    
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


    enum Weather {
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
                Weather::CLOUDY =>  '<span class="text-info-emphasis"><i class="bi bi-cloud-fill p-2"></i>',
                Weather::SUNNY  =>  '<span class="text-warning"><i class="bi bi-brightness-high-fill p-2"></i>',
                Weather::HAILING => '<span class="text-primary-emphasis"><i class="bi bi-cloud-hail-fill p-2"></i>',
                Weather::SNOWING => '<span class="text-white"><i class="bi bi-cloud-snow-fill p-2"></i>',
                Weather::RAINING => '<span class="text-primary"><i class="bi bi-cloud-lightning-rain-fill p-2"></i>'
            };
        }

        case SUNNY;
        case RAINING;
        case HAILING;
        case CLOUDY;
        case SNOWING;
    };

    enum FriendStatus {
        case MUTUAL;
        case REQUESTED;
        case REQUEST;
        case NONE;
    };
?>