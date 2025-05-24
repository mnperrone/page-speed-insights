# 🚀 Google PageSpeed Insights Dashboard

Un panel de control intuitivo para analizar y monitorear el rendimiento de sitios web utilizando la API de Google PageSpeed Insights. Obtén métricas detalladas sobre el rendimiento, accesibilidad, mejores prácticas, PWA y SEO de cualquier sitio web con una interfaz fácil de usar.

![Dashboard Preview](https://via.placeholder.com/1200x600/4f46e5/ffffff?text=Google+PageSpeed+Insights+Dashboard)

## 🌟 Características Principales

- 📊 Análisis completo de rendimiento web
- 📱 Soporte para dispositivos móviles y de escritorio
- 🎯 Métricas detalladas en categorías clave:
  - Rendimiento (Performance)
  - Accesibilidad (Accessibility)
  - Mejores Prácticas (Best Practices)
  - Aplicaciones Web Progresivas (PWA)
  - Optimización para Motores de Búsqueda (SEO)
- 📈 Historial de análisis
- 🔄 Interfaz de usuario intuitiva y responsiva
- 🚀 Resultados rápidos con visualización clara

## 🛠️ Requisitos Técnicos

- Docker y Docker Compose
- Clave de API de Google PageSpeed Insights
- PHP 8.0 o superior
- Node.js 14.x o superior
- Composer

## 🚀 Instalación Rápida

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/tu-usuario/GoogleInsightsApi.git
   cd GoogleInsightsApi
   ```

2. **Configurar variables de entorno**
   ```bash
   cp .env.example .env
   ```
   Actualiza el archivo `.env` con tu clave de API de Google PageSpeed Insights.

3. **Iniciar los contenedores**
   ```bash
   docker-compose up -d --build
   ```

4. **Instalar dependencias**
   ```bash
   docker-compose exec app composer install
   docker-compose exec app npm install
   docker-compose exec app npm run build
   ```

5. **Acceder a la aplicación**
   Abre tu navegador y visita:
   - Aplicación: http://localhost:8000
   - PHPMyAdmin: http://localhost:8080 (usuario: root, sin contraseña)

## 🏗️ Estructura del Proyecto

```
GoogleInsightsApi/
├── app/                    # Lógica de la aplicación
│   ├── Http/               # Controladores
│   ├── Services/           # Servicios de negocio
│   ├── Exceptions/         # Manejo de excepciones
│   └── ...
├── config/                # Archivos de configuración
├── database/              # Migraciones y seeders
├── public/                # Punto de entrada público
├── resources/
│   ├── views/            # Vistas Blade
│   └── ...
├── routes/                # Definición de rutas
└── tests/                 # Pruebas automatizadas
```

## 📚 Documentación de la API

### Obtener Métricas

```http
POST /get-metrics
```

**Parámetros:**
- `url` (requerido): URL completa del sitio a analizar (ej: https://ejemplo.com)
- `categories` (array, requerido): Categorías a analizar
  - PERFORMANCE
  - ACCESSIBILITY
  - BEST_PRACTICES
  - PWA
  - SEO
- `strategy` (string, requerido): 'DESKTOP' o 'MOBILE'

**Ejemplo de respuesta exitosa:**
```json
{
  "success": true,
  "data": {
    "url": "https://ejemplo.com",
    "strategy": "DESKTOP",
    "performance": 89,
    "accessibility": 95,
    "best_practices": 100,
    "seo": 90
  }
}
```

## 🛠️ Despliegue

### Requisitos de Producción
- Servidor web (Nginx/Apache)
- PHP 8.0+
- MySQL 5.7+ o MariaDB 10.3+
- Node.js 14.x+
- Composer

### Pasos para Producción

1. Configurar el servidor web para apuntar a `/public`
2. Configurar las variables de entorno en `.env`
3. Instalar dependencias:
   ```bash
   composer install --optimize-autoloader --no-dev
   npm install && npm run production
   ```
4. Generar clave de aplicación:
   ```bash
   php artisan key:generate
   ```
5. Optimizar la aplicación:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

## 🤝 Contribuir

Las contribuciones son bienvenidas. Por favor, lee nuestra [guía de contribución](CONTRIBUTING.md) para detalles sobre nuestro código de conducta y el proceso para enviar pull requests.

## 📄 Licencia

Este proyecto está licenciado bajo la Licencia MIT - ver el archivo [LICENSE](LICENSE) para más detalles.

## ✨ Créditos

- Matias Perrone - [mnperrone](https://github.com/mnperrone)

---

Hecho con ❤️ por https://github.com/mnperrone
