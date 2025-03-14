<?php
namespace Game\Inventory\Gems;
use Game\Inventory\Enums\ObjectRarity;
use Game\Inventory\Gems\Enums\GemType;

class Gem {
    private int $itemID;
    private int $socketID;
    private GemType $type;
    private $quality;
    private ObjectRarity $rarity;
    private string $name;
    private GemModifiers $modifiers;

    public function __construct($socketID = 0) {
        //$this->rarity = ObjectRarity::getObjectRarity(random_int(0,100));
    }
}