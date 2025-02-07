<?php
namespace Game\Traits;

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
        $table = 'none';

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
            case Type::STATS;
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
                $table = $_ENV['SQL_MNST_TBL'];
                break;
            default:
                exit(LOAError::FUNCT_PROPSYNC_TYPE);
        }

        $log->debug("In PropSync", [
            'Table' => $table,
            'Method' => $method,
            'Params' => print_r($params, true)
        ]);

        if (strcmp($action, "get") === 0) { /* GET */
            $log->debug("PROPSYNC GET", [ 'Type' => $type, 'Prop' => $prop, 'Return' => $this->$prop ]);
            return $this->$prop;
        } elseif (strcmp($action, 'set') === 0) { /* SET */
            $id = $this->id;
            $sql_query = null;
            $table_col = null;

            $this->$prop = $params[0];

            switch ($type) {
                case Type::STATS:
                    $table_col = 'stats';
                    $srl_data = safe_serialize($this);
                    $sql_query = "UPDATE $table SET `stats` = ? WHERE `id` = ?";
                    $db->execute_query($sql_query, [ $srl_data, $id ]);
                    return;
                case Type::INVENTORY:
                    $table_col = 'inventory';
                    $srl_data = safe_serialize($this);
                    $sql_query = "UPDATE $table SET `inventory` = ? WHERE `id` = ?";
                    $id = $_SESSION['character-id'];
                    $db->execute_query($sql_query, [ $srl_data, $this->id ]);
                    return;
                case Type::MONSTER:
                    $table_col = 'monster';
                    $srl_data = safe_serialize($this);
                    $sql_query_char = "UPDATE {$_ENV['SQL_CHAR_TBL']} SET `monster` = ? WHERE `id` = ?";
                    $db->execute_query($sql_query_char, [ $srl_data, $this->characterID ]);
            }

            $sql_query = "UPDATE $table ";
            $table_col = $this->clsprop_to_tblcol($prop);

            $sql_query .= "SET `$table_col` = ? WHERE `id` = ?";

            $db->execute_query($sql_query, [$params[0], $id ]);

            $log->debug("PROPSYNC SET",
                [
                    'SQLQuery' => $sql_query,
                    'params' => print_r($params, 1),
                    'method' => $method,
                    'prop' => $prop,
                    'type' => $type->name
                ]
            );
        } elseif (strcmp($action, 'new') === 0) { /*NEW*/
            $focused_id = null;

            switch ($type) {
                case Type::ACCOUNT:
                    $accountID  = getNextTableID($_ENV['SQL_ACCT_TBL']);
                    $focused_id = $accountID;
                    $sql_query  = "INSERT INTO $table (id, email) VALUES (?, ?)";

                    $db->execute_query($sql_query, [$accountID, $this->email]);
                    $this->id = $accountID;
                    $log->debug("PROPSYNC NEW ACCOUNT", [ 'Account ID' => $accountID ]);
                    break;
                case Type::CHARACTER:
                    if (isset($params[0])) {
                        $next_slot = $params[0];
                    } else {
                        $next_slot = $this->getNextCharSlotID($this->accountID);
                    }
                    $char_id    = getNextTableId($_ENV['SQL_CHAR_TBL']);
                    $focused_id = $char_id;
                    $char_col   = "char_slot$next_slot";

                    $log->debug("PROPSYNC NEW CHAR", [ 'Next Slot' => $next_slot, 'Char ID' => $char_id ]);

                    if ($next_slot == -1) {
                        header('Location: /select?no_slots');
                        exit();
                    }
                    
                    $this->id = $char_id;
                    $tmp_inventory = new Inventory();
                    $tmp_stats     = new Stats($char_id);
                    $tmp_monster   = new Monster(Scope::PERSONAL, $char_id);

                    
                    $srl_inventory = safe_serialize($tmp_inventory);
                    $srl_stats     = safe_serialize($tmp_stats);
                    $srl_monster   = safe_serialize($tmp_monster);

                    $this->inventory = $tmp_inventory;
                    $this->stats     = $tmp_stats;
                    $this->monster   = $tmp_monster;

                    $chr_query = "INSERT INTO $table (`id`, `account_id`, `inventory`, `stats`, `monster`) VALUES (?, ?, ?, ?, ?)";
                    $act_query = "UPDATE {$_ENV['SQL_ACCT_TBL']} SET `$char_col` = ? WHERE `id` = ?";

                    $db->execute_query($chr_query, [ $char_id, $this->accountID, $srl_inventory, $srl_stats, $srl_monster ]);
                    $db->execute_query($act_query, [$this->id, $this->accountID]);
                    break;
            }
        } elseif (strcmp($action, 'load') === 0) { /*LOAD*/
            $sql_query = "SELECT * FROM $table WHERE `id` = ?";
            $tmp_char  = $db->execute_query($sql_query, [$this->id])->fetch_assoc();
            
            foreach ($tmp_char as $key => $value) {
                $key = $this->tblcol_to_clsprop($key);
                $this->$key = $value;
            }

            if ($type == Type::CHARACTER) {
                $tmp_inv     = safe_serialize($tmp_char['inventory'], true);
                $tmp_stats   = safe_serialize($tmp_char['stats'], true);
                $tmp_monster = safe_serialize($tmp_char['monster'], true);

                $this->inventory = $tmp_inv;
                $this->stats     = $tmp_stats;
                $this->monster   = $tmp_monster;
            }

            $log->debug("PROPSYNC LOAD {$type->name}" . print_r($this, true));
        }
    }
}