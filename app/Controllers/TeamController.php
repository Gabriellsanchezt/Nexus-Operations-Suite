<?php
/**
 * Controlador de Gestión de Personal, Trabajadores, Roles y Especialidades
 * Exclusivo para Dirección General (Jefe Admin)
 */

namespace App\Controllers;

use Config\Auth;
use App\Models\UserModel;

class TeamController extends Controller {
    private UserModel $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    /**
     * Vista Principal: Tablero de Trabajadores, Roles del Sistema y Especialidades
     */
    public function index(): void {
        Auth::requireJefe();

        $activeTab = $_GET['tab'] ?? 'workers';
        $statusFilter = $_GET['status'] ?? null;

        $employees = $this->userModel->getAllEmployeesWithDetails();
        $roles = $this->userModel->getAllRoles();
        $specialties = $this->userModel->getAllSpecialties();
        $stats = $this->userModel->getTeamStats();

        // Filtrado opcional de trabajadores por estado (ACTIVO / SUSPENDIDO)
        if (!empty($statusFilter)) {
            $employees = array_values(array_filter($employees, function($e) use ($statusFilter) {
                return ($e['user_status'] === $statusFilter);
            }));
        }

        $this->render('team/index', [
            'pageTitle' => 'Gestión de Trabajadores, Roles y Especialidades',
            'employees' => $employees,
            'roles' => $roles,
            'specialties' => $specialties,
            'stats' => $stats,
            'activeTab' => $activeTab,
            'statusFilter' => $statusFilter
        ]);
    }

    /**
     * 1. CREAR NUEVO TRABAJADOR
     */
    public function store(): void {
        Auth::requireJefe();

        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $roleId = (int)($_POST['role_id'] ?? 2);
        $specialtyIds = $_POST['specialties'] ?? [];

        if (empty($fullName) || empty($email) || empty($password)) {
            $this->setFlash('error', 'Nombre, correo electrónico y contraseña son obligatorios.');
            $this->redirect('/team?tab=workers');
        }

        try {
            $this->userModel->createTeamMember([
                'full_name' => $fullName,
                'email' => $email,
                'phone' => $phone,
                'password' => $password,
                'role_id' => $roleId,
                'hire_date' => date('Y-m-d')
            ], $specialtyIds);

            $this->setFlash('success', "Trabajador '{$fullName}' registrado exitosamente con sus roles asignados.");
        } catch (\Exception $e) {
            $this->setFlash('error', 'Error al registrar trabajador: ' . $e->getMessage());
        }

        $this->redirect('/team?tab=workers');
    }

    /**
     * 2. MODIFICAR TRABAJADOR EXISTENTE
     */
    public function update(): void {
        Auth::requireJefe();

        $userId = (int)($_POST['user_id'] ?? 0);
        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $roleId = (int)($_POST['role_id'] ?? 2);
        $specialtyIds = $_POST['specialties'] ?? [];
        $hireDate = $_POST['hire_date'] ?? date('Y-m-d');

        if (!$userId || empty($fullName) || empty($email)) {
            $this->setFlash('error', 'Datos incompletos para actualizar al trabajador.');
            $this->redirect('/team?tab=workers');
        }

        try {
            $this->userModel->updateTeamMember($userId, [
                'full_name' => $fullName,
                'email' => $email,
                'phone' => $phone,
                'password' => $password,
                'role_id' => $roleId,
                'hire_date' => $hireDate
            ], $specialtyIds);

            $this->setFlash('success', "Ficha del trabajador '{$fullName}' modificada exitosamente.");
        } catch (\Exception $e) {
            $this->setFlash('error', 'Error al modificar trabajador: ' . $e->getMessage());
        }

        $this->redirect('/team?tab=workers');
    }

    /**
     * 3. SUSPENDER / REACTIVAR TRABAJADOR
     */
    public function toggleStatus(): void {
        Auth::requireJefe();

        $userId = (int)($_POST['user_id'] ?? 0);
        $currentUser = Auth::user();
        $adminId = (int)($currentUser['id'] ?? 0);

        if (!$userId) {
            $this->setFlash('error', 'Trabajador no especificado.');
            $this->redirect('/team?tab=workers');
        }

        try {
            $newStatus = $this->userModel->toggleUserStatus($userId, $adminId);
            $msg = ($newStatus === 'SUSPENDIDO') 
                ? 'El trabajador ha sido suspendido del sistema. No podrá acceder ni operar hasta su reactivación.' 
                : 'El trabajador ha sido reactivado y restablecido en el sistema.';
            $this->setFlash(($newStatus === 'SUSPENDIDO' ? 'warning' : 'success'), $msg);
        } catch (\Exception $e) {
            $this->setFlash('error', 'No se pudo alterar el estado: ' . $e->getMessage());
        }

        $this->redirect('/team?tab=workers');
    }

    /**
     * 4. CREAR ROL DEL SISTEMA
     */
    public function storeRole(): void {
        Auth::requireJefe();

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($name)) {
            $this->setFlash('error', 'El nombre del rol es obligatorio.');
            $this->redirect('/team?tab=roles');
        }

        try {
            $this->userModel->createRole($name, $description);
            $this->setFlash('success', "Nuevo rol '{$name}' creado en el sistema.");
        } catch (\Exception $e) {
            $this->setFlash('error', 'Error al crear rol: ' . $e->getMessage());
        }

        $this->redirect('/team?tab=roles');
    }

    /**
     * 5. MODIFICAR ROL DEL SISTEMA
     */
    public function updateRole(): void {
        Auth::requireJefe();

        $roleId = (int)($_POST['role_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (!$roleId || empty($name)) {
            $this->setFlash('error', 'Datos incompletos para actualizar el rol.');
            $this->redirect('/team?tab=roles');
        }

        try {
            $this->userModel->updateRole($roleId, $name, $description);
            $this->setFlash('success', "Rol modificado exitosamente.");
        } catch (\Exception $e) {
            $this->setFlash('error', 'Error al modificar rol: ' . $e->getMessage());
        }

        $this->redirect('/team?tab=roles');
    }

    /**
     * 6. SUSPENDER / REACTIVAR ROL
     */
    public function toggleRoleStatus(): void {
        Auth::requireJefe();

        $roleId = (int)($_POST['role_id'] ?? 0);

        if (!$roleId) {
            $this->setFlash('error', 'Rol no especificado.');
            $this->redirect('/team?tab=roles');
        }

        try {
            $newStatus = $this->userModel->toggleRoleStatus($roleId);
            $this->setFlash('info', "El estado del rol cambió a {$newStatus}.");
        } catch (\Exception $e) {
            $this->setFlash('error', 'Error: ' . $e->getMessage());
        }

        $this->redirect('/team?tab=roles');
    }

    /**
     * 7. CREAR ESPECIALIDAD / CARGO OPERATIVO
     */
    public function storeSpecialty(): void {
        Auth::requireJefe();

        $code = trim($_POST['code'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($code) || empty($name)) {
            $this->setFlash('error', 'El código y nombre de la especialidad son obligatorios.');
            $this->redirect('/team?tab=roles');
        }

        try {
            $this->userModel->createSpecialty($code, $name, $description);
            $this->setFlash('success', "Especialidad '{$name}' incorporada al catálogo operativo.");
        } catch (\Exception $e) {
            $this->setFlash('error', 'Error al crear especialidad: ' . $e->getMessage());
        }

        $this->redirect('/team?tab=roles');
    }

    /**
     * 8. MODIFICAR ESPECIALIDAD / CARGO OPERATIVO
     */
    public function updateSpecialty(): void {
        Auth::requireJefe();

        $specId = (int)($_POST['specialty_id'] ?? 0);
        $code = trim($_POST['code'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (!$specId || empty($code) || empty($name)) {
            $this->setFlash('error', 'Datos incompletos para actualizar la especialidad.');
            $this->redirect('/team?tab=roles');
        }

        try {
            $this->userModel->updateSpecialty($specId, $code, $name, $description);
            $this->setFlash('success', "Especialidad '{$name}' actualizada con éxito.");
        } catch (\Exception $e) {
            $this->setFlash('error', 'Error al modificar especialidad: ' . $e->getMessage());
        }

        $this->redirect('/team?tab=roles');
    }

    /**
     * 9. SUSPENDER / REACTIVAR ESPECIALIDAD
     */
    public function toggleSpecialtyStatus(): void {
        Auth::requireJefe();

        $specId = (int)($_POST['specialty_id'] ?? 0);

        if (!$specId) {
            $this->setFlash('error', 'Especialidad no especificada.');
            $this->redirect('/team?tab=roles');
        }

        try {
            $newStatus = $this->userModel->toggleSpecialtyStatus($specId);
            $this->setFlash('info', "El estado de la especialidad operativa cambió a {$newStatus}.");
        } catch (\Exception $e) {
            $this->setFlash('error', 'Error: ' . $e->getMessage());
        }

        $this->redirect('/team?tab=roles');
    }

    /**
     * 10. ACTUALIZAR RÁPIDO DE ESPECIALIDADES
     */
    public function updateSpecialties(): void {
        Auth::requireJefe();

        $userId = (int)($_POST['user_id'] ?? 0);
        $specialtyIds = $_POST['specialties'] ?? [];

        if (!$userId) {
            $this->setFlash('error', 'Usuario inválido.');
            $this->redirect('/team?tab=workers');
        }

        try {
            $this->userModel->updateUserSpecialties($userId, $specialtyIds);
            $this->setFlash('success', 'Especialidades operativas actualizadas correctamente.');
        } catch (\Exception $e) {
            $this->setFlash('error', 'Error al actualizar permisos: ' . $e->getMessage());
        }

        $this->redirect('/team?tab=workers');
    }
}
