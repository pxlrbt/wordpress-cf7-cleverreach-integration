<?php

namespace pxlrbt\Cf7Cleverreach\Cleverreach;

use pxlrbt\Cf7Cleverreach\Vendor\GuzzleHttp\Client;
use Exception;

class Api
{
    private $token;
    private $listId;
    private $formId;

    private static $restUrl = 'https://rest.cleverreach.com/v2/';
    private static $tokenUrl = 'https://rest.cleverreach.com/oauth/token.php';
    private static $authUrl = 'https://rest.cleverreach.com/oauth/authorize.php';

    public function __construct($token = '')
    {
        $this->token = $token;
        $this->client = new Client();
    }

    /* HELPER FUNCTIONS */
    public function request($url, $method = "GET", $data = null)
    {
        $headers = ['Authorization' => 'Bearer ' . $this->token];

        $response = $this->client->request($method, $url, [
            'json' => $data,
            'headers' => $headers
        ]);

        $content = $response->getBody()->getContents();
        $json = json_decode($content);

        if ($json === null) {
            throw new Exception('Invalid JSON response.');
        }

        return $json;
    }

    public static function generateAuthLink($clientId, $redirectUrl)
    {
        return self::$authUrl
                . '?client_id=' . $clientId
                . '&grant=basic&response_type=code&redirect_uri=' . $redirectUrl;
    }

    public function buildUrl($endpoint)
    {
        return self::$restUrl . $endpoint;
    }

    public function validateResponse($response)
    {
        if (isset($response->error)) {
            throw new Exception("CF7 to CleverReach:" . $response->error->message);
        }
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

        $response = $this->client->request('POST', self::$tokenUrl, [
            'json' => $data
        ]);

        $content = $response->getBody()->getContents();
        $json = json_decode($content);

        if ($json === null) {
            throw new Exception('Invalid JSON response.');
        }

        return $json;
    }

    public function refreshApiToken($clientId, $clientSecret, $refreshToken)
    {
        $data = [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken
        ];

        $response = $this->client->request('POST', self::$tokenUrl, [
            'json' => $data
        ]);

        $content = $response->getBody()->getContents();
        $json = json_decode($content);

        if ($json === null) {
            throw new Exception('Invalid JSON response.');
        }

        return $json;
    }

    /* API FUNCTIONS */
    public function getContactByEmail($listId, $email)
    {

        $url = $this->buildUrl('receivers/filter.json');
        $result = $this->request($url, 'POST', [
            'rules' => [
                [
                    'field' => 'email',
                    'logic' => 'eq',
                    'condition' => $email
                ]
            ],
            'activeonly' => false,
            'groups' => [$listId],
            'page' => 0,
            'pagesize' => 1

        ]);

        $this->validateResponse($result);

        return is_array($result) > 0 ? $result[0] : null;
    }

    public function updateContact($listId, $email, $tags = [], $attributes = [], $globalAttributes = [])
    {
        $url = $this->buildUrl('groups.json/' . $listId . '/receivers/' . $email);
        $result = $this->request($url, 'PUT', [
            "email" => $email,
            "attributes" => $attributes,
            "tags" => $tags,
            "global_attributes" => $globalAttributes
        ]);

        $this->validateResponse($result);

        return $result;
    }

    public function createContact($listId, $email, $active = false, $source = '', $tags = [], $attributes = [], $globalAttributes = [])
    {
        $url = $this->buildUrl('groups.json/' . $listId . '/receivers');
        $result = $this->request($url, 'POST', [
            "email" => $email,
            "source" => $source,
            "tags" => $tags,
            "active" => $active,
            "registered" => time(),
            "activated" => ($active ? time() : 0),
            "attributes" => $attributes,
            "global_attributes" => $globalAttributes
        ]);

        $this->validateResponse($result);

        return $result;
    }

    public function sendActivationMail($formId, $email)
    {
        global $wp;

        $doidata = [
            "user_ip" => isset($_SERVER['REMOTE_ADDR'])
                ? $_SERVER['REMOTE_ADDR']
                : 'redacted',

            "user_agent" => isset($_SERVER['HTTP_USER_AGENT'])
                ? $_SERVER['HTTP_USER_AGENT']
                : 'redacted',

            "referer" => isset($_SERVER['HTTP_REFERER'])
                ? $_SERVER['HTTP_REFERER']
                : home_url($wp->request),
        ];

        $url = $this->buildUrl('forms.json/' . $formId . '/send/activate');

        $result = $this->request($url, 'POST', [
            'email' => $email,
            'doidata' => $doidata
        ]);

        $this->validateResponse($result);

        return $result;
    }

    public function getGroups()
    {
        $url = $this->buildUrl('groups.json');
        $result = $this->request($url, 'GET');

        $this->validateResponse($result);

        return $result;
    }

    public function getForms()
    {
        $url = $this->buildUrl('forms.json');
        $result = $this->request($url, 'GET');

        $this->validateResponse($result);

        return $result;
    }

    public function getAttributes($groupId = 0)
    {
        $url = $this->buildUrl('attributes.json/?group_id=' . $groupId);
        $result = $this->request($url, 'GET');

        $this->validateResponse($result);

        return $result;
    }
}
