<?php
    namespace Game\Traits\EnumExtender;

    trait EnumExtender {
        public static function name_to_enum(string $name): self {
            foreach (self::cases() as $case) {
                if ($name == $case->name) {
                    return $case;
                }
            }
            throw new \TypeError("$name is not a valid backing type for enum " . self::class);
        }

        public static function value_to_enum(int|string $value): self {
            foreach (self::cases() as $case) {
                if ($value == $case->value) {
                    return $case;
                }
            }
            throw new \ValueError("$value is not a valid backing value for enum " . self::class);
        }

        public static function random_enum(): self {
            $count = count(self::cases()) - 1;
            return self::cases()[mt_rand(0, $count)];
        }

        public static function name_to_value(string $name): string {
            foreach (self::cases() as $case) {
                if ($name === $case->name){
                    return $case->value;
                }
            }
            throw new \ValueError("$name is not a valid backing value for enum " . self::class);
        }
    }
?>