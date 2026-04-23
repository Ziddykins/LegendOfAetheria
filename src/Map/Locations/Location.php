<?php
namespace Game\Map\Locations;
use Game\Traits\PropSuite\PropSuite;
use Game\Traits\PropSuite\Enums\PropType;

class Location {
    use PropSuite;
    
    private string $name;
    private array $coords;
    private int $radius = 10;
    private bool $pvp = false;
    private bool $pve = false;
    private array $npcs = [];
    
    public function __construct(string $name, array $coords) {
        $this->name = $name;
        $this->coords = $coords;
    }

}
