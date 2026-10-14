<?php
/**
 * Controlador de Reportes Ejecutivos
 * Exclusivamente: Rendimiento y Productividad por Empleado
 * Filtro exclusivo de 15 o 30 días
 */

namespace App\Controllers;

use Config\Auth;
use App\Models\ReportModel;

class ReportController extends Controller {
    private ReportModel $reportModel;

    public function __construct() {
        $this->reportModel = new ReportModel();
    }

    public function index(): void {
        Auth::requireJefe();

        $days = (int)($_GET['days'] ?? 30);
        if ($days !== 15 && $days !== 30) {
            $days = 30;
        }

        $report = $this->reportModel->getExecutiveReport($days);

        $this->render('reports/index', [
            'pageTitle' => "Reportes de Rendimiento & Productividad ({$days} Días)",
            'report' => $report,
            'currentDays' => $days
        ]);
    }

    /**
     * Exportación de datos de rendimiento y productividad a formato CSV
     */
    public function exportExcel(): void {
        Auth::requireJefe();

        $days = (int)($_GET['days'] ?? 30);
        if ($days !== 15 && $days !== 30) {
            $days = 30;
        }

        $report = $this->reportModel->getExecutiveReport($days);
        $filename = "reporte_rendimiento_productividad_{$days}_dias_" . date('Ymd_His') . ".csv";

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        // Escribir BOM UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($output, ['MIAMI LUXURY MEDIA - REPORTE DE RENDIMIENTO Y PRODUCTIVIDAD']);
        fputcsv($output, ['Periodo:', "Ultimos {$days} dias", 'Desde:', $report['since_date'], 'Generado:', $report['generated_at']]);
        fputcsv($output, []);

        // 1. Resumen de Rendimiento General
        fputcsv($output, ['--- METRICAS DE RENDIMIENTO GENERAL ---']);
        fputcsv($output, ['Metrica', 'Valor']);
        fputcsv($output, ['Tareas Totales en el Periodo', $report['performance']['total_tasks']]);
        fputcsv($output, ['Tareas Culminadas y Aprobadas', $report['performance']['completed_tasks']]);
        fputcsv($output, ['Tareas en Proceso Activo', $report['performance']['in_progress_tasks']]);
        fputcsv($output, ['Tareas por Revisar', $report['performance']['in_review_tasks']]);
        fputcsv($output, ['Tareas con Observaciones / Correccion', $report['performance']['correction_tasks']]);
        fputcsv($output, ['Entregables Subidos en el Periodo', $report['performance']['total_deliverables']]);
        fputcsv($output, ['Tasa de Efectividad General', $report['performance']['completion_rate'] . '%']);
        fputcsv($output, []);

        // 2. Productividad por Empleado
        fputcsv($output, ['--- PRODUCTIVIDAD POR EMPLEADO ---']);
        fputcsv($output, ['Empleado', 'Tareas Asignadas', 'Tareas Culminadas', 'En Proceso', 'Por Revisar', 'Entregables Subidos', 'Tasa Cumplimiento']);
        foreach ($report['staff_productivity'] as $st) {
            fputcsv($output, [
                $st['full_name'],
                $st['assigned_tasks'],
                $st['completed_tasks'],
                $st['in_progress_tasks'],
                $st['in_review_tasks'],
                $st['deliverables_submitted'],
                $st['completion_rate'] . '%'
            ]);
        }

        fclose($output);
        exit;
    }
}
