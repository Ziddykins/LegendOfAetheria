# Map System Classes

PSR-4 compliant PHP classes for the game map/zone/location structure.

## Structure

```
Game\Map\
├── GameMap         - Top-level map container
├── Zone            - Game zones with level ranges and alignment
├── Location        - Specific locations within zones
├── NPC             - Non-player characters
├── Rumors          - NPC rumor system
└── Metadata        - Map metadata (first_run, created timestamp)
```

## Installation

After creating these classes, update composer autoload:

```bash
composer dump-autoload
```

## Usage

### Load from JSON

```php
use Game\Map\GameMap;

$json = file_get_contents('map-data.json');
$map = GameMap::fromJson($json);

// Access zones
foreach ($map->zones as $zone) {
    echo "Zone: {$zone->name} (Level {$zone->maxLevel})\n";
    
    // Access locations
    foreach ($zone->locations as $location) {
        echo "  Location: {$location->name}\n";
        
        // Access NPCs
        foreach ($location->npcs as $npc) {
            echo "    NPC: {$npc->name} at {$npc->location}\n";
        }
    }
}
```

### Create Programmatically

```php
use Game\Map\{GameMap, Zone, Location, NPC, Rumors};

$map = new GameMap();

// Create NPC with rumors
$rumors = new Rumors('active', ['Rumor 1'], ['Rumor 2']);
$npc = new NPC('Frank', 'shrine', 'shrine', false, $rumors);

// Create location with NPCs
$location = new Location('shrine', [0, 0], 10, false, false, [$npc]);

// Create zone with locations
$zone = new Zone('zone1', [0, 0], [100, 100], 15, 'neutral', [$location]);

// Add zone to map
$map->addZone($zone);

// Export to JSON
echo $map->toJson();
```

### Find Specific Zone

```php
$zone = $map->getZone('zone1');
if ($zone) {
    echo "Found zone: {$zone->name}\n";
}
```

## Class Properties

### GameMap
- `zones`: Zone[] - Array of zones
- `metadata`: Metadata - Map metadata

### Zone
- `name`: string - Zone identifier
- `zcoords`: array - Zone coordinates [x, y]
- `limits`: array - Zone size limits [width, height]
- `maxLevel`: int - Maximum character level for zone
- `alignment`: string - Zone alignment (neutral/good/evil)
- `locations`: Location[] - Locations within zone

### Location
- `name`: string - Location identifier
- `lcoords`: array - Location coordinates [x, y]
- `radius`: int - Location radius
- `pvp`: bool - PVP enabled
- `pve`: bool - PVE enabled
- `npcs`: NPC[] - NPCs at this location

### NPC
- `name`: string - NPC name
- `location`: string - Current location
- `hometown`: string - Home location
- `travels`: bool - Whether NPC travels
- `rumors`: Rumors - Rumor system data

### Rumors
- `status`: string - Rumor system status (active/inactive)
- `rumors_heard`: array - Rumors this NPC has heard
- `rumors_started`: array - Rumors this NPC has started

### Metadata
- `first_run`: bool - Whether this is the first run
- `created`: int - Unix timestamp of creation

## JSON Serialization

All classes implement `JsonSerializable` so you can use:

```php
json_encode($map);        // Entire map
json_encode($zone);       // Single zone
json_encode($location);   // Single location
json_encode($npc);        // Single NPC
```

Or use the provided methods:

```php
$map->toJson();      // Formatted JSON string
$map->toArray();     // PHP array
```
