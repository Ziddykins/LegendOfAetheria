<?php

namespace Game\Inventory\Gems\Enums;
use Game\Traits\EnumExtender\EnumExtender;

enum GemType {
    use EnumExtender;
    // Red gems
    case RUBY;
    case GARNET;
    case PYROPE;

    // Blue gems
    case SAPPHIRE;
    case AQUAMARINE;

    // Green gems
    case EMERALD;
    case PERIDOT;
    case OLIVINE;
    case TOURMALINE;

    // Yellow gems
    case TOPAZ;

    // Colorless gems
    case DIAMOND;
    case QUARTZ;

    // Other colors
    case BARITE; // Typically white or yellow
    case CHLOROPHANE; // Greenish glow
    case FELDSPAR; // Various colors
    case FLUORITE; // Wide range of colors
    case FOSTERITE; // Green to yellow-green
    case TOURMAL; // Short for Tourmaline, various colors
}