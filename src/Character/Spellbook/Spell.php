<?php
    namespace Game\Character\Spellbook;
    use Game\Character\Enums\Status;
    class Spell {
        private int $id;
        private int $bookID;

        private string $name;
        private int $level;
        private int $exp;
        private int $maxExp;
        private int $mpCost;
        private array $statuses;

        public function __construct(int $bookID) {
            $this->bookID = $bookID;
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
    }