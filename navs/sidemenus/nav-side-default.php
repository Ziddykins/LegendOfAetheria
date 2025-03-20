<span?php
$char_menu_icon = $character->stats->get_hp() > 0 ? 'sentiment_satisfied' : 'sentiment_sad';
?>

<aside class="app-sidebar shadow" data-bs-theme="dark">
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
                        <a href="/game?page=Sheet" class="nav-link">
                            <span class="nav-icon material-symbols-sharp">mist</span>
                            <p>Sheet</p>
                        </a>
                    </li>
                
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <span class="nav-icon material-symbols-sharp"></span>
                            <p>Inventory</p>
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </a>
                        
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="/game?page=Equipment" class="nav-link">
                                    <span class="nav-icon material-symbols-sharp">line_start_circle</span>
                                    <span class="nav-icon material-symbols-sharp">colorize</span>
                                    <p>Equipment</p>
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <span class="nav-icon material-symbols-sharp">line_start_circle</span>
                                    <span class="nav-icon material-symbols-sharp">handyman</span>
                                    <p>Items</p>
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </a>
                            

                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="/game?page=Questitems" class="nav-link">
                                            <span class="nav-icon material-symbols-sharp">line_start_arrow</span>
                                            <span class="nav-icon material-symbols-sharp">deployed_code_alert</span>
                                            <p>Quest Items</p>
                                        </a>
                                    </li>
                        
                                    <li class="nav-item">
                                        <a href="/game?page=Consumables" class="nav-link">
                                            <span class="nav-icon material-symbols-sharp">line_start_arrow</span>
                                            <span class="nav-icon material-symbols-sharp">grocery</span>
                                            <p>Consumables</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>

                        <li class="nav-item">
                            <a href="/game?page=Skills" class="nav-link">
                                <span class="nav-icon material-symbols-sharp">hotel_class</span>
                                <p>Skills</p>
                            </a>
                        </li>
                       
                        <li class="nav-item">
                            <a href="/game?page=Spells" class="nav-link">
                                <span class="nav-icon material-symbols-sharp"></span>
                                <p>Spells</p>
                            </a>
                        </li>
                       
                        <li class="nav-item">
                            <a href="/game?page=Train" class="nav-link">
                                <span class="nav-icon material-symbols-sharp"></span>
                                <p>Train</p>
                            </a>
                        </li>
                    </li>
                    
                    <li class="nav-item">
                        <li class="nav-header">Familiar</li>
                    </li>
                
                    <li class="nav-item">
                        <a href="/game?page=Manage" class="nav-link">
                            <span class="nav-icon material-symbols-sharp"></span>
                            <p>Manage</p>
                        </a>
                    </li>
                
                    <li class="nav-item">
                        <a href="/game?page=Hatchery" class="nav-link">
                            <span class="nav-icon material-symbols-sharp"></span>
                            <p>Hatchery</p>
                        </a>
                    </li>
                
                    <li class="nav-item">
                        <a href="/game?page=Equipment" class="nav-link">
                            <span class="nav-icon material-symbols-sharp"></span>
                            <p>Equipment</p>
                        </a>
                    </li>
                
                    <li class="nav-item">
                        <li class="nav-header">Location</li>
                    </li>
                
                    <li class="nav-item">
                        <a href="/game?page=Hunt" class="nav-link">
                            <span class="nav-icon material-symbols-sharp">cruelty_free</span>
                            <p>Hunt</p>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="/game?page=Map" class="nav-link">
                            <span class="nav-icon material-symbols-sharp"></span>
                            <p>Map</p>
                        </a>
                    </li>
                
                    <li class="nav-item">
                        <a href="/game?page=Explore" class="nav-link">
                            <span class="nav-icon material-symbols-sharp">forest</span>
                            <p>Explore</p>
                        </a>
                    </li>
                
                    <li class="nav-item">
                        <a href="/game?page=Zone" class="nav-link">
                            <span class="nav-icon material-symbols-sharp">rocket</span>
                            <p>Zone</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/game?page=Rest" class="nav-link">
                            <span class="nav-icon material-symbols-sharp">offline_bolt</span>
                            <p>Rest</p>
                        </a>
                    </li>
                
                    <li class="nav-item">
                        <li class="nav-header">Economy</li>
                    </li>

                    <li class="nav-item">
                        <a href="/game?page=Equipment" class="nav-link">
                            <span class="nav-icon material-symbols-sharp"></span>
                            <p>Equipment</p>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="/game?page=Items" class="nav-link">
                            <span class="nav-icon material-symbols-sharp"></span>
                            <p>Items</p>
                        </a>
                    </li>
                
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <span class="nav-icon material-symbols-sharp">loyalty</span>
                            <p>Blackmarket</p>
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </a>
                    
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="/game?page=Buy" class="nav-link">
                                    <span class="nav-icon material-symbols-sharp">paid</span>
                                    <p>Buy</p>
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a href="/game?page=Sell" class="nav-link">
                                    <span class="nav-icon material-symbols-sharp">attach_money</span>
                                    <p>Sell</p>
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a href="/game?page=Market" class="nav-link">
                                    <span class="nav-icon material-symbols-sharp">storefront</span>
                                    <p>Market</p>
                                </a>
                            </li>
                        
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <span class="nav-icon material-symbols-sharp">account_balance</span>
                                    <p>Bank</p>
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </a>
                                
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="/game?page=Depost" class="nav-link">
                                            <span class="nav-icon material-symbols-sharp"></span>
                                            <p>Depost</p>
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="/game?page=Withdrawal" class="nav-link">
                                            <span class="nav-icon material-symbols-sharp"></span>
                                            <p>Withdrawal</p>
                                        </a>
                                    </li>
    
                                    <li class="nav-item">
                                        <a href="/game?page=Loans" class="nav-link">
                                            <span class="nav-icon material-symbols-sharp">payments</span>
                                            <p>Loans</p>
                                        </a>
                                    </li>
                                </ul>
                        
                                <li class="nav-item">
                                        <li class="nav-header">Dungeon</li>
                                    </li>
                            <li class="nav-item">
                            <a href="/game?page=Floor1" class="nav-link">
                                <span class="nav-icon material-symbols-sharp"></span>
                                <p>Floor1</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/game?page=Settings" class="nav-link">
                                <span class="nav-icon material-symbols-sharp"></span>
                                <p>Settings</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/game?page=Reset" class="nav-link">
                                <span class="nav-icon material-symbols-sharp"></span>
                                <p>Reset</p>
                            </a>
                        </li>
                        <li class="nav-item">
                        <li class="nav-header">Quests</li>
                </li>
                <li class="nav-item">
                    <a href="/game?page=Active" class="nav-link">
                        <span class="nav-icon material-symbols-sharp"></span>
                        <p>Active</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/game?page=Accepted" class="nav-link">
                        <span class="nav-icon material-symbols-sharp"></span>
                        <p>Accepted</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/game?page=Completed" class="nav-link">
                        <span class="nav-icon material-symbols-sharp"></span>
                        <p>Completed</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/game?page=Abandoned" class="nav-link">
                        <span class="nav-icon material-symbols-sharp"></span>
                        <p>Abandoned</p>
                    </a>
                </li>
                <li class="nav-item">
                <li class="nav-header">Mail</li>
                </li>
                <li class="nav-item">
                    <a href="/game?page=Compose" class="nav-link">
                        <span class="nav-icon material-symbols-sharp"></span>
                        <p>Compose</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/game?page=Folders" class="nav-link">
                        <span class="nav-icon material-symbols-sharp"></span>
                        <p>Folders</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/game?page=Inbox" class="nav-link">
                        <span class="nav-icon material-symbols-sharp">inbox</span>
                        <p>Inbox</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/game?page=Outbox" class="nav-link">
                        <span class="nav-icon material-symbols-sharp">outbox</span>
                        <p>Outbox</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/game?page=Deleted" class="nav-link">
                        <span class="nav-icon material-symbols-sharp">cancel_presentation</span>
                        <p>Deleted</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/game?page=Drafts" class="nav-link">
                        <span class="nav-icon material-symbols-sharp">mark_as_unread</span>
                        <p>Drafts</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/game?page=Settings" class="nav-link">
                        <span class="nav-icon material-symbols-sharp">inbox_customize</span>
                        <p>Settings</p>
                    </a>
                </li>
                <li class="nav-item">
                <li class="nav-header">Account</li>
                </li>
                <li class="nav-item">
                    <a href="/game?page=Profile" class="nav-link">
                        <span class="nav-icon material-symbols-sharp"></span>
                        <p>Profile</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/game?page=Settings" class="nav-link">
                        <span class="nav-icon material-symbols-sharp"></span>
                        <p>Settings</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/game?page=Awards" class="nav-link">
                        <span class="nav-icon material-symbols-sharp">workspace_premium</span>
                        <p>Awards</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="nav-icon material-symbols-sharp">military_tech</span>
                        <p>Achievements</p>
                        <i class="nav-arrow bi bi-chevron-right"></i>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/game?page=Friends" class="nav-link">
                                <span class="nav-icon material-symbols-sharp">cheer</span>
                                <p>Friends</p>
                            </a>
                        </li>
                        <li class="nav-item">
                        <li class="nav-header">CharacterSelect</li>
                </li>
                <li class="nav-item">
                <li class="nav-header">SignOut</li>
                </li>

        </nav>
    </div>
</aside>