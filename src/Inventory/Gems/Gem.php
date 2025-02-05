<?php
namespace Game\Inventory\Gems;
use Game\Inventory\Enums\ObjectRarity;

class Gem {
    private int $itemID;
    private int $socketID;
    private $color;
    private $quality;
    private ObjectRarity $rarity;
    private string $name;
    private Modifiers $modifiers;

    public function __construct($socketID = 0) {
        //$this->rarity = ObjectRarity::getObjectRarity(random_int(0,100));
    }
}