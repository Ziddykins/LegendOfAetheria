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
        
            $sql_query = 'INSERT INTO ' . $_ENV['SQL_FMLR_TBL'] . ' ' .
                         '(character_id, date_acquired, hatch_time, level) ' .
                         "VALUES ($this->characterID, NOW(), (NOW() + INTERVAL 8 HOUR), 1)";

            $db->query($sql_query);

            

            $sql_query    = 'SELECT `id` FROM ' . $_ENV['SQL_FMLR_TBL'] . " WHERE `character_id` = $this->characterID";
            $result       = $db->query($sql_query);

            $familiar_id  = $result->fetch_assoc();
            $this->id     = $familiar_id['id'];

            $log->info("New familiar $this->id registered for character $this->characterID");
            $this->saveFamiliar();
        }

        public function saveFamiliar() {
            global $log, $db;

            $sql_query = 'UPDATE ' . $_ENV['SQL_FMLR_TBL'] . ' ' .
                         'SET ' .
                             "`name` = '$this->name', " .
                             "`level` = '$this->level', " .
                             "`rarity` = '" . $this->rarity->name . "', " .
                             "`rarity_color` = '$this->rarityColor', " .
                             "`hatch_time` = '$this->hatchTime', " .
                             "`date_acquired` = '$this->dateAcquired', " .
                             "`hatched` = '$this->hatched' " .
                         'WHERE ' .
                             "`id` = $this->id";

            $db->query($sql_query); 
            $log->info("Saved familiar", [ 'Character ID' => $this->id, 'Name' => $this->name ]);
        }

        public function getCard($which = 'current') {
            $html = null;

            if ($which === 'current') {
                if ($this->rarity === '') {
                    $html = '<div class="container text-center d-flex justify-content-center align-items-center">
                                <div class="row">
                                    <div class="col">
                                        <div class="card mb-3" style="max-width: 700px;">
                                            <div class="row g-0">
                                                <div class="col-4">
                                                    <img src="img/avatars/unknown.png" class="img-fluid rounded m-3" alt="character-avatar">
                                                </div>
                                                <div class="col-6">
                                                    <div class="card-body">
                                                        <h3 class="card-title">No Egg!</h3>
                                                        <div class="container">
                                                            <div class="row mb-3">
                                                                <div class="col-4">
                                                                    <h5>Every new player is gifted one egg</h5>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col-4">
                                                                    <button class="btn btn-primary" id="generate-egg" name="generate-egg" value="1">Generate</button>
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
                $var = lcfirst(substr($method, 4));

                if (strncasecmp($method, "get_", 4) === 0) {
                    return $this->$var;
                }
            
                if (strncasecmp($method, "set_", 4) === 0) {
                    $this->$var = $params[0];
                }
            }
        }
?>
