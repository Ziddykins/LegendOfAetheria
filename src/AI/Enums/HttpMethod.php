<?php
namespace Game\AI\Enums;

/**
 * HttpMethod enum defines HTTP request methods for API calls.
 * 
 * @package Game\AI\Enums
 */
enum HttpMethod {
    /** HTTP POST method for creating/updating resources */
    case POST;
    
    /** HTTP GET method for retrieving resources */
    case GET;
};