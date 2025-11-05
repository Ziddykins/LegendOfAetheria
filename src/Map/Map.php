<?php
namespace Game\Map;
use DateTime;
use Game\Traits\PropSuite\PropSuite;
use Game\Traits\PropSuite\Enums\PropType;

class Map {
    use PropSuite;
    private array $zones = [];
    public bool $first_run;
    public int $created;

    public function __construct($created = null) {
        $timestamp = new DateTime();
        $this->created = $timestamp->getTimestamp();
        $this->first_run = true;
    }

    public function loadZones($map_id): void {
    
    }
}
