<?php
/**
 * Controlador de Autenticación y Sesiones
 */

namespace App\Controllers;

use Config\Auth;

class AuthController extends Controller {
    public function showLogin(): void {
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }

        // Si la redirección fue ocasionada por inactividad, desplegar advertencia informativa
        if (isset($_GET['reason']) && $_GET['reason'] === 'inactivity' && empty($_SESSION['flash_warning']) && empty($_SESSION['flash_error'])) {
            $this->setFlash('warning', 'Su sesión ha sido cerrada automáticamente por inactividad para proteger la seguridad de su cuenta.');
        }

        $this->render('auth/login', [
            'pageTitle' => 'Iniciar Sesión | Miami Media Agency'
        ], 'none');
    }

    public function login(): void {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $this->setFlash('error', 'Por favor complete todos los campos de acceso.');
            $this->redirect('/login');
        }

        if (Auth::attempt($email, $password)) {
            $this->setFlash('success', 'Bienvenido al sistema de operaciones.');
            $this->redirect('/dashboard');
        } else {
            $this->setFlash('error', 'Credenciales inválidas o cuenta inactiva.');
            $this->redirect('/login');
        }
    }

    public function logout(): void {
        $reason = $_GET['reason'] ?? '';
        Auth::logout();
        if ($reason === 'inactivity') {
            $this->setFlash('warning', 'Su sesión ha sido cerrada automáticamente por inactividad para proteger la seguridad de su cuenta.');
            $this->redirect('/login?reason=inactivity');
        } else {
            $this->setFlash('info', 'Ha cerrado sesión correctamente.');
            $this->redirect('/login');
        }
    }
}
