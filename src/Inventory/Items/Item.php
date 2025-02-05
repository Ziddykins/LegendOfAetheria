<?php
namespace Game\Inventory\Items;
class Item {
    private string $name;
    private int $weight;
    /* Array of Class ItemSockets */
    private array $sockets;
    private array $modifiers;

    public function __construct($name = "None", $weight = 0, $socketCount = 1) {
        $this->name   = $name;
        $this->weight = $weight;
        
        for ($i=0; $i<$socketCount; $i++) {
            $this->sockets[$i] = new Socket($i);
        }
    }
}
