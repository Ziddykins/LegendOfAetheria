<?php

namespace Game\Map\Zones;

class Zone {
    private string $name;
    private array $zcoords;
    private array $bounds = [-100, 100, -100, 100];
    private int $maxLevel = 25;
    private string $alignment = 'neutral';
    private array $locations;
    private array $connections;

    public function __construct(string $name, array $zcoords, array $limits) {
        $this->name = $name;
        $this->zcoords = $zcoords;
        $this->limits = $limits;
    }
}
