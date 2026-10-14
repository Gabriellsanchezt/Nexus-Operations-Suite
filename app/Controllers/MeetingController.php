<?php
/**
 * Controlador de Agenda de Reuniones
 * Sincronización directa con Google Calendar y notificaciones móviles
 */

namespace App\Controllers;

use Config\Auth;
use App\Models\MeetingModel;
use App\Models\RealtorModel;

class MeetingController extends Controller {
    private MeetingModel $meetingModel;
    private RealtorModel $realtorModel;

    public function __construct() {
        $this->meetingModel = new MeetingModel();
        $this->realtorModel = new RealtorModel();
    }

    public function index(): void {
        Auth::requireAuth();

        $meetings = $this->meetingModel->getAll();
        $realtors = $this->realtorModel->getAll();

        // Enriquecer cada reunión con su link oficial de Google Calendar
        foreach ($meetings as &$m) {
            $m['google_calendar_url'] = MeetingModel::buildGoogleCalendarUrl($m);
        }

        $this->render('meetings/index', [
            'pageTitle' => 'Agenda de Reuniones & Sincronización Google Calendar',
            'meetings' => $meetings,
            'realtors' => $realtors
        ]);
    }

    public function store(): void {
        Auth::requireAuth();

        $title = trim($_POST['title'] ?? '');
        $realtorId = !empty($_POST['realtor_id']) ? (int)$_POST['realtor_id'] : null;
        $date = $_POST['meeting_date'] ?? date('Y-m-d');
        $time = $_POST['meeting_time'] ?? '10:00';
        $duration = (int)($_POST['duration_minutes'] ?? 30);
        $link = trim($_POST['meeting_link'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($title) || empty($date) || empty($time)) {
            $this->setFlash('error', 'El título, fecha y hora de la reunión son obligatorios.');
            $this->redirect('/agenda');
        }

        try {
            $meetingId = $this->meetingModel->create([
                'title' => $title,
                'realtor_id' => $realtorId,
                'meeting_date' => $date,
                'meeting_time' => $time,
                'duration_minutes' => $duration,
                'meeting_link' => $link,
                'description' => $description
            ]);

            // Generar enlace temporal para feedback
            $this->setFlash('success', "Reunión agendada exitosamente. Haz clic en 'Añadir a Google Calendar' para sincronizarla con tu teléfono y recibir recordatorios.");
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
            http_response_code(404);
            die("Reunión no encontrada");
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
