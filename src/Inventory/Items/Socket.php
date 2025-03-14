<?php
namespace Game\Inventory\Items;

use Game\Inventory\Gems\Gem;
use Game\Inventory\Items\ItemModifiers;
class Socket {
    private int $socketID;
    private int $itemID;
    private Gem $gem;
    private ItemModifiers $modifiers;

    public function __construct($socketID = 0) {
        $this->socketID = $socketID;
    }

    private function socketGem ($socketID, $gemID) {
        
    }

}
