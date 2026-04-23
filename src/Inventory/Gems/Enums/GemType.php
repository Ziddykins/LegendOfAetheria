<?php

namespace Game\Inventory\Gems\Enums;
use Game\Traits\EnumExtender\EnumExtender;

/**
 * Defines available gem types for socketing into items.
 * Organized by color families (red, blue, green, yellow, colorless, etc.).
 * Each type may provide different stat bonuses or effects.
 * 
 * @method static GemType name_to_enum(string $name) Converts a case name to enum instance
 */
enum GemType {
    use EnumExtender;
    // Red gems - typically strength/damage focused
    /** Deep red precious gem */
    case RUBY;
    /** Red semi-precious gem */
    case GARNET;
    /** Red variety of garnet */
    case PYROPE;

    // Blue gems - typically intelligence/magic focused
    /** Deep blue precious gem */
    case SAPPHIRE;
    /** Blue-green beryl gemstone */
    case AQUAMARINE;

    // Green gems - typically defense/regeneration focused
    /** Vivid green precious gem */
    case EMERALD;
    /** Olive-green olivine mineral */
    case PERIDOT;
    /** Green magnesium iron silicate */
    case OLIVINE;
    /** Multi-colored boron silicate mineral */
    case TOURMALINE;

    // Yellow gems - typically luck/critical focused
    /** Golden yellow precious gem */
    case TOPAZ;

    // Colorless gems - typically versatile/balanced
    /** Clearest and hardest precious gem */
    case DIAMOND;
    /** Common crystalline mineral */
    case QUARTZ;

    // Other colors - specialty effects
    /** White or yellow sulfate mineral */
    case BARITE;
    /** Greenish-glowing variety of fluorite */
    case CHLOROPHANE;
    /** Multi-colored group of minerals */
    case FELDSPAR;
    /** Wide color range halide mineral */
    case FLUORITE;
    /** Green to yellow-green olivine variety */
    case FOSTERITE;
    /** Alternate spelling for tourmaline */
    case TOURMAL;
}