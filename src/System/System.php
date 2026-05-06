<?php
namespace Game\System;

/**
 * Manages system-level game state including monsters and weather for zones.
 * Loads monster data from external files and tracks zone-specific conditions.
 */
class System {
    /** @var array Monster data loaded from file */
    public $monsters = [];
    
    /** @var mixed Current weather conditions */
    private $weather;
    
    /** @var mixed Zone identifier this system manages */
    private $zone_id;

    /**
     * Creates a system instance for a specific zone.
     * 
     * @param mixed $zone_id Zone identifier
     */
    public function __construct($zone_id) {
        $this->zone_id = $zone_id;
    }

    /**
     * Loads monster data from monsters.raw file.
     * Reads line-by-line and populates monsters array.
     * 
     * @return void
     */
    public function load_sheet() {
        global $log;

        $handle = fopen(WEBROOT . '/monsters.raw', 'r');
        
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                array_push($this->monsters, $line);
            }
        }
    }
}
