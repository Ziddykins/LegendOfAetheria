<?php
namespace Game\Traits\Enums;

enum Type {
    case ACCOUNT;
    case CHARACTER;
    case FAMILIAR;
    case INVENTORY;
    case MONSTER;

    /* Character Stats */
    case CSTATS;

    /* Monster Stats */
    case MSTATS;
}

