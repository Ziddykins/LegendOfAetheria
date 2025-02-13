<?php
namespace Game\Traits;

use Exception;
use Game\Character\Stats;
use Game\Inventory\Inventory;
use Game\Monster\Monster;
use Game\Monster\Enums\Scope;
use Game\System\Enums\LOAError;
use Game\Traits\Enums\Type;
use Game\Traits\PropConvert;

trait PropSync {

    
    private function propSync($method, $params, Type $type) {
        global $db, $log;
        $table = null;
        $prop  = null;

        /* Anything before the underscore, in the sent method is considered
           to be the "action" (set_, get_, init, etc), and the target/property
           is after the underscore (email, accountID, etc)
        */
        $offset = strpos($method, "_");
        // offset = length in this case
        $action = substr($method, 0, $offset);

        if ($offset === false) {
            $action = $method;
        } else {
            $prop = lcfirst(substr($method, $offset + 1));
        }

        if ($method == 'propSync') {
            return;
        }

        switch ($type) {
            case Type::CSTATS;
            case Type::CHARACTER:
            case Type::INVENTORY;
                $table = $_ENV['SQL_CHAR_TBL'];
                break;
            case Type::ACCOUNT;
                $table = $_ENV['SQL_ACCT_TBL'];
                break;
            case Type::FAMILIAR:
                $table = $_ENV['SQL_FMLR_TBL'];
                break;
            case Type::MONSTER:
            case Type::MSTATS;
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
                case Type::CSTATS:
                case Type::MSTATS:
                    $tbl = $type == Type::MSTATS
                        ? $tbl = $_ENV['SQL_MNST_TBL']
                        : $tbl = $_ENV['SQL_CHAR_TBL'];

                    $id = $this->id;
                    $srl_data = safe_serialize($this);
                    $sql_query = "UPDATE $tbl SET `stats` = ? WHERE `id` = ?";
                    $db->execute_query($sql_query, [ $srl_data, $this->id ]);
                    return;
                case Type::INVENTORY:
                    $id = $this->id;
                    $srl_data = safe_serialize($this);
                    $sql_query = "UPDATE {$_ENV['SQL_CHAR_TBL']} SET `inventory` = ? WHERE `id` = ?";
                    $db->execute_query($sql_query, [ $srl_data, $_SESSION['character-id'] ]);
                    return;
                case Type::MONSTER:
                    if (isset($params[1]) && $params[1] === 'false') {
                        return;
                    }

                    $srl_data = safe_serialize($this);
                    $sql_query = "UPDATE {$_ENV['SQL_CHAR_TBL']} SET `monster` = ? WHERE `id` = ?";
                    $db->execute_query($sql_query, [ $srl_data, $this->characterID ]);
                    
                    if ($prop === 'scope') {
                        $params[0] = $params[0]->value;
                    }
                    break;
                case Type::CHARACTER:
                    $id = $this->id;

                    if ($prop === 'monster') {
                        $params[0] = safe_serialize($params[0]);
                    }
                    
                    break;

            }

            $sql_query = "UPDATE $table SET `$table_col` = ? WHERE `id` = ?";    
            $db->execute_query($sql_query, [ $params[0], $id ]);
        } elseif (strcmp($action, 'new') === 0) { /*NEW*/
            $focused_id = null;

            switch ($type) {
                case Type::ACCOUNT:
                    $accountID  = getNextTableID($_ENV['SQL_ACCT_TBL']);
                    $sql_query  = "INSERT INTO $table (id, email) VALUES (?, ?)";
                    $this->id = $accountID;
                    $db->execute_query($sql_query, [$accountID, $this->email]);
                    return $accountID;
                case Type::CHARACTER:
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
                case Type::MONSTER:
                    $table     = $_ENV['SQL_MNST_TBL'];
                    $sql_query = "INSERT INTO $table (`id`, `account_id`, `character_id`, `scope`, `seed`, `stats`) VALUES (?, ?, ?, ?, ?, ?)";
                    $next_id   = getNextTableID($table);
                    
                    $this->id = $next_id;
                    $this->characterID($_SESSION['character-id']);
                    $this->accountID($_SESSION['account-id']);

                    $tmp_stats = new \Game\Monster\Stats($next_id);
                    $srl_data = safe_serialize($tmp_stats);

                    $this->stats = $tmp_stats;

                    $log->debug("Monster insert", [ 'NextID' => $next_id, 'Query' => $sql_query ]);
                    $db->execute_query($sql_query, [ $next_id, $_SESSION['account-id'], $_SESSION['character-id'], $this->scope->value, $this->seed, $srl_data ]);
                    return $next_id;
            }
        } elseif (strcmp($action, 'load') === 0) { /*LOAD*/
            $tmp_obj = null;

            if ($type === Type::MONSTER) {
                $scope = $params[0];
                $sql_query = "SELECT * FROM {$_ENV['SQL_MNST_TBL']} WHERE `scope` = ?";
                $tmp_obj = $db->execute_query($sql_query, [ $scope->value]);
            } else {
                $sql_query = "SELECT * FROM $table WHERE `id` = ?";
                $tmp_obj = $db->execute_query($sql_query, [ $this->id ])->fetch_assoc();
            }
            
            foreach ($tmp_obj as $key => $value) {
                $key = $this->tblcol_to_clsprop($key);

                if ($value !== null || $value !== 'NULL') {
                    $this->$key = $value;
                }
            }

            if ($type == Type::CHARACTER) {
                $tmp_inv     = safe_serialize($tmp_obj['inventory'], true);
                $tmp_stats   = safe_serialize($tmp_obj['stats'],     true);

                if ($tmp_obj['monster']) {
                    $tmp_monster   = safe_serialize($tmp_obj['monster'], true);
                    $this->monster = $tmp_monster;
                }

                $this->inventory = $tmp_inv;
                $this->stats     = $tmp_stats;
            }
        } elseif (preg_match('/sub|add|mul|div|exp|mod/', $action)) {
            $cur_value = null;
            
            if (isset($this->$prop)) {
                $cur_value = (int)$this->$prop;
            }

            if (is_numeric($cur_value)) {
                $new_value = $cur_value;
                switch ($action) {
                    case 'add':
                        $new_value += $params[0];
                        break;
                    case 'sub':
                        $new_value -= $params[0];
                        break;
                    case 'mul':
                        $new_value *= $params[0];
                        break;
                    case 'div':
                        if (!$params[0]) {
                            return;
                        }
                        $new_value /= $params[0];

                        break;
                    case 'exp':
                        $new_value **= $params[0];
                        break;
                    case 'mod':
                        if (!$params[0]) {
                            return;
                        }
                        $new_value %= $params[0];
                        break;
                    default:
                        throw new Exception('Unknown PropModder');
                }
                $func = "set_$prop";
                $this->$func($new_value);
            } else {
                throw new Exception('Current value is not numeric!');
            }
        }
      
    }
}