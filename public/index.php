<?php
/**
 * Front Controller & Enrutador MVC
 * Miami Agency Operations & Contracts Platform
 */

declare(strict_types=1);

// Cargar Autoloader de Composer
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Cargar Configuración Global
require_once dirname(__DIR__) . '/config/config.php';

use Config\Auth;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\ContractController;
use App\Controllers\ActivityController;
use App\Controllers\RealtorController;
use App\Controllers\TeamController;
use App\Controllers\ReportController;
use App\Controllers\ProfileController;

// Inicializar sesión segura
Auth::initSession();

// Normalizar URI
$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Quitar prefijo de subcarpeta en XAMPP si aplica y detectar la ruta base
$GLOBALS['basePrefix'] = '';
foreach (['/miami-agency-ops/public', '/miami-agency-ops'] as $prefix) {
    if (str_starts_with($requestUri, $prefix)) {
        $GLOBALS['basePrefix'] = $prefix;
        $requestUri = substr($requestUri, strlen($prefix));
        break;
    }
}

// Función helper url() global para construir URLs con el prefijo de XAMPP
if (!function_exists('url')) {
    function url(string $path = ''): string {
        $prefix = $GLOBALS['basePrefix'] ?? '';
        return $prefix . '/' . ltrim($path, '/');
    }
}

// Quitar trailing slash excepto para '/'
if ($requestUri !== '/' && str_ends_with($requestUri, '/')) {
    $requestUri = rtrim($requestUri, '/');
}
if ($requestUri === '') {
    $requestUri = '/';
}

// Enrutamiento MVC
try {
    switch ($requestUri) {
        // Redirección Raíz
        case '/':
            if (Auth::check()) {
                header('Location: ' . url('/dashboard'));
            } else {
                header('Location: ' . url('/login'));
            }
            exit;

        // Autenticación
        case '/login':
            $auth = new AuthController();
            if ($requestMethod === 'POST') {
                $auth->login();
            } else {
                $auth->showLogin();
            }
            break;

        case '/logout':
            (new AuthController())->logout();
            break;

        // Dashboard
        case '/dashboard':
            (new DashboardController())->index();
            break;

        // API Notificaciones
        case '/api/notifications/mark-read':
            if ($requestMethod === 'POST') {
                (new DashboardController())->markNotificationRead();
            }
            break;

        // Perfil de Usuario
        case '/profile':
            $profile = new ProfileController();
            if ($requestMethod === 'POST') {
                $profile->update();
            } else {
                $profile->showEdit();
            }
            break;

        // Módulos de Contratos
        case '/contracts/final':
            (new ContractController())->finalContracts();
            break;

        case '/contracts/approvals':
            (new ContractController())->approvals();
            break;

        case '/contracts/review':
            if ($requestMethod === 'POST') {
                (new ContractController())->processReview();
            }
            break;

        case '/contracts/activities':
            (new ContractController())->activities();
            break;

        case '/contracts/create':
            (new ContractController())->create();
            break;

        case '/contracts/store':
            if ($requestMethod === 'POST') {
                (new ContractController())->store();
            }
            break;

        case '/contracts/update':
            if ($requestMethod === 'POST') {
                (new ContractController())->update();
            }
            break;

        case '/contracts/delete':
            if ($requestMethod === 'POST') {
                (new ContractController())->delete();
            }
            break;

        case '/contracts/submit-existing':
            if ($requestMethod === 'POST') {
                (new ContractController())->submitExistingDraft();
            }
            break;

        case '/contracts/preview-ajax':
            if ($requestMethod === 'POST') {
                (new ContractController())->previewAjax();
            }
            break;

        case '/contracts/view':
            (new ContractController())->view();
            break;

        // Módulos de Actividades Audiovisuales
        case '/activities':
            (new ActivityController())->index();
            break;

        case '/activities/store':
            if ($requestMethod === 'POST') {
                (new ActivityController())->store();
            }
            break;

        case '/activities/update':
            if ($requestMethod === 'POST') {
                (new ActivityController())->update();
            }
            break;

        case '/activities/delete':
            if ($requestMethod === 'POST') {
                (new ActivityController())->delete();
            }
            break;

        case '/activities/my-tasks':
            (new ActivityController())->myTasks();
            break;

        case '/activities/submit-deliverable':
            if ($requestMethod === 'POST') {
                (new ActivityController())->submitDeliverable();
            }
            break;

        case '/activities/start-task':
            if ($requestMethod === 'POST') {
                (new ActivityController())->startTask();
            }
            break;

        case '/activities/review-deliverable':
            if ($requestMethod === 'POST') {
                (new ActivityController())->reviewDeliverable();
            }
            break;

        case '/activities/details-ajax':
            (new ActivityController())->detailsAjax();
            break;

        // Realtors CRUD
        case '/realtors':
            (new RealtorController())->index();
            break;

        case '/realtors/store':
            if ($requestMethod === 'POST') {
                (new RealtorController())->store();
            }
            break;

        case '/realtors/update':
            if ($requestMethod === 'POST') {
                (new RealtorController())->update();
            }
            break;

        case '/realtors/delete':
            if ($requestMethod === 'POST') {
                (new RealtorController())->delete();
            }
            break;

        // Equipo, Trabajadores, Roles y Especialidades CRUD
        case '/team':
            (new TeamController())->index();
            break;

        case '/team/store':
            if ($requestMethod === 'POST') {
                (new TeamController())->store();
            }
            break;

        case '/team/update':
            if ($requestMethod === 'POST') {
                (new TeamController())->update();
            }
            break;

        case '/team/toggle-status':
            if ($requestMethod === 'POST') {
                (new TeamController())->toggleStatus();
            }
            break;

        case '/team/roles/store':
            if ($requestMethod === 'POST') {
                (new TeamController())->storeRole();
            }
            break;

        case '/team/roles/update':
            if ($requestMethod === 'POST') {
                (new TeamController())->updateRole();
            }
            break;

        case '/team/roles/toggle-status':
            if ($requestMethod === 'POST') {
                (new TeamController())->toggleRoleStatus();
            }
            break;

        case '/team/specialties/store':
            if ($requestMethod === 'POST') {
                (new TeamController())->storeSpecialty();
            }
            break;

        case '/team/specialties/update':
            if ($requestMethod === 'POST') {
                (new TeamController())->updateSpecialty();
            }
            break;

        case '/team/specialties/toggle-status':
            if ($requestMethod === 'POST') {
                (new TeamController())->toggleSpecialtyStatus();
            }
            break;

        case '/team/update-specialties':
            if ($requestMethod === 'POST') {
                (new TeamController())->updateSpecialties();
            }
            break;

        // Reportes Ejecutivos
        case '/reports':
            (new ReportController())->index();
            break;

        case '/reports/export-excel':
            (new ReportController())->exportExcel();
            break;

        default:
            http_response_code(404);
            echo "<h1>404 Not Found</h1><p>La ruta solicitada '{$requestUri}' no existe.</p><a href='" . url('/dashboard') . "'>Volver al inicio</a>";
            break;
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo "<h1>Error 500 del Sistema</h1>";
    echo "<p><strong>Mensaje:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Archivo:</strong> " . htmlspecialchars($e->getFile()) . " en línea " . $e->getLine() . "</p>";
    if (defined('APP_ENV') && APP_ENV === 'development') {
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    }
}