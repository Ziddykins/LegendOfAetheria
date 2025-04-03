<?php
namespace Game\Account;

use Exception;
use Game\Traits\PropManager\PropManager;
use Game\Traits\PropManager\Enums\PropType;
use Game\Components\Sidebar\Enums\SidebarType;

class Settings {
    use PropManager;

    private int $id;
    private string $colorMode;
    private SidebarType $sideBar;

    public function __construct($accountID) {
        $this->id = $accountID;
        $this->sideBar = SidebarType::LTE_DEFAULT;
        $this->colorMode = 'dark';
    }

    public function __call($method, $params) {
        global $db, $log;

        /* If it's a get, this is true */
        if (!count($params)) {
            $params = null;
        }

        /* Avoid loops with propSync triggering itself */
        if ($method == 'propSync' || $method == 'propMod') {
            $log->debug("$method loop");
            return;
        }

        if (preg_match('/^(add|sub|exp|mod|mul|div)_/', $method)) {
            return $this->propMod($method, $params);
        } else {
            return $this->propSync($method, $params, PropType::SETTINGS);
        }
    }
}