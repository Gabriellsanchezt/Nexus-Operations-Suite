<?php
/**
 * =====================================================================
 * SUITE DE PRUEBAS DE CAJA BLANCA (WHITE-BOX TESTING)
 * Nexus Operations Suite - Miami Luxury Media
 * =====================================================================
 * 
 * Evalúa la lógica interna, algoritmos, modelos, servicios, cobertura de 
 * condiciones, transacciones atómicas, restricciones 3NF y criptografía.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/config/config.php';

use Config\Auth;
use Config\Database;
use App\Models\UserModel;
use App\Models\RealtorModel;
use App\Models\ContractModel;
use App\Models\ActivityModel;
use App\Models\MeetingModel;
use App\Models\ReportModel;
use App\Services\NotificationService;

class WhiteBoxTestSuite {
    private int $passed = 0;
    private int $failed = 0;
    private array $failures = [];
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    public function run(): void {
        echo "\n" . str_repeat('=', 70) . "\n";
        echo " EJECUTANDO SUITE DE PRUEBAS DE CAJA BLANCA (WHITE-BOX)\n";
        echo " Evaluacion estructural de codigo, ramas, datos y seguridad interna\n";
        echo str_repeat('=', 70) . "\n\n";

        $startTime = microtime(true);

        $this->testPasswordHashingAndSecurity();
        $this->testRbacRolesAndSpecialtiesStructure();
        $this->testDatabaseNormalization3NF();
        $this->testUserModelAtomicTransactions();
        $this->testNotificationServiceLogic();
        $this->testMeetingModelAndGoogleCalendarSync();
        $this->testReportModel15And30DaysZeroMoney();
        $this->testContractModelIntegrity();
        $this->testRealtorModelProjectsIsolation();
        $this->testIdleSessionSecurityAndActivityTracking();
        $this->testUtf8EncodingAndAccentsIntegrity();

        $elapsed = round(microtime(true) - $startTime, 4);

        echo "\n" . str_repeat('-', 70) . "\n";
        echo " RESUMEN CAJA BLANCA: Total: " . ($this->passed + $this->failed) . " | ";
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
            echo "\nTODAS LAS PRUEBAS DE CAJA BLANCA PASARON EXITOSAMENTE (100% OK).\n\n";
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
     * 1. Criptografía y Seguridad de Credenciales (BCRYPT Costo 12)
     */
    private function testPasswordHashingAndSecurity(): void {
        echo "\n--- 1. Criptografia y Algoritmo de Hashing (BCRYPT Costo 12) ---\n";

        $plain = 'PruebaPasswordSegura123';
        $hash = password_hash($plain, PASSWORD_BCRYPT, ['cost' => 12]);

        $info = password_get_info($hash);
        $this->assert($info['algoName'] === 'bcrypt', 'Algoritmo detectado es BCRYPT', "Algoritmo: {$info['algoName']}");
        $this->assert($info['options']['cost'] === 12, 'Factor de costo criptografico es exactamente 12', "Costo: {$info['options']['cost']}");

        $verifyOk = password_verify($plain, $hash);
        $this->assert($verifyOk === true, 'Verificacion de clave correcta retorna TRUE');

        $verifyBad = password_verify('ClaveIncorrecta999', $hash);
        $this->assert($verifyBad === false, 'Verificacion de clave incorrecta retorna FALSE');

        // Verificar que los usuarios en base de datos usan costo 12
        $stmt = $this->pdo->query("SELECT email, password_hash FROM users LIMIT 1");
        $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
        $dbInfo = password_get_info($userRow['password_hash']);
        $this->assert($dbInfo['algoName'] === 'bcrypt' && $dbInfo['options']['cost'] === 12, 
            'Hash de usuario en base de datos esta en BCRYPT costo 12', 
            "Usuario: {$userRow['email']} tiene costo " . ($dbInfo['options']['cost'] ?? 'desconocido'));
    }

    /**
     * 2. Estructura de Roles (4) y Especialidades (6) del Sistema
     */
    private function testRbacRolesAndSpecialtiesStructure(): void {
        echo "\n--- 2. Estructura de Control de Acceso RBAC (4 Roles, 6 Especialidades) ---\n";

        $roles = $this->pdo->query("SELECT name FROM roles ORDER BY id ASC")->fetchAll(PDO::FETCH_COLUMN);
        $expectedRoles = ['JEFE', 'ASISTENTE', 'ESTRATEGA_MARKETING', 'SOCIAL_MANAGER'];
        $this->assert($roles === $expectedRoles, 'Existen exactamente los 4 roles corporativos aprobados', json_encode($roles));

        $specs = $this->pdo->query("SELECT code FROM specialties ORDER BY id ASC")->fetchAll(PDO::FETCH_COLUMN);
        $expectedSpecs = ['EDITOR_VIDEO', 'EDITOR_CARRUSELES', 'EMAIL_MARKETING', 'BLOGGERS', 'AGENDADOR_REUNIONES', 'AUTOMATIZADOR'];
        $this->assert($specs === $expectedSpecs, 'Existen exactamente las 6 especialidades tecnicas aprobadas', json_encode($specs));

        // Metodos estaticos Auth
        $this->assert(Auth::getRoleLabel('JEFE') === 'Jefe (Dirección)', 'Etiqueta de rol JEFE coincide');
        $this->assert(Auth::getRoleLabel('ASISTENTE') === 'Asistente Operativo', 'Etiqueta de rol ASISTENTE coincide');
        $this->assert(Auth::getRoleLabel('ESTRATEGA_MARKETING') === 'Estrategia de Marketing', 'Etiqueta de rol ESTRATEGA coincide');
        $this->assert(Auth::getRoleLabel('SOCIAL_MANAGER') === 'Social Manager', 'Etiqueta de rol SOCIAL_MANAGER coincide');
    }

    /**
     * 3. Normalizacion Relacional en 3NF y Verificacion de Cero Dinero
     */
    private function testDatabaseNormalization3NF(): void {
        echo "\n--- 3. Normalizacion Relacional 3NF y Regla Estricta CERO DINERO ---\n";

        // Tablas requeridas
        $requiredTables = [
            'roles', 'specialties', 'users', 'user_specialties', 'employees',
            'realtors', 'contracts', 'email_campaigns', 'meetings', 'activities',
            'activity_deliverables', 'activity_reviews'
        ];

        $stmt = $this->pdo->query("SHOW TABLES");
        $tablesInDb = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($requiredTables as $rt) {
            $this->assert(in_array($rt, $tablesInDb, true), "Tabla requerida '{$rt}' existe en MySQL");
        }

        // Inspeccionar que ninguna columna contenga terminos financieros
        $forbiddenKeywords = ['price', 'fee', 'cost', 'mrr', 'amount', 'salary', 'currency', 'dollar', 'tarifa', 'monto', 'pago'];
        $foundFinancialCols = [];

        foreach ($requiredTables as $table) {
            $colStmt = $this->pdo->query("DESCRIBE `{$table}`");
            $cols = $colStmt->fetchAll(PDO::FETCH_COLUMN);
            foreach ($cols as $col) {
                foreach ($forbiddenKeywords as $fk) {
                    if (stripos($col, $fk) !== false) {
                        $foundFinancialCols[] = "{$table}.{$col}";
                    }
                }
            }
        }

        $this->assert(empty($foundFinancialCols), 'Regla Cero Dinero: No existen columnas financieras o monetarias en la BD', json_encode($foundFinancialCols));

        // Verificar relacion M:N user_specialties (clave primaria compuesta)
        $usKeyStmt = $this->pdo->query("SHOW KEYS FROM user_specialties WHERE Key_name = 'PRIMARY'");
        $primaryCols = $usKeyStmt->fetchAll(PDO::FETCH_COLUMN, 4);
        $this->assert($primaryCols === ['user_id', 'specialty_id'], 'user_specialties tiene clave primaria compuesta (user_id, specialty_id) conforme a 3NF');
    }

    /**
     * 4. Transacciones Atomicas de UserModel y Prevencion de Auto-Suspension
     */
    private function testUserModelAtomicTransactions(): void {
        echo "\n--- 4. Modelo UserModel y Blindaje de Auto-Suspension ---\n";

        $userModel = new UserModel();

        // Intentar que el admin ID 1 se suspenda a si mismo
        $selfSuspensionBlocked = false;
        try {
            $userModel->toggleUserStatus(1, 1);
        } catch (\InvalidArgumentException $e) {
            $selfSuspensionBlocked = true;
        }

        $this->assert($selfSuspensionBlocked === true, 'El administrador no puede suspender su propia cuenta');

        // Test perfil de usuario
        $profile = $userModel->getUserProfile(1);
        $this->assert($profile !== null && $profile['email'] === 'jefe@miamiagency.com', 'getUserProfile retorna perfil integro para Jefe');
        $this->assert(isset($profile['specialties']), 'getUserProfile incluye arreglo relacional de especialidades');
    }

    /**
     * 5. Logica de Notificaciones y Filtrado por Estado de Lectura
     */
    private function testNotificationServiceLogic(): void {
        echo "\n--- 5. Servicio de Notificaciones y Filtrado en Sesion ---\n";

        $jefeUser = ['id' => 1, 'role' => 'JEFE', 'employee_id' => 1];
        $allNotifs = NotificationService::getUserNotifications($jefeUser, false);
        $this->assert(is_array($allNotifs), 'getUserNotifications sin filtro retorna arreglo');

        // Simular marcado en sesion
        if (!empty($allNotifs)) {
            $testId = $allNotifs[0]['id'];
            $_SESSION['read_notification_ids'] = [$testId => true];

            $filteredNotifs = NotificationService::getUserNotifications($jefeUser, true);
            $foundFiltered = false;
            foreach ($filteredNotifs as $fn) {
                if ($fn['id'] === $testId) {
                    $foundFiltered = true;
                    break;
                }
            }
            $this->assert($foundFiltered === false, "Notificacion {$testId} fue excluida correctamente al marcarse como leida en sesion");
            unset($_SESSION['read_notification_ids']);
        } else {
            $this->assert(true, 'Sin notificaciones pendientes previas para probar filtro');
        }
    }

    /**
     * 6. Sincronizacion de Google Calendar y Generacion de Archivos ICS (RFC 5545)
     */
    private function testMeetingModelAndGoogleCalendarSync(): void {
        echo "\n--- 6. Agenda de Reuniones, Enlace Google Calendar y Estandar ICS ---\n";

        $testMeeting = [
            'id' => 999,
            'title' => 'Reunion Ejecutiva de Prueba',
            'description' => 'Alineacion semanal de produccion',
            'meeting_date' => '2026-11-20',
            'meeting_time' => '14:30:00',
            'duration_minutes' => 45,
            'company_name' => 'Brickell Real Estate Group',
            'contact_person' => 'Elena Rostova',
            'realtor_email' => 'elena.rostova@sothebysrealty.com',
            'employee_name' => 'Valentina Rossi',
            'employee_email' => 'asistente@miamiagency.com',
            'miami_zone' => 'Brickell / Financial District'
        ];

        // 1. Enlace Google Calendar configurado como alarma personal del gestor (sin parametro add para no emitir invitaciones directas)
        $gUrl = MeetingModel::buildGoogleCalendarUrl($testMeeting);
        $this->assert(str_starts_with($gUrl, 'https://calendar.google.com/calendar/render?action=TEMPLATE'), 'Enlace de Google Calendar cumple con esquema oficial');
        $this->assert(str_contains($gUrl, '20261120T143000'), 'Fecha y hora de inicio formateadas en estandar ISO (20261120T143000)');
        $this->assert(str_contains($gUrl, '20261120T151500'), 'Calculo de duracion sumado correctamente (45 min -> 151500)');
        $this->assert(str_contains(urldecode($gUrl), 'Recordatorio: Valentina Rossi con Brickell Real Estate Group'), 'Titulo formateado como alarma explicita para el telefono del gestor');
        $this->assert(str_contains(urldecode($gUrl), 'asistente@miamiagency.com'), 'Cuerpo del evento incluye correo de contacto del trabajador');
        $this->assert(str_contains(urldecode($gUrl), 'elena.rostova@sothebysrealty.com'), 'Cuerpo del evento incluye correo de contacto del cliente');
        $this->assert(!str_contains($gUrl, 'add='), 'Enlace excluye intencionalmente parametro add para que suene exclusivamente en el telefono del gestor');

        // 2. Generador ICS (RFC 5545) con alarmas sonoras nativas a 30m y 15m
        $ics = MeetingModel::generateIcs($testMeeting);
        $this->assert(str_contains($ics, 'BEGIN:VCALENDAR'), 'ICS contiene cabecera BEGIN:VCALENDAR');
        $this->assert(str_contains($ics, 'BEGIN:VEVENT'), 'ICS contiene bloque BEGIN:VEVENT');
        $this->assert(str_contains($ics, 'SUMMARY:Recordatorio: Valentina Rossi con Brickell Real Estate Group'), 'ICS contiene SUMMARY formateado como recordatorio');
        $this->assert(str_contains($ics, 'BEGIN:VALARM'), 'ICS incluye bloques de alarma nativa VALARM');
        $this->assert(str_contains($ics, 'TRIGGER:-PT30M'), 'ICS incluye alarma sonora a los 30 minutos previos');
        $this->assert(str_contains($ics, 'TRIGGER:-PT15M'), 'ICS incluye alarma sonora a los 15 minutos previos');
        $this->assert(str_contains($ics, 'asistente@miamiagency.com'), 'ICS contiene en descripcion los datos de contacto del trabajador asignado');
        $this->assert(str_contains($ics, 'END:VCALENDAR'), 'ICS contiene pie END:VCALENDAR');
        $this->assert(str_contains($ics, "\r\n"), 'ICS cumple con separadores de linea CRLF estandar RFC 5545');

        // 3. Consulta en base de datos incluye datos del trabajador asignado
        $model = new MeetingModel();
        $all = $model->getAll();
        $this->assert(!empty($all), 'MeetingModel::getAll() retorna reuniones');
        $this->assert(array_key_exists('employee_name', $all[0]), 'getAll incluye campo employee_name');
        $this->assert(array_key_exists('employee_email', $all[0]), 'getAll incluye campo employee_email');
    }

    /**
     * 7. Modelo de Reportes Ejecutivos: Filtros de 15/30 Dias y Cero Dinero
     */
    private function testReportModel15And30DaysZeroMoney(): void {
        echo "\n--- 7. Reportes Ejecutivos (15 / 30 Dias y Ausencia de Dinero) ---\n";

        $reportModel = new ReportModel();

        // 15 dias
        $rep15 = $reportModel->getExecutiveReport(15);
        $this->assert(isset($rep15['performance']['completion_rate']), 'Reporte 15 dias contiene tasa de rendimiento');
        $this->assert(isset($rep15['staff_productivity']), 'Reporte 15 dias contiene lista de productividad del personal');
        $this->assert($rep15['days'] === 15, 'Periodo de reporte es exactamente 15 dias');

        // 30 dias
        $rep30 = $reportModel->getExecutiveReport(30);
        $this->assert($rep30['days'] === 30, 'Periodo de reporte es exactamente 30 dias');

        // Validar que el reporte no contenga ninguna clave con simbolos monetarios o precios
        $financialKeys = ['money', 'price', 'fee', 'dollar', 'mrr', 'total_revenue', 'billing', 'tarifa'];
        $foundInRep = false;
        foreach ($financialKeys as $fk) {
            if (isset($rep15['performance'][$fk]) || isset($rep30['performance'][$fk])) {
                $foundInRep = true;
                break;
            }
        }
        $this->assert($foundInRep === false, 'El payload de Reportes Ejecutivos respeta la directiva de Cero Metricas Monetarias');
    }

    /**
     * 8. Integridad del Modelo de Contratos Finales (PDF)
     */
    private function testContractModelIntegrity(): void {
        echo "\n--- 8. Modelo de Contratos Finales (PDFs) ---\n";

        $contractModel = new ContractModel();
        $contracts = $contractModel->getAll();
        $this->assert(is_array($contracts), 'ContractModel::getAll() retorna listado de contratos');

        $stats = $contractModel->getStats();
        $this->assert(isset($stats['total']), 'Estadisticas de contratos contienen total');
        $this->assert(isset($stats['realtors_count']), 'Estadisticas de contratos contienen realtors_count');
        $this->assert(!isset($stats['total_value']), 'Estadisticas de contratos no calculan valores monetarios');
    }

    /**
     * 9. Aislamiento de Proyectos del Realtor (Sin Mezclar Contratos en Boton Ver)
     */
    private function testRealtorModelProjectsIsolation(): void {
        echo "\n--- 9. Aislamiento de Expedientes de Realtor (Boton Ver Proyectos) ---\n";

        $realtorModel = new RealtorModel();
        $realtors = $realtorModel->getAll();
        $this->assert(!empty($realtors), 'Existen clientes Realtors registrados en la base de datos');

        $firstRealtor = $realtors[0];
        $projects = $realtorModel->getProjects((int)$firstRealtor['id']);
        $this->assert(is_array($projects), 'RealtorModel::getProjects() retorna arreglo de proyectos');

        // Verificar que cada proyecto pertenece al Realtor consultado
        $allBelong = true;
        foreach ($projects as $p) {
            if ((int)$p['realtor_id'] !== (int)$firstRealtor['id']) {
                $allBelong = false;
                break;
            }
        }
        $this->assert($allBelong === true, 'Todos los proyectos devueltos pertenecen estrictamente al Realtor seleccionado');
    }

    /**
     * 10. Control de Inactividad de Sesión y Heartbeat (Idle Security)
     */
    private function testIdleSessionSecurityAndActivityTracking(): void {
        echo "\n--- 10. Control de Inactividad de Sesion y Heartbeat (Idle Security) ---\n";

        // 1. Constante de tiempo límite configurada en 15 minutos (900s)
        $this->assert(defined('\Config\Auth::IDLE_TIMEOUT'), 'Constante Auth::IDLE_TIMEOUT esta definida');
        $this->assert(Auth::IDLE_TIMEOUT === 900, 'Auth::IDLE_TIMEOUT equivale exactamente a 900 segundos (15 min)');

        // 2. Simulación de sesión activa
        Auth::initSession();
        $_SESSION['user_id'] = 999;
        $_SESSION['role'] = 'JEFE';
        $_SESSION['email'] = 'test_idle@miamiagency.com';
        $_SESSION['last_activity'] = time();

        $this->assert(Auth::check() === true, 'Sesión con actividad reciente es evaluada como activa');
        $this->assert(Auth::getLastActivity() !== null, 'Auth::getLastActivity() retorna timestamp valido');

        // 3. Touch de actividad actualiza la marca
        $prevTime = time() - 10;
        $_SESSION['last_activity'] = $prevTime;
        Auth::touchActivity();
        $this->assert(Auth::getLastActivity() >= $prevTime, 'Auth::touchActivity() renueva marca temporal de actividad');

        // 4. Expiración por inactividad superior al umbral
        $_SESSION['last_activity'] = time() - (Auth::IDLE_TIMEOUT + 60); // 960 segundos atrás
        $checkExpired = Auth::check();
        $this->assert($checkExpired === false, 'Auth::check() invalida sesion inactiva por mas de 900 segundos');
        $this->assert(!isset($_SESSION['user_id']), 'Auth::check() destruye datos de sesion al expirar por inactividad');

        // Limpiar sesión de pruebas
        Auth::logout();
    }

    /**
     * 11. Integridad de Codificación Internacional UTF-8 y Acentos en Español
     */
    private function testUtf8EncodingAndAccentsIntegrity(): void {
        echo "\n--- 11. Codificación Internacional UTF-8 y Preservación de Acentos ---\n";

        // 1. Verificar charset de la base de datos
        $stmt = $this->pdo->query("SELECT default_character_set_name, default_collation_name FROM information_schema.SCHEMATA WHERE schema_name = 'miami_agency_db'");
        $dbCharset = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assert($dbCharset['default_character_set_name'] === 'utf8mb4', 'Charset predeterminado de la BD es utf8mb4');
        $this->assert(str_starts_with($dbCharset['default_collation_name'], 'utf8mb4_'), 'Collation predeterminada de la BD es compatible con utf8mb4');

        // 2. Verificar acentos en roles
        $rolesStmt = $this->pdo->query("SELECT id, name, description FROM roles ORDER BY id ASC");
        $roles = $rolesStmt->fetchAll(PDO::FETCH_ASSOC);
        $this->assert(str_contains($roles[0]['description'], 'Dirección') && str_contains($roles[0]['description'], 'supervisión'), 'Rol Jefe preserva acentos (Dirección, supervisión)');
        $this->assert(str_contains($roles[1]['description'], 'gestión'), 'Rol Asistente preserva acentos (gestión)');
        $this->assert(str_contains($roles[2]['description'], 'campañas'), 'Rol Estratega preserva la letra ñ (campañas)');
        $this->assert(str_contains($roles[3]['description'], 'producción'), 'Rol Social Manager preserva acentos (producción)');

        // 3. Verificar acentos en especialidades
        $specStmt = $this->pdo->query("SELECT code, description FROM specialties WHERE code = 'EDITOR_VIDEO'")->fetch(PDO::FETCH_ASSOC);
        $this->assert(str_contains($specStmt['description'], 'Edición'), 'Especialidad EDITOR_VIDEO contiene acento (Edición)');

        // 4. Verificar ausencia de caracteres dañados o corruptos (doble interrogación ??)
        $tables = ['roles', 'specialties', 'contracts', 'meetings', 'activities', 'email_campaigns'];
        $hasCorruptedMarks = false;
        foreach ($tables as $tbl) {
            $rows = $this->pdo->query("SELECT * FROM {$tbl}")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $r) {
                foreach ($r as $val) {
                    if (is_string($val) && str_contains($val, '??')) {
                        $hasCorruptedMarks = true;
                        break 3;
                    }
                }
            }
        }
        $this->assert($hasCorruptedMarks === false, 'Ningun registro en la base de datos contiene secuencias corruptas (??)');
    }
}

// Ejecutar suite
$suite = new WhiteBoxTestSuite();
$suite->run();
