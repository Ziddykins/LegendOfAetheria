<?php
    namespace Game\Bank\Enums;
    use Game\Traits\EnumExtender\EnumExtender;

    enum BankBracket {
        use EnumExtender;
        case STANDARD;
        case ELITE;
        case PLATINUM;
        case DIAMOND;
    }
?>