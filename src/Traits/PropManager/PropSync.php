<?php
namespace Game\Traits\PropManager;

use Game\Account\Settings;
use Game\Account\Enums\Privileges;
use Game\Character\Stats;
use Game\Inventory\Inventory;
use Game\System\Enums\LOAError;
use Game\Traits\PropManager\Enums\PropType;


/**
     * Trait PropSync
     * 
     * Provides functionality for synchronizing properties between the application and the database.
     * This trait handles various actions such as getting, setting, creating, and loading properties
     * for different types of entities (e.g., characters, accounts, monsters, etc.).
     * 
    * @method mixed propSync(string $method, array $params, PropType $type)
     * 
     * @param string $method The method name indicating the action to perform (e.g., "get", "set", "new", "load").
     * @param array $params Parameters required for the action. The first parameter is typically the value to set or additional data.
     * @param PropType $type The type of property being synchronized (e.g., PropType::CHARACTER, PropType::ACCOUNT).
     * 
     * @return mixed Returns the result of the action performed. For "get", it returns the property value.
     *               For "set", "new", and "load", it may return an ID or null depending on the action.
     * 
     * @throws LOAError::FUNCT_PROPSYNC_TYPE If an unsupported PropType is provided.
     * 
     * Actions:
     * - **get**: Retrieves the value of a property.
     * - **set**: Updates the value of a property in the database and the object.
     * - **new**: Creates a new entity in the database and initializes its properties.
     * - **load**: Loads an entity's properties from the database into the object.
     * 
     * Supported PropTypes:
     * - PropType::CSTATS, PropType::CHARACTER, PropType::INVENTORY: Uses the character table.
     * - PropType::ACCOUNT, PropType::SETTINGS: Uses the account table.
     * - PropType::FAMILIAR: Uses the familiar table.
     * - PropType::MONSTER, PropType::MSTATS: Uses the monster table.
     * 
     * Notes:
     * - The method dynamically determines the action and property based on the method name.
     * - Serialization is used for complex properties like inventory, settings, and stats.
     * - Global variables `$db` and `$log` are used for database operations and logging.
     * - Environment variables (e.g., `$_ENV['SQL_CHAR_TBL']`) are used to determine table names.
 */
trait PropSync {
    private function propSync($method, $params, PropType $type): mixed {
        global $db, $log;
        $table  = null;
        $prop   = null;
        $offset = strpos($method, "_");
        $action = substr($method, 0, $offset);

        if ($offset === false) {
            $action = $method;
        } else {
            $prop = lcfirst(substr($method, $offset + 1));
        }

        if ($method == 'propSync') {
            return null;
        }

        switch ($type) {
            case PropType::CSTATS:
            case PropType::CHARACTER:
            case PropType::INVENTORY:
                $table = $_ENV['SQL_CHAR_TBL'];
                break;
            case PropType::ACCOUNT:
            case PropType::SETTINGS:
                $table = $_ENV['SQL_ACCT_TBL'];
                break;
            case PropType::FAMILIAR:
                $table = $_ENV['SQL_FMLR_TBL'];
                break;
            case PropType::MONSTER:
            case PropType::MSTATS:
                $table = $_ENV['SQL_MNST_TBL'];
                break;
            default:
                exit(LOAError::FUNCT_PROPSYNC_TYPE);
        }

        if (!isset($params[0])) {
            $params[0] = null;
        }

        if (isset($params[0]->name)) {
            $log_str = $params[0]->name;
        } else {
            $log_str = $params[0];
        }

        if (strcmp($action, "get") === 0) { /* GET */
            return $this->$prop;
        } elseif (strcmp($action, 'set') === 0) { /* SET */

            $table_col = $this->clsprop_to_tblcol($prop);
            $sql_query = null;
        
            $id = $this->id;

            $this->$prop = $params[0];

            switch ($type) {
                case PropType::CSTATS:
                case PropType::MSTATS:
                    $tbl = $type == PropType::MSTATS
                        ? $tbl = $_ENV['SQL_MNST_TBL']
                        : $tbl = $_ENV['SQL_CHAR_TBL'];

                    $id = $this->id;
                    $srl_data = safe_serialize($this);
                    $sql_query = "UPDATE $tbl SET `stats` = ? WHERE `id` = ?";
                    $db->execute_query($sql_query, [ $srl_data, $this->id ]);
                    return null;
                case PropType::INVENTORY:
                    $id = $this->id;
                    $srl_data = safe_serialize($this);
                    $sql_query = "UPDATE {$_ENV['SQL_CHAR_TBL']} SET `inventory` = ? WHERE `id` = ?";
                    $db->execute_query($sql_query, [ $srl_data, $_SESSION['character-id'] ]);
                    return null;
                case PropType::SETTINGS:
                    $id = $this->id;
                    $srl_data = safe_serialize($this);
                    $sql_query = "UPDATE {$_ENV['SQL_ACCT_TBL']} SET `settings` = ? WHERE `id` = ?";
                    $db->execute_query($sql_query, [ $srl_data, $this->id ]);
                    return null;
                case PropType::MONSTER:
                    if (isset($params[1]) && $params[1] === 'false') {
                        return null;
                    }

                    $srl_data = safe_serialize($this);
                    $sql_query = "UPDATE {$_ENV['SQL_CHAR_TBL']} SET `monster` = ? WHERE `id` = ?";
                    $db->execute_query($sql_query, [ $srl_data, $this->characterID ]);
                    
                    if ($prop === 'scope') {
                        $params[0] = $params[0]->value;
                    }
                    break;
                case PropType::CHARACTER:
                    $id = $this->id;

                    if ($prop === 'monster') {
                        $params[0] = safe_serialize($params[0]);
                    }
                    
                    break;
                case PropType::ACCOUNT:
                    $id = $this->id;
                    if ($prop === 'settings') {
                        $params[0] = safe_serialize($params[0]);
                    }
                    break;
            }

            $sql_query = "UPDATE $table SET `$table_col` = ? WHERE `id` = ?";  
            
            if (is_object($params[0])) {
                if (is_numeric($params[0]->value)) { // if an enum, essentially
                    $params[0] = $params[0]->value;
                } else {
                    $params[0] = safe_serialize($params[0]);
                }
            }

            $db->execute_query($sql_query, [ $params[0], $id ]);
        } elseif (strcmp($action, 'new') === 0) { /*NEW*/
            $focused_id = null;

            switch ($type) {
                case PropType::ACCOUNT:
                    $accountID  = getNextTableID($_ENV['SQL_ACCT_TBL']);
                    
                    $tmp_settings = new Settings($accountID);
                    $this->set_settings($tmp_settings);
                    $srl_data = safe_serialize($tmp_settings);

                    $sql_query  = "INSERT INTO $table (`id`, `email`, `settings`) VALUES (?, ?, ?)";
                    $this->id = $accountID;
                    $db->execute_query($sql_query, [ $accountID, $this->email, $srl_data ] );

                    return $accountID;
                case PropType::CHARACTER:
                    if (isset($params[0])) {
                        $next_slot = $params[0];
                    } else {
                        $next_slot = $this->getNextCharSlotID($this->accountID);
                    }
                    $char_id    = getNextTableId($_ENV['SQL_CHAR_TBL']);
                    $char_col   = "char_slot$next_slot";

                    if ($next_slot == -1) {
                        header('Location: /select?no_slots');
                        exit();
                    }
                    
                    $this->id = $char_id;

                    $tmp_inventory = new Inventory($this->id);
                    $tmp_stats     = new Stats($char_id);
                    
                    $srl_inventory = safe_serialize($tmp_inventory);
                    $srl_stats     = safe_serialize($tmp_stats);

                    $this->inventory = $tmp_inventory;
                    $this->stats     = $tmp_stats;

                    $chr_query = "INSERT INTO $table (`id`, `account_id`, `inventory`, `stats`) VALUES (?, ?, ?, ?)";
                    $act_query = "UPDATE {$_ENV['SQL_ACCT_TBL']} SET `$char_col` = ? WHERE `id` = ?";

                    $db->execute_query($chr_query, [ $char_id, $this->accountID, $srl_inventory, $srl_stats ]);
                    $db->execute_query($act_query, [$this->id, $this->accountID]);

                    return $char_id;
                case PropType::MONSTER:
                    $table     = $_ENV['SQL_MNST_TBL'];
                    $sql_query = "INSERT INTO $table (`id`, `account_id`, `character_id`, `scope`, `seed`, `stats`) VALUES (?, ?, ?, ?, ?, ?)";
                    $next_id   = getNextTableID($table);
                    
                    $this->id = $next_id;
                    $this->characterID($_SESSION['character-id']);
                    $this->accountID($_SESSION['account-id']);

                    $tmp_stats = new \Game\Monster\Stats($next_id);
                    $srl_data = safe_serialize($tmp_stats);

                    $this->stats = $tmp_stats;

                    $db->execute_query($sql_query, [ $next_id, $_SESSION['account-id'], $_SESSION['character-id'], $this->scope->value, $this->seed, $srl_data ]);
                    return $next_id;
            }
        } elseif (strcmp($action, 'load') === 0) { /*LOAD*/
            $tmp_obj = null;
            $sql_query = "SELECT * FROM $table WHERE `id` = ?";
            $tmp_obj = $db->execute_query($sql_query, [ $this->id ])->fetch_assoc();
            
            foreach ($tmp_obj as $key => $value) {
                $key = $this->tblcol_to_clsprop($key);

                
                if ($value !== null && $value !== 'NULL') {
                    if (array_search($key, ['inventory', 'settings', 'stats', 'monster']) == false) {
                    }
                    if ($key == 'privileges') {
                        $this->$key = Privileges::name_to_enum($value);
                    }
                    $this->$key = $value;
                }
            }

            if ($type == PropType::CHARACTER) {
                $tmp_inv     = safe_serialize($tmp_obj['inventory'], true);
                $tmp_stats   = safe_serialize($tmp_obj['stats'],     true);

                if ($tmp_obj['monster']) {
                    $tmp_monster   = safe_serialize($tmp_obj['monster'], true);
                    $this->monster = $tmp_monster;
                }

                $this->inventory = $tmp_inv;
                $this->stats     = $tmp_stats;
            } elseif ($type == PropType::ACCOUNT) {
                $tmp_settings = safe_serialize($tmp_obj['settings'], true);
                $tmp_privs    = Privileges::name_to_enum($tmp_obj['privileges']);
                $this->privileges = $tmp_privs;
                $this->settings = $tmp_settings;
            } elseif ($type == PropType::MONSTER) {
                $tmp_stats = safe_serialize($tmp_obj['stats'], true);
                $this->stats = $tmp_stats;
            }
        }
        return 0;
    }    
}