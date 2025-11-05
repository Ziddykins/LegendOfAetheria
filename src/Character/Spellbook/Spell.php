<?php
    namespace Game\Character\Spellbook;
    use Game\Character\Enums\Status;
    
    /**
     * Represents an individual spell within a character's spellbook.
     * Spells have levels, experience points, MP costs, and can inflict status effects on targets.
     * Uses custom __call() magic method for dynamic get_/set_ access to private properties.
     * 
     * @method int get_id() Gets spell ID
     * @method int get_bookID() Gets parent spellbook ID
     * @method string get_name() Gets spell name
     * @method int get_level() Gets current spell level
     * @method int get_exp() Gets current experience points
     * @method int get_maxExp() Gets experience needed for next level
     * @method int get_mpCost() Gets MP cost to cast this spell
     * @method array get_statuses() Gets array of Status effects this spell can inflict
     * 
     * @method void set_id(int $id) Sets spell ID
     * @method void set_bookID(int $bookID) Sets parent spellbook ID
     * @method void set_name(string $name) Sets spell name
     * @method void set_level(int $level) Sets spell level
     * @method void set_exp(int $exp) Sets experience points
     * @method void set_maxExp(int $maxExp) Sets experience threshold
     * @method void set_mpCost(int $mpCost) Sets MP cost
     * @method void set_statuses(array $statuses) Sets status effects array
     */
    class Spell {
        /** @var int Unique spell identifier */
        private int $id;
        
        /** @var int ID of the spellbook this spell belongs to */
        private int $bookID;

        /** @var string Display name of the spell */
        private string $name;
        
        /** @var int Current level of the spell (affects power/effects) */
        private int $level;
        
        /** @var int Current experience points earned by using this spell */
        private int $exp;
        
        /** @var int Experience points needed to reach next level */
        private int $maxExp;
        
        /** @var int Mana points consumed when casting this spell */
        private int $mpCost;
        
        /** @var array<Status> Status effects this spell can inflict on targets */
        private array $statuses;

        /**
         * Creates a new spell instance.
         * 
         * @param int $bookID ID of the parent spellbook
         */
        public function __construct(int $bookID) {
            $this->bookID = $bookID;
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
    }