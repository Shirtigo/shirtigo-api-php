<?php

namespace Shirtigo\ApiClient;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

class ApiClient {
    /** @var Client */
	protected $client;

    public function __construct(string $api_key, string $base_url="https://cockpit.shirtigo.de/api/")
    {
	// Validate base URL
	if (substr($base_url, -1) !== '/' ) {
            throw new \InvalidArgumentException('Invalid API base URL. The URL has to end with a backslash.');
        }
	    
    	// initialize GuzzleHttp client
        $this->client = new Client([
            'verify' => false,
            'base_uri' => $base_url,
            'headers' => [
            	'User-Agent' => 'Shirtigo Cockpit Guzzle-PHP REST API Client',
            	'Authorization' => 'Bearer ' . $api_key,
            ]
        ]);
    }

    protected function request(string $url, string $method="GET", array $data=[], array $params=[], array $files=[]) : ?\stdClass
    {
        try {
            $options = [
                'json' => $data,
                'query' => $params
            ];

            if (count($files) > 0) {
                unset($options['json']);
                $options['multipart'] = [];
                foreach ($files as $key => $value) {
                    $options['multipart'][] = [
                        'name' => $key,
                        'contents' => $value,
                    ];
                }
            }

        	$response = $this->client->request($method, $url, $options);

        } catch (BadResponseException $e) {
        	// convert exception into PHP RuntimeException with the correct error message
            $status = $e->getResponse()->getStatusCode();
        	$body = $e->getResponse()->getBody();

        	if ($status == 401) {
        	    throw new \RuntimeException('Authentication error. Check whether the provided API key is valid.');
            } else if ($status == 405) {
                throw new \RuntimeException("{$method} is not valid a valid HTTP verb for the '{$url}' endpoint.");
            } else if ($status == 422) {
        	    $json = json_decode($body, true);
        	    $message = isset($json['errors']) ? json_encode($json['errors']) : $json['message'];
        	    throw new \RuntimeException("Input validation failed: {$message}");
            } else {
                $json = json_decode($body, true);
                if (!is_null($json) && isset($json['message'])) {
                    $message = $json['message'];
                    throw new \RuntimeException("Endpoint returned HTTP status {$status}: {$message}");
                } else {
                    throw new \RuntimeException("Endpoint returned unhandled HTTP status {$status}");
                }
            }
        }

    	$response_data = json_decode($response->getBody(),false);

    	return $response_data;
    }

    public function get(string $url, array $params=[]) : ?\stdClass
    {
        return $this->request($url, 'GET', [], $params);
    }

    public function post(string $url, array $data=[], array $params=[], array $files=[]) : ?\stdClass
    {
        return $this->request($url, 'POST', $data, $params, $files);
    }

    public function put(string $url, array $data=[], array $params=[], array $files=[]) : ?\stdClass
    {
        return $this->request($url, 'PUT', $data, $params, $files);
    }

   	public function delete(string $url, array $params=[]) : ?\stdClass
   	{
        return $this->request($url, 'DELETE', [], $params);
   	}
}
