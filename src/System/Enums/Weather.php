<?php
namespace Game\System\Enums;
use Game\Traits\EnumExtender\EnumExtender;

/**
 * Defines weather conditions for zones with visual icon representations.
 * Each weather type provides a Bootstrap-styled icon HTML string.
 * 
 * @method static Weather name_to_enum(string $name) Converts a case name to enum instance
 */
enum Weather {
    use EnumExtender;
    
    /**
     * Returns Bootstrap icon HTML for the weather condition.
     * 
     * @return string HTML span with icon and color styling
     */
    public function icon(): string {
        return match($this) {
            Weather::CLOUDY =>  '<span class="text-info-emphasis"><i class="bi bi-cloud-fill p-2"></i>',
            Weather::SUNNY  =>  '<span class="text-warning"><i class="bi bi-brightness-high-fill p-2"></i>',
            Weather::HAILING => '<span class="text-primary-emphasis"><i class="bi bi-cloud-hail-fill p-2"></i>',
            Weather::SNOWING => '<span class="text-white"><i class="bi bi-cloud-snow-fill p-2"></i>',
            Weather::RAINING => '<span class="text-primary"><i class="bi bi-cloud-lightning-rain-fill p-2"></i>'
        };
    }

    /** Clear sunny weather - brightness icon in yellow */
    case SUNNY;
    
    /** Rain with lightning - storm icon in blue */
    case RAINING;
    
    /** Hailstorm - hail cloud icon in dark blue */
    case HAILING;
    
    /** Overcast/cloudy - cloud icon in light blue */
    case CLOUDY;
    
    /** Snowing - snow cloud icon in white */
    case SNOWING;
}