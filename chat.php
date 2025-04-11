<?php
    require_once "bootstrap.php";

    $request = file_get_contents('php://input');
    $req_obj = json_decode($request);

    switch ($req_obj->action) {
        case 'get':
            $count = $req_obj->count;
            $room  = $req_obj->room;
            $messages = get_messages($room, intval($count));
            echo json_encode($messages);
            break;
        case 'add':
            $char_id  = $req_obj->char_id;
            $room     = $req_obj->room;
            $message  = $req_obj->message;
            $nickname = $req_obj->nickname;

            if ($char_id !== $_SESSION['character-id']) {
                //check_abuse(AbuseType::CHATABUSE);
            } elseif ($room != '!main') {
                //check_abuse(AbuseType::CHATABUSE);
            }

            $message = htmlentities($message);
            $result = add_message($char_id, $room, $message, $nickname);
            
            if ($result == false) {
                http_response_code(400);
                echo '{"status": "error - couldnt add message"}';
            } else {
                echo '{"status": "success"}';
            }
            break;
        case 'online':
            $sql_query = "SELECT COUNT(`id`) FROM tbl_accounts WHERE `session_id` IS NOT NULL";
            return $db->execute_query($sql_query)->fetch_column();            
        default:
            echo '{"status": "error - invalid action"}';
    }

    function add_message(int $char_id, string $room, string $message, string $nickname): int {
        global $db;
        $sql_query = "INSERT INTO {$_ENV['SQL_CHAT_TBL']} (`character_id`, `room`, `message`, `nickname`) VALUES (?, ?, ?, ?)";

        return $db->execute_query($sql_query, [$char_id, $room, $message, $nickname]);
    }
    function get_messages(string $room = '!main', int $count = 100): array {
        global $db;
        $messages = [];
        

        $sql_query = "SELECT * FROM {$_ENV['SQL_CHAT_TBL']} WHERE `room` = ? ORDER BY `id` DESC LIMIT ?";
        $messages = $db->execute_query($sql_query, [$room, $count])->fetch_all(MYSQLI_ASSOC);

        return $messages;
    }