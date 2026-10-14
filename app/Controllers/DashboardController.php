<?php
/**
 * Controlador del Dashboard Principal
 * Despacha vistas según rol (JEFE_ADMIN vs EMPLEADO)
 */

namespace App\Controllers;

use Config\Auth;
use App\Models\ActivityModel;
use App\Models\ContractModel;
use App\Models\RealtorModel;

class DashboardController extends Controller {
    public function index(): void {
        Auth::requireAuth();

        if (Auth::isJefe()) {
            $this->showJefeDashboard();
        } else {
            $this->showEmpleadoDashboard();
        }
    }

    private function showJefeDashboard(): void {
        $activityModel = new ActivityModel();
        $contractModel = new ContractModel();
        $realtorModel = new RealtorModel();

        $activityCounts = $activityModel->getDashboardCounts();
        $finalContracts = $contractModel->getAll();
        $allActivities = $activityModel->getAll();
        $recentActivities = array_slice($allActivities, 0, 5);
        $realtors = $realtorModel->getAll();

        $this->render('dashboard/jefe', [
            'pageTitle' => 'Panel Ejecutivo de Dirección (Jefe)',
            'counts' => $activityCounts,
            'finalContractsCount' => count($finalContracts),
            'allActivities' => $allActivities,
            'recentActivities' => $recentActivities,
            'realtorsCount' => count($realtors)
        ]);
    }

    private function showEmpleadoDashboard(): void {
        $user = Auth::user();
        $activityModel = new ActivityModel();

        $myTasks = [];
        if (!empty($user['employee_id'])) {
            $myTasks = $activityModel->getByEmployee((int)$user['employee_id']);
        }

        $this->render('dashboard/empleado', [
            'pageTitle' => 'Panel de Trabajo - ' . $user['full_name'],
            'user' => $user,
            'myTasks' => $myTasks
        ]);
    }

    /**
     * Endpoint AJAX para marcar notificaciones como vistas
     */
    public function markNotificationRead(): void {
        Auth::requireAuth();
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true) ?? [];

        if (!isset($_SESSION['read_notification_ids'])) {
            $_SESSION['read_notification_ids'] = [];
        }

        $id = $data['id'] ?? null;
        $all = !empty($data['all']);

        if ($all) {
            $user = Auth::user();
            $notifs = \App\Services\NotificationService::getUserNotifications($user, false);
            foreach ($notifs as $n) {
                if (!empty($n['id'])) {
                    $_SESSION['read_notification_ids'][$n['id']] = true;
                }
            }
        } elseif (!empty($id)) {
            $_SESSION['read_notification_ids'][$id] = true;
        }

        // Devolver cantidad restante de notificaciones no leídas
        $user = Auth::user();
        $remaining = \App\Services\NotificationService::getUserNotifications($user, true);

        $this->json([
            'success' => true,
            'remaining_count' => count($remaining)
        ]);
    }
}

