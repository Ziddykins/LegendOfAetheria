<?php
namespace Game\AI;

use Game\AI\Enums\HttpMethod;

/**
 * Class for interacting with the NodeJS server, running a modified
 * version of the @ai-rpg-engine
 * 
 * Provides methods for making HTTP requests to the local server's
 * endpoints, getting item details, rumor propagation, and more.
 * 
 * @package Game\AI
 * 
 */
class RPG {
	/* @var string $baseUrl The base URL for the node server */
	private string $baseUrl = 'localhost';

	/* @var int $serverPort The port for the server; defaults to 3000 */
	private int $serverPort = 3000;
	
	/* @var string $protocol If $secure is true, protocol will be https, http otherwise */
	private string $protocol = 'http';

	/* @var int $timeout Amount of seconds before a request times out, defaults to 20 */
	private int $timeout = 20;

    /**
     * Constructs a new RPG class instance.
     * 
	 * @param string $url The base URL for the node server
	 * @param int $port The port for the server; defaults to 3000
	 * @param bool $secure If true, requests will be sent over https
	 * 
     */
	public function __construct(string $url, int $port, bool $secure) {
		$this->protocol = $secure ? 'https' : 'http';
		$this->serverPort = preg_replace('/[^0-9]+/', '', $port);
		$this->baseUrl = "{$protocol}://$url";

		if ($port !== 443 & $port !== 80) {
			$this->baseUrl .= ":{$port}"
		}
    }

    /**
     * Executes an HTTP request to the NodeJS server.
     * 
     * @param HttpMethod $method HTTP method (POST or GET)
     * @param array $data Unencoded body payload of a POST request
     * @return string|false Response body or false on failure
     */
    public function send(string $endpoint, HttpMethod $method, array $data = null) {
		$ch  = curl_init();
		$url = "{$this->baseUrl}/$endpoint"

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json',
			'Accept: application/json'
		]);

        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        
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
