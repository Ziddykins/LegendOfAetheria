<?php
namespace Game\Character\Enums;
use Game\Traits\EnumExtender\EnumExtender;
enum FriendStatus {
    use EnumExtender;
    case NONE;
    case REQUEST_SENT;
    case REQUEST_RECV;
    case MUTUAL;
    case BLOCKED;
    case BLOCKED_BY;
}