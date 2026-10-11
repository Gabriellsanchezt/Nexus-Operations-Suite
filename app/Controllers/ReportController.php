<?php
/**
 * Controlador de Reportes Ejecutivos de Operaciones y Producción
 * Soporta Períodos Semanal, Quincenal y Mensual, y exportación a Excel / CSV
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

        $period = $_GET['period'] ?? 'mensual';
        if (!in_array($period, ['semanal', 'quincenal', 'mensual'], true)) {
            $period = 'mensual';
        }

        $report = $this->reportModel->getExecutiveReport($period);

        $this->render('reports/index', [
            'pageTitle' => 'Reportes Ejecutivos de Operaciones y Producción',
            'report' => $report,
            'currentPeriod' => $period
        ]);
    }

    /**
     * Exportación de datos consolidados a formato CSV compatible con Microsoft Excel
     */
    public function exportExcel(): void {
        Auth::requireJefe();

        $period = $_GET['period'] ?? 'mensual';
        $report = $this->reportModel->getExecutiveReport($period);

        $filename = "reporte_operativo_miami_agency_{$period}_" . date('Ymd_His') . ".csv";

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        // Escribir BOM UTF-8 para compatibilidad directa con Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Cabecera del reporte
        fputcsv($output, ['MIAMI LUXURY MEDIA AGENCY - REPORTE EJECUTIVO DE OPERACIONES Y PRODUCCION']);
        fputcsv($output, ['Periodo:', strtoupper($period), 'Generado:', $report['generated_at']]);
        fputcsv($output, []);

        // Métricas de Contratos y Acuerdos
        fputcsv($output, ['--- RESUMEN DE CONTRATOS Y ACUERDOS FORMALIZADOS ---']);
        fputcsv($output, ['Métrica', 'Valor']);
        fputcsv($output, ['Contratos Formalizados Aprobados', $report['financial']['total_approved_contracts']]);
        fputcsv($output, ['Tareas Audiovisuales Culminadas', $report['operations']['completed_tasks']]);
        fputcsv($output, ['Tareas en Proceso Activo', $report['operations']['in_progress_tasks'] + $report['operations']['in_review_tasks']]);
        fputcsv($output, []);

        // Desglose por Zona de Miami
        fputcsv($output, ['--- COBERTURA Y CONTRATOS POR ZONA DE MIAMI ---']);
        fputcsv($output, ['Zona de Miami', 'Contratos Formalizados', 'Estado Cobertura']);
        foreach ($report['financial']['by_zone'] as $z) {
            fputcsv($output, [
                $z['miami_zone'],
                $z['contract_count'],
                'Cobertura Activa'
            ]);
        }
        fputcsv($output, []);

        // Resumen Operativo de Tareas Audiovisuales
        fputcsv($output, ['--- METRICAS DE OPERACION AUDIOVISUAL ---']);
        fputcsv($output, ['Métrica', 'Cantidad']);
        fputcsv($output, ['Total Tareas Programadas', $report['operations']['total_tasks']]);
        fputcsv($output, ['Tareas Culminadas y Aprobadas', $report['operations']['completed_tasks']]);
        fputcsv($output, ['Tareas en Revisión de Entregables', $report['operations']['in_review_tasks']]);
        fputcsv($output, ['Tareas en Proceso Activo', $report['operations']['in_progress_tasks']]);
        fputcsv($output, ['Tareas con Observaciones / Corrección', $report['operations']['correction_tasks']]);
        fputcsv($output, []);

        // Rendimiento del Personal Creativo
        fputcsv($output, ['--- DESEMPEÑO POR ESPECIALISTA CREATIVO ---']);
        fputcsv($output, ['Especialista', 'Tareas Asignadas', 'Tareas Aprobadas', 'Correcciones']);
        foreach ($report['operations']['staff'] as $st) {
            fputcsv($output, [
                $st['full_name'],
                $st['assigned_tasks'],
                $st['completed_tasks'],
                $st['rework_tasks']
            ]);
        }

        fclose($output);
        exit;
    }
}
