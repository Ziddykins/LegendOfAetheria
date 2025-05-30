<?php
    namespace Game\Traits\PropSuite;

    use Game\Traits\PropSuite\PropConvert;
    use Game\Traits\PropSuite\PropMod;
    use Game\Traits\PropSuite\PropSync;
    use Game\Traits\PropSuite\PropDump;

    trait PropSuite {
        use PropConvert;
        use PropMod;
        use PropSync;
        use PropDump;
    }
?>