<?php
namespace Game\Character\Enums;
use Game\Traits\EnumExtender\EnumExtender;
enum Races {
    use EnumExtender;   
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