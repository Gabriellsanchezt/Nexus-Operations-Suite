<?php
/**
 * Modelo de Almacén de Contratos Finales (PDFs Concluidos)
 * Solo se almacenan contratos terminados asociados a un Realtor.
 * La fecha de subida se calcula automáticamente.
 */

namespace App\Models;

use PDO;

class ContractModel extends Model {
    /**
     * Listar todos los contratos finales subidos
     */
    public function getAll(?int $realtorId = null): array {
        $sql = "
            SELECT c.*, r.company_name, r.contact_person, r.miami_zone,
                   e.full_name AS uploader_name
            FROM contracts c
            JOIN realtors r ON c.realtor_id = r.id
            LEFT JOIN users u ON c.uploaded_by_user_id = u.id
            LEFT JOIN employees e ON e.user_id = u.id
        ";

        $params = [];
        if ($realtorId !== null) {
            $sql .= " WHERE c.realtor_id = ?";
            $params[] = $realtorId;
        }

        $sql .= " ORDER BY c.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Obtener un contrato por ID
     */
    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT c.*, r.company_name, r.contact_person, r.miami_zone,
                   e.full_name AS uploader_name
            FROM contracts c
            JOIN realtors r ON c.realtor_id = r.id
            LEFT JOIN users u ON c.uploaded_by_user_id = u.id
            LEFT JOIN employees e ON e.user_id = u.id
            WHERE c.id = ?
            LIMIT 1
        ");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Registrar un nuevo contrato final con su archivo PDF
     * La fecha de subida es automática (CURRENT_TIMESTAMP)
     */
    public function uploadContract(int $realtorId, string $title, string $pdfFilename, ?string $notes = null, ?int $userId = null): int {
        $stmt = $this->db->prepare("
            INSERT INTO contracts (realtor_id, contract_title, pdf_filename, notes, uploaded_by_user_id, created_at)
            VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
        ");
        $stmt->execute([
            $realtorId,
            trim($title),
            trim($pdfFilename),
            trim($notes ?? '') ?: null,
            $userId
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Eliminar contrato y su archivo físico
     */
    public function delete(int $id): bool {
        $contract = $this->getById($id);
        if (!$contract) {
            return false;
        }

        // Eliminar archivo físico de almacenamiento si existe
        $pdfPath = BASE_PATH . '/storage/contracts/' . $contract['pdf_filename'];
        if (file_exists($pdfPath)) {
            @unlink($pdfPath);
        }

        $stmt = $this->db->prepare("DELETE FROM contracts WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Estadísticas de contratos finales
     */
    public function getStats(): array {
        $stmt = $this->db->query("
            SELECT 
                COUNT(*) AS total_contracts,
                COUNT(DISTINCT realtor_id) AS total_realtors_with_contract,
                SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) AS uploads_last_30_days
            FROM contracts
        ");
        $row = $stmt->fetch();
        return [
            'total' => (int)($row['total_contracts'] ?? 0),
            'realtors_count' => (int)($row['total_realtors_with_contract'] ?? 0),
            'recent_uploads' => (int)($row['uploads_last_30_days'] ?? 0)
        ];
    }
}
