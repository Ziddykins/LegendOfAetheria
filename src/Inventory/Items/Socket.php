<?php
namespace Game\Inventory\Items;

use Game\Inventory\Gems\Gem;
use Game\Inventory\Items\ItemModifiers;

/**
 * Represents a gem socket within an item.
 * Sockets can hold gems that provide stat modifiers to the equipped item.
 */
class Socket {
    /** @var int Unique identifier for this socket */
    private int $socketID = 0;
    
    /** @var int|null ID of item this socket belongs to */
    private ?int $itemID = null;
    
    /** @var Gem|null Gem inserted into this socket (null if empty) */
    private ?Gem $gem = null;
    
    /** @var ItemModifiers|null Stat modifiers provided by socketed gem */
    private ?ItemModifiers $modifiers = null;

    /**
     * Creates a new socket instance.
     * 
     * @param int $socketID Socket identifier
     */
    public function __construct($socketID = 0) {
        $this->socketID = $socketID;
    }

    /**
     * Inserts a gem into this socket.
     * Implementation pending - will handle gem insertion and modifier application.
     * 
     * @param int $socketID Socket to insert gem into
     * @param int $gemID Gem to be inserted
     * @return void
     */
    private function socketGem ($socketID, $gemID) {
        
    }

}
