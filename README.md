# Google PageSpeed Insights API

Esta aplicación permite consultar métricas de rendimiento de páginas web utilizando la API de Google PageSpeed Insights.

## Requisitos

- Docker y Docker Compose
- Una clave de API de Google PageSpeed Insights (ya configurada en el archivo .env)

## Configuración y Ejecución

1. Clona este repositorio:
   ```
   git clone <url-del-repositorio>
   cd GoogleInsightsApi
   ```

2. Inicia los contenedores con Docker Compose:
   ```
   docker-compose up -d
   ```

3. La aplicación estará disponible en:
   - Frontend: http://localhost:8000/pagespeed
   - PHPMyAdmin: http://localhost:8080 (usuario: root, sin contraseña)

## Características

- Consulta de métricas de rendimiento de páginas web
- Selección de categorías de análisis (ACCESSIBILITY, BEST_PRACTICES, PERFORMANCE, PWA, SEO)
- Selección de estrategia (DESKTOP o MOBILE)
- Visualización de resultados en formato tabular
- Manejo de errores con mensajes informativos

## Estructura del Proyecto

- `app/Http/Controllers/PageSpeedController.php`: Controlador principal que maneja las solicitudes
- `app/Services/PageSpeedService.php`: Servicio que interactúa con la API de Google PageSpeed
- `app/Http/Requests/PageSpeedRequest.php`: Validación de solicitudes
- `app/Exceptions/PageSpeedException.php`: Manejo de excepciones específicas
- `resources/views/pagespeed.blade.php`: Vista principal de la aplicación
- `resources/css/estilos.css`: Estilos CSS personalizados
- `routes/web.php`: Definición de rutas

## API Endpoints

- `POST /get-metrics`: Endpoint para obtener métricas de rendimiento
  - Parámetros:
    - `url`: URL de la página a analizar (requerido)
    - `categories`: Categorías de análisis (array, al menos una requerida)
    - `strategy`: Estrategia de análisis (DESKTOP o MOBILE, requerido)

## Licencia

Este proyecto está licenciado bajo la Licencia MIT.
