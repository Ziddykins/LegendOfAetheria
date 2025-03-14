<?php
namespace Game\Monster\Enums;
# Global:
#   - Global monsters are available for everyone to attack and will pop up occasionally
#     Expeience and gold, as well as items will be based on damage contribution
#   - Zone monsters are a bit less powerful, but are restricted to zones (maps); any
#     players in this area will be able to contribute. Leaving the area forefits contribution
#   - Personal monsters are only visible and attackable by you
enum MonsterScope: int {
    case GLOBAL = 0;
    case ZONE = 1;
    case PERSONAL = 2;
    case NONE = 3;

    public static function name_to_enum(string $name) {
        foreach (self::cases() as $privilege) {
            if ($name === $privilege->name){
                return $privilege;
            }
        }
        throw new \ValueError("$name is not a valid backing value for enum " . self::class);
    }
}