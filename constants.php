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
?>