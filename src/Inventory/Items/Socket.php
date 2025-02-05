<?php
namespace Game\Inventory\Items;

class Socket {
    private int $socketID;
    private int $itemID;
    private Gem $gem;
    private ItemModifiers $modifiers;

    public function __construct($socketID = 0) {
        $this->socketID = $socketID;
    }
}
