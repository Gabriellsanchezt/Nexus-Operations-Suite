<?php
/**
 * Controlador de Calendario y Cronograma de Email Marketing
 * Organización visual en pantalla amplia y filtros por Realtor
 */

namespace App\Controllers;

use Config\Auth;
use App\Models\EmailMarketingModel;
use App\Models\RealtorModel;

class EmailMarketingController extends Controller {
    private EmailMarketingModel $emailModel;
    private RealtorModel $realtorModel;

    public function __construct() {
        $this->emailModel = new EmailMarketingModel();
        $this->realtorModel = new RealtorModel();
    }

    public function index(): void {
        Auth::requireAuth();

        $selectedRealtorId = !empty($_GET['realtor_id']) ? (int)$_GET['realtor_id'] : null;
        $campaigns = $this->emailModel->getAll($selectedRealtorId);
        $realtors = $this->realtorModel->getAll();
        $counts = $this->emailModel->getCounts();

        $this->render('email_marketing/index', [
            'pageTitle' => 'Cronograma & Calendario de Email Marketing',
            'campaigns' => $campaigns,
            'realtors' => $realtors,
            'counts' => $counts,
            'selectedRealtorId' => $selectedRealtorId
        ]);
    }

    public function store(): void {
        Auth::requireAuth();

        $realtorId = (int)($_POST['realtor_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $scheduledDate = $_POST['scheduled_date'] ?? date('Y-m-d');
        $status = $_POST['status'] ?? 'PROGRAMADO';
        $notes = trim($_POST['notes'] ?? '');

        if (!$realtorId || empty($title)) {
            $this->setFlash('error', 'El Realtor y el título de la campaña son obligatorios.');
            $this->redirect('/email-marketing');
        }

        try {
            $this->emailModel->create([
                'realtor_id' => $realtorId,
                'title' => $title,
                'scheduled_date' => $scheduledDate,
                'status' => $status,
                'notes' => $notes
            ]);
            $this->setFlash('success', 'Fecha de Email Marketing programada correctamente.');
        } catch (\Exception $e) {
            $this->setFlash('error', 'Error al programar campaña: ' . $e->getMessage());
        }

        $redirectUrl = $realtorId ? '/email-marketing?realtor_id=' . $realtorId : '/email-marketing';
        $this->redirect($redirectUrl);
    }

    public function updateStatus(): void {
        Auth::requireAuth();

        $id = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? 'PROGRAMADO';
        $realtorId = (int)($_POST['realtor_id'] ?? 0);

        if ($id && in_array($status, ['PROGRAMADO', 'SUBIDO', 'ENVIADO'], true)) {
            $this->emailModel->updateStatus($id, $status);
            $this->setFlash('success', 'Estado de la campaña actualizado.');
        }

        $redirectUrl = $realtorId ? '/email-marketing?realtor_id=' . $realtorId : '/email-marketing';
        $this->redirect($redirectUrl);
    }

    public function delete(): void {
        Auth::requireAuth();

        $id = (int)($_POST['id'] ?? 0);
        $realtorId = (int)($_POST['realtor_id'] ?? 0);

        if ($id) {
            $this->emailModel->delete($id);
            $this->setFlash('success', 'Campaña eliminada del cronograma.');
        }

        $redirectUrl = $realtorId ? '/email-marketing?realtor_id=' . $realtorId : '/email-marketing';
        $this->redirect($redirectUrl);
    }
}
