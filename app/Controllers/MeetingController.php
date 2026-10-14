<?php
/**
 * Controlador de Agenda de Reuniones
 * Sincronización directa con Google Calendar y notificaciones móviles
 */

namespace App\Controllers;

use Config\Auth;
use App\Models\MeetingModel;
use App\Models\RealtorModel;
use App\Models\UserModel;

class MeetingController extends Controller {
    private MeetingModel $meetingModel;
    private RealtorModel $realtorModel;
    private UserModel $userModel;

    public function __construct() {
        $this->meetingModel = new MeetingModel();
        $this->realtorModel = new RealtorModel();
        $this->userModel = new UserModel();
    }

    public function index(): void {
        Auth::requireAuth();

        $meetings = $this->meetingModel->getAll();
        $realtors = $this->realtorModel->getAll();
        $employees = $this->userModel->getAllEmployeesWithDetails();

        // Enriquecer cada reunión con su link oficial de Google Calendar con los asistentes incluidos
        foreach ($meetings as &$m) {
            $m['google_calendar_url'] = MeetingModel::buildGoogleCalendarUrl($m);
        }

        $this->render('meetings/index', [
            'pageTitle' => 'Agenda de Reuniones & Sincronización Google Calendar',
            'meetings' => $meetings,
            'realtors' => $realtors,
            'employees' => $employees
        ]);
    }

    public function store(): void {
        Auth::requireAuth();

        $title = trim($_POST['title'] ?? '');
        $realtorId = !empty($_POST['realtor_id']) ? (int)$_POST['realtor_id'] : null;
        $employeeId = !empty($_POST['employee_id']) ? (int)$_POST['employee_id'] : null;
        $date = $_POST['meeting_date'] ?? date('Y-m-d');
        $time = $_POST['meeting_time'] ?? '10:00';
        $description = trim($_POST['description'] ?? '');

        if (empty($title) || empty($date) || empty($time)) {
            $this->setFlash('error', 'El título, la fecha y la hora de inicio de la reunión son obligatorios.');
            $this->redirect('/agenda');
        }

        try {
            $this->meetingModel->create([
                'title' => $title,
                'realtor_id' => $realtorId,
                'employee_id' => $employeeId,
                'meeting_date' => $date,
                'meeting_time' => $time,
                'duration_minutes' => 30, // Cálculo automático de bloque de calendario
                'description' => $description
            ]);

            $this->setFlash('success', "Reunión agendada exitosamente. Se ha vinculado al correo del trabajador asignado para la sincronización con Google Calendar y aviso al teléfono móvil.");
        } catch (\Exception $e) {
            $this->setFlash('error', 'Error al agendar reunión: ' . $e->getMessage());
        }

        $this->redirect('/agenda');
    }

    public function downloadIcs(): void {
        Auth::requireAuth();

        $id = (int)($_GET['id'] ?? 0);
        $meetings = $this->meetingModel->getAll();
        $meeting = null;

        foreach ($meetings as $m) {
            if ((int)$m['id'] === $id) {
                $meeting = $m;
                break;
            }
        }

        if (!$meeting) {
            $this->setFlash('error', 'La reunión solicitada no fue encontrada en la agenda.');
            $this->redirect('/agenda');
            return;
        }

        $ics = MeetingModel::generateIcs($meeting);
        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="reunion-' . $id . '.ics"');
        echo $ics;
        exit;
    }

    public function delete(): void {
        Auth::requireAuth();

        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $this->meetingModel->delete($id);
            $this->setFlash('success', 'Reunión eliminada de la agenda.');
        }

        $this->redirect('/agenda');
    }
}
