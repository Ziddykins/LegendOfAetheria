/**
 * This PHP script runs different cron jobs based on the command line argument passed to it.
 *
 * @example
 * php cron.php minute
 * This will run the `do_minute()` function, which adds energy to a database table.
 *
 * @package Cron
 */

require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

include('logger.php');
include('db.php');
include('constants.php');
include('functions.php');

if (!isset($argv)) {
    $log->critical('Access to cron.php directly is not allowed!', ['REQUEST' => print_r($_REQUEST, 1)]);
    echo 'Access to cron.php directly is not allowed!';
    exit(1);
}

$log->info('cronjob running', ['argv[1]' => $argv[1]]);

switch ($argv[1]) {
    case 'minute':
        do_minute();
        break;
    case 'hourly':
        do_hourly();
        break;
    case 'default':
        $log->warning('No cron job specified!');
        die('No cron job specified!');
}

/**
 * Adds energy to the database table.
 *
 * @global object $db The database connection object.
 * @global object $log The logger object.
 */
function do_minute()
{
    global $db, $log;
    $sql_query = 'SELECT * FROM ' . $_ENV['SQL_CHAR_TBL'] . ' WHERE ep <> max_ep';
    $results = $db->query($sql_query);

    while ($row = $results->fetch_assoc()) {
        $new_ep = $row['ep'] + 3;

        $new_ep = min($new_ep, $row['max_ep']);

        $sql_query = 'UPDATE ' . $_ENV['SQL_CHAR_TBL'] . " SET ep = $new_ep WHERE id = " . $row['id'];
        $log->info("Updating ep to $new_ep", ['SQL_QUERY' => $sql_query]);
        $db->query($sql_query);
    }
}

/**
 * Empty function that does nothing.
 */
function do_hourly()
{
    // Empty function
}