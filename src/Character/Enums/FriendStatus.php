<?php
namespace Game\Character\Enums;

enum FriendStatus {
    case MUTUAL;
    case REQUESTED;
    case REQUEST;
    case NONE;
    case BLOCKED;
    case BLOCKED_BY;
}