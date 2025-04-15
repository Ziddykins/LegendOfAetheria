<?php

use Game\Account\Account;
use Game\Character\Character;
    /**
     * Get a list of new members registered between two dates.
     *
     * @param string $start_date The start date.
     * @param string $end_date The end date.
     * @return array Returns an array of new member accounts.
     */
    function get_new_members($days) {
        global $db;

        $sql_query = "SELECT * FROM `tbl_characters` WHERE `date_created` BETWEEN (NOW() - INTERVAL $days DAY) AND NOW() ORDER BY `date_created` ASC LIMIT 8";
        $characters = $db->execute_query($sql_query)->fetch_all(MYSQLI_ASSOC);

        return $characters;
    }

    /**
     * Get the count of new members registered between two dates.
     *
     * @param string $start_date The start date.
     * @param string $end_date The end date.
     * @return int Returns the count of new member accounts.
     */
    function new_members_count($days = 30): int {
        global $db, $t;

        $query = "SELECT COUNT(`id`) FROM {$t['characters']} WHERE `date_created` BETWEEN (NOW() - INTERVAL $days DAY) AND NOW()";
        return $db->execute_query($query)->fetch_column();        
    }

    /**
     * Populate a list of new members in a HTML format.
     *
     * @param array $accounts The array of new member accounts.
     */
    function populate_new_members($days = 30) {
        global $db;
        $out_str = '';
        
        $characters = get_new_members(30);

        foreach ($characters as $character) {
            $t_aid = $character['account_id'];
            $t_cid = $character['id'];
            $char = new Character($t_aid, $t_cid);
            $char->load();

            echo '<div class="col-3 p-2"><img class="img-fluid object-fit-sm-contain rounded-circle mx-auto" style="height: 100px; width: 100px;" src="/img/avatars/' . $char->get_avatar() . '" alt="User Image">' .
                 '<a class="btn fw-bold fs-7 text-secondary text-truncate w-100 p-0" href="/users?id=' . $t_cid . '">' .
                 $char->get_name() . '</a><div class="fs-8">' . $char->get_dateCreated()  . '</div></div>';
        }
    }

    /**
     * Get the full name of an account.
     *
     * @param \Game\Account\Account $account The account array.
     * @return string Returns the full name of the account.
     */
    function get_full_name(Account $account) {
        return $account->get_firstName() . ' ' . $account->get_lastName();
    }

    /**
     * Trait to handle conversions between class properties <=> SQL table columns.
     */
    trait HandlePropsAndCols {
        /**
         * Convert a class property to a table column name.
         *
         * @param string $property The class property.
         * @return string Returns the corresponding table column name.
         */
        function clsprop_to_tblcol($property) {
            $splits = preg_split('/(?=[A-Z]{1,2})/', $property);

            if (count($splits) === 1) {
                return $property;
            }

            $table_column = $splits[0] . '_' . strtolower($splits[1]);

            if (isset($splits[2])) {
                $table_column .= strtolower($splits[2]);
            }

            return $table_column;
        }

        /**
         * Converts a table column name to a class property.
         *
         * @param string $column The table column name.
         * @return string Returns the corresponding class property.
         *
         * @throws Exception If the column name does not match the expected format.
         */
        function tblcol_to_clsprop($column) {
            $splits = preg_split('/_/', $column);

            if (count($splits) === 1) {
                return $column;
            }

            if ($splits[1] === 'id') {
                $class_property = $splits[0]. strtoupper($splits[1]);
            } else {
                $class_property = $splits[0] . ucfirst($splits[1]);
            }

            if (isset($splits[2])) {
                $class_property .= ucfirst($splits[2]);
            }

            return $class_property;
        }
    }

        /**
         * Ensures that the user is authenticated before accessing certain pages.
         * If the user is not logged in, they will be redirected to the login page.
         *
         * @return void
         *
         * @throws Exception If the session is not started.
         */
        function ensure_authenticated() {
            global $db;

            if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
                header('Location: /login');
                exit();
            }

            $query = "SELECT `verified` FROM `tbl_users` WHERE `id` = ?";
            $verified = $db->execute_query($query, [$_SESSION['user_id']])->fetch_assoc()['verified'];

            if ($verified === 'False' && $_SERVER['REQUEST_URI'] !== '/verify') {
                header('Location: /verify');
                exit();
            }
        }

        /**
         * Checks if an account with the given email exists in the database.
         *
         * @param string $email The email of the account to check.
         * @return bool Returns true if an account with the given email exists, false otherwise.
         *
         * @throws Exception If there is an error executing the database query.
         */
        function account_email_exists($email) {
            global $db; // Assuming $db is a global database connection object.

            // Prepare the SQL query with a parameter placeholder.
            $query = 'SELECT `email` FROM `tbl_users` WHERE `email` = ?';

            // Execute the query with the provided email as a parameter.
            // fetch_column() is used to retrieve the first column of the first row in the result set.
            $result = $db->execute_query($query, [$email])->fetch_column();

            // Return true if a result was found (i.e., an account with the given email exists), false otherwise.
            return $result ? true : false;
        }

        /**
         * Generates a CSRF token and stores it in the session.
         *
         * @return void
         *
         * @throws Exception If there is an error with the session.
         */
        function generate_csrf_token() {
            global $log;
            if (!isset($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                $log->debug('Generated CSRF token', ['csrf_token' => $_SESSION]);

            }
        }

        /**
         * Checks if the received CSRF token matches the stored one in the session.
         *
         * @param string $csrf_received The received CSRF token.
         * @return bool Returns true if the tokens match, false otherwise.
         *
         * @throws Exception If there is an error with the session.
         */
        function check_csrf_token($csrf_received) {
            global $log;

            if (isset($_SESSION['csrf_token'])) {
                $log->debug("CSRF token check, got: $csrf_received");
                if ($csrf_received === $_SESSION['csrf_token']) {
                    return true;
                }
            }
            return false;
        }

        function dump_globals() {
            echo "<pre>====================FILES===========================";
            print_r($_FILES);
            echo "===================post==========================";
            print_r($_POST);
            echo "===================SERVER===========================";
            print_r($_SERVER);
            echo "====================END=============================";
        }
        function gen_column_dta($class_obj): array {
            $refl = new ReflectionClass($class_obj);
            $props = $refl->getProperties();
            $col_data = [];

            foreach ($props as $prop) {
                $conv_prop = clsprop_to_tblcol($prop->name);
                $col = "{title:\"{$conv_prop}\", field:\"{$conv_prop}\", cellEdited:function(cell) {cell.getData();} ";
                $col .= '}';
                array_push($col_data, $col);
            }

            return $col_data;
        }

        function gen_globalchat_html(array $messages): string {
            $html = null;

            foreach ($messages as $message) {
                $tmp_aid = Character::getAccountID($message['character_id']);
                $char_obj = new Character($tmp_aid, $message['character_id']);
                $char_obj->load();


                $avatar = $char_obj->get_avatar();
                $sender = $char_obj->get_name();

                $direction = "left";
                $opdir = "right";
                $div_dir = '';
                
                if ($message['id'] % 2 == 0) {
                    $direction = "right";
                    $div_dir = "right";
                    $opdir = "left";
                }



                $html .= '<div class="d-flex"><div class="direct-chat-msg ' . $div_dir . '">';
                $html .= '    <div class="direct-chat-infos clearfix">';
                $html .= "        <span class=\"direct-chat-name float-$direction\">$sender</span>";
                $html .= "        <span class=\"direct-chat-timestamp float-$opdir\">{$message['when']}</span>";
                $html .= "    </div>";
                $html .= "    <img class=\"direct-chat-img\" src=\"../../img/avatars/$avatar\" alt=\"message user image\" />";
                $html .= '    <div class="direct-chat-text">';
                $html .= "        {$message['message']}";
                $html .= '    </div>';
                $html .= '</div></div>';
            }

            return $html;
        }

        function add_global_message($character_id, $message) {

        }
