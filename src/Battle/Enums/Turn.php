<?php
namespace Game\Battle\Enums;
use Game\Traits\EnumExtender\EnumExtender;

enum Turn: int{
    use EnumExtender;
    case ENEMY = 0;
    case PLAYER = 1;
}
?>