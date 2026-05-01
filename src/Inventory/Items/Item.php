<?php
namespace Game\Inventory\Items;
use Game\Inventory\Enums\ObjectRarity;
use Game\AI\Enums\HttpMethod;


/**
 * Represents an inventory item with weight, gem sockets, and stat modifiers.
 * Items can have multiple sockets for gems and provide various stat modifications.
 * The class fetches item details from a remote service based on item type and ID.
 */
class Item {
	/** @var int The ID of the item, relating to the SQL entry */
	private int $id;

	/** @var string $name Display name of the item */
	private string $name = "None";

	/** @var string $image The items image path, defaults to an unknown placeholder */
	private string $image = "img/items/potions/unknown.png";

	/** @var string $img_thumb The items image thumbnail path, defaults to an unknown placeholder */
	private string $imgThumb = "img/items/potions/thumbs/unknown.png";
    
    /** @var int $weight Weight value (contributes to encumbrance) */
	private int $weight = 0;

	/** @var int $itemId Item ID to fetch the schema for that item */
	private int $itemId = 0;
    	
	/** @var string $type Item type to fetch the schema for that item */
	private string $type = "";
	
	/** @var string $subtype Item subtype to fetch the schema for that item */
	private string $subtype = "";


	/** @var int $maxSockets Number of gem sockets */
	private int $maxSockets = 0;

	/** @var string|null $rarity Item rarity */
	private ObjectRarity $rarity = ObjectRarity::NONE;

	/** @var int|null $expireTick Optional expiration tick */
	private ?int $expireTick = null;

	/** @var array $implicit Implicit stat modifiers */
	private array $implicit = [];

	/** @var array $affixPool Available affix pool for this item */
	private array $affixPool = [];

	/** @var string $description Item description */
	private string $description = "";

    /** @var array<Socket> Array of Socket objects for gem insertion */
    private array $sockets = [];
    
    /** @var array $modifiers Stat modifiers provided by this item */
	private array $modifiers = [];

	/** @var bool $stackable Whether or not the item is stackable */
	private bool $stackable = false;


    /**
     * Creates a new item instance and hydrates it from the remote item schema.
     * If no type/itemId are supplied, an empty placeholder item is created.
     * 
     * @param string $type Item type to fetch from item service
     * @param int $itemId Item ID to fetch from item service
     */
	public function __construct(string $type = "", int $itemId = 0) {
		global $log;
		$this->type = $type;
		$this->itemId = $itemId;

		$log->warning("it $type iid $itemId");

		if ($this->type !== "" && $this->itemId > 0) {
			$item = $this->get_item_details();
			if (is_object($item)) {
				$this->name = $item->name ?? $this->name;
				$this->image = $item->image ?? $this->image;
				$this->imgThumb = preg_replace('/(.*)\/(.*?).png$/', '$1/thumbs/$2.png', $this->image);
				$this->weight = $item->weight ?? $this->weight;
				$this->itemId = $item->itemId ?? $this->itemId;
				$this->type = $item->type ?? $this->type;
				$this->subtype = $item->subtype ?? $this->subtype;
				$this->maxSockets = $item->maxSockets ?? $this->maxSockets;
				$this->rarity = ObjectRarity::name_to_enum($item->rarity) ?? $this->rarity;
				$this->expireTick = $item->expireTick ?? $this->expireTick;
				$this->implicit = $item->implicit ?? $this->implicit;
				$this->affixPool = $item->affixPool ?? $this->affixPool;
				$this->description = $item->description ?? $this->description;
			}
		}

/*		for ($i=0; $i<$socketCount; $i++) {
            $this->sockets[$i] = new Socket($i);
		}*/
	}

	private function get_item_details() {
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, "http://localhost:3000/item/{$this->type}/{$this->itemId}");
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json',
			'Accept: application/json'
		]);

		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);

		unset($ch);

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


	public function __call($method, $params): mixed {
		$var = lcfirst(substr($method, 4));

		if (strncasecmp($method, "get_", 4) === 0) {
			return $this->$var;
		}

		if (strncasecmp($method, "set_", 4) === 0) {
			$this->$var = $params[0];
		}
	}
}
