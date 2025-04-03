<?php
namespace Game\Traits\PropManager;
use Game\Traits\PropManager\Enums\PropType;
use Game\Traits\PropManager\PropConvert;
use Game\Traits\PropManager\PropMod;
use Game\Traits\PropManager\PropSync;

trait PropManager {
    use PropConvert;
    use PropMod;
    use PropSync;
}
?>