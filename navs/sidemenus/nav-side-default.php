<?php
require_once "bootstrap.php";
use Game\Account\Account;
use Game\Character\Character;
use Game\Mail\Folder\Enums\FolderType;
use Game\Mail\MailBox\MailBox;

$account   = new Account($_SESSION['email']); 
$character = new Character($account->get_id(), $_SESSION['character-id']); 
$folders = [];

foreach (["OUTBOX", "INBOX", "DELETED", "DRAFTS"] as $type) {
    $folder = FolderType::name_to_enum($type);

    $folders[$type] = MailBox::getFolderCount(
        FolderType::name_to_enum($type),
        $character->get_id()
    );
}

$char_menu_icon = $character->stats->get_hp() > 0 ? 'sentiment_satisfied' : 'skull';

// Determine the current page and submenu
$currentPage = $_GET['page'] ?? '';
$currentSub = $_GET['sub'] ?? '';
?>

<aside id="sidebar" class="app-sidebar shadow overflow-hidden" data-bs-theme="<?php echo $color_mode; ?>" style="width: 240px; min-width: 240px; height: 100vh;">
    <div class="sidebar-brand d-flex align-items-center mb-3">
        <a href="/game" class="brand-link ms-2">
            <img src="/img/logos/logo-banner-no-bg.webp" alt="Legend of Aetheria Logo" class="brand-image img-fluid">
        </a>
    </div>

    <div class="d-flex justify-items-center text-center mb-3">
        <?php include 'navs/sidemenus/nav-quicknav.php'; ?>
    </div>

    <div class="sidebar-wrapper" style="height: calc(100vh - 100px); overflow-y: auto;">
        <nav class="nav-menu h-100 d-flex flex-column">
            <ul class="nav sidebar-menu flex-column flex-grow-1" data-lte-toggle="treeview" role="menu">
                <!-- Character Section -->
                <li id="character-anchor" class="nav-item <?php echo ($currentSub === 'character' || $currentSub === 'inventory') ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link d-flex align-items-center ps-2">
                        <i class="nav-icon material-symbols-outlined"><?php echo $char_menu_icon; ?></i>
                        <p class="ms-2">Character</p>
                        <i class="ms-auto bi bi-chevron-right"></i>
                    </a>
                    
                    <ul id="character-list" class="nav nav-treeview">
                        <!-- Character submenu items -->
                        <li class="nav-item">
                            <a href="/game?page=profile&sub=character" class="nav-link d-flex align-items-center ps-3 <?php echo ($currentPage === 'profile' && $currentSub === 'character') ? 'active' : ''; ?>">
                                <i class="nav-icon material-symbols-outlined">person</i>
                                <p class="ms-2">Profile</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="/game?page=sheet&sub=character" class="nav-link d-flex align-items-center ps-3 <?php echo ($currentPage === 'sheet' && $currentSub === 'character') ? 'active' : ''; ?>">
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
                                    <a href="/game?page=equipment&sub=inventory" class="nav-link d-flex align-items-center ps-4 <?php echo ($currentPage === 'equipment' && $currentSub === 'inventory') ? 'active' : ''; ?>">
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
                                            <a href="/game?page=quest&sub=items" class="nav-link d-flex align-items-center ps-5 <?php echo ($currentPage === 'quest' && $currentSub === 'items') ? 'active' : ''; ?>">
                                                <i class="nav-icon material-symbols-outlined">deployed_code_alert</i>
                                                <p class="ms-2">Quest Items</p>
                                            </a>
                                        </li>

                                        <li class="nav-item">
                                            <a href="/game?page=consumables&sub=items" class="nav-link d-flex align-items-center ps-5 <?php echo ($currentPage === 'consumables' && $currentSub === 'items') ? 'active' : ''; ?>">
                                                <i class="nav-icon material-symbols-outlined">grocery</i>
                                                <p class="ms-2">Consumables</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>

                            <li class="nav-item">
                                <a href="/game?page=skills&sub=character" class="nav-link d-flex align-items-center ps-3 <?php echo ($currentPage === 'skills' && $currentSub === 'character') ? 'active' : ''; ?>">
                                    <i class="nav-icon material-symbols-outlined">hotel_class</i>
                                    <p class="ms-2">Skills</p>
                                </a>
                            </li>
                        
                            <li class="nav-item">
                                <a href="/game?page=spells&sub=character" class="nav-link d-flex align-items-center ps-3 <?php echo ($currentPage === 'spells' && $currentSub === 'character') ? 'active' : ''; ?>">
                                    <i class="nav-icon material-symbols-outlined">book</i>
                                    <p class="ms-2">Spells</p>
                                </a>
                            </li>
                        
                            <li class="nav-item">
                                <a href="/game?page=train&sub=character" class="nav-link d-flex align-items-center ps-3 <?php echo ($currentPage === 'train' && $currentSub === 'character') ? 'active' : ''; ?>">
                                    <i class="nav-icon material-symbols-outlined">fitness_center</i>
                                    <p class="ms-2">Train</p>
                                </a>
                            </li>
                        </li>
                    </ul>
                </li>

                <li id="familiar-anchor" class="nav-item <?php echo ($currentSub === 'familiar') ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link d-flex align-items-center ps-2">
                        <i class="nav-icon material-symbols-outlined">raven</i>
                        <p class="ms-2">Familiar</p>
                        <i class="ms-auto bi bi-chevron-right"></i>
                    </a>

                    <ul id="familiar-list" class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/game?page=manage&sub=familiar" class="nav-link d-flex align-items-center ps-3 <?php echo ($currentPage === 'manage' && $currentSub === 'familiar') ? 'active' : ''; ?>">
                                <i class="nav-icon material-symbols-outlined">sound_detection_dog_barking</i>
                                <p class="ms-2">Manage</p>
                            </a>
                        </li>
                    
                        <li class="nav-item">
                            <a href="/game?page=hatchery&sub=familiar" class="nav-link d-flex align-items-center ps-3 <?php echo ($currentPage === 'hatchery' && $currentSub === 'familiar') ? 'active' : ''; ?>">
                                <i class="nav-icon material-symbols-outlined">egg</i>
                                <p class="ms-2">Hatchery</p>
                            </a>
                        </li>
                    
                        <li class="nav-item">
                            <a href="/game?page=equipment&sub=familiar" class="nav-link d-flex align-items-center ps-3 <?php echo ($currentPage === 'equipment' && $currentSub === 'familiar') ? 'active' : ''; ?>">
                                <i class="nav-icon material-symbols-outlined">pet_supplies</i>
                                <p class="ms-2">Equipment</p>
                            </a>
                        </li>
                    </ul>
                </li>
            
                <li id="location-anchor" class="nav-item <?php echo ($currentSub === 'location') ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link d-flex align-items-center ps-2">
                        <i class="nav-icon material-symbols-outlined">public</i>
                        <p class="ms-2">Location</p>
                        <i class="ms-auto bi bi-chevron-right"></i>
                    </a>
                
                    <ul id="location-list" class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/game?page=hunt&sub=location" class="nav-link d-flex align-items-center ps-3 <?php echo ($currentPage === 'hunt' && $currentSub === 'location') ? 'active' : ''; ?>">
                                <i class="nav-icon material-symbols-outlined">cruelty_free</i>
                                <p class="ms-2">Hunt</p>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="/game?page=map&sub=location" class="nav-link d-flex align-items-center ps-3 <?php echo ($currentPage === 'map' && $currentSub === 'location') ? 'active' : ''; ?>">
                                <i class="nav-icon material-symbols-outlined">map</i>
                                <p class="ms-2">Map</p>
                            </a>
                        </li>
                    
                        <li class="nav-item">
                            <a href="/game?page=explore&sub=location" class="nav-link d-flex align-items-center ps-3 <?php echo ($currentPage === 'explore' && $currentSub === 'location') ? 'active' : ''; ?>">
                                <i class="nav-icon material-symbols-outlined">forest</i>
                                <p class="ms-2">Explore</p>
                            </a>
                        </li>
                    
                        <li class="nav-item">
                            <a href="/game?page=zone&sub=location" class="nav-link d-flex align-items-center ps-3 <?php echo ($currentPage === 'zone' && $currentSub === 'location') ? 'active' : ''; ?>">
                                <i class="nav-icon material-symbols-outlined">rocket</i>
                                <p class="ms-2">Zone</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="/game?page=rest&sub=location" class="nav-link d-flex align-items-center ps-3 <?php echo ($currentPage === 'rest' && $currentSub === 'location') ? 'active' : ''; ?>">
                                <i class="nav-icon material-symbols-outlined">offline_bolt</i>
                                <p class="ms-2">Rest</p>
                            </a>
                        </li>
                    </ul>
                </li>
            
                <li id="economy-anchor" class="nav-item <?php echo ($currentSub === 'economy' || $currentSub === 'blackmarket' || $currentSub === 'bank') ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link d-flex align-items-center ps-2">
                        <i class="nav-icon material-symbols-outlined">monitoring</i>
                        <p class="ms-2">Economy</p>
                        <i class="ms-auto bi bi-chevron-right"></i>
                    </a>
                
                    <ul id="economy-list" class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/game?page=equipment&sub=economy" class="nav-link d-flex align-items-center ps-3 <?php echo ($currentPage === 'equipment' && $currentSub === 'economy') ? 'active' : ''; ?>">
                                <i class="nav-icon material-symbols-outlined">swords</i>
                                <p class="ms-2">Equipment</p>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="/game?page=items&sub=economy" class="nav-link d-flex align-items-center ps-3 <?php echo ($currentPage === 'items' && $currentSub === 'economy') ? 'active' : ''; ?>">
                                <i class="nav-icon material-symbols-outlined">diamond</i>
                                <p class="ms-2">Items</p>
                            </a>
                        </li>
                    
                        <li id="blackmarket-anchor" class="nav-item <?php echo ($currentSub === 'blackmarket') ? 'menu-open' : ''; ?>">
                            <a href="#" class="nav-link d-flex align-items-center ps-3">
                                <i class="nav-icon material-symbols-outlined">loyalty</i>
                                <p class="ms-2">Blackmarket</p>
                                <i class="ms-auto bi bi-chevron-right"></i>
                            </a>
                        
                            <ul id="blackmarket-list" class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="/game?page=buy&sub=blackmarket" class="nav-link d-flex align-items-center ps-4 <?php echo ($currentPage === 'buy' && $currentSub === 'blackmarket') ? 'active' : ''; ?>">
                                        <i class="nav-icon material-symbols-outlined">attach_money</i>
                                        <p class="ms-2">Buy</p>
                                    </a>
                                </li>
                                
                                <li class="nav-item">
                                    <a href="/game?page=sell&sub=blackmarket" class="nav-link d-flex align-items-center ps-4 <?php echo ($currentPage === 'sell' && $currentSub === 'blackmarket') ? 'active' : ''; ?>">
                                        <i class="nav-icon material-symbols-outlined">paid</i>
                                        <p class="ms-2">Sell</p>
                                    </a>
                                </li>
                                
                                <li class="nav-item">
                                    <a href="/game?page=market&sub=blackmarket" class="nav-link d-flex align-items-center ps-4 <?php echo ($currentPage === 'market' && $currentSub === 'blackmarket') ? 'active' : ''; ?>">
                                        <i class="nav-icon material-symbols-outlined">storefront</i>
                                        <p class="ms-2">Market</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                            
                        <li id="bank-anchor" class="nav-item <?php echo ($currentSub === 'bank') ? 'menu-open' : ''; ?>">
                            <a href="#" class="nav-link d-flex align-items-center ps-3">
                                <i class="nav-icon material-symbols-outlined">account_balance</i>
                                <p class="ms-2">Bank</p>
                                <i class="ms-auto bi bi-chevron-right"></i>
                            </a>
                            
                            <ul id="bank-list" class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="/game?page=loans&sub=bank" class="nav-link d-flex align-items-center ps-4 <?php echo ($currentPage === 'loans' && $currentSub === 'bank') ? 'active' : ''; ?>">
                                        <i class="nav-icon material-symbols-outlined">clinical_notes</i>
                                        <p class="ms-2">Account</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="/game?page=deposit&sub=bank" class="nav-link d-flex align-items-center ps-4 <?php echo ($currentPage === 'deposit' && $currentSub === 'bank') ? 'active' : ''; ?>">
                                        <i class="nav-icon material-symbols-outlined">attach_money</i>
                                        <p class="ms-2">Depost</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="/game?page=withdrawal&sub=bank" class="nav-link d-flex align-items-center ps-4 <?php echo ($currentPage === 'withdrawal' && $currentSub === 'bank') ? 'active' : ''; ?>">
                                        <i class="nav-icon material-symbols-outlined">paid</i>
                                        <p class="ms-2">Withdrawal</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="/game?page=loans&sub=bank" class="nav-link d-flex align-items-center ps-4 <?php echo ($currentPage === 'loans' && $currentSub === 'bank') ? 'active' : ''; ?>">
                                        <i class="nav-icon material-symbols-outlined">payments</i>
                                        <p class="ms-2">Loans</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                
                <li id="dungeon-anchor" class="nav-item <?php echo ($currentSub === 'dungeon') ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link d-flex align-items-center ps-2">
                        <i class="nav-icon material-symbols-outlined">widgets</i>
                        <p class="ms-2">Dungeon</p>
                        <i class="ms-auto bi bi-chevron-right"></i>
                    </a>
                    
                    <ul id="dungeon-list" class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/game?page=dungeon&sub=dungeon" class="nav-link d-flex align-items-center ps-3 <?php echo ($currentPage === 'dungeon' && $currentSub === 'dungeon') ? 'active' : ''; ?>">
                                <i class="nav-icon material-symbols-outlined">stat_minus_3</i>
                                <p class="ms-2">Floor 1</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="/game?page=settings&sub=dungeon" class="nav-link d-flex align-items-center ps-3 <?php echo ($currentPage === 'settings' && $currentSub === 'dungeon') ? 'active' : ''; ?>">
                                <i class="nav-icon material-symbols-outlined">settings</i>
                                <p class="ms-2">Settings</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="/game?page=reset&sub=dungeon" class="nav-link d-flex align-items-center ps-3 <?php echo ($currentPage === 'reset' && $currentSub === 'dungeon') ? 'active' : ''; ?>">
                                <i class="nav-icon material-symbols-outlined text-danger">restart_alt</i>
                                <p class="ms-2">Reset</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li id="quests-anchor" class="nav-item <?php echo ($currentSub === 'quests') ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link d-flex align-items-center ps-2">
                        <i class="nav-icon material-symbols-outlined">volcano</i>
                        <p class="ms-2">Quests</p>
                        <i class="ms-auto bi bi-chevron-right"></i>
                    </a>
                    
                    <ul id="quest-list" class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/game?page=active&sub=quests" class="nav-link d-flex align-items-center ps-3 <?php echo ($currentPage === 'active' && $currentSub === 'quests') ? 'active' : ''; ?>">
                                <i class="nav-icon material-symbols-outlined">lists</i>
                                <p class="ms-2">Active</p>
                            </a>
                        </li>
                
                        <li class="nav-item">
                            <a href="/game?page=accepted&sub=quests" class="nav-link d-flex align-items-center ps-3 <?php echo ($currentPage === 'accepted' && $currentSub === 'quests') ? 'active' : ''; ?>">
                                <i class="nav-icon material-symbols-outlined">fact_check</i>
                                <p class="ms-2">Accepted</p>
                            </a>
                        </li>
                
                        <li class="nav-item">
                            <a href="/game?page=completed&sub=quests" class="nav-link d-flex align-items-center ps-3 <?php echo ($currentPage === 'completed' && $currentSub === 'quests') ? 'active' : ''; ?>">
                                <i class="nav-icon material-symbols-outlined">done_all</i>
                                <p class="ms-2">Completed</p>
                            </a>
                        </li>
                
                        <li class="nav-item">
                            <a href="/game?page=abandoned&sub=quests" class="nav-link d-flex align-items-center ps-3 <?php echo ($currentPage === 'abandoned' && $currentSub === 'quests') ? 'active' : ''; ?>">
                                <i class="nav-icon material-symbols-outlined">backspace</i>
                                <p class="ms-2">Abandoned</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li id="mail-anchor" class="nav-item <?php echo ($currentSub === 'mail' || $currentSub === 'folders') ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link d-flex align-items-center ps-2">
                        <i class="nav-icon material-symbols-outlined">alternate_email</i>
                        <p class="ms-2">Mail</p>
                        <i class="ms-auto bi bi-exclamation-square-fill text-warning"></i>
                        <i class="ms-auto bi bi-chevron-right"></i>
                    </a>
                    
                    <ul id="mail-list" class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/game?page=compose&sub=mail" class="nav-link d-flex align-items-center ps-3 <?php echo ($currentPage === 'compose' && $currentSub === 'mail') ? 'active' : ''; ?>">
                                <i class="nav-icon material-symbols-outlined">forward_to_inbox</i>
                                <p class="ms-2">Compose</p>
                            </a>
                        </li>
                    
                        <li id="folder-anchor" class="nav-item <?php echo ($currentSub === 'folders') ? 'menu-open' : ''; ?>">
                            <a href="#" class="nav-link d-flex align-items-center ps-3">
                                <i class="nav-icon material-symbols-outlined">folder_open</i>
                                <p class="ms-2">Folders</p>
                                <i class="ms-auto bi bi-chevron-right"></i>
                            </a>

                            <ul id="folder-list" class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="/game?page=inbox&sub=folders" class="nav-link d-flex align-items-center ps-4 <?php echo ($currentPage === 'inbox' && $currentSub === 'folders') ? 'active' : ''; ?>">
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
                                    <a href="/game?page=outbox&sub=folders" class="nav-link d-flex align-items-center ps-4 <?php echo ($currentPage === 'outbox' && $currentSub === 'folders') ? 'active' : ''; ?>">
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
                                    <a href="/game?page=deleted&sub=folders" class="nav-link d-flex align-items-center ps-4 <?php echo ($currentPage === 'deleted' && $currentSub === 'folders') ? 'active' : ''; ?>">
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
                                    <a href="/game?page=drafts&sub=folders" class="nav-link d-flex align-items-center ps-4 <?php echo ($currentPage === 'drafts' && $currentSub === 'folders') ? 'active' : ''; ?>">
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
                            <a href="/game?page=settings&sub=mail" class="nav-link d-flex align-items-center ps-3 <?php echo ($currentPage === 'settings' && $currentSub === 'mail') ? 'active' : ''; ?>">
                                <i class="nav-icon material-symbols-outlined">inbox_customize</i>
                                <p class="ms-2">Settings</p>
                            </a>
                        </li>
                    </ul>
                </li>
            
                <li id="account-anchor" class="nav-item <?php echo ($currentSub === 'account' || $currentSub === 'friends') ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link d-flex align-items-center ps-2">
                        <i class="nav-icon material-symbols-outlined">person_pin</i>
                        <p class="ms-2">Account</p>
                        <i class="ms-auto bi bi-chevron-right"></i>
                    </a>
                    
                    <ul id="account-list" class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/game?page=profile&sub=account" class="nav-link d-flex align-items-center ps-3 <?php echo ($currentPage === 'profile' && $currentSub === 'account') ? 'active' : ''; ?>">
                                <i class="nav-icon material-symbols-outlined">assignment_ind</i>
                                <p class="ms-2">Profile</p>
                            </a>
                        </li>
                
                        <li class="nav-item">
                            <a href="/game?page=awards&sub=account" class="nav-link d-flex align-items-center ps-3 <?php echo ($currentPage === 'awards' && $currentSub === 'account') ? 'active' : ''; ?>">
                                <i class="nav-icon material-symbols-outlined">workspace_premium</i>
                                <p class="ms-2">Awards</p>
                            </a>
                        </li>
                    
                        <li class="nav-item">
                            <a href="/game?page=achievements&sub=account" class="nav-link d-flex align-items-center ps-3 <?php echo ($currentPage === 'achievements' && $currentSub === 'account') ? 'active' : ''; ?>">
                                <i class="nav-icon material-symbols-outlined">military_tech</i>
                                <p class="ms-2">Achievements</p>
                            </a>
                        </li>
            
                        <li id="friends-anchor" class="nav-item <?php echo ($currentSub === 'friends') ? 'menu-open' : ''; ?>">
                            <a href="#" class="nav-link d-flex align-items-center ps-3">
                                <i class="nav-icon material-symbols-outlined">cheer</i>
                                <p class="ms-2">Friends</p>
                                <i class="ms-auto bi bi-chevron-right"></i>
                            </a>
                            
                            <ul id="friends-list" class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="/game?page=mutual&sub=friends" class="nav-link d-flex align-items-center ps-4 <?php echo ($currentPage === 'mutual' && $currentSub === 'friends') ? 'active' : ''; ?>">
                                        <i class="nav-icon material-symbols-outlined">handshake</i>
                                        <p class="ms-2">Mutual</p>
                                        <span class="nav-badge badge text-bg-success ms-auto me-3">0</span>
                                        <span class="nav-badge badge text-bg-secondary ms-auto me-3">0</span>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="/game?page=requested&sub=friends" class="nav-link d-flex align-items-center ps-4 <?php echo ($currentPage === 'requested' && $currentSub === 'friends') ? 'active' : ''; ?>">
                                        <i class="nav-icon material-symbols-outlined">person_add</i>
                                        <p class="ms-2">Send Request</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="/game?page=requests&sub=friends" class="nav-link d-flex align-items-center ps-4 <?php echo ($currentPage === 'requests' && $currentSub === 'friends') ? 'active' : ''; ?>">
                                        <i class="nav-icon material-symbols-outlined">emoji_people</i>
                                        <p class="ms-2">Received</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="/game?page=blocked&sub=friends" class="nav-link d-flex align-items-center ps-4 <?php echo ($currentPage === 'blocked' && $currentSub === 'friends') ? 'active' : ''; ?>">
                                        <i class="nav-icon material-symbols-outlined">person_cancel</i>
                                        <p class="ms-2">Blocked</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                            
                        <li class="nav-item">
                            <a href="/select" class="nav-link d-flex align-items-center ps-3">
                                <i class="nav-icon material-symbols-outlined">group</i>
                                <p class="ms-2">Character Select</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="/game?page=settings&sub=account" class="nav-link d-flex align-items-center ps-3 <?php echo ($currentPage === 'settings' && $currentSub === 'account') ? 'active' : ''; ?>">
                                <i class="nav-icon material-symbols-outlined">settings_account_box</i>
                                <p class="ms-2">Settings</p>
                            </a>
                        </li>                        
                    </ul>
                    <div class="pb-5 mb-5 mt-5 d-flex w-100 align-content-center justify-content-center">
                        <a href="/logout" class="btn bg-dark-subtle shadow">
                            <span class="d-flex align-content-around">
                                <span class="material-symbols-outlined float-start">move_item</span>
                                <span class="float-end">&nbsp;&nbsp;&nbsp;Sign out</span>
                            </span>

                        </a>
                    </div>
                </li>
            </ul>
        </nav>
    </div>
</aside>
<div id="sidebar-sliver" class="text-center" style="position: fixed; left: 0; top: 0; width: 10px; height: 100vh; z-index: 999; cursor: pointer; display: none;" onclick="document.body.classList.remove('sidebar-collapse'); document.body.classList.add('sidebar-open');">&gt;</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const activeLink = document.querySelector(".nav-link.active");
    const sidebar = document.getElementById("sidebar");
    const sliver = document.getElementById("sidebar-sliver");
    
    if (activeLink) {
        let parent = activeLink.closest(".nav-item.menu-open");
        while (parent) {
            parent.classList.add("menu-open");
            parent = parent.parentElement.closest(".nav-item.menu-open");
        }

        activeLink.scrollIntoView({ behavior: "smooth", block: "center" });
    }


    
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.target.classList.contains("sidebar-collapse")) {
                sliver.style.display = "flex";
                sliver.style.alignItems = "center";
                sliver.style.justifyContent = "center";
                sliver.style.backgroundColor = "rgba(5, 57, 28, 0.21)";
                sliver.innerHTML = "<i class=\"bi bi-chevron-right\"></i>";
                console.log("SIDEBAR CLOSED");
            } else {
                sliver.style.display = "none";
            }
        });
    });

    observer.observe(document.body, {
        attributes: true,
        attributeFilter: ["class"]
    });
});
</script>

<style>
    .nav-link.active {
        font-weight: bold;
        color: red;
    }
    .menu-open > .nav-treeview {
        display: block !important;
    }
</style>