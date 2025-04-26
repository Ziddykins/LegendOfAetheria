<?php
namespace Game\Monster\Enums;
use Game\Traits\EnumExtender\EnumExtender;
# Global:
#   - Global monsters are available for everyone to attack and will pop up occasionally
#     Expeience and gold, as well as items will be based on damage contribution
#   - Zone monsters are a bit less powerful, but are restricted to zones (maps); any
#     players in this area will be able to contribute. Leaving the area forefits contribution
#   - Personal monsters are only visible and attackable by you
enum MonsterScope: int {
    use EnumExtender;
    case GLOBAL = 0;
    case ZONE = 1;
    case PERSONAL = 2;
    case NONE = 3;
}