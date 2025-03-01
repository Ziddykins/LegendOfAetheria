<?php
namespace Game\System;

class System {
    public $monsters = [];
    private $weather;

    public function __construct($zone_id) {
        $this->zone_id = $zone_id;
    }

    public function load_sheet() {
        global $log;

        $handle = fopen(PATH_WEBROOT . '/monsters.raw', 'r');
        
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                array_push($this->monsters, $line);
            }
        }
    }
}
