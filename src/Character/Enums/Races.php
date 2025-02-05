<?php
namespace Game\Character\Enums;
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