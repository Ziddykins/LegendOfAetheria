# To Do List

## User Flow Analysis - Full Path Simulation

### Path: New User Registration → Character Selection → Game Entry

#### 1. index.php - Registration
**Flow**: User fills registration form → POST to index.php
- ✅ Creates Account via `Account::new()`
- ✅ Creates Character via `Character::new()`
- ✅ Sets character stats (str/int/def distribution)
- ✅ Applies racial bonuses via `$race->set_stat_adjust()`
- ⚠️ **ISSUE**: `$character->stats->set_status(Status::HEALTHY)` called but Stats class has no `$status` property (belongs to Character class, not Stats)
- ⚠️ **ISSUE**: Email verification commented out - all new accounts remain UNVERIFIED
- ⚠️ **ISSUE**: Stats modifications after `new()` may not persist - need to verify PropSync auto-saves on set operations
- ✅ Redirects to `/?register_success`

#### 2. index.php - Login  
**Flow**: User enters credentials → POST to index.php
- ✅ Rate limiting: 5 attempts per IP per 15 minutes
- ✅ Password verification with bcrypt
- ✅ Sets session vars: `logged-in`, `email`, `account-id`, `selected-slot`, `ip`, `last_activity`
- ✅ Updates Account: `sessionID`, `lastLogin`
- ✅ Redirects to `/select`

#### 3. select.php - Character Selection
**Flow**: Displays 3 character slots, user selects existing or creates new
- ✅ Loads Account from session email
- ✅ Renders 3 CharacterSelectCard components (one per slot)
- **Actions**:
  - **load**: Sets `focused-slot` and `character-id` in session → redirects to `/game?page=sheet&sub=character`
  - **new**: Creates new character in specified slot
    - ⚠️ **ISSUE**: Calls `getNextTableID()` then `Character::new($slot)` - potential race condition
    - ⚠️ **ISSUE**: Stats set after creation without explicit save
  - **delete**: Deletes character and clears slot
- ⚠️ **TODO CONFIRMED**: Avatar preview not displaying (noted in TODO.md)

#### 4. game.php - Main Game Interface
**Flow**: Loads character and displays game content
- ✅ Loads Account and Character from session
- ✅ Checks if account is UNVERIFIED → shows verification notice and exits
- ✅ Sets `lastAction` timestamp
- ✅ Loads sidebar based on user settings
- ✅ Routes to page based on GET params: `?page=X&sub=Y` → `pages/Y/X.php`
- ✅ Default fallback: `pages/character/sheet.php`
- ✅ Includes chat interface

### Critical Issues Found:

1. ✅ **Stats persistence CONFIRMED AUTO-SAVE**: PropSync's `handle_set()` for CSTATS/MSTATS immediately calls `propDump()` and executes UPDATE query. Each `set_X()` call triggers DB write. This works but is inefficient (N queries for N stat changes).

2. ❌ **BUG CONFIRMED - Status property mismatch**: 
   - `index.php` line ~175: `$character->stats->set_status(Status::HEALTHY)` 
   - Stats class has NO `$status` property (it's in Character, not Stats)
   - This will call PropSync with invalid property, likely fail silently or error
   - **FIX NEEDED**: Remove this line or move to `$character->set_status()`

3. ⚠️ **Race condition in character creation**: 
   - `select.php` line ~81: `getNextTableID()` → assign to account slot → `Character::new()`
   - Gap between ID reservation and creation allows duplicate IDs
   - **MITIGATION**: `Character::new()` calls `getNextTableId()` again internally, so the external call is redundant
   - **FIX NEEDED**: Remove external `getNextTableID()` call in select.php line ~81

4. ❌ **BUG CONFIRMED - Email verification lockout**: 
   - Registration sets `Privileges::UNVERIFIED`
   - Email sending is commented out (`//send_mail()`)
   - `game.php` blocks UNVERIFIED users with `include 'html/verify.html'; exit();`
   - **RESULT**: New users (except account #1) are locked out immediately after registration
   - **FIX NEEDED**: Either enable email verification OR set default privilege to VERIFIED for non-admin accounts

5. ⚠️ **Character slot assignment logic**:
   - `index.php` registration: `Character::new()` with no slot param → uses `getNextCharSlotID()` (auto-finds first empty)
   - `select.php` new character: `Character::new($slot)` → uses specified slot
   - Both call same function, logic is consistent
   - **ISSUE**: `select.php` pre-sets account slot BEFORE calling `new()`, `new()` might find different slot
   - **Line 93-94 select.php**: Manual UPDATE of account slot, then `new($slot)` which also updates account slot → double update

### PropSync Behavior Analysis:
- **CSTATS/MSTATS**: `set_X()` → immediate propDump() + UPDATE (auto-save) ✅
- **INVENTORY**: `set_X()` → immediate propDump() + UPDATE (auto-save) ✅  
- **SETTINGS**: `set_X()` → immediate propDump() + UPDATE (auto-save) ✅
- **CHARACTER/ACCOUNT**: `set_X()` → falls through to standard UPDATE with type conversion
- **Performance concern**: Each stat change = 1 DB query. Setting 10 stats = 10 queries.

### Bugs to Fix:
1. ❌ `index.php` line 164: `$character->stats->set_status(Status::HEALTHY)` - **CRITICAL BUG**
   - Stats class has NO `$status` property
   - `$status` is a Character property (line 132 of Character.php)
   - **Correct call should be**: `$character->set_status(Status::HEALTHY)`
   - However, Status already defaults to `Status::HEALTHY` in Character class, so this line is redundant
   - **RECOMMENDATION**: Remove the line entirely

2. ✅ `select.php` character creation flow is CORRECT:
   - Line 81: `getNextTableID()` - reads current AUTO_INCREMENT (doesn't consume it)
   - Line 93: Reserves that ID in account's `char_slot` column
   - Line 96: `Character::new($slot)` - INSERT consumes the AUTO_INCREMENT, should match reserved ID
   - **Slot reservation is necessary** to maintain char_slot → character_id mapping
   - ⚠️ **Minor race condition**: If another table gets an INSERT between line 81 and 96, IDs could mismatch (very unlikely in practice)
   
3. ⚠️ `select.php` slot parameter is INTENTIONAL - allows creating characters in non-sequential slots (e.g., slot 1 and slot 3, skipping slot 2)
4. ❌ `index.php` or `game.php`: Fix UNVERIFIED lockout issue - **BLOCKS ALL NEW USERS**

### Verification Lockout Analysis:
- **Current flow**: Register → set UNVERIFIED → email commented out → login → game.php → BLOCKED
- **Account #1 exception**: First account gets ADMINISTRATOR, bypasses lockout
- **All other users**: Cannot access game after registration
- **Possible fixes**:
  1. Enable `send_mail()` in index.php and implement actual email verification
  2. Set default privilege to VERIFIED for all users (security risk)
  3. Auto-verify on first login
  4. Remove UNVERIFIED check from game.php (security risk)
- **RECOMMENDED**: Implement proper email verification flow

### Performance Improvements Needed:
- Batch stat updates: Add `Stats::updateAll()` method to serialize once and save all changes in single query

---

## System

- [x] Move Abuse under the System namespace

## Combat

- [ ] Add functions to add gems to sockets
- [ ] Figure out a way to keep track of the gem ID's, don't really want a separate table, nor do I wanna iterate through all players' inventories.
- [ ] Make flee (most important) work along with steal, spell, etc
- [ ] why only enemies attack?
- [ ] battle-queue; hoards or something, many enemies, multiple drops;
# Ideas

* [x] **Mail system (in-game)**
  * [x] Implement class for users' mailboxes

* [ ] **Friends list**
  * [ ] Have the cancel request link remove the sql entry
  * [ ] repopulate tab on any posts (ajax)
  * [ ] also make decline, block, and message buttons work

~~* **Status bar**~~
  ~~* no sticky top, so it fits inside game content area~~
  ~~* redo the bar so it pulls real values~~

* [x] **Cronjobs**
  * [X] Cycle weather hourly

* [ ] **Spindels**
  * [ ] implement
  * [ ] tooltips

* [ ] Eastereggs
  * [ ] Egg icon randomly selects a div, on a random page which all registered
        players have access to, and blends in, hourly - grants random amount of
        xp/gold
  * [ ] maybe once per day a golden egg grants boosts or equip

* [x] Livechat - ~~PHP/MySQL/jQuery/AJAX~~ ~~websockets~~ PHP/MySQL/~~jQuery~~




# AutoInstaller

  - [x] Update hosts file
  - [x] Script closes before composer/perms, fix
  - [x] creation of ssl vhost doesn't happen

# Select Character (select.php)

  - [ ] Fix avatar display not showing preview image

# Mail

  - [ ] Need to redo how compose.php works; no emails, char only
  - [ ] Search bar to populate dropdown with predictive text (pull from mutal list and populate datalist)
  
# meh
  - [x] fix avatar selection, shouldn't see or be able to specify unknown (add to check)

### Installer fixes:

- [x] Change INSTALL paths, include "install" i.e. install/scripts
- [x]  ubuntu php, need sed on apt sources.d ->
       sed -i 's/Components: main/Components: main\nTrusted: yes/' /etc/apt/sources.list.d/ondrej-ubuntu-php-plucky.sources
- [x]  script needs to be re-ran in new dir after moving, running in memory after move isn't great
- [x]  re-copy config file default to new location as script will have populated the fqdn lol
- [x]  ssl apache vhost still not created >:|
- [x]  #REM and #SSLREM not removed in template processing


