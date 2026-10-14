<?php
/**
 * Modelo de Reportes Ejecutivos
 * Exclusivamente: Rendimiento Operativo y Productividad por Empleado
 * Filtro exclusivo: 15 / 30 días
 * Cero términos financieros o métricas monetarias
 */

namespace App\Models;

use PDO;

class ReportModel extends Model {
    /**
     * Obtener métricas consolidadas exclusivamente para 15 o 30 días
     */
    public function getExecutiveReport(int $days = 30): array {
        $days = in_array($days, [15, 30], true) ? $days : 30;
        $sinceDate = date('Y-m-d 00:00:00', strtotime("-{$days} days"));

        // 1. Rendimiento General de Producción en el Período
        $stmtPerformance = $this->db->prepare("
            SELECT
                COUNT(id) AS total_tasks,
                SUM(CASE WHEN current_status = 'APROBADA' THEN 1 ELSE 0 END) AS completed_tasks,
                SUM(CASE WHEN current_status = 'EN_PROCESO' THEN 1 ELSE 0 END) AS in_progress_tasks,
                SUM(CASE WHEN current_status = 'ENTREGADA_REVISION' THEN 1 ELSE 0 END) AS in_review_tasks,
                SUM(CASE WHEN current_status = 'CORRECCION' THEN 1 ELSE 0 END) AS correction_tasks,
                SUM(CASE WHEN current_status = 'NUEVA' THEN 1 ELSE 0 END) AS new_tasks
            FROM activities
            WHERE created_at >= ?
        ");
        $stmtPerformance->execute([$sinceDate]);
        $perf = $stmtPerformance->fetch();

        $totalTasks = (int)($perf['total_tasks'] ?? 0);
        $completedTasks = (int)($perf['completed_tasks'] ?? 0);
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;

        // Entregables totales en el período
        $stmtDeliv = $this->db->prepare("
            SELECT COUNT(*) AS total_deliverables
            FROM activity_deliverables ad
            WHERE ad.submitted_at >= ?
        ");
        $stmtDeliv->execute([$sinceDate]);
        $totalDeliverables = (int)($stmtDeliv->fetchColumn() ?? 0);

        // 2. Productividad por Empleado en el Período
        $stmtStaff = $this->db->prepare("
            SELECT
                e.id AS employee_id,
                e.full_name,
                COUNT(a.id) AS assigned_tasks,
                SUM(CASE WHEN a.current_status = 'APROBADA' THEN 1 ELSE 0 END) AS completed_tasks,
                SUM(CASE WHEN a.current_status = 'EN_PROCESO' THEN 1 ELSE 0 END) AS in_progress_tasks,
                SUM(CASE WHEN a.current_status = 'ENTREGADA_REVISION' THEN 1 ELSE 0 END) AS in_review_tasks,
                (
                    SELECT COUNT(*)
                    FROM activity_deliverables ad
                    JOIN activities sub_a ON ad.activity_id = sub_a.id
                    WHERE sub_a.employee_id = e.id AND ad.submitted_at >= ?
                ) AS deliverables_submitted
            FROM employees e
            LEFT JOIN activities a ON a.employee_id = e.id AND a.created_at >= ?
            GROUP BY e.id, e.full_name
            ORDER BY completed_tasks DESC, assigned_tasks DESC
        ");
        $stmtStaff->execute([$sinceDate, $sinceDate]);
        $staffProductivity = $stmtStaff->fetchAll();

        // Calcular tasa individual de productividad para cada empleado
        foreach ($staffProductivity as &$st) {
            $assigned = (int)$st['assigned_tasks'];
            $completed = (int)$st['completed_tasks'];
            $st['completion_rate'] = $assigned > 0 ? round(($completed / $assigned) * 100, 1) : 0;
        }

        return [
            'days' => $days,
            'since_date' => $sinceDate,
            'generated_at' => date('Y-m-d H:i:s'),
            'performance' => [
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'in_progress_tasks' => (int)($perf['in_progress_tasks'] ?? 0),
                'in_review_tasks' => (int)($perf['in_review_tasks'] ?? 0),
                'correction_tasks' => (int)($perf['correction_tasks'] ?? 0),
                'new_tasks' => (int)($perf['new_tasks'] ?? 0),
                'completion_rate' => $completionRate,
                'total_deliverables' => $totalDeliverables
            ],
            'staff_productivity' => $staffProductivity
        ];
    }
}
