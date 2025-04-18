<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google PageSpeed Insights</title>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                                <button type="submit">Get Metrics</button>
                                <div id="loading" style="display:none;">
                                    <i class="fas fa-spinner fa-spin"></i> Cargando...
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <h2>Results</h2>
                <div id="results"></div>

                <!-- Tabla de resultados -->
                <table id="metricsTable" class="display" style="display:none;">
                    <thead>
                        <tr>
                            <th>Nombre de Métrica</th>
                            <th>Valor Obtenido</th>
                        </tr>
                    </thead>
                    <tbody id="metricsBody"></tbody>
                </table>
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

    <script>
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

                // Mostrar el spinner de carga
                $('#loading').show();

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
                        $('#results').html('');
                        $('#loading').hide(); // Ocultar el spinner al recibir la respuesta

                        if (response.success) {
                            $('#metricsTable').show();
                            $('#metricsBody').empty();  
                            for (const [metric, value] of Object.entries(response.metrics)) {
                                $('#metricsBody').append(`<tr><td>${metric}</td><td>${value}</td></tr>`);
                            }
                        } else {
                            $('#results').html('<p class="error">Error: ' + response.error + '</p>');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                        $('#errorModalText').html('Ocurrió un error al obtener los datos. Por favor, inténtelo de nuevo.');
                        $('#errorModal').modal({
                            backdrop: 'static',
                            keyboard: false
                        });
                        $('#loading').hide(); // Ocultar el spinner si ocurre un error
                    }
                });
            });
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