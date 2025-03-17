<?php
    /**
     * Get a list of new members registered between two dates.
     *
     * @param string $start_date The start date.
     * @param string $end_date The end date.
     * @return array Returns an array of new member accounts.
     */
    function get_new_members($start_date, $end_date) {
        global $db;

        $query = "SELECT * FROM `tbl_accounts` WHERE `date_registered` BETWEEN ? AND ? ORDER BY `date_registered` ASC LIMIT 8";
        $accounts = $db->execute_query($query, [$start_date, $end_date])->fetch_all(MYSQLI_ASSOC);

        return $accounts;
    }

    /**
     * Get the count of new members registered between two dates.
     *
     * @param string $start_date The start date.
     * @param string $end_date The end date.
     * @return int Returns the count of new member accounts.
     */
    function new_members_count($days = 7) {
        global $db;

        $query = "SELECT * FROM `tbl_accounts` WHERE `date_registered` BETWEEN (NOW() - INTERVAL $days DAY) AND NOW() ORDER BY `date_registered` ASC";
        $accounts = $db->execute_query($query)->fetch_all();

        return count($accounts);
    }

    /**
     * Populate a list of new members in a HTML format.
     *
     * @param array $accounts The array of new member accounts.
     */
    function populate_new_members($accounts) {
        foreach ($accounts as $account) {
            $character = null;
            
            if ($account['char_slot1'] > 0) {
                $character = new Game\Character\Character($account['id'], $account['char_slot1']);
                $character->load();


            echo '<div class="col-3 p-2"><img class="img-fluid object-fit-sm-contain rounded-circle mx-auto" style="height: 100px; width: 100px;" src="/img/avatars/' . $character->get_avatar() . '" alt="User Image">' .
                 '<a class="btn fw-bold fs-7 text-secondary text-truncate w-100 p-0" href="/users?id=' .
                 $account['id'] . '">' . $character->get_name() . '</a>' .
                 '<div class="fs-8">' . $account['date_registered'] . '</div></div>';
            }
        }
    }

    /**
     * Get the full name of an account.
     *
     * @param Account $account The account array.
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
