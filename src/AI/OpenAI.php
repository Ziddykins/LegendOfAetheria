<?php
namespace Game\AI;

use Game\AI\Enums\HttpMethod;

/**
 * OpenAI API client for interacting with OpenAI services.
 * 
 * Provides methods for making HTTP requests to OpenAI endpoints,
 * including image generation and text completion.
 * 
 * @package Game\AI
 * 
 * @method string get_apiKey() Gets the API key
 * @method string get_endPoint() Gets the API endpoint URL
 * @method string get_imageModel() Gets the image generation model
 * @method string get_imagePrompt() Gets the image generation prompt
 * @method int get_imageCount() Gets the number of images to generate
 * @method string get_imageSize() Gets the image size specification
 * @method mixed get_payload() Gets the request payload
 * @method int get_maxTokens() Gets the maximum tokens limit
 * 
 * @method void set_apiKey(string $key) Sets the API key
 * @method void set_endPoint(string $url) Sets the API endpoint URL
 * @method void set_imageModel(string $model) Sets the image generation model
 * @method void set_imagePrompt(string $prompt) Sets the image generation prompt
 * @method void set_imageCount(int $count) Sets the number of images to generate
 * @method void set_imageSize(string $size) Sets the image size specification
 * @method void set_payload(mixed $payload) Sets the request payload
 * @method void set_maxTokens(int $tokens) Sets the maximum tokens limit
 */
class OpenAI {
    /** @var string OpenAI API key */
    protected $apiKey;
    
    /** @var string API endpoint URL */
    protected $endPoint;
    
    /** @var string Image generation model identifier */
    protected $imageModel;
    
    /** @var string Prompt for image generation */
    protected $imagePrompt;
    
    /** @var int Number of images to generate */
    protected $imageCount;
    
    /** @var string Image dimensions (e.g., "1024x1024") */
    protected $imageSize;

    /** @var mixed Request payload data */
    protected $payload;

    /** @var int Maximum number of tokens for completions */
    protected $maxTokens;

    /**
     * Constructs a new OpenAI client instance.
     * 
     * @param string $api_key OpenAI API key for authentication
     * @param string $end_point API endpoint URL
     */
    public function __construct($api_key, $end_point) {
        $this->apiKey   = $api_key;
        $this->endPoint = $end_point;
    }

    /**
     * Executes an HTTP request to the OpenAI API.
     * 
     * @param HttpMethod $method HTTP method (POST or GET)
     * @param array $data Request payload data
     * @return string|false Response body or false on failure
     */
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

    /**
     * Displays the current payload with API key masked.
     * 
     * Useful for debugging without exposing sensitive credentials.
     * 
     * @return void
     */
    public function showPayload() {
        if (isset($this->payload)) {
            $temp_payload = $this->payload;
            $temp_payload = preg_replace("{$_ENV['OPENAI_APIKEY']}", '<APIKEY>', $temp_payload);
            print_r($temp_payload);
        } else {
            echo "Payload Not Set";
        }
    }

    /**
     * Magic method for dynamic getter/setter access.
     * 
     * Handles get_* and set_* method calls for protected properties.
     * 
     * @param string $method Method name (get_* or set_*)
     * @param array $params Method parameters
     * @return mixed Property value for getters, void for setters
     */
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