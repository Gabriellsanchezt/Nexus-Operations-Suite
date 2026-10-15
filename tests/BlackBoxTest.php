<?php
/**
 * =====================================================================
 * SUITE DE PRUEBAS DE CAJA NEGRA (BLACK-BOX TESTING)
 * Nexus Operations Suite - Miami Luxury Media
 * =====================================================================
 * 
 * Evalua el sistema desde la perspectiva del cliente/usuario externo:
 * Puntos de entrada HTTP, autenticacion, control de acceso RBAC,
 * seguridad de cabeceras, validaciones de formularios, APIs JSON,
 * sanitizacion de errores 404/500 y proteccion contra fuga de datos.
 */

declare(strict_types=1);

class BlackBoxTestSuite {
    private string $baseUrl = 'http://127.0.0.1:8000';
    private string $cookieJar;
    private int $passed = 0;
    private int $failed = 0;
    private array $failures = [];

    public function __construct() {
        $this->cookieJar = tempnam(sys_get_temp_dir(), 'nex_cookie_');
    }

    public function __destruct() {
        if (file_exists($this->cookieJar)) {
            @unlink($this->cookieJar);
        }
    }

    public function run(): void {
        echo "\n" . str_repeat('=', 70) . "\n";
        echo " EJECUTANDO SUITE DE PRUEBAS DE CAJA NEGRA (BLACK-BOX)\n";
        echo " Evaluacion externa de endpoints, seguridad, sesiones y comportamiento\n";
        echo str_repeat('=', 70) . "\n\n";

        $startTime = microtime(true);

        $this->testUnauthenticatedProtectionAndHeaders();
        $this->testAuthenticationSecurityAndBoundaries();
        $this->testRbacPrivilegeEscalation();
        $this->testDashboardUiInvariants();
        $this->testNotificationsRealtimeApi();
        $this->testRealtorProjectsModalApi();
        $this->testMeetingsAgendaAndGoogleCalendar();
        $this->testError404And500Sanitization();
        $this->testIdleSessionWarningModalAndKeepAliveApi();
        $this->testUtf8HeadersAndContentDelivery();
        $this->testEmailMarketingModuleDesignAndCalendar();

        $elapsed = round(microtime(true) - $startTime, 4);

        echo "\n" . str_repeat('-', 70) . "\n";
        echo " RESUMEN CAJA NEGRA: Total: " . ($this->passed + $this->failed) . " | ";
        echo "Pasadas: {$this->passed} | ";
        echo "Fallidas: {$this->failed} | ";
        echo "Tiempo: {$elapsed}s\n";
        echo str_repeat('-', 70) . "\n";

        if ($this->failed > 0) {
            echo "\nDETALLE DE FALLAS:\n";
            foreach ($this->failures as $f) {
                echo " [FALLA] {$f}\n";
            }
            exit(1);
        } else {
            echo "\nTODAS LAS PRUEBAS DE CAJA NEGRA PASARON EXITOSAMENTE (100% OK).\n\n";
            exit(0);
        }
    }

    private function assert(bool $condition, string $testName, string $failureDetails = ''): void {
        if ($condition) {
            $this->passed++;
            echo " [PASS] {$testName}\n";
        } else {
            $this->failed++;
            $msg = "{$testName} - Detalle: {$failureDetails}";
            $this->failures[] = $msg;
            echo " [FAIL] {$msg}\n";
        }
    }

    /**
     * Realizar solicitud HTTP mediante cURL
     */
    private function request(string $path, string $method = 'GET', ?array $postData = null, bool $useCookies = true, bool $isJson = false): array {
        $ch = curl_init();
        $url = $this->baseUrl . $path;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        if ($useCookies) {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieJar);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieJar);
        }

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($isJson) {
                $payload = json_encode($postData ?? []);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($payload),
                    'X-Requested-With: XMLHttpRequest'
                ]);
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData ?? []));
            }
        }

        $rawResponse = curl_exec($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $headers = substr($rawResponse, 0, $headerSize);
        $body = substr($rawResponse, $headerSize);

        return [
            'code' => $httpCode,
            'headers' => $headers,
            'body' => $body
        ];
    }

    private function resetCookies(): void {
        if (file_exists($this->cookieJar)) {
            unlink($this->cookieJar);
        }
        $this->cookieJar = tempnam(sys_get_temp_dir(), 'nex_cookie_');
    }

    /**
     * 1. Proteccion de Rutas sin Autenticacion y Encabezados Anti-Caché
     */
    private function testUnauthenticatedProtectionAndHeaders(): void {
        echo "\n--- 1. Rutas Protegidas sin Sesion y Encabezados de Seguridad ---\n";
        $this->resetCookies();

        $protectedRoutes = ['/dashboard', '/activities', '/contracts/final', '/reports', '/profile', '/team'];
        foreach ($protectedRoutes as $route) {
            $res = $this->request($route, 'GET', null, false);
            $this->assert($res['code'] === 302, "Acceso no autenticado a '{$route}' redirige (HTTP 302)", "Codigo recibido: {$res['code']}");
            $this->assert(str_contains($res['headers'], 'Location: /login') || str_contains($res['headers'], 'Location: /'), "Redireccion de '{$route}' apunta a /login");
        }

        // Inspeccionar encabezados anti-caché en respuesta
        $resLogin = $this->request('/login', 'GET', null, false);
        $this->assert(str_contains($resLogin['headers'], 'Cache-Control: no-store') || str_contains($resLogin['headers'], 'no-cache'), 'Encabezados incluyen directiva Cache-Control no-cache/no-store');
        $this->assert(str_contains($resLogin['headers'], 'Pragma: no-cache'), 'Encabezados incluyen directiva Pragma: no-cache');
    }

    /**
     * 2. Seguridad de Autenticacion y Pruebas de Limites
     */
    private function testAuthenticationSecurityAndBoundaries(): void {
        echo "\n--- 2. Autenticacion, Ataques de Inyeccion y Manejo de Sesion ---\n";
        $this->resetCookies();

        // Intento 1: Campos vacíos
        $resEmpty = $this->request('/login', 'POST', ['email' => '', 'password' => '']);
        $this->assert($resEmpty['code'] === 302, 'Login con campos vacios es rechazado (HTTP 302)');

        // Intento 2: Inyeccion SQL en email
        $resSqlInj = $this->request('/login', 'POST', ['email' => "admin' OR '1'='1", 'password' => 'cualquiera']);
        $this->assert($resSqlInj['code'] === 302 && str_contains($resSqlInj['headers'], 'Location: /login'), 'Intento de Inyeccion SQL es neutralizado y redirige a /login');

        // Intento 3: Clave incorrecta
        $resBadPass = $this->request('/login', 'POST', ['email' => 'jefe@miamiagency.com', 'password' => 'ClaveInvalida999']);
        $this->assert($resBadPass['code'] === 302 && str_contains($resBadPass['headers'], 'Location: /login'), 'Clave incorrecta es rechazada y redirige a /login');

        // Intento 4: Credenciales validas Jefe
        $resValid = $this->request('/login', 'POST', ['email' => 'jefe@miamiagency.com', 'password' => 'password123']);
        $this->assert($resValid['code'] === 302 && str_contains($resValid['headers'], 'Location: /dashboard'), 'Credenciales validas autentican exitosamente y redirigen a /dashboard');
        $this->assert(str_contains($resValid['headers'], 'HttpOnly'), 'Cookie de sesion PHPSESSID se emite con la bandera segura HttpOnly');
    }

    /**
     * 3. Prevencion de Escalada de Privilegios RBAC (Asistente intentando acceder a modulos de Jefe)
     */
    private function testRbacPrivilegeEscalation(): void {
        echo "\n--- 3. Prevencion de Escalada de Privilegios RBAC ---\n";
        $this->resetCookies();

        // Autenticar como Asistente Operativo
        $this->request('/login', 'POST', ['email' => 'asistente@miamiagency.com', 'password' => 'password123']);

        // Intentar acceder a /team (Exclusivo de Jefe)
        $resTeam = $this->request('/team', 'GET');
        $this->assert($resTeam['code'] === 403 || $resTeam['code'] === 302, 'Asistente no puede acceder a /team (HTTP 403 o redireccion)', "Codigo: {$resTeam['code']}");

        // Intentar acceder a /reports (Exclusivo de Jefe)
        $resReports = $this->request('/reports', 'GET');
        $this->assert($resReports['code'] === 403 || $resReports['code'] === 302, 'Asistente no puede acceder a /reports (HTTP 403 o redireccion)', "Codigo: {$resReports['code']}");
    }

    /**
     * 4. Invariantes de Interfaz: Slider Cerrado, Sin Boton de Contratos, Cache Busting
     */
    private function testDashboardUiInvariants(): void {
        echo "\n--- 4. Invariantes de Interfaz (Slider Cerrado, Dashboard Despejado, Cache-Buster) ---\n";
        $this->resetCookies();

        // Login Jefe
        $this->request('/login', 'POST', ['email' => 'jefe@miamiagency.com', 'password' => 'password123']);
        $dash = $this->request('/dashboard', 'GET');

        // 1. Slider bar siempre cerrado al ingresar
        $this->assert(str_contains($dash['body'], 'app-sidebar sidebar-collapsed'), 'Barra lateral inicia con la clase sidebar-collapsed (cerrada)');
        $this->assert(str_contains($dash['body'], 'app-main-wrapper main-expanded'), 'Contenedor principal inicia con la clase main-expanded (pantalla completa)');

        // 2. Boton de contratos finales eliminado del dashboard
        $this->assert(!str_contains($dash['body'], 'Contratos Finales (PDF)'), 'Boton de Contratos Finales eliminado exitosamente de la cabecera del Dashboard');

        // 3. Cache-busting en assets
        $this->assert(str_contains($dash['body'], 'style.css?v='), 'style.css incluye parametro de version dinámico (?v=...) para cache-busting');
        $this->assert(str_contains($dash['body'], 'app.js?v='), 'app.js incluye parametro de version dinámico (?v=...) para cache-busting');
    }

    /**
     * 5. API de Notificaciones en Tiempo Real y Marcado
     */
    private function testNotificationsRealtimeApi(): void {
        echo "\n--- 5. API de Notificaciones en Tiempo Real y Marcado AJAX ---\n";
        $this->resetCookies();

        $this->request('/login', 'POST', ['email' => 'jefe@miamiagency.com', 'password' => 'password123']);

        // GET /api/notifications
        $getRes = $this->request('/api/notifications', 'GET');
        $this->assert($getRes['code'] === 200, 'GET /api/notifications responde HTTP 200');

        $json = json_decode($getRes['body'], true);
        $this->assert(is_array($json) && ($json['success'] ?? false) === true, 'Payload de notificaciones tiene success=true');
        $this->assert(isset($json['count']) && isset($json['notifications']), 'Payload contiene claves count y notifications');

        // POST /api/notifications/mark-read (Marcar todas)
        $postRes = $this->request('/api/notifications/mark-read', 'POST', ['all' => true], true, true);
        $this->assert($postRes['code'] === 200, 'POST /api/notifications/mark-read responde HTTP 200');

        $postJson = json_decode($postRes['body'], true);
        $this->assert(($postJson['remaining_count'] ?? -1) === 0, 'Marcar todas actualiza remaining_count a 0');

        // Verificar que subsecuente GET retorna count=0
        $getAfter = $this->request('/api/notifications', 'GET');
        $afterJson = json_decode($getAfter['body'], true);
        $this->assert(($afterJson['count'] ?? -1) === 0, 'GET posterior confirma 0 notificaciones no leidas');
    }

    /**
     * 6. Endpoint de Proyectos de Realtor (Boton Ver en Directorio)
     */
    private function testRealtorProjectsModalApi(): void {
        echo "\n--- 6. Endpoint de Proyectos del Realtor (Boton Ver) ---\n";
        $this->resetCookies();

        $this->request('/login', 'POST', ['email' => 'jefe@miamiagency.com', 'password' => 'password123']);

        $res = $this->request('/realtors/projects?id=1', 'GET');
        $this->assert($res['code'] === 200, 'GET /realtors/projects?id=1 responde HTTP 200');

        $data = json_decode($res['body'], true);
        $this->assert(isset($data['realtor']) && isset($data['projects']), 'Respuesta JSON contiene estructuras realtor y projects');
        $this->assert(array_key_exists('credentials_url', $data['realtor']), 'Objeto realtor contiene campo credentials_url');
        $this->assert(array_key_exists('dropbox_url', $data['realtor']), 'Objeto realtor contiene campo dropbox_url');
    }

    /**
     * 7. Agenda de Reuniones y Enlace Google Calendar
     */
    private function testMeetingsAgendaAndGoogleCalendar(): void {
        echo "\n--- 7. Agenda de Reuniones y Enlace de Sincronizacion Google Calendar ---\n";
        $this->resetCookies();

        $this->request('/login', 'POST', ['email' => 'jefe@miamiagency.com', 'password' => 'password123']);

        $res = $this->request('/agenda', 'GET');
        $this->assert($res['code'] === 200, 'GET /agenda responde HTTP 200');
        $this->assert(str_contains($res['body'], 'calendar.google.com/calendar/render'), 'La vista de Agenda contiene enlaces activos a Google Calendar');
        $this->assert(str_contains($res['body'], '/agenda/download-ics'), 'La vista de Agenda contiene botones para descarga de archivos .ics');

        // Verificación de campos estrictos y ausencia de campos innecesarios
        $this->assert(str_contains($res['body'], 'name="employee_id"'), 'El formulario de reunión incluye selector de trabajador asignado (employee_id)');
        $this->assert(str_contains($res['body'], 'name="title"'), 'El formulario incluye campo title');
        $this->assert(str_contains($res['body'], 'name="realtor_id"'), 'El formulario incluye campo realtor_id');
        $this->assert(str_contains($res['body'], 'name="meeting_date"'), 'El formulario incluye campo meeting_date');
        $this->assert(str_contains($res['body'], 'name="meeting_time"'), 'El formulario incluye campo meeting_time');
        $this->assert(str_contains($res['body'], 'name="description"'), 'El formulario incluye campo description');
        $this->assert(!str_contains($res['body'], 'name="duration_minutes"'), 'El formulario NO incluye selector de hora estimada/duración');
        $this->assert(!str_contains($res['body'], 'name="meeting_link"'), 'El formulario NO incluye campo de link de Meet');

        // Comprobación de asignación de correo institucional en la tabla
        $this->assert(str_contains($res['body'], '@miamiagency.com'), 'La tabla de reuniones despliega el correo institucional del trabajador asignado');

        // Registro de nueva reunión con trabajador asignado
        $postMeeting = [
            'title' => 'Reunión de Coordinación VIP',
            'realtor_id' => 1,
            'employee_id' => 2, // Valentina Rossi (asistente@miamiagency.com)
            'meeting_date' => date('Y-m-d', strtotime('+3 days')),
            'meeting_time' => '11:00',
            'description' => 'Alineación de objetivos de campaña'
        ];
        $resStore = $this->request('/agenda/store', 'POST', $postMeeting);
        $this->assert($resStore['code'] === 302, 'POST /agenda/store redirige exitosamente (HTTP 302)');

        // Validar que la nueva reunión aparece en la agenda
        $resAfter = $this->request('/agenda', 'GET');
        $this->assert(str_contains($resAfter['body'], 'Reunión de Coordinación VIP'), 'La nueva reunión creada aparece en el listado');
        $this->assert(str_contains($resAfter['body'], 'asistente@miamiagency.com'), 'La nueva reunión muestra el correo asignado del trabajador');
    }

    /**
     * 8. Sanitizacion de Errores 404 y 500 (Cero Fuga Tecnica CWE-209)
     */
    private function testError404And500Sanitization(): void {
        echo "\n--- 8. Blindaje de Vistas de Error 404 y 500 (Cero Fuga CWE-209) ---\n";

        // Ruta inexistente (404)
        $res404 = $this->request('/ruta-completamente-inexistente-xyz', 'GET', null, false);
        $this->assert($res404['code'] === 404, 'Ruta desconocida retorna codigo HTTP 404');
        $this->assert(str_contains($res404['body'], 'Módulo o Recurso No Encontrado'), 'Vista 404 despliega diseño ejecutivo formal');
        $this->assert(!str_contains($res404['body'], 'Fatal error') && !str_contains($res404['body'], 'Stack trace'), 'Vista 404 no expone ningun volcado interno de PHP');

        // Inspeccionar plantilla de error 500
        $view500Content = file_get_contents(dirname(__DIR__) . '/app/Views/errors/500.php');
        $this->assert(!str_contains($view500Content, '$errorMessage'), 'Plantilla 500 no expone $errorMessage al cliente');
        $this->assert(!str_contains($view500Content, '$errorFile'), 'Plantilla 500 no expone rutas fisicas de archivos ($errorFile)');
        $this->assert(!str_contains($view500Content, '$errorLine'), 'Plantilla 500 no expone lineas de codigo ($errorLine)');
        $this->assert(!str_contains($view500Content, '$errorTrace'), 'Plantilla 500 no expone el stack trace ($errorTrace)');
        $this->assert(str_contains($view500Content, 'Código de Referencia de Incidencia'), 'Plantilla 500 despliega formalmente el Codigo de Referencia de Incidencia');
    }

    /**
     * 9. Modal de Advertencia de Inactividad y Heartbeat API
     */
    private function testIdleSessionWarningModalAndKeepAliveApi(): void {
        echo "\n--- 9. Modal de Advertencia de Inactividad y Heartbeat API ---\n";

        // 1. Petición no autenticada a /api/session/keep-alive retorna HTTP 401
        $this->resetCookies();
        $resUnauth = $this->request('/api/session/keep-alive', 'POST', [], false, true);
        $this->assert($resUnauth['code'] === 401, 'POST /api/session/keep-alive no autenticado retorna HTTP 401');
        $jsonUnauth = json_decode($resUnauth['body'], true);
        $this->assert(isset($jsonUnauth['authenticated']) && $jsonUnauth['authenticated'] === false, 'Respuesta 401 contiene authenticated=false');

        // 2. Iniciar sesión como Jefe
        $this->request('/login', 'POST', ['email' => 'jefe@miamiagency.com', 'password' => 'password123']);

        // 3. Heartbeat / Keep-Alive autenticado
        $resAuth = $this->request('/api/session/keep-alive', 'POST', [], true, true);
        $this->assert($resAuth['code'] === 200, 'POST /api/session/keep-alive autenticado retorna HTTP 200');
        $jsonAuth = json_decode($resAuth['body'], true);
        $this->assert(isset($jsonAuth['success']) && $jsonAuth['success'] === true, 'Payload keep-alive contiene success=true');
        $this->assert(isset($jsonAuth['expires_in']) && $jsonAuth['expires_in'] === 900, 'Payload keep-alive reporta expires_in=900');

        // 4. Modal en layout principal (Dashboard)
        $resDash = $this->request('/dashboard', 'GET');
        $this->assert(str_contains($resDash['body'], 'id="idleSessionModal"'), 'El layout incluye el modal #idleSessionModal');
        $this->assert(str_contains($resDash['body'], 'id="idleCountdownTimer"'), 'El modal incluye el cronometro #idleCountdownTimer');
        $this->assert(str_contains($resDash['body'], 'id="btnExtendSession"'), 'El modal incluye el boton #btnExtendSession');
        $this->assert(str_contains($resDash['body'], 'sessionTimeout'), 'APP_CONFIG expone los parametros de sessionTimeout');

        // 5. Cierre de sesión por inactividad
        $resLogout = $this->request('/logout?reason=inactivity', 'GET');
        $this->assert($resLogout['code'] === 302, 'GET /logout?reason=inactivity redirige (HTTP 302)');
        $this->assert(str_contains($resLogout['headers'], 'Location: /login?reason=inactivity'), 'Redirección apunta a /login?reason=inactivity');

        // 6. Mensaje en vista de Login
        $resLogin = $this->request('/login?reason=inactivity', 'GET');
        $this->assert(str_contains($resLogin['body'], 'inactividad'), 'Vista de login presenta advertencia formal de sesion cerrada por inactividad');
    }

    /**
     * 10. Verificación de Encabezados UTF-8 y Ausencia de Caracteres Corruptos
     */
    private function testUtf8HeadersAndContentDelivery(): void {
        echo "\n--- 10. Codificación UTF-8 en Vistas y Ausencia de Corrupción (?) ---\n";

        // Login Jefe
        $this->request('/login', 'POST', ['email' => 'jefe@miamiagency.com', 'password' => 'password123']);

        $resDash = $this->request('/dashboard', 'GET');
        $this->assert($resDash['code'] === 200, 'GET /dashboard responde HTTP 200');
        $this->assert(str_contains(strtolower($resDash['headers']), 'charset=utf-8'), 'Encabezados HTTP contienen charset=UTF-8 explícito');
        $this->assert(mb_check_encoding($resDash['body'], 'UTF-8'), 'Cuerpo HTML de /dashboard es UTF-8 válido');
        $this->assert(!str_contains($resDash['body'], '??'), 'Cuerpo HTML no contiene caracteres dobles de interrogación corruptos (??)');
        $this->assert(str_contains($resDash['body'], 'Producción') || str_contains($resDash['body'], 'Reuniones') || str_contains($resDash['body'], 'Operaciones'), 'Vistas renderizan caracteres acentuados correctamente');
    }

    /**
     * 11. Módulo de Email Marketing: Tarjetas Ejecutivas y Calendario Interactivo
     */
    private function testEmailMarketingModuleDesignAndCalendar(): void {
        echo "\n--- 11. Módulo de Email Marketing: Tarjetas Ejecutivas y Calendario Interactivo ---\n";

        // 1. Acceso no autenticado es protegido
        $this->resetCookies();
        $resUnauth = $this->request('/email-marketing', 'GET');
        $this->assert($resUnauth['code'] === 302 && str_contains($resUnauth['headers'], 'Location: /login'), 'Acceso no autenticado a /email-marketing redirige a /login');

        // 2. Acceso autenticado
        $this->request('/login', 'POST', ['email' => 'jefe@miamiagency.com', 'password' => 'password123']);
        $res = $this->request('/email-marketing', 'GET');
        $this->assert($res['code'] === 200, 'GET /email-marketing autenticado responde HTTP 200');

        // 3. Tarjetas KPI ejecutivas idénticas al Dashboard
        $this->assert(str_contains($res['body'], 'exec-kpi-card kpi-navy'), 'Vista incluye tarjeta ejecutiva kpi-navy');
        $this->assert(str_contains($res['body'], 'exec-kpi-card kpi-steel'), 'Vista incluye tarjeta ejecutiva kpi-steel');
        $this->assert(str_contains($res['body'], 'exec-kpi-card kpi-bronze'), 'Vista incluye tarjeta ejecutiva kpi-bronze');
        $this->assert(str_contains($res['body'], 'exec-kpi-card kpi-sage'), 'Vista incluye tarjeta ejecutiva kpi-sage');
        $this->assert(str_contains($res['body'], 'exec-icon-box'), 'Tarjetas KPI incluyen contenedor de icono corporativo exec-icon-box');

        // 4. Calendario interactivo de 2 columnas estilo Dashboard
        $this->assert(str_contains($res['body'], 'emailCalGridDaysContainer'), 'Vista incluye contenedor de cuadrícula mensual interactiva');
        $this->assert(str_contains($res['body'], 'emailCalListContainer'), 'Vista incluye contenedor de agenda dinámica de campañas');
        $this->assert(str_contains($res['body'], 'calendar-grid'), 'Vista incluye clase CSS estándar calendar-grid');

        // 5. Barra de filtros por Realtor y tabla completa
        $this->assert(str_contains($res['body'], 'Filtrar Cronograma por Cliente Realtor'), 'Vista incluye barra de botones filtradores por Realtor');
        $this->assert(str_contains($res['body'], 'Fechas Estipuladas de Email Marketing'), 'Vista incluye tabla de registro completo');
        $this->assert(str_contains($res['body'], 'action="') && str_contains($res['body'], '/email-marketing/update-status"'), 'Vista incluye selector rápido de estado de campaña');

        // 6. Modal con validaciones en tiempo real
        $this->assert(str_contains($res['body'], 'id="modalNewEmailCampaign"'), 'Modal de programación de email presente');
        $this->assert(str_contains($res['body'], 'data-purpose=') && str_contains($res['body'], 'data-error-msg='), 'Formulario de modal instrumentado con validaciones en tiempo real');
    }
}

// Ejecutar suite
$suite = new BlackBoxTestSuite();
$suite->run();
