<?php
/**
 * Conexión a Base de Datos (Singleton PDO)
 * Soporta MySQL, MariaDB, PostgreSQL y SQLite para máxima portabilidad y pruebas inmediatas.
 */

namespace Config;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;

    private function __construct() {}
    private function __clone() {}

    /**
     * Obtener instancia única de conexión PDO
     */
    public static function getConnection(): PDO {
        if (self::$instance === null) {
            self::connect();
        }
        return self::$instance;
    }

    private static function connect(): void {
        $driver = defined('DB_DRIVER') ? DB_DRIVER : 'mysql';

        try {
            if ($driver === 'pgsql') {
                $dsn = sprintf("pgsql:host=%s;port=%s;dbname=%s", DB_HOST, DB_PORT, DB_DATABASE);
                self::$instance = new PDO($dsn, DB_USERNAME, DB_PASSWORD, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
            } else {
                // Default Oficial: MySQL / MariaDB (XAMPP)
                $dsn = sprintf("mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4", DB_HOST, DB_PORT, DB_DATABASE);
                self::$instance = new PDO($dsn, DB_USERNAME, DB_PASSWORD, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ]);

                // Verificación y auto-población de datos en MySQL
                self::checkAndInitializeMysql(self::$instance);
            }
        } catch (PDOException $e) {
            throw new PDOException("Error fatal en conexión de base de datos MySQL (XAMPP): " . $e->getMessage());
        }
    }

    /**
     * Verificar e inicializar esquema y datos base en MySQL si la BD está vacía
     */
    private static function checkAndInitializeMysql(PDO $pdo): void {
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE 'roles'");
            $tableExists = $stmt->fetch();

            if (!$tableExists) {
                $schemaFile = BASE_PATH . '/database/schema_mysql.sql';
                if (file_exists($schemaFile)) {
                    $sql = file_get_contents($schemaFile);
                    $pdo->exec($sql);
                }
            }

            // Verificar si hay registros de usuarios
            $userCount = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
            if ($userCount === 0) {
                self::seedInitialData($pdo);
            }
        } catch (\Throwable $e) {
            error_log("Error inicializando esquema MySQL: " . $e->getMessage());
        }
    }



    /**
     * Población inicial con datos realistas para la agencia en Miami
     */
    private static function seedInitialData(PDO $pdo): void {
        // 1. Roles
        $pdo->exec("
            INSERT IGNORE INTO roles (id, name, description) VALUES
            (1, 'JEFE', 'Dirección General, supervisión total y control operativo'),
            (2, 'ASISTENTE', 'Asistente Operativo, gestión de agendas, Realtors y soporte'),
            (3, 'ESTRATEGA_MARKETING', 'Estrategia de Marketing, cronograma de emails y campañas'),
            (4, 'SOCIAL_MANAGER', 'Social Media Manager, producción y entregables de contenido');
        ");

        // 2. Especialidades Granulares
        $pdo->exec("
            INSERT IGNORE INTO specialties (id, code, name, description) VALUES
            (1, 'EDITOR_VIDEO', 'Editor de Videos', 'Edición de reels, videos de propiedades y walkthroughs'),
            (2, 'EDITOR_CARRUSELES', 'Editor de Carruseles', 'Diseño de carruseles de propiedades e infografías'),
            (3, 'EMAIL_MARKETING', 'Email Marketing', 'Campañas y newsletters para base de datos de Realtors'),
            (4, 'BLOGGERS', 'Bloggers', 'Redacción de artículos de blog y contenido editorial'),
            (5, 'AGENDADOR_REUNIONES', 'Agendador de Reuniones', 'Coordinación y agenda de llamadas con clientes y prospectos'),
            (6, 'AUTOMATIZADOR', 'Automatizador', 'Automatización de flujos de trabajo, CRM y secuencias');
        ");

        // 3. Usuarios iniciales (password: 'password123' hasheada con bcrypt cost 12)
        $passHash = password_hash('password123', PASSWORD_BCRYPT, ['cost' => 12]);

        $stmt = $pdo->prepare("
            INSERT IGNORE INTO users (id, email, password_hash, role_id, status) VALUES
            (1, 'jefe@miamiagency.com', ?, 1, 'ACTIVO'),
            (2, 'asistente@miamiagency.com', ?, 2, 'ACTIVO'),
            (3, 'estrategia@miamiagency.com', ?, 3, 'ACTIVO'),
            (4, 'social@miamiagency.com', ?, 4, 'ACTIVO')
        ");
        $stmt->execute([$passHash, $passHash, $passHash, $passHash]);

        // 4. Perfil de Empleados
        $pdo->exec("
            INSERT INTO employees (id, user_id, full_name, phone, hire_date) VALUES
            (1, 1, 'Carlos Mendoza (Jefe)', '+1 (305) 555-0101', '2024-01-15'),
            (2, 2, 'Valentina Rossi (Asistente)', '+1 (305) 555-0102', '2024-03-01'),
            (3, 3, 'Sofía Herrera (Estratega Mkt)', '+1 (305) 555-0103', '2024-04-10'),
            (4, 4, 'Mateo Silva (Social Manager)', '+1 (305) 555-0104', '2024-05-20');
        ");

        // 5. Asignación de especialidades
        $pdo->exec("
            INSERT INTO user_specialties (user_id, specialty_id) VALUES
            (2, 5),
            (3, 3),
            (3, 4),
            (3, 6),
            (4, 1),
            (4, 2);
        ");

        // 6. Realtors en Miami con enlaces a Bóveda de Credenciales y Dropbox
        $pdo->exec("
            INSERT INTO realtors (id, company_name, contact_person, phone, email, miami_zone, credentials_url, dropbox_url, social_handles, status) VALUES
            (1, 'One Sotheby’s International Realty', 'Elena Rostova', '+1 (305) 789-4321', 'elena.rostova@sothebysrealty.com', 'Brickell / Financial District', 'https://vault.miamiluxury.internal/realtor/elena-sothebys', 'https://www.dropbox.com/sh/sothebys-elena-brickell/AAAw123', '{\"instagram\":\"@elena_luxury_brickell\",\"tiktok\":\"@elenarostovamiami\"}', 'ACTIVO'),
            (2, 'The Jills Zeder Group / Coldwell Banker', 'Marcus Sterling', '+1 (305) 890-1122', 'marcus.s@jillszeder.com', 'Miami Beach / South Beach', 'https://vault.miamiluxury.internal/realtor/marcus-coldwell', 'https://www.dropbox.com/sh/coldwell-marcus-beach/BBBw456', '{\"instagram\":\"@marcus_southbeach_estates\"}', 'ACTIVO'),
            (3, 'Compass Florida Luxury Division', 'Sofia Alvarez', '+1 (305) 441-9988', 'sofia.alvarez@compass.com', 'Coral Gables', 'https://vault.miamiluxury.internal/realtor/sofia-compass', 'https://www.dropbox.com/sh/compass-sofia-gables/CCCw789', '{\"instagram\":\"@sofiaalvarez_coralgables\"}', 'ACTIVO'),
            (4, 'Fortune International Group', 'David Goldstein', '+1 (305) 672-3344', 'david.g@fortuneintl.com', 'Sunny Isles Beach', 'https://vault.miamiluxury.internal/realtor/david-fortune', 'https://www.dropbox.com/sh/fortune-david-sunnyisles/DDDw012', '{\"instagram\":\"@goldstein_sunnyisles\"}', 'ACTIVO');
        ");

        // 7. Contratos Finales (Almacén de PDFs Concluidos con Fecha de Subida Automática)
        $pdo->exec("
            INSERT INTO contracts (id, realtor_id, contract_title, pdf_filename, notes, uploaded_by_user_id, created_at) VALUES
            (1, 1, 'Acuerdo Operativo Audiovisual Anual - One Sothebys Brickell', 'contrato_final_sothebys_elena_2026.pdf', 'Contrato firmado legalmente y validado.', 1, CURRENT_TIMESTAMP),
            (2, 2, 'Convenio de Producción Mensual Redes - The Jills Zeder', 'contrato_marcus_coldwell_final.pdf', 'PDF subido concluido por gerencia.', 2, CURRENT_TIMESTAMP);
        ");

        // 8. Actividades Audiovisuales iniciales
        $pdo->exec("
            INSERT INTO activities (id, title, description, realtor_id, employee_id, contract_id, activity_type, priority, scheduled_date, current_status) VALUES
            (1, 'Edición Reel Penthouse Brickell Flatiron', 'Montaje con música de tendencia y tomas de balcón al atardecer', 1, 4, 1, 'EDICION_VIDEO', 'ALTA', '2026-10-12', 'EN_PROCESO'),
            (2, 'Carrusel 5 Tips de Inversión en South Beach', 'Diseño de 7 láminas en Canva/Photoshop con paleta de colores de Coldwell', 2, 4, 2, 'CARRUSEL_FOTO', 'MEDIA', '2026-10-14', 'NUEVA'),
            (3, 'Grabación Dron & Gimbal Mansión Coral Gables', 'Rodaje en locación con Realtor Marcus Sterling a las 9:00 AM', 3, 4, NULL, 'RODAJE_CAMARA', 'URGENTE', '2026-10-15', 'NUEVA'),
            (4, 'Edición Video Walkthrough Sunny Isles', 'Recorte de tomas y etalonaje en DaVinci Resolve', 4, 4, NULL, 'EDICION_VIDEO', 'MEDIA', '2026-10-10', 'ENTREGADA_REVISION');

            INSERT INTO activity_deliverables (id, activity_id, deliverable_url, deliverable_type, notes) VALUES
            (1, 4, 'https://frame.io/v/miami-sunny-isles-walkthrough-v1', 'FRAME_IO', 'Versión preliminar con corrección de color y música instrumental aprobada.');
        ");

        // 9. Campañas Email Marketing
        $pdo->exec("
            INSERT INTO email_campaigns (id, realtor_id, title, scheduled_date, status, notes) VALUES
            (1, 1, 'Newsletter Mensual: Oportunidades Pre-construcción Brickell', '2026-10-18', 'PROGRAMADO', 'Base de datos compradores calificados de Elena Rostova'),
            (2, 2, 'Email Blast: Reducción de Precio Mansión Venetian Islands', '2026-10-20', 'PROGRAMADO', 'Filtro contactos VIP Miami Beach');
        ");

        // 10. Reuniones Agendadas
        $pdo->exec("
            INSERT INTO meetings (id, title, realtor_id, meeting_date, meeting_time, duration_minutes, meeting_link, description, status) VALUES
            (1, 'Revisión Estratégica Trimestral con Elena Rostova', 1, '2026-10-16', '10:00:00', 45, 'https://meet.google.com/abc-defg-hij', 'Alineación de lanzamientos audiovisuales en Brickell', 'PROGRAMADA'),
            (2, 'Kickoff Campaña de Dron con Marcus Sterling', 2, '2026-10-17', '14:30:00', 30, 'https://meet.google.com/xyz-uvwx-rst', 'Coordinación logística de acceso a propiedad en South Beach', 'PROGRAMADA');
        ");
    }
}
