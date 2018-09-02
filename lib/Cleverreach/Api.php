<?php

namespace Pixelarbeit\Cleverreach;

use Pixelarbeit\Http\JsonClient;



class Api
{
    private $token;
    private $listId;
    private $formId;

    private static $restUrl = 'https://rest.cleverreach.com/v2/';
    private static $tokenUrl = 'https://rest.cleverreach.com/oauth/token.php';
    private static $authUrl = 'https://rest.cleverreach.com/oauth/authorize.php';



    // public function __construct($token, $listId, $formId)
    public function __construct($token = '')
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



    public static function generateAuthLink($clientId, $redirectUrl)
    {
        return self::$authUrl
                . '?client_id=' . $clientId
                . '&grant=basic&response_type=code&redirect_uri=' . $redirectUrl;
    }



    public function getApiToken($clientId, $clientSecret, $code, $redirectUrl)
    {
        $data = [
            'client_id' => $clientId, 
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUrl,
            'grant_type' => 'authorization_code',
            'code' => $code
        ];

        $response = $this->client->request('POST', self::$tokenUrl, $data, []);
        return $response[1];
    }
}
