<?php
namespace Game\Map;
use DateTime;
use Game\Traits\PropSuite\PropSuite;
use Game\Traits\PropSuite\Enums\PropType;

class Map
{
    use PropSuite;
    private array $zones = [];
    private bool $first_run;
    private int $created;

    public function __construct()
    {
        $timestamp = new DateTime();
        $this->created = $timestamp->getTimestamp();
        $this->first_run = true;
    }

    public function loadZones($map_id): void
    {

    }

    public function generateMap(string $map_id): void
    {
        if ($this->first_run) {

        }
    }
}
