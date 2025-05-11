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
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                "Authorization: Bearer {$this->apiKey}",
            ],
        ]);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        
        error_log("OpenAI Request: " . json_encode($data));
        error_log("OpenAI Response: " . $response);
        
        curl_close($ch);
        
        if ($err) {
            throw new \Exception("cURL Error: " . $err);
        }

        return $response;
    }

    public function get_payload() {
        $model = Models::name_to_enum($this->model);
        if ($model === Models::DALLE2 || $model === Models::DALLE3) {
            $payload = [
                "model" => $this->model->value,
                "prompt" => $this->prompt,
                "n" => (int)$this->count,
                "size" => $this->resolution,
                "response_format" => "url"
            ];
            return json_encode($payload);
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

    public function generate_image() {
        global $log;
        $this->endPoint = 'https://api.openai.com/v1/images/generations';
        $payload = $this->get_payload();

        return $this->doRequest(HttpMethod::POST, $payload);
    }
}
