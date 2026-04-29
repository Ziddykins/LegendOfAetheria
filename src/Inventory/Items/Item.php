<?php
namespace Game\Inventory\Items;

use Game\AI\Enums\HttpMethod;


/**
 * Represents an inventory item with weight, gem sockets, and stat modifiers.
 * Items can have multiple sockets for gems and provide various stat modifications.
 */
class Item {
	/** @var int The ID of the item, relating to the SQL entry
	private int $id;

	/** @var string $name Display name of the item */
	private string $name = "None";

	/** @var string $image The items image path, defaultsnto an unknown placeholder */	
	private string $image = "items/unknown.png";
    
    /** @var int $weight Weight value (contributes to encumbrance) */
	private int $weight = 0;

	/** @var int $itemId Item ID to fetch the schema for that item
	private int $itemId = 0;
    
    /** @var array<Socket> Array of Socket objects for gem insertion */
    private array $sockets = [];
    
    /** @var array $modifiers Stat modifiers provided by this item */
    private array $modifiers = [];

    /**
     * Creates a new item with specified properties.
     * Initializes empty Socket objects based on socket count.
     * 
     * @param string $name Item display name
     * @param int $weight Item weight
     * @param int $socketCount Number of gem sockets (default 1)
     */
    public function __construct(string $itemType, int $itemId) {
		$item = $this->get_item_details($itemType, $itemId);
		
		$this->name = $item->name;
		

/*		for ($i=0; $i<$socketCount; $i++) {
            $this->sockets[$i] = new Socket($i);
		}*/
	}

	private function get_item_details() {
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, "http://localhost:3000/item/$itemType/$itemId");
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json',
			'Accept: application/json'
		]);

		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);

		curl_close($ch);

		return json_decode($response);
	}

	private function mod_pool($rarity) {
		$RARITY_CONFIG = [
			"WORTHLESS" => ["weight" => 50.0, "mult" => 0.3, "affixes" => [0, 1]],
			"TARNISHED" => ["weight" => 30.0, "mult" => 0.5, "affixes" => [0, 2]],
			"COMMON"    => ["weight" => 20.0, "mult" => 1.0, "affixes" => [1, 2]],
			"ENCHANTED" => ["weight" => 12.0, "mult" => 1.3, "affixes" => [2, 3]],
			"MAGICAL"   => ["weight" => 8.0,  "mult" => 1.6, "affixes" => [2, 4]],
			"LEGENDARY" => ["weight" => 5.0,  "mult" => 2.2, "affixes" => [3, 5]],
			"EPIC"      => ["weight" => 2.5,  "mult" => 2.8, "affixes" => [4, 6]],
			"MYSTIC"    => ["weight" => 1.5,  "mult" => 3.5, "affixes" => [5, 7]],
			"HEROIC"    => ["weight" => 0.75, "mult" => 4.5, "affixes" => [6, 8]],
			"INFAMOUS"  => ["weight" => 0.24, "mult" => 6.0, "affixes" => [7, 9]],
			"GODLY"     => ["weight" => 0.01, "mult" => 10.0,"affixes" => [8, 10]]
		];
		
		$STAT_POOLS = [
			"weapon" => ["str", "crit", "accu", "sped"],
			"armor"  => ["def", "maxHP", "rsst", "mdef"],
			"boots"  => ["sped", "dodg", "dext"],
			"helmet" => ["def", "int", "maxMP"],
			"gloves" => ["str", "dext", "accu"],
			"shield" => ["def", "blck", "rsst"],
			"potion" => ["hp", "mp", "ep"],
			"misc"   => ["luck", "chsm", "rgen"]
		];	
	}
}
