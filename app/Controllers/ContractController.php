<?php
/**
 * Controlador de Almacén de Contratos Finales (PDFs Concluidos)
 * Solo se almacenan contratos terminados asociados a un Realtor.
 * La fecha de subida es automática y calculada por el sistema.
 */

namespace App\Controllers;

use Config\Auth;
use App\Models\ContractModel;
use App\Models\RealtorModel;

class ContractController extends Controller {
    private ContractModel $contractModel;
    private RealtorModel $realtorModel;

    public function __construct() {
        $this->contractModel = new ContractModel();
        $this->realtorModel = new RealtorModel();
    }

    /**
     * VISTA PRINCIPAL: Almacén de Contratos Finales Concluidos
     */
    public function finalContracts(): void {
        Auth::requireAuth();

        $selectedRealtorId = !empty($_GET['realtor_id']) ? (int)$_GET['realtor_id'] : null;
        $contracts = $this->contractModel->getAll($selectedRealtorId);
        $realtors = $this->realtorModel->getAll();
        $stats = $this->contractModel->getStats();

        $this->render('contracts/final_repository', [
            'pageTitle' => 'Almacén de Contratos Finales (PDFs Concluidos)',
            'contracts' => $contracts,
            'realtors' => $realtors,
            'stats' => $stats,
            'selectedRealtorId' => $selectedRealtorId
        ]);
    }

    /**
     * SUBIR CONTRATO CONCLUIDO (PDF)
     */
    public function store(): void {
        Auth::requireAuth();

        $realtorId = (int)($_POST['realtor_id'] ?? 0);
        $title = trim($_POST['contract_title'] ?? '');
        $notes = trim($_POST['notes'] ?? '');

        if (!$realtorId || empty($title)) {
            $this->setFlash('error', 'El Realtor y el título del contrato son obligatorios.');
            $this->redirect('/contracts/final');
        }

        if (empty($_FILES['contract_pdf']) || $_FILES['contract_pdf']['error'] !== UPLOAD_ERR_OK) {
            $this->setFlash('error', 'Debe adjuntar un archivo PDF válido del contrato.');
            $this->redirect('/contracts/final');
        }

        $file = $_FILES['contract_pdf'];
        
        // 1. Validar límite de tamaño (Máximo 15 MB)
        if ($file['size'] > 15 * 1024 * 1024) {
            $this->setFlash('error', 'El archivo PDF excede el límite máximo de tamaño permitido (15 MB).');
            $this->redirect('/contracts/final');
        }

        // 2. Validar extensión de archivo
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            $this->setFlash('error', 'Únicamente se permiten archivos en formato PDF (.pdf).');
            $this->redirect('/contracts/final');
        }

        // 3. Validar firma binaria de documento PDF (%PDF-) para prevenir archivos maliciosos
        $handle = @fopen($file['tmp_name'], 'rb');
        $headerBytes = $handle ? fread($handle, 5) : '';
        if ($handle) {
            fclose($handle);
        }
        if ($headerBytes !== '%PDF-') {
            $this->setFlash('error', 'El archivo suministrado no corresponde a un documento PDF auténtico.');
            $this->redirect('/contracts/final');
        }

        // Crear directorio de almacenamiento si no existe
        $uploadDir = BASE_PATH . '/storage/contracts';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
        }

        // Nombre seguro y único para el archivo
        $safeName = 'contract_r' . $realtorId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.pdf';
        $destPath = $uploadDir . '/' . $safeName;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            $this->setFlash('error', 'No se pudo guardar el archivo PDF en el servidor.');
            $this->redirect('/contracts/final');
        }

        try {
            $userId = Auth::id();
            $this->contractModel->uploadContract($realtorId, $title, $safeName, $notes, $userId);
            $this->setFlash('success', 'Contrato final (PDF) subido y almacenado exitosamente.');
        } catch (\Exception $e) {
            @unlink($destPath);
            $this->setFlash('error', 'Error en base de datos al registrar contrato: ' . $e->getMessage());
        }

        $this->redirect('/contracts/final');
    }

    /**
     * VISUALIZAR O DESCARGAR PDF DE FORMA SEGURA
     */
    public function viewPdf(): void {
        Auth::requireAuth();

        $id = (int)($_GET['id'] ?? 0);
        $contract = $this->contractModel->getById($id);

        if (!$contract) {
            $this->setFlash('error', 'El contrato solicitado no existe en el repositorio.');
            $this->redirect('/contracts/final');
        }

        $filePath = BASE_PATH . '/storage/contracts/' . $contract['pdf_filename'];

        if (!file_exists($filePath)) {
            $this->setFlash('error', 'El archivo físico del contrato no se encuentra en el servidor de almacenamiento.');
            $this->redirect('/contracts/final');
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($contract['contract_title'] . '.pdf') . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');

        readfile($filePath);
        exit;
    }

    /**
     * ELIMINAR CONTRATO (Solo Dirección / Jefe)
     */
    public function delete(): void {
        Auth::requireJefe();

        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $this->contractModel->delete($id);
            $this->setFlash('success', 'Contrato final eliminado correctamente del repositorio.');
        }
        $this->redirect('/contracts/final');
    }

    /**
     * Redirecciones de rutas legacy hacia /contracts/final
     */
    public function approvals(): void {
        $this->redirect('/contracts/final');
    }

    public function create(): void {
        $this->redirect('/contracts/final');
    }

    public function activities(): void {
        $this->redirect('/activities');
    }
}
