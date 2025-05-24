<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PageSpeed Insights Analyzer</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap CSS and JS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/estilos.css', 'resources/js/app.js'])
    @endif
    <style>
        :root {
            --primary-color: #4285f4;
            --secondary-color: #34a853;
            --accent-color: #fbbc05;
            --danger-color: #ea4335;
            --light-bg: #f8f9fa;
            --dark-bg: #2c3e50;
            --text-muted: #6c757d;
            --border-radius: 8px;
            --box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background-color: var(--dark-bg) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 600;
            letter-spacing: 0.5px;
            color: white !important;
        }

        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .form-control, .form-select {
            padding: 0.75rem 1rem;
            border-radius: var(--border-radius);
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(66, 133, 244, 0.25);
        }

        .btn {
            padding: 0.6rem 1.5rem;
            border-radius: var(--border-radius);
            font-weight: 500;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.85rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #3367d6;
            border-color: #3367d6;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .score-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            font-weight: bold;
            color: white;
            margin: 0 auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .score-excellent {
            background: linear-gradient(135deg, #0f9d58, #34a853);
        }

        .score-good {
            background: linear-gradient(135deg, #f4b400, #fb8c00);
        }

        .score-poor {
            background: linear-gradient(135deg, #db4437, #ea4335);
        }

        footer {
            background-color: var(--dark-bg);
            color: white;
            padding: 2.5rem 0 1.5rem;
            margin-top: auto;
        }

        footer a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        footer a:hover {
            color: var(--primary-color);
            text-decoration: none;
        }


        @media (max-width: 768px) {
            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.8rem;
            }

            .navbar-brand {
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="{{ asset('images/favicon.ico') }}" alt="Logo" width="32" height="32" class="d-inline-block me-2">
                <span>PageSpeed Analyzer</span>
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-4 flex-grow-1">

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <ul class="nav nav-tabs card-header-tabs" id="mainTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="run-tab" data-bs-toggle="tab" data-bs-target="#run-metrics" type="button" role="tab" aria-controls="run-metrics" aria-selected="true">
                            <i class="fas fa-tachometer-alt me-2"></i>Analizar Sitio Web
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#metrics-history" type="button" role="tab" aria-controls="metrics-history" aria-selected="false">
                            <i class="fas fa-history me-2"></i>Historial
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-4">
                <div class="tab-content" id="mainTabsContent">
                    <!-- Run Metrics Tab -->
                    <div class="tab-pane fade show active" id="run-metrics" role="tabpanel" aria-labelledby="run-tab">
                        <form id="metricsForm" class="needs-validation" novalidate>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="url" class="form-label fw-bold">URL del Sitio Web</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                        <input type="url" class="form-control form-control-lg" id="url" name="url" placeholder="https://ejemplo.com" required>
                                        <button class="btn btn-primary" type="submit" id="submitBtn">
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                            <span class="btn-text">Analizar</span>
                                        </button>
                                    </div>
                                    <div id="urlError" class="invalid-feedback">Por favor ingrese una URL válida.</div>
                                </div>

                                <div class="col-12 mt-4">
                                    <label class="form-label fw-bold d-block mb-3">Categorías</label>
                                    <div class="row g-3">
                                        @foreach ([
                                            'PERFORMANCE' => ['Rendimiento', 'fas fa-tachometer-alt text-primary'],
                                            'ACCESSIBILITY' => ['Accesibilidad', 'fas fa-universal-access text-success'],
                                            'BEST_PRACTICES' => ['Mejores Prácticas', 'fas fa-check-circle text-warning'],
                                            'SEO' => ['SEO', 'fas fa-search text-info'],
                                            'PWA' => ['PWA', 'fas fa-mobile-alt text-purple']
                                        ] as $category => $data)
                                            <div class="col-md-4 col-6">
                                                <div class="form-check p-3 border rounded-3 h-100">
                                                    <input class="form-check-input category-checkbox" type="checkbox" name="categories[]" value="{{ $category }}" id="{{ $category }}">
                                                    <label class="form-check-label d-flex align-items-center" for="{{ $category }}">
                                                        <i class="{{ $data[1] }} me-2"></i>
                                                        {{ $data[0] }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div id="categoryError" class="text-danger mt-2" style="display: none;">Por favor, seleccione al menos una categoría</div>
                                    <div class="form-check mt-3">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                        <label class="form-check-label" for="selectAll">Seleccionar/Deseleccionar Todas</label>
                                    </div>
                                </div>

                                <div class="col-12 mt-3">
                                    <label for="strategy" class="form-label fw-bold">Estrategia</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-mobile-alt"></i></span>
                                        <select class="form-select" name="strategy" id="strategy" required>
                                            <option value="DESKTOP">Escritorio</option>
                                            <option value="MOBILE">Móvil</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Results Section -->
                    <div id="resultsContainer" style="display: none;">
                        <!-- Analysis Info Card -->
                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-white">
                                <h4 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Información del Análisis</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <h6 class="text-muted mb-1">URL Solicitada</h6>
                                            <p class="mb-0 text-truncate" id="requestedUrl">-</p>
                                        </div>
                                        <div class="mb-3">
                                            <h6 class="text-muted mb-1">URL Final</h6>
                                            <p class="mb-0 text-truncate" id="finalUrl">-</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <h6 class="text-muted mb-1">Versión de Lighthouse</h6>
                                            <p class="mb-0" id="lighthouseVersion">-</p>
                                        </div>
                                        <div>
                                            <h6 class="text-muted mb-1">Fecha del Análisis</h6>
                                            <p class="mb-0" id="analysisDate">-</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Categories Scores -->
                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-white">
                                <h4 class="mb-0"><i class="fas fa-chart-pie me-2 text-primary"></i>Puntuaciones por Categoría</h4>
                            </div>
                            <div class="card-body">
                                <div class="row g-4" id="categoriesContainer">
                                    <!-- Categories will be loaded here -->
                                </div>
                            </div>
                        </div>

                        <!-- Performance Metrics -->
                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-white">
                                <h4 class="mb-0"><i class="fas fa-tachometer-alt me-2 text-primary"></i>Métricas de Rendimiento</h4>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Métrica</th>
                                                <th class="text-end">Valor</th>
                                                <th class="text-center">Puntuación</th>
                                                <th class="text-end">Valor Numérico</th>
                                            </tr>
                                        </thead>
                                        <tbody id="metricsBody">
                                            <!-- Metrics will be loaded here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Loading Experience -->
                        <div class="card mb-4 shadow-sm" id="loadingExperienceSection" style="display: none;">
                            <div class="card-header bg-white">
                                <h4 class="mb-0"><i class="fas fa-chart-line me-2 text-primary"></i>Experiencia de Carga (Datos Reales)</h4>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Métrica</th>
                                                <th class="text-center">Categoría</th>
                                                <th class="text-end">Percentil</th>
                                            </tr>
                                        </thead>
                                        <tbody id="loadingExperienceBody">
                                            <!-- Loading experience data will be loaded here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Save Results Button -->
                        <div id="saveResultsContainer" class="text-center mb-4" style="display: none;">
                            <button id="saveBtn" class="btn btn-success btn-lg px-5">
                                <i class="fas fa-save me-2"></i>Guardar Resultados
                            </button>
                            <div id="saveStatus" class="mt-3"></div>
                        </div>
                    </div>

                    <!-- Error Modal -->
                    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title" id="errorModalLabel">Error</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-exclamation-triangle text-danger fa-2x me-3"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <p class="mb-0" id="errorModalText"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- History Tab -->
                <div class="tab-pane fade" id="metrics-history" role="tabpanel" aria-labelledby="history-tab">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h4 class="mb-0"><i class="fas fa-history me-2 text-primary"></i>Historial de Análisis</h4>
                        </div>
                        <div class="card-body">
                            <!-- Loading State -->
                            <div id="loadingHistory" class="text-center my-5 py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <p class="mt-3 text-muted">Cargando historial de análisis...</p>
                            </div>

                            <!-- Empty State -->
                            <div id="noMetricsMessage" class="text-center py-5" style="display: none;">
                                <div class="mb-4">
                                    <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                                </div>
                                <h4 class="mb-2">No hay análisis guardados</h4>
                                <p class="text-muted mb-0">Los análisis que guardes aparecerán aquí.</p>
                            </div>

                            <!-- History Content -->
                            <div id="metricsHistoryContainer" class="table-responsive">
                                <!-- History will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="mt-auto">
        <div class="container py-4">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0">© {{ date('Y') }} PageSpeed Analyzer. Todos los derechos reservados.</p>
                </div>
                <div class="col-md-6 text-center text-md-end mt-3 mt-md-0">
                    <div class="social-links">
                        <a href="https://www.linkedin.com/in/matiasperrone/" target="_blank" title="LinkedIn">
                            <i class="fab fa-linkedin-in" style="padding-right: 2em;"></i>
                        </a>
                        <a href="https://github.com/mnperrone" target="_blank" title="GitHub">
                            <i class="fab fa-github" style="padding-right: 2em;"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="text-center mt-3">
                <p class="mb-0 small text-muted">Desarrollado por Matías Perrone</p>
            </div>
        </div>
    </footer>

    <script>
        // Variable para almacenar la respuesta de las métricas
        let lastMetricsResponse = null;

        // Función para formatear la fecha
        function formatDate(dateString) {
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            };
            return new Date(dateString).toLocaleDateString('es-ES', options);
        }

        // Función para obtener el color según el puntaje
        function getScoreColor(score) {
            if (score >= 0.9) return 'score-excellent';
            if (score >= 0.5) return 'score-good';
            return 'score-poor';
        }

        // Función para formatear el valor de la métrica
        function formatMetricValue(value) {
            if (value === undefined || value === null) return 'N/A';
            if (typeof value === 'number') {
                // Si es un número, redondear a 2 decimales
                return value % 1 === 0 ? value : value.toFixed(2);
            }
            return value;
        }

        // Función para mostrar los resultados
        function displayResults(data) {
            console.log('Mostrando resultados con datos:', data);

            // Verificar si los datos son válidos
            if (!data) {
                console.error('No se recibieron datos para mostrar');
                $('#errorModalText').text('No se recibieron datos para mostrar');
                new bootstrap.Modal(document.getElementById('errorModal')).show();
                return;
            }

            // Mostrar información del análisis
            $('#requestedUrl').text(data.requestedUrl || 'N/A');
            $('#finalUrl').text(data.finalUrl || data.url || 'N/A');
            $('#lighthouseVersion').text(data.lighthouseVersion || 'N/A');
            $('#analysisDate').text(formatDate(data.analysisUTCTimestamp) || 'N/A');

            // Mostrar el contenedor de resultados
            $('#resultsContainer').show();

            // Mostrar puntuaciones por categoría
            const categoriesContainer = $('#categoriesContainer');
            categoriesContainer.empty();

            const categoryIcons = {
                'performance': 'fa-tachometer-alt',
                'accessibility': 'fa-universal-access',
                'best-practices': 'fa-check-circle',
                'seo': 'fa-search',
                'pwa': 'fa-mobile-alt'
            };

            const categoryNames = {
                'performance': 'Rendimiento',
                'accessibility': 'Accesibilidad',
                'best-practices': 'Mejores Prácticas',
                'seo': 'SEO',
                'pwa': 'PWA'
            };

            // Verificar si hay categorías para mostrar
            if (data.categories && typeof data.categories === 'object') {
                Object.entries(data.categories).forEach(([key, category]) => {
                    if (category && category.score !== null && category.score !== undefined) {
                        const score = Math.round(category.score); // Ya viene como porcentaje del controlador
                        const scoreDecimal = category.score / 100; // Convertir a decimal para getScoreColor
                        const colorClass = getScoreColor(scoreDecimal);
                        const icon = categoryIcons[key] || 'fa-chart-pie';
                        const name = categoryNames[key] || key;

                        categoriesContainer.append(`
                            <div class="col-md-4 col-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body text-center p-4">
                                        <div class="mb-3">
                                            <i class="fas ${icon} fa-2x text-muted"></i>
                                        </div>
                                        <h5 class="card-title mb-3">${name}</h5>
                                        <div class="score-circle ${colorClass} mb-3">
                                            ${score}
                                        </div>
                                        <p class="card-text text-muted small mb-0">${category.description || ''}</p>
                                    </div>
                                </div>
                            </div>
                        `);
                    }
                });
            }

            // Mostrar métricas de rendimiento
            const metricsBody = $('#metricsBody');
            metricsBody.empty();

            // Mostrar métricas de rendimiento si están disponibles
            if (data.metrics && typeof data.metrics === 'object') {
                // Definir el orden deseado de las métricas
                const metricOrder = [
                    'first-contentful-paint',
                    'largest-contentful-paint',
                    'cumulative-layout-shift',
                    'total-blocking-time',
                    'interactive',
                    'speed-index'
                ];

                // Ordenar las métricas según el orden definido
                const sortedMetrics = Object.entries(data.metrics).sort(([a], [b]) => {
                    const aIndex = metricOrder.indexOf(a);
                    const bIndex = metricOrder.indexOf(b);
                    return (aIndex === -1 ? 999 : aIndex) - (bIndex === -1 ? 999 : bIndex);
                });

                sortedMetrics.forEach(([metricKey, metric]) => {
                    if (metric) {
                        const score = metric.score !== null && metric.score !== undefined ?
                            Math.round(metric.score * 100) : 'N/A';
                        const displayValue = formatMetricValue(metric.displayValue || metric.title) || 'N/A';
                        const numericValue = formatMetricValue(metric.numericValue) || 'N/A';

                        metricsBody.append(`
                            <tr>
                                <td>${metric.title || metricKey}</td>
                                <td class="text-end">${displayValue}</td>
                                <td class="text-center">${score}</td>
                                <td class="text-end">${numericValue}</td>
                            </tr>
                        `);
                    }
                });
            } else {
                metricsBody.append(`
                    <tr>
                        <td colspan="4" class="text-center text-muted">No hay métricas disponibles</td>
                    </tr>
                `);
            }

            // Mostrar experiencia de carga si está disponible
            if (data.loadingExperience && typeof data.loadingExperience === 'object' && Object.keys(data.loadingExperience).length > 0) {
                $('#loadingExperienceSection').show();
                const loadingExpBody = $('#loadingExperienceBody');
                loadingExpBody.empty();

                Object.entries(data.loadingExperience).forEach(([key, metric]) => {
                    if (metric) {
                        loadingExpBody.append(`
                            <tr>
                                <td>${key}</td>
                                <td class="text-center">${metric.category || 'N/A'}</td>
                                <td class="text-end">${metric.percentile || 'N/A'}</td>
                            </tr>
                        `);
                    }
                });
            } else {
                $('#loadingExperienceSection').hide();
            }

            // Mostrar el botón de guardar si hay datos válidos
            $('#saveResultsContainer').show();

            // Mostrar los resultados
            $('#resultsContainer').show();

            // Desplazarse suavemente a los resultados
            $('html, body').animate({
                scrollTop: $('#resultsContainer').offset().top - 20
            }, 500);
        }

        // This function has been moved to a single implementation below

        $(document).ready(function() {
            // Manejar el envío del formulario
            $('#metricsForm').on('submit', function(e) {
                e.preventDefault();

                const $form = $(this);
                const $submitBtn = $('#submitBtn');
                const $spinner = $submitBtn.find('.spinner-border');
                const $btnText = $submitBtn.find('.btn-text');

                // Validar categorías
                if ($('input[name="categories[]"]:checked').length === 0) {
                    $('#categoryError').show();
                    return false;
                }

                // Mostrar estado de carga
                $submitBtn.prop('disabled', true);
                $btnText.text('Analizando...');
                $spinner.removeClass('d-none');
                $spinner.css('display', 'inline-block');

                // Obtener datos del formulario
                const formData = {
                    url: $('#url').val(),
                    categories: $('input[name="categories[]"]:checked').map(function() {
                        return $(this).val();
                    }).get(),
                    strategy: $('#strategy').val(),
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

                // Ocultar resultados anteriores
                $('#resultsContainer').hide();

                // Enviar solicitud AJAX
                $.ajax({
                    url: '{{ route("getMetrics") }}',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        console.log('Respuesta recibida:', response);

                        // Verificar si la respuesta es exitosa
                        if (response && response.success && response.data) {
                            // Guardar la respuesta completa para usarla después
                            lastMetricsResponse = response.data;

                            // Mostrar los resultados
                            displayResults(response.data);

                            // Mostrar el botón de guardar
                            $('#saveResultsContainer').show();

                            // Mostrar el contenedor de resultados
                            $('#resultsContainer').show();
                        } else {
                            // Manejar error en la respuesta
                            const errorMsg = response && response.error
                                ? response.error
                                : 'La respuesta del servidor no es válida o está vacía';

                            console.error('Error en la respuesta:', errorMsg);

                            // Mostrar el error en el modal
                            $('#errorModalText').text(errorMsg);
                            const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                            errorModal.show();

                            // Mostrar el error en la interfaz también
                            const $errorContainer = $('#errorContainer');
                            if ($errorContainer.length) {
                                $errorContainer.html(`
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <strong>Error:</strong> ${errorMsg}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                `);
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error en la petición AJAX:', status, error);
                        let errorMessage = 'Ocurrió un error al procesar la solicitud.';

                        try {
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                errorMessage = xhr.responseJSON.error;
                            } else if (xhr.responseText) {
                                // Intentar extraer el mensaje de error del HTML si la respuesta no es JSON
                                const errorMatch = xhr.responseText.match(/<title>([^<]+)</) ||
                                                xhr.responseText.match(/<pre>([\s\S]*?)<\/pre>/) ||
                                                [];
                                if (errorMatch[1]) {
                                    errorMessage = errorMatch[1].trim();
                                }
                            }
                        } catch (e) {
                            console.error('Error al procesar la respuesta de error:', e);
                        }

                        $('#errorModalText').html(errorMessage);
                        new bootstrap.Modal(document.getElementById('errorModal')).show();
                    },
                    complete: function() {
                        $submitBtn.prop('disabled', false);
                        $btnText.text('Analizar');
                        $spinner.addClass('d-none').css('display', 'none');
                    }
                });
            });

            // Manejar el botón de guardar resultados
            $(document).on('click', '#saveBtn', function() {
                if (!lastMetricsResponse) return;

                const $btn = $(this);
                const $status = $('#saveStatus');

                $btn.prop('disabled', true);
                $btn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Guardando...');
                $status.html('');

                $.ajax({
                    url: '{{ route("saveResults") }}',
                    method: 'POST',
                    data: {
                        data: JSON.stringify(lastMetricsResponse.data),
                        url: lastMetricsResponse.data.requestedUrl,
                        strategy: $('#strategy').val(),
                        categories_list: $('input[name="categories[]"]:checked').map(function() {
                            return $(this).val();
                        }).get().join(','),
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            $status.html('<div class="alert alert-success">Resultados guardados correctamente.</div>');
                            // Recargar el historial
                            if ($('#metrics-history').hasClass('active')) {
                                loadMetricsHistory();
                            }
                        } else {
                            $status.html(`<div class="alert alert-danger">${response.error || 'Error al guardar los resultados.'}</div>`);
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Error al guardar los resultados.';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;
                        }
                        $status.html(`<div class="alert alert-danger">${errorMessage}</div>`);
                    },
                    complete: function() {
                        $btn.html('<i class="fas fa-save me-2"></i>Guardar Resultados');
                        $btn.prop('disabled', false);
                    }
                });
            });

            // Manejar el botón de seleccionar/deseleccionar todas las categorías
            $('#selectAll').on('change', function() {
                $('.category-checkbox').prop('checked', $(this).prop('checked'));
                $('#categoryError').hide();
            });

            // Manejar cambios en las casillas de verificación individuales
            $('.category-checkbox').on('change', function() {
                if (!$('#selectAll').is(':checked') && $('.category-checkbox:checked').length === $('.category-checkbox').length) {
                    $('#selectAll').prop('checked', true);
                } else if ($('#selectAll').is(':checked') && $('.category-checkbox:checked').length < $('.category-checkbox').length) {
                    $('#selectAll').prop('checked', false);
                }
                $('#categoryError').hide();
            });

            // Cargar el historial cuando se muestre la pestaña
            $('[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                if (e.target.getAttribute('aria-controls') === 'metrics-history') {
                    loadMetricsHistory();
                }
            });

            // Manejar clic en ver métrica del historial
            $(document).on('click', '.view-metric', function() {
                const id = $(this).data('id');
                // Aquí puedes implementar la lógica para mostrar los detalles de una métrica guardada
                alert(`Mostrar detalles de la métrica con ID: ${id}`);
            });

            // Inicializar tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>

    <style>
        .score-circle {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            color: white;
            margin: 10px auto;
        }

        .score-excellent {
            background-color: #0cc478; /* Verde para puntuaciones altas */
        }

        .score-good {
            background-color: #a3c14a; /* Verde amarillento para buenas puntuaciones */
        }

        .score-average {
            background-color: #f5a623; /* Naranja para puntuaciones medias */
        }

        .score-poor {
            background-color: #e74c3c; /* Rojo para puntuaciones bajas */
        }

        .badge {
            font-size: 0.9em;
            padding: 5px 10px;
            border-radius: 10px;
        }

        .card {
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .card-header {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .table th {
            background-color: #f8f9fa;
        }
    </style>

    <style>
        #saveBtn {
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 4px;
        }

        #saveBtn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        #saveStatus {
            min-height: 24px;
        }

        .success-message {
            color: #0c5460;
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            padding: 8px 15px;
            border-radius: 4px;
            display: inline-block;
        }

        .error-message {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 8px 15px;
            border-radius: 4px;
            display: inline-block;
        }
    </style>
    <script>
        // Función para eliminar una métrica
        let metricToDelete = null; // Variable global para almacenar el ID a eliminar
        
        function deleteMetric() {
            if (!metricToDelete) return;
            
            const $row = $(`#metric-row-${metricToDelete}`);
            const $deleteBtn = $row.find('.delete-metric');
            
            // Deshabilitar el botón y mostrar spinner
            $deleteBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Eliminando...');
            
            $.ajax({
                url: `/metrics-history/${metricToDelete}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $row.fadeOut(400, function() {
                            $(this).remove();
                            // Verificar si la tabla está vacía después de la eliminación
                            if ($('#metricsHistoryContainer tbody tr').length === 0) {
                                $('#noMetricsMessage').show();
                            }
                        });
                    } else {
                        showError('Error al eliminar la métrica: ' + (response.error || 'Error desconocido'));
                        $deleteBtn.html('<i class="fas fa-trash"></i>').prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    const errorMsg = xhr.responseJSON?.error || 'Error en la solicitud';
                    showError('Error al eliminar la métrica: ' + errorMsg);
                    $deleteBtn.html('<i class="fas fa-trash"></i>').prop('disabled', false);
                }
            });
        }

        // Función para cargar el historial de métricas
        function loadMetricsHistory() {
            const $loading = $('#loadingHistory');
            const $noMetrics = $('#noMetricsMessage');
            const $container = $('#metricsHistoryContainer');

            $loading.show();
            $noMetrics.hide();
            $container.empty();

            $.ajax({
                url: '{{ route('getMetricsHistory') }}',
                method: 'GET',
                success: function(response) {
                    $loading.hide();

                    if (response.success && response.metrics && response.metrics.length > 0) {
                        let html = `
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>URL</th>
                                        <th>Estrategia</th>
                                        <th>Fecha</th>
                                        <th>Performance</th>
                                        <th>Accesibilidad</th>
                                        <th>Mejores Prácticas</th>
                                        <th>SEO</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>`;

                        response.metrics.forEach(metric => {
                            html += `
                                <tr id="metric-row-${metric.id}">
                                    <td>${metric.url}</td>
                                    <td>${metric.strategy}</td>
                                    <td>${formatDate(metric.created_at)}</td>
                                    <td>${metric.performance_metric ? Math.round(metric.performance_metric) + '%' : 'N/A'}</td>
                                    <td>${metric.accessibility_metric ? Math.round(metric.accessibility_metric) + '%' : 'N/A'}</td>
                                    <td>${metric.best_practices_metric ? Math.round(metric.best_practices_metric) + '%' : 'N/A'}</td>
                                    <td>${metric.seo_metric ? Math.round(metric.seo_metric) + '%' : 'N/A'}</td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-danger delete-metric" data-id="${metric.id}" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>`;
                        });

                        html += `
                                </tbody>
                            </table>`;

                        $container.html(html);

                        // Manejador para el botón de eliminar
                        $(document).off('click', '.delete-metric').on('click', '.delete-metric', function(e) {
                            e.preventDefault();
                            metricToDelete = $(this).data('id');
                            
                            // Mostrar el modal
                            const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
                            modal.show();
                        });
                    } else {
                        $noMetrics.show();
                    }
                },
                error: function(xhr) {
                    $loading.hide();
                    $noMetrics.html('<div class="alert alert-danger">Error al cargar el historial. Intente nuevamente más tarde.</div>').show();
                    console.error('Error loading metrics history:', xhr.responseText);
                }
            });
        }
        // Función para determinar la clase de puntuación
        function getScoreClass(score) {
            // Asegurarse de que el puntaje sea un número válido
            const numericScore = Number(score);
            if (isNaN(numericScore)) return 'score-poor';

            if (numericScore >= 90) return 'score-excellent';
            if (numericScore >= 70) return 'score-good';
            if (numericScore >= 50) return 'score-average';
            return 'score-poor';
        }
        $(document).ready(function() {
            // Agregar evento para cambiar entre pestañas
            $('.tabs a').on('click', function(e) {
                e.preventDefault();
                var tabId = $(this).attr('href');
                $('.tabs li').removeClass('active');
                $(this).parent().addClass('active');
                $('.tab-content div').removeClass('active');
                $(tabId).addClass('active');

                // Load metrics history when the Metrics History tab is clicked
                if (tabId === '#metrics-history') {
                    loadMetricsHistory();
                }
            });

            $('#metricsForm').on('submit', function (e) {
                e.preventDefault();

                // **Validación de categorías**
                if ($('input[name="categories[]"]:checked').length === 0) {
                    // **Mostrar mensaje de error de categoría**
                    $('#categoryError').show();
                    return; // Detener el envío del formulario
                } else {
                    // **Ocultar mensaje de error si hay categorías seleccionadas**
                    $('#categoryError').hide();
                }

                // Obtener referencias a los elementos
                const $submitButton = $(this).find('button[type="submit"]');
                const $spinner = $submitButton.find('.spinner-border');
                const $btnText = $submitButton.find('.btn-text');

                // Deshabilitar el botón y mostrar el indicador de carga
                $submitButton.prop('disabled', true);
                $btnText.text('Analizando...');
                $spinner.removeClass('d-none');
                $spinner.css('display', 'inline-block');

                // Obtener valores del formulario
                const url = $('#url').val();
                const categories = $('input[name="categories[]"]:checked').map(function () { return this.value; }).get();
                const strategy = $('#strategy').val();

                $.ajax({
                    url: '{{ route('getMetrics') }}',
                    method: 'POST',
                    data: {
                        url: url,
                        categories: categories,
                        strategy: strategy,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        console.log('Respuesta recibida:', response);
                        $('#results').html('');
                        // Restaurar el botón y ocultar el indicador de carga
                        $submitButton.prop('disabled', false);
                        $spinner.addClass('d-none').css('display', 'none');
                        $btnText.text('Analizar');

                        if (response.success) {
                            // Guardar la respuesta completa para usarla después
                            lastMetricsResponse = response.data;

                            // Mostrar los resultados usando la función displayResults
                            displayResults(response.data);

                        } else {
                            $('#results').html(`<div class="alert alert-danger">Error: ${response.error || 'Error desconocido'}</div>`);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                        // Restaurar el botón y ocultar el indicador de carga
                        $submitButton.prop('disabled', false);
                        $spinner.addClass('d-none').css('display', 'none');
                        $btnText.text('Analizar');

                        // Mostrar mensaje de error
                        $('#errorModalText').html('Ocurrió un error al obtener los datos. Por favor, inténtelo de nuevo.');
                        var errorModal = new bootstrap.Modal(document.getElementById('errorModal'), {
                            backdrop: 'static',
                            keyboard: false
                        });
                        errorModal.show();
                    }
                });
            });

            // Manejador para el botón de guardar
            $('#saveBtn').on('click', function() {
                if (!lastMetricsResponse) {
                    showError('No hay resultados para guardar');
                    return;
                }

                const $btn = $(this);
                const $status = $('#saveStatus');

                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
                $status.empty();

                // Obtener los valores actuales del formulario
                const currentUrl = $('#url').val();
                // Asegurarse de que la estrategia esté en mayúsculas
                const currentStrategy = $('#strategy').val().toUpperCase();
                console.log('Valor de la estrategia seleccionada:', currentStrategy);
                const currentCategories = $('input[name="categories[]"]:checked').map(function() {
                    return this.value;
                }).get();

                // Preparar los datos para guardar
                const metricsData = lastMetricsResponse;

                // Crear un objeto con las categorías y sus scores
                const categoriesData = {};
                if (metricsData.categories) {
                    Object.entries(metricsData.categories).forEach(([category, data]) => {
                        categoriesData[category] = {
                            score: data.score / 100  // Convertir de porcentaje a decimal
                        };
                    });
                }

                const saveData = {
                    ...metricsData,  // Datos de métricas
                    categories: categoriesData,  // Datos de categorías con scores
                    categories_list: currentCategories,  // Lista de categorías seleccionadas
                    url: currentUrl,  // URL actual del formulario
                    strategy: currentStrategy,  // Estrategia actual del formulario
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

                console.log('Datos finales a enviar:', saveData);
                console.log('Categorías procesadas:', JSON.stringify(categoriesData, null, 2));

                // Enviar la solicitud al servidor
                $.ajax({
                    url: '{{ route('saveResults') }}',
                    method: 'POST',
                    data: saveData,
                    success: function(response) {
                        if (response.success) {
                            $status.html('<div class="success-message"><i class="fas fa-check-circle"></i> ' + (response.message || 'Resultados guardados correctamente') + '</div>');
                            // Deshabilitar el botón después de guardar exitosamente
                            $btn.prop('disabled', true).html('<i class="fas fa-check"></i> Guardado');
                        } else {
                            showError(response.error || 'Error desconocido al guardar los resultados');
                            $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Guardar Resultados');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error en la solicitud:', status, error);
                        console.error('Respuesta del servidor:', xhr.responseJSON);

                        let errorMessage = 'Error al guardar los resultados';
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            if (xhr.responseJSON.errors) {
                                errorMessage += ' ' + JSON.stringify(xhr.responseJSON.errors);
                            }
                        }
                        showError(errorMessage);
                        $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Guardar Resultados');
                    }
                });
            });

            // Función para mostrar mensajes de error
            function showError(message) {
                $('#saveStatus').html('<div class="error-message"><i class="fas fa-exclamation-circle"></i> ' + message + '</div>');
            }

            $('#selectAll').on('change', function() {
                const isChecked = $(this).prop('checked');
                $('input[name="categories[]"]').prop('checked', isChecked);
            });

            function enviarFormulario() {
                $('#metricsForm').submit();
            }
        });
        
    </script>
    
    <!-- Modal de Confirmación de Eliminación -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar este registro?</p>
                    <p class="text-muted small">Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Sí, Eliminar</button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Manejador para el botón de eliminar
        $(document).on('click', '.delete-metric', function(e) {
            e.preventDefault();
            metricToDelete = $(this).data('id');
            const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            modal.show();
            return false;
        });
        
        // Manejador para el botón de confirmación
        $('#confirmDeleteBtn').on('click', function() {
            if (metricToDelete) {
                deleteMetric();
            }
            const modal = bootstrap.Modal.getInstance(document.getElementById('deleteConfirmModal'));
            modal.hide();
            return false;
        });
    </script>
</div>
</body>
</html>
