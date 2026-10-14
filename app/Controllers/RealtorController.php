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

        $company = trim($_POST['company_name'] ?? '');
        $contact = trim($_POST['contact_person'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $zone = trim($_POST['miami_zone'] ?? '');
        $rawCreds = trim($_POST['credentials_url'] ?? '');
        $rawDropbox = trim($_POST['dropbox_url'] ?? '');
        $instagram = trim($_POST['instagram'] ?? '');
        $tiktok = trim($_POST['tiktok'] ?? '');

        if (empty($company) || empty($contact) || empty($email) || empty($phone)) {
            $this->setFlash('error', 'Todos los campos principales del Realtor son requeridos.');
            $this->redirect('/realtors');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setFlash('error', 'El formato del correo electrónico ingresado para el Realtor no es válido.');
            $this->redirect('/realtors');
        }

        $credentialsUrl = $this->sanitizeUrl($rawCreds);
        if (!empty($rawCreds) && empty($credentialsUrl)) {
            $this->setFlash('error', 'El enlace ingresado para el Baúl de Credenciales no tiene un formato de URL válido.');
            $this->redirect('/realtors');
        }

        $dropboxUrl = $this->sanitizeUrl($rawDropbox);
        if (!empty($rawDropbox) && empty($dropboxUrl)) {
            $this->setFlash('error', 'El enlace ingresado para el repositorio de Dropbox no tiene un formato de URL válido.');
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

        $company = trim($_POST['company_name'] ?? '');
        $contact = trim($_POST['contact_person'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $zone = trim($_POST['miami_zone'] ?? '');
        $rawCreds = trim($_POST['credentials_url'] ?? '');
        $rawDropbox = trim($_POST['dropbox_url'] ?? '');

        if (empty($company) || empty($contact) || empty($email) || empty($phone)) {
            $this->setFlash('error', 'Todos los campos principales del Realtor son requeridos.');
            $this->redirect('/realtors');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setFlash('error', 'El formato del correo electrónico ingresado para el Realtor no es válido.');
            $this->redirect('/realtors');
        }

        $credentialsUrl = $this->sanitizeUrl($rawCreds);
        if (!empty($rawCreds) && empty($credentialsUrl)) {
            $this->setFlash('error', 'El enlace ingresado para el Baúl de Credenciales no tiene un formato de URL válido.');
            $this->redirect('/realtors');
        }

        $dropboxUrl = $this->sanitizeUrl($rawDropbox);
        if (!empty($rawDropbox) && empty($dropboxUrl)) {
            $this->setFlash('error', 'El enlace ingresado para el repositorio de Dropbox no tiene un formato de URL válido.');
            $this->redirect('/realtors');
        }

        $this->realtorModel->update($id, [
            'company_name' => $company,
            'contact_person' => $contact,
            'phone' => $phone,
            'email' => $email,
            'miami_zone' => $zone,
            'credentials_url' => $credentialsUrl,
            'dropbox_url' => $dropboxUrl,
            'social_handles' => [
                'instagram' => trim($_POST['instagram'] ?? ''),
                'tiktok' => trim($_POST['tiktok'] ?? '')
            ],
            'status' => $_POST['status'] ?? 'ACTIVO'
        ]);

        $this->setFlash('success', 'Datos del Realtor actualizados correctamente.');
        $this->redirect('/realtors');
    }

    /**
     * Sanitizar y normalizar URL garantizando esquema https://
     */
    private function sanitizeUrl(string $url): string {
        $url = trim($url);
        if ($url === '') return '';
        if (!preg_match('~^https?://~i', $url)) {
            $url = 'https://' . $url;
        }
        return filter_var($url, FILTER_VALIDATE_URL) ? $url : '';
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
