<?php
use Game\Account\Enums\Privileges;
use Game\Account\Account;
use Game\Character\Character;
use Game\Mail\Folder\Enums\FolderType;
use Game\Mail\MailBox\MailBox;

$account   = new Account($_SESSION['email']); 
$character = new Character($account->get_id(), $_SESSION['character-id']); 
$character->load();
$folders = [];

foreach (["OUTBOX", "INBOX", "DELETED", "DRAFTS"] as $type) {
    $folder = FolderType::name_to_enum($type);
    $folders[$type] = MailBox::getFolderCount(
        FolderType::name_to_enum($type),
        $character->get_id()
    );
}

$char_menu_icon = $character->stats->get_hp() > 0 ? 'sentiment_satisfied' : 'skull';

?>

<aside id="sidebar" name="sidebar" class="app-sidebar shadow overflow-hidden" data-bs-theme="<?php echo $color_mode; ?>">
    <div class="sidebar-brand mb-3">
    <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
            <i class="bi bi-list"></i>
        </a>
    
        <a href="./index.php" class="brand-link">
            <img src="/img/logos/logo-banner-no-bg.webp" alt="Legend of Aetheria Logo" class="brand-image img-fluid">
        </a>
    </div>

    <?php include 'navs/sidemenus/nav-quicknav.php'; ?>

    <div class="sidebar-wrapper">
        <nav>
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <li id="character-anchor" name="character-anchor" class="nav-item menu-open">
                        <a href="#" class="nav-link">
                            <span class="nav-icon material-symbols-sharp"><?php echo $char_menu_icon; ?></span>
                            <p class="align-self-center fw-bold fw-bold">Character</p>
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </a>
                    
                        <ul id="character-list" name="character-list" class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="/game?page=sheet" class="nav-link align-items-center ms-2">
                                    <span class="nav-icon material-symbols-sharp">mist</span>
                                    <p class="align-self-center text-warning opacity-50">Sheet</p>
                                </a>
                            </li>
                    
                            <li id="inventory-anchor" name="inventory-anchor" class="nav-item">
                                <a href="#" class="nav-link align-items-center ms-2">
                                    <span class="nav-icon material-symbols-sharp">inventory_2</span>
                                    <p class="align-self-center text-warning opacity-50 fw-bold">Inventory</p>
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </a>
                            
                                <ul id="inventory-list" name="inventory-list" class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="/game?page=Equipment" class="nav-link align-items-center ms-4">
                                            <span class="nav-icon material-symbols-sharp">colorize</span>
                                            <p class="align-self-center text-warning opacity-50">Equipment</p>
                                        </a>
                                    </li>
                                    
                                    <li id="items-anchor" name="items-anchor" class="nav-item">
                                        <a href="#" class="nav-link ms-4">
                                            <span class="nav-icon material-symbols-sharp">handyman</span>
                                            <p class="align-self-center text-warning opacity-50 fw-bold">Items</p>
                                            <i class="nav-arrow bi bi-chevron-right"></i>
                                        </a>

                                        <ul id="items-list" name="items-list" class="nav nav-treeview">
                                            <li class="nav-item">
                                                <a href="/game?page=Questitems" class="nav-link justify-items-center">
                                                    <span class="nav-icon material-symbols-sharp ms-5">deployed_code_alert</span>
                                                    <p class="align-self-center text-warning opacity-50">Quest Items</p>
                                                </a>
                                            </li>

                                            <li class="nav-item">
                                                <a href="/game?page=Consumables" class="nav-link">
                                                    <span class="nav-icon material-symbols-sharp ms-5">grocery</span>
                                                    <p class="align-self-center text-warning opacity-50">Consumables</p>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>

                                <li class="nav-item">
                                    <a href="/game?page=skills" class="nav-link ms-2">
                                        <span class="nav-icon material-symbols-sharp">hotel_class</span>
                                        <p class="align-self-center text-warning opacity-50">Skills</p>
                                    </a>
                                </li>
                            
                                <li class="nav-item">
                                    <a href="/game?page=spells" class="nav-link ms-2">
                                        <span class="nav-icon material-symbols-sharp">book</span>
                                        <p class="align-self-center text-warning opacity-50">Spells</p>
                                    </a>
                                </li>
                            
                                <li class="nav-item">
                                    <a href="/game?page=train" class="nav-link ms-2">
                                        <span class="nav-icon material-symbols-sharp">fitness_center</span>
                                        <p class="align-self-center text-warning opacity-50">Train</p>
                                    </a>
                                </li>
                            </li>
                        </ul>
                    </li>

                    <li id="familiar-anchor" name="familiar-anchor"  class="nav-item">
                        <a href="#" class="nav-link">
                            <span class="nav-icon material-symbols-sharp">sound_detection_dog_barking</span>
                            <p class="align-self-center fw-bold shadow fw-bold">Familiar</p>
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </a>

                        <ul id="familiar-list" name="familiar-list"  class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="/game?page=Manage" class="nav-link">
                                    <span class="nav-icon material-symbols-sharp ms-2">raven</span>
                                    <p class="align-self-center text-warning opacity-50">Manage</p>
                                </a>
                            </li>
                        
                            <li class="nav-item">
                                <a href="/game?page=Hatchery" class="nav-link">
                                    <span class="nav-icon material-symbols-sharp ms-2">egg</span>
                                    <p class="align-self-center text-warning opacity-50">Hatchery</p>
                                </a>
                            </li>
                        
                            <li class="nav-item">
                                <a href="/game?page=Equipment" class="nav-link">
                                    <span class="nav-icon material-symbols-sharp ms-2">pet_supplies</span>
                                    <p class="align-self-center text-warning opacity-50">Equipment</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                
                    <li id="location-anchor" name="location-anchor" class="nav-item">
                        <a href="#" class="nav-link">
                            <span class="nav-icon material-symbols-sharp">public</span>
                            <p class="align-self-center fw-bold shadow fw-bold">Location</p>
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </a>
                    
                        <ul id="location-list" name="location-list"  class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="/game?page=hunt" class="nav-link">
                                    <span class="nav-icon material-symbols-sharp ms-2">cruelty_free</span>
                                    <p class="align-self-center text-warning opacity-50">Hunt</p>
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a href="/game?page=Map" class="nav-link">
                                    <span class="nav-icon material-symbols-sharp ms-2">map</span>
                                    <p class="align-self-center text-warning opacity-50">Map</p>
                                </a>
                            </li>
                        
                            <li class="nav-item">
                                <a href="/game?page=Explore" class="nav-link">
                                    <span class="nav-icon material-symbols-sharp ms-2">forest</span>
                                    <p class="align-self-center text-warning opacity-50">Explore</p>
                                </a>
                            </li>
                        
                            <li class="nav-item">
                                <a href="/game?page=Zone" class="nav-link">
                                    <span class="nav-icon material-symbols-sharp ms-2">rocket</span>
                                    <p class="align-self-center text-warning opacity-50">Zone</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="/game?page=Rest" class="nav-link">
                                    <span class="nav-icon material-symbols-sharp ms-2">offline_bolt</span>
                                    <p class="align-self-center text-warning opacity-50">Rest</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                
                    <li id="economy-anchor" name="economy-anchor"  class="nav-item">
                        <a href="#" class="nav-link align-items-center">
                            <span class="nav-icon material-symbols-sharp">monitoring</span>
                            <p class="align-self-center fw-bold shadow fw-bold">Economy</p>
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </a>
                    
                        <ul id="economy-list" name="economy-list" class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="/game?page=Equipment" class="nav-link">
                                    <span class="nav-icon material-symbols-sharp ms-2">swords</span>
                                    <p class="align-self-center text-warning opacity-50">Equipment</p>
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a href="/game?page=Items" class="nav-link">
                                    <span class="nav-icon material-symbols-sharp ms-2">diamond</span>
                                    <p class="align-self-center text-warning opacity-50">Items</p>
                                </a>
                            </li>
                        
                            <li id="blackmarket-anchor" name="blackmarket-anchor" class="nav-item">
                                <a href="#" class="nav-link">
                                    <span class="nav-icon material-symbols-sharp ms-2">loyalty</span>
                                    <p class="align-self-center text-warning opacity-50 fw-bold">Blackmarket</p>
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </a>
                            
                                <ul id="blackmarket-list" name="blackmarket-list" class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="/game?page=Buy" class="nav-link">
                                            <span class="nav-icon material-symbols-sharp ms-4">attach_money</span>
                                            <p class="align-self-center text-warning opacity-50">Buy</p>
                                        </a>
                                    </li>
                                    
                                    <li class="nav-item">
                                        <a href="/game?page=Sell" class="nav-link">
                                            <span class="nav-icon material-symbols-sharp ms-4">paid</span>
                                            <p class="align-self-center text-warning opacity-50">Sell</p>
                                        </a>
                                    </li>
                                    
                                    <li class="nav-item">
                                        <a href="/game?page=Market" class="nav-link">
                                            <span class="nav-icon material-symbols-sharp ms-4">storefront</span>
                                            <p class="align-self-center text-warning opacity-50">Market</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                                
                            <li id="bank-anchor" name="bank-anchort" class="nav-item">
                                <a href="#" class="nav-link">
                                    <span class="nav-icon material-symbols-sharp ms-2">account_balance</span>
                                    <p class="align-self-center text-warning opacity-50 fw-bold">Bank</p>
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </a>
                                
                                <ul id="bank-list" name="bank-list" class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="/game?page=Loans" class="nav-link">
                                            <span class="nav-icon material-symbols-sharp ms-4">clinical_notes</span>
                                            <p class="align-self-center text-warning opacity-50">Account</p>
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="/game?page=Depost" class="nav-link">
                                            <span class="nav-icon material-symbols-sharp ms-4">attach_money</span>
                                            <p class="align-self-center text-warning opacity-50">Depost</p>
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="/game?page=Withdrawal" class="nav-link">
                                            <span class="nav-icon material-symbols-sharp ms-4">paid</span>
                                            <p class="align-self-center text-warning opacity-50">Withdrawal</p>
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="/game?page=Loans" class="nav-link">
                                            <span class="nav-icon material-symbols-sharp ms-4">payments</span>
                                            <p class="align-self-center text-warning opacity-50">Loans</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    
                    <li id="dungeon-anchor" name="dungeon-anchor"  class="nav-item">
                        <a href="#" class="nav-link">
                            <span class="nav-icon material-symbols-sharp">widgets</span>
                            <p class="align-self-center fw-bold shadow fw-bold">Dungeon</p>
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </a>
                        
                        <ul id="dungeon-list" name="dungeon-list" class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="/game?page=dungeon" class="nav-link">
                                    <span class="nav-icon material-symbols-sharp ms-2">stat_minus_3</span>
                                    <p class="align-self-center text-warning opacity-50">Floor 1</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="/game?page=Settings" class="nav-link">
                                    <span class="nav-icon material-symbols-sharp ms-2">settings</span>
                                    <p class="align-self-center text-warning opacity-50">Settings</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="/game?page=Reset" class="nav-link">
                                    <span class="nav-icon material-symbols-sharp ms-2 text-danger">restart_alt</span>
                                    <p class="align-self-center text-warning opacity-50">Reset</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li id="quests-anchor" name="quests-anchor"  class="nav-item">
                        <a href="#" class="nav-link">
                            <span class="nav-icon material-symbols-sharp">volcano</span>
                            <p class="align-self-center fw-bold shadow fw-bold">Quests</p>
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </a>
                        
                        <ul id="quest-list" name="quest-list" class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="/game?page=Active" class="nav-link">
                                    <span class="nav-icon material-symbols-sharp ms-2">lists</span>
                                    <p class="align-self-center text-warning opacity-50">Active</p>
                                </a>
                            </li>
                    
                            <li class="nav-item">
                                <a href="/game?page=Accepted" class="nav-link">
                                    <span class="nav-icon material-symbols-sharp ms-2">fact_check</span>
                                    <p class="align-self-center text-warning opacity-50">Accepted</p>
                                </a>
                            </li>
                    
                            <li class="nav-item">
                                <a href="/game?page=Completed" class="nav-link">
                                    <span class="nav-icon material-symbols-sharp ms-2">done_all</span>
                                    <p class="align-self-center text-warning opacity-50">Completed</p>
                                </a>
                            </li>
                    
                            <li class="nav-item">
                                <a href="/game?page=Abandoned" class="nav-link">
                                <span class="nav-icon material-symbols-sharp ms-2">backspace</span>
                                <p class="align-self-center text-warning opacity-50">Abandoned</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li id="mail-anchor" name="mail-anchor"  class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="nav-icon material-symbols-sharp">alternate_email</span>
                        <p class="align-self-center fw-bold shadow fw-bold">Mail</p>
                        <i class="nav-arrow bi bi-chevron-right"></i>
                    </a>
                    
                    <ul id="mail-list" name="mail-list" class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/game?page=compose" class="nav-link">
                                <span class="nav-icon material-symbols-sharp ms-2">forward_to_inbox</span>
                                <p class="align-self-center text-warning opacity-50">Compose</p>
                            </a>
                        </li>
                    
                        <li id="folder-anchor" name="folder-anchor" class="nav-item">
                            <a href="#" class="nav-link">
                                <span class="nav-icon material-symbols-sharp ms-2">folder_open</span>
                                <p class="align-self-center text-warning opacity-50 fw-bold">Folders</p>
                                <i class="nav-arrow bi bi-chevron-right"></i>
                            </a>

                            <ul id="folder-list" name="folder-list" class="nav nav-treeview">
                                <li class="nav-items">
                                    <a href="/game?page=inbox" class="nav-link align-items-center">
                                        <span class="nav-icon material-symbols-sharp ms-4">inbox</span>
                                        <p class="align-self-center text-warning opacity-50">Inbox</p>
                                    <?php if ($folders['INBOX']): ?>
                                        <span class="nav-badge badge text-bg-danger me-3"><?php echo $folders['INBOX']; ?></span>
                                    <?php else: ?>
                                        <span class="nav-badge badge text-bg-secondary me-3">0</span>
                                    <?php endif; ?>
                                    </a>
                                </li>
                            
                                <li class="nav-item">
                                    <a href="/game?page=outbox" class="nav-link">
                                        <span class="nav-icon material-symbols-sharp ms-4">outbox</span>
                                        <p class="align-self-center text-warning opacity-50">Outbox</p>
                                    <?php if ($folders['OUTBOX']): ?>
                                        <span class="nav-badge badge text-bg-danger me-3"><?php echo $folders['OUTBOX']; ?></span>
                                    <?php else: ?>
                                        <span class="nav-badge badge text-bg-secondary me-3">0</span>
                                    <?php endif; ?>
                                    </a>
                                </li>
                            
                                <li class="nav-item">
                                    <a href="/game?page=deleted" class="nav-link">
                                        <span class="nav-icon material-symbols-sharp ms-4">cancel_presentation</span>
                                        <p class="align-self-center text-warning opacity-50">Deleted</p>
                                    <?php if ($folders['DELETED']): ?>
                                        <span class="nav-badge badge text-bg-danger me-3"><?php echo $folders['DELETED']; ?></span>
                                    <?php else: ?>
                                        <span class="nav-badge badge text-bg-secondary me-3">0</span>
                                    <?php endif; ?>
                                    </a>
                                </li>
                            
                                <li class="nav-item">
                                    <a href="/game?page=drafts" class="nav-link align-items-center-">
                                        <span class="nav-icon material-symbols-sharp ms-4">mark_as_unread</span>
                                        <p class="align-self-center text-warning opacity-50">Drafts</p>
                                        <?php if ($folders['DRAFTS']): ?>
                                        <span class="nav-badge badge text-bg-danger me-3"><?php echo $folders['DRAFTS']; ?></span>
                                        <?php else: ?>
                                        <span class="nav-badge badge text-bg-secondary me-3">0</span>
                                        <?php endif; ?>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a href="/game?page=mail-settings" class="nav-link">
                                <span class="nav-icon material-symbols-sharp ms-2">inbox_customize</span>
                                <p class="align-self-center text-warning opacity-50">Settings</p>
                            </a>
                        </li>
                    </ul>
                </li>
            
                <li id="account-anchor" name="account-anchor"  class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="nav-icon material-symbols-sharp">person_pin</span>
                        <p class="align-self-center fw-bold shadow fw-bold">Account</p>
                        <i class="nav-arrow bi bi-chevron-right"></i>
                    </a>
                    
                    <ul id="account-list" name="account-list" class="nav nav-treeview">
                        <li
                        -+ class="nav-item">
                            <a href="/game?page=Profile" class="nav-link">
                                <span class="nav-icon material-symbols-sharp ms-2">assignment_ind</span>
                                <p class="align-self-center text-warning opacity-50">Profile</p>
                            </a>
                        </li>
                
                        <li class="nav-item">
                            <a href="/game?page=Awards" class="nav-link">
                                <span class="nav-icon material-symbols-sharp ms-2">workspace_premium</span>
                                <p class="align-self-center text-warning opacity-50">Awards</p>
                            </a>
                        </li>
                    
                        <li class="nav-item">
                            <a href="/game?page=achievements" class="nav-link">
                                <span class="nav-icon material-symbols-sharp ms-2">military_tech</span>
                                <p class="align-self-center text-warning opacity-50">Achievements</p>
                            </a>
                        </li>
            
                        <li class="nav-item">
                            <a href="/game?page=friends" class="nav-link">
                                <span class="nav-icon material-symbols-sharp ms-2">cheer</span>
                                <p class="align-self-center text-warning opacity-50">Friends</p>
                            </a>
                        </li>
                            
                        <li class="nav-item">
                            <a href="/select" class="nav-link">
                                <span class="nav-icon material-symbols-sharp ms-2">group</span>
                                <p class="align-self-center text-warning opacity-50">Character Select</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="/game?page=acct-settings" class="nav-link">
                                <span class="material-symbols-sharp ms-2">settings_account_box</span>
                                <p class="align-self-center text-warning opacity-50">Settings</p>
                            </a>
                        </li>                        
                    </ul>
                </li>
            </ul>
            <a href="/logout"><div class="text-danger border p-3 m-3 text-center mb-5 shadow">Sign out</div></a>
        </nav>
    </div>
</aside>