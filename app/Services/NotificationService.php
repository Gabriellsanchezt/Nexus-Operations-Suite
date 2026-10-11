<?php
/**
 * Servicio Centralizado de Notificaciones en Tiempo Real
 * Alimenta los Popovers de Bootstrap interactivos para el Jefe y Empleados
 * Permite marcar notificaciones individuales o masivas como vistas
 */

declare(strict_types=1);

namespace App\Services;

use Config\Database;
use Config\Auth;
use PDO;

class NotificationService {
    /**
     * Obtener listado de notificaciones activas para el usuario conectado
     *
     * @param array|null $user
     * @param bool $filterRead Si es true, excluye las que han sido marcadas como vistas en sesión
     * @return array
     */
    public static function getUserNotifications(?array $user = null, bool $filterRead = true): array {
        if (!$user) {
            $user = Auth::user();
        }
        if (!$user) {
            return [];
        }

        $readIds = ($filterRead && isset($_SESSION['read_notification_ids'])) 
            ? (array)$_SESSION['read_notification_ids'] 
            : [];

        $notifications = [];
        $pdo = Database::getConnection();

        if (($user['role'] ?? '') === 'JEFE_ADMIN') {
            // 1. Contratos en revisión esperando decisión de aprobación (individuales)
            $stmt = $pdo->query("
                SELECT c.id, c.contract_code, c.monthly_fee, c.updated_at, r.company_name
                FROM contracts c
                JOIN realtors r ON c.realtor_id = r.id
                WHERE c.status = 'EN_REVISION'
                ORDER BY c.updated_at DESC
                LIMIT 5
            ");
            $pendingContracts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($pendingContracts as $c) {
                $notifId = 'contract_' . $c['id'];
                if ($filterRead && !empty($readIds[$notifId])) {
                    continue;
                }
                $notifications[] = [
                    'id' => $notifId,
                    'type' => 'warning',
                    'badge' => 'Contrato',
                    'icon' => 'bi-file-earmark-check-fill text-warning',
                    'title' => 'Contrato ' . htmlspecialchars($c['contract_code']),
                    'message' => htmlspecialchars($c['company_name']) . ' &bull; Producción Mensual',
                    'link' => function_exists('url') ? url('/contracts/approvals') : '/contracts/approvals',
                    'action_text' => 'Revisar',
                    'time' => date('d M, h:i A', strtotime($c['updated_at'] ?? 'now'))
                ];
            }

            // 2. Actividades / videos entregados en revisión (individuales)
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
                $notifId = 'activity_review_' . $a['id'];
                if ($filterRead && !empty($readIds[$notifId])) {
                    continue;
                }
                $notifications[] = [
                    'id' => $notifId,
                    'type' => 'info',
                    'badge' => 'Entregable',
                    'icon' => 'bi-hourglass-split text-info',
                    'title' => htmlspecialchars($a['title']),
                    'message' => 'Por ' . htmlspecialchars($a['employee_name']) . ' &bull; ' . htmlspecialchars($a['priority']),
                    'link' => function_exists('url') ? url('/activities?status=ENTREGADA_REVISION') : '/activities?status=ENTREGADA_REVISION',
                    'action_text' => 'Evaluar',
                    'time' => date('d M, h:i A', strtotime($a['updated_at'] ?? $a['scheduled_date'] ?? 'now'))
                ];
            }

            // 3. Tareas nuevas en cola (individuales)
            $stmt = $pdo->query("
                SELECT a.id, a.title, a.priority, a.scheduled_date, r.company_name
                FROM activities a
                JOIN realtors r ON a.realtor_id = r.id
                WHERE a.current_status = 'NUEVA'
                ORDER BY a.id DESC
                LIMIT 3
            ");
            $newActivities = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($newActivities as $na) {
                $notifId = 'activity_new_' . $na['id'];
                if ($filterRead && !empty($readIds[$notifId])) {
                    continue;
                }
                $notifications[] = [
                    'id' => $notifId,
                    'type' => 'primary',
                    'badge' => 'Nueva',
                    'icon' => 'bi-plus-circle text-primary',
                    'title' => htmlspecialchars($na['title']),
                    'message' => htmlspecialchars($na['company_name']) . ' &bull; Fecha: ' . htmlspecialchars($na['scheduled_date']),
                    'link' => function_exists('url') ? url('/activities?status=NUEVA') : '/activities?status=NUEVA',
                    'action_text' => 'Ver tablero',
                    'time' => date('d M', strtotime($na['scheduled_date'] ?? 'now'))
                ];
            }
        } else {
            // EMPLEADO
            $employeeId = $user['employee_id'] ?? null;
            if ($employeeId) {
                // 1. Tareas devueltas para corrección (individuales)
                $stmt = $pdo->prepare("
                    SELECT id, title, priority, updated_at FROM activities
                    WHERE employee_id = ? AND current_status = 'CORRECCION'
                    ORDER BY updated_at DESC
                    LIMIT 5
                ");
                $stmt->execute([(int)$employeeId]);
                $corrections = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($corrections as $cor) {
                    $notifId = 'emp_correction_' . $cor['id'];
                    if ($filterRead && !empty($readIds[$notifId])) {
                        continue;
                    }
                    $notifications[] = [
                        'id' => $notifId,
                        'type' => 'danger',
                        'badge' => 'Corrección',
                        'icon' => 'bi-exclamation-triangle-fill text-danger',
                        'title' => htmlspecialchars($cor['title']),
                        'message' => 'El director solicitó ajustes en el entregable',
                        'link' => function_exists('url') ? url('/activities/my-tasks') : '/activities/my-tasks',
                        'action_text' => 'Ajustar',
                        'time' => date('d M, h:i A', strtotime($cor['updated_at'] ?? 'now'))
                    ];
                }

                // 2. Tareas nuevas asignadas (individuales)
                $stmt = $pdo->prepare("
                    SELECT id, title, priority, scheduled_date FROM activities
                    WHERE employee_id = ? AND current_status = 'NUEVA'
                    ORDER BY scheduled_date ASC
                    LIMIT 5
                ");
                $stmt->execute([(int)$employeeId]);
                $newTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($newTasks as $nt) {
                    $notifId = 'emp_new_' . $nt['id'];
                    if ($filterRead && !empty($readIds[$notifId])) {
                        continue;
                    }
                    $notifications[] = [
                        'id' => $notifId,
                        'type' => 'primary',
                        'badge' => 'Asignación',
                        'icon' => 'bi-play-circle-fill text-primary',
                        'title' => htmlspecialchars($nt['title']),
                        'message' => 'Prioridad: ' . htmlspecialchars($nt['priority']) . ' &bull; Entrega: ' . htmlspecialchars($nt['scheduled_date']),
                        'link' => function_exists('url') ? url('/activities/my-tasks') : '/activities/my-tasks',
                        'action_text' => 'Comenzar',
                        'time' => date('d M', strtotime($nt['scheduled_date'] ?? 'now'))
                    ];
                }

                // 3. Notificaciones de contratos generados por el empleado
                if (\Config\Auth::canManageContracts()) {
                    $stmt = $pdo->prepare("
                        SELECT id, contract_code, updated_at FROM contracts
                        WHERE generated_by_employee_id = ? AND status = 'RECHAZADO'
                        ORDER BY updated_at DESC
                        LIMIT 5
                    ");
                    $stmt->execute([(int)$employeeId]);
                    $rejectedContracts = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($rejectedContracts as $rc) {
                        $notifId = 'emp_ctr_rej_' . $rc['id'];
                        if ($filterRead && !empty($readIds[$notifId])) {
                            continue;
                        }
                        $notifications[] = [
                            'id' => $notifId,
                            'type' => 'danger',
                            'badge' => 'Observaciones',
                            'icon' => 'bi-file-earmark-x-fill text-danger',
                            'title' => 'Borrador ' . htmlspecialchars($rc['contract_code']),
                            'message' => 'Devuelto por dirección para ajuste de condiciones',
                            'link' => function_exists('url') ? url('/contracts/create') : '/contracts/create',
                            'action_text' => 'Editar',
                            'time' => date('d M, h:i A', strtotime($rc['updated_at'] ?? 'now'))
                        ];
                    }
                }
            }
        }

        return $notifications;
    }
}
