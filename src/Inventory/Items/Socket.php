<?php
namespace Game\Inventory\Items;

use Game\Inventory\Gems\Gem;
use Game\Inventory\Items\Modifiers;
class Socket {
    private int $socketID;
    private int $itemID;
    private Gem $gem;
    private Modifiers $modifiers;

    public function __construct($socketID = 0) {
        $this->socketID = $socketID;
    }

    private function socketGem ($socketID, $gemID) {
        
    }

}
