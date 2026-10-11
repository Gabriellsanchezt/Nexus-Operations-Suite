<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-warning bg-opacity-10 text-warning-emphasis border border-warning border-opacity-25 rounded-pill px-3 py-1 fw-bold small">
                <i class="bi bi-shield-check me-1"></i> Control de Calidad Jurídica & Operaciones
            </span>
            <span class="text-muted small">&bull; Dirección General</span>
        </div>
        <h4 class="fw-bold mb-1 text-dark">
            <i class="bi bi-file-earmark-check-fill text-warning me-2"></i>Módulo de Aprobación de Contratos
        </h4>
        <p class="text-muted small mb-0">
            Revisión de expedientes remitidos por redactores autorizados. Apruebe y archive en el repositorio oficial o devuelva con observaciones.
        </p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <a href="<?= url('/contracts/create') ?>" class="btn btn-outline-dark btn-sm fw-semibold shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Redactar Contrato
        </a>
        <a href="<?= url('/contracts/final') ?>" class="btn btn-outline-success btn-sm fw-semibold shadow-sm">
            <i class="bi bi-archive-fill me-1"></i> Repositorio Final Aprobado
        </a>
    </div>
</div>

<!-- KPI Mini-bar de Aprobaciones -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm border-start border-warning border-4 bg-white">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Pendientes de Revisión</div>
                        <h3 class="fw-bold mb-0 text-dark"><?= count($pending) ?></h3>
                    </div>
                    <div class="p-2 bg-warning bg-opacity-10 text-warning-emphasis rounded-circle fs-4 lh-1">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm border-start border-primary border-4 bg-white">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Realtors Solicitantes</div>
                        <h3 class="fw-bold mb-0 text-primary">
                            <?= count(array_unique(array_column($pending, 'realtor_id'))) ?>
                        </h3>
                    </div>
                    <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-circle fs-4 lh-1">
                        <i class="bi bi-building"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm border-start border-success border-4 bg-white">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Destino al Aprobar</div>
                        <div class="fw-bold text-success mt-1 small">
                            <i class="bi bi-shield-lock-fill me-1"></i>Repositorio de Contratos Finales
                        </div>
                    </div>
                    <div class="p-2 bg-success bg-opacity-10 text-success rounded-circle fs-4 lh-1">
                        <i class="bi bi-folder-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <h5 class="fw-bold mb-0 text-dark">Borradores en Estado "EN_REVISION"</h5>
            <span class="badge bg-warning text-dark rounded-pill fw-bold"><?= count($pending) ?> pendientes</span>
        </div>
        <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Requiere validación directa del Director General</small>
    </div>
    <div class="card-body p-0">
        <?php if (empty($pending)): ?>
            <div class="text-center py-5">
                <div class="p-3 bg-light text-muted mx-auto mb-3 rounded-circle d-inline-flex">
                    <i class="bi bi-check2-circle fs-1 text-success"></i>
                </div>
                <h5 class="fw-bold text-dark">¡Bandeja de Aprobaciones al día!</h5>
                <p class="text-muted small mb-3">No hay contratos esperando revisión ejecutiva en este momento.</p>
                <a href="<?= url('/contracts/final') ?>" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-archive-fill me-1"></i> Ir al Repositorio de Contratos Finales
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th style="width: 140px;">Código</th>
                            <th>Realtor / Inmobiliaria</th>
                            <th>Zona Miami</th>
                            <th>Tipo de Acuerdo</th>
                            <th>Vigencia</th>
                            <th>Redactor</th>
                            <th class="text-end" style="min-width: 250px;">Decisión de Dirección</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pending as $p): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-warning bg-opacity-25 text-warning-emphasis font-monospace px-2 py-1 border border-warning border-opacity-50">
                                        <?= htmlspecialchars($p['contract_code']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-dark text-white fw-bold d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                            <?= strtoupper(substr($p['company_name'], 0, 2)) ?>
                                        </div>
                                        <div>
                                            <strong class="text-dark d-block"><?= htmlspecialchars($p['company_name']) ?></strong>
                                            <small class="text-muted"><i class="bi bi-person me-1"></i><?= htmlspecialchars($p['contact_person']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1"><?= htmlspecialchars($p['miami_zone']) ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1">
                                        <i class="bi bi-camera-reels me-1 text-primary"></i>Producción Mensual
                                    </span>
                                    <div class="text-muted small" style="font-size: 0.72rem;">Acuerdo Operativo</div>
                                </td>
                                <td>
                                    <small class="d-block text-dark fw-semibold"><?= htmlspecialchars($p['start_date']) ?></small>
                                    <small class="text-muted" style="font-size: 0.75rem;">hasta <?= htmlspecialchars($p['end_date']) ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-secondary border">
                                        <i class="bi bi-person-fill me-1"></i><?= htmlspecialchars($p['generator_employee_name']) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group shadow-sm">
                                        <a href="<?= url('/contracts/view?id=' . $p['id']) ?>" target="_blank" class="btn btn-sm btn-outline-dark" title="Inspeccionar documento completo">
                                            <i class="bi bi-eye-fill"></i> Ver
                                        </a>
                                        <button type="button" class="btn btn-sm btn-success fw-bold" data-bs-toggle="modal" data-bs-target="#modalApprove<?= $p['id'] ?>">
                                            <i class="bi bi-check-lg"></i> Aprobar
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalReject<?= $p['id'] ?>">
                                            <i class="bi bi-arrow-return-left"></i> Devolver
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- MODAL APROBAR Y ARCHIVAR -->
                            <div class="modal fade" id="modalApprove<?= $p['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <form action="<?= url('/contracts/review') ?>" method="POST">
                                            <input type="hidden" name="contract_id" value="<?= $p['id'] ?>">
                                            <input type="hidden" name="action" value="APROBAR">

                                            <div class="modal-header bg-success text-white py-3">
                                                <h5 class="modal-title fw-bold fs-6"><i class="bi bi-shield-check me-2"></i>Aprobar y Archivar en Repositorio Final</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <p class="mb-3 text-secondary">
                                                    ¿Confirma la aprobación formal del contrato <strong><?= htmlspecialchars($p['contract_code']) ?></strong> para el cliente <strong><?= htmlspecialchars($p['company_name']) ?></strong>?
                                                </p>
                                                <div class="p-3 bg-light rounded border mb-3">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span class="text-muted small">Modalidad de Servicio:</span>
                                                        <strong class="text-dark">Producción Audiovisual Mensual</strong>
                                                    </div>
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span class="text-muted small">Zona Territorial:</span>
                                                        <strong><?= htmlspecialchars($p['miami_zone']) ?></strong>
                                                    </div>
                                                    <div class="d-flex justify-content-between">
                                                        <span class="text-muted small">Redactor:</span>
                                                        <span><?= htmlspecialchars($p['generator_employee_name']) ?></span>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-dark">Notas de Aprobación (Opcional):</label>
                                                    <textarea name="feedback_notes" class="form-control" rows="2" placeholder="Ej: Aprobado conforme al plan operativo. Proceder con la canalización de rodajes."></textarea>
                                                </div>
                                                <div class="alert alert-info py-2 px-3 small mb-0 d-flex align-items-center gap-2">
                                                    <i class="bi bi-info-circle-fill fs-5"></i>
                                                    <div>El contrato pasará inmediatamente al <strong>Repositorio de Contratos Finales</strong> con estado <strong>APROBADO</strong>.</div>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light py-2">
                                                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-success btn-sm fw-bold px-3">
                                                    <i class="bi bi-check2-circle me-1"></i> Confirmar y Aprobar
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- MODAL DEVOLVER CON OBSERVACIONES -->
                            <div class="modal fade" id="modalReject<?= $p['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <form action="<?= url('/contracts/review') ?>" method="POST">
                                            <input type="hidden" name="contract_id" value="<?= $p['id'] ?>">
                                            <input type="hidden" name="action" value="RECHAZAR">

                                            <div class="modal-header bg-danger text-white py-3">
                                                <h5 class="modal-title fw-bold fs-6"><i class="bi bi-exclamation-octagon me-2"></i>Devolver con Observaciones</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <p class="mb-2 text-secondary">
                                                    Indique las observaciones o rectificaciones que debe realizar el redactor (<strong><?= htmlspecialchars($p['generator_employee_name']) ?></strong>):
                                                </p>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-danger">Observaciones de Corrección *:</label>
                                                    <textarea name="feedback_notes" class="form-control" rows="3" required placeholder="Ej: Ajustar cláusula de entregas en Brickell y revisar las actividades programadas con el broker."></textarea>
                                                </div>
                                                <div class="alert alert-warning py-2 px-3 small mb-0">
                                                    <i class="bi bi-arrow-counterclockwise me-1"></i> El contrato volverá a la bandeja de borradores del redactor para ser editado y reenviado.
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light py-2">
                                                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-danger btn-sm fw-bold px-3">
                                                    <i class="bi bi-arrow-return-left me-1"></i> Devolver para Corrección
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
