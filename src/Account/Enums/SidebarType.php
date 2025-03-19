<?php
namespace Game\Account\Enums;

enum SidebarType: string {
    case CLASSIC = "navs/sidemenus/nav-side-classic.php";
    case LTE_DEFAULT = "navs/sidemenus/nav-side-default.php";
    case LTE_COLLAPSED = "navs/sidemenus/nav-side-collapsed.php";
    case LTE_FIXED = "navs/sidemenus/nav-side-fixed.php";
    case LTE_MINI = "navs/sidemenus/nav-side-mini.php";
    case LTE_UNFIXED = "navs/sidemenus/nav-side-unfixed.php";
}