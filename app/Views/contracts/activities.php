<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">
            <i class="bi bi-clock-history text-primary me-2"></i>Actividades y Estado de Contratos
        </h4>
        <p class="text-muted small mb-0">
            Seguimiento del ciclo legal completo: borradores en preparación, contratos en revisión y resoluciones culminadas (Aprobadas & Negadas).
        </p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <a href="<?= url('/contracts/create') ?>" class="btn btn-primary btn-sm fw-semibold shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Redactar Nuevo Contrato
        </a>
        <?php if (!empty($isJefe)): ?>
            <a href="<?= url('/contracts/final') ?>" class="btn btn-outline-success btn-sm fw-semibold">
                <i class="bi bi-archive me-1"></i> Repositorio Final
            </a>
        <?php endif; ?>
    </div>
</div>

<?php
// Métricas rápidas de culminación y estado
$totalContracts = count($contracts ?? []);
$aprobadosCount = count(array_filter($contracts ?? [], fn($c) => $c['status'] === 'APROBADO'));
$negadosCount = count(array_filter($contracts ?? [], fn($c) => $c['status'] === 'RECHAZADO'));
$enRevisionCount = count(array_filter($contracts ?? [], fn($c) => $c['status'] === 'EN_REVISION'));
$borradoresCount = count(array_filter($contracts ?? [], fn($c) => $c['status'] === 'BORRADOR'));
?>

<!-- Tarjetas KPI de Estado de Actividades -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl">
        <div class="card border-0 shadow-sm border-start border-primary border-4 h-100">
            <div class="card-body">
                <span class="text-muted small fw-semibold text-uppercase">Total Contratos</span>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <h3 class="fw-bold mb-0 text-primary"><?= $totalContracts ?></h3>
                    <i class="bi bi-files text-primary fs-3"></i>
                </div>
                <span class="small text-muted mt-2 d-inline-block">Expedientes registrados</span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl">
        <div class="card border-0 shadow-sm border-start border-success border-4 h-100 bg-success-subtle bg-opacity-10">
            <div class="card-body">
                <span class="text-success small fw-semibold text-uppercase">Culminados: Aprobados</span>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <h3 class="fw-bold mb-0 text-success"><?= $aprobadosCount ?></h3>
                    <i class="bi bi-check2-circle text-success fs-3"></i>
                </div>
                <span class="small text-muted mt-2 d-inline-block">Activos en repositorio</span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl">
        <div class="card border-0 shadow-sm border-start border-danger border-4 h-100 bg-danger-subtle bg-opacity-10">
            <div class="card-body">
                <span class="text-danger small fw-semibold text-uppercase">Culminados: Negados</span>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <h3 class="fw-bold mb-0 text-danger"><?= $negadosCount ?></h3>
                    <i class="bi bi-x-circle text-danger fs-3"></i>
                </div>
                <span class="small text-muted mt-2 d-inline-block">Con observaciones</span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl">
        <div class="card border-0 shadow-sm border-start border-warning border-4 h-100 bg-warning-subtle bg-opacity-10">
            <div class="card-body">
                <span class="text-warning-emphasis small fw-semibold text-uppercase">En Revisión</span>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <h3 class="fw-bold mb-0 text-warning-emphasis"><?= $enRevisionCount ?></h3>
                    <i class="bi bi-hourglass-split text-warning-emphasis fs-3"></i>
                </div>
                <span class="small text-muted mt-2 d-inline-block">Pendiente de firma</span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl">
        <div class="card border-0 shadow-sm border-start border-secondary border-4 h-100">
            <div class="card-body">
                <span class="text-muted small fw-semibold text-uppercase">Borradores</span>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <h3 class="fw-bold mb-0 text-secondary"><?= $borradoresCount ?></h3>
                    <i class="bi bi-pencil-square text-secondary fs-3"></i>
                </div>
                <span class="small text-muted mt-2 d-inline-block">En redacción</span>
            </div>
        </div>
    </div>
</div>

<!-- Filtros de Estado y Pestañas -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
        <ul class="nav nav-pills gap-2" id="contractFilterPills" role="tablist">
            <li class="nav-item">
                <button class="nav-link active py-1 px-3 small fw-semibold" onclick="filterContracts('ALL', this)">
                    Todos (<?= $totalContracts ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link py-1 px-3 small fw-semibold text-success" onclick="filterContracts('APROBADO', this)">
                    <i class="bi bi-check-circle me-1"></i> Culminadas Aprobadas (<?= $aprobadosCount ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link py-1 px-3 small fw-semibold text-danger" onclick="filterContracts('RECHAZADO', this)">
                    <i class="bi bi-x-circle me-1"></i> Culminadas Negadas (<?= $negadosCount ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link py-1 px-3 small fw-semibold text-warning-emphasis" onclick="filterContracts('EN_REVISION', this)">
                    <i class="bi bi-hourglass-split me-1"></i> En Revisión (<?= $enRevisionCount ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link py-1 px-3 small fw-semibold text-secondary" onclick="filterContracts('BORRADOR', this)">
                    <i class="bi bi-file-earmark me-1"></i> Borradores (<?= $borradoresCount ?>)
                </button>
            </li>
        </ul>

        <div class="input-group input-group-sm" style="max-width: 260px;">
            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input type="text" id="contractSearchInput" class="form-control border-start-0 bg-light" placeholder="Buscar por código, realtor..." onkeyup="searchContracts()">
        </div>
    </div>

    <div class="card-body p-0">
        <?php if (empty($contracts)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-file-earmark-x text-secondary fs-1 d-block mb-2 opacity-50"></i>
                <h6 class="fw-bold mb-1">No hay actividades de contratos registradas</h6>
                <p class="small text-muted mb-3">Comienza creando tu primer contrato para Realtors de Miami.</p>
                <a href="<?= url('/contracts/create') ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Ir al Generador de Contratos
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="contractsActivitiesTable">
                    <thead class="table-light small text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        <tr>
                            <th class="ps-4">Código & Vigencia</th>
                            <th>Realtor / Brokerage</th>
                            <th>Tipo de Acuerdo</th>
                            <th>Estado Actual</th>
                            <th>Resolución & Observaciones</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contracts as $c): ?>
                            <tr class="contract-row" 
                                data-status="<?= htmlspecialchars($c['status']) ?>"
                                data-search="<?= strtolower(htmlspecialchars($c['contract_code'] . ' ' . $c['company_name'] . ' ' . $c['contact_person'] . ' ' . $c['miami_zone'])) ?>">
                                
                                <!-- Código y Vigencia -->
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="p-2 rounded bg-light border text-primary font-monospace fw-bold small">
                                            <?= htmlspecialchars($c['contract_code']) ?>
                                        </div>
                                    </div>
                                    <div class="small text-muted mt-1" style="font-size: 0.72rem;">
                                        <i class="bi bi-calendar-event me-1"></i>
                                        <?= date('d/m/Y', strtotime($c['start_date'])) ?> &rarr; <?= date('d/m/Y', strtotime($c['end_date'])) ?>
                                    </div>
                                </td>

                                <!-- Realtor & Zona -->
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($c['company_name']) ?></div>
                                    <div class="small text-muted d-flex align-items-center gap-1">
                                        <i class="bi bi-geo-alt text-danger"></i>
                                        <span><?= htmlspecialchars($c['miami_zone']) ?></span>
                                        &bull;
                                        <span><?= htmlspecialchars($c['contact_person']) ?></span>
                                    </div>
                                </td>

                                <!-- Tipo de Acuerdo -->
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1">
                                        <i class="bi bi-camera-reels me-1 text-primary"></i>Producción Mensual
                                    </span>
                                    <span class="text-muted d-block small" style="font-size: 0.7rem;">Plan Operativo</span>
                                </td>

                                <!-- Estado Actual -->
                                <td>
                                    <?php if ($c['status'] === 'APROBADO'): ?>
                                        <span class="badge bg-success bg-opacity-15 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill small">
                                            <i class="bi bi-check-circle-fill me-1"></i> Culminado & Aprobado
                                        </span>
                                    <?php elseif ($c['status'] === 'RECHAZADO'): ?>
                                        <span class="badge bg-danger bg-opacity-15 text-danger border border-danger border-opacity-25 px-2 py-1 rounded-pill small">
                                            <i class="bi bi-x-circle-fill me-1"></i> Culminado & Negado
                                        </span>
                                    <?php elseif ($c['status'] === 'EN_REVISION'): ?>
                                        <span class="badge bg-warning bg-opacity-15 text-warning-emphasis border border-warning border-opacity-25 px-2 py-1 rounded-pill small">
                                            <i class="bi bi-hourglass-split me-1"></i> En Revisión del Jefe
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary bg-opacity-15 text-secondary border border-secondary border-opacity-25 px-2 py-1 rounded-pill small">
                                            <i class="bi bi-file-earmark me-1"></i> Borrador Activo
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Resolución y Observaciones del Jefe -->
                                <td style="max-width: 280px;">
                                    <?php if ($c['status'] === 'APROBADO'): ?>
                                        <div class="text-success small fw-semibold">
                                            <i class="bi bi-patch-check-fill me-1"></i> Aprobado formalmente
                                        </div>
                                        <?php if (!empty($c['feedback_notes'])): ?>
                                            <div class="text-muted small fst-italic text-truncate" title="<?= htmlspecialchars($c['feedback_notes']) ?>" style="font-size: 0.74rem;">
                                                "<?= htmlspecialchars($c['feedback_notes']) ?>"
                                            </div>
                                        <?php endif; ?>
                                        <div class="text-muted" style="font-size: 0.68rem;">
                                            Por: <?= htmlspecialchars($c['reviewer_name'] ?? 'Dirección General') ?>
                                        </div>

                                    <?php elseif ($c['status'] === 'RECHAZADO'): ?>
                                        <div class="p-2 rounded bg-danger bg-opacity-10 border border-danger border-opacity-25">
                                            <div class="text-danger small fw-bold">
                                                <i class="bi bi-exclamation-triangle-fill me-1"></i> Observaciones de Dirección:
                                            </div>
                                            <div class="text-dark small fst-italic" style="font-size: 0.74rem;">
                                                "<?= htmlspecialchars($c['feedback_notes'] ?? 'Requiere ajuste de actividades o condiciones antes de aprobar.') ?>"
                                            </div>
                                        </div>

                                    <?php elseif ($c['status'] === 'EN_REVISION'): ?>
                                        <span class="text-muted small">
                                            <i class="bi bi-clock-history me-1"></i> Esperando decisión de Dirección General.
                                        </span>

                                    <?php else: ?>
                                        <span class="text-muted small">
                                            <i class="bi bi-pencil me-1"></i> En edición. Pendiente de envío.
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Acciones -->
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-1">
                                        <?php if ($c['status'] === 'APROBADO'): ?>
                                            <a href="<?= url('/contracts/view?id=' . $c['id']) ?>" target="_blank" class="btn btn-sm btn-outline-success" title="Ver / Descargar Contrato PDF">
                                                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Ver PDF
                                            </a>
                                        <?php elseif ($c['status'] === 'RECHAZADO'): ?>
                                            <a href="<?= url('/contracts/create?edit_id=' . $c['id']) ?>" class="btn btn-sm btn-outline-danger" title="Editar y corregir borrador negado">
                                                <i class="bi bi-pencil-square me-1"></i> Subsanar
                                            </a>
                                            <a href="<?= url('/contracts/view?id=' . $c['id']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary" title="Ver borrador">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        <?php elseif ($c['status'] === 'BORRADOR'): ?>
                                            <form action="<?= url('/contracts/submit-existing') ?>" method="POST" class="d-inline" onsubmit="return confirm('¿Enviar este borrador a revisión del Jefe?')">
                                                <input type="hidden" name="contract_id" value="<?= $c['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-success" title="Enviar a Revisión">
                                                    <i class="bi bi-send-fill me-1"></i> Enviar
                                                </button>
                                            </form>
                                            <a href="<?= url('/contracts/create?edit_id=' . $c['id']) ?>" class="btn btn-sm btn-outline-primary" title="Editar borrador">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= url('/contracts/view?id=' . $c['id']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary" title="Ver documento en revisión">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php if (\Config\Auth::isJefe()): ?>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalDeleteContractAct<?= $c['id'] ?>" title="Eliminar Contrato (Exclusivo Jefe)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Modales de Eliminación de Contrato para el Jefe -->
            <?php if (\Config\Auth::isJefe()): ?>
                <?php foreach ($contracts as $c): ?>
                    <div class="modal fade" id="modalDeleteContractAct<?= $c['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form action="<?= url('/contracts/delete') ?>" method="POST">
                                    <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Eliminar Contrato <?= htmlspecialchars($c['contract_code']) ?></h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4 text-start">
                                        <p class="mb-2">¿Está seguro de eliminar permanentemente el contrato con <strong><?= htmlspecialchars($c['company_name']) ?></strong>?</p>
                                        <div class="alert alert-warning small mb-0">
                                            <i class="bi bi-info-circle me-1"></i> Estado: <strong><?= htmlspecialchars($c['status']) ?></strong>. Esta acción eliminará el expediente y desvinculará sus registros asociados en el sistema.
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-danger fw-semibold"><i class="bi bi-trash-fill me-1"></i> Eliminar Definitivamente</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
// Filtrado instantáneo por pestaña
function filterContracts(status, btn) {
    document.querySelectorAll('#contractFilterPills .nav-link').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    const rows = document.querySelectorAll('.contract-row');
    rows.forEach(row => {
        const rowStatus = row.getAttribute('data-status');
        if (status === 'ALL' || rowStatus === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Búsqueda en vivo
function searchContracts() {
    const term = document.getElementById('contractSearchInput').value.toLowerCase().trim();
    const rows = document.querySelectorAll('.contract-row');
    rows.forEach(row => {
        const searchText = row.getAttribute('data-search') || '';
        if (searchText.includes(term)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
