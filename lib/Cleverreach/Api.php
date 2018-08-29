<?php

namespace Pixelarbeit\Cleverreach;

use Pixelarbeit\Http\JsonClient;



class Api
{
    private $token;
    private $listId;
    private $formId;

    private static $restUrl = "https://rest.cleverreach.com/v2/";



    // public function __construct($token, $listId, $formId)
    public function __construct($token)
    {
        $this->token = $token;
        $this->client = new JsonClient();
    }

    

    public function request($url, $method = "GET", $data = null)
    {
        $headers = ['Authorization: Bearer ' . $this->token];        
        $response = $this->client->request($method, $url, $data, $headers);
        return $response[1];
    }


    
    public function buildUrl($endpoint)
    {
        return self::$restUrl . $endpoint;
    }
}
