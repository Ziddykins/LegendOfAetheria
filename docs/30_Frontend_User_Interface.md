# Frontend & User Interface

<details>
<summary>Relevant source files</summary>

The following files were used as context for generating this wiki page:

- [css/gfonts.css](css/gfonts.css)
- [html/headers.html](html/headers.html)
- [js/functions.js](js/functions.js)
- [navs/nav-login.php](navs/nav-login.php)
- [navs/sidemenus/nav-side-default.php](navs/sidemenus/nav-side-default.php)
- [src/Account/Settings.php](src/Account/Settings.php)

</details>



This document describes the frontend architecture of Legend of Aetheria, including the asset loading pipeline, UI frameworks, navigation structure, client-side JavaScript functionality, and integration patterns between the browser and PHP backend. 

The frontend uses Bootstrap 5.3 as the base CSS framework, enhanced with AdminLTE for admin-style layouts, RPGUI for retro game aesthetics, and Material Symbols for iconography. jQuery 3.7.1 provides DOM manipulation, while custom JavaScript modules handle game-specific interactions. All assets are loaded through a centralized header/footer template system that ensures consistent resource inclusion across pages.

For details on individual frameworks, see [UI Frameworks](#7.1). For navigation implementation, see [Navigation System](#7.2). For JavaScript modules, see [Client-Side JavaScript](#7.3).

---

## Architecture Overview

The frontend architecture follows a layered template system where PHP controllers include standardized header and footer templates that inject CSS and JavaScript resources. Dynamic content is rendered server-side by PHP, then enhanced with client-side JavaScript for interactivity. Session state is bridged from PHP to JavaScript through a global `loa` object, enabling seamless client-server communication.

### Asset Loading Pipeline

All pages include [html/headers.html:1-65]() at the top and a corresponding footers template at the bottom. The headers file loads CSS frameworks in dependency order: Bootstrap first (base framework), then AdminLTE (admin theme), then RPGUI (game theme), then custom styles in `loa.css`. JavaScript libraries follow a similar pattern: Bootstrap and jQuery first, then custom modules.

```mermaid
graph TB
    subgraph "PHP Controller Layer"
        GamePHP["game.php"]
        IndexPHP["index.php"]
        SelectPHP["select.php"]
    end
    
    subgraph "Template System"
        Headers["headers.html<br/>CSS + Meta Tags"]
        Footers["footers.html<br/>JavaScript Includes"]
        PageContent["Dynamic PHP Page<br/>(pages/game-*.php)"]
    end
    
    subgraph "CSS Layer"
        Bootstrap["bootstrap.min.css<br/>Base Framework"]
        BootstrapIcons["bootstrap-icons.min.css"]
        AdminLTE["adminlte.min.css<br/>Admin Theme"]
        RPGUICSS["rpgui.min.css<br/>Game Theme"]
        GFonts["gfonts.css<br/>Font Definitions"]
        CustomCSS["loa.css<br/>Custom Styles"]
    end
    
    subgraph "JavaScript Layer"
        jQuery["jquery-3.7.1.min.js<br/>DOM Manipulation"]
        BootstrapJS["bootstrap.bundle.min.js<br/>Components"]
        RPGUIJS["rpgui.min.js<br/>Game UI"]
        CustomJS["functions.js, battle.js,<br/>chat.js, toasts.js"]
    end
    
    subgraph "Font Resources"
        MaterialSymbols["Material Symbols<br/>Icon Font"]
        VT323["VT323<br/>Retro Monospace"]
        PressStart["Press Start 2P<br/>8-bit Style"]
    end
    
    GamePHP --> Headers
    IndexPHP --> Headers
    SelectPHP --> Headers
    
    Headers --> Bootstrap
    Headers --> BootstrapIcons
    Headers --> AdminLTE
    Headers --> RPGUICSS
    Headers --> GFonts
    Headers --> CustomCSS
    
    Headers --> PageContent
    PageContent --> Footers
    
    Footers --> jQuery
    Footers --> BootstrapJS
    Footers --> RPGUIJS
    Footers --> CustomJS
    
    GFonts --> MaterialSymbols
    GFonts --> VT323
    GFonts --> PressStart
```

**Sources:** [html/headers.html:1-65](), Diagram 6 from high-level architecture

The CSS loading order is critical: Bootstrap provides base utilities, AdminLTE adds admin panel styling, RPGUI overlays retro game aesthetics, and `loa.css` provides final overrides. This layering allows game-themed components to coexist with modern form controls.

| Resource | Purpose | Load Order |
|----------|---------|------------|
| `bootstrap.min.css` | Base CSS framework, grid system, utilities | 1 |
| `bootstrap-icons.min.css` | Icon library for UI elements | 2 |
| `gfonts.css` | Font-face definitions for VT323, Press Start 2P, Material Symbols | 3 |
| `loa.css` | Custom game-specific styles and overrides | 4 |
| `adminlte.min.css` | Admin panel theme for authenticated pages | 5 |
| `rpgui.min.css` | Retro RPG UI styling (borders, buttons, containers) | 6 |
| `overlayscrollbars.min.css` | Custom scrollbar styling | 7 |

**Sources:** [html/headers.html:21-30]()

### Session State Bridge

PHP session data is exposed to JavaScript through a global `loa` object defined in [html/headers.html:41-65](). This object contains user email, account ID, character ID, CSRF token, and session ID. Client-side scripts reference these values for AJAX requests and dynamic UI updates.

```mermaid
graph LR
    subgraph "PHP Session"
        PHPSession["$_SESSION array<br/>logged-in, email,<br/>account-id, character-id,<br/>name, csrf-token"]
    end
    
    subgraph "HTML Template"
        Headers["headers.html<br/>lines 41-65"]
        ScriptTag["&lt;script&gt; block<br/>defines loa object"]
    end
    
    subgraph "JavaScript Runtime"
        LoaObject["loa = {<br/>u_email,<br/>u_aid,<br/>u_csrf,<br/>u_sid,<br/>u_cid,<br/>u_name,<br/>chat_pos,<br/>chat_history<br/>}"]
        CustomJS["Custom JS Modules<br/>Access loa.u_email, etc."]
    end
    
    subgraph "Security Layer"
        CSRFMeta["&lt;meta name='csrf-token'&gt;<br/>line 46"]
    end
    
    PHPSession --> Headers
    Headers --> ScriptTag
    Headers --> CSRFMeta
    ScriptTag --> LoaObject
    LoaObject --> CustomJS
    CSRFMeta --> CustomJS
```

**Sources:** [html/headers.html:41-65]()

The CSRF token is included both as a meta tag for DOM access and in the `loa` object for JavaScript access. This dual inclusion supports both jQuery AJAX patterns and direct fetch() API calls.

---

## UI Framework Integration

Legend of Aetheria combines three distinct UI frameworks, each serving a specific purpose:

1. **Bootstrap 5.3** - Provides the foundational grid system, form controls, navigation components, and utility classes
2. **AdminLTE** - Supplies sidebar navigation structure, card components, and admin panel aesthetics for authenticated pages
3. **RPGUI** - Adds retro RPG styling with medieval borders, textured buttons, and game-themed containers

### Framework Layering Strategy

The frameworks are layered such that RPGUI styles override AdminLTE, which overrides Bootstrap base styles. Custom `loa.css` provides the final layer of overrides for game-specific requirements.

```mermaid
graph TB
    subgraph "CSS Cascade Layers"
        Base["Bootstrap 5.3<br/>.container, .row, .col-*<br/>.btn, .form-control, .nav"]
        Admin["AdminLTE<br/>.app-sidebar, .nav-treeview<br/>.card, .dropdown-menu"]
        Game["RPGUI<br/>.rpgui-container, .rpgui-button<br/>.rpgui-border, .rpgui-window"]
        Custom["loa.css<br/>Game-specific overrides<br/>.main-font, theme adjustments"]
    end
    
    Base --> Admin
    Admin --> Game
    Game --> Custom
```

**Sources:** [html/headers.html:21-30](), [css/gfonts.css:1-225]()

### Component Mapping

Different page sections use different framework combinations:

| Page Section | Primary Framework | Secondary Framework | Purpose |
|--------------|------------------|---------------------|---------|
| Login/Registration | Bootstrap + RPGUI | None | Clean forms with retro styling |
| Sidebar Navigation | AdminLTE | Bootstrap | Collapsible tree menu structure |
| Game Content Area | Bootstrap + RPGUI | None | Responsive grid with game aesthetics |
| Combat Interface | RPGUI | Bootstrap | Full retro game experience |
| Admin Panel | AdminLTE | Bootstrap | Professional admin interface |

**Sources:** [navs/nav-login.php:1-427](), [navs/sidemenus/nav-side-default.php:1-650]()

---

## Navigation System Architecture

The navigation system uses AdminLTE's tree-view sidebar with Bootstrap's collapse functionality. Menu items are organized hierarchically with dynamic active state management and badge notifications.

### Sidebar Structure

The sidebar navigation is defined in [navs/sidemenus/nav-side-default.php:32-601]() and consists of a fixed-position sidebar with scrollable content area. The structure uses nested `<ul>` and `<li>` elements with specific classes for tree-view behavior:

- `nav-item` - Individual menu items
- `menu-open` - Expanded parent items
- `nav-link` - Clickable link elements
- `nav-treeview` - Nested submenu containers
- `active` - Currently selected page

```mermaid
graph TB
    Sidebar["#sidebar.app-sidebar<br/>Fixed position container"]
    Brand["Logo Area<br/>img/logos/logo-banner-no-bg.webp"]
    QuickNav["Quick Navigation<br/>navs/sidemenus/nav-quicknav.php"]
    MenuWrapper["Sidebar Wrapper<br/>Scrollable content"]
    BottomMenu["Bottom Menu<br/>Avatar + Account dropdown"]
    
    Sidebar --> Brand
    Sidebar --> QuickNav
    Sidebar --> MenuWrapper
    Sidebar --> BottomMenu
    
    MenuWrapper --> CharacterMenu["Character Menu<br/>#character-anchor"]
    MenuWrapper --> FamiliarMenu["Familiar Menu<br/>#familiar-anchor"]
    MenuWrapper --> LocationMenu["Location Menu<br/>#location-anchor"]
    MenuWrapper --> EconomyMenu["Economy Menu<br/>#economy-anchor"]
    MenuWrapper --> MailMenu["Mail Menu<br/>#mail-anchor"]
    MenuWrapper --> AccountMenu["Account Menu<br/>#account-anchor"]
    
    CharacterMenu --> ProfileLink["Profile<br/>?page=profile&sub=character"]
    CharacterMenu --> SheetLink["Sheet<br/>?page=sheet&sub=character"]
    CharacterMenu --> InventorySubmenu["Inventory<br/>#inventory-anchor"]
    
    InventorySubmenu --> EquipmentLink["Equipment<br/>?page=equipment&sub=inventory"]
    InventorySubmenu --> ItemsSubmenu["Items<br/>#items-anchor"]
    
    MailMenu --> ComposeLink["Compose<br/>?page=compose&sub=mail"]
    MailMenu --> FoldersSubmenu["Folders<br/>#folder-anchor"]
    
    FoldersSubmenu --> InboxLink["Inbox<br/>Badge: folders['INBOX']"]
    FoldersSubmenu --> OutboxLink["Outbox<br/>Badge: folders['OUTBOX']"]
```

**Sources:** [navs/sidemenus/nav-side-default.php:32-601]()

### Active State Management

Active menu items are determined by comparing URL parameters `$_GET['page']` and `$_GET['sub']` against each link's target. The active class is applied server-side during PHP rendering:

```php
// Example from nav-side-default.php line 57
<?php echo ($currentPage === 'profile' && $currentSub === 'character') ? 'active' : ''; ?>
```

Client-side JavaScript in [navs/sidemenus/nav-side-default.php:604-640]() scrolls the active item into view on page load and manages the sidebar collapse state using a `MutationObserver` to show/hide a sidebar sliver when collapsed.

**Sources:** [navs/sidemenus/nav-side-default.php:27-30](), [navs/sidemenus/nav-side-default.php:604-640]()

### Badge Notifications

Badge counters display unread counts for mail folders and friend requests. These are calculated server-side using `MailBox::getFolderCount()` and stored in the `$folders` array:

```mermaid
graph LR
    subgraph "PHP Processing"
        LoadAccount["new Account($_SESSION['email'])"]
        LoadCharacter["new Character(account_id, character_id)"]
        CountInbox["MailBox::getFolderCount(INBOX)"]
        CountOutbox["MailBox::getFolderCount(OUTBOX)"]
        CountDeleted["MailBox::getFolderCount(DELETED)"]
        CountDrafts["MailBox::getFolderCount(DRAFTS)"]
    end
    
    subgraph "Badge Rendering"
        InboxBadge["span.nav-badge.text-bg-danger<br/>or text-bg-secondary"]
        OutboxBadge["span.nav-badge"]
        DeletedBadge["span.nav-badge"]
        DraftsBadge["span.nav-badge"]
    end
    
    LoadAccount --> LoadCharacter
    LoadCharacter --> CountInbox
    LoadCharacter --> CountOutbox
    LoadCharacter --> CountDeleted
    LoadCharacter --> CountDrafts
    
    CountInbox --> InboxBadge
    CountOutbox --> OutboxBadge
    CountDeleted --> DeletedBadge
    CountDrafts --> DraftsBadge
```

**Sources:** [navs/sidemenus/nav-side-default.php:12-23](), [navs/sidemenus/nav-side-default.php:386-427]()

Badges use `text-bg-danger` for non-zero counts and `text-bg-secondary` for zero counts, providing visual distinction between active and empty folders.

---

## Form Handling and Validation

Forms use a combination of HTML5 validation attributes (`required`), Bootstrap form classes (`.form-control`, `.form-floating`), and custom JavaScript validation functions. The registration form demonstrates all validation patterns.

### Registration Form Validation

The registration form in [navs/nav-login.php:69-276]() implements multi-stage validation:

1. **HTML5 Validation** - `required` attributes on inputs
2. **Password Confirmation** - JavaScript comparison before submit
3. **Attribute Point Allocation** - Custom `stat_adjust()` function
4. **Race/Avatar Selection** - Ensures dropdowns are not at default index
5. **Toast Notifications** - Visual feedback using `gen_toast()`

```mermaid
graph TB
    subgraph "Form Submit Event"
        SubmitButton["#register-submit click"]
        CopyStats["Copy stat values to hidden inputs<br/>lines 243-246"]
        ValidatePasswords["Compare password fields<br/>lines 247-254"]
        ValidateAP["Check remaining AP == 0<br/>lines 256-260"]
        ValidateRace["Check race selected<br/>lines 262-266"]
        ValidateAvatar["Check avatar selected<br/>lines 268-272"]
    end
    
    subgraph "Validation Outcomes"
        PreventDefault["e.preventDefault()<br/>e.stopPropagation()"]
        GenToast["gen_toast(id, type, icon, title, msg)"]
        AllowSubmit["Form submits to /"]
    end
    
    SubmitButton --> CopyStats
    CopyStats --> ValidatePasswords
    ValidatePasswords -->|Mismatch| PreventDefault
    ValidatePasswords -->|Match| ValidateAP
    PreventDefault --> GenToast
    
    ValidateAP -->|Unassigned AP| PreventDefault
    ValidateAP -->|All Assigned| ValidateRace
    
    ValidateRace -->|Not Selected| PreventDefault
    ValidateRace -->|Selected| ValidateAvatar
    
    ValidateAvatar -->|Not Selected| PreventDefault
    ValidateAvatar -->|Selected| AllowSubmit
```

**Sources:** [navs/nav-login.php:242-274]()

### Attribute Point Allocation

The `stat_adjust()` function in [js/functions.js:14-43]() manages the allocation of attribute points during character creation. It maintains a pool of 10 available points that can be distributed among Strength, Defense, and Intelligence stats:

**Sources:** [js/functions.js:14-43](), [navs/nav-login.php:209-230]()

### Password Toggle Functionality

Password fields include a "Show/Hide" toggle button that switches the input type between `password` and `text`. This is implemented with a data attribute selector `[data-loa=pw_toggle]` and event listener:

```javascript
// From nav-login.php lines 413-426
document.querySelectorAll("[data-loa=pw_toggle]").forEach((e) => {
    e.addEventListener("click", (ev) => {
        var cur_ele_textbox = ev.target.previousElementSibling;
        if (cur_ele_textbox.type == 'password') {
            ev.target.textContent = 'Hide';
            cur_ele_textbox.type = 'text';
        } else {
            ev.target.textContent = 'Show';
            cur_ele_textbox.type = 'password';
        }
    });
});
```

**Sources:** [navs/nav-login.php:413-426]()

---

## Client-Side State Management

Client-side state is managed through a combination of the global `loa` object, browser sessionStorage/localStorage, and DOM manipulation. The `loa` object serves as the primary bridge between PHP session state and JavaScript runtime.

### Global `loa` Object Structure

```mermaid
graph TB
    subgraph "loa Object Properties"
        Email["u_email<br/>User's email address"]
        AccountID["u_aid<br/>Account ID"]
        CSRF["u_csrf<br/>CSRF token"]
        SessionID["u_sid<br/>PHP session ID"]
        CharID["u_cid<br/>Character ID (conditional)"]
        CharName["u_name<br/>Character name (conditional)"]
        ChatPos["chat_pos<br/>Chat scroll position"]
        ChatHistory["chat_history[]<br/>Chat message array"]
    end
    
    subgraph "Usage Patterns"
        AJAXRequests["AJAX Request Headers<br/>Include loa.u_csrf"]
        UserDisplay["UI Elements<br/>Display loa.u_name"]
        ChatSystem["Chat Module<br/>Uses chat_pos, chat_history"]
    end
    
    Email --> UserDisplay
    AccountID --> AJAXRequests
    CSRF --> AJAXRequests
    CharID --> AJAXRequests
    ChatPos --> ChatSystem
    ChatHistory --> ChatSystem
```

**Sources:** [html/headers.html:49-64]()

The `loa` object is conditionally populated: `u_cid` and `u_name` are only included when `$_SESSION['character-id']` is set, allowing the same header template to work for both logged-in and logged-out states.

### Menu Collapse State

Sidebar menu expansion state is managed through CSS classes and JavaScript helper functions in [js/functions.js:63-78]():

| Function | Purpose | Implementation |
|----------|---------|----------------|
| `collapse_all()` | Closes all menu sections | Removes `.menu-open` class, sets `display: none` |
| `expand_all()` | Opens all menu sections | Adds `.menu-open` class, clears display style |

**Sources:** [js/functions.js:63-78]()

The sidebar collapse state is also tracked at the body level with `.sidebar-collapse` and `.sidebar-open` classes. A `MutationObserver` in [navs/sidemenus/nav-side-default.php:620-638]() watches for changes to the body class and shows/hides a sidebar sliver element accordingly.

---

## Theme and Appearance Settings

User appearance preferences are stored in the `Settings` class and synchronized with the database. The `Settings` class in [src/Account/Settings.php:27-76]() uses the PropSuite trait for automatic persistence.

### Theme Properties

```mermaid
classDiagram
    class Settings {
        -int id
        -string colorMode
        -SidebarType sideBar
        +__construct(accountID)
        +get_colorMode()
        +set_colorMode(mode)
        +get_sideBar()
        +set_sideBar(type)
    }
    
    class SidebarType {
        <<enumeration>>
        LTE_DEFAULT
        LTE_COMPACT
        LTE_MINIMAL
    }
    
    Settings --> SidebarType
```

**Sources:** [src/Account/Settings.php:27-76]()

The `colorMode` property accepts values like `'dark'` or `'light'`, which are applied to the page through `data-bs-theme` attributes. The `sideBar` property uses the `SidebarType` enum to control sidebar layout variations.

Theme switching is handled client-side by the `set_theme()` function in [js/functions.js:58-61]():

```javascript
function set_theme(theme) {
    var hidden_el = document.getElementById("profile-theme");
    hidden_el.value = theme;
}
```

This function updates a hidden form field that gets submitted to persist the theme preference.

**Sources:** [js/functions.js:58-61](), [src/Account/Settings.php:34-47]()

---

## Asset and Icon Systems

### Material Symbols Integration

Material Symbols provide iconography throughout the application. The font is loaded through [css/gfonts.css:141-178]() with two variants: Outlined and Sharp.

```mermaid
graph LR
    subgraph "Font Face Definitions"
        OutlinedFont["@font-face<br/>Material Symbols Outlined<br/>weight 100-700"]
        SharpFont["@font-face<br/>Material Symbols Sharp<br/>weight 100-700"]
    end
    
    subgraph "CSS Classes"
        OutlinedClass[".material-symbols-outlined<br/>font-size: 24px"]
        SharpClass[".material-symbols-sharp<br/>font-size: 24px"]
    end
    
    subgraph "HTML Usage"
        NavIcons["&lt;i class='material-symbols-outlined'&gt;<br/>person, inventory_2, map, etc."]
    end
    
    OutlinedFont --> OutlinedClass
    SharpFont --> SharpClass
    OutlinedClass --> NavIcons
```

**Sources:** [css/gfonts.css:141-178](), [navs/sidemenus/nav-side-default.php:49-59]()

Icons are inserted using semantic HTML `<i>` elements with text content matching the Material Symbol name (e.g., `<i class="material-symbols-outlined">person</i>`). This approach provides accessibility and allows dynamic icon changes.

### Retro Gaming Fonts

The application uses two primary gaming fonts for aesthetic consistency:

1. **VT323** - A monospace font mimicking vintage terminal displays, used for general game text
2. **Press Start 2P** - An 8-bit arcade-style font for headers and emphasis

These fonts are defined in [css/gfonts.css:30-83]() with multiple unicode range subsets for broad language support. The `.main-font` class applies VT323 with 32px size:

```css
.main-font {
    font-family: "VT323", monospace;
    font-weight: 400;
    font-style: normal;
    font-size: 32px;
}
```

**Sources:** [css/gfonts.css:219-224](), [css/gfonts.css:30-83]()

---

## Summary

The frontend architecture of Legend of Aetheria combines multiple CSS frameworks in a layered cascade, with Bootstrap providing the foundation, AdminLTE adding admin panel structure, and RPGUI contributing retro game aesthetics. Asset loading follows a strict order through centralized header/footer templates, ensuring consistent resource availability across all pages.

Navigation uses AdminLTE's tree-view sidebar with server-side active state detection and client-side scroll management. Forms implement multi-stage validation with HTML5 attributes, JavaScript checks, and toast notifications for user feedback. Client-side state is bridged from PHP sessions through the global `loa` object, enabling seamless AJAX communication and dynamic UI updates.

Theme preferences are persisted through the `Settings` class, while Material Symbols and retro gaming fonts (VT323, Press Start 2P) provide consistent iconography and typography. The entire frontend is designed to support both authenticated and unauthenticated states with conditional rendering based on session data.

**Sources:** All files referenced throughout document, particularly [html/headers.html:1-65](), [navs/sidemenus/nav-side-default.php:1-650](), [navs/nav-login.php:1-427](), [js/functions.js:1-78](), [css/gfonts.css:1-225](), [src/Account/Settings.php:1-76]()