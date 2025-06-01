<?php
namespace Game\Inventory\Gems;
use Game\Inventory\Enums\ObjectRarity;
use Game\Inventory\Gems\Enums\GemType;

class Gem {
    private ?int $itemID = null;
    private ?int $socketID = null;
    private ?GemType $type = null;
    private $quality = null;
    private ?ObjectRarity $rarity = null;
    private ?string $name = null;
    private ?GemModifiers $modifiers = null;

    public function __construct($socketID = 0) {
        //$this->rarity = ObjectRarity::getObjectRarity(random_int(0,100));
    }
}