<?php
namespace Game\Character\Enums;

enum FriendStatus {
    case NONE;
    case REQUEST_SENT;
    case REQUEST_RECV;
    case MUTUAL;
    case BLOCKED;
    case BLOCKED_BY;

    public static function name_to_enum(string $name) {
        foreach (self::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }
    }
}