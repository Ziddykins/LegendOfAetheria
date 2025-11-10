<?php

    namespace Game\AI;
    use Game\Traits\PropSuite\PropSuite;
    
    
    class LoAllama {
        use PropSuite;
        private int $id;
        private string $name;
        private string $model;
        private string $startingPrompt;
        private string $baseUrl = "http://localhost:11434";
        private string $endPoint = "/api/generate";
        private bool $sentPrompt;

        public function __construct(int $id, string $name, string $model) {
            $this->id = $id;
            $this->name = $name;
            $this->model = $model;
        }

        /*
        {
            "model": "loa",
            "created_at": "2025-11-06T15:43:29.115261741Z",
            "response": "What",
            "done": false
        }
*/
        public function sendText(string $text): string {
            $url = $this->baseUrl . $this->endPoint;
            $buffer = [];
            $headers = [
                'Content-Type: application/json',
                'Accept: application/json',
            ];
            echo $url;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                "model" => $this->model,
                "prompt" => $text,
                "options" => [
                    "num_predict" => 5,
                    "num_ctx" => 5
                ],
                "stream" => false
            ]));
            
            while ($response = curl_exec($ch)) {
                $data = json_decode($response, true);

                if (isset($data['done']) && $data['done'] == 'false') {
                    array_push($buffer, $data['response']);
                } else {
                    return join('', $buffer);
                }
            };
            return curl_error($ch);
        }
        /**
         * Magic method for dynamic property access and modification.
         * 
         * Handles get/set operations, mathematical operations (add, sub, mul, div, exp, mod),
         * and property dump/restore operations via PropSuite trait.
         * Note: add/sub operations on HP/MP/EP are automatically capped at their max values.
         * 
         * @param string $method Method name to invoke
         * @param array $params Parameters for the method
         * @return mixed Result of the invoked method
         */
        public function __call($method, $params) {
            global $db;

            if (!count($params)) {
                $params = null;
            }

            if (preg_match('/^(add|sub|exp|mod|mul|div)_/', $method)) {
                return $this->propMod($method, $params);
            } elseif (preg_match('/^(propDump|propRestore)$/', $method, $matches)) {
                $func = $matches[1];
                return $this->$func($params[0] ?? null);
            } else {
                return $this->propSync($method, $params, PropType::LLAMA);
            }
        }

        public function jsonSerialize(): array {
            return get_object_vars($this);
        }
    }
?>
