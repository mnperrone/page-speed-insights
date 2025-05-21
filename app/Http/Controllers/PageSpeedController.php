<?php

namespace App\Http\Controllers;

use App\Http\Requests\PageSpeedRequest;
use App\Models\MetricHistoryRun;
use App\Services\PageSpeedService;
use App\Exceptions\PageSpeedException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            $processedData = $this->processMetrics($data);

            // Agregar datos adicionales al resultado
            $processedData['strategy'] = strtolower($strategy) === 'mobile' ? 'mobile' : 'desktop';
            $processedData['categories_list'] = $categories;

            return response()->json([
                'success' => true,
                'analysisUTCTimestamp' => $processedData['analysisUTCTimestamp'],
                'lighthouseVersion' => $processedData['lighthouseVersion'],
                'requestedUrl' => $processedData['requestedUrl'],
                'finalUrl' => $processedData['finalUrl'],
                'categories' => $processedData['categories'],
                'metrics' => $processedData['metrics'],
                'loadingExperience' => $processedData['loadingExperience'],
                'originLoadingExperience' => $processedData['originLoadingExperience'] ?? null,
                'strategy' => $processedData['strategy'],
                'categories_list' => $processedData['categories_list']
            ]);
        } catch (PageSpeedException $e) { // Catch la excepción específica
            \Log::error('Error de PageSpeed:', ['error' => $e->getMessage(), 'errorCode' => $e->getApiErrorCode()]); // Loguea el error y el código de error de la API si existe
            return response()->json(['success' => false, 'error' => $e->getMessage(), 'api_error_code' => $e->getApiErrorCode()]); // Devuelve el mensaje de error y el código de error en la respuesta
        } catch (\Exception $e) { // Catch otras excepciones generales
            \Log::error('Error general:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => 'Ha ocurrido un error inesperado.']); // Mensaje genérico para el usuario
        }
    }

    public function saveResults(PageSpeedRequest $request): JsonResponse
    {
        try {
            $data = $request->all();
            
            // Validar que los datos requeridos estén presentes
            if (empty($data['url']) || empty($data['strategy']) || empty($data['categories_list'])) {
                throw new \Exception('Datos incompletos para guardar los resultados');
            }

            // Asegurarse de que la estrategia esté en mayúsculas
            $strategyName = strtoupper($data['strategy']);
            
            // Verificar si la estrategia existe, si no, crearla
            $strategy = DB::table('strategies')
                ->where('name', $strategyName)
                ->first();

            if (!$strategy) {
                // Crear la estrategia si no existe
                $strategyId = DB::table('strategies')->insertGetId([
                    'name' => $strategyName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $strategy = (object)['id' => $strategyId];
            }


            // Preparar los datos para guardar
            $metricsData = [
                'url' => $data['url'],
                'strategy_id' => $strategy->id,
                'analysis_utc_timestamp' => $data['analysisUTCTimestamp'] ?? now(),
                'lighthouse_version' => $data['lighthouseVersion'] ?? null,
                'final_url' => $data['finalUrl'] ?? $data['url'],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Agregar métricas de categorías
            if (!empty($data['categories'])) {
                foreach ($data['categories'] as $category => $categoryData) {
                    $field = strtolower($category) . '_metric';
                    $metricsData[$field] = $categoryData['score'] * 100; // Convertir a porcentaje
                }
            }

            // Agregar métricas detalladas
            if (!empty($data['metrics'])) {
                foreach ($data['metrics'] as $metric => $metricData) {
                    $field = strtolower(str_replace('.', '_', $metric));
                    $metricsData[$field] = $metricData['numericValue'] ?? null;
                }
            }

            // Agregar datos de experiencia de carga
            if (!empty($data['loadingExperience'])) {
                $loadingExp = $data['loadingExperience'];
                $metricsData['loading_experience_metric'] = $loadingExp['metric'] ?? null;
                $metricsData['loading_experience_category'] = $loadingExp['category'] ?? null;
                $metricsData['loading_experience_percentile'] = $loadingExp['percentile'] ?? null;
            }

            // Guardar en la base de datos
            $metricHistory = MetricHistoryRun::create($metricsData);

            return response()->json([
                'success' => true,
                'message' => 'Resultados guardados correctamente',
                'data' => $metricHistory
            ]);

        } catch (\Exception $e) {
            Log::error('Error al guardar resultados:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Error al guardar los resultados: ' . $e->getMessage()
            ], 500);
        }
    }

    private function processMetrics(array $data): array
    {
        $result = [
            'analysisUTCTimestamp' => $data['analysisUTCTimestamp'] ?? null,
            'lighthouseVersion' => $data['lighthouseResult']['lighthouseVersion'] ?? null,
            'requestedUrl' => $data['lighthouseResult']['requestedUrl'] ?? null,
            'finalUrl' => $data['lighthouseResult']['finalUrl'] ?? null,
            'categories' => [],
            'metrics' => [],
            'loadingExperience' => null,
        ];

        // Procesar categorías
        if (isset($data['lighthouseResult']['categories'])) {
            foreach ($data['lighthouseResult']['categories'] as $categoryId => $category) {
                $result['categories'][$categoryId] = [
                    'title' => $category['title'],
                    'score' => $category['score'] * 100, // Convertir a porcentaje
                ];
            }
        }

        // Procesar métricas principales (Core Web Vitals)
        if (isset($data['lighthouseResult']['audits'])) {
            $audits = $data['lighthouseResult']['audits'];
            
            // Core Web Vitals
            $coreMetrics = [
                'first-contentful-paint' => 'First Contentful Paint (FCP)',
                'largest-contentful-paint' => 'Largest Contentful Paint (LCP)',
                'cumulative-layout-shift' => 'Cumulative Layout Shift (CLS)',
                'total-blocking-time' => 'Total Blocking Time (TBT)',
                'interactive' => 'Time to Interactive (TTI)',
                'speed-index' => 'Speed Index',
                'total-byte-weight' => 'Total Byte Weight',
            ];

            foreach ($coreMetrics as $metricId => $metricName) {
                if (isset($audits[$metricId])) {
                    $result['metrics'][$metricId] = [
                        'title' => $metricName,
                        'displayValue' => $audits[$metricId]['displayValue'] ?? null,
                        'score' => $audits[$metricId]['score'] ?? null,
                        'numericValue' => $audits[$metricId]['numericValue'] ?? null,
                        'scoreDisplayMode' => $audits[$metricId]['scoreDisplayMode'] ?? null,
                    ];
                }
            }
        }

        // Procesar loading experience (datos de campo)
        if (isset($data['loadingExperience']['metrics'])) {
            $result['loadingExperience'] = [];
            foreach ($data['loadingExperience']['metrics'] as $metricId => $metric) {
                $result['loadingExperience'][$metricId] = [
                    'category' => $metric['category'] ?? null,
                    'percentile' => $metric['percentile'] ?? null,
                    'distributions' => $metric['distributions'] ?? null,
                ];
            }
        }

        // Agregar información del origin loading experience si está disponible
        if (isset($data['originLoadingExperience']['metrics'])) {
            $result['originLoadingExperience'] = [];
            foreach ($data['originLoadingExperience']['metrics'] as $metricId => $metric) {
                $result['originLoadingExperience'][$metricId] = [
                    'category' => $metric['category'] ?? null,
                    'percentile' => $metric['percentile'] ?? null,
                ];
            }
        }

        return $result;
    }
}