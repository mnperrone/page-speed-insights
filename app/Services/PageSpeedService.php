<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Exceptions\PageSpeedException;


class PageSpeedService
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getMetrics(string $url, array $categories, string $strategy)
    {
        $categories = implode('&category=', array_map('strtolower', $categories));
        $apiKey = env('GOOGLE_API_KEY');
        $apiUrl = "https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url={$url}&key={$apiKey}&category={$categories}&strategy={$strategy}";

        try {
            $response = $this->client->get($apiUrl, ['verify' => false]);
            dd(json_decode($response->getBody(), true));
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            \Log::error('Error en la llamada a la API:', ['error' => $e->getMessage()]);
            throw new PageSpeedException('Error al llamar a la API de PageSpeed', 500, $e); 
        }
    }
}
