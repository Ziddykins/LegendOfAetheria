<?php
namespace Game\Mail\Envelope\Enums;

/**
 * Class EnvelopeStatus
 *
 * Provides constants representing the possible statuses of an envelope,
 * such as read, unread, archived, etc.
 *
 * @package Game\Mail\Envelope\Enums
 */
enum EnvelopeStatus: int {
    public static function get_status_line(string $flagstring): string {
        $status_line = '<span class="small">';
        $status_line .= '<i class="text-danger me-1 bi bi-arrow-through-heart';
        $val = self::value_from_flagstring($flagstring);


        if ($val & EnvelopeStatus::FAVORITE->value) {
            $status_line .= '-fill"></i>';
        } else {
            $status_line .= '"></i>';
        }

        $status_line .= '<i class="text-primary me-1 bi bi-arrow-down-left-circle';

        if ($val & EnvelopeStatus::REPLIED->value) {
            $status_line .= '-fill"></i>';
        } else {
            $status_line .= '"></i>';
        }

        $status_line .= '<i style="color: #7f03fc" class="me-1 bi bi-exclamation-diamond';

        if ($val & EnvelopeStatus::IMPORTANT->value) {
            $status_line .= '-fill"></i>';
        } else {
            $status_line .= '"></i>';
        }

        return $status_line;
    }

    public static function value_from_flagstring(string $flagstring): int {
        $flags = explode(',', $flagstring);
        $value = 0;

        foreach ($flags as $flag) {
            $value += self::name_to_value($flag);
        }

        return $value;
    }

    public static function name_to_value(string $name): int {
        foreach (self::cases() as $case) {
            if ($name === $case->name) {
                return $case->value;
            }
        }
        return 0;
    }

    case IMPORTANT = 1;
    case READ      = 2;
    case REPLIED   = 4;
    case FAVORITE  = 8;
};