# Familiar System

<details>
<summary>Relevant source files</summary>

The following files were used as context for generating this wiki page:

- [html/card-egg-none.html](html/card-egg-none.html)
- [src/Account/Account.php](src/Account/Account.php)
- [src/Character/Character.php](src/Character/Character.php)
- [src/Character/Stats.php](src/Character/Stats.php)
- [src/Familiar/Familiar.php](src/Familiar/Familiar.php)
- [src/Monster/Stats.php](src/Monster/Stats.php)

</details>



The Familiar System provides companion creatures that assist characters in combat and progression. This document covers familiar acquisition through eggs, hatching mechanics, rarity tiers, stat management, and database persistence. For character-level progression mechanics, see [Character Management](#5.1). For combat mechanics involving familiars, see [Combat System](#5.2).

## Overview

Familiars are companion pets that characters acquire as eggs, which hatch after a time period. Each familiar has its own stats, level progression, and rarity tier. The system is implemented through the `Familiar` class ([src/Familiar/Familiar.php]()) and integrates with the Character and Account systems.

**Sources:** [src/Familiar/Familiar.php:1-311](), [src/Character/Character.php:137-147](), [src/Account/Account.php:131-135]()

## System Architecture

```mermaid
graph TB
    Character["Character<br/>(Character.php)"]
    Account["Account<br/>(Account.php)"]
    Familiar["Familiar<br/>(Familiar.php)"]
    FamiliarStats["Familiar Stats<br/>(Familiar/Stats.php)"]
    PropSuite["PropSuite Trait"]
    FamiliarsTable[("familiars table<br/>(database)")]
    ObjectRarity["ObjectRarity Enum"]
    
    Character -->|"owns 0..1"| Familiar
    Account -->|"tracks eggsOwned<br/>eggsOwned"| Familiar
    Familiar -->|"has"| FamiliarStats
    Familiar -.->|"uses"| PropSuite
    FamiliarStats -.->|"uses"| PropSuite
    Familiar -->|"persists to"| FamiliarsTable
    Familiar -->|"uses"| ObjectRarity
    PropSuite -->|"syncs with"| FamiliarsTable
```

**Sources:** [src/Familiar/Familiar.php:1-40](), [src/Character/Character.php:137-147](), [src/Account/Account.php:131-135]()

## Familiar Class Structure

The `Familiar` class ([src/Familiar/Familiar.php:39-311]()) manages all aspects of familiar entities. It uses the `PropSuite` trait for database synchronization and provides dynamic get/set methods through `__call()`.

### Core Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `id` | `?int` | `null` | Unique familiar identifier |
| `characterID` | `?int` | `null` | Owner character ID |
| `level` | `int` | `1` | Current familiar level |
| `experience` | `int` | `0` | Current experience points |
| `nextLevel` | `int` | `100` | XP required for next level |
| `name` | `?string` | `null` | Display name of familiar |
| `avatar` | `mixed` | `null` | Avatar image file path |
| `stats` | `?Stats` | `null` | Combat statistics object |

**Sources:** [src/Familiar/Familiar.php:42-64]()

### Dynamic Property Access

The `Familiar` class implements a custom `__call()` method ([src/Familiar/Familiar.php:284-310]()) that provides dynamic get/set accessors:

```mermaid
flowchart LR
    MethodCall["Method Call<br/>e.g., get_level()"]
    CheckPrefix{"Prefix?"}
    GetOperation["Return property value<br/>return this->var"]
    SetOperation["UPDATE familiars SET<br/>Sync to database"]
    
    MethodCall --> CheckPrefix
    CheckPrefix -->|"get_"| GetOperation
    CheckPrefix -->|"set_"| SetOperation
```

**Example methods:**
- `get_level()` - Returns familiar level
- `set_name(string $name)` - Sets familiar name and updates database
- `get_characterID()` - Returns owning character ID

**Sources:** [src/Familiar/Familiar.php:284-310]()

## Egg Generation System

### Rarity Tiers

Familiar eggs are generated with rarity levels determined by dice rolls. The `ObjectRarity` enum defines 11 rarity tiers:

| Rarity | Color Code | Description |
|--------|------------|-------------|
| WORTHLESS | `#FACEF0` | Lowest tier |
| TARNISHED | `#779988` | Poor quality |
| COMMON | `#ADD8D7` | Standard drop |
| ENCHANTED | `#A6D9F8` | Above average |
| MAGICAL | `#08E71C` | Rare |
| LEGENDARY | `#F8C81C` | Very rare |
| EPIC | `#CAB51F` | Exceptional |
| MYSTIC | `#01CBF6` | Mystical quality |
| HEROIC | `#1C4F2C` | Heroic tier |
| INFAMOUS | `#CB20EE` | Dark/infamous |
| GODLY | `#FF2501` | Highest tier |

**Sources:** [src/Familiar/Familiar.php:210-253]()

### Generation Process

```mermaid
sequenceDiagram
    participant Player
    participant UI as "card-egg-none.html"
    participant Controller as "game.php?page=eggs"
    participant Familiar as "Familiar->generateEgg()"
    participant Database as "familiars table"
    
    Player->>UI: Click "Collect Egg"
    UI->>Controller: POST action=generate
    Controller->>Familiar: generateEgg(familiar, rarity_roll)
    Familiar->>Familiar: ObjectRarity::getObjectRarity(roll)
    Familiar->>Familiar: getRarityColor(rarity)
    Familiar->>Familiar: set_level(1)
    Familiar->>Familiar: set_dateAcquired(now)
    Familiar->>Familiar: set_hatchTime(now + 8 hours)
    Familiar->>Database: saveFamiliar()
    Database-->>Player: Egg created
```

The `generateEgg()` method ([src/Familiar/Familiar.php:263-282]()) performs the following operations:

1. Determines rarity from dice roll using `ObjectRarity::getObjectRarity()`
2. Maps rarity to color code via `getRarityColor()`
3. Sets initial level to 1
4. Records acquisition timestamp
5. Sets hatch time to 8 hours in the future
6. Initializes egg counters
7. Persists to database via `saveFamiliar()`

**Sources:** [src/Familiar/Familiar.php:263-282](), [html/card-egg-none.html:30-48]()

## Database Persistence

### Familiar Registration

When a character first accesses the familiar system, a familiar entry is registered via `registerFamiliar()` ([src/Familiar/Familiar.php:82-100]()):

```mermaid
flowchart TD
    Start["registerFamiliar()"]
    Insert["INSERT INTO familiars<br/>(character_id)"]
    Retrieve["SELECT id WHERE<br/>character_id = ?"]
    SetDefaults["Set defaults:<br/>name='!Unset!'<br/>level=1<br/>avatar='egg-unhatched.jpeg'"]
    Save["saveFamiliar()"]
    
    Start --> Insert
    Insert --> Retrieve
    Retrieve --> SetDefaults
    SetDefaults --> Save
```

**Sources:** [src/Familiar/Familiar.php:82-100]()

### Data Synchronization

The `saveFamiliar()` method ([src/Familiar/Familiar.php:108-137]()) persists all familiar properties to the database:

1. Iterates through all object properties
2. Converts camelCase property names to snake_case columns via `clsprop_to_tblcol()`
3. Builds UPDATE query with property values
4. Executes parameterized query with familiar ID

The `loadFamiliar()` method ([src/Familiar/Familiar.php:177-201]()) performs the reverse operation:

1. Queries `familiars` table by `character_id`
2. If no record exists, calls `registerFamiliar()`
3. Maps snake_case columns to camelCase properties
4. Populates object state

**Sources:** [src/Familiar/Familiar.php:108-137](), [src/Familiar/Familiar.php:177-201]()

## Familiar Stats System

Familiars have their own stats system separate from character stats. While the implementation details are partially commented out in the codebase ([src/Familiar/Familiar.php:313-371]()), the architecture shows that familiars use a `Stats` object ([src/Familiar/Familiar.php:63-64]()).

### Expected Stats Structure

Based on commented code and system architecture:

| Stat Category | Attributes |
|---------------|------------|
| **Resources** | health, maxHealth, mana, maxMana, energy, maxEnergy |
| **Combat** | intelligence, strength, defense |
| **Progression** | level, experience, nextLevel |
| **Tracking** | eggsOwned, eggsSeen |

**Sources:** [src/Familiar/Familiar.php:313-371](), [src/Familiar/Familiar.php:63-64]()

## Integration with Account System

The `Account` class tracks familiar-related metrics:

| Property | Type | Purpose |
|----------|------|---------|
| `eggsOwned` | `int` | Total eggs acquired by account |
| `eggsSeen` | `int` | Unique egg types encountered |

These properties support account-wide familiar collection tracking across all character slots.

**Sources:** [src/Account/Account.php:131-135]()

## UI Components

### No Egg/Familiar Card

The `card-egg-none.html` template ([html/card-egg-none.html:1-60]()) displays when a character has no familiar:

```mermaid
graph LR
    Avatar["Egg Avatar Image<br/>img-thumbnail"]
    Title["'No Egg/Familiar!'<br/>Warning Badge"]
    Description["Tier 4 Starter Info"]
    Form["Generate Egg Form"]
    NameInput["Egg Name Input<br/>id='egg-name'"]
    Submit["Collect Egg Button<br/>id='generate-egg'"]
    
    Avatar --> Card
    Title --> Card
    Description --> Card
    Form --> Card
    NameInput --> Form
    Submit --> Form
```

Key form elements:
- **Action:** `?page=eggs`
- **Method:** `POST`
- **Hidden Field:** `action=generate`
- **Name Input:** Optional egg naming field (`id="egg-name"`)
- **Submit Button:** `id="generate-egg"`, `value="1"`

**Sources:** [html/card-egg-none.html:1-60]()

### Current Egg/Familiar Card

The `getCard()` method ([src/Familiar/Familiar.php:146-167]()) generates HTML for displaying familiars. The implementation includes:

- **Empty card** (`$which === 'empty'`): Loads `card-egg-none.html`
- **Current card** (`$which === 'current'`): Intended to show egg timer and status (currently commented out)

**Sources:** [src/Familiar/Familiar.php:146-167]()

## Hatching Mechanics

### Time-Based Hatching

Eggs are configured to hatch 8 hours after acquisition ([src/Familiar/Familiar.php:276]()):

```php
$familiar->set_hatchTime(get_mysql_datetime('+8 hours'));
```

The system records:
- `dateAcquired`: Timestamp when egg was obtained
- `hatchTime`: Future timestamp when egg becomes available

### Expected Hatch Flow

```mermaid
stateDiagram-v2
    [*] --> Generated: generateEgg()
    Generated --> Incubating: dateAcquired set
    Incubating --> Ready: Current time >= hatchTime
    Ready --> Hatched: Player action
    Hatched --> [*]
    
    note right of Incubating
        8 hour wait period
        hatchTime = dateAcquired + 8h
    end note
```

**Sources:** [src/Familiar/Familiar.php:263-282]()

## PropSuite Integration

The `Familiar` class uses the `PropSuite` trait ([src/Familiar/Familiar.php:40]()) for database operations. This provides:

- Automatic property-to-column mapping via `clsprop_to_tblcol()`
- Dynamic get/set methods through `__call()`
- Database synchronization on property changes

The custom `__call()` implementation ([src/Familiar/Familiar.php:284-310]()) extends PropSuite with familiar-specific logic:

```mermaid
flowchart TD
    Call["__call(method, params)"]
    ParseMethod["Extract property name<br/>var = lcfirst(substr(method, 4))"]
    CheckGet{"get_ prefix?"}
    ReturnValue["return this->var"]
    CheckSet{"set_ prefix?"}
    BuildSQL["Build UPDATE query<br/>Convert to snake_case"]
    ExecuteSQL["Execute query<br/>Update database"]
    UpdateProp["Update object property<br/>this->var = params[0]"]
    
    Call --> ParseMethod
    ParseMethod --> CheckGet
    CheckGet -->|Yes| ReturnValue
    CheckGet -->|No| CheckSet
    CheckSet -->|Yes| BuildSQL
    BuildSQL --> ExecuteSQL
    ExecuteSQL --> UpdateProp
```

**Sources:** [src/Familiar/Familiar.php:284-310](), [src/Traits/PropSuite/PropSuite.php]()

## Database Schema Reference

The `familiars` table stores familiar state. Based on the code, expected columns include:

| Column | Type | Description |
|--------|------|-------------|
| `id` | INT | Primary key |
| `character_id` | INT | Foreign key to characters |
| `level` | INT | Current level |
| `experience` | INT | Current XP |
| `next_level` | INT | XP threshold |
| `name` | VARCHAR | Familiar name |
| `avatar` | VARCHAR | Avatar path |
| `rarity` | ENUM | ObjectRarity value |
| `rarity_color` | VARCHAR | Hex color code |
| `last_roll` | FLOAT | Rarity dice roll |
| `date_acquired` | DATETIME | Acquisition timestamp |
| `hatch_time` | DATETIME | Hatch timestamp |
| `hatched` | BOOLEAN | Hatch status |
| `eggs_owned` | INT | Counter |
| `eggs_seen` | INT | Counter |

**Sources:** [src/Familiar/Familiar.php:42-64](), [src/Familiar/Familiar.php:263-282]()

## Key Methods Reference

### Constructor

**Signature:** `__construct(int $characterID, string $table)`

Initializes familiar instance with character ID. The `$table` parameter appears to be legacy.

**Sources:** [src/Familiar/Familiar.php:72-74]()

### registerFamiliar()

**Signature:** `registerFamiliar(): void`

Creates new familiar database entry with default values:
- Name: `'!Unset!'`
- Level: `1`
- Avatar: `'img/generated/eggs/egg-unhatched.jpeg'`

**Sources:** [src/Familiar/Familiar.php:82-100]()

### generateEgg()

**Signature:** `generateEgg(Familiar $familiar, float $rarity_roll): void`

Configures a familiar as a new egg with rarity-based properties and 8-hour hatch timer.

**Sources:** [src/Familiar/Familiar.php:263-282]()

### getRarityColor()

**Signature:** `getRarityColor(ObjectRarity $rarity): string`

Maps ObjectRarity enum values to hex color codes for UI display.

**Returns:** Hex color string (e.g., `"#FF2501"` for GODLY)

**Sources:** [src/Familiar/Familiar.php:210-253]()

### saveFamiliar()

**Signature:** `saveFamiliar(): void`

Persists all familiar properties to database via UPDATE query. Converts camelCase properties to snake_case columns.

**Sources:** [src/Familiar/Familiar.php:108-137]()

### loadFamiliar()

**Signature:** `loadFamiliar(int $characterID): void`

Loads familiar from database by character ID. Auto-registers if no familiar exists.

**Sources:** [src/Familiar/Familiar.php:177-201]()