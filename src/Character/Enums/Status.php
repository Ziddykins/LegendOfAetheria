<?php
namespace Game\Character\Enums;
use Game\Traits\EnumExtender\EnumExtender;

enum Status: int {
    use EnumExtender;
    
    case HEALTHY        = 1;
    case POISONED       = 2;
    case BLINDED        = 4;
    case SCARED         = 8;
    case OVERENCUMBERED = 16;
    case OVERHEATED     = 32;
    case STUNNED        = 64;
    case FROZEN         = 128;
    case BURNING        = 256;
    case CONFUSED       = 512;
    case CHARMED        = 1024;
    case SLEEPING       = 2048;
    case DEAD           = 4096;
    case BLEEDING       = 8192;
}