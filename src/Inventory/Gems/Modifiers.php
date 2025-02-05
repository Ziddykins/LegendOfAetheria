<?php
namespace Game\Inventory\Gems;

class Modifiers {
    private int $gemID;
    private string $target;
    private int $effect;

    public function __construct($gemID = 0) {
        $this->gemID = $gemID;
    }

}