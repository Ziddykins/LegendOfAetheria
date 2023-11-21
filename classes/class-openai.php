<?php
    class OpenAI {
        protected $apiKey;
        protected $endPoint;

        protected $model;

        protected $systemPrompt;
    
        protected $userPrompt;

        public function __construct($api_key, $end_point) {
            $this->apiKey   = $api_key;
            $this->endPoint = $end_point;
        }

        public function doRequest($method, $headers, $data) {
            $ch = curl_init($this->endPoint);

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            if ($method == HttpMethod::POST) {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
            
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $response = curl_exec($ch);
            print_r($ch);

            curl_close($ch);
            
            return $response;
        }

        public static function processPrompt($user_prompt) {

        }
    
        public function get_model() {
            return $this->model;
        }

        public function set_model($model) {
            $this->model = $model;
        }
    }
        
    enum HttpMethod {
        case HEAD;
        case POST;
        case DEL;
        case GET;
        case PUT;
    };
