<?php
/**
 * Modelo de Clientes Realtors (Inmobiliarias y Brokers en Miami)
 */

namespace App\Models;

use PDO;

class RealtorModel extends Model {
    /**
     * Listar todos los realtors
     */
    public function getAll(): array {
        $stmt = $this->db->query("
            SELECT r.*,
                   (SELECT COUNT(*) FROM activities a WHERE a.realtor_id = r.id) AS total_activities_count
            FROM realtors r
            ORDER BY r.company_name ASC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Obtener un realtor por ID
     */
    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM realtors WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Crear un nuevo Realtor
     */
    public function create(array $data): int {
        $socialJson = is_array($data['social_handles'] ?? null)
            ? json_encode($data['social_handles'])
            : ($data['social_handles'] ?? json_encode(['instagram' => '']));

        $stmt = $this->db->prepare("
            INSERT INTO realtors (company_name, contact_person, phone, email, miami_zone, credentials_url, dropbox_url, social_handles, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            trim($data['company_name']),
            trim($data['contact_person']),
            trim($data['phone']),
            trim($data['email']),
            trim($data['miami_zone']),
            trim($data['credentials_url'] ?? '') ?: null,
            trim($data['dropbox_url'] ?? '') ?: null,
            $socialJson,
            $data['status'] ?? 'ACTIVO'
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Actualizar datos del Realtor
     */
    public function update(int $id, array $data): bool {
        $socialJson = is_array($data['social_handles'] ?? null)
            ? json_encode($data['social_handles'])
            : ($data['social_handles'] ?? null);

        $stmt = $this->db->prepare("
            UPDATE realtors
            SET company_name = ?,
                contact_person = ?,
                phone = ?,
                email = ?,
                miami_zone = ?,
                credentials_url = ?,
                dropbox_url = ?,
                social_handles = COALESCE(?, social_handles),
                status = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            trim($data['company_name']),
            trim($data['contact_person']),
            trim($data['phone']),
            trim($data['email']),
            trim($data['miami_zone']),
            trim($data['credentials_url'] ?? '') ?: null,
            trim($data['dropbox_url'] ?? '') ?: null,
            $socialJson,
            $data['status'] ?? 'ACTIVO',
            $id
        ]);
    }

    /**
     * Obtener los proyectos / actividades audiovisuales gestionados con el Realtor
     */
    public function getProjects(int $realtorId): array {
        $stmt = $this->db->prepare("
            SELECT a.*, e.full_name AS employee_name,
                   (SELECT COUNT(*) FROM activity_deliverables ad WHERE ad.activity_id = a.id) AS deliverables_count
            FROM activities a
            JOIN employees e ON a.employee_id = e.id
            WHERE a.realtor_id = ?
            ORDER BY a.created_at DESC
        ");
        $stmt->execute([$realtorId]);
        return $stmt->fetchAll();
    }

    /**
     * Eliminar o desactivar un Realtor
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM realtors WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
