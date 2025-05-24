<?php

namespace App\Http\Controllers;

use App\Http\Requests\PageSpeedRequest;
use App\Models\MetricHistoryRun;
use App\Models\Strategy;
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

    /**
     * Process the metrics data from the API
     *
     * @param array $data
     * @return array
     */
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

    public function getMetrics(PageSpeedRequest $request): JsonResponse
    {
        $url = $request->input('url');
        $categories = $request->input('categories');
        $strategy = $request->input('strategy');

        \Log::info('Solicitud recibida', [
            'url' => $url,
            'categories' => $categories,
            'strategy' => $strategy
        ]);

        try {
            $data = $this->pageSpeedService->getMetrics($url, $categories, $strategy);
            
            if (empty($data) || !isset($data['lighthouseResult'])) {
                throw new \Exception('La respuesta de la API de PageSpeed no es válida');
            }

            $processedData = $this->processMetrics($data);

            // Agregar datos adicionales al resultado
            $response = [
                'success' => true,
                'data' => [
                    'analysisUTCTimestamp' => $processedData['analysisUTCTimestamp'] ?? null,
                    'lighthouseVersion' => $processedData['lighthouseVersion'] ?? null,
                    'requestedUrl' => $processedData['requestedUrl'] ?? $url,
                    'finalUrl' => $processedData['finalUrl'] ?? $url,
                    'categories' => $processedData['categories'] ?? [],
                    'metrics' => $processedData['metrics'] ?? [],
                    'loadingExperience' => $processedData['loadingExperience'] ?? null,
                    'originLoadingExperience' => $processedData['originLoadingExperience'] ?? null,
                    'strategy' => strtolower($strategy) === 'mobile' ? 'mobile' : 'desktop',
                    'categories_list' => $categories
                ]
            ];

            \Log::info('Respuesta generada', ['response' => $response]);
            return response()->json($response);
        } catch (PageSpeedException $e) {
            $errorMessage = 'Error en el servicio de PageSpeed: ' . $e->getMessage();
            \Log::error($errorMessage, [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'api_error_code' => $e->getApiErrorCode(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => $errorMessage,
                'api_error_code' => $e->getApiErrorCode()
            ], 500);
            
        } catch (\Exception $e) {
            $errorMessage = 'Error inesperado: ' . $e->getMessage();
            \Log::error($errorMessage, [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => $errorMessage
            ], 500);
        }
    }

    public function saveResults(PageSpeedRequest $request): JsonResponse
    {
        try {
            $data = $request->all();
            
            // Log the entire request data for debugging
            Log::info('Save Results - Request Data:', [
                'all_data' => $data,
                'categories' => $data['categories'] ?? 'No categories',
                'categories_type' => gettype($data['categories'] ?? null),
                'categories_list' => $data['categories_list'] ?? 'No categories_list',
                'categories_list_type' => gettype($data['categories_list'] ?? null)
            ]);
            
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


            // Convert timestamp to MySQL format
            $timestamp = $data['analysisUTCTimestamp'] ?? now();
            if (is_string($timestamp)) {
                $timestamp = new \DateTime($timestamp);
            }
            $mysqlTimestamp = $timestamp->format('Y-m-d H:i:s');
            
            // Prepare data for saving
            $metricsData = [
                'url' => $data['url'],
                'strategy_id' => $strategy->id,
                'analysis_utc_timestamp' => $mysqlTimestamp,
                'lighthouse_version' => $data['lighthouseVersion'] ?? null,
                'final_url' => $data['finalUrl'] ?? $data['url'],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Add category metrics
            if (!empty($data['categories']) && is_array($data['categories'])) {
                // Mapear los nombres de categoría a los nombres de columna en la base de datos
                // Usar diferentes variaciones de los nombres de categoría para mayor compatibilidad
                $categoryMapping = [
                    'PERFORMANCE' => 'performance_metric',
                    'PERFORMANCE' => 'performance_metric',
                    'ACCESSIBILITY' => 'accessibility_metric',
                    'ACCESSIBILITY' => 'accessibility_metric',
                    'BEST_PRACTICES' => 'best_practices_metric',
                    'BEST-PRACTICES' => 'best_practices_metric',
                    'BESTPRACTICES' => 'best_practices_metric',
                    'SEO' => 'seo_metric',
                    'SEARCH-ENGINE-OPTIMIZATION' => 'seo_metric',
                    'PWA' => 'pwa_metric',
                    'PROGRESSIVE-WEB-APP' => 'pwa_metric',
                    'PROGRESSIVEWEBAPP' => 'pwa_metric'
                ];

                // Debug: Log the incoming categories data
                Log::info('Categories data received:', $data['categories']);

                foreach ($data['categories'] as $category => $categoryData) {
                    $originalCategory = $category;
                    $score = null;
                    
                    // Intentar obtener el puntaje de diferentes maneras
                    if (is_array($categoryData)) {
                        if (isset($categoryData['score'])) {
                            $score = $categoryData['score'];
                        } elseif (isset($categoryData[0]) && is_array($categoryData[0])) {
                            $score = $categoryData[0]['score'] ?? null;
                        }
                    } elseif (is_numeric($categoryData)) {
                        $score = $categoryData;
                    }
                    
                    if ($score !== null) {
                        // Normalizar el nombre de la categoría
                        $category = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $originalCategory));
                        
                        Log::info('Processing category:', [
                            'original' => $originalCategory,
                            'normalized' => $category,
                            'score' => $score,
                            'type' => gettype($score)
                        ]);
                        
                        // Buscar coincidencia flexible
                        $matched = false;
                        foreach ($categoryMapping as $pattern => $field) {
                            if (strpos($category, $pattern) !== false) {
                                $scoreValue = is_numeric($score) ? $score * 100 : null;
                                $metricsData[$field] = $scoreValue;
                                Log::info('Saving metric:', [
                                    'field' => $field, 
                                    'value' => $scoreValue,
                                    'from_category' => $originalCategory
                                ]);
                                $matched = true;
                                break;
                            }
                        }
                        
                        if (!$matched) {
                            Log::warning('Category not matched in mapping:', [
                                'original' => $originalCategory,
                                'normalized' => $category
                            ]);
                        }
                    }
                }
            }

            // Add detailed metrics
            if (!empty($data['metrics']) && is_array($data['metrics'])) {
                foreach ($data['metrics'] as $metric => $metricData) {
                    $field = strtolower(str_replace('.', '_', $metric));
                    if (is_array($metricData)) {
                        $metricsData[$field] = $metricData['numericValue'] ?? null;
                    }
                }
            }

            // Add loading experience data
            if (!empty($data['loadingExperience'])) {
                $loadingExp = $data['loadingExperience'];
                $metricsData['loading_experience_metric'] = $loadingExp['metric'] ?? null;
                $metricsData['loading_experience_category'] = $loadingExp['category'] ?? null;
                $metricsData['loading_experience_percentile'] = $loadingExp['percentile'] ?? null;
            }

            // Debug: Log the final metrics data before saving
            Log::info('Final metrics data before save:', $metricsData);
            
            // Validate we have at least one metric to save
            $hasMetrics = false;
            $metricFields = ['performance_metric', 'accessibility_metric', 'best_practices_metric', 'seo_metric', 'pwa_metric'];
            foreach ($metricFields as $field) {
                if (isset($metricsData[$field]) && $metricsData[$field] !== null) {
                    $hasMetrics = true;
                    break;
                }
            }
            
            if (!$hasMetrics) {
                throw new \Exception('No se encontraron métricas válidas para guardar');
            }
            
            // Guardar en la base de datos
            $metricHistory = MetricHistoryRun::create($metricsData);
            
            // Debug: Log the saved record
            Log::info('Metric saved:', $metricHistory->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Resultados guardados correctamente',
                'data' => $metricHistory->toArray()
            ]);
        } catch (\Exception $e) {
            Log::error('Error al guardar resultados:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Error al guardar los resultados: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the metrics history
     *
     * @return JsonResponse
     */
    public function getMetricsHistory(): JsonResponse
    {
        try {
            $metrics = MetricHistoryRun::with('strategy')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($metric) {
                    return [
                        'id' => $metric->id,
                        'url' => $metric->url,
                        'strategy' => $metric->strategy ? $metric->strategy->name : 'N/A',
                        'created_at' => $metric->created_at->toDateTimeString(),
                        'performance_metric' => $metric->performance_metric,
                        'accessibility_metric' => $metric->accessibility_metric,
                        'best_practices_metric' => $metric->best_practices_metric,
                        'seo_metric' => $metric->seo_metric,
                        'pwa_metric' => $metric->pwa_metric,
                    ];
                });

            return response()->json([
                'success' => true,
                'metrics' => $metrics
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching metrics history: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener el historial de métricas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a metric
     *
     * @param int $id
     * @return JsonResponse
     */
    public function deleteMetric($id): JsonResponse
    {
        try {
            $metric = MetricHistoryRun::findOrFail($id);
            $metric->delete();

            return response()->json([
                'success' => true,
                'message' => 'Métrica eliminada correctamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al eliminar la métrica:', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'error' => 'Error al eliminar la métrica: ' . $e->getMessage()
            ], 500);
        }
    }

}