<?php
    declare(strict_types = 1);
    session_start();
    require __DIR__ . '/vendor/autoload.php';

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();

    //require_once 'logger.php';
    //require_once 'db.php';
    //require_once 'constants.php';
    //require_once 'functions.php';
    require_once 'classes/class-openai.php';
    $api_endpoint = 'https://api.openai.com/v1/chat/completions';
    
    $default_headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $_ENV['OPENAI_APIKEY'],
    ];
    
    $GPT = new OpenAI(
        $_ENV['OPENAI_APIKEY'],
        $api_endpoint
    );

    $GPT->set_model('gpt-3.5-turbo');
    $model = $GPT->get_model();
    
    $data = (
    [
        'model'    => $GPT->get_model(),
        'messages' => [
            'role'    => 'system',
            'content' => 'You are an informative dungeon master, helping this ' .
                         'new warrior along his quest for glory.'
        ], 
        [
            'role'    => 'user',
            'content' =>  'Where am I? Why can\'t I remember anything?'
        ]
    ]);

    $response = $GPT->doRequest(
        HttpMethod::POST,
        $default_headers,
        $data
    );

    echo "<pre>";
    print_r($response);

    print_r($GPT);
