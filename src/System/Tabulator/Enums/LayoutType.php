<?php
namespace Game\System\Tabulator\Enums;

/**
 * Defines Tabulator.js table layout modes.
 * Controls how columns are sized relative to container and data.
 */
enum LayoutType {
    /** Stretch columns to fill available width equally */
    case FIT_COLUMNS;
    
    /** Size columns based on content width */
    case FIT_DATA;
    
    /** Fit data width but expand to fill container */
    case FIT_DATA_FILL;
    
    /** Fit data and stretch columns proportionally */
    case FIT_DATA_STRETCH;
    
    /** Standard table layout with scrolling */
    case FIT_DATA_TABLE;
}