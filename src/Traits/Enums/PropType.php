<?php
namespace Game\Traits\Enums;

enum PropType {
    case ACCOUNT;
    case CHARACTER;
    case FAMILIAR;
    case INVENTORY;
    case MONSTER;
    case SETTINGS;

    /* Character Stats */
    case CSTATS;

    /* Monster Stats */
    case MSTATS;
}

