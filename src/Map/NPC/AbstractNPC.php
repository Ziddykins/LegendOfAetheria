<?php
    namespace Game\Map\NPC;
    use Game\Map\NPC\Rumors\Rumor;
    
    class AbstractNPC {
        private int $id;
        private int $locationID = 0;
        private int $hometownID = 0;
        private bool $canTravel = true;
        private array $reputation = [];
        private array $rumors = [];

        /* Holds the ID's of players, along with some data
        relating to their progress through the tutorial */
        private array $players = [];

        public function __construct($accountID, $characterID) {
            $this->accountID = $accountID;
            $this->characterID = $characterID;
            $this->tutorialStep = 0;
        }
    }
?>