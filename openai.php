<?php
    declare(strict_types = 1);
    session_start();
    require '../vendor/autoload.php';
    require_once 'classes/class-openai.php';  
    require_once 'logger.php';
    require_once 'db.php';
    require_once 'constants.php';
    require_once 'functions.php';



    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();

    $account   = table_to_obj($_SESSION['email'], 'account');
    $character = table_to_obj($account['id'], 'character');

  
    if (isset($_REQUEST['generate_description']) 
            && $_REQUEST['generate_description'] == 1
            && isset($_REQUEST['characterID'])) {

        
        $credits = $account['credits'];

    
        if ($credits < 1) {
            echo "Not Enough Credits\n";
            exit();
        } else {
            $sql_query = 'UPDATE tbl_accounts SET credits = credits - 1 WHERE id = ?';
            $account['credits']--;
            $db->execute_query($sql_query, [ $account['id'] ]);
        }

        $sql_query = "SELECT ch.`name`, ch.`race`, ch.`account_id`, ac.`credits` FROM {$_ENV['SQL_CHAR_TBL']} AS `ch` JOIN {$_ENV['SQL_ACCT_TBL']} AS `ac` ON (ch.`account_id` = ac.`id`) WHERE ac.`id` = ?;";

        $db->execute_query($sql_query, [ $account['id'] ]);

        $api_endpoint = 'https://api.openai.com/v1/chat/completions';
        
        $chatbot = new OpenAI(
            $_ENV['OPENAI_APIKEY'],
            $api_endpoint
        );

        $chatbot->set_model('gpt-3.5-turbo-1106');
        $chatbot->set_maxTokens(200);

        $prompt = "Generate a character description for a(n) " . $character['race'] . " named " . $character['name'];

        $data = [
            "model" => $chatbot->get_model(),
            "messages" => [
                [
                    "role" => "system",
                    "content" =>  "You are a dungeon master who generates highly-detailed character descriptions",
                ],
                [
                    "role" => "user",
                    "content" => "$prompt",
                ],
            ],
            "max_tokens" => $chatbot->get_maxTokens()
        ];

        $response = json_decode(
            $chatbot->doRequest(HttpMethod::POST, $data)
        );


        $content       = $response->choices[0]->message->content;
        $finish_reason = $response->choices[0]->finish_reason;
        $description   = "";

        if ($finish_reason == 'length') {
            $tmp = explode("\n\n", $content);

            for ($i=0; $i<count($tmp) - 2; $i++) {
                $description .= $tmp[$i] . "\n\n";
            }
        }

        echo htmlentities($content);
    } else {
        echo "Invalid Query";
    }
?>