<?php
    declare(strict_types = 1);
    session_start();
    require __DIR__ . '/vendor/autoload.php';
    require_once 'classes/class-openai.php';
    
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();

    $api_endpoint = 'https://api.openai.com/v1/chat/completions';
    
    $default_headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $_ENV['OPENAI_APIKEY'],
    ];
    
    $chatbot = new OpenAI(
        $_ENV['OPENAI_APIKEY'],
        $api_endpoint
    );

    $chatbot->set_model('gpt-3.5-turbo');
    $model = $chatbot->get_model();

    $data = [
        "model" => $chatbot->get_model(),
        "messages" => [
            [
                "role" => "system",
                "content" =>  "You're a customer support rep for a multi-national conglomerancy focused on cloud computing",
            ],
            [
                "role" => "user",
                "content" => "How do I internet?",
            ],
        ],
    ];

    /*$response = json_decode($chatbot->doRequest(
        HttpMethod::POST,
        $default_headers,
        $data)
    );
     */
    $chat_response = $response->choices[0]->message->content;

?>

<div class="container justify-content-center">
    <div class="row">
        <div class="col">
