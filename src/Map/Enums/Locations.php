<?php
namespace Game\Map\Enums;
use Game\Traits\EnumExtender\EnumExtender;

enum Locations: int {
    use EnumExtender;

    case SHRINE = 0;
    case MARKET = 1;
    case DUNGEON = 2;
    case TRAINER = 3;
    case STABLES = 4;
    case INN = 5;
    case BLACKSMITH = 6;
    case ALCHEMIST = 7;
    case TAVERN = 8;
    case LIBRARY = 9;
    case ARMORY = 10;
}
