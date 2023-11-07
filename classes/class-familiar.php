<?php
    class Familiar {
        protected $characterID;
        protected $name;
        protected $rarity;
        protected $rarityColor;
        protected $level;
        protected $hatchTime;
        protected $dateAcquired;
        protected $hatched;
        protected $id;

        public function __construct($characterID) {
            $this->characterID = $characterID;
        }

        public function registerFamiliar() {
            global $db, $log;

            $sql_time   = get_mysql_datetime();
            $hatch_time = get_mysql_datetime('+8 hours');

            $log->critical('New SQL time function ah damn!',
                [
                    'SQLTime' => $sql_time,
                    'SQLNewTime' => $hatch_time,
                ]
            );

            $sql_query = 'INSERT INTO ' . $_ENV['SQL_FMLR_TBL'] .
                        '   (`character_id`) ' .
                         "VALUES ($this->characterID)";
            $db->query($sql_query);

            $sql_query    = 'SELECT `id` FROM ' . $_ENV['SQL_FMLR_TBL'] . " WHERE `character_id` = $this->characterID";
            $result       = $db->query($sql_query);

            $familiar_id  = $result->fetch_assoc();

            $this->dateAcquired = NULL;
            $this->rarityColor  = '#000';
            $this->hatchTime    = NULL;
            $this->hatched      = NULL;
            $this->rarity       = 'NONE';
            $this->name         = '!Unset!';
            $this->id           = $familiar_id['id'];

            $log->info("New familiar $this->id registered for character $this->characterID");
        }

        public function saveFamiliar() {
            global $log, $db;

            echo '<pre>';
            print_r($this);
            echo '</pre>';

            $sql_query = 'UPDATE ' . $_ENV['SQL_FMLR_TBL'] . ' ' .
                         'SET ' .
                             "`name`          = '$this->name', " .
                             "`rarity`        = '" . $this->rarity->name . "', " .
                             "`rarity_color`  = '$this->rarityColor', " .
                             "`hatch_time`    = '$this->hatchTime', " .
                             "`date_acquired` = '$this->dateAcquired', " .
                             "`hatched`       = '$this->hatched', " .
                             "`level`         =  $this->level " .
                         'WHERE ' .
                             "`id` = $this->id";

            $db->query($sql_query); 
            $log->info("Saved familiar", [ 'Character ID' => $this->id, 'Name' => $this->name ]);
        }

        public function getCard($which = 'current') {
            $html = null;

            if ($which === 'empty') {
                $html = '<div class="container text-center d-flex justify-content-center align-items-center">
                            <div class="row">
                                 <div class="col">
                                    <div class="card mb-3" style="max-width: 600px;">
                                        <div class="row g-0">
                                            <div class="col-2">
                                                <img src="img/avatars/avatar-unknown.jpg" class="img-fluid rounded-circle m-3" alt="character-avatar">
                                            </div>
                                            <div class="col ms-4">
                                                <div class="card-body">
                                                    <h3 class="card-title bg-warning border g-0">No Egg!</h3>
                                                    <div class="container text-center border">
                                                        <div class="row">  
                                                            <div class="col">
                                                                <strong>Claim your Tier 4 Starter Egg below!</strong><br />
                                                                Its rarity will be chosen at random, but you will be guaranteed at least<br />
                                                                tier 4. < More text explaining the hatch process and familiar >
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <div class="col">
                                                                <form id="generate-egg" name="generate-egg" action="?page=eggs" method="POST">
                                                                    <div class="input-group">
                                                                        <span class="input-group-text" id="name-icon" name="name-icon">
                                                                            <span class="material-symbols-sharp">cognition</span>
                                                                        </span>
                                                                        <div class="form-floating">
                                                                            <input type="text" class="form-control" id="egg-name" name="egg-name">
                                                                            <label for="egg-name">Egg Name (optional)</label>
                                                                        </div>
                                                                    </div>
                                                                    <small>You can wait to name your familiar until when it hatches if you\'d prefer</small>
                                                                    
                                                                    <button class="btn btn-primary" id="generate-egg" name="generate-egg" value="1">Collect Egg</button>
                                                                    <input type="hidden" id="action" name="action" value="generate">
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>';
                }
                return $html;
            }

            public function loadFamiliar($characterID) {
                global $db, $log;

                $sql_query = 'SELECT * FROM ' . $_ENV['SQL_FMLR_TBL'] . " WHERE `character_id` = $characterID";
                $result = $db->query($sql_query);

                if ($result->num_rows == 0) {
                    $log->warning('Attempted to load familiar but no corresponding character ID found: ' . $characterID);
                    $this->registerFamiliar();
                    return;
                }

                $familiar = $result->fetch_assoc();

                $this->characterID  = $familiar['character_id'];
                $this->id           = $familiar['id'];
                $this->name         = $familiar['name'];
                $this->rarity       = $familiar['rarity'];
                $this->rarityColor  = $familiar['rarity_color'];
                $this->hatched      = $familiar['hatched'];
                $this->dateAcquired = $familiar['date_acquired'];
                $this->hatchTime    = $familiar['hatch_time'];

                $log->info("Familiar ID $this->id loaded for character ID $this->characterID");
            }

            function __call($method, $params) {
                global $log, $db;

                $var = lcfirst(substr($method, 4));
                $params[0] = clsprop_to_tblcol($params[0]);

                if (strncasecmp($method, "get_", 4) === 0) {
                    $log->info("'get_' triggered for var '$var'; returning '" . $params[0] . "'");
                    return $this->$var;
                }
                
                if (strncasecmp($method, "set_", 4) === 0) {
                    $sql_query =  'UPDATE ' . $_ENV['SQL_FMLR_TBL'] . ' ';

                    if (is_int($params[0])) {
                        $sql_query .= "SET `$var` = " . $params[0] . " ";
                    } else {
                        $sql_query .= "SET `$var` = '" . $params[0] . "' ";
                    }

                    $sql_query .= 'WHERE `id` = ' . $this->id;
                    
                    $db->query($sql_query);
                    $this->$var = $params[0];

                    $log->info("'set_' triggered for var '\$this->$var'; assigning '" . $params[0] . "' to it", [ 'SQLQuery' => $sql_query ]);

                }
            }
        }
?>
