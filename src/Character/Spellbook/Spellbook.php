<?php
    namespace Game\Character\Spellbook;
    use Game\Character\Enums\Status;
    
    class Spellbook {
        private int $id;
        private int $accountID;
        private int $characterID;
        private array $spells;
        private int $maxSpells;
        private int $maxLevel;

        public function __construct(int $accountID, int $characterID) {
            $this->accountID = $accountID;
            $this->characterID = $characterID;
            $this->spells = [];
            $this->maxSpells = 10; // Default max spells
            $this->maxLevel = 100; // Default max level
        }

        private function register_spell(Spell $spell) {

        }        
        
        public function __call($method, $params) {
            $var = lcfirst(substr($method, 4));

            if (strncasecmp($method, "get_", 4) === 0) {
                return $this->$var;
            }

            if (strncasecmp($method, "set_", 4) === 0) {
                $this->$var = $params[0];
            }
        }

        private function unregister_spell($spellID) {

        }

        private function populate_starter_spells() {
            $burn = new Spell($this->id);
            $burn->set_name('Burn');
            $burn->set_level(1);
            $burn->set_exp(0);
            $burn->set_maxExp(100);
            $burn->set_mpCost(10);
            $burn->set_statuses([ Status::BURNING, Status::OVERHEATED ]);
        }
    }
?>