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
    }

    public function doRequest($method, $data) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->endPoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            "Authorization: Bearer {$this->apiKey}"
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $verbose = fopen('php://temp', 'w+');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);
        
        if ($method == HttpMethod::POST) {
            curl_setopt($ch, CURLOPT_POST, 1);
            $postData = json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            error_log("OpenAI API Request: " . $this->endPoint . "\nPayload: " . $postData);
        }
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        
        // Get curl debug info
        rewind($verbose);
        $verboseLog = stream_get_contents($verbose);
        error_log("OpenAI API Debug: " . $verboseLog);

        if (curl_errno($ch)) {
            error_log("OpenAI API Error: " . curl_error($ch));
            return json_encode(['error' => ['message' => curl_error($ch)]]);
        }

        error_log("OpenAI API Response: " . $response);
        
        curl_close($ch);
        return $response;
    }

    public function get_payload() {
        if ($this->model === Models::DALLE2 || $this->model === Models::DALLE3) {
            $payload = [
                "model" => $this->model,
                "prompt" => $this->prompt,
                "n" => $this->model === Models::DALLE3 ? 1 : $this->count,
                "size" => $this->resolution,
                "output_format" => $this->outputFormat,
            ];

            
            $this->payload = $payload;
            return $this->payload;
        }
        return $this->payload;
    }

    public function showPayload() {
        if (isset($this->payload)) {
            $temp_payload = $this->payload;
            $temp_payload = preg_replace($_ENV['OPENAI_APIKEY'], '<APIKEY>', $temp_payload);
            print_r($temp_payload);
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
        echo "<div class=\"alert alert-danger\">$error</div>";
    }
}
