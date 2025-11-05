<?php
namespace Game\Inventory\Gems;
use Game\Inventory\Enums\ObjectRarity;
use Game\Inventory\Gems\Enums\GemType;

/**
 * Represents a gem that can be socketed into items to provide stat modifiers.
 * Gems have types (ruby, sapphire, etc.), quality levels, rarity tiers, and stat-modifying effects.
 */
class Gem {
    /** @var int|null ID of item this gem is socketed into */
    private ?int $itemID = null;
    
    /** @var int|null ID of specific socket within the item */
    private ?int $socketID = null;
    
    /** @var GemType|null Type of gem (ruby, sapphire, emerald, etc.) */
    private ?GemType $type = null;
    
    /** @var mixed Quality level of the gem */
    private $quality = null;
    
    /** @var ObjectRarity|null Rarity tier affecting modifier strength */
    private ?ObjectRarity $rarity = null;
    
    /** @var string|null Display name of the gem */
    private ?string $name = null;
    
    /** @var GemModifiers|null Stat modifications this gem provides */
    private ?GemModifiers $modifiers = null;

    /**
     * Creates a new gem instance.
     * Rarity generation commented out - likely determined externally.
     * 
     * @param int $socketID ID of socket this gem will occupy
     */
    public function __construct($socketID = 0) {
        //$this->rarity = ObjectRarity::getObjectRarity(random_int(0,100));
    }
}