<?php
namespace Game\OpenAI;

use Game\OpenAI\Enums\HttpMethod;
class OpenAI {
    protected $apiKey;
    protected $endPoint;
    protected $imageModel;
    
    protected $imagePrompt;
    protected $imageCount;
    protected $imageSize;

    protected $payload;

    protected $maxTokens;

    public function __construct($api_key, $end_point) {
        $this->apiKey   = $api_key;
        $this->endPoint = $end_point;
    }

    public function doRequest($method, $data) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->endPoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            "Authorization: Bearer {$this->apiKey}"
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        if ($method == HttpMethod::POST) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);

        curl_close($ch);
        
        return $response;
    }

    public function showPayload() {
        if (isset($this->payload)) {
            $temp_payload = $this->payload;
            $temp_payload = preg_replace("{$_ENV['OPENAI_APIKEY']}", '<APIKEY>', $temp_payload);
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
}