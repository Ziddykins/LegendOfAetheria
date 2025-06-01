<?php
namespace Game\Account;

use Exception;
use Game\Traits\PropSuite\PropSuite;
use Game\Traits\PropSuite\Enums\PropType;
use Game\Components\Sidebar\Enums\SidebarType;

class Settings {
    use PropSuite;

    private ?int $id = null;
    private string $colorMode = 'dark';
    private ?SidebarType $sideBar = null;

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

        if (preg_match('/^(add|sub|exp|mod|mul|div)_/', $method)) {
            return $this->propMod($method, $params);
        } elseif (preg_match('/^(propDump|propRestore)$/', $method, $matches)) {
            $func = $matches[1];
            return $this->$func($params[0] ?? null);
        } else {
            return $this->propSync($method, $params, PropType::SETTINGS);
        }
    }
}