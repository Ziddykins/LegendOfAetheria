<?php

    define('MAX_ASSIGNABLE_AP', 40);
    define('ENERGY_PER_TICK',    3);
    
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

    enum UserPrivileges {
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
    }


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
    }

    enum FriendStatus {
        case MUTUAL;
        case REQUESTED;
        case REQUEST;
        case NONE;
        case BLOCKED;
        case BLOCKED_BY;
    }
    
    enum Error {
        case FUNCT_DOSQL_INVALIDACTION = -1000;
        
        case SQLDB_NOCONNECTION        = -2000;
        case SQLDB_PREPPED_EXECUTE     = -2001;
    }
    
    enum MonsterScope {
        case GLOBAL;
        case ZONE;
        case PERSONAL;
    }
    
    enum MonsterClass {
        case GIANT;       /* 2-3x HP */
        case MINI;        /* 1/2 - 1/4 HP */
        case HARDENED;    /* 3-4x DEF */
        case ENRAGED;     /* 4x STR, -5% HP */
        case EXPERIENCED; /* 3x EXP, +2% HP+DEF */
        case PACKRAT;     /* Item rarity chances increased/1.5x, 3x gold */
        case DEFECTING;   /* Chance to spare life/let join your army */
        case LEADER;      /* 5-10x all stats */
    }
    
    enum ItemRarity {
       public static function getItemRarity($roll) {
           foreach (self::cases() as $rarity) {
               $log->info("checking $rarity rarity against $roll");
               if ($roll >= $rarity->value) {
                   return $rarity;
  
               }
           }
       }
       
       case WORTHLESS = 50.0;  /* 50.00% chance */
       case TARNISHED = 30.0;  /* 20.00% chance */
       case COMMON    = 20.0;  /* 10.00% chance */
       case ENCHANTED = 12.0;  /*  8.00% chance */
       case MAGICAL   =  8.0;  /*  4.00% chance */
       case LEGENDARY =  5.0;  /*  3.00% chance */
       case EPIC      = 2.50;  /*  2.50% chance */
       case MYSTIC    = 1.50;  /*  1.00% chance */
       case HEROIC    = 0.75;  /*  0.75% chance */
       case INFAMOUS  = 0.24;  /*  0.51% chance */
       case GODLY     = 0.01;  /*  0.23% chance */
    }
?>