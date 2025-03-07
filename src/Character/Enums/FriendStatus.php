<?php
namespace Game\Character\Enums;

enum FriendStatus {
    case MUTUAL;
    case REQUESTED;
    case REQUEST;
    case NONE;
    case BLOCKED;
    case BLOCKED_BY;

    public static function name_to_enum(string $name) {
        foreach (self::cases() as $case) {
            if ($case === $name) {
                return $case;
            }
        }
    }
}