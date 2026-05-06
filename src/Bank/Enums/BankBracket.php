<?php
    namespace Game\Bank\Enums;
    use Game\Traits\EnumExtender\EnumExtender;

    /**
     * BankBracket enum defines banking tier levels.
     * 
     * Higher tiers provide better interest rates, higher transfer limits,
     * and additional banking features.
     * 
     * @package Game\Bank\Enums
     */
    enum BankBracket {
        use EnumExtender;
        
        /** Standard tier - basic banking features */
        case STANDARD;
        
        /** Elite tier - improved interest rates and limits */
        case ELITE;
        
        /** Platinum tier - premium banking benefits */
        case PLATINUM;
        
        /** Diamond tier - highest tier with maximum benefits */
        case DIAMOND;
    }
?>