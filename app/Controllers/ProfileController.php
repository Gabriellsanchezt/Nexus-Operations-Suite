<?php
/**
 * Controlador de Perfil de Usuario & Seguridad
 * Permite editar datos personales (nombre, teléfono, correo) y cambiar la clave si es necesario.
 */

namespace App\Controllers;

use Config\Auth;
use App\Models\UserModel;

class ProfileController extends Controller {

    /**
     * Mostrar vista de edición de perfil
     */
    public function showEdit(): void {
        Auth::requireAuth();

        $currentUser = Auth::user();
        $userModel = new UserModel();
        $profile = $userModel->getUserProfile((int)$currentUser['id']);

        if (!$profile) {
            $this->setFlash('error', 'No se pudo cargar el perfil del usuario.');
            $this->redirect('/dashboard');
            return;
        }

        $this->render('profile/edit', [
            'pageTitle' => 'Editar Perfil & Seguridad de Cuenta',
            'profile' => $profile,
            'currentUser' => $currentUser
        ]);
    }

    /**
     * Procesar actualización de datos del perfil y cambio de clave
     */
    public function update(): void {
        Auth::requireAuth();

        $currentUser = Auth::user();
        $userId = (int)$currentUser['id'];
        $userModel = new UserModel();

        $fullName = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validaciones básicas de campos de contacto
        if ($fullName === '') {
            $this->setFlash('error', 'El nombre completo es requerido.');
            $this->redirect('/profile');
            return;
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setFlash('error', 'Debes ingresar un correo electrónico corporativo válido.');
            $this->redirect('/profile');
            return;
        }

        // Lógica de cambio de contraseña ("si es necesario")
        $isChangingPassword = !empty($newPassword);

        if ($isChangingPassword) {
            if (empty($currentPassword)) {
                $this->setFlash('error', 'Debes ingresar tu contraseña actual para autorizar el cambio de clave.');
                $this->redirect('/profile');
                return;
            }

            if (!$userModel->verifyPassword($userId, $currentPassword)) {
                $this->setFlash('error', 'La contraseña actual ingresada es incorrecta.');
                $this->redirect('/profile');
                return;
            }

            if (strlen($newPassword) < 6) {
                $this->setFlash('error', 'La nueva contraseña debe tener al menos 6 caracteres.');
                $this->redirect('/profile');
                return;
            }

            if ($newPassword !== $confirmPassword) {
                $this->setFlash('error', 'La confirmación de la nueva contraseña no coincide.');
                $this->redirect('/profile');
                return;
            }
        }

        try {
            $userModel->updateProfile(
                $userId,
                $fullName,
                $phone,
                $email,
                $isChangingPassword ? $newPassword : null
            );

            // Actualizar datos en la sesión activa inmediatamente
            $_SESSION['email'] = $email;
            $_SESSION['full_name'] = $fullName;

            $successMsg = '¡Tus datos de perfil han sido actualizados correctamente!';
            if ($isChangingPassword) {
                $successMsg .= ' Tu contraseña ha sido cambiada de forma segura.';
            }

            $this->setFlash('success', $successMsg);
        } catch (\InvalidArgumentException $e) {
            $this->setFlash('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->setFlash('error', 'Error al actualizar el perfil: ' . $e->getMessage());
        }

        $this->redirect('/profile');
    }
}
