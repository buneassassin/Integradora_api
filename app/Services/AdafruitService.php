<?php

namespace App\Services;

use GuzzleHttp\Client;

class AdafruitService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://io.adafruit.com/api/v2/',
            'headers' => [
                'X-AIO-Key' => 'aio_yjbO36VNKQgCA5W3q8OncHNrc4nR', 
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function getFeedData($feedName)
    {
        $username = 'Treckersillo';
        $response = $this->client->get("$username/feeds/$feedName");
        return json_decode($response->getBody()->getContents(), true);
    }
}
