<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\MetricHistoryRun;

class PageSpeedController extends Controller
{
    public function getMetrics(Request $request)
    {
        // Validar los datos recibidos
        $request->validate([
            'url' => 'required|url',
            'categories' => 'required|array|min:1',
            'strategy' => 'required|string|in:DESKTOP,MOBILE',
        ]);

        // Preparar los datos para la API
        $url = $request->input('url');
        $categories = implode('&category=', array_map('strtolower', $request->input('categories')));
        $strategy = $request->input('strategy');
        $apiKey = env('GOOGLE_API_KEY');

        $apiUrl = "https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url={$url}&key={$apiKey}&category={$categories}&strategy={$strategy}";

        // Llamada a la API usando Guzzle
        $client = new Client();
        try {
            $response = $client->get($apiUrl, [
                'verify' => false, 
            ]);
            $data = json_decode($response->getBody(), true);
            
            \Log::info('API Response:', $data);

            // Procesar las métricas principales
            $metrics = [];
            if (isset($data['lighthouseResult'])) {
                $lighthouse = $data['lighthouseResult']['audits'];
                $metrics = [
                    'First Contentful Paint' => $lighthouse['first-contentful-paint']['displayValue'] ?? 'No data',
                    'Speed Index' => $lighthouse['speed-index']['displayValue'] ?? 'No data',
                    'Time to Interactive' => $lighthouse['interactive']['displayValue'] ?? 'No data',
                    'First Meaningful Paint' => $lighthouse['first-meaningful-paint']['displayValue'] ?? 'No data',
                    'First CPU Idle' => $lighthouse['first-cpu-idle']['displayValue'] ?? 'No data',
                    'Estimated Input Latency' => $lighthouse['estimated-input-latency']['displayValue'] ?? 'No data',
                ];
            }

            return response()->json(['success' => true, 'metrics' => $metrics]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
