<?php
    declare(strict_types = 1);
    session_start();
    require '../../../vendor/autoload.php';
    require_once 'classes/class-openai.php';  
    require_once 'logger.php';
    require_once 'db.php';
    require_once 'constants.php';
    require_once 'functions.php';

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();

    if (isset($_REQUEST['generate_description']) 
            && $_REQUEST['generate_description'] == 1
            && isset($_REQUEST['characterID'])) {
        
        $credits = $account['credits'];

        if ($ai_credits < 1) {
            echo "Not Enough Credits\n";
            exit();
        } else {
            $ai_credits--;
        }

        $sql_query = "SELECT `char.name`, `char.race`, `ch.account_id`, `acct.credits` " .
                     "FROM " . $_ENV['SQL_CHAR_TBL'] . " AS `char` " .
                     "JOIN " . $_ENV['SQL_ACCT_TBL'] . " AS `acct` " .
                     "ON ch.`account_id` = `acct.id`"
                     "WHERE id = ?";

        $prepped = $db->prepare($sql_query);
        $prepped->bind_param('d', $_REQUEST['characterID']);
        $prepped->execute();
        
        $result    = $prepped->get_result();
        $character = $result->fetch_assoc();

        $sql_query = "UPDATE " . $_ENV['SQL_ACCT_TBL'] . . " " .
                     "SET ai_credits = ?";

        $api_endpoint = 'https://api.openai.com/v1/chat/completions';
        
        $chatbot = new OpenAI(
            $_ENV['OPENAI_APIKEY'],
            $api_endpoint
        );

        $chatbot->set_model('gpt-3.5-turbo');
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

        $content = $response->choices[0]->message->content;
        $finish_reason = $response->choices[0]->finish_reason;
        $description = "";

        if ($finish_reason == 'length') {
            $tmp = explode("\n\n", $content);

            for ($i=0; $i<count($tmp) - 2; $i++) {
                $description .= $tmp[$i] . "\n\n";
            }
        }

        echo $description;
    } else {
        echo "Invalid Query";
    }
?>