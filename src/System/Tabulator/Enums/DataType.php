<?php
namespace Game\System\Tabulator\Enums;

/**
 * Defines data type categories for Tabulator table generation.
 * Determines whether to generate column definitions or row data.
 */
enum DataType {
    /** Generate column header definitions */
    case HEADER_DATA;
    
    /** Generate table row data */
    case TABLE_DATA;
};