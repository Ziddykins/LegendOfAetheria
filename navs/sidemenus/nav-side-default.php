<?php
$char_menu_icon = $character->stats->get_hp() > 0 ? 'sentiment_satisfied' : 'sentiment_sad';
?>

<aside id="sidebar" name="sidebar" class="app-sidebar shadow overflow-hidden" data-bs-theme="<?php echo $color_mode; ?>">
    <div class="sidebar-brand">
        <a href="./index.php" class="brand-link">
            <img src="/img/logos/logo-banner-no-bg.webp" alt="Legend of Aetheria Logo" class="brand-image img-fluid">
        </a>
    </div>

    <div class="sidebar-wrapper">
        <nav>
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                <li class="nav-item menu-open">
                    <li class="nav-item">
                        <li class="nav-header">Character</li>
                    </li>
                
                    <li class="nav-item">
                        <a href="/game?page=Sheet" class="nav-link align-items-center">
                            <span class="nav-icon material-symbols-sharp">mist</span>
                            <p class="align-self-center">Sheet</p>
                        </a>
                    </li>
                
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <span class="nav-icon material-symbols-sharp">inventory_2</span>
                            <p class="align-self-center">Inventory</p>
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </a>
                        
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="/game?page=Equipment" class="nav-link">
                                    <span class="nav-icon material-symbols-sharp ms-2">colorize</span>
                                    <p class="align-self-center">Equipment</p>
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <span class="nav-icon material-symbols-sharp ms-2">handyman</span>
                                    <p class="align-self-center">Items</p>
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </a>

                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="/game?page=Questitems" class="nav-link justify-items-center">
                                            <span class="nav-icon material-symbols-sharp ms-4">deployed_code_alert</span>
                                            <p class="align-self-center">Quest Items</p>
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="/game?page=Consumables" class="nav-link">
                                            <span class="nav-icon material-symbols-sharp ms-4">grocery</span>
                                            <p class="align-self-center">Consumables</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>

                        <li class="nav-item">
                            <a href="/game?page=Skills" class="nav-link">
                                <span class="nav-icon material-symbols-sharp">hotel_class</span>
                                <p class="align-self-center">Skills</p>
                            </a>
                        </li>
                       
                        <li class="nav-item">
                            <a href="/game?page=Spells" class="nav-link">
                                <span class="nav-icon material-symbols-sharp">book</span>
                                <p class="align-self-center">Spells</p>
                            </a>
                        </li>
                       
                        <li class="nav-item">
                            <a href="/game?page=Train" class="nav-link">
                                <span class="nav-icon material-symbols-sharp">fitness_center</span>
                                <p class="align-self-center">Train</p>
                            </a>
                        </li>
                    </li>
                    
                    <li class="nav-item">
                        <li class="nav-header">Familiar</li>
                    </li>
                
                    <li class="nav-item">
                        <a href="/game?page=Manage" class="nav-link">
                            <span class="nav-icon material-symbols-sharp ms-2">raven</span>
                            <p class="align-self-center">Manage</p>
                        </a>
                    </li>
                
                    <li class="nav-item">
                        <a href="/game?page=Hatchery" class="nav-link">
                            <span class="nav-icon material-symbols-sharp ms-2">egg</span>
                            <p class="align-self-center">Hatchery</p>
                        </a>
                    </li>
                
                    <li class="nav-item">
                        <a href="/game?page=Equipment" class="nav-link">
                            <span class="nav-icon material-symbols-sharp ms-2">pet_supplies</span>
                            <p class="align-self-center">Equipment</p>
                        </a>
                    </li>
                
                    <li class="nav-item">
                        <li class="nav-header">Location</li>
                    </li>
                
                    <li class="nav-item">
                        <a href="/game?page=Hunt" class="nav-link">
                            <span class="nav-icon material-symbols-sharp ms-2">cruelty_free</span>
                            <p class="align-self-center">Hunt</p>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="/game?page=Map" class="nav-link">
                            <span class="nav-icon material-symbols-sharp ms-2">map</span>
                            <p class="align-self-center">Map</p>
                        </a>
                    </li>
                
                    <li class="nav-item">
                        <a href="/game?page=Explore" class="nav-link">
                            <span class="nav-icon material-symbols-sharp ms-2">forest</span>
                            <p class="align-self-center">Explore</p>
                        </a>
                    </li>
                
                    <li class="nav-item">
                        <a href="/game?page=Zone" class="nav-link">
                            <span class="nav-icon material-symbols-sharp ms-2">rocket</span>
                            <p class="align-self-center">Zone</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/game?page=Rest" class="nav-link">
                            <span class="nav-icon material-symbols-sharp ms-2">offline_bolt</span>
                            <p class="align-self-center">Rest</p>
                        </a>
                    </li>
                
                    <li class="nav-item">
                        <li class="nav-header">Economy</li>
                    </li>

                    <li class="nav-item">
                        <a href="/game?page=Equipment" class="nav-link">
                            <span class="nav-icon material-symbols-sharp ms-2">swords</span>
                            <p class="align-self-center">Equipment</p>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="/game?page=Items" class="nav-link">
                            <span class="nav-icon material-symbols-sharp ms-2">diamond</span>
                            <p class="align-self-center">Items</p>
                        </a>
                    </li>
                
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <span class="nav-icon material-symbols-sharp ms-2">loyalty</span>
                            <p class="align-self-center">Blackmarket</p>
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </a>
                    
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="/game?page=Buy" class="nav-link">
                                    <span class="nav-icon material-symbols-sharp ms-4">paid</span>
                                    <p class="align-self-center">Buy</p>
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a href="/game?page=Sell" class="nav-link">
                                    <span class="nav-icon material-symbols-sharp ms-4">attach_money</span>
                                    <p class="align-self-center">Sell</p>
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a href="/game?page=Market" class="nav-link">
                                    <span class="nav-icon material-symbols-sharp ms-4">storefront</span>
                                    <p class="align-self-center">Market</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                        
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <span class="nav-icon material-symbols-sharp ms-2">account_balance</span>
                            <p class="align-self-center">Bank</p>
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </a>
                        
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="/game?page=Depost" class="nav-link">
                                    <span class="nav-icon material-symbols-sharp ms-4"></span>
                                    <p class="align-self-center">Depost</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="/game?page=Withdrawal" class="nav-link">
                                    <span class="nav-icon material-symbols-sharp ms-4"></span>
                                    <p class="align-self-center">Withdrawal</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="/game?page=Loans" class="nav-link">
                                    <span class="nav-icon material-symbols-sharp ms-4">payments</span>
                                    <p class="align-self-center">Loans</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <li class="nav-header">Dungeon</li>
                    </li>
                    
                    <li class="nav-item">
                        <a href="/game?page=Floor1" class="nav-link">
                            <span class="nav-icon material-symbols-sharp ms-2"></span>
                            <p class="align-self-center">Floor 1</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/game?page=Settings" class="nav-link">
                            <span class="nav-icon material-symbols-sharp ms-2"></span>
                            <p class="align-self-center">Settings</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/game?page=Reset" class="nav-link">
                            <span class="nav-icon material-symbols-sharp ms-2 text-danger"></span>
                            <p class="align-self-center">Reset</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <li class="nav-header">Quests</li>
                    </li>
        
                    <li class="nav-item">
                        <a href="/game?page=Active" class="nav-link">
                            <span class="nav-icon material-symbols-sharp"></span>
                            <p class="align-self-center">Active</p>
                        </a>
                    </li>
            
                    <li class="nav-item">
                        <a href="/game?page=Accepted" class="nav-link">
                            <span class="nav-icon material-symbols-sharp"></span>
                            <p class="align-self-center">Accepted</p>
                        </a>
                    </li>
            
                    <li class="nav-item">
                        <a href="/game?page=Completed" class="nav-link">
                            <span class="nav-icon material-symbols-sharp"></span>
                            <p class="align-self-center">Completed</p>
                        </a>
                    </li>
            
                    <li class="nav-item">
                        <a href="/game?page=Abandoned" class="nav-link">
                        <span class="nav-icon material-symbols-sharp"></span>
                        <p class="align-self-center">Abandoned</p>
                    </a>
                </li>

                <li class="nav-item">
                    <li class="nav-header">Mail</li>
                </li>
            
                <li class="nav-item">
                    <a href="/game?page=Compose" class="nav-link">
                        <span class="nav-icon material-symbols-sharp">forward_to_inbox</span>
                        <p class="align-self-center">Compose</p>
                    </a>
                </li>
            
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="nav-icon material-symbols-sharp">folder_open</span>
                        <p class="align-self-center">Folders</p>
                        <i class="nav-arrow bi bi-chevron-right"></i>
                    </a>

                    <ul class="nav nav-treeview">
                        <li class="nav-items">
                            <a href="/game?page=Inbox" class="nav-link">
                                <span class="nav-icon material-symbols-sharp ms-2">inbox</span>
                                <p class="align-self-center">Inbox</p>
                            </a>
                        </li>
                    
                        <li class="nav-item">
                            <a href="/game?page=Outbox" class="nav-link">
                                <span class="nav-icon material-symbols-sharp ms-2">outbox</span>
                                <p class="align-self-center">Outbox</p>
                            </a>
                        </li>
                    
                        <li class="nav-item">
                            <a href="/game?page=Deleted" class="nav-link">
                                <span class="nav-icon material-symbols-sharp ms-2">cancel_presentation</span>
                                <p class="align-self-center">Deleted</p>
                            </a>
                        </li>
                    
                        <li class="nav-item">
                            <a href="/game?page=Drafts" class="nav-link">
                                <span class="nav-icon material-symbols-sharp ms-2">mark_as_unread</span>
                                <p class="align-self-center">Drafts</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="/game?page=Settings" class="nav-link">
                        <span class="nav-icon material-symbols-sharp">inbox_customize</span>
                        <p class="align-self-center">Settings</p>
                    </a>
                </li>
            
                <li class="nav-item">
                    <li class="nav-header">Account</li>
                </li>
            
                <li class="nav-item">
                    <a href="/game?page=Profile" class="nav-link">
                        <span class="nav-icon material-symbols-sharp"></span>
                        <p class="align-self-center">Profile</p>
                    </a>
                </li>
           
                <li class="nav-item">
                    <a href="/game?page=Awards" class="nav-link">
                        <span class="nav-icon material-symbols-sharp">workspace_premium</span>
                        <p class="align-self-center">Awards</p>
                    </a>
                </li>
            
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="nav-icon material-symbols-sharp">military_tech</span>
                        <p class="align-self-center">Achievements</p>
                        <i class="nav-arrow bi bi-chevron-right"></i>
                    </a>
                </li>
    
                <li class="nav-item">
                    <a href="/game?page=Friends" class="nav-link">
                        <span class="nav-icon material-symbols-sharp">cheer</span>
                        <p class="align-self-center">Friends</p>
                    </a>
                </li>
                    
                <li class="nav-item">
                    <a href="/select" class="nav-link">
                        <span class="nav-icon material-symbols-sharp"></span>
                        <p class="align-self-center">Character Select</p>
                    </a>
                </li>
            </ul>
            <li class="nav-header">SignOut</li>
        </nav>
    </div>
</aside>