<?php
class Monster {
    protected $id;
    protected $accountID;
    protected $name;
    protected $scope;
    protected $seed;
    protected $summondBy;
    protected $hp;
    protected $maxHP;
    protected $mp;
    protected $maxMP;
    protected $strength;
    protected $intelligence;
    protected $defense;
    protected $dropLevel;
    protected $expAwarded;
    protected $goldAwarded;
    protected $monsterClass;

    protected $monsters;

    protected $table;

    /**
    * Magic method to handle dynamic getters and setters for the Monster class properties.
    *
    * This method intercepts calls to properties that start with "get_" or "set_" and performs
    * the corresponding actions. For getters, it logs the access and returns the property value.
    * For setters, it updates the corresponding database column and logs the operation.
    *
    * @param string $method The name of the method being called.
    * @param array $params The parameters passed to the method.
    *
    * @return mixed The value of the property if a getter is called, or void if a setter is called.
    */
    public function __call($method, $params) {
        global $log, $db;
        $caller = debug_backtrace()[1]['function'];

        $var = lcfirst(substr($method, 4));

        if (strncasecmp($method, "get_", 4) === 0) {
            $log->info("'get_' triggered for var '$var'; returning '" . $this->$var . "'");
            return $this->$var;
        }

        if (strncasecmp($method, "set_", 4) === 0) {
            /*$sql_query =  "UPDATE $this->table ";
            $table_col = clsprop_to_tblcol($var);

            if (is_int($params[0])) {
                $sql_query .= "SET `$table_col` = " . $params[0] . " ";
            } else {
                $sql_query .= "SET `$table_col` = '" . $params[0] . "' ";
            }

            $sql_query .= 'WHERE `id` = ' . $this->id;
*/
            //$db->query($sql_query);
            $this->$var = $params[0];

            /*$log->info("'set_' triggered for var '\$this->$var'; assigning '" . $params[0] . "' to it",
                [
                    'SQLQuery'    => $sql_query,
                    'CallingFunc' => $caller,
                    'PropToCol'   => $table_col
                ]
            );*/
        }

    }
    
    public function __construct(MonsterScope $scope, $account_id = null, $table_name) {
        $this->table     = $table_name;
    }

    

    function random_monster($dropLevel = 0) {
        
    }
}

class MonsterPool {
    public $monsters = [];

    private function get_monster_count() {
        return count($this->monsters);
    }
}

?>
