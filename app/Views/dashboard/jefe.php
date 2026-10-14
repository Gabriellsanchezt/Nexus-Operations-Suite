<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold mb-1"><i class="bi bi-speedometer2 text-primary me-2"></i>Dashboard de Dirección General</h2>
        <p class="text-muted mb-0">Control integral de producción audiovisual, contratos y cartera de Realtors en Miami</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('/reports') ?>" class="btn btn-outline-dark">
            <i class="bi bi-graph-up me-1"></i> Reportes Ejecutivos
        </a>
        <a href="<?= url('/contracts/final') ?>" class="btn btn-outline-danger fw-semibold">
            <i class="bi bi-file-earmark-pdf me-1"></i> Contratos Finales (PDF)
        </a>
    </div>
</div>

<!-- Tarjetas KPI del Flujo de Trabajo (Pipeline) -->
<div class="row g-3 mb-4">
    <!-- Nuevas -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card exec-kpi-card kpi-navy h-100">
            <div class="card-body p-3">
                <span class="text-muted small fw-semibold text-uppercase">Tareas Nuevas</span>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <h3 class="fw-bold mb-0 text-primary"><?= $counts['nuevas'] ?></h3>
                    <div class="exec-icon-box exec-icon-navy">
                        <i class="bi bi-plus-circle"></i>
                    </div>
                </div>
                <a href="<?= url('/activities?status=NUEVA') ?>" class="stretched-link small text-decoration-none mt-2 d-inline-block text-muted">Ver detalles &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Canalizadas (En Proceso) -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card exec-kpi-card kpi-steel h-100">
            <div class="card-body p-3">
                <span class="text-muted small fw-semibold text-uppercase">Canalizadas</span>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <h3 class="fw-bold mb-0 text-info"><?= $counts['canalizadas'] ?></h3>
                    <div class="exec-icon-box exec-icon-steel">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                </div>
                <a href="<?= url('/activities?status=EN_PROCESO') ?>" class="stretched-link small text-decoration-none mt-2 d-inline-block text-muted">En producción &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Por Aprobar (Entregadas) -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card exec-kpi-card kpi-bronze h-100">
            <div class="card-body p-3">
                <span class="text-muted small fw-semibold text-uppercase">Por Aprobar</span>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <h3 class="fw-bold mb-0 text-warning"><?= $counts['por_aprobar'] ?></h3>
                    <div class="exec-icon-box exec-icon-bronze">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
                <a href="<?= url('/activities?status=ENTREGADA_REVISION') ?>" class="stretched-link small text-decoration-none mt-2 d-inline-block text-muted">Revisar videos &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Culminadas (Aprobadas) -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card exec-kpi-card kpi-sage h-100">
            <div class="card-body p-3">
                <span class="text-muted small fw-semibold text-uppercase">Culminadas</span>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <h3 class="fw-bold mb-0 text-success"><?= $counts['culminadas'] ?></h3>
                    <div class="exec-icon-box exec-icon-sage">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                </div>
                <a href="<?= url('/activities?status=APROBADA') ?>" class="stretched-link small text-decoration-none mt-2 d-inline-block text-muted">Completadas &rarr;</a>
            </div>
        </div>
    </div>
</div>

<!-- TABLERO DE PRODUCCIÓN ESCALABLE POR TIEMPO (FIFO) -->
<?php
$activities = $allActivities ?? [];
require APP_PATH . '/Views/dashboard/partials/production_board.php';
?>

<!-- MINI CALENDARIO INTERACTIVO DE ACTIVIDADES DEL MES (DIRECCIÓN) -->
<?php
$calendarActivities = $allActivities ?? [];
$isJefeView = true;
$calendarTitle = 'Calendario de Producción Audiovisual del Mes (Miami-Dade)';
$calendarSubtitle = 'Supervisión de rodajes, drones, carruseles y fechas límite de entrega del equipo';
require APP_PATH . '/Views/dashboard/partials/mini_calendar.php';
?>
