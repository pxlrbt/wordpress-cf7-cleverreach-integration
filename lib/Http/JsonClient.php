<?php

namespace Pixelarbeit\Http;

use Pixelarbeit\Http\Exceptions\InvalidResponseException;
use Pixelarbeit\Http\Exceptions\ConnectionException;



class JsonClient
{
    public $debug = false;

    private $bulkHandler;
    private $bulkRequests;



    public function __contruct()
    {

    }



    /**
     * Sending request to given url
     * Throwing exception on connection error
     * @param  string $url Webservice request url
     * @return object      response
     */
    public function request($method, $url, $data = '', $headers = [])
    {
        $ch = curl_init();
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        $this->addJsonHeaders($headers, $data);
        $this->setOptions($ch, $method, $url, $data, $headers);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new ConnectionException(curl_error($ch), curl_errno($ch));
        }

        list($header, $body) = explode("\r\n\r\n", $response, 2);
        $json = json_decode($body);

        if ($json === null) {
            var_dump($response);
            throw new InvalidResponseException("No valid JSON", 1);
        }

        $headers = [];
        $header = explode("\n", $header);
        for ($i = 1; $i < count($header); $i++) {
            list($key, $item) = explode(': ', $header[$i]);
            $headers[$key] = $item;
        }

        if ($this->debug == true) {
            echo '<pre>';
            echo "<strong>SENT HEADERS:</strong><br>";
            print(curl_getinfo($ch)['request_header']) . '<br>';

            echo "<strong>SENT PAYLOAD:</strong><br>";
            print($data) . '<br><br>';

            echo "<strong>RESPONSE:</strong><br>";
            print($response) . '<br><br>';
            echo '</pre>';
        }


        return [$headers, $json];
    }



    public function get($url, $data = '', $headers = [])
    {
        return $this->request('GET', $url, $data = '', $headers = []);
    }



    public function put($url, $data = '', $headers = [])
    {
        return $this->request('PUT', $url, $data = '', $headers = []);
    }



    public function post($url, $data = '', $headers = [])
    {
        return $this->request('POST', $url, $data = '', $headers = []);
    }



    public function delete($url, $data = '', $headers = [])
    {
        return $this->request('DELETE', $url, $data = '', $headers = []);
    }



    private function addJsonHeaders(&$headers, $json)
    {
        $headers[] = 'Content-Type: application/json; charset=utf-8';
        $headers[] = 'Content-Length: ' . strlen($json);
    }



    private function setOptions(&$ch, $method, $url, $data, $headers)
    {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLINFO_HEADER_OUT , $this->debug);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        curl_setopt($ch, CURLOPT_HEADER, 1);

        curl_setopt($ch, CURLOPT_VERBOSE, true);
    }



    public function initBulkRequest()
    {
        $this->bulkHandler = curl_multi_init();
        $this->bulkRequests = [];
    }



    public function addBulkRequest($method, $url, $data = '', $headers = [])
    {
        $ch = curl_init();
        $data = json_encode($data);
        $this->addJsonHeaders($headers, $data);
        $this->setOptions($ch, $method, $url, $data, $headers);

        curl_multi_add_handle($this->bulkHandler, $ch);
        $this->bulkRequests[] = $ch;
    }



    public function executeBulkRequest($jsonResponse = false)
    {
        $active = null;

        do {
            curl_multi_exec($this->bulkHandler, $running);
            usleep(100);
        } while($running > 0);

        $result = $this->getBulkResult($jsonResponse);

        curl_multi_close($this->bulkHandler);

        $this->bulkHandler = null;
        $this->bulkRequests = [];

        return  $result;
    }



    private function getBulkResult($jsonResponse)
    {
        $result = [];

        foreach ($this->bulkRequests as $key => $ch) {
            $json = json_decode(curl_multi_getcontent($ch));

            if ($json === null) {
                throw new InvalidResponseException("No valid JSON in bulk request", 1);
            }

            $result[$key] = $json;
            curl_multi_remove_handle($this->bulkHandler, $ch);
        }

        return $result;
    }
}
