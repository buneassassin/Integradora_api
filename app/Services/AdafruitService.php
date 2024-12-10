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
        $this->username = 'Treckersillo';
        $this->apiKey = 'aio_yjbO36VNKQgCA5W3q8OncHNrc4nR';
        
        $this->baseUrl = "https://io.adafruit.com/api/v2/{$this->username}/";
    }
    public function enviardatos($feedName, $valor)
    {
        $client = new \GuzzleHttp\Client();
        $response = Http::withHeaders([
            'X-AIO-Key' => $this->apiKey, 
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}feeds/{$feedName}/data", [
            'value' => $valor, 
        ]);
        if ($response->successful()) {
            return "Dato enviado correctamente al feed {$feedName}.";
        } else {
            return "Error al enviar el dato: " . $response->body();
        }
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
