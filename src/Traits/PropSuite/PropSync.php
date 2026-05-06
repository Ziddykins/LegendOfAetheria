<?php
namespace Game\Traits\PropSuite;

use ValueError;
use Game\Character\Stats;
use Game\Character\Enums\Status;
use Game\Character\Enums\Races;
use Game\Account\Settings;
use Game\Bank\BankManager;
use Game\Inventory\Inventory;
use Game\System\Enums\LOAError;
use Game\Bank\Enums\BankBracket;
use Game\Account\Enums\Privileges;
use Game\Traits\PropSuite\Enums\PropType;
use Game\Monster\Monster;
use Game\Monster\Enums\MonsterScope;
use Exception;
use ReflectionType;

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
     * - PropType::BANKMANAGER: Uses the bank table.
     * 
     * Notes:
     * - The method dynamically determines the action and property based on the method name.
     * - Serialization is used for complex properties like inventory, settings, bank and stats.
     * - Global variables `$db` and `$log` are used for database operations and logging.
     * - Environment variables (e.g., `$_ENV['SQL_CHAR_TBL']`) are used to determine table names.
 */
trait PropSync {
    private function propSync($method, $params, PropType $type): mixed {
        if (in_array($method, [ 'propSync', 'propMod', 'propDump', 'propRestore' ])) {
            return null;
        }

        $prop   = null;
        $offset = strpos($method, "_");
        $action = substr($method, 0, $offset);

        if ($offset === false) {
            $action = $method;
        } else {
            $prop = lcfirst(substr($method, $offset + 1));
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
            case PropType::BANKMANAGER:
                $table = $_ENV['SQL_BANK_TBL'];
                break;
            default:
                exit(LOAError::FUNCT_PROPSYNC_TYPE);
        }

        if (!isset($params[0])) {
            $params[0] = null;
        }

        switch($action) {
            case "get":
                return $this->$prop ?? null;
            case "set":
                $this->handle_set($prop, $params, $type, $table);
                break;
            case "load":
                $this->handle_load($type, $params, $table);
                break;
            case "new":
                return $this->handle_new($type, $params, $table);
        }

        return LOAError::FUNCT_PROPSYNC_TYPE;
    }

    private function handle_set($prop, $params, $type, $table): void {
        global $db, $log;
        $table_col = $this->clsprop_to_tblcol($prop);

        if (isset($this->id)) {
            $id = $this->id;
        }
        
        switch ($type) {
            case PropType::CSTATS:
            case PropType::MSTATS:
                $tbl = $type == PropType::MSTATS
                    ? $tbl = $_ENV['SQL_MNST_TBL']
                    : $tbl = $_ENV['SQL_CHAR_TBL'];

                $id = $this->id;
                $srl_data = $this->propDump();
                $log->debug($srl_data);
                $sql_query = "UPDATE $tbl SET `stats` = ? WHERE `id` = ?";
                $db->execute_query($sql_query, [ $srl_data, $this->id ]);
                return;
            case PropType::INVENTORY:
                $id = $this->id;
                $srl_data = $this->propDump();
                $log->debug($srl_data);
                $sql_query = "UPDATE {$_ENV['SQL_CHAR_TBL']} SET `inventory` = ? WHERE `id` = ?";
                $db->execute_query($sql_query, [ $srl_data, $$this->id ]);
                return;
            case PropType::SETTINGS:
                $id = $this->id;
                $srl_data = $this->propDump();
                $log->debug($srl_data);
                $sql_query = "UPDATE {$_ENV['SQL_ACCT_TBL']} SET `settings` = ? WHERE `id` = ?";
                $db->execute_query($sql_query, [ $srl_data, $this->id ]);
                return;
            case PropType::MONSTER:
                if (isset($params[1]) && $params[1] === 'false') {
                    return;
                }

                $srl_data = $this->propDump();
                $sql_query = "UPDATE {$_ENV['SQL_CHAR_TBL']} SET `monster` = ? WHERE `id` = ?";
                $db->execute_query($sql_query, [ $srl_data, $this->characterID ]);
                
                if ($prop === 'scope') {
                    $params[0] = $params[0]->value;
                }

                break;
            case PropType::CHARACTER:
                $id = $this->id;
                

                if ($prop === 'monster') {
                    $tmp_monster = $params[0];
                    $params[0] = $tmp_monster->propDump();
                } else if ($prop === 'bank') {
                    $tmp_bank = $params[0];
                    $params[0] = $tmp_bank->propDump();
                }

                break;
            case PropType::ACCOUNT:

                $id = $this->id;

                if ($prop === 'settings') {
                    $tmp_settings = $params[0];
                    $params[0] = $tmp_settings->propDump();    
                } else if ($prop == 'loggedIn') {
                    $params[0] = $params[0] ? 1 : 0;
                }

                break;
        }

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $table) || !preg_match('/^[a-zA-Z0-9_]+$/', $table_col)) {
            throw new ValueError('Invalid input');
        }

        $type_query = <<<SQL
            SELECT DATA_TYPE
            FROM information_schema.COLUMNS
            WHERE
                TABLE_NAME = ? AND
                COLUMN_NAME = ?
        SQL;

        $column_type = $db->execute_query($type_query, [ $table, $table_col ])->fetch_column();

        if ($column_type === 'enum' or gettype($params[0]) == 'object') {
            try {
                $params[0] = $params[0]->name;
            } catch (ValueError $e) {
                echo $e->getMessage() .'\n';
            }
        } else if ($column_type === 'set') {
            $params[0] = $params[0]->value;
        }
 
        $sql_query = "UPDATE $table SET `$table_col` = ? WHERE `id` = ?";
        $db->execute_query($sql_query, [ $params[0], $id ]);
    }

    private function handle_new($type, $params, $table) {
        global $db;
        $focused_id = null;

        switch ($type) {
            case PropType::ACCOUNT:
                $accountID  = getNextTableID($_ENV['SQL_ACCT_TBL']);
                $this->id = $accountID;

                $this->settings = new Settings($accountID);
                $srl_data = $this->settings->propDump();

                $sql_query  = "INSERT INTO $table (`id`, `email`, `settings`) VALUES (?, ?, ?)";
                
                $db->execute_query($sql_query, [ $accountID, $this->email, $srl_data ] );

                return $accountID;            
            case PropType::CHARACTER:
                $next_slot = $params[0] ?? $this->getNextCharSlotID($this->accountID);

                $this->id = getNextTableId($_ENV['SQL_CHAR_TBL']);
                $char_col   = "char_slot$next_slot";

                if ($next_slot == -1) {
                    header('Location: /select?no_slots');
                    exit();
                }
                
                $tmp_inventory = new Inventory($this->id);
                $tmp_stats     = new Stats($this->id);
                $tmp_bank      = new BankManager($this->accountID, $this->id);

                $srl_inventory = $tmp_inventory->propDump();
                $srl_stats     = $tmp_stats->propDump();
                $srl_bank      = $tmp_bank->propDump();

                $chr_query = "INSERT INTO $table (`id`, `account_id`, `inventory`, `stats`, `bank`, `status`) VALUES (?, ?, ?, ?, ?, ?)";
                $bnk_query = "INSERT INTO {$_ENV['SQL_BANK_TBL']} (`id`, `account_id`, `character_id`) VALUES (?, ?, ?)";

                if (!preg_match('/^[a-zA-Z0-9_]+$/', $char_col)) {
                    throw new ValueError('Invalid input');
                }

                $act_query = "UPDATE {$_ENV['SQL_ACCT_TBL']} SET `$char_col` = ? WHERE `id` = ?";

                $db->execute_query($chr_query, [ $this->id, $this->accountID, $srl_inventory, $srl_stats, $srl_bank, $this->status->value ]);
                $db->execute_query($bnk_query, [ $tmp_bank->get_id(), $this->accountID, $this->id ]);
                $db->execute_query($act_query, [ $this->id, $this->accountID ]);

                return $this->id;
            case PropType::MONSTER:
                $table     = $_ENV['SQL_MNST_TBL'];
                $sql_query = "INSERT INTO $table (`id`, `account_id`, `character_id`, `scope`, `seed`, `stats`) VALUES (?, ?, ?, ?, ?, ?)";
                $next_id   = getNextTableID($table);
                
                $this->id = $next_id;
                $this->characterID($_SESSION['character-id']);
                $this->accountID($_SESSION['account-id']);

                $tmp_stats = new \Game\Monster\Stats($next_id);
                $srl_data = $tmp_stats->propDump();

                $this->stats = $tmp_stats;

                $db->execute_query($sql_query, [ $next_id, $_SESSION['account-id'], $_SESSION['character-id'], $this->scope->value, $this->seed, $srl_data ]);
                return $next_id;
        }

        return -1;
    }

    private function handle_load($type, $params, $table) {
		global $db;

        $objects = [ 'bracket', 'scope', 'settings', 'privileges', 'bank', 'stats', 'inventory', 'race', 'monster', 'status' ];

        $tmp_obj = null;
        $sql_query = "SELECT * FROM $table WHERE `id` = ?";

		try {
	        $tmp_obj = $db->execute_query($sql_query, [ $this->id ])->fetch_assoc();
		} catch (Exception $e) {
			echo "ahmagad: table: '$table', type: $type, params: " . print_r($params, true), " - plz gooby " . $e->getMessage() . "\n";
			exit();
		}
        
        if ($type == PropType::CHARACTER) {
            if (isset($tmp_obj['inventory'])) {
                $tmp_inventory = new Inventory($this->id);
                $inv_data = $tmp_obj['inventory'];
                $this->inventory = $tmp_inventory->propRestore($inv_data);
            }
            
            if (isset($tmp_obj['stats'])) {
                $tmp_stats = new Stats($this->id);
                $stats_data = $tmp_obj['stats'];
                $this->stats = $tmp_stats->propRestore($stats_data);
            }

            if (isset($tmp_obj['bank'])) {
                $tmp_bank = new BankManager($_SESSION['account-id'], $this->id);
                $bank_data = $tmp_obj['bank'];
                $this->bank = $tmp_bank->propRestore($bank_data);
            }

            if (isset($tmp_obj['monster'])) {
                $tmp_monster = new Monster(MonsterScope::PERSONAL);
                $monster_data = $tmp_obj['monster'];
                $this->monster = $tmp_monster->propRestore($monster_data);
            }

            $this->status = Status::name_to_enum($tmp_obj['status']);
            $this->race   = Races::name_to_enum($tmp_obj['race']);
        } elseif ($type == PropType::ACCOUNT) {
            if (isset($tmp_obj['settings'])) {
                $tmp_settings = new Settings($this->id);
                $settings_data = $tmp_obj['settings'];
                $this->settings = $tmp_settings->propRestore($settings_data);
            }

            $this->privileges = Privileges::name_to_enum($tmp_obj['privileges']);
        } elseif ($type == PropType::MONSTER) {
            $this->stats = $this->propRestore($tmp_obj['stats']);
            $this->scope = MonsterScope::name_to_enum($tmp_obj['scope']);
        } elseif ($type == PropType::FAMILIAR) {
            /* TODO: Familiar loading */
        } elseif ($type == PropType::BANKMANAGER) {
            $this->bracket = BankBracket::name_to_enum($tmp_obj['bracket']);
        }

        foreach ($tmp_obj as $key => $value) {
            if (in_array($key, $objects)) {
                continue;
            }

            $key = $this->tblcol_to_clsprop($key);

            if ($value !== null && $value !== 'NULL' && $value) {
                $this->$key = $value;
            }
        }
    } 
    private function convertToType(mixed $value, string|ReflectionType $type): mixed {
        if ($value === null) {
            return null;
        }

        $typeName = is_string($type) ? $type : $type->__tostring();
        
        // Handle enum types
        if (enum_exists($typeName)) {
            // If value is already the correct enum type, return it
            if ($value instanceof $typeName) {
                return $value;
            }
            
            // Try to convert string to enum
            try {
                // Handle backed enums (with values)
                if (is_subclass_of($typeName, \BackedEnum::class)) {
                    return $typeName::from($value);
                }
                // Handle unit enums (without values)
                return constant("$typeName::$value");
            } catch (ValueError $e) {
                throw new \TypeError("Cannot convert value '$value' to enum type $typeName");
            }
        }

        // Handle regular types
        return match($typeName) {
            'int' => (int)$value,
            'float' => (float)$value,
            'bool' => (bool)$value,
            'string' => (string)$value,
            'array' => (array)$value,
            default => $value
        };
    }
}