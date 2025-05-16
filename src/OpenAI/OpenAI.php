<?php
namespace Game\OpenAI;

use Game\OpenAI\Enums\HttpMethod;
use Game\OpenAI\Enums\Models;


class OpenAI {
    private $apiKey;
    private $endPoint;
    private $model;
    
    private $prompt;
    private $count;
    private $resolution;

    private $payload;

    private $maxTokens;
    private $outputFormat = 'png';
    private array $previous_prompts;
    private array $previous_files;

    public function __construct($api_key) {
        $this->apiKey = $api_key;
        $this->previous_prompts = [];
        $this->previous_files = [];
    }

    public function doRequest($method, $data) {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $this->endPoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                "Authorization: Bearer {$this->apiKey}",
            ],
        ]);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        

        
        curl_close($ch);
        
        if ($err) {
            throw new \Exception("cURL Error: " . $err);
        }

        return $response;
    }

    public function get_payload() {
        if ($this->model === 'dall-e-2' || $this->model === 'dall-e-3') {
            $payload = [
                "model" => 'dall-e-2',
                "prompt" => 'testing this'
                //"model" => $this->model,
                //"prompt" => $this->prompt,
                //"n" => (int)$this->count, // Ensure number of images is included
                //"size" => $this->resolution, // Ensure resolution is included
            ];

            return json_encode($payload);
        }

        return $this->payload;
    }

    public function showPayload() {
        if (isset($this->payload)) {
            $key = $this->apiKey;
            $this->set_apiKey('<APIKEY>');
            print_r($this);
            $this->set_apiKey($key);
        } else {
            echo "Payload Not Set";
        }
    }

    public function __call($method, $params) {
        $var = lcfirst(substr($method, 4));

        if (strncasecmp($method, "get_", 4) === 0) {
            return $this->$var;
        }

        if (strncasecmp($method, "set_", 4) === 0) {
            $this->$var = $params[0];
        }
    }
    
    public function throw_error($error) {
        return "<div class=\"alert alert-danger\">$error</div>";
    }

    public function generate_image() {
        global $log;
        $this->endPoint = 'https://api.openai.com/v1/images/generations';
        $this->payload = $this->get_payload();
        return $this->doRequest(HttpMethod::POST, $this->payload);
    }
}
