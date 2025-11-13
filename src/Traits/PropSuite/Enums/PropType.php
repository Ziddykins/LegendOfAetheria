<?php
    namespace Game\Traits\PropSuite\Enums;

    /**
     * Maps entity types to their corresponding database tables for PropSync operations.
     * Used by PropSync to determine which table to query when performing get_/set_/load_/new_ operations.
     * 
     * Each case corresponds to a specific game entity and its associated database table.
     */
    enum PropType {
        /** User account data (tbl_accounts) */
        case ACCOUNT;
        
        /** Player character data (tbl_characters) */
        case CHARACTER;
        
        /** Companion familiar data (tbl_familiars) */
        case FAMILIAR;
        
        /** Character inventory data (tbl_characters) */
        case INVENTORY;
        
        /** Enemy monster data (tbl_monsters) */
        case MONSTER;
        
        /** Account settings/preferences (tbl_settings) */
        case SETTINGS;

        /** Character combat statistics (tbl_characters) */
        case CSTATS;

        /** Monster combat statistics (tbl_monsters) */
        case MSTATS;

        /** Familiar combat statistics (tbl_familiar) */
        case FSTATS;
        
        /** Banking system data (tbl_bank) */
        case BANKMANAGER;

        /** Future AI incorporation */
        case LLAMA;

    }
?>