<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Google PageSpeed Insights</title>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap CSS and JS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/estilos.css', 'resources/js/app.js'])
    @endif
</head>
<body>
    <h1>Google Api PageSpeed Insights</h1>

    <div class="tab-container new-tabs">
        <ul class="tabs">
            <li class="active"><a href="#run-metrics">Run Metrics</a></li>
            <li><a href="#metrics-history">Metrics History</a></li>
        </ul>
        <div class="tab-content">
            <div id="run-metrics" class="tab-pane active">
                <div class="form-container">
                    <form id="metricsForm">
                        <label for="url">URL:</label>
                        <input type="url" id="url" name="url" placeholder="https://example.com" required>
                        <div id="urlError" class="error" style="display: none;">Please enter a valid URL.</div>
                        <div>
                            <label class="categories-label">Categories</label>
                            <div class="checkbox-group">
                                @foreach (['ACCESSIBILITY', 'BEST_PRACTICES', 'PERFORMANCE', 'PWA', 'SEO'] as $category)
                                    <div>
                                        <input type="checkbox" name="categories[]" value="{{ $category }}" id="{{ $category }}">
                                        <label for="{{ $category }}">{{ $category }}</label>
                                    </div>
                                @endforeach
                            </div>
                            <div id="categoryError" class="error" style="display: none;">Por favor, seleccione al menos una categoría</div>
                            <div class="check-all-container">
                                <input type="checkbox" id="selectAll">
                                <label for="selectAll">Check - Uncheck All</label>
                            </div>
                        </div>

                        <div>
                            <div class="strategy-container">
                                <label for="strategy" class="strategy-label">Strategy</label>
                                <select name="strategy" id="strategy" required>
                                    <option value="DESKTOP">Desktop</option>
                                    <option value="MOBILE">Mobile</option>
                                </select>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-chart-line"></i> Obtener Métricas
                                </button>
                                <div id="loading" class="ms-2" style="display: none; vertical-align: middle;">
                                    <i class="fas fa-spinner fa-spin"></i> Cargando...
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <h2>Results</h2>
                <div id="results"></div>

                <!-- Resultados -->
                <div id="resultsContainer" style="display: none;">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="mb-0">Información del Análisis</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>URL Solicitada:</strong> <span id="requestedUrl"></span></p>
                                    <p><strong>URL Final:</strong> <span id="finalUrl"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Versión de Lighthouse:</strong> <span id="lighthouseVersion"></span></p>
                                    <p><strong>Fecha del Análisis:</strong> <span id="analysisDate"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Puntuaciones por Categoría -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="mb-0">Puntuaciones por Categoría</h3>
                        </div>
                        <div class="card-body">
                            <div class="row" id="categoriesContainer">
                                <!-- Se llenará dinámicamente -->
                            </div>
                        </div>
                    </div>

                    <!-- Métricas Principales -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="mb-0">Métricas de Rendimiento</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Métrica</th>
                                            <th>Valor</th>
                                            <th>Puntuación</th>
                                            <th>Valor Numérico</th>
                                        </tr>
                                    </thead>
                                    <tbody id="metricsBody">
                                        <!-- Se llenará dinámicamente -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Experiencia de Carga (Field Data) -->
                    <div class="card mb-4" id="loadingExperienceSection" style="display: none;">
                        <div class="card-header">
                            <h3 class="mb-0">Experiencia de Carga (Datos Reales)</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Métrica</th>
                                            <th>Categoría</th>
                                            <th>Percentil</th>
                                        </tr>
                                    </thead>
                                    <tbody id="loadingExperienceBody">
                                        <!-- Se llenará dinámicamente -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botón para guardar resultados -->
                    <div id="saveResultsContainer" class="mt-4 text-center" style="display: none;">
                        <button id="saveBtn" class="btn btn-success">
                            <i class="fas fa-save"></i> Guardar Resultados
                        </button>
                        <div id="saveStatus" class="mt-2"></div>
                    </div>
                </div>
                
                <!-- Modal para errores -->
                <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true" style="display:none;">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-body">
                                <!-- Mensaje de error -->
                                <p id="errorModalText"></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="metrics-history" class="tab-pane">
                <!-- Contenido de la pestaña "Metrics History" -->
                <!-- Aquí agregarás el contenido de esta pestaña -->
            </div>
        </div>
    </div>

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
        // Variable para almacenar la respuesta de las métricas
        let lastMetricsResponse = null;
        // Función para determinar la clase de puntuación
        function getScoreClass(score) {
            if (score >= 90) return 'score-excellent';
            if (score >= 70) return 'score-good';
            if (score >= 50) return 'score-average';
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
                const $loading = $('#loading');
                const $submitButton = $(this).find('button[type="submit"]');
                
                // Deshabilitar el botón y mostrar el indicador de carga
                $submitButton.prop('disabled', true);
                $loading.css('display', 'inline-block');
                
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
                        $loading.hide();

                        if (response.success) {
                            // Guardar la respuesta para usarla al guardar
                            lastMetricsResponse = response;
                            
                            // Mostrar el contenedor de resultados
                            $('#resultsContainer').show();
                            
                            // Mostrar el botón de guardar
                            $('#saveResultsContainer').show();
                            $('#saveStatus').empty();

                            // Información del análisis
                            $('#requestedUrl').text(response.requestedUrl || 'N/A');
                            $('#finalUrl').text(response.finalUrl || 'N/A');
                            $('#lighthouseVersion').text(response.lighthouseVersion || 'N/A');
                            
                            const analysisDate = response.analysisUTCTimestamp ? 
                                new Date(response.analysisUTCTimestamp).toLocaleString() : 'N/A';
                            $('#analysisDate').text(analysisDate);

                            // Mostrar categorías
                            const categoriesContainer = $('#categoriesContainer');
                            categoriesContainer.empty();
                            
                            if (response.categories && Object.keys(response.categories).length > 0) {
                                for (const [categoryId, category] of Object.entries(response.categories)) {
                                    const score = category.score || 0;
                                    const scoreClass = getScoreClass(score);
                                    
                                    categoriesContainer.append(`
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100">
                                                <div class="card-body text-center">
                                                    <h5 class="card-title">${category.title || categoryId}</h5>
                                                    <div class="score-circle ${scoreClass} mx-auto">
                                                        ${Math.round(score)}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `);
                                }
                            } else {
                                categoriesContainer.html('<div class="col-12"><p>No hay datos de categorías disponibles.</p></div>');
                            }

                            // Mostrar métricas
                            const metricsBody = $('#metricsBody');
                            metricsBody.empty();
                            
                            if (response.metrics && Object.keys(response.metrics).length > 0) {
                                for (const [metricId, metric] of Object.entries(response.metrics)) {
                                    const score = metric.score !== null && metric.score !== undefined ? 
                                        Math.round(metric.score * 100) : 'N/A';
                                    const scoreClass = getScoreClass(metric.score * 100);
                                    
                                    metricsBody.append(`
                                        <tr>
                                            <td>${metric.title || metricId}</td>
                                            <td>${metric.displayValue || 'N/A'}</td>
                                            <td>
                                                ${typeof score === 'number' ? 
                                                    `<span class="badge ${scoreClass}">${score}</span>` : 
                                                    'N/A'}
                                            </td>
                                            <td>${metric.numericValue || 'N/A'}</td>
                                        </tr>
                                    `);
                                }
                            } else {
                                metricsBody.html('<tr><td colspan="4">No hay métricas disponibles.</td></tr>');
                            }

                            // Mostrar datos de experiencia de carga si están disponibles
                            const loadingExpSection = $('#loadingExperienceSection');
                            const loadingExpBody = $('#loadingExperienceBody');
                            loadingExpBody.empty();
                            
                            if (response.loadingExperience && Object.keys(response.loadingExperience).length > 0) {
                                loadingExpSection.show();
                                
                                for (const [metricId, metric] of Object.entries(response.loadingExperience)) {
                                    let metricName = metricId;
                                    if (metricId === 'FCP') metricName = 'First Contentful Paint';
                                    else if (metricId === 'FID') metricName = 'First Input Delay';
                                    else if (metricId === 'LCP') metricName = 'Largest Contentful Paint';
                                    else if (metricId === 'CLS') metricName = 'Cumulative Layout Shift';
                                    
                                    loadingExpBody.append(`
                                        <tr>
                                            <td>${metricName}</td>
                                            <td>${metric.category || 'N/A'}</td>
                                            <td>${metric.percentile || 'N/A'}</td>
                                        </tr>
                                    `);
                                }
                            } else {
                                loadingExpSection.hide();
                            }
                            
                        } else {
                            $('#results').html(`<div class="alert alert-danger">Error: ${response.error || 'Error desconocido'}</div>`);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                        // Restaurar el botón y ocultar el indicador de carga
                        $submitButton.prop('disabled', false);
                        $loading.hide();
                        
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
                const { url, strategy, categories, ...metricsData } = lastMetricsResponse;
                const saveData = {
                    ...metricsData,  // Primero los datos de las métricas
                    url: currentUrl,  // Sobrescribir con los valores del formulario
                    strategy: currentStrategy,
                    categories: currentCategories,
                    _token: $('meta[name="csrf-token"]').attr('content')
                };
                
                console.log('Datos finales a enviar:', saveData);
                
                console.log('Datos a guardar:', JSON.stringify(saveData, null, 2));
                console.log('Valor de strategy en el objeto:', saveData.strategy);
                console.log('Tipo de strategy:', typeof saveData.strategy);
                
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
</body>
</html>
