<?php
namespace Game\Components\Sidebar\Enums;

/**
 * Defines available sidebar layout types with paths to their template files.
 * Includes the original Legend of Aetheria classic layout and multiple AdminLTE variants
 * for different display preferences (collapsed, fixed, mini, etc.).
 */
enum SidebarType: string {
    /** Original LOA sidebar layout */
    case LOA_CLASSIC = "navs/sidemenus/nav-side-classic.php";
    
    /** AdminLTE default sidebar - standard width, expandable menu */
    case LTE_DEFAULT = "navs/sidemenus/nav-side-default.php";
    
    /** AdminLTE collapsed sidebar - minimized by default, expands on hover */
    case LTE_COLLAPSED = "navs/sidemenus/nav-side-collapsed.php";
    
    /** AdminLTE fixed sidebar - stays visible during page scroll */
    case LTE_FIXED = "navs/sidemenus/nav-side-fixed.php";
    
    /** AdminLTE mini sidebar - compact icon-only view */
    case LTE_MINI = "navs/sidemenus/nav-side-mini.php";
    
    /** AdminLTE unfixed sidebar - scrolls with page content */
    case LTE_UNFIXED = "navs/sidemenus/nav-side-unfixed.php";
}