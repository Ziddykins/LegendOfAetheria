<?php
namespace Game\Account;

use Exception;
use Game\Traits\PropSuite\PropSuite;
use Game\Traits\PropSuite\Enums\PropType;
use Game\Components\Sidebar\Enums\SidebarType;

/**
 * Settings class manages user-specific preferences and UI configurations.
 * 
 * Stores account-level settings such as theme preferences and sidebar layout.
 * Uses PropSuite trait for dynamic property management and database synchronization.
 * 
 * @package Game\Account
 * 
 * @method mixed load_(int $account_id) Loads settings from database
 * @method mixed new_() Creates new settings record in database
 * 
 * @method int|null get_id() Gets the account ID
 * @method string get_colorMode() Gets the color theme mode (light/dark)
 * @method SidebarType|null get_sideBar() Gets the sidebar type preference
 * 
 * @method void set_colorMode(string $mode) Sets the color theme mode
 * @method void set_sideBar(SidebarType $type) Sets the sidebar type preference
 */
class Settings {
    use PropSuite;

    /** @var int|null Account ID this settings belongs to */
    private ?int $id = null;
    
    /** @var string Color theme mode (light/dark) */
    private string $colorMode = 'dark';
    
    /** @var SidebarType|null Sidebar layout preference */
    private ?SidebarType $sideBar = null;

    /**
     * Constructs a new Settings instance for an account.
     * 
     * @param int $accountID Account ID to associate settings with
     */
    public function __construct($accountID) {
        $this->id = $accountID;
        $this->sideBar = SidebarType::LTE_DEFAULT;
        $this->colorMode = 'dark';
    }

    /**
     * Magic method for dynamic property access and modification.
     * 
     * Handles get/set operations, mathematical operations (add, sub, mul, div, exp, mod),
     * and property dump/restore operations via PropSuite trait.
     * 
     * @param string $method Method name to invoke
     * @param array $params Parameters for the method
     * @return mixed Result of the invoked method
     */
    public function __call($method, $params) {
        global $db, $log;

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