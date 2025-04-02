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

<aside id="sidebar" class="app-sidebar shadow overflow-hidden" data-bs-theme="<?php echo $color_mode; ?>" 
       style="width: 230px; min-width: 230px; background: rgba(33, 37, 41, 0.95); height: 100vh;">
    <div class="sidebar-brand d-flex align-items-center mb-3">
        <a href="/game" class="brand-link ms-2">
            <img src="/img/logos/logo-banner-no-bg.webp" alt="Legend of Aetheria Logo" class="brand-image img-fluid">
        </a>
        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
            <i class="bi bi-list"></i>
        </a>
    </div>

    <div class="d-flex justify-content-center mb-3">
        <?php include 'navs/sidemenus/nav-quicknav.php'; ?>
    </div>

    <div class="sidebar-wrapper" style="height: calc(100vh - 100px); overflow-y: auto;">
        <nav class="nav-menu h-100 d-flex flex-column">
            <ul class="nav sidebar-menu flex-column flex-grow-1" data-lte-toggle="treeview" role="menu">
                <!-- Character Section -->
                <li id="character-anchor" class="nav-item menu-open">
                    <a href="#" class="nav-link d-flex align-items-center px-2">
                        <i class="nav-icon material-symbols-outlined"><?php echo $char_menu_icon; ?></i>
                        <p class="ms-2">Character</p>
                        <i class="ms-auto bi bi-chevron-right"></i>
                    </a>
                    
                    <ul id="character-list" class="nav nav-treeview">
                        <!-- Character submenu items -->
                        <li class="nav-item">
                            <a href="/game?page=char-profile" class="nav-link d-flex align-items-center ps-3">
                                <i class="nav-icon material-symbols-outlined">person</i>
                                <p class="ms-2">Profile</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="/game?page=sheet" class="nav-link d-flex align-items-center ps-3">
                                <i class="nav-icon material-symbols-outlined">mist</i>
                                <p class="ms-2">Sheet</p>
                            </a>
                        </li>
                        
                        <li id="inventory-anchor" class="nav-item">
                            <a href="#" class="nav-link d-flex align-items-center ps-3">
                                <i class="nav-icon material-symbols-outlined">inventory_2</i>
                                <p class="ms-2">Inventory</p>
                                <i class="ms-auto bi bi-chevron-right"></i>
                            </a>
                        
                            <ul id="inventory-list" class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="/game?page=Equipment" class="nav-link d-flex align-items-center ps-4">
                                        <i class="nav-icon material-symbols-outlined">colorize</i>
                                        <p class="ms-2">Equipment</p>
                                    </a>
                                </li>
                                
                                <li id="items-anchor" class="nav-item">
                                    <a href="#" class="nav-link d-flex align-items-center ps-4">
                                        <i class="nav-icon material-symbols-outlined">handyman</i>
                                        <p class="ms-2">Items</p>
                                        <i class="ms-auto bi bi-chevron-right"></i>
                                    </a>

                                    <ul id="items-list" class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="/game?page=Questitems" class="nav-link d-flex align-items-center ps-5">
                                                <i class="nav-icon material-symbols-outlined">deployed_code_alert</i>
                                                <p class="ms-2">Quest Items</p>
                                            </a>
                                        </li>

                                        <li class="nav-item">
                                            <a href="/game?page=Consumables" class="nav-link d-flex align-items-center ps-5">
                                                <i class="nav-icon material-symbols-outlined">grocery</i>
                                                <p class="ms-2">Consumables</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>

                            <li class="nav-item">
                                <a href="/game?page=skills" class="nav-link d-flex align-items-center ps-3">
                                    <i class="nav-icon material-symbols-outlined">hotel_class</i>
                                    <p class="ms-2">Skills</p>
                                </a>
                            </li>
                        
                            <li class="nav-item">
                                <a href="/game?page=spells" class="nav-link d-flex align-items-center ps-3">
                                    <i class="nav-icon material-symbols-outlined">book</i>
                                    <p class="ms-2">Spells</p>
                                </a>
                            </li>
                        
                            <li class="nav-item">
                                <a href="/game?page=train" class="nav-link d-flex align-items-center ps-3">
                                    <i class="nav-icon material-symbols-outlined">fitness_center</i>
                                    <p class="ms-2">Train</p>
                                </a>
                            </li>
                        </li>
                    </ul>
                </li>

                <li id="familiar-anchor" class="nav-item">
                    <a href="#" class="nav-link d-flex align-items-center px-2">
                        <i class="nav-icon material-symbols-outlined">raven</i>
                        <p class="ms-2">Familiar</p>
                        <i class="ms-auto bi bi-chevron-right"></i>
                    </a>

                    <ul id="familiar-list" class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/game?page=Manage" class="nav-link d-flex align-items-center ps-3">
                                <i class="nav-icon material-symbols-outlined">sound_detection_dog_barking</i>
                                <p class="ms-2">Manage</p>
                            </a>
                        </li>
                    
                        <li class="nav-item">
                            <a href="/game?page=Hatchery" class="nav-link d-flex align-items-center ps-3">
                                <i class="nav-icon material-symbols-outlined">egg</i>
                                <p class="ms-2">Hatchery</p>
                            </a>
                        </li>
                    
                        <li class="nav-item">
                            <a href="/game?page=Equipment" class="nav-link d-flex align-items-center ps-3">
                                <i class="nav-icon material-symbols-outlined">pet_supplies</i>
                                <p class="ms-2">Equipment</p>
                            </a>
                        </li>
                    </ul>
                </li>
            
                <li id="location-anchor" class="nav-item">
                    <a href="#" class="nav-link d-flex align-items-center px-2">
                        <i class="nav-icon material-symbols-outlined">public</i>
                        <p class="ms-2">Location</p>
                        <i class="ms-auto bi bi-chevron-right"></i>
                    </a>
                
                    <ul id="location-list" class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/game?page=hunt" class="nav-link d-flex align-items-center ps-3">
                                <i class="nav-icon material-symbols-outlined">cruelty_free</i>
                                <p class="ms-2">Hunt</p>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="/game?page=Map" class="nav-link d-flex align-items-center ps-3">
                                <i class="nav-icon material-symbols-outlined">map</i>
                                <p class="ms-2">Map</p>
                            </a>
                        </li>
                    
                        <li class="nav-item">
                            <a href="/game?page=Explore" class="nav-link d-flex align-items-center ps-3">
                                <i class="nav-icon material-symbols-outlined">forest</i>
                                <p class="ms-2">Explore</p>
                            </a>
                        </li>
                    
                        <li class="nav-item">
                            <a href="/game?page=Zone" class="nav-link d-flex align-items-center ps-3">
                                <i class="nav-icon material-symbols-outlined">rocket</i>
                                <p class="ms-2">Zone</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="/game?page=Rest" class="nav-link d-flex align-items-center ps-3">
                                <i class="nav-icon material-symbols-outlined">offline_bolt</i>
                                <p class="ms-2">Rest</p>
                            </a>
                        </li>
                    </ul>
                </li>
            
                <li id="economy-anchor" class="nav-item">
                    <a href="#" class="nav-link d-flex align-items-center px-2">
                        <i class="nav-icon material-symbols-outlined">monitoring</i>
                        <p class="ms-2">Economy</p>
                        <i class="ms-auto bi bi-chevron-right"></i>
                    </a>
                
                    <ul id="economy-list" class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/game?page=Equipment" class="nav-link d-flex align-items-center ps-3">
                                <i class="nav-icon material-symbols-outlined">swords</i>
                                <p class="ms-2">Equipment</p>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="/game?page=Items" class="nav-link d-flex align-items-center ps-3">
                                <i class="nav-icon material-symbols-outlined">diamond</i>
                                <p class="ms-2">Items</p>
                            </a>
                        </li>
                    
                        <li id="blackmarket-anchor" class="nav-item">
                            <a href="#" class="nav-link d-flex align-items-center ps-3">
                                <i class="nav-icon material-symbols-outlined">loyalty</i>
                                <p class="ms-2">Blackmarket</p>
                                <i class="ms-auto bi bi-chevron-right"></i>
                            </a>
                        
                            <ul id="blackmarket-list" class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="/game?page=Buy" class="nav-link d-flex align-items-center ps-4">
                                        <i class="nav-icon material-symbols-outlined">attach_money</i>
                                        <p class="ms-2">Buy</p>
                                    </a>
                                </li>
                                
                                <li class="nav-item">
                                    <a href="/game?page=Sell" class="nav-link d-flex align-items-center ps-4">
                                        <i class="nav-icon material-symbols-outlined">paid</i>
                                        <p class="ms-2">Sell</p>
                                    </a>
                                </li>
                                
                                <li class="nav-item">
                                    <a href="/game?page=Market" class="nav-link d-flex align-items-center ps-4">
                                        <i class="nav-icon material-symbols-outlined">storefront</i>
                                        <p class="ms-2">Market</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                            
                        <li id="bank-anchor" class="nav-item">
                            <a href="#" class="nav-link d-flex align-items-center ps-3">
                                <i class="nav-icon material-symbols-outlined">account_balance</i>
                                <p class="ms-2">Bank</p>
                                <i class="ms-auto bi bi-chevron-right"></i>
                            </a>
                            
                            <ul id="bank-list" class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="/game?page=Loans" class="nav-link d-flex align-items-center ps-4">
                                        <i class="nav-icon material-symbols-outlined">clinical_notes</i>
                                        <p class="ms-2">Account</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="/game?page=Depost" class="nav-link d-flex align-items-center ps-4">
                                        <i class="nav-icon material-symbols-outlined">attach_money</i>
                                        <p class="ms-2">Depost</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="/game?page=Withdrawal" class="nav-link d-flex align-items-center ps-4">
                                        <i class="nav-icon material-symbols-outlined">paid</i>
                                        <p class="ms-2">Withdrawal</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="/game?page=Loans" class="nav-link d-flex align-items-center ps-4">
                                        <i class="nav-icon material-symbols-outlined">payments</i>
                                        <p class="ms-2">Loans</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                
                <li id="dungeon-anchor" class="nav-item">
                    <a href="#" class="nav-link d-flex align-items-center px-2">
                        <i class="nav-icon material-symbols-outlined">widgets</i>
                        <p class="ms-2">Dungeon</p>
                        <i class="ms-auto bi bi-chevron-right"></i>
                    </a>
                    
                    <ul id="dungeon-list" class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/game?page=dungeon" class="nav-link d-flex align-items-center ps-3">
                                <i class="nav-icon material-symbols-outlined">stat_minus_3</i>
                                <p class="ms-2">Floor 1</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="/game?page=Settings" class="nav-link d-flex align-items-center ps-3">
                                <i class="nav-icon material-symbols-outlined">settings</i>
                                <p class="ms-2">Settings</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="/game?page=Reset" class="nav-link d-flex align-items-center ps-3">
                                <i class="nav-icon material-symbols-outlined text-danger">restart_alt</i>
                                <p class="ms-2">Reset</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li id="quests-anchor" class="nav-item">
                    <a href="#" class="nav-link d-flex align-items-center px-2">
                        <i class="nav-icon material-symbols-outlined">volcano</i>
                        <p class="ms-2">Quests</p>
                        <i class="ms-auto bi bi-chevron-right"></i>
                    </a>
                    
                    <ul id="quest-list" class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/game?page=Active" class="nav-link d-flex align-items-center ps-3">
                                <i class="nav-icon material-symbols-outlined">lists</i>
                                <p class="ms-2">Active</p>
                            </a>
                        </li>
                
                        <li class="nav-item">
                            <a href="/game?page=Accepted" class="nav-link d-flex align-items-center ps-3">
                                <i class="nav-icon material-symbols-outlined">fact_check</i>
                                <p class="ms-2">Accepted</p>
                            </a>
                        </li>
                
                        <li class="nav-item">
                            <a href="/game?page=Completed" class="nav-link d-flex align-items-center ps-3">
                                <i class="nav-icon material-symbols-outlined">done_all</i>
                                <p class="ms-2">Completed</p>
                            </a>
                        </li>
                
                        <li class="nav-item">
                            <a href="/game?page=Abandoned" class="nav-link d-flex align-items-center ps-3">
                                <i class="nav-icon material-symbols-outlined">backspace</i>
                                <p class="ms-2">Abandoned</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li id="mail-anchor" class="nav-item">
                    <a href="#" class="nav-link d-flex align-items-center px-2">
                        <i class="nav-icon material-symbols-outlined">alternate_email</i>
                        <p class="ms-2">Mail</p>
                        <i class="ms-auto bi bi-chevron-right"></i>
                    </a>
                    
                    <ul id="mail-list" class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/game?page=compose" class="nav-link d-flex align-items-center ps-3">
                                <i class="nav-icon material-symbols-outlined">forward_to_inbox</i>
                                <p class="ms-2">Compose</p>
                            </a>
                        </li>
                    
                        <li id="folder-anchor" class="nav-item">
                            <a href="#" class="nav-link d-flex align-items-center ps-3">
                                <i class="nav-icon material-symbols-outlined">folder_open</i>
                                <p class="ms-2">Folders</p>
                                <i class="ms-auto bi bi-chevron-right"></i>
                            </a>

                            <ul id="folder-list" class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="/game?page=inbox" class="nav-link d-flex align-items-center ps-4">
                                        <i class="nav-icon material-symbols-outlined">inbox</i>
                                        <p class="ms-2">Inbox</p>
                                    <?php if ($folders['INBOX']): ?>
                                        <span class="nav-badge badge text-bg-danger ms-auto me-3"><?php echo $folders['INBOX']; ?></span>
                                    <?php else: ?>
                                        <span class="nav-badge badge text-bg-secondary ms-auto me-3">0</span>
                                    <?php endif; ?>
                                    </a>
                                </li>
                            
                                <li class="nav-item">
                                    <a href="/game?page=outbox" class="nav-link d-flex align-items-center ps-4">
                                        <i class="nav-icon material-symbols-outlined">outbox</i>
                                        <p class="ms-2">Outbox</p>
                                    <?php if ($folders['OUTBOX']): ?>
                                        <span class="nav-badge badge text-bg-danger ms-auto me-3"><?php echo $folders['OUTBOX']; ?></span>
                                    <?php else: ?>
                                        <span class="nav-badge badge text-bg-secondary ms-auto me-3">0</span>
                                    <?php endif; ?>
                                    </a>
                                </li>
                            
                                <li class="nav-item">
                                    <a href="/game?page=deleted" class="nav-link d-flex align-items-center ps-4">
                                        <i class="nav-icon material-symbols-outlined">cancel_presentation</i>
                                        <p class="ms-2">Deleted</p>
                                    <?php if ($folders['DELETED']): ?>
                                        <span class="nav-badge badge text-bg-danger ms-auto me-3"><?php echo $folders['DELETED']; ?></span>
                                    <?php else: ?>
                                        <span class="nav-badge badge text-bg-secondary ms-auto me-3">0</span>
                                    <?php endif; ?>
                                    </a>
                                </li>
                            
                                <li class="nav-item">
                                    <a href="/game?page=drafts" class="nav-link d-flex align-items-center ps-4">
                                        <i class="nav-icon material-symbols-outlined">mark_as_unread</i>
                                        <p class="ms-2">Drafts</p>
                                        <?php if ($folders['DRAFTS']): ?>
                                        <span class="nav-badge badge text-bg-danger ms-auto me-3"><?php echo $folders['DRAFTS']; ?></span>
                                        <?php else: ?>
                                        <span class="nav-badge badge text-bg-secondary ms-auto me-3">0</span>
                                        <?php endif; ?>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a href="/game?page=mail-settings" class="nav-link d-flex align-items-center ps-3">
                                <i class="nav-icon material-symbols-outlined">inbox_customize</i>
                                <p class="ms-2">Settings</p>
                            </a>
                        </li>
                    </ul>
                </li>
            
                <li id="account-anchor" class="nav-item">
                    <a href="#" class="nav-link d-flex align-items-center px-2">
                        <i class="nav-icon material-symbols-outlined">person_pin</i>
                        <p class="ms-2">Account</p>
                        <i class="ms-auto bi bi-chevron-right"></i>
                    </a>
                    
                    <ul id="account-list" class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/game?page=acct-profile" class="nav-link d-flex align-items-center ps-3">
                                <i class="nav-icon material-symbols-outlined">assignment_ind</i>
                                <p class="ms-2">Profile</p>
                            </a>
                        </li>
                
                        <li class="nav-item">
                            <a href="/game?page=Awards" class="nav-link d-flex align-items-center ps-3">
                                <i class="nav-icon material-symbols-outlined">workspace_premium</i>
                                <p class="ms-2">Awards</p>
                            </a>
                        </li>
                    
                        <li class="nav-item">
                            <a href="/game?page=achievements" class="nav-link d-flex align-items-center ps-3">
                                <i class="nav-icon material-symbols-outlined">military_tech</i>
                                <p class="ms-2">Achievements</p>
                            </a>
                        </li>
            
                        <li class="nav-item">
                            <a href="/game?page=friends" class="nav-link d-flex align-items-center ps-3">
                                <i class="nav-icon material-symbols-outlined">cheer</i>
                                <p class="ms-2">Friends</p>
                            </a>
                        </li>
                            
                        <li class="nav-item">
                            <a href="/select" class="nav-link d-flex align-items-center ps-3">
                                <i class="nav-icon material-symbols-outlined">group</i>
                                <p class="ms-2">Character Select</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="/game?page=acct-settings" class="nav-link d-flex align-items-center ps-3">
                                <i class="nav-icon material-symbols-outlined">settings_account_box</i>
                                <p class="ms-2">Settings</p>
                            </a>
                        </li>                        
                    </ul>
                </li>
            <div class="mt-auto p-3">
                <a href="/logout" class="btn btn-danger w-100 shadow">Sign out</a>
            </div>
        </ul>
        </nav>
    </div>
</aside>