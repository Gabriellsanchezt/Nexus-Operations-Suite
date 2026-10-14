<?php
/**
 * Configuración Global del Sistema
 * Miami Agency Operations & Contracts Platform
 */

// Definición de Rutas Base
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('CONFIG_PATH', BASE_PATH . '/config');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('STORAGE_PATH', BASE_PATH . '/storage');

// Configuración de la Aplicación
define('APP_NAME', 'Nexus Operations Suite');
define('APP_ENV', getenv('APP_ENV') ?: 'development');

// Zona Horaria Oficial: Hora Legal de Venezuela (America/Caracas, UTC-4)
date_default_timezone_set('America/Caracas');

// Detección dinámica de BASE_URL para soporte nativo en XAMPP (/miami-agency-ops) y CLI (/)
if (!defined('BASE_URL')) {
    $envBase = getenv('BASE_URL');
    if ($envBase !== false && $envBase !== '') {
        define('BASE_URL', rtrim($envBase, '/'));
    } else {
        $requestUri = str_replace('\\', '/', $_SERVER['REQUEST_URI'] ?? '');
        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        if (str_starts_with($requestUri, '/miami-agency-ops/public') || str_starts_with($scriptName, '/miami-agency-ops/public')) {
            define('BASE_URL', '/miami-agency-ops/public');
        } elseif (str_starts_with($requestUri, '/miami-agency-ops') || str_starts_with($scriptName, '/miami-agency-ops')) {
            define('BASE_URL', '/miami-agency-ops');
        } else {
            define('BASE_URL', '');
        }
    }
}

/**
 * Helper global para generar URLs relativas a BASE_URL
 */
if (!function_exists('url')) {
    function url(string $path = ''): string {
        $path = '/' . ltrim($path, '/');
        return (defined('BASE_URL') ? BASE_URL : '') . $path;
    }
}

/**
 * Helper global para generar URLs de assets con cache-busting dinámico basado en filemtime
 */
if (!function_exists('asset_url')) {
    function asset_url(string $path = ''): string {
        $cleanPath = '/' . ltrim($path, '/');
        $fullPath = PUBLIC_PATH . $cleanPath;
        $version = file_exists($fullPath) ? filemtime($fullPath) : time();
        return url($cleanPath) . '?v=' . $version;
    }
}

// Configuración de Base de Datos (MySQL / MariaDB en XAMPP)
define('DB_DRIVER', getenv('DB_DRIVER') ?: 'mysql');
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_DATABASE', getenv('DB_DATABASE') ?: 'miami_agency_db');
define('DB_USERNAME', getenv('DB_USERNAME') ?: 'root');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: '');

// Zonas de Miami predefinidas para segmentación y contratos
define('MIAMI_ZONES', [
    'Brickell / Financial District',
    'Miami Beach / South Beach',
    'Coral Gables',
    'Wynwood / Design District',
    'Downtown Miami',
    'Coconut Grove',
    'Sunny Isles Beach',
    'Bal Harbour',
    'Aventura',
    'Key Biscayne',
    'Doral',
    'Edgewater'
]);

// Paquetes Operativos estándar para Realtors (Producción & Marketing Audiovisual)
define('REALTOR_PACKAGES', [
    'LUXURY_VIDEO_REELS' => [
        'name' => 'Paquete Reels & Luxury Video Walkthrough',
        'deliverables' => '4 Reels cinematográficos 4K con dron + 1 Walkthrough de 2 minutos'
    ],
    'FULL_BRANDING_SOCIAL' => [
        'name' => 'Branding Integral & Redes Mensual',
        'deliverables' => '8 Reels + 12 Carruseles gráficos + Gestión de Comunidad semanal'
    ],
    'VIP_LISTING_LAUNCH' => [
        'name' => 'Lanzamiento Exclusivo de Propiedad VIP',
        'deliverables' => 'Producción completa con 2 cámaras, dron FPV y edición prioritaria 48h'
    ]
]);