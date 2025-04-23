<?php

use Game\Components\Modals\Enums\ModalButtonType;
use Game\System\Enums\AbuseType;
use Game\Character\Enums\FriendStatus;
use Game\Character\Enums\Races;
use Game\System\Enums\LOAError;


     /**     * Retrieves a MySQL datetime string based on the provided modifier.
     *
     * This function calculates a datetime string in 'Y-m-d H:i:s' format based on the given modifier.
     * If no modifier is provided, it defaults to the current datetime.
     *
     * @param string $modifier A string modifier for datetime calculation (e.g., '+1 day'). Defaults to 'now'.
     * @return string The formatted MySQL datetime string.
     */
    function get_mysql_datetime($modifier = 'now') {
        global $log;

        if ($modifier !== 'now') {
            $operand  = substr($modifier, 0, 1);
            $amount   = substr($modifier, 1);
            $modifier = "$operand$amount";
        }

        return date("Y-m-d H:i:s", strtotime((string) $modifier));
    }

    /**
     * Calculates the difference in seconds between two MySQL datetime strings.
     *
     * Converts the provided datetime strings into Unix timestamps and computes the difference in seconds.
     *
     * @param string $date_one The first datetime string in 'Y-m-d H:i:s' format.
     * @param string $date_two The second datetime string in 'Y-m-d H:i:s' format.
     * @return int The difference in seconds between the two datetime strings.
     */
    function sub_mysql_datetime(string $date_one, string $date_two) {
        $date_one_secs = strtotime($date_one);
        $date_two_secs = strtotime($date_two);
        $seconds_left  = $date_two_secs - $date_one_secs;

        return $seconds_left;
    }

    /**
     * Retrieves a global configuration value from the database.
     *
     * Queries the 'globals' table to fetch the value associated with the given name.
     *
     * @param string $which The name of the global configuration to retrieve.
     * @return string|null The value of the global configuration, or null if not found.
     */
    function get_globals($which) {
        global $db, $t;
        $ret_val = '';
        $sql_query = "SELECT `value` FROM {$t['globals']} WHERE `name` = '$which'";
        $result = $db->query($sql_query);
        $row = $result->fetch_assoc();

        return $row['value'];
    }

    /**
     * Updates a global configuration value in the database.
     *
     * Updates the 'globals' table with the provided name and value.
     *
     * @param string $name The name of the global configuration to update.
     * @param string $value The new value for the global configuration.
     * @return void
     */
    function set_globals($name, $value) {
        global $db, $t;

        $sql_query = "UPDATE {$t['globals']} SET `value` = '$value' WHERE `name` = '$name'";
        $db->query($sql_query);
    }

    /**
     * Generates a random floating-point number within a specified range and precision.
     *
     * Uses a pseudo-random number generator to create a number between the given minimum and maximum values.
     *
     * @param float $min The minimum value for the random number.
     * @param float $max The maximum value for the random number.
     * @param int $precision The number of decimal places to round the result to.
     * @return float A random floating-point number within the specified range.
     */
    function random_float($min, $max, $precision): float {
        return round($min + mt_rand() / mt_getrandmax() * (abs($max - $min)), $precision);
    }

    /**
     * Checks the number of unread emails for the current account.
     *
     * Queries the database to count emails with a status that does not include 'READ' for the current account.
     *
     * @param string $what The type of email to check. Only 'unread' is supported.
     * @return int|LOAError|string The count of unread emails or an error if the directive is unsupported.
     */
    function check_mail($what): int|LOAError|string {
        global $db, $log, $t;

        switch ($what) {
            case 'unread':
                $sql_query = "SELECT * FROM {$t['mail']} WHERE NOT FIND_IN_SET('READ', `status`) AND `r_aid` = ?";
                $result = $db->execute_query($sql_query, [ $_SESSION['account-id'] ])->num_rows;
                return $result;
            default:
                return LOAError::MAIL_UNKNOWN_DIRECTIVE;
        }
    }

    /**
     * Determines the friendship status between the current user and another user.
     *
     * Checks the database for friendship records involving the specified character ID and returns the status.
     *
     * @param int $character_id The ID of the character to check the friendship status with.
     * @return Game\Character\Enums\FriendStatus The friendship status or an error if determination fails.
     */
    function friend_status(int $character_id): FriendStatus {
        global $db, $log, $t;

        $status    = FriendStatus::NONE;
        $sql_query = "SELECT * FROM {$t['friends']} WHERE `recipient_id` = ? OR `sender_id` = ?";
        $results   = $db->execute_query($sql_query, [ $character_id, $character_id ])->fetch_assoc();

        if ($results) {
            $status = FriendStatus::name_to_enum($results['friend_status']);
        }

        if ($status == null) {
            $status = FriendStatus::NONE;
        }

        //$log->warning("Friend status between character ID $character_id -> {$status->name}");        
        return $status;
    }

    /**
     * Accepts a friend request from the specified sender.
     *
     * Updates the friendship status to 'MUTUAL' if a friend request exists from the sender.
     *
     * @param int $sender The ID of the sender who sent the friend request.
     * @return bool True if the request was successfully accepted, false otherwise.
     */
    function accept_friend_req($sender):bool {
        global $db, $log, $t;

        if (friend_status($sender) === FriendStatus::REQUEST_RECV) {
            $sql_query = "UPDATE {$t['friends']} SET `friend_status` = ? WHERE `recipient_id` = ?";
            $db->execute_query($sql_query, [ FriendStatus::MUTUAL->value, $_SESSION['character-id'] ]);
            $log->info('Friend request accepted', [ 'sender' => $sender, 'recipient' => $_SESSION['character-id'] ]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Retrieves friend-related data such as counts or lists based on the specified criteria.
     *
     * Queries the database for friendship statuses and optionally returns a list of character IDs.
     *
     * @param FriendStatus|null $status The specific friendship status to filter by (optional).
     * @param bool $return_list Whether to return a list of character IDs (default: false).
     * @return array An array containing friendship statuses and optionally character IDs.
     */
    function get_friend_counts(?FriendStatus $status, bool $return_list=false): array {
        global $db, $character, $t;
        $count = 0;
        $ids = [];

        $sql_query = <<<SQL
            SELECT 
                `friend_status` AS `status`,
                COUNT(`friend_status`) AS `count`
            FROM {$t['friends']}
            WHERE
                sender_id = ? OR
                recipient_id = ?
            GROUP BY `friend_status`
        SQL;

        $statuses = $db->execute_query($sql_query, [ $character->get_id(), $character->get_id() ])->fetch_all(MYSQLI_ASSOC);

        $statuses['MUTUAL'] ?? 0;
        $statuses['REQUEST_RECV'] ?? 0;
        $statuses['REQUEST_SENT'] ?? 0;
        $statuses['BLOCKED'] ?? 0;

        if ($return_list) {
            $status_clause = null;
            if ($status !== null) {
                $status_clause = " AND `friend_status` = '" . $status->name . "'";
            }
                

            $sql_query = <<<SQL
                SELECT DISTINCT
                    IF(`recipient_id` = ?, `sender_id`, `recipient_id`) AS `character_id`
                FROM {$t['friends']}
                WHERE `recipient_id` = ? OR `sender_id` = ? $status_clause
            SQL;
            $ids = $db->execute_query($sql_query, [$character->get_id(), $character->get_id(), $character->get_id()])->fetch_all(MYSQLI_ASSOC);
            $data = [];
            $data['statuses'] = $statuses;
            $data['ids'] = $ids;
            return $data;
        }
        
        return $statuses;
    }

    /* TODO: test */
    /**
     * Blocks a user by updating the friendship status to 'BLOCKED'.
     *
     * Inserts or updates the friendship record to reflect the blocked status.
     *
     * @param string $email_1 The email of the current user.
     * @param string $email_2 The email of the user to block.
     * @return int Always returns 0.
     */
    function block_user($email_1, $email_2): int {
        global $db, $t;
        $sql_query = <<<SQL
            INSERT INTO {$t['friends']}
                (`sender_id`, `recipient_id`, `friend_status`)
            VALUES
                (?, ?, ?)
            UPDATE ON DUPLICATE KEY
                `friend_status` = 'BLOCKED'
        SQL;

        $db->execute_query($sql_query, [$email_1, $email_2, 'BLOCKED']);
        
        return 0;
    }

    /**
     * Validates an email address for proper format and sanitization.
     *
     * Ensures the email is sanitized and matches the original input, then checks if it is valid.
     *
     * @param string $email The email address to validate.
     * @return bool True if the email is valid, false otherwise.
     */
    function check_valid_email($email): bool {
        $sanitized_email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if ($sanitized_email == $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks for potential abuse based on the specified type and data.
     *
     * Performs abuse detection by querying logs for suspicious activity.
     *
     * @param Game\System\Enums\AbuseType $type The type of abuse to check for.
     * @param int $account_id The account ID to check abuse for.
     * @param string $ip The IP address to check abuse for.
     * @param int $threshold The threshold for abuse detection (default: 1).
     * @return bool True if abuse is detected, false otherwise.
     */
    function check_abuse(AbuseType $type, $account_id, $ip, $threshold = 1): bool {
        global $db, $log, $t;

        switch ($type) {
            case AbuseType::MULTISIGNUP:
                $sql_query = <<<SQL
                                SELECT `id` FROM {$t['logs']}
                                WHERE `type` = ?
                                    AND `ip` = ?
                                    AND `date` BETWEEN (NOW() - INTERVAL 1 HOUR) AND NOW()
                            SQL;
                $count = $db->execute_query($sql_query, [ $type->name, $ip ])->num_rows;

                if ($count > $threshold) {
                    return true;
                }

                return false;
            case AbuseType::TAMPERING:
                $sql_query = <<<SQL
                    SELECT `id` FROM {$t['logs']}
                    WHERE `type` = ? AND `ip` = ?
                SQL;
                $count = $db->execute_query($sql_query, [ $type->name, $ip ])->num_rows;

                if ($count > $threshold) {
                    return true;
                }
                return false;
            default:
                $log->error("No type specified for abuse lookup");
        }

        return false;
    }

    /**
     * Generates an HTML modal with the specified parameters.
     *
     * Creates a Bootstrap modal with customizable content and button types.
     *
     * @param string $id The ID of the modal.
     * @param string $bg_color The background color of the modal header.
     * @param string $header The header text of the modal.
     * @param string $body The body content of the modal.
     * @param ModalButtonType $btn_type The type of buttons to include in the modal.
     * @return string The generated HTML for the modal.
     */
    function generate_modal($id, $bg_color, $header, $body, ModalButtonType $btn_type): string {
        $btn = null;
        if ($btn_type === ModalButtonType::YESNO) {
            $btn  = '<button type="button" class="btn btn-danger"  data-bs-dismiss="modal">No</button>';
            $btn .= '<button type="button" class="btn btn-success" data-bs-dismiss="modal">Yes</button>';
        } elseif ($btn_type === ModalButtonType::CLOSE) {
            $btn  = '<button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button>';
        } elseif ($btn_type === ModalButtonType::OKCANCEL) {
            $btn   = '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>';
            $btn  .= '<button type="button" class="btn btn-primary"   data-bs-dismiss="modal">Okay</button>';
        } else {
            $btn = '<input type="button" value=":(" />';
        }

        $html = '<div class="modal fade" id="' . $id . '-modal" tabindex="-1" aria-hidden="true" style="z-index: 1044;">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-' . $bg_color . ' text-bg-' . $bg_color . '">
                                <h1 class="modal-title fs-5">' . $header . '</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <strong>' . $body . '</strong>
                            </div>

                            <div class="modal-footer">
                                ' . $btn . '
                            </div>
                        </div>
                    </div>
                </div>';

        return $html;
    }

    /**
     * Retrieves the total count of monsters in the system.
     *
     * @return int The number of monsters.
     */
    function get_monster_count(): int {
        global $system;
        return count($system->monsters);
    }

    /**
     * Retrieves the next available ID for a specified database table.
     *
     * Queries the table to find the maximum ID and increments it by 1.
     *
     * @param string $table The name of the table to retrieve the next ID for.
     * @return int The next available ID.
     */
    function getNextTableID($table): int {
        global $db, $t;
        $sql_query = "SELECT IF(MAX(`id`) IS NULL, 1, MAX(`id`)+1) AS `next_id` FROM $table";
        $next_id = $db->execute_query($sql_query)->fetch_assoc()['next_id'];
        
        return $next_id;
    }

    /**
     * Logs a message to the database with the specified type and IP address.
     *
     * Inserts a log entry into the 'logs' table.
     *
     * @param string $log_type The type of log entry.
     * @param string $message The log message.
     * @param string $ip The IP address associated with the log entry.
     * @return void
     */
    function write_log(string $log_type, string $message, string $ip): void {
        global $db, $t;
        
        $sql_query = "INSERT INTO {$t['logs']} (`type`, `message`, `ip`) VALUES (?, ?, ?)";
        $db->execute_query($sql_query, [ $log_type, $message, $ip ]);
    }

    /**
     * Bans a user for a specified duration with a reason.
     *
     * Updates the user's account status to 'banned' and logs the ban details.
     *
     * @param int $account_id The ID of the account to ban.
     * @param int $length_secs The duration of the ban in seconds.
     * @param string $reason The reason for the ban.
     * @return void
     */
    function ban_user($account_id, $length_secs, $reason): void {
        global $db, $t;
        $expires = get_mysql_datetime("+$length_secs seconds");
        $sql_query = "UPDATE {$t['accounts']} SET `banned` = 'True' WHERE `id` = ?";
        $db->execute_query($sql_query, [ $account_id ]);

        $sql_query = <<<SQL
            INSERT INTO {$t['banned']} 
                (`account_id`, `expires`, `reason`)
            VALUES (?, ?, ?)
        SQL;

        $db->execute_query($sql_query, [ $account_id, $expires, $reason ]);
    }

    /**
     * Validates a race against the predefined list of valid races.
     *
     * If the race is invalid, a random valid race is selected.
     *
     * @param string $race The race to validate.
     * @return string The validated or randomly selected race.
     */
    function validate_race($race): string {
        global $log, $account;

        $valid_race = 0;

        foreach (Races::cases() as $enum_race) {
            if ($race === $enum_race->name) {
                $valid_race = 1;
            }
        }

        if (!$valid_race) {
            $race = Races::random()->name;
            $log->critical("Possible POST modify in race selection", ['Race' => $race, 'AID' => $account->get_id()]);
        }
        
        return $race;
    }

    /**
     * Validates an avatar against the list of available avatars.
     *
     * If the avatar is invalid, a default avatar is assigned.
     *
     * @param string $avatar The avatar to validate.
     * @return string The validated or default avatar.
     */
    function validate_avatar($avatar): string {
        global $log;

        $arr_images = scandir('img/avatars');

        if (!array_search($avatar, $arr_images)) {
            $avatar_now = 'avatar-unknown.webp';
            $log->critical(
                'Avatar wasn\'t found in our ' .
                'accepted list of avatar choices!',
                [
                    'Avatar' => $avatar,
                    'Avatar_now' => $avatar_now,
                ]
            );
            $avatar = $avatar_now;
        }

        return $avatar;
    }

    /**
     * Safely serializes or unserializes data using base64 encoding.
     *
     * @param mixed $data The data to serialize or unserialize.
     * @param bool|null $unserialize Whether to unserialize the data (default: false).
     * @return mixed The serialized or unserialized data.
     */
    function safe_serialize($data, ?bool $unserialize=null): mixed {
        global $log;
        $ret_data = null;

        if ($unserialize === true) {
            $ret_data = unserialize(base64_decode($data));
        } else {
            $ret_data = base64_encode(serialize($data));
        }

        return $ret_data;
    }

    /**
     * Validates the current session for the logged-in user.
     *
     * Checks session variables and database records to ensure the session is valid.
     *
     * @return bool True if the session is valid, false otherwise.
     */
    function check_session(): bool {
        global $db, $log, $t;

        if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] != 1) {
            return false;
        }

        $sql_query = "SELECT `session_id` FROM {$t['accounts']} WHERE `id` = ?";
        $result = $db->execute_query($sql_query, [ $_SESSION['account-id'] ])->fetch_assoc();
        
        if (!$result) {
            $log->warning("session_id not found");
            return false;
        }

        $session = $result['session_id'];
        
        if ($session != session_id()) {
            $log->warning("Session ID in db doesn't match browser session id", [ 'SessionDB' => $session, 'SessionBrowser' => session_id() ]);
            return false;
        }

        return true;
    }

    /**
     * Generates a CSRF token for the current session.
     *
     * Creates a random token and logs it for debugging purposes.
     *
     * @return string The generated CSRF token.
     */
    function gen_csrf_token(): string {
        global $log;
        $csrf = bin2hex(random_bytes(14)) . 'L04D' . bin2hex(random_bytes(14));
        $log->warning("csrf: $csrf");
        return $csrf;
    }

    /**
     * Validates a CSRF token against the session token.
     *
     * If the tokens do not match, the session is destroyed, and the user is redirected.
     *
     * @param string $req_csrf The CSRF token to validate.
     * @return bool True if the token is valid, false otherwise.
     */
    function check_csrf($req_csrf): bool {
        if ($req_csrf != $_SESSION['csrf-token']) {
            $_SESSION = [];
            session_destroy();
            header('Location: /?csrf_fail');
            exit();
        }

        return true;
    }

    /**
     * Dumps a variable to HTML for debugging purposes.
     *
     * Outputs the variable in a readable format and optionally exits the script.
     *
     * @param mixed $obj The variable to dump.
     * @param bool|null $exit Whether to exit the script after dumping (default: true).
     * @return int Always returns 0.
     */
    function dump_to_html(mixed $obj, ?bool $exit=true): int {
        echo '<pre>';
        print_r($obj);
        
        if ($exit) {
            exit();
        } 

        return 0;
    }

    /**
     * Formats a name with a possessive suffix.
     *
     * Adds an apostrophe or "'s" to the end of the name based on its last character.
     *
     * @param string $name The name to format.
     * @return string The formatted name.
     */
    function fix_name_header(string $name): string {
        $ending_char = substr($name, -1, 1);

        if (preg_match('/[sS]/', $ending_char)) {
            return "$name'";
        }

        return "$name's";
    }