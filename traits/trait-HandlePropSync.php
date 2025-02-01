<?php
trait HandlePropSync {
    private function prop_sync($method, $params, $type) {
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

        if ($method == 'prop_sync') {
            return;
        }

        switch ($type) {
            case PropSyncType::STATS:
            case PropSyncType::INVENTORY;
            case PropSyncType::CHARACTER:
                $table = $_ENV['SQL_CHAR_TBL'];
                break;
            case PropSyncType::ACCOUNT:
                $table = $_ENV['SQL_ACCT_TBL'];
                $identifier = 'email';
                break;
            case PropSyncType::FAMILIAR:
                $table = $_ENV['SQL_FMLR_TBL'];
                break;
            default:
                exit(LOAError::FUNCT_PROPSYNC_TYPE);
        }

        $log->error("In PropSync", [
            'Table' => $table,
            'Method' => $method,
            'Params' => print_r($params, true)
        ]);

        if (strcmp($action, "get") === 0) { /* GET */
            $log->error("PROPSYNC GET", [ 'Type' => $type, 'Prop' => $prop, 'Return' => $this->$prop ]);
            return $this->$prop;
        } elseif (strcmp($action, 'set') === 0) { /* SET */
            $id = $this->id;
            $sql_query = null;
            $table_col = null;

            $this->$prop = $params[0];

            if ($type == PropSyncType::STATS) {
                $table_col = 'stats';
                $params[0] = serialize($this);
                $sql_query = "UPDATE $table SET `$table_col` = '{$params[0]}' WHERE `id` = ?";
                $id = $_SESSION['character-id'];
            } elseif ($type == PropSyncType::INVENTORY) {
                $table_col = 'inventory';
                $params[0] = serialize($this);
                $sql_query = "UPDATE $table SET `$table_col` = '{$params[0]}' WHERE `id` = ?";
                $id = $_SESSION['character-id'];
            } else {
                $sql_query = "UPDATE $table ";
                $table_col = $this->clsprop_to_tblcol($prop);

                if (is_int($params[0])) {
                    $sql_query .= "SET `$table_col` = {$params[0]} ";
                } else {
                    $sql_query .= "SET `$table_col` = '{$params[0]}' ";
                }

                $sql_query .= "WHERE `id` = ?";
            }

            $db->execute_query($sql_query, [$id]);

            $log->error("PROPSYNC SET",
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
                case PropSyncType::ACCOUNT:
                    $accountID = getNextTableID($_ENV['SQL_ACCT_TBL']);
                    $focused_id = $accountID;
                    $sql_query = "INSERT INTO $table (id, email) VALUES (?, ?)";

                    $db->execute_query($sql_query, [$accountID, $this->email]);
                    $this->id = $accountID;
                    $log->error("PROPSYNC NEW ACCOUNT", [ 'Account ID' => $accountID ]);
                    break;
                case PropSyncType::CHARACTER:
                    if (isset($params[0])) {
                        $next_slot = $params[0];
                    } else {
                        $next_slot = $this->getNextCharSlotID($this->accountID);
                    }
                    $char_id = getNextTableId($_ENV['SQL_CHAR_TBL']);
                    $focused_id = $char_id;
                    $char_col = "char_slot$next_slot";

                    $log->error("PROPSYNC NEW CHAR", [ 'Next Slot' => $next_slot, 'Char ID' => $char_id ]);

                    if ($next_slot == -1) {
                        header('Location: /select?no_slots');
                        exit();
                    }
                    
                    $this->id = $char_id;
                    $tmp_inventory = new Inventory();
                    $tmp_stats = new Stats($char_id);
                    
                    $srl_inventory = serialize($tmp_inventory);
                    $srl_stats = serialize($tmp_stats);

                    $this->inventory = $tmp_inventory;
                    $this->stats = $tmp_stats;

                    $chr_query = "INSERT INTO $table (`id`, `account_id`, `inventory`, `stats`) VALUES (?, ?, ?, ?)";
                    $act_query = "UPDATE {$_ENV['SQL_ACCT_TBL']} SET `$char_col` = ? WHERE `id` = ?";

                    $db->execute_query($chr_query, [$char_id, $this->accountID, $srl_inventory, $srl_stats]);
                    $db->execute_query($act_query, [$this->id, $this->accountID]);
                    break;
            }
        } elseif (strcmp($action, 'load') === 0) { /*LOAD*/
            $sql_query = "SELECT * FROM $table WHERE `id` = ?";
            $tmp_char = $db->execute_query($sql_query, [$this->id])->fetch_assoc();
            
            foreach ($tmp_char as $key => $value) {
                $key = $this->tblcol_to_clsprop($key);
                $this->$key = $value;
            }

            if ($type == PropSyncType::CHARACTER) {
                $tmp_inv = unserialize($tmp_char['inventory']);
                $tmp_stats = unserialize($tmp_char['stats']);

                $this->inventory = $tmp_inv;
                $this->stats = $tmp_stats;
            }

            $log->error("PROPSYNC LOAD {$type->name}" . print_r($this, true));
        }
    }

    private function assoc_to_object(array &$assoc, string $class) {
        $new_obj = new $class();

        foreach ($assoc as $key => $value) {
            switch ($key) {
                case 'inventory':
                    $new_obj->set_inventory(unserialize($value));
                    break;
                case 'stats':
                    $new_obj->set_stats(unserialize($value));
                    break;
                case 'monster':
                    $new_obj->set_monster(unserialize($value));
                    break;
                default:
                    $new_obj->$key = $value;
                    break;
            }
        }

        return $new_obj;
    }

    public static function getProps($obj_type_str): array {
        return get_class_vars($obj_type_str);
    }
}