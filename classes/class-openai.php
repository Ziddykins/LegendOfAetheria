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
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $this->endPoint);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            if ($method == HttpMethod::POST) {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
            
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            $response = curl_exec($ch);

            curl_close($ch);
            
            return $response;
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
