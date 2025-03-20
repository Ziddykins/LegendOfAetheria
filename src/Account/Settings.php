<?php
namespace Game\Account;
use Game\Traits\PropConvert;
use Game\Traits\PropSync;
use Game\Traits\Enums\PropType;
use Game\Components\Sidebar\Enums\SidebarType;
use Game\Components\Sidebar\Sidebar;


class Settings {
    use PropSync;
    use PropConvert;

    private int $accountID;
    private bool $darkMode = false;
    private SidebarType $sideBar;

    public function __construct($accountID) {
        $this->accountID = $accountID;
        $this->sideBar = SidebarType::LTE_DEFAULT;
    }

    public function __call($method, $params): mixed {
        if ($method === 'propSync') {
            return 0;
        }

        return $this->propSync($method, $params, PropType::SETTINGS);
    }
}