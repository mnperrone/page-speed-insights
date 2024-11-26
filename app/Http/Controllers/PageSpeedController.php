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
        $categories = implode('&category=', $request->input('categories'));
        $strategy = $request->input('strategy');

        // API Key (usa una variable de entorno)
        $apiKey = env('GOOGLE_API_KEY');

        // Endpoint de la API
        $apiUrl = "https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url={$url}&key={$apiKey}&category={$categories}&strategy={$strategy}";

        // Llamada a la API usando Guzzle
        $client = new Client();
        try {
            $response = $client->get($apiUrl, [
                'verify' => false,  // Desactiva la verificación del certificado SSL
            ]);
            $data = json_decode($response->getBody(), true);

            // Procesar los resultados de las métricas
            $metrics = [];
            foreach ($request->input('categories') as $category) {
                $metrics[$category] = $data['lighthouseResult']['categories'][$category]['score'] ?? null;
            }

            return response()->json(['success' => true, 'metrics' => $metrics]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
