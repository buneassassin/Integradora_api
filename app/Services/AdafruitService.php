<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AdafruitService
{
    protected $baseUrl;
    protected $apiKey;
    protected $username;

    public function __construct()
    {
        $this->username = env('ADAFRUIT_USERNAME');
        $this->apiKey = env('ADAFRUIT_API_KEY');
        
        $this->baseUrl = "https://io.adafruit.com/api/v2/{$this->username}/";
    }

    public function getFeedData($feedName)
    {
        $response = Http::withHeaders([
            'X-AIO-Key' => $this->apiKey
        ])->get("{$this->baseUrl}feeds/{$feedName}");

        if ($response->successful()) {
            return $response->json();
        }

        return [
            'error' => 'No se pudo acceder al Adafruit',
            'status' => $response->status(),
            'details' => $response->body()
        ];
    }
}
