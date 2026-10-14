<?php
/**
 * Servicio Centralizado de Notificaciones y Alertas del Sistema
 * Soporta los 4 roles oficiales: JEFE, ASISTENTE, ESTRATEGA_MARKETING, SOCIAL_MANAGER
 * Integrado con actividades, contratos finales PDF, reuniones y entregables
 */

namespace App\Services;

use Config\Database;
use Config\Auth;
use PDO;

class NotificationService {
    /**
     * Obtener listado de notificaciones activas para el usuario en sesión
     */
    public static function getUserNotifications(array $user, bool $filterRead = true): array {
        if (empty($user['id'])) {
            return [];
        }

        $readIds = ($filterRead && isset($_SESSION['read_notification_ids'])) 
            ? (array)$_SESSION['read_notification_ids'] 
            : [];

        $notifications = [];
        $pdo = Database::getConnection();
        $isJefe = in_array($user['role'] ?? '', ['JEFE', 'JEFE_ADMIN'], true);

        if ($isJefe) {
            // 1. Actividades con entregables en revisión
            $stmt = $pdo->query("
                SELECT a.id, a.title, a.priority, a.scheduled_date, a.updated_at, e.full_name AS employee_name
                FROM activities a
                JOIN employees e ON a.employee_id = e.id
                WHERE a.current_status = 'ENTREGADA_REVISION'
                ORDER BY a.updated_at DESC
                LIMIT 5
            ");
            $pendingActivities = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($pendingActivities as $a) {
                $notifId = 'act_rev_' . $a['id'];
                if ($filterRead && !empty($readIds[$notifId])) continue;
                $notifications[] = [
                    'id' => $notifId,
                    'type' => 'info',
                    'badge' => 'Entregable',
                    'icon' => 'bi-hourglass-split text-info',
                    'title' => htmlspecialchars($a['title']),
                    'message' => 'Entregable subido por ' . htmlspecialchars($a['employee_name']) . ' &bull; Prioridad: ' . htmlspecialchars($a['priority']),
                    'link' => function_exists('url') ? url('/activities?status=ENTREGADA_REVISION') : '/activities?status=ENTREGADA_REVISION',
                    'action_text' => 'Evaluar',
                    'time' => date('d M, h:i A', strtotime($a['updated_at'] ?? 'now'))
                ];
            }

            // 2. Próximas reuniones de agenda
            $stmtM = $pdo->query("
                SELECT m.id, m.title, m.meeting_date, m.meeting_time, r.company_name
                FROM meetings m
                LEFT JOIN realtors r ON m.realtor_id = r.id
                WHERE m.meeting_date >= CURDATE()
                ORDER BY m.meeting_date ASC, m.meeting_time ASC
                LIMIT 3
            ");
            $upcomingMeetings = $stmtM->fetchAll(PDO::FETCH_ASSOC);

            foreach ($upcomingMeetings as $m) {
                $notifId = 'meeting_' . $m['id'];
                if ($filterRead && !empty($readIds[$notifId])) continue;
                $notifications[] = [
                    'id' => $notifId,
                    'type' => 'primary',
                    'badge' => 'Reunión',
                    'icon' => 'bi-calendar-event-fill text-primary',
                    'title' => htmlspecialchars($m['title']),
                    'message' => ($m['company_name'] ? htmlspecialchars($m['company_name']) . ' &bull; ' : '') . date('d/m/Y', strtotime($m['meeting_date'])) . ' a las ' . substr($m['meeting_time'], 0, 5),
                    'link' => function_exists('url') ? url('/agenda') : '/agenda',
                    'action_text' => 'Ver Agenda',
                    'time' => htmlspecialchars($m['meeting_date'])
                ];
            }

            // 3. Contratos finales subidos recientemente
            $stmtC = $pdo->query("
                SELECT c.id, c.contract_title, c.created_at, r.company_name
                FROM contracts c
                JOIN realtors r ON c.realtor_id = r.id
                ORDER BY c.created_at DESC
                LIMIT 3
            ");
            $recentContracts = $stmtC->fetchAll(PDO::FETCH_ASSOC);

            foreach ($recentContracts as $c) {
                $notifId = 'ctr_final_' . $c['id'];
                if ($filterRead && !empty($readIds[$notifId])) continue;
                $notifications[] = [
                    'id' => $notifId,
                    'type' => 'danger',
                    'badge' => 'Contrato PDF',
                    'icon' => 'bi-file-earmark-pdf-fill text-danger',
                    'title' => htmlspecialchars($c['contract_title']),
                    'message' => 'Realtor: ' . htmlspecialchars($c['company_name']),
                    'link' => function_exists('url') ? url('/contracts/final') : '/contracts/final',
                    'action_text' => 'Ver PDF',
                    'time' => date('d M', strtotime($c['created_at']))
                ];
            }
        } else {
            // Empleados y colaboradores (Asistente, Estratega, Social Manager)
            $employeeId = $user['employee_id'] ?? null;

            if ($employeeId) {
                // Tareas asignadas que requieren atención
                $stmt = $pdo->prepare("
                    SELECT a.id, a.title, a.current_status, a.scheduled_date, a.updated_at
                    FROM activities a
                    WHERE a.employee_id = ? AND a.current_status IN ('NUEVA', 'CORRECCION')
                    ORDER BY a.updated_at DESC
                    LIMIT 5
                ");
                $stmt->execute([(int)$employeeId]);
                $myTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($myTasks as $t) {
                    $isCorrec = ($t['current_status'] === 'CORRECCION');
                    $notifId = 'emp_task_' . $t['id'] . '_' . $t['current_status'];
                    if ($filterRead && !empty($readIds[$notifId])) continue;

                    $notifications[] = [
                        'id' => $notifId,
                        'type' => $isCorrec ? 'danger' : 'primary',
                        'badge' => $isCorrec ? 'Corrección' : 'Nueva Tarea',
                        'icon' => $isCorrec ? 'bi-exclamation-octagon-fill text-danger' : 'bi-play-circle-fill text-primary',
                        'title' => htmlspecialchars($t['title']),
                        'message' => $isCorrec ? 'Dirección devolvió con observaciones para ajuste' : 'Nueva tarea programada para ' . htmlspecialchars($t['scheduled_date']),
                        'link' => function_exists('url') ? url('/activities/my-tasks') : '/activities/my-tasks',
                        'action_text' => 'Abrir',
                        'time' => date('d M', strtotime($t['updated_at'] ?? 'now'))
                    ];
                }
            }

            // Próximas reuniones
            $stmtM = $pdo->query("
                SELECT m.id, m.title, m.meeting_date, m.meeting_time, r.company_name
                FROM meetings m
                LEFT JOIN realtors r ON m.realtor_id = r.id
                WHERE m.meeting_date >= CURDATE()
                ORDER BY m.meeting_date ASC, m.meeting_time ASC
                LIMIT 2
            ");
            $upcomingMeetings = $stmtM->fetchAll(PDO::FETCH_ASSOC);

            foreach ($upcomingMeetings as $m) {
                $notifId = 'emp_meet_' . $m['id'];
                if ($filterRead && !empty($readIds[$notifId])) continue;
                $notifications[] = [
                    'id' => $notifId,
                    'type' => 'primary',
                    'badge' => 'Reunión',
                    'icon' => 'bi-calendar-event-fill text-primary',
                    'title' => htmlspecialchars($m['title']),
                    'message' => date('d/m/Y', strtotime($m['meeting_date'])) . ' a las ' . substr($m['meeting_time'], 0, 5),
                    'link' => function_exists('url') ? url('/agenda') : '/agenda',
                    'action_text' => 'Ver Agenda',
                    'time' => htmlspecialchars($m['meeting_date'])
                ];
            }
        }

        return $notifications;
    }
}
