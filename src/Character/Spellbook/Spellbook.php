<?php
    namespace Game\Character\Spellbook;
    use Game\Character\Enums\Status;
    
    /**
     * Manages a character's collection of spells, spell registration, and spell capacity.
     * Each character has one spellbook that tracks learned spells, spell slots, and maximum levels.
     * Uses custom __call() magic method for dynamic get_/set_ access to private properties.
     * 
     * @method int get_id() Gets spellbook ID
     * @method int get_accountID() Gets owning account ID
     * @method int get_characterID() Gets owning character ID
     * @method array get_spells() Gets array of Spell objects
     * @method int get_maxSpells() Gets maximum number of spells allowed
     * @method int get_maxLevel() Gets maximum spell level achievable
     * 
     * @method void set_id(int $id) Sets spellbook ID
     * @method void set_accountID(int $accountID) Sets account ID
     * @method void set_characterID(int $characterID) Sets character ID
     * @method void set_spells(array $spells) Sets spell collection
     * @method void set_maxSpells(int $maxSpells) Sets spell capacity limit
     * @method void set_maxLevel(int $maxLevel) Sets maximum spell level
     */
    class Spellbook {
        /** @var int Unique spellbook identifier */
        private int $id;
        
        /** @var int Account ID that owns this spellbook */
        private int $accountID;
        
        /** @var int Character ID that owns this spellbook */
        private int $characterID;
        
        /** @var array<Spell> Collection of learned spells */
        private array $spells;
        
        /** @var int Maximum number of spells this character can learn */
        private int $maxSpells;
        
        /** @var int Maximum level any spell in this book can reach */
        private int $maxLevel;

        /**
         * Creates a new spellbook for a character.
         * Initializes with empty spell array and default capacity/level limits.
         * 
         * @param int $accountID Account that owns the character
         * @param int $characterID Character this spellbook belongs to
         */
        public function __construct(int $accountID, int $characterID) {
            $this->accountID = $accountID;
            $this->characterID = $characterID;
            $this->spells = [];
            $this->maxSpells = 10; // Default max spells
            $this->maxLevel = 100; // Default max level
        }

        /**
         * Adds a new spell to the spellbook.
         * Implementation pending - will handle spell capacity checks and database persistence.
         * 
         * @param Spell $spell Spell object to register
         * @return void
         */
        private function register_spell(Spell $spell) {

        }        
        
        /**
         * Magic method to provide dynamic getter/setter access to private properties.
         * Supports get_propertyName() and set_propertyName($value) method patterns.
         * 
         * @param string $method Method name (must start with get_ or set_)
         * @param array $params Parameters passed to method
         * @return mixed Returns property value for getters, void for setters
         */
        public function __call($method, $params) {
            $var = lcfirst(substr($method, 4));

            if (strncasecmp($method, "get_", 4) === 0) {
                return $this->$var;
            }

            if (strncasecmp($method, "set_", 4) === 0) {
                $this->$var = $params[0];
            }
        }

        /**
         * Removes a spell from the spellbook.
         * Implementation pending - will handle spell removal and database cleanup.
         * 
         * @param int $spellID ID of spell to remove
         * @return void
         */
        private function unregister_spell($spellID) {

        }

        /**
         * Populates the spellbook with default starter spells for new characters.
         * Currently creates a "Burn" spell with BURNING and OVERHEATED status effects.
         * 
         * @return void
         */
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