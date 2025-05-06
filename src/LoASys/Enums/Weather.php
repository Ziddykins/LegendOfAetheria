<?php
namespace Game\LoASys\Enums;
use Game\Traits\EnumExtender\EnumExtender;

enum Weather {
    use EnumExtender;
    public function icon(): string {
        return match($this) {
            Weather::CLOUDY =>  '<span class="text-info-emphasis"><i class="bi bi-cloud-fill p-2"></i>',
            Weather::SUNNY  =>  '<span class="text-warning"><i class="bi bi-brightness-high-fill p-2"></i>',
            Weather::HAILING => '<span class="text-primary-emphasis"><i class="bi bi-cloud-hail-fill p-2"></i>',
            Weather::SNOWING => '<span class="text-white"><i class="bi bi-cloud-snow-fill p-2"></i>',
            Weather::RAINING => '<span class="text-primary"><i class="bi bi-cloud-lightning-rain-fill p-2"></i>'
        };
    }

    case SUNNY;
    case RAINING;
    case HAILING;
    case CLOUDY;
    case SNOWING;
}