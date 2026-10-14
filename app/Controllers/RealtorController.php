<?php
/**
 * Controlador de Clientes Realtors en Miami
 */

namespace App\Controllers;

use Config\Auth;
use App\Models\RealtorModel;

class RealtorController extends Controller {
    private RealtorModel $realtorModel;

    public function __construct() {
        $this->realtorModel = new RealtorModel();
    }

    public function index(): void {
        Auth::requireAuth(); // Jefe y Empleados pueden consultar directorio

        $realtors = $this->realtorModel->getAll();

        $this->render('realtors/index', [
            'pageTitle' => 'Gestión de Clientes Realtors (Miami)',
            'realtors' => $realtors,
            'zones' => MIAMI_ZONES
        ]);
    }

    public function store(): void {
        Auth::requireAuth(); // Todos los perfiles pueden registrar clientes

        $company = $_POST['company_name'] ?? '';
        $contact = $_POST['contact_person'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $email = $_POST['email'] ?? '';
        $zone = $_POST['miami_zone'] ?? '';
        $credentialsUrl = $_POST['credentials_url'] ?? '';
        $dropboxUrl = $_POST['dropbox_url'] ?? '';
        $instagram = $_POST['instagram'] ?? '';
        $tiktok = $_POST['tiktok'] ?? '';

        if (empty($company) || empty($contact) || empty($email) || empty($phone)) {
            $this->setFlash('error', 'Todos los campos principales del Realtor son requeridos.');
            $this->redirect('/realtors');
        }

        try {
            $this->realtorModel->create([
                'company_name' => $company,
                'contact_person' => $contact,
                'phone' => $phone,
                'email' => $email,
                'miami_zone' => $zone,
                'credentials_url' => $credentialsUrl,
                'dropbox_url' => $dropboxUrl,
                'social_handles' => [
                    'instagram' => $instagram,
                    'tiktok' => $tiktok
                ],
                'status' => 'ACTIVO'
            ]);
            $this->setFlash('success', 'Cliente Realtor registrado exitosamente.');
        } catch (\Exception $e) {
            $this->setFlash('error', 'Error al registrar: ' . $e->getMessage());
        }

        $this->redirect('/realtors');
    }

    public function update(): void {
        Auth::requireAuth();

        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            $this->setFlash('error', 'Realtor no válido.');
            $this->redirect('/realtors');
        }

        $this->realtorModel->update($id, [
            'company_name' => $_POST['company_name'],
            'contact_person' => $_POST['contact_person'],
            'phone' => $_POST['phone'],
            'email' => $_POST['email'],
            'miami_zone' => $_POST['miami_zone'],
            'credentials_url' => $_POST['credentials_url'] ?? '',
            'dropbox_url' => $_POST['dropbox_url'] ?? '',
            'social_handles' => [
                'instagram' => $_POST['instagram'] ?? '',
                'tiktok' => $_POST['tiktok'] ?? ''
            ],
            'status' => $_POST['status'] ?? 'ACTIVO'
        ]);

        $this->setFlash('success', 'Datos del Realtor actualizados correctamente.');
        $this->redirect('/realtors');
    }

    /**
     * Endpoint API para consultar el expediente y proyectos del Realtor (Botón Ver)
     */
    public function projects(): void {
        Auth::requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $realtor = $this->realtorModel->getById($id);
        if (!$realtor) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Realtor no encontrado']);
            exit;
        }

        $projects = $this->realtorModel->getProjects($id);
        header('Content-Type: application/json');
        echo json_encode([
            'realtor' => $realtor,
            'projects' => $projects
        ]);
        exit;
    }

    public function delete(): void {
        Auth::requireJefe();

        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            try {
                $this->realtorModel->delete($id);
                $this->setFlash('success', 'Realtor eliminado del sistema.');
            } catch (\Exception $e) {
                $this->setFlash('error', 'No se puede eliminar el Realtor porque tiene contratos o tareas vinculadas.');
            }
        }
        $this->redirect('/realtors');
    }
}
