<?php
/**
 * Modelo de Usuarios, Roles, Especialidades y Empleados
 * Arquitectura 3NF estricta
 */

namespace App\Models;

use PDO;

class UserModel extends Model {
    /**
     * Obtener listado de todo el equipo de trabajo con sus roles y especialidades
     */
    public function getAllEmployeesWithDetails(): array {
        $sql = "
            SELECT e.id AS employee_id, e.user_id, e.full_name, e.phone, e.hire_date,
                   u.email, u.status AS user_status, r.name AS role_name, r.id AS role_id
            FROM employees e
            JOIN users u ON e.user_id = u.id
            JOIN roles r ON u.role_id = r.id
            ORDER BY e.id ASC
        ";
        $employees = $this->db->query($sql)->fetchAll();

        // Cargar especialidades para cada empleado
        foreach ($employees as &$emp) {
            $stmt = $this->db->prepare("
                SELECT s.id, s.code, s.name
                FROM user_specialties us
                JOIN specialties s ON us.specialty_id = s.id
                WHERE us.user_id = ?
            ");
            $stmt->execute([$emp['user_id']]);
            $emp['specialties'] = $stmt->fetchAll();
        }

        return $employees;
    }

    /**
     * Obtener empleados filtrados por especialidad
     */
    public function getEmployeesBySpecialty(string $specialtyCode): array {
        $stmt = $this->db->prepare("
            SELECT e.id, e.full_name, e.phone, u.email, s.code AS specialty_code
            FROM employees e
            JOIN users u ON e.user_id = u.id
            JOIN user_specialties us ON us.user_id = u.id
            JOIN specialties s ON us.specialty_id = s.id
            WHERE s.code = ? AND u.status = 'ACTIVO'
            ORDER BY e.full_name ASC
        ");
        $stmt->execute([$specialtyCode]);
        return $stmt->fetchAll();
    }

    /**
     * Obtener todos los roles disponibles
     */
    public function getAllRoles(): array {
        return $this->db->query("SELECT * FROM roles ORDER BY id ASC")->fetchAll();
    }

    /**
     * Obtener todas las especialidades disponibles
     */
    public function getAllSpecialties(): array {
        return $this->db->query("SELECT * FROM specialties ORDER BY id ASC")->fetchAll();
    }

    /**
     * Crear un nuevo miembro del equipo (Transacción atómica: user + employee + specialties)
     */
    public function createTeamMember(array $data, array $specialtyIds): int {
        $this->beginTransaction();
        try {
            // 1. Crear usuario
            $passHash = password_hash($data['password'], PASSWORD_BCRYPT);
            $stmtUser = $this->db->prepare("
                INSERT INTO users (email, password_hash, role_id, status)
                VALUES (?, ?, ?, 'ACTIVO')
            ");
            $stmtUser->execute([
                trim($data['email']),
                $passHash,
                (int)$data['role_id']
            ]);
            $userId = (int)$this->db->lastInsertId();

            // 2. Crear ficha laboral en employees
            $stmtEmp = $this->db->prepare("
                INSERT INTO employees (user_id, full_name, phone, hire_date)
                VALUES (?, ?, ?, ?)
            ");
            $stmtEmp->execute([
                $userId,
                trim($data['full_name']),
                trim($data['phone'] ?? ''),
                $data['hire_date'] ?: date('Y-m-d')
            ]);
            $employeeId = (int)$this->db->lastInsertId();

            // 3. Asociar especialidades (M:N)
            if (!empty($specialtyIds)) {
                $stmtSpec = $this->db->prepare("
                    INSERT INTO user_specialties (user_id, specialty_id)
                    VALUES (?, ?)
                ");
                foreach ($specialtyIds as $specId) {
                    $stmtSpec->execute([$userId, (int)$specId]);
                }
            }

            $this->commit();
            return $employeeId;
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }

    /**
     * Actualizar especialidades asignadas a un usuario
     */
    public function updateUserSpecialties(int $userId, array $specialtyIds): void {
        $this->beginTransaction();
        try {
            $delStmt = $this->db->prepare("DELETE FROM user_specialties WHERE user_id = ?");
            $delStmt->execute([$userId]);

            if (!empty($specialtyIds)) {
                $insStmt = $this->db->prepare("INSERT INTO user_specialties (user_id, specialty_id) VALUES (?, ?)");
                foreach ($specialtyIds as $specId) {
                    $insStmt->execute([$userId, (int)$specId]);
                }
            }
            $this->commit();
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }

    /**
     * Modificar datos de un trabajador (ficha laboral, credenciales, rol y especialidades)
     */
    public function updateTeamMember(int $userId, array $data, array $specialtyIds): bool {
        // Validar unicidad de correo
        $stmtEmail = $this->db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmtEmail->execute([trim($data['email']), $userId]);
        if ($stmtEmail->fetch()) {
            throw new \InvalidArgumentException("El correo '" . htmlspecialchars($data['email']) . "' ya pertenece a otro usuario.");
        }

        $this->beginTransaction();
        try {
            // 1. Actualizar users (email, role_id y contraseña opcional)
            if (!empty($data['password'])) {
                $passHash = password_hash($data['password'], PASSWORD_BCRYPT);
                $stmtUser = $this->db->prepare("
                    UPDATE users
                    SET email = ?, password_hash = ?, role_id = ?
                    WHERE id = ?
                ");
                $stmtUser->execute([
                    trim($data['email']),
                    $passHash,
                    (int)$data['role_id'],
                    $userId
                ]);
            } else {
                $stmtUser = $this->db->prepare("
                    UPDATE users
                    SET email = ?, role_id = ?
                    WHERE id = ?
                ");
                $stmtUser->execute([
                    trim($data['email']),
                    (int)$data['role_id'],
                    $userId
                ]);
            }

            // 2. Actualizar employees
            $stmtEmp = $this->db->prepare("
                UPDATE employees
                SET full_name = ?, phone = ?, hire_date = ?
                WHERE user_id = ?
            ");
            $stmtEmp->execute([
                trim($data['full_name']),
                trim($data['phone'] ?? ''),
                $data['hire_date'] ?: date('Y-m-d'),
                $userId
            ]);

            // 3. Actualizar especialidades
            $delStmt = $this->db->prepare("DELETE FROM user_specialties WHERE user_id = ?");
            $delStmt->execute([$userId]);

            if (!empty($specialtyIds)) {
                $insStmt = $this->db->prepare("INSERT INTO user_specialties (user_id, specialty_id) VALUES (?, ?)");
                foreach ($specialtyIds as $specId) {
                    $insStmt->execute([$userId, (int)$specId]);
                }
            }

            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }

    /**
     * Suspender o Reactivar a un trabajador (Cambio de estado ACTIVO / SUSPENDIDO)
     */
    public function toggleUserStatus(int $userId, int $currentAdminUserId): string {
        if ($userId === $currentAdminUserId) {
            throw new \InvalidArgumentException("Por seguridad no puedes suspender tu propia cuenta de Administrador.");
        }

        $stmt = $this->db->prepare("SELECT status, role_id FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        if (!$user) {
            throw new \InvalidArgumentException("Usuario no encontrado.");
        }

        $newStatus = (($user['status'] ?? 'ACTIVO') === 'ACTIVO') ? 'SUSPENDIDO' : 'ACTIVO';
        $upStmt = $this->db->prepare("UPDATE users SET status = ? WHERE id = ?");
        $upStmt->execute([$newStatus, $userId]);

        return $newStatus;
    }

    /**
     * Crear un nuevo Rol del Sistema
     */
    public function createRole(string $name, string $description): int {
        $name = strtoupper(trim($name));
        $stmtCheck = $this->db->prepare("SELECT id FROM roles WHERE name = ?");
        $stmtCheck->execute([$name]);
        if ($stmtCheck->fetch()) {
            throw new \InvalidArgumentException("El rol '{$name}' ya existe en el sistema.");
        }

        $stmt = $this->db->prepare("INSERT INTO roles (name, description, status) VALUES (?, ?, 'ACTIVO')");
        $stmt->execute([$name, trim($description)]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Modificar Rol del Sistema
     */
    public function updateRole(int $roleId, string $name, string $description): bool {
        $name = strtoupper(trim($name));
        $stmtCheck = $this->db->prepare("SELECT id, name FROM roles WHERE id = ?");
        $stmtCheck->execute([$roleId]);
        $currentRole = $stmtCheck->fetch();
        if (!$currentRole) {
            throw new \InvalidArgumentException("Rol no encontrado.");
        }

        // Si es rol base del sistema, no permitir cambiar su identificador clave
        if (in_array($currentRole['name'], ['JEFE_ADMIN', 'EMPLEADO'], true) && $currentRole['name'] !== $name) {
            throw new \InvalidArgumentException("No se puede renombrar un rol base del sistema ({$currentRole['name']}).");
        }

        $stmtUnique = $this->db->prepare("SELECT id FROM roles WHERE name = ? AND id != ?");
        $stmtUnique->execute([$name, $roleId]);
        if ($stmtUnique->fetch()) {
            throw new \InvalidArgumentException("Ya existe otro rol con el nombre '{$name}'.");
        }

        $stmt = $this->db->prepare("UPDATE roles SET name = ?, description = ? WHERE id = ?");
        return $stmt->execute([$name, trim($description), $roleId]);
    }

    /**
     * Suspender o Reactivar un Rol del Sistema
     */
    public function toggleRoleStatus(int $roleId): string {
        $stmt = $this->db->prepare("SELECT name, status FROM roles WHERE id = ?");
        $stmt->execute([$roleId]);
        $role = $stmt->fetch();
        if (!$role) {
            throw new \InvalidArgumentException("Rol no encontrado.");
        }

        if ($role['name'] === 'JEFE_ADMIN') {
            throw new \InvalidArgumentException("El rol JEFE_ADMIN es vital para la administración y no puede ser suspendido.");
        }

        $newStatus = (($role['status'] ?? 'ACTIVO') === 'ACTIVO') ? 'SUSPENDIDO' : 'ACTIVO';
        $up = $this->db->prepare("UPDATE roles SET status = ? WHERE id = ?");
        $up->execute([$newStatus, $roleId]);

        return $newStatus;
    }

    /**
     * Crear una nueva Especialidad / Cargo Operativo
     */
    public function createSpecialty(string $code, string $name, string $description): int {
        $code = strtoupper(preg_replace('/[^A-Za-z0-9_]/', '_', trim($code)));
        $stmtCheck = $this->db->prepare("SELECT id FROM specialties WHERE code = ?");
        $stmtCheck->execute([$code]);
        if ($stmtCheck->fetch()) {
            throw new \InvalidArgumentException("Ya existe una especialidad con el código '{$code}'.");
        }

        $stmt = $this->db->prepare("
            INSERT INTO specialties (code, name, description, status)
            VALUES (?, ?, ?, 'ACTIVO')
        ");
        $stmt->execute([$code, trim($name), trim($description)]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Modificar Especialidad / Cargo Operativo
     */
    public function updateSpecialty(int $specId, string $code, string $name, string $description): bool {
        $code = strtoupper(preg_replace('/[^A-Za-z0-9_]/', '_', trim($code)));
        $stmtCheck = $this->db->prepare("SELECT id FROM specialties WHERE code = ? AND id != ?");
        $stmtCheck->execute([$code, $specId]);
        if ($stmtCheck->fetch()) {
            throw new \InvalidArgumentException("El código '{$code}' ya está en uso por otra especialidad.");
        }

        $stmt = $this->db->prepare("
            UPDATE specialties
            SET code = ?, name = ?, description = ?
            WHERE id = ?
        ");
        return $stmt->execute([$code, trim($name), trim($description), $specId]);
    }

    /**
     * Suspender o Reactivar una Especialidad / Cargo Operativo
     */
    public function toggleSpecialtyStatus(int $specId): string {
        $stmt = $this->db->prepare("SELECT code, status FROM specialties WHERE id = ?");
        $stmt->execute([$specId]);
        $spec = $stmt->fetch();
        if (!$spec) {
            throw new \InvalidArgumentException("Especialidad no encontrada.");
        }

        $newStatus = (($spec['status'] ?? 'ACTIVO') === 'ACTIVO') ? 'SUSPENDIDO' : 'ACTIVO';
        $up = $this->db->prepare("UPDATE specialties SET status = ? WHERE id = ?");
        $up->execute([$newStatus, $specId]);

        return $newStatus;
    }

    /**
     * Estadísticas generales de equipo y roles para el Jefe
     */
    public function getTeamStats(): array {
        $stmt = $this->db->query("
            SELECT
                COUNT(*) as total_employees,
                SUM(CASE WHEN u.status = 'ACTIVO' THEN 1 ELSE 0 END) as active_employees,
                SUM(CASE WHEN u.status = 'SUSPENDIDO' THEN 1 ELSE 0 END) as suspended_employees
            FROM employees e
            JOIN users u ON e.user_id = u.id
        ");
        $empCounts = $stmt->fetch();

        $roleCount = (int)$this->db->query("SELECT COUNT(*) FROM roles")->fetchColumn();
        $specCount = (int)$this->db->query("SELECT COUNT(*) FROM specialties")->fetchColumn();

        return [
            'total_employees' => (int)($empCounts['total_employees'] ?? 0),
            'active_employees' => (int)($empCounts['active_employees'] ?? 0),
            'suspended_employees' => (int)($empCounts['suspended_employees'] ?? 0),
            'total_roles' => $roleCount,
            'total_specialties' => $specCount
        ];
    }

    /**
     * Obtener el perfil completo de un usuario con detalles de empleado, rol y especialidades
     */
    public function getUserProfile(int $userId): ?array {
        $stmt = $this->db->prepare("
            SELECT u.id, u.email, u.status, u.role_id, r.name AS role_name,
                   e.id AS employee_id, e.full_name, e.phone, e.hire_date
            FROM users u
            JOIN roles r ON u.role_id = r.id
            LEFT JOIN employees e ON e.user_id = u.id
            WHERE u.id = ?
            LIMIT 1
        ");
        $stmt->execute([$userId]);
        $profile = $stmt->fetch();

        if (!$profile) {
            return null;
        }

        // Cargar especialidades
        $specStmt = $this->db->prepare("
            SELECT s.id, s.code, s.name, s.description
            FROM user_specialties us
            JOIN specialties s ON us.specialty_id = s.id
            WHERE us.user_id = ?
            ORDER BY s.id ASC
        ");
        $specStmt->execute([$userId]);
        $profile['specialties'] = $specStmt->fetchAll();

        return $profile;
    }

    /**
     * Verificar si la contraseña actual coincide con el hash en base de datos
     */
    public function verifyPassword(int $userId, string $password): bool {
        $stmt = $this->db->prepare("SELECT password_hash FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $hash = $stmt->fetchColumn();
        return $hash && password_verify($password, $hash);
    }

    /**
     * Actualizar los datos del perfil (nombre, teléfono, correo y contraseña opcional)
     */
    public function updateProfile(int $userId, string $fullName, string $phone, string $email, ?string $newPassword = null): void {
        // Validar unicidad de correo
        $stmtEmail = $this->db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmtEmail->execute([trim($email), $userId]);
        if ($stmtEmail->fetch()) {
            throw new \InvalidArgumentException("El correo electrónico '" . htmlspecialchars($email) . "' ya está en uso por otra cuenta.");
        }

        $this->beginTransaction();
        try {
            // 1. Actualizar usuario (email y contraseña opcional)
            if ($newPassword !== null && trim($newPassword) !== '') {
                $passHash = password_hash(trim($newPassword), PASSWORD_BCRYPT);
                $stmtUser = $this->db->prepare("
                    UPDATE users
                    SET email = ?, password_hash = ?
                    WHERE id = ?
                ");
                $stmtUser->execute([trim($email), $passHash, $userId]);
            } else {
                $stmtUser = $this->db->prepare("
                    UPDATE users
                    SET email = ?
                    WHERE id = ?
                ");
                $stmtUser->execute([trim($email), $userId]);
            }

            // 2. Actualizar o insertar ficha de empleado
            $checkEmp = $this->db->prepare("SELECT id FROM employees WHERE user_id = ?");
            $checkEmp->execute([$userId]);
            if ($checkEmp->fetch()) {
                $stmtEmp = $this->db->prepare("
                    UPDATE employees
                    SET full_name = ?, phone = ?
                    WHERE user_id = ?
                ");
                $stmtEmp->execute([trim($fullName), trim($phone), $userId]);
            } else {
                $stmtEmp = $this->db->prepare("
                    INSERT INTO employees (user_id, full_name, phone, hire_date)
                    VALUES (?, ?, ?, ?)
                ");
                $stmtEmp->execute([$userId, trim($fullName), trim($phone), date('Y-m-d')]);
            }

            $this->commit();
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }
}
