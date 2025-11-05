<?php
namespace Game\System\Enums;

/**
 * Defines error codes for Legend of Aetheria system errors.
 * Organized by category prefix:
 * - FUNCT (1000s): Function usage errors
 * - SQLDB (2000s): Database connection/query errors
 * - FRNDS (3000s): Friend system errors
 * - MAIL (4000s): Mail system errors
 * - CRON (5000s): Scheduled task errors
 * - CHAR (6000s): Character creation/management errors
 */
enum LOAError: int {
    /** Function errors - invalid action passed to doSQL() */
    case FUNCT_DOSQL_INVALIDACTION = -1000;
    
    /** Function errors - unknown component requested in generator */
    case FUNCT_GENCOMP_UNKNOWN     = -1001;
    
    /** Function errors - invalid PropType passed to propSync() */
    case FUNCT_PROPSYNC_TYPE       = -1002;
    
    /** Database errors - failed to establish connection */
    case SQLDB_NOCONNECTION        = -2000;
    
    /** Database errors - prepared statement execution failed (deprecated for execute_query in PHP 8.2) */
    case SQLDB_PREPPED_EXECUTE     = -2001;
    
    /** Database errors - unrecognized save type requested */
    case SQLDB_UNKNOWN_SAVE_TYPE   = -2002;
    
    /** Database errors - referenced table does not exist */
    case SQLDB_UNKNOWN_TABLE       = -2003;

    /** Friend system errors - invalid friend status transition */
    case FRNDS_FRIEND_STATUS_ERROR = -3000;

    /** Mail system errors - unrecognized mail operation directive */
    case MAIL_UNKNOWN_DIRECTIVE    = -4000;
    
    /** Mail system errors - attempted to block already blocked user */
    case MAIL_ALREADY_BLOCKED      = -4001;
    
    /** Cron errors - cron script accessed directly via HTTP instead of CLI */
    case CRON_HTTP_DIRECT_ACCESS   = -5000;

    /** Character errors - maximum character limit reached for account */
    case CHAR_MAX_CHAR_COUNT       = -6000;
}