<?php
/**
 * Modelo de Cronograma y Calendario de Email Marketing
 * Administra las fechas estipuladas de subida y envío de emails por cada Realtor
 */

namespace App\Models;

use PDO;

class EmailMarketingModel extends Model {
    /**
     * Obtener todas las campañas de email programadas
     */
    public function getAll(?int $realtorId = null): array {
        $sql = "
            SELECT ec.*, r.company_name, r.contact_person, r.miami_zone
            FROM email_campaigns ec
            JOIN realtors r ON ec.realtor_id = r.id
        ";
        $params = [];

        if ($realtorId !== null && $realtorId > 0) {
            $sql .= " WHERE ec.realtor_id = ?";
            $params[] = $realtorId;
        }

        $sql .= " ORDER BY ec.scheduled_date ASC, ec.id ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Programar un nuevo email para un Realtor
     */
    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO email_campaigns (realtor_id, title, scheduled_date, status, notes)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            (int)$data['realtor_id'],
            trim($data['title']),
            $data['scheduled_date'],
            $data['status'] ?? 'PROGRAMADO',
            trim($data['notes'] ?? '') ?: null
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Actualizar estado (PROGRAMADO, SUBIDO, ENVIADO)
     */
    public function updateStatus(int $id, string $status): bool {
        $stmt = $this->db->prepare("UPDATE email_campaigns SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    /**
     * Eliminar campaña programada
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM email_campaigns WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Contadores para métricas de email marketing
     */
    public function getCounts(): array {
        $stmt = $this->db->query("
            SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN status = 'PROGRAMADO' THEN 1 ELSE 0 END) AS programados,
                SUM(CASE WHEN status = 'SUBIDO' THEN 1 ELSE 0 END) AS subidos,
                SUM(CASE WHEN status = 'ENVIADO' THEN 1 ELSE 0 END) AS enviados
            FROM email_campaigns
        ");
        $row = $stmt->fetch();
        return [
            'total' => (int)($row['total'] ?? 0),
            'programados' => (int)($row['programados'] ?? 0),
            'subidos' => (int)($row['subidos'] ?? 0),
            'enviados' => (int)($row['enviados'] ?? 0)
        ];
    }
}
