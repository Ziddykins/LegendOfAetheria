<?php
namespace Game\System\Enums;

enum Weather {
    public static function random(): self {
        $count = count(self::cases()) - 1;
        return self::cases()[rand(0, $count)];
    }

    public static function name_to_value(string $name): string {
        foreach (self::cases() as $weather) {
            if ($name === $weather->name){
                return $weather->value;
            }
        }
        throw new \ValueError("$name is not a valid backing value for enum " . self::class);
    }

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