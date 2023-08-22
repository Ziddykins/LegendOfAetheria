<?php

    define('MAX_ASSIGNABLE_AP', 40);

    enum UserPrivileges: int {
        case BANNED = 1;
        case MUTED = 2;
        case UNREGISTERED = 4;
        case UNVERIFIED = 8;
        case USER = 16;
        case MODERATOR = 32;
        case SUPER_MODERATOR = 64;
        case ADMINISTRATOR = 128;
        case GLOBAL_ADMINISTRATOR = 256;
        case OWNER = 512;
        case ROOTED = 1024;
    };
?>