<?php
namespace Game\LoASys\Enums;

# Categories:
#   - FUNCT - Relating to the usage of a function
#   - SQLDB - Directly relating to the database, i.e. invalid
#             prepare statements or connection issues
#   - FRNDS - Well, friend related issues
#   - MAIL  - Mail related issues
#   - CRON  - Cron related issues
#   - CHAR  - Character related issues
enum LOAError: int {
    case FUNCT_DOSQL_INVALIDACTION = -1000;
    case FUNCT_GENCOMP_UNKNOWN     = -1001;
    case FUNCT_PROPSYNC_TYPE       = -1002;
    
    case SQLDB_NOCONNECTION        = -2000;
    case SQLDB_PREPPED_EXECUTE     = -2001; // deprecated for execute_query in php8.2
    case SQLDB_UNKNOWN_SAVE_TYPE   = -2002;

    case FRNDS_FRIEND_STATUS_ERROR = -3000;

    case MAIL_UNKNOWN_DIRECTIVE    = -4000;
    case MAIL_ALREADY_BLOCKED      = -4001;
    
    case CRON_HTTP_DIRECT_ACCESS   = -5000;

    case CHAR_MAX_CHAR_COUNT       = -6000;
}