<?php
namespace Game\Inventory\Items;

use Game\Inventory\Gems\Gem;
use Game\Inventory\Items\ItemModifiers;
class Socket {
    private int $socketID = 0;
    private ?int $itemID = null;
    private ?Gem $gem = null;
    private ?ItemModifiers $modifiers = null;

    public function __construct($socketID = 0) {
        $this->socketID = $socketID;
    }

    private function socketGem ($socketID, $gemID) {
        
    }

}
