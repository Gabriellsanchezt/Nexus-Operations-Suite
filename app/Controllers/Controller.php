<?php
/**
 * Controlador Base MVC
 * Manejo de renderizado de vistas, respuestas JSON, redirecciones y mensajes flash
 */

namespace App\Controllers;

use Config\Auth;

abstract class Controller {
    /**
     * Renderizar una vista dentro del layout principal
     */
    protected function render(string $viewPath, array $data = [], string $layout = 'main'): void {
        // Enviar encabezados anti-caché y codificación UTF-8 estricta
        if (!headers_sent()) {
            header("Content-Type: text/html; charset=UTF-8");
            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
        }

        extract($data);
        $currentUser = Auth::user();
        $systemNotifications = $currentUser ? \App\Services\NotificationService::getUserNotifications($currentUser) : [];

        // Capturar la vista solicitada
        $viewFile = APP_PATH . '/Views/' . $viewPath . '.php';
        if (!file_exists($viewFile)) {
            http_response_code(404);
            $requestedPath = $viewPath;
            $error404File = APP_PATH . '/Views/errors/404.php';
            if (file_exists($error404File)) {
                require $error404File;
                exit;
            }
            throw new \RuntimeException("La vista '{$viewPath}' no fue encontrada.");
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Si layout es false, emitir vista directa sin plantilla (útil para PDFs limpios o AJAX)
        if ($layout === '' || $layout === 'none') {
            echo $content;
            return;
        }

        $layoutFile = APP_PATH . '/Views/layouts/' . $layout . '.php';
        if (!file_exists($layoutFile)) {
            http_response_code(500);
            throw new \RuntimeException("El layout de plantilla '{$layout}' no existe.");
        }

        require $layoutFile;
    }

    /**
     * Responder con payload JSON (para llamadas AJAX y Fetch de JavaScript Vanilla)
     */
    protected function json(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            header("Pragma: no-cache");
        }
        echo json_encode($data);
        exit;
    }

    /**
     * Redirección HTTP con soporte de BASE_URL
     */
    protected function redirect(string $url): void {
        if (!str_starts_with($url, 'http://') && !str_starts_with($url, 'https://')) {
            $url = function_exists('url') ? url($url) : $url;
        }
        header("Location: {$url}");
        exit;
    }

    /**
     * Establecer mensaje flash en sesión
     */
    protected function setFlash(string $type, string $message): void {
        Auth::initSession();
        $_SESSION['flash_' . $type] = $message;
    }
}
