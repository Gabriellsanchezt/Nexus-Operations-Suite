<?php
/**
 * Controlador de Actividades y Flujo Audiovisual
 * Conecta roles de producción (Editores, Diseñadores, Cámara, CM) con la supervisión del Jefe
 */

namespace App\Controllers;

use Config\Auth;
use App\Models\ActivityModel;
use App\Models\RealtorModel;
use App\Models\UserModel;
use App\Models\ContractModel;

class ActivityController extends Controller {
    private ActivityModel $activityModel;
    private RealtorModel $realtorModel;
    private UserModel $userModel;
    private ContractModel $contractModel;

    public function __construct() {
        $this->activityModel = new ActivityModel();
        $this->realtorModel = new RealtorModel();
        $this->userModel = new UserModel();
        $this->contractModel = new ContractModel();
    }

    /**
     * VISTA DEL JEFE: Tablero General de Producción Audiovisual
     */
    public function index(): void {
        Auth::requireJefe();

        $statusFilter = $_GET['status'] ?? null;
        $activities = $this->activityModel->getAll(['status' => $statusFilter]);
        $counts = $this->activityModel->getDashboardCounts();
        $realtors = $this->realtorModel->getAll();
        $employees = $this->userModel->getAllEmployeesWithDetails();
        $contracts = $this->contractModel->getFinalApprovedContracts();

        $pageTitle = ($statusFilter === 'APROBADA') 
            ? 'Producción Audiovisual - Actividades Culminadas & Aprobadas' 
            : 'Gestión y Supervisión de Producción Audiovisual';

        $this->render('activities/index', [
            'pageTitle' => $pageTitle,
            'activities' => $activities,
            'counts' => $counts,
            'realtors' => $realtors,
            'employees' => $employees,
            'contracts' => $contracts,
            'activeFilter' => $statusFilter
        ]);
    }

    /**
     * VISTA DEL EMPLEADO: Panel "Mis Tareas Audiovisuales"
     * Exclusivo para especialidades creativas
     */
    public function myTasks(): void {
        Auth::requireAuth();

        if (!Auth::canProduceMedia()) {
            $this->setFlash('warning', 'Este panel está destinado exclusivamente al personal creativo y audiovisual.');
            $this->redirect('/dashboard');
            return;
        }

        $user = Auth::user();
        $statusFilter = $_GET['status'] ?? null;
        $tasks = $this->activityModel->getByEmployee((int)$user['employee_id'], $statusFilter);
        $counts = $this->activityModel->getEmployeeCounts((int)$user['employee_id']);

        $pageTitle = ($statusFilter === 'APROBADA')
            ? 'Mis Tareas Audiovisuales Culminadas & Aprobadas'
            : 'Mis Tareas Audiovisuales Asignadas';

        $this->render('activities/my_tasks', [
            'pageTitle' => $pageTitle,
            'tasks' => $tasks,
            'user' => $user,
            'activeFilter' => $statusFilter,
            'counts' => $counts
        ]);
    }

    /**
     * VISTA DEL JEFE: Crear y Asignar Tarea
     */
    public function store(): void {
        Auth::requireJefe();

        $title = $_POST['title'] ?? '';
        $realtorId = (int)($_POST['realtor_id'] ?? 0);
        $employeeId = (int)($_POST['employee_id'] ?? 0);
        $activityType = $_POST['activity_type'] ?? 'EDICION_VIDEO';
        $priority = $_POST['priority'] ?? 'MEDIA';
        $scheduledDate = $_POST['scheduled_date'] ?? date('Y-m-d');
        $contractId = !empty($_POST['contract_id']) ? (int)$_POST['contract_id'] : null;
        $description = $_POST['description'] ?? '';

        if (empty($title) || !$realtorId || !$employeeId) {
            $this->setFlash('error', 'Por favor complete el título, cliente Realtor y empleado asignado.');
            $this->redirect('/activities');
        }

        $this->activityModel->create([
            'title' => $title,
            'description' => $description,
            'realtor_id' => $realtorId,
            'employee_id' => $employeeId,
            'contract_id' => $contractId,
            'activity_type' => $activityType,
            'priority' => $priority,
            'scheduled_date' => $scheduledDate
        ]);

        $this->setFlash('success', 'Nueva tarea creada y canalizada al empleado correspondiente.');
        $this->redirect('/activities');
    }

    /**
     * VISTA DEL EMPLEADO: Subir Entregable (URLs Frame.io, Drive, Dropbox)
     */
    public function submitDeliverable(): void {
        Auth::requireAuth();

        $activityId = (int)($_POST['activity_id'] ?? 0);
        $url = $_POST['deliverable_url'] ?? '';
        $type = $_POST['deliverable_type'] ?? 'FRAME_IO';
        $notes = $_POST['notes'] ?? '';

        if (!$activityId || empty($url)) {
            $this->setFlash('error', 'Debe proporcionar una URL válida de entregable.');
            $this->redirect('/activities/my-tasks');
        }

        $this->activityModel->addDeliverable($activityId, $url, $type, $notes);
        $this->setFlash('success', 'Entregable enviado con éxito. La tarea ha pasado al estado "ENTREGADA PARA REVISIÓN" del Jefe.');
        $this->redirect('/activities/my-tasks');
    }

    /**
     * Empleado cambia estado a "EN_PROCESO"
     */
    public function startTask(): void {
        Auth::requireAuth();

        $activityId = (int)($_POST['activity_id'] ?? 0);
        if ($activityId) {
            $this->activityModel->updateStatus($activityId, 'EN_PROCESO');
            $this->setFlash('info', 'Tarea iniciada y marcada como "En Proceso".');
        }
        $this->redirect('/activities/my-tasks');
    }

    /**
     * VISTA DEL JEFE: Revisar y Calificar Entregable
     */
    public function reviewDeliverable(): void {
        Auth::requireJefe();

        $deliverableId = (int)($_POST['deliverable_id'] ?? 0);
        $activityId = (int)($_POST['activity_id'] ?? 0);
        $status = $_POST['review_status'] ?? 'APROBADO'; // 'APROBADO' o 'REQUIERE_CAMBIOS'
        $notes = $_POST['review_notes'] ?? '';

        if (!$deliverableId || !$activityId) {
            $this->setFlash('error', 'Entregable no identificado.');
            $this->redirect('/activities');
        }

        $user = Auth::user();
        $this->activityModel->reviewDeliverable($deliverableId, $activityId, (int)$user['id'], $status, $notes);

        if ($status === 'APROBADO') {
            $this->setFlash('success', '¡Entregable aprobado con éxito! La tarea quedó formalmente culminada.');
        } else {
            $this->setFlash('warning', 'Se ha solicitado corrección al creativo con las notas correspondientes.');
        }

        $this->redirect('/activities');
    }

    /**
     * Endpoint AJAX para inspección rápida de entregables de una tarea
     */
    public function detailsAjax(): void {
        Auth::requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $activity = $this->activityModel->getById($id);
        if (!$activity) {
            $this->json(['success' => false, 'error' => 'Actividad no encontrada'], 404);
        }
        $this->json(['success' => true, 'activity' => $activity]);
    }

    /**
     * MODIFICAR (UPDATE): Actualizar datos de la actividad
     * El Jefe puede modificar cualquier tarea. Los empleados pueden actualizar notas/fechas de sus tareas no culminadas.
     */
    public function update(): void {
        Auth::requireAuth();

        $id = (int)($_POST['id'] ?? 0);
        $activity = $this->activityModel->getById($id);
        if (!$activity) {
            $this->setFlash('error', 'Actividad no encontrada.');
            $this->redirect('/activities');
        }

        $user = Auth::user();
        if (!Auth::isJefe()) {
            // Empleado solo puede modificar si está asignada a él y no está culminada
            if ((int)$activity['employee_id'] !== (int)($user['employee_id'] ?? 0)) {
                $this->setFlash('error', 'Acceso denegado: Solo el Jefe o el empleado asignado pueden modificar esta tarea.');
                $this->redirect('/activities/my-tasks');
                return;
            }
            if ($activity['current_status'] === 'APROBADA') {
                $this->setFlash('error', 'Las actividades aprobadas y culminadas no pueden ser modificadas.');
                $this->redirect('/activities/my-tasks');
            }
        }

        $title = trim($_POST['title'] ?? $activity['title']);
        $description = $_POST['description'] ?? $activity['description'];
        $realtorId = !empty($_POST['realtor_id']) ? (int)$_POST['realtor_id'] : (int)$activity['realtor_id'];
        $employeeId = !empty($_POST['employee_id']) ? (int)$_POST['employee_id'] : (int)$activity['employee_id'];
        $activityType = $_POST['activity_type'] ?? $activity['activity_type'];
        $priority = $_POST['priority'] ?? $activity['priority'];
        $scheduledDate = $_POST['scheduled_date'] ?? $activity['scheduled_date'];

        $this->activityModel->update($id, [
            'title' => $title,
            'description' => $description,
            'realtor_id' => $realtorId,
            'employee_id' => $employeeId,
            'activity_type' => $activityType,
            'priority' => $priority,
            'scheduled_date' => $scheduledDate
        ]);

        $this->setFlash('success', 'Actividad actualizada exitosamente.');
        if (Auth::isJefe()) {
            $this->redirect('/activities');
        } else {
            $this->redirect('/activities/my-tasks');
        }
    }

    /**
     * ELIMINAR (DELETE): Exclusivo para el Jefe
     * Los empleados no tienen permiso de eliminación bajo la metodología CRUD establecida.
     */
    public function delete(): void {
        Auth::requireJefe(); // ESTRICTAMENTE SOLO EL JEFE

        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            $this->setFlash('error', 'Identificador de actividad inválido.');
            $this->redirect('/activities');
        }

        try {
            $this->activityModel->delete($id);
            $this->setFlash('success', 'La actividad ha sido eliminada del sistema.');
        } catch (\Exception $e) {
            $this->setFlash('error', 'Error al eliminar actividad: ' . $e->getMessage());
        }

        $this->redirect('/activities');
    }
}
