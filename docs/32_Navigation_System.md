# Navigation System

<details>
<summary>Relevant source files</summary>

The following files were used as context for generating this wiki page:

- [css/gfonts.css](css/gfonts.css)
- [js/functions.js](js/functions.js)
- [navs/nav-login.php](navs/nav-login.php)
- [navs/sidemenus/nav-side-default.php](navs/sidemenus/nav-side-default.php)
- [src/Account/Settings.php](src/Account/Settings.php)

</details>



## Purpose and Scope

This document describes the navigation system that allows users to traverse the game interface. It covers the tab-based navigation on the login page, the hierarchical sidebar menu structure used during gameplay, active state management, menu expansion/collapse mechanics, and integration with URL routing. For information about the underlying UI frameworks, see [UI Frameworks](#7.1). For client-side JavaScript interactions, see [Client-Side JavaScript](#7.3).

---

## Overview of Navigation Components

Legend of Aetheria implements two distinct navigation systems depending on the application state:

| Navigation Type | File Location | Context | Features |
|----------------|---------------|---------|----------|
| Login Navigation | `navs/nav-login.php` | Pre-authentication | Tab-based interface with Login, Register, Contact, Status, Admin tabs |
| Sidebar Navigation | `navs/sidemenus/nav-side-default.php` | Post-authentication gameplay | Hierarchical collapsible menu with nested submenus |
| Quick Navigation | `navs/sidemenus/nav-quicknav.php` | During gameplay | Fast-access toolbar (referenced but not provided in files) |

The navigation state persists via the `Settings` class, which stores user preferences such as `colorMode` and `sideBar` type.

**Sources:** [navs/nav-login.php:1-427](), [navs/sidemenus/nav-side-default.php:1-650](), [src/Account/Settings.php:1-76]()

---

## Navigation Architecture

```mermaid
graph TB
    subgraph "Pre-Auth Navigation"
        IndexPHP["index.php<br/>Entry Point"]
        NavLogin["navs/nav-login.php<br/>Tab Navigation"]
        LoginTab["#login-tab-pane"]
        RegisterTab["#register-tab-pane"]
        ContactTab["#contact-tab-pane"]
        StatusTab["#status-tab-pane"]
        AdminTab["#admin-tab-pane"]
    end
    
    subgraph "Post-Auth Navigation"
        GamePHP["game.php<br/>Main Controller"]
        NavSideDefault["navs/sidemenus/nav-side-default.php<br/>Sidebar Component"]
        NavQuicknav["navs/sidemenus/nav-quicknav.php<br/>Quick Access"]
        
        CharacterMenu["Character Section<br/>#character-anchor"]
        FamiliarMenu["Familiar Section<br/>#familiar-anchor"]
        LocationMenu["Location Section<br/>#location-anchor"]
        EconomyMenu["Economy Section<br/>#economy-anchor"]
        MailMenu["Mail Section<br/>#mail-anchor"]
        AccountMenu["Account Section<br/>#account-anchor"]
    end
    
    subgraph "State Management"
        SessionVars["$_SESSION['email']<br/>$_SESSION['character-id']"]
        URLParams["$_GET['page']<br/>$_GET['sub']"]
        Settings["Settings::sideBar<br/>Settings::colorMode"]
    end
    
    IndexPHP --> NavLogin
    NavLogin --> LoginTab
    NavLogin --> RegisterTab
    NavLogin --> ContactTab
    NavLogin --> StatusTab
    NavLogin --> AdminTab
    
    GamePHP --> NavSideDefault
    GamePHP --> NavQuicknav
    
    NavSideDefault --> CharacterMenu
    NavSideDefault --> FamiliarMenu
    NavSideDefault --> LocationMenu
    NavSideDefault --> EconomyMenu
    NavSideDefault --> MailMenu
    NavSideDefault --> AccountMenu
    
    URLParams --> NavSideDefault
    SessionVars --> NavSideDefault
    Settings --> NavSideDefault
```

**Sources:** [navs/nav-login.php:1-31](), [navs/sidemenus/nav-side-default.php:12-13,27-29](), [src/Account/Settings.php:27-48]()

---

## Login Page Navigation

The login page uses Bootstrap 5.3 tabs to organize pre-authentication interfaces into five distinct sections.

### Tab Structure

The navigation is implemented using Bootstrap's `nav-tabs` component with `data-bs-toggle="tab"` attributes for tab switching:

```mermaid
graph LR
    LoginBox["ul#login-box.nav-tabs"]
    
    LoginButton["button#login-tab<br/>data-bs-target=#login-tab-pane"]
    RegisterButton["button#register-tab<br/>data-bs-target=#register-tab-pane"]
    ContactButton["button#contact-tab<br/>data-bs-target=#contact-tab-pane"]
    StatusButton["button#status-tab<br/>data-bs-target=#status-tab-pane"]
    AdminButton["button#status-tab<br/>data-bs-target=#admin-tab-pane"]
    
    LoginContent["div#login-tab-pane.tab-pane<br/>Login form"]
    RegisterContent["div#register-tab-pane.tab-pane<br/>Registration form"]
    ContactContent["div#contact-tab-pane.tab-pane<br/>Contact form"]
    StatusContent["div#status-tab-pane.tab-pane<br/>System status"]
    AdminContent["div#admin-tab-pane.tab-pane<br/>Admin login"]
    
    LoginBox --> LoginButton
    LoginBox --> RegisterButton
    LoginBox --> ContactButton
    LoginBox --> StatusButton
    LoginBox --> AdminButton
    
    LoginButton -.-> LoginContent
    RegisterButton -.-> RegisterContent
    ContactButton -.-> ContactContent
    StatusButton -.-> StatusContent
    AdminButton -.-> AdminContent
```

Each tab button includes:
- An icon from Bootstrap Icons (`bi bi-diamond-fill`, `bi bi-diamond`)
- An `onclick=tgl_active_signup(this)` handler for visual feedback
- ARIA attributes for accessibility (`role="tab"`, `aria-controls`, `aria-selected`)

The active tab has the class `nav-link active`, while inactive tabs have class `nav-link`. Content panes use `tab-pane fade show active` for the active pane and `tab-pane fade` for hidden panes.

**Sources:** [navs/nav-login.php:1-31,35-369]()

---

## Sidebar Navigation Structure

The main game navigation is a hierarchical sidebar implemented in `nav-side-default.php` with nested collapsible menus up to four levels deep.

### Top-Level Menu Structure

```mermaid
graph TB
    Sidebar["aside#sidebar.app-sidebar"]
    BrandLink["div.sidebar-brand<br/>Logo"]
    Quicknav["nav-quicknav.php<br/>Quick access toolbar"]
    SidebarWrapper["div.sidebar-wrapper<br/>Scrollable container"]
    SidebarMenu["ul.nav.sidebar-menu"]
    
    Character["li#character-anchor.nav-item<br/>Character section"]
    Familiar["li#familiar-anchor.nav-item<br/>Familiar section"]
    Location["li#location-anchor.nav-item<br/>Location section"]
    Economy["li#economy-anchor.nav-item<br/>Economy section"]
    Dungeon["li#dungeon-anchor.nav-item<br/>Dungeon section"]
    Quests["li#quests-anchor.nav-item<br/>Quests section"]
    Mail["li#mail-anchor.nav-item<br/>Mail section"]
    Account["li#account-anchor.nav-item<br/>Account section"]
    
    Sidebar --> BrandLink
    Sidebar --> Quicknav
    Sidebar --> SidebarWrapper
    SidebarWrapper --> SidebarMenu
    
    SidebarMenu --> Character
    SidebarMenu --> Familiar
    SidebarMenu --> Location
    SidebarMenu --> Economy
    SidebarMenu --> Dungeon
    SidebarMenu --> Quests
    SidebarMenu --> Mail
    SidebarMenu --> Account
```

Each top-level menu item follows this pattern:
1. An anchor `<li>` with ID format `{section}-anchor` (e.g., `character-anchor`)
2. A clickable `<a class="nav-link">` that toggles expansion
3. An icon from Material Symbols Outlined font
4. A text label
5. A chevron indicator (`bi bi-chevron-right`)
6. A nested `<ul class="nav nav-treeview">` containing submenu items

**Sources:** [navs/sidemenus/nav-side-default.php:32-43,45-132]()

### Character Section Hierarchy

The Character section demonstrates the deepest nesting level (4 levels):

```mermaid
graph TB
    CharAnchor["li#character-anchor"]
    CharList["ul#character-list.nav-treeview"]
    
    Profile["Profile<br/>/game?page=profile&sub=character"]
    Sheet["Sheet<br/>/game?page=sheet&sub=character"]
    InventoryAnchor["li#inventory-anchor<br/>Inventory"]
    Skills["Skills<br/>/game?page=skills&sub=character"]
    Spells["Spells<br/>/game?page=spells&sub=character"]
    Train["Train<br/>/game?page=train&sub=character"]
    
    InventoryList["ul#inventory-list"]
    Equipment["Equipment<br/>/game?page=equipment&sub=inventory"]
    ItemsAnchor["li#items-anchor<br/>Items"]
    
    ItemsList["ul#items-list"]
    QuestItems["Quest Items<br/>/game?page=quest&sub=items"]
    Consumables["Consumables<br/>/game?page=consumables&sub=items"]
    
    CharAnchor --> CharList
    CharList --> Profile
    CharList --> Sheet
    CharList --> InventoryAnchor
    CharList --> Skills
    CharList --> Spells
    CharList --> Train
    
    InventoryAnchor --> InventoryList
    InventoryList --> Equipment
    InventoryList --> ItemsAnchor
    
    ItemsAnchor --> ItemsList
    ItemsList --> QuestItems
    ItemsList --> Consumables
```

**Sources:** [navs/sidemenus/nav-side-default.php:46-131]()

### Mail Section with Dynamic Badges

The Mail section displays unread message counts using the `MailBox::getFolderCount()` method:

```mermaid
graph TB
    MailAnchor["li#mail-anchor"]
    MailList["ul#mail-list"]
    
    Compose["Compose<br/>/game?page=compose&sub=mail"]
    FolderAnchor["li#folder-anchor<br/>Folders"]
    Settings["Settings<br/>/game?page=settings&sub=mail"]
    
    FolderList["ul#folder-list"]
    Inbox["Inbox<br/>span.badge.text-bg-danger<br/>$folders['INBOX']"]
    Outbox["Outbox<br/>span.badge.text-bg-danger<br/>$folders['OUTBOX']"]
    Deleted["Deleted<br/>span.badge.text-bg-danger<br/>$folders['DELETED']"]
    Drafts["Drafts<br/>span.badge.text-bg-danger<br/>$folders['DRAFTS']"]
    
    MailBox["MailBox::getFolderCount()<br/>FolderType enum"]
    
    MailAnchor --> MailList
    MailList --> Compose
    MailList --> FolderAnchor
    MailList --> Settings
    
    FolderAnchor --> FolderList
    FolderList --> Inbox
    FolderList --> Outbox
    FolderList --> Deleted
    FolderList --> Drafts
    
    MailBox -.-> Inbox
    MailBox -.-> Outbox
    MailBox -.-> Deleted
    MailBox -.-> Drafts
```

The folder counts are computed server-side at page load:

| Folder Type | Enum Value | Badge Class | Display Condition |
|------------|-----------|-------------|-------------------|
| INBOX | `FolderType::INBOX` | `text-bg-danger` | Count > 0 |
| OUTBOX | `FolderType::OUTBOX` | `text-bg-danger` | Count > 0 |
| DELETED | `FolderType::DELETED` | `text-bg-danger` | Count > 0 |
| DRAFTS | `FolderType::DRAFTS` | `text-bg-danger` | Count > 0 |
| (empty) | N/A | `text-bg-secondary` | Count = 0 |

**Sources:** [navs/sidemenus/nav-side-default.php:14-23,358-439]()

---

## Active State Management

The sidebar uses URL parameters `$_GET['page']` and `$_GET['sub']` to determine which menu items should be highlighted and expanded.

### Active State Detection

```mermaid
graph LR
    URLParams["$_GET['page']<br/>$_GET['sub']"]
    CurrentPage["$currentPage variable"]
    CurrentSub["$currentSub variable"]
    
    NavLinks["a.nav-link elements"]
    ActiveClass["class='active'"]
    MenuOpen["parent li.menu-open"]
    
    URLParams --> CurrentPage
    URLParams --> CurrentSub
    
    CurrentPage --> NavLinks
    CurrentSub --> NavLinks
    
    NavLinks --> ActiveClass
    ActiveClass --> MenuOpen
```

Each navigation link includes a PHP conditional that adds the `active` class when both `page` and `sub` parameters match:

```php
class="nav-link ... <?php echo ($currentPage === 'profile' && $currentSub === 'character') ? 'active' : ''; ?>"
```

The active link styling is defined in CSS:
- Bold font weight
- Color: `rgba(200, 255, 200, .7)`

**Sources:** [navs/sidemenus/nav-side-default.php:27-29,57-60,643-646]()

### Automatic Menu Expansion

A JavaScript `DOMContentLoaded` event handler automatically expands parent menus when a page loads:

```mermaid
graph TB
    DOMLoad["DOMContentLoaded event"]
    FindActive["document.querySelector('.nav-link.active')"]
    TraverseParents["let parent = activeLink.closest('.nav-item.menu-open')"]
    AddClass["parent.classList.add('menu-open')"]
    ScrollIntoView["activeLink.scrollIntoView()"]
    
    DOMLoad --> FindActive
    FindActive --> TraverseParents
    TraverseParents --> AddClass
    AddClass --> TraverseParents
    AddClass --> ScrollIntoView
```

This algorithm:
1. Finds the active link (`.nav-link.active`)
2. Traverses up the DOM to find parent `.nav-item` elements
3. Adds `menu-open` class to each parent
4. Scrolls the active link into view with smooth behavior

**Sources:** [navs/sidemenus/nav-side-default.php:605-618]()

---

## Menu Expansion and Collapse

The navigation system supports both manual toggling and bulk operations for menu expansion.

### CSS-Based Toggle Mechanism

Menu expansion is controlled purely through CSS classes:

| Class | Effect | Applied To |
|-------|--------|-----------|
| `menu-open` | Displays child menu | Parent `<li class="nav-item">` |
| (no class) | Hides child menu | Parent `<li class="nav-item">` |

The CSS rule enforces visibility:

```css
.menu-open > .nav-treeview {
    display: block !important;
}
```

**Sources:** [navs/sidemenus/nav-side-default.php:647-649]()

### Bulk Menu Operations

The `functions.js` file provides two utility functions for expanding/collapsing all menus:

```mermaid
graph TB
    FunctionsJS["js/functions.js"]
    
    CollapseAll["collapse_all()<br/>Line 63-71"]
    ExpandAll["expand_all()<br/>Line 73-78"]
    
    QueryAnchors["querySelectorAll('li[id$=anchor],ul[id$=list]')"]
    RemoveOpen["classList.remove('menu-open')"]
    AddOpen["classList.add('menu-open')"]
    HideDisplay["style.display = 'none'"]
    ShowDisplay["style.display = ''"]
    
    FunctionsJS --> CollapseAll
    FunctionsJS --> ExpandAll
    
    CollapseAll --> QueryAnchors
    QueryAnchors --> RemoveOpen
    QueryAnchors --> HideDisplay
    
    ExpandAll --> QueryAnchors
    QueryAnchors --> AddOpen
    QueryAnchors --> ShowDisplay
```

Both functions target elements with ID patterns:
- `li[id$="anchor"]` - Parent menu items (e.g., `character-anchor`, `mail-anchor`)
- `ul[id$="list"]` - Child menu containers (e.g., `character-list`, `folder-list`)

**Sources:** [js/functions.js:63-78]()

---

## Sidebar Collapse Behavior

The sidebar implements a collapsible feature with a visual indicator when collapsed.

### Collapse Detection and Indicator

```mermaid
graph TB
    MutationObserver["MutationObserver<br/>Watches document.body classes"]
    BodyClass["document.body.classList"]
    SidebarCollapse["contains('sidebar-collapse')"]
    Sliver["div#sidebar-sliver<br/>Vertical indicator bar"]
    
    SliverShow["style.display = 'flex'"]
    SliverHide["style.display = 'none'"]
    SliverClick["onclick: remove 'sidebar-collapse'<br/>add 'sidebar-open'"]
    
    MutationObserver --> BodyClass
    BodyClass --> SidebarCollapse
    
    SidebarCollapse -->|true| SliverShow
    SidebarCollapse -->|false| SliverHide
    
    SliverShow --> Sliver
    Sliver --> SliverClick
```

The sliver is a 10px-wide clickable bar positioned at the left edge of the viewport:
- Position: `fixed; left: 0; top: 0`
- Dimensions: `width: 10px; height: 100vh`
- Background: `rgba(5, 57, 28, 0.21)`
- Icon: `<i class="bi bi-chevron-right"></i>`
- Z-index: `999`

When clicked, it removes the `sidebar-collapse` class from `document.body`, restoring the sidebar.

**Sources:** [navs/sidemenus/nav-side-default.php:602,604-639]()

---

## URL-Based Routing Integration

Navigation links use a consistent URL pattern to maintain state and trigger page-specific content loading.

### URL Parameter Structure

All navigation links follow this format:

```
/game?page={page_name}&sub={submenu_name}
```

| Component | Purpose | Example Values |
|-----------|---------|---------------|
| `page` | Primary content identifier | `profile`, `sheet`, `hunt`, `compose` |
| `sub` | Submenu/context identifier | `character`, `location`, `mail`, `friends` |

Example navigation links:

```mermaid
graph LR
    CharSheet["/game?page=sheet&sub=character"]
    Hunt["/game?page=hunt&sub=location"]
    Inbox["/game?page=inbox&sub=folders"]
    BankAccount["/game?page=account&sub=bank"]
    
    GamePHP["game.php"]
    IncludePage["include 'pages/game-{page}.php'"]
    
    CharSheet --> GamePHP
    Hunt --> GamePHP
    Inbox --> GamePHP
    BankAccount --> GamePHP
    
    GamePHP --> IncludePage
```

The `game.php` controller extracts these parameters and dynamically includes the corresponding page file from the `pages/` directory.

**Sources:** [navs/sidemenus/nav-side-default.php:57,64,79,94,101,111,118,125]()

### Character Select Link

The sidebar includes a special link to the character selection page that exits the current game session:

```
<a href="/select" class="nav-link">
    <i class="material-symbols-outlined">group</i>
    <p>Character Select</p>
</a>
```

This navigates to `select.php` rather than `game.php`, allowing users to switch between their three character slots.

**Sources:** [navs/sidemenus/nav-side-default.php:511-515]()

---

## Bottom Menu and User Avatar

The sidebar footer displays the current character's avatar and provides access to account-level actions.

### Bottom Menu Structure

```mermaid
graph TB
    BottomMenu["div#bottom-menu<br/>position: absolute; bottom: 0"]
    AvatarLink["a href=#offcanvas-summary<br/>data-bs-toggle=offcanvas"]
    AvatarImg["img src=img/avatars/{character.avatar}<br/>width: 50px, rounded-circle"]
    DropdownToggle["a.dropdown-toggle<br/>data-bs-toggle=dropdown"]
    DropdownMenu["ul.dropdown-menu"]
    
    ProfileItem["Profile"]
    FriendsItem["Friends<br/>with badge"]
    MailItem["Mail<br/>with unread count"]
    SettingsItem["Settings"]
    AdminItem["Administrator<br/>if privileges > ADMIN"]
    CharactersItem["Characters<br/>link to /select"]
    SignOutItem["Sign out<br/>link to /logout"]
    
    BottomMenu --> AvatarLink
    BottomMenu --> DropdownToggle
    
    AvatarLink --> AvatarImg
    DropdownToggle --> DropdownMenu
    
    DropdownMenu --> ProfileItem
    DropdownMenu --> FriendsItem
    DropdownMenu --> MailItem
    DropdownMenu --> SettingsItem
    DropdownMenu --> AdminItem
    DropdownMenu --> CharactersItem
    DropdownMenu --> SignOutItem
```

The avatar image is loaded from the character's `avatar` property:

```php
<img src="img/avatars/<?php echo $character->get_avatar(); ?>" 
     alt="avatar" width="50" height="50" class="rounded-circle" />
```

The dropdown menu items include:
- **Friends**: Displays a badge with `get_friend_counts(FriendStatus::REQUEST_RECV)`
- **Mail**: Shows unread count via `check_mail('unread')`
- **Administrator**: Only visible if `$account->get_privileges() > Privileges::ADMINISTRATOR->value`

**Sources:** [navs/sidemenus/nav-side-default.php:525-596]()

---

## Settings Persistence

User navigation preferences are stored in the `Settings` class and persisted to the database.

### Settings Properties

```mermaid
graph LR
    SettingsClass["Settings class<br/>src/Account/Settings.php"]
    PropSuite["PropSuite trait<br/>ORM layer"]
    
    IdProp["private ?int $id<br/>Account ID"]
    ColorMode["private string $colorMode<br/>'light' or 'dark'"]
    SideBar["private ?SidebarType $sideBar<br/>SidebarType enum"]
    
    Database["Database<br/>settings table"]
    
    SettingsClass --> PropSuite
    PropSuite --> Database
    
    SettingsClass --> IdProp
    SettingsClass --> ColorMode
    SettingsClass --> SideBar
```

The `Settings` constructor initializes defaults:

| Property | Default Value | Type |
|----------|---------------|------|
| `id` | `$accountID` parameter | `int` |
| `colorMode` | `'dark'` | `string` |
| `sideBar` | `SidebarType::LTE_DEFAULT` | `SidebarType` enum |

Property access uses the PropSuite trait's magic `__call()` method, which handles:
- `get_{property}()` - Retrieve property value
- `set_{property}($value)` - Update property value and sync to database
- `add_{property}($amount)` - Mathematical operations
- `propDump()` - Export all properties
- `propRestore()` - Import properties

**Sources:** [src/Account/Settings.php:27-76]()

---

## Material Symbols Icons

The sidebar navigation uses Material Symbols Outlined font for icons rather than Bootstrap Icons.

### Icon Font Configuration

```mermaid
graph TB
    GFontsCSS["css/gfonts.css"]
    FontFace["@font-face<br/>Material Symbols Outlined"]
    FontFile["url(/css/fonts/material-symbols-outline.woff2)"]
    IconClass["class='material-symbols-outlined'"]
    
    IconExamples["Icon Usage Examples"]
    Person["person - Character Profile"]
    Raven["raven - Familiar"]
    Public["public - Location"]
    Monitoring["monitoring - Economy"]
    AlternateEmail["alternate_email - Mail"]
    
    GFontsCSS --> FontFace
    FontFace --> FontFile
    FontFace --> IconClass
    
    IconClass --> IconExamples
    IconExamples --> Person
    IconExamples --> Raven
    IconExamples --> Public
    IconExamples --> Monitoring
    IconExamples --> AlternateEmail
```

The Material Symbols class definition:

```css
.material-symbols-outlined {
  font-family: 'Material Symbols Outlined';
  font-weight: 100 700 !important;
  font-style: normal;
  font-size: 24px;
  line-height: 1;
  ...
}
```

Each navigation item uses a semantic icon name:
- `sentiment_satisfied` / `skull` - Character health indicator
- `inventory_2` - Inventory
- `egg` - Hatchery
- `cruelty_free` - Hunt
- `account_balance` - Bank

**Sources:** [css/gfonts.css:141-162](), [navs/sidemenus/nav-side-default.php:25,49,58,72,136,151,174,212,265,290,322,360]()