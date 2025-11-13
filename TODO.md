+
# To Do List

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

* [ ] **Spinels**
  * [ ] implement
  * [ ] tooltips

* [ ] Eastereggs
  * [ ] Egg icon randomly selects a div, on a random page which all registered
        players have access to, and blends in, hourly - grants random amount of
        xp/gold
  * [ ] maybe once per day a golden egg grants boosts or equip

* [x] Livechat - ~~PHP/MySQL/jQuery/AJAX~~ ~~websockets~~ PHP/MySQL/jQuery




# AutoInstaller

  - [x] Update hosts file
  - [x] Script closes before composer/perms, fix
  - [x] creation of ssl vhost doesn't happen

# Select Character (select.php)

  - [ ] Fix avatar display not showing preview image

# Mail

  - [ ] Need to redo how compose.php works; no emails, char only
  
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


