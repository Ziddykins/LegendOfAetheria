<?php
if ($_SERVER['SCRIPT_NAME'] == '/game.php') {
    require_once "bootstrap.php";
} else {
    require_once "../bootstrap.php";
}

// Check if user is logged in
if (!isset($_SESSION['account-id'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Please log in to access chat']);
    exit();
}

$request = file_get_contents('php://input');
$req_obj = json_decode($request);

header('Content-Type: application/json');

if (!$req_obj || !isset($req_obj->action)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request format']);
    exit();
}

switch ($req_obj->action) {
    case 'get_msgs':
        $count = $req_obj->count ?? 100;
        $room = $req_obj->room ?? '!main';
        $since_id = $req_obj->since_id ?? 0;
        get_messages($room, intval($count), intval($since_id));
        break;
    case 'add_msg':
        if (!isset($req_obj->data)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing message data']);
            exit();
        }

        $char_id = $req_obj->data->char_id;
        $room = $req_obj->data->room;
        $message = $req_obj->data->message;
        $nickname = $req_obj->data->nickname;
        $message = htmlentities($message);
        $result = add_message($char_id, $room, $message, $nickname);
        
        if ($result === false) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Could not add message']);
            exit();
        }
        
        echo json_encode(['status' => 'success']);
        exit();

    case 'get_online':
        $sql_query = "SELECT COUNT(`id`) AS `online` FROM {$t['accounts']} WHERE `session_id` IS NOT NULL";
        $result = $db->execute_query($sql_query)->fetch_assoc();
        echo json_encode(['online' => (int)$result['online']]);
        exit();

    default:
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        exit();
}

function add_message(int $char_id, string $room, string $message, string $nickname): bool {
    global $db, $t;
    $sql_query = "INSERT INTO {$t['chat']} (`character_id`, `room`, `message`, `nickname`) VALUES (?, ?, ?, ?)";
    
    return $db->execute_query($sql_query, [$char_id, $room, $message, $nickname]) !== false;
}

function get_messages(string $room = '!main', int $count = 100, int $since_id = 0): void {
    global $db, $t;
    
    // If since_id is provided, get only newer messages
    if ($since_id > 0) {
        $sql_query = "SELECT DISTINCT * FROM {$t['chat']} WHERE `room` = ? AND `id` > ? ORDER BY `id` ASC";
        $result = $db->execute_query($sql_query, [$room, $since_id]);
    } else {
        // Initial load - get latest messages
        $sql_query = "SELECT DISTINCT * FROM {$t['chat']} WHERE `room` = ? ORDER BY `id` DESC LIMIT ?";
        $result = $db->execute_query($sql_query, [$room, $count]);
    }
    
    if (!$result) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => $db->error]);
        exit();
    }
    
    $messages = $result->fetch_all(MYSQLI_ASSOC);
    

    echo json_encode($messages);
    exit();
}