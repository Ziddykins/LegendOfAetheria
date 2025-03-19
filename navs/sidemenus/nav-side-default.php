<?php
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
                            <a href="/game?page=sheet" class="nav-link">
                                <span class="nav-icon material-symbols-sharp"><?php echo $char_menu_icon; ?></span>
                                <p>Sheet</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <span class="material-symbols-sharp">deployed_code</span>
                                <p>Inventory</p>
                                <i class="nav-arrow bi bi-chevron-right"></i>
                            </a>

                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="./profile" class="nav-link">
                                        <i class="nav-icon bi bi-dash"></i>
                                        <p>Equipment</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon bi bi-dash"></i>
                                        <p>Items</p>
                                        <i class="nav-arrow bi bi-chevron-right"></i>
                                    </a>

                                    <ul class="nav nav-treeview">
                                        <li class="nav-items">
                                            <li class="nav-item">
                                                <a href="/company" class="nav-link">
                                                    <i class="nav-icon bi bi-dash-lg"></i>
                                                    <p>Quest Items</p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="#" class="nav-link">
                                                    <i class="nav-icon bi bi-dash-lg"></i>
                                                    <p>Consumables</p>
                                                </a>
                                            </li>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a href="./dashboard" class="nav-link">
                                <i class="nav-icon bi <?php echo $char_menu_icon; ?>"></i>
                                <p>Skills</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="./dashboard" class="nav-link">
                                <i class="nav-icon bi <?php echo $char_menu_icon; ?>"></i>
                                <p>Spells</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="./dashboard" class="nav-link">
                                <i class="nav-icon bi <?php echo $char_menu_icon; ?>"></i>
                                <p>Train</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-clipboard-fill"></i>
                                <p>
                                    Alerts
                                    <span class="nav-badge badge text-bg- me-3">0</span>
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>

                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon bi bi-dash"></i>
                                        <p>Notifications</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <li class="nav-header fw-bold">Manage Entities</li>
                        </li>

                        <li class="nav-item">
                            <a href="./accounts" class="nav-link">
                                <i class="nav-icon bi bi-file-person-fill"></i>
                                <p>Accounts</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="./characters" class="nav-link">
                                <i class="nav-icon bi bi-shield-lock-fill"></i>
                                <p>Characters</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-chat-dots-fill"></i>
                                <p>
                                    Messages
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                        

                            <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="./layout/unfixed-sidebar.php" class="nav-link">
                                    <i class="nav-icon bi bi-dash"></i>
                                    <p>Send Message</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="./layout/unfixed-sidebar.php" class="nav-link">
                                    <i class="nav-icon bi bi-dash"></i>
                                    <p>Read Messages</p><span class="badge nav-badge text-bg-primary">0</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <li class="nav-header">BACKUPS</li>
                    </li>

                    <li class="nav-item">
                        <a href="/dashboard" class="nav-link">
                            <i class="nav-icon bi bi-calendar-week-fill"></i>
                            <p>Schedules</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <li class="nav-header">CONFIGURATION</li>
                    </li>

                    <li class="nav-item">
                        <a href="/dashboard" class="nav-link">
                            <i class="nav-icon bi bi-box-fill"></i>
                            <p>Register Box</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <li class="nav-header">DOCUMENTATION</li>
                    </li>

                    <li class="nav-item">
                        <a href="/dashboard" class="nav-link">
                            <i class="nav-icon bi bi-wrench-adjustable"></i>
                            <p>Initial Setup</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <li class="nav-header">ADMINISTRATION</li>
                    </li>

                    <li class="nav-item">
                        <a href="/admini/straitor/users" class="nav-link">
                            <i class="nav-icon bi bi-person-fill"></i>
                            <p>Users</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>