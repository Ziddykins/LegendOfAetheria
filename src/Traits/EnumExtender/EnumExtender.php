<?php
    namespace Game\Traits\EnumExtender;

    trait EnumExtender {
        /**
         * Convert a name to the corresponding enum.
         *
         * @param string $name The name to convert.
         * @return self The corresponding enum.
         * @throws \TypeError If the name is not valid.
         */
        public static function name_to_enum(string $name): self {
            foreach (self::cases() as $case) {
                if ($name == $case->name) {
                    return $case;
                }
            }
            throw new \TypeError("$name is not a valid backing type for enum " . self::class);
        }

        /**
         * Convert a value to the corresponding enum.
         *
         * @param int|string $value The value to convert.
         * @return self The corresponding enum.
         * @throws \ValueError If the value is not valid.
         */
        public static function value_to_enum(int|string $value): self {
            foreach (self::cases() as $case) {
                if ($value == $case->value) {
                    return $case;
                }
            }
            throw new \ValueError("$value is not a valid backing value for enum " . self::class);
        }

        /**
         * Get a random enum from the defined cases.
         *
         * @return self A random enum.
         */
        public static function random_enum(): self {
            $count = count(self::cases()) - 1;
            return self::cases()[mt_rand(0, $count)];
        }

        /**
         * Convert a name to the corresponding value.
         *
         * @param string $name The name to convert.
         * @return string The corresponding value.
         * @throws \ValueError If the name is not valid.
         */
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
