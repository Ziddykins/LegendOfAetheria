<?php
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
            $ch = curl_init($this->endPoint);
            
            $default_headers = [];
            $default_headers[] = 'Content-Type: application/json';
            $default_headers[] = 'Authorization: Bearer ' . $this->apiKey;

            curl_setopt($ch, CURLOPT_HTTPHEADER, $default_headers);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            if ($method == HttpMethod::POST) {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
            
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            
            $response = curl_exec($ch);

            curl_close($ch);
            
            return $response;
        }

        public function buildRequest() {
            if ($this->imageCount && $this->imagePrompt) {
                $data = [
                    "model"  => $this->imageModel,
                    "prompt" => $this->imagePrompt,
                    "n"      => $this->imageCount,
                    "size"   => $this->imageSize,
                    "max_tokens" => $this->maxTokens
                ];
                $this->payload = json_encode($data);
            } else {
                echo "something missing";
                exit();
            }
        }

        public function showPayload() {
            if (isset($this->payload)) {
                print_r($this->payload);
            } else {
                echo "Payload Not Set";
            }
        }

        function __call($method, $params) {
            $var = lcfirst(substr($method, 4));

            if (strncasecmp($method, "get_", 4) === 0) {
                return $this->$var;
            }

            if (strncasecmp($method, "set_", 4) === 0) {
                $this->$var = $params[0];
            }
        }
    }
        
    enum HttpMethod {
        case HEAD;
        case POST;
        case DEL;
        case GET;
        case PUT;
    };
