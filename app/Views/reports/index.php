<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold mb-1">
            <i class="bi bi-graph-up-arrow text-primary me-2"></i>Reportes de Rendimiento & Productividad
        </h2>
        <p class="text-muted mb-0">Métricas operativas consolidadas exclusivamente para períodos de 15 y 30 días</p>
    </div>
    <div class="d-flex gap-2 align-items-center flex-wrap">
        <!-- Filtro Exclusivo de 15 / 30 Días -->
        <div class="btn-group shadow-sm" role="group">
            <a href="<?= url('/reports?days=15') ?>" class="btn btn-sm <?= ($currentDays === 15) ? 'btn-primary fw-bold' : 'btn-outline-secondary' ?>">
                <i class="bi bi-calendar2-range me-1"></i> Últimos 15 Días
            </a>
            <a href="<?= url('/reports?days=30') ?>" class="btn btn-sm <?= ($currentDays === 30) ? 'btn-primary fw-bold' : 'btn-outline-secondary' ?>">
                <i class="bi bi-calendar2-month me-1"></i> Últimos 30 Días
            </a>
        </div>

        <a href="<?= url('/reports/export-excel?days=' . $currentDays) ?>" class="btn btn-sm btn-outline-success fw-semibold shadow-sm">
            <i class="bi bi-file-earmark-excel me-1"></i> Exportar CSV
        </a>
    </div>
</div>

<!-- 1. Tarjetas de Rendimiento Operativo General -->
<div class="row g-3 mb-4">
    <!-- Tasa de Cumplimiento -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card exec-kpi-card kpi-sage h-100">
            <div class="card-body p-3">
                <span class="text-muted small fw-semibold text-uppercase">Tasa de Efectividad</span>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <h3 class="fw-bold mb-0 text-success"><?= $report['performance']['completion_rate'] ?>%</h3>
                    <div class="exec-icon-box exec-icon-sage">
                        <i class="bi bi-pie-chart-fill"></i>
                    </div>
                </div>
                <small class="text-muted mt-2 d-block">
                    <?= $report['performance']['completed_tasks'] ?> de <?= $report['performance']['total_tasks'] ?> tareas culminadas
                </small>
            </div>
        </div>
    </div>

    <!-- Tareas Culminadas -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card exec-kpi-card kpi-navy h-100">
            <div class="card-body p-3">
                <span class="text-muted small fw-semibold text-uppercase">Tareas Culminadas</span>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <h3 class="fw-bold mb-0 text-primary"><?= $report['performance']['completed_tasks'] ?></h3>
                    <div class="exec-icon-box exec-icon-navy">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                </div>
                <small class="text-muted mt-2 d-block">Aprobadas por Dirección</small>
            </div>
        </div>
    </div>

    <!-- Tareas en Producción / Revisión -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card exec-kpi-card kpi-steel h-100">
            <div class="card-body p-3">
                <span class="text-muted small fw-semibold text-uppercase">En Producción Activa</span>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <h3 class="fw-bold mb-0 text-info">
                        <?= $report['performance']['in_progress_tasks'] + $report['performance']['in_review_tasks'] ?>
                    </h3>
                    <div class="exec-icon-box exec-icon-steel">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                </div>
                <small class="text-muted mt-2 d-block">
                    <?= $report['performance']['in_progress_tasks'] ?> en curso, <?= $report['performance']['in_review_tasks'] ?> por revisar
                </small>
            </div>
        </div>
    </div>

    <!-- Entregables Subidos -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card exec-kpi-card kpi-bronze h-100">
            <div class="card-body p-3">
                <span class="text-muted small fw-semibold text-uppercase">Entregables Subidos</span>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <h3 class="fw-bold mb-0 text-warning"><?= $report['performance']['total_deliverables'] ?></h3>
                    <div class="exec-icon-box exec-icon-bronze">
                        <i class="bi bi-link-45deg"></i>
                    </div>
                </div>
                <small class="text-muted mt-2 d-block">Piezas multimedia entregadas</small>
            </div>
        </div>
    </div>
</div>

<!-- 2. Tabla de Productividad por Empleado -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-people-fill text-primary me-2"></i>Productividad por Empleado (Últimos <?= $currentDays ?> Días)
            </h5>
            <span class="small text-muted">Desglose de tareas completadas, entregables presentados y porcentaje de cumplimiento individual</span>
        </div>
        <span class="badge bg-light text-dark border">
            Desde: <?= date('d/m/Y', strtotime($report['since_date'])) ?>
        </span>
    </div>
    <div class="card-body p-0">
        <?php if (empty($report['staff_productivity'])): ?>
            <div class="text-center py-5 text-muted">
                No hay registros de productividad en este período.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th>Especialista / Empleado</th>
                            <th>Tareas Asignadas</th>
                            <th>Tareas Culminadas</th>
                            <th>En Proceso</th>
                            <th>Por Revisar</th>
                            <th>Entregables Subidos</th>
                            <th style="width: 220px;">Tasa de Cumplimiento</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($report['staff_productivity'] as $st): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($st['full_name']) ?></div>
                                    <small class="text-muted">ID: #<?= (int)$st['employee_id'] ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border font-monospace">
                                        <?= (int)$st['assigned_tasks'] ?> tareas
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-success font-monospace">
                                        <i class="bi bi-check-lg me-1"></i><?= (int)$st['completed_tasks'] ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark font-monospace">
                                        <?= (int)$st['in_progress_tasks'] ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark font-monospace">
                                        <?= (int)$st['in_review_tasks'] ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-dark font-monospace">
                                        <i class="bi bi-link-45deg me-1"></i><?= (int)$st['deliverables_submitted'] ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 8px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: <?= $st['completion_rate'] ?>%"></div>
                                        </div>
                                        <span class="small fw-bold text-dark font-monospace"><?= $st['completion_rate'] ?>%</span>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
