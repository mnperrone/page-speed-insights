<?php

namespace App\Http\Controllers;

use App\Http\Requests\PageSpeedRequest;
use App\Services\PageSpeedService;
use App\Exceptions\PageSpeedException;
use Illuminate\Http\JsonResponse;

class PageSpeedController extends Controller
{
    protected PageSpeedService $pageSpeedService;

    public function __construct(PageSpeedService $pageSpeedService)
    {
        $this->pageSpeedService = $pageSpeedService;
    }

    public function getMetrics(PageSpeedRequest $request): JsonResponse
    {
        
        $url = $request->input('url');
        $categories = $request->input('categories');
        $strategy = $request->input('strategy');

        try {
            $data = $this->pageSpeedService->getMetrics($url, $categories, $strategy);
            $metrics = $this->processMetrics($data);

            return response()->json(['success' => true, 'metrics' => $metrics]);
        } catch (PageSpeedException $e) { // Catch la excepción específica
            \Log::error('Error de PageSpeed:', ['error' => $e->getMessage(), 'errorCode' => $e->getApiErrorCode()]); // Loguea el error y el código de error de la API si existe
            return response()->json(['success' => false, 'error' => $e->getMessage(), 'api_error_code' => $e->getApiErrorCode()]); // Devuelve el mensaje de error y el código de error en la respuesta
        } catch (\Exception $e) { // Catch otras excepciones generales
            \Log::error('Error general:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => 'Ha ocurrido un error inesperado.']); // Mensaje genérico para el usuario
        }
    }

    private function processMetrics(array $data): array
    {
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
        return $metrics;
    }
}