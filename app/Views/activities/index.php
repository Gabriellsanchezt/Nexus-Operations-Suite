<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h1 class="module-title">
            <?php if ($activeFilter === 'APROBADA'): ?>
                <i class="bi bi-patch-check-fill text-success"></i> Producción Concluida
            <?php else: ?>
                <i class="bi bi-kanban text-primary"></i> Producción Audiovisual
            <?php endif; ?>
        </h1>
        <p class="module-subtitle">
            <?php if ($activeFilter === 'APROBADA'): ?>
                Historial de actividades concluidas y entregables aprobados
            <?php else: ?>
                Supervisión de rodajes, canales y entregables creativos
            <?php endif; ?>
        </p>
    </div>
    <button type="button" class="btn btn-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#modalNewActivity">
        <i class="bi bi-plus-lg me-1"></i> Asignar Nueva Tarea
    </button>
</div>

<!-- Filtros Rápidos por Estado -->
<div class="d-flex flex-wrap gap-2 mb-4">
    <a href="<?= url('/activities') ?>" class="btn btn-sm <?= empty($activeFilter) ? 'btn-dark' : 'btn-outline-dark' ?>">
        Todas (<?= $counts['total'] ?>)
    </a>
    <a href="<?= url('/activities?status=NUEVA') ?>" class="btn btn-sm <?= ($activeFilter === 'NUEVA') ? 'btn-primary' : 'btn-outline-primary' ?>">
        Nuevas (<?= $counts['nuevas'] ?>)
    </a>
    <a href="<?= url('/activities?status=EN_PROCESO') ?>" class="btn btn-sm <?= ($activeFilter === 'EN_PROCESO') ? 'btn-info' : 'btn-outline-info' ?>">
        En Proceso (<?= $counts['canalizadas'] ?>)
    </a>
    <a href="<?= url('/activities?status=ENTREGADA_REVISION') ?>" class="btn btn-sm <?= ($activeFilter === 'ENTREGADA_REVISION') ? 'btn-warning' : 'btn-outline-warning' ?>">
        Por Aprobar (<?= $counts['por_aprobar'] ?>)
    </a>
    <a href="<?= url('/activities?status=CORRECCION') ?>" class="btn btn-sm <?= ($activeFilter === 'CORRECCION') ? 'btn-danger' : 'btn-outline-danger' ?>">
        En Corrección (<?= $counts['correccion'] ?>)
    </a>
    <a href="<?= url('/activities?status=APROBADA') ?>" class="btn btn-sm <?= ($activeFilter === 'APROBADA') ? 'btn-success' : 'btn-outline-success' ?>">
        <i class="bi bi-patch-check-fill me-1"></i> Culminadas & Aprobadas (<?= $counts['culminadas'] ?>)
    </a>
</div>

<!-- Tabla del Tablero Audiovisual -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($activities)): ?>
            <div class="text-center py-5 text-muted">
                <?php if ($activeFilter === 'APROBADA'): ?>
                    <i class="bi bi-patch-check display-5 d-block mb-2 text-success"></i>
                    No hay actividades culminadas y aprobadas bajo este filtro.
                <?php else: ?>
                    <i class="bi bi-camera-reels display-5 d-block mb-2"></i>
                    No se encontraron actividades con el filtro seleccionado.
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th>Prioridad</th>
                            <th>Título de la Tarea</th>
                            <th>Cliente Realtor</th>
                            <th>Especialista Asignado</th>
                            <th>Fecha Límite</th>
                            <th>Estado Actual</th>
                            <th>Entregables</th>
                            <th class="text-end">Acción de Supervisión</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($activities as $act): ?>
                            <?php $isAprob = ($act['current_status'] === 'APROBADA'); ?>
                            <tr class="<?= $isAprob ? 'bg-success bg-opacity-10' : '' ?>">
                                <td>
                                    <?php
                                        $prioBadge = match($act['priority']) {
                                            'URGENTE' => 'bg-danger',
                                            'ALTA' => 'bg-warning text-dark',
                                            default => 'bg-secondary'
                                        };
                                    ?>
                                    <span class="badge <?= $prioBadge ?>"><?= htmlspecialchars($act['priority']) ?></span>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($act['title']) ?></strong>
                                    <div class="small text-muted">
                                        <i class="bi bi-tag me-1"></i><?= htmlspecialchars($act['activity_type']) ?>
                                        <?php if (!empty($act['contract_code'])): ?>
                                            &bull; <span class="badge bg-light text-dark border font-monospace"><?= htmlspecialchars($act['contract_code']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold"><?= htmlspecialchars($act['company_name']) ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($act['miami_zone']) ?></small>
                                    <?php if (!empty($act['dropbox_url'])): ?>
                                        <div class="mt-1">
                                            <a href="<?= htmlspecialchars($act['dropbox_url']) ?>" target="_blank" rel="noopener noreferrer" class="badge bg-primary-subtle text-primary border border-primary-subtle text-decoration-none">
                                                <i class="bi bi-dropbox me-1"></i> Dropbox
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-person me-1"></i><?= htmlspecialchars($act['employee_name']) ?>
                                    </span>
                                </td>
                                <td>
                                    <small><?= htmlspecialchars($act['scheduled_date']) ?></small>
                                </td>
                                <td>
                                    <?php if ($isAprob): ?>
                                        <span class="badge bg-success px-2 py-1">
                                            <i class="bi bi-patch-check-fill me-1"></i> CULMINADA
                                        </span>
                                    <?php elseif ($act['current_status'] === 'CORRECCION'): ?>
                                        <span class="badge bg-danger px-2 py-1">
                                            <i class="bi bi-exclamation-octagon-fill me-1"></i> CORRECCIÓN
                                        </span>
                                    <?php elseif ($act['current_status'] === 'ENTREGADA_REVISION'): ?>
                                        <span class="badge bg-warning text-dark px-2 py-1">
                                            <i class="bi bi-hourglass-split me-1"></i> POR REVISAR
                                        </span>
                                    <?php elseif ($act['current_status'] === 'EN_PROCESO'): ?>
                                        <span class="badge bg-info text-dark px-2 py-1">
                                            <i class="bi bi-arrow-repeat me-1"></i> EN PROCESO
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-primary px-2 py-1">NUEVA</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($act['deliverables_count'] > 0): ?>
                                        <span class="badge bg-dark"><i class="bi bi-link-45deg me-1"></i><?= $act['deliverables_count'] ?> subidos</span>
                                        <?php if ($isAprob): ?>
                                            <span class="badge bg-success bg-opacity-25 text-success border border-success ms-1 small"><i class="bi bi-check-all"></i></span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <small class="text-muted">Ninguno</small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group shadow-sm">
                                        <button type="button" class="btn btn-sm <?= $isAprob ? 'btn-outline-success fw-semibold' : 'btn-outline-dark' ?>" onclick="inspectActivity(<?= $act['id'] ?>)" title="Inspeccionar / Evaluar">
                                            <i class="bi <?= $isAprob ? 'bi-patch-check-fill text-success' : 'bi-eye' ?> me-1"></i>
                                            <?= $isAprob ? 'Ver' : 'Inspeccionar' ?>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditActivity<?= $act['id'] ?>" title="Modificar Actividad">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <?php if (\Config\Auth::isJefe()): ?>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalDeleteActivity<?= $act['id'] ?>" title="Eliminar Actividad (Exclusivo Jefe)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>

                            <!-- MODAL MODIFICAR ACTIVIDAD -->
                            <div class="modal fade" id="modalEditActivity<?= $act['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <form action="<?= url('/activities/update') ?>" method="POST">
                                            <input type="hidden" name="id" value="<?= $act['id'] ?>">

                                            <div class="modal-header bg-dark text-white">
                                                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Modificar Tarea Audiovisual</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4 text-start">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Título de la Tarea *</label>
                                                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($act['title']) ?>" required>
                                                </div>

                                                <div class="row g-3 mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold">Cliente Realtor *</label>
                                                        <select name="realtor_id" class="form-select" required>
                                                            <?php foreach ($realtors as $r): ?>
                                                                <option value="<?= $r['id'] ?>" <?= ((int)$act['realtor_id'] === (int)$r['id']) ? 'selected' : '' ?>>
                                                                    <?= htmlspecialchars($r['company_name']) ?> (<?= htmlspecialchars($r['miami_zone']) ?>)
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold">Empleado Asignado *</label>
                                                        <select name="employee_id" class="form-select" required>
                                                            <?php foreach ($employees as $emp): ?>
                                                                <?php if ($emp['role_name'] === 'EMPLEADO'): ?>
                                                                    <option value="<?= $emp['employee_id'] ?>" <?= ((int)$act['employee_id'] === (int)$emp['employee_id']) ? 'selected' : '' ?>>
                                                                        <?= htmlspecialchars($emp['full_name']) ?> (<?= implode(', ', array_column($emp['specialties'], 'code')) ?>)
                                                                    </option>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row g-3 mb-3">
                                                    <div class="col-md-4">
                                                        <label class="form-label small fw-semibold">Tipo de Actividad *</label>
                                                        <select name="activity_type" class="form-select" required>
                                                            <option value="EDICION_VIDEO" <?= ($act['activity_type'] === 'EDICION_VIDEO') ? 'selected' : '' ?>>Edición de Video</option>
                                                            <option value="CARRUSEL_FOTO" <?= ($act['activity_type'] === 'CARRUSEL_FOTO') ? 'selected' : '' ?>>Diseño de Carrusel / Fotos</option>
                                                            <option value="RODAJE_CAMARA" <?= ($act['activity_type'] === 'RODAJE_CAMARA') ? 'selected' : '' ?>>Rodaje en Locación / Dron</option>
                                                            <option value="COMMUNITY_MANAGEMENT" <?= ($act['activity_type'] === 'COMMUNITY_MANAGEMENT') ? 'selected' : '' ?>>Community Management / Redes</option>
                                                            <option value="OTRO" <?= ($act['activity_type'] === 'OTRO') ? 'selected' : '' ?>>Otro Servicio</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label small fw-semibold">Prioridad *</label>
                                                        <select name="priority" class="form-select" required>
                                                            <option value="BAJA" <?= ($act['priority'] === 'BAJA') ? 'selected' : '' ?>>Baja</option>
                                                            <option value="MEDIA" <?= ($act['priority'] === 'MEDIA') ? 'selected' : '' ?>>Media</option>
                                                            <option value="ALTA" <?= ($act['priority'] === 'ALTA') ? 'selected' : '' ?>>Alta</option>
                                                            <option value="URGENTE" <?= ($act['priority'] === 'URGENTE') ? 'selected' : '' ?>>Urgente (Entrega 24h)</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label small fw-semibold">Fecha Límite *</label>
                                                        <input type="date" name="scheduled_date" class="form-control" value="<?= htmlspecialchars($act['scheduled_date']) ?>" required>
                                                    </div>
                                                </div>

                                                <div class="mb-0">
                                                    <label class="form-label small fw-semibold">Instrucciones y Brief Creativo</label>
                                                    <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($act['description'] ?? '') ?></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary fw-semibold"><i class="bi bi-save me-1"></i> Guardar Modificaciones</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- MODAL ELIMINAR ACTIVIDAD (EXCLUSIVO JEFE) -->
                            <?php if (\Config\Auth::isJefe()): ?>
                                <div class="modal fade" id="modalDeleteActivity<?= $act['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <form action="<?= url('/activities/delete') ?>" method="POST">
                                                <input type="hidden" name="id" value="<?= $act['id'] ?>">

                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Eliminar Actividad #<?= $act['id'] ?></h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4 text-start">
                                                    <p class="mb-2">¿Está seguro de eliminar permanentemente la tarea <strong><?= htmlspecialchars($act['title']) ?></strong>?</p>
                                                    <div class="alert alert-warning small mb-0">
                                                        <i class="bi bi-info-circle me-1"></i> Se eliminarán en cascada todos los entregables subidos y revisiones de Dirección asociadas a esta tarea.
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
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- MODAL ASIGNAR NUEVA TAREA -->
<div class="modal fade" id="modalNewActivity" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= url('/activities/store') ?>" method="POST">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Asignar Nueva Tarea Audiovisual</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Título de la Tarea *</label>
                        <input type="text" name="title" class="form-control" required placeholder="Ej: Rodaje Dron Mansión Sunny Isles o Edición Reel Walkthrough Brickell">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Cliente Realtor *</label>
                            <select name="realtor_id" class="form-select" required>
                                <option value="">-- Seleccionar Realtor --</option>
                                <?php foreach ($realtors as $r): ?>
                                    <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['company_name']) ?> (<?= htmlspecialchars($r['miami_zone']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Empleado Creativo Asignado *</label>
                            <select name="employee_id" class="form-select" required>
                                <option value="">-- Seleccionar Empleado --</option>
                                <?php foreach ($employees as $emp): ?>
                                    <?php if ($emp['role_name'] === 'EMPLEADO'): ?>
                                        <option value="<?= $emp['employee_id'] ?>">
                                            <?= htmlspecialchars($emp['full_name']) ?>
                                            (<?= implode(', ', array_column($emp['specialties'], 'code')) ?>)
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Tipo de Actividad *</label>
                            <select name="activity_type" class="form-select" required>
                                <option value="EDICION_VIDEO">Edición de Video</option>
                                <option value="CARRUSEL_FOTO">Diseño de Carrusel / Fotos</option>
                                <option value="RODAJE_CAMARA">Rodaje en Locación / Dron</option>
                                <option value="COMMUNITY_MANAGEMENT">Community Management / Redes</option>
                                <option value="OTRO">Otro Servicio</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Prioridad *</label>
                            <select name="priority" class="form-select" required>
                                <option value="BAJA">Baja</option>
                                <option value="MEDIA" selected>Media</option>
                                <option value="ALTA">Alta</option>
                                <option value="URGENTE">Urgente (Entrega 24h)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Fecha de Entrega Programada *</label>
                            <input type="date" name="scheduled_date" class="form-control" value="<?= date('Y-m-d', strtotime('+3 days')) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Vincular a Contrato Aprobado (Opcional)</label>
                        <select name="contract_id" class="form-select">
                            <option value="">-- Sin contrato vinculado --</option>
                            <?php foreach ($contracts as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['contract_code']) ?> - <?= htmlspecialchars($c['company_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-semibold">Instrucciones y Brief Creativo</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Detalles de edición, enlaces de audio de referencia, horarios de rodaje..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-semibold">Crear y Canalizar Tarea</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EVALUAR ENTREGABLES & DETALLES -->
<div class="modal fade" id="modalInspectActivity" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold" id="inspectTitle"><i class="bi bi-camera-video me-2"></i>Inspección de Tarea</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="inspectBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function inspectActivity(id) {
    const modal = new bootstrap.Modal(document.getElementById('modalInspectActivity'));
    modal.show();

    fetch('<?= url('/activities/details-ajax') ?>?id=' + id)
    .then(r => r.json())
    .then(data => {
        if (!data.success) {
            document.getElementById('inspectBody').innerHTML = '<div class="alert alert-danger">Error al cargar actividad</div>';
            return;
        }

        const act = data.activity;
        document.getElementById('inspectTitle').innerHTML = '<i class="bi bi-camera-video me-2"></i>' + escapeHtml(act.title);

        let deliverablesHtml = '';
        if (act.deliverables && act.deliverables.length > 0) {
            deliverablesHtml = '<h6 class="fw-bold mt-4 mb-2"><i class="bi bi-folder-fill text-primary me-1"></i> Entregables Subidos por el Creativo:</h6>';
            act.deliverables.forEach(deliv => {
                const isApproved = (deliv.review_status === 'APROBADO');
                const isCorreccion = (deliv.review_status === 'REQUIERE_CAMBIOS');

                let reviewBadgeHtml = '';
                if (isApproved) {
                    reviewBadgeHtml = '<span class="badge bg-success"><i class="bi bi-patch-check-fill me-1"></i> APROBADO</span>';
                } else if (isCorreccion) {
                    reviewBadgeHtml = '<span class="badge bg-danger"><i class="bi bi-exclamation-octagon-fill me-1"></i> REQUIERE CAMBIOS</span>';
                } else {
                    reviewBadgeHtml = '<span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i> PENDIENTE REVISIÓN</span>';
                }

                let approvalBannerHtml = '';
                if (isApproved) {
                    approvalBannerHtml = `
                        <div class="alert alert-success bg-success bg-opacity-10 border border-success p-3 rounded mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap gap-1">
                                <strong class="text-success"><i class="bi bi-award-fill me-1"></i> ¡Entregable Calificado como Aprobado y Culminado!</strong>
                                <small class="text-muted"><i class="bi bi-calendar-check me-1"></i> ${deliv.reviewed_at || ''}</small>
                            </div>
                            <div class="small text-dark mb-2"><strong>Notas / Felicitaciones del Supervisor:</strong> "${escapeHtml(deliv.review_notes || 'Sin observaciones')}"</div>
                            <div class="pt-2 border-top border-success border-opacity-25">
                                <a href="${escapeHtml(deliv.deliverable_url)}" target="_blank" class="btn btn-sm btn-success fw-semibold">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Abrir Archivo Final Aprobado (${escapeHtml(deliv.deliverable_type)})
                                </a>
                            </div>
                        </div>
                    `;
                }

                deliverablesHtml += `
                    <div class="p-3 bg-light rounded border mb-3 ${isApproved ? 'border-success' : ''}">
                        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-1">
                            <div>
                                <span class="badge bg-primary me-1">${escapeHtml(deliv.deliverable_type)}</span>
                                ${reviewBadgeHtml}
                            </div>
                            <small class="text-muted"><i class="bi bi-clock me-1"></i> Subido: ${escapeHtml(deliv.submitted_at)}</small>
                        </div>
                        <div class="mb-2">
                            <strong>URL del Entregable:</strong>
                            <a href="${escapeHtml(deliv.deliverable_url)}" target="_blank" class="fw-bold text-decoration-none d-inline-flex align-items-center gap-1 ms-1">
                                <i class="bi bi-box-arrow-up-right"></i> ${escapeHtml(deliv.deliverable_url)}
                            </a>
                        </div>
                        <div class="small text-muted mb-2"><strong>Notas del Creativo:</strong> ${escapeHtml(deliv.notes || 'Sin notas adicionales')}</div>

                        ${approvalBannerHtml}

                        <!-- Formulario de Evaluación para el Jefe -->
                        <form action="<?= url('/activities/review-deliverable') ?>" method="POST" class="mt-3 p-3 bg-white rounded border">
                            <input type="hidden" name="deliverable_id" value="${deliv.id}">
                            <input type="hidden" name="activity_id" value="${act.id}">

                            <label class="form-label small fw-semibold text-dark">
                                ${isApproved ? '<i class="bi bi-pencil-square me-1"></i> Actualizar Evaluación de Dirección:' : '<i class="bi bi-shield-check me-1"></i> Calificación y Dictamen de Dirección:'}
                            </label>
                            <div class="row g-2 align-items-center">
                                <div class="col-md-4">
                                    <select name="review_status" class="form-select form-select-sm" required>
                                        <option value="APROBADO" ${isApproved ? 'selected' : ''}>Aprobar Entregable (Culminar Tarea)</option>
                                        <option value="REQUIERE_CAMBIOS" ${isCorreccion ? 'selected' : ''}>Solicitar Correcciones al Creativo</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <input type="text" name="review_notes" class="form-control form-control-sm" required placeholder="Observaciones / Feedback directo al creativo..." value="${escapeHtml(deliv.review_notes || '')}">
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-sm ${isApproved ? 'btn-outline-success' : 'btn-dark'} w-100 fw-semibold">
                                        <i class="bi bi-save me-1"></i> Guardar Dictamen
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                `;
            });
        } else {
            deliverablesHtml = '<div class="alert alert-light border mt-3 text-muted"><i class="bi bi-info-circle me-1"></i> Aún no se han subido links de entregables para esta tarea.</div>';
        }

        let statusClass = 'bg-secondary';
        if (act.current_status === 'APROBADA') statusClass = 'bg-success';
        else if (act.current_status === 'CORRECCION') statusClass = 'bg-danger';
        else if (act.current_status === 'ENTREGADA_REVISION') statusClass = 'bg-warning text-dark';
        else if (act.current_status === 'EN_PROCESO') statusClass = 'bg-info text-dark';
        else if (act.current_status === 'NUEVA') statusClass = 'bg-primary';

        document.getElementById('inspectBody').innerHTML = `
            <div class="row g-2 mb-3 small">
                <div class="col-md-6"><strong>Cliente Realtor:</strong> ${escapeHtml(act.company_name)} (${escapeHtml(act.miami_zone)})</div>
                <div class="col-md-6"><strong>Especialista:</strong> ${escapeHtml(act.employee_name)} ${act.employee_phone ? '(' + escapeHtml(act.employee_phone) + ')' : ''}</div>
                <div class="col-md-6"><strong>Prioridad:</strong> <span class="badge bg-secondary">${escapeHtml(act.priority)}</span></div>
                <div class="col-md-6"><strong>Estado Actual:</strong> <span class="badge ${statusClass}">${escapeHtml(act.current_status)}</span></div>
                ${act.contract_code ? `<div class="col-12"><strong>Contrato Vinculado:</strong> <span class="badge bg-dark">${escapeHtml(act.contract_code)}</span></div>` : ''}
            </div>
            <div class="p-3 bg-light rounded border mb-2">
                <strong class="small text-muted">Descripción / Brief Creativo:</strong><br>
                <div class="mt-1 small">${escapeHtml(act.description || 'Sin instrucciones adicionales')}</div>
            </div>
            ${deliverablesHtml}
        `;
    });
}

function escapeHtml(text) {
    if (!text) return '';
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
</script>
