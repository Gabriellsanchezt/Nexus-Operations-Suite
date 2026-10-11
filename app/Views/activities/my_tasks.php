<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold mb-1">
            <i class="bi bi-play-circle-fill text-primary me-2"></i>
            <?= ($activeFilter === 'APROBADA') ? 'Mis Tareas Audiovisuales Culminadas & Aprobadas' : 'Mis Tareas Audiovisuales' ?>
        </h2>
        <p class="text-muted mb-0">Gestión de entregables creativos, subida de links (Frame.io/Drive) y supervisión de aprobaciones del Jefe</p>
    </div>
</div>

<!-- Filtros de Estado en Pestañas / Pills -->
<div class="d-flex flex-wrap gap-2 mb-4">
    <a href="<?= url('/activities/my-tasks') ?>" class="btn btn-sm <?= empty($activeFilter) ? 'btn-dark' : 'btn-outline-dark' ?>">
        Todas (<?= $counts['total'] ?? count($tasks) ?>)
    </a>
    <a href="<?= url('/activities/my-tasks?status=EN_PROCESO') ?>" class="btn btn-sm <?= ($activeFilter === 'EN_PROCESO') ? 'btn-info' : 'btn-outline-info' ?>">
        En Producción (<?= $counts['canalizadas'] ?? 0 ?>)
    </a>
    <a href="<?= url('/activities/my-tasks?status=ENTREGADA_REVISION') ?>" class="btn btn-sm <?= ($activeFilter === 'ENTREGADA_REVISION') ? 'btn-warning' : 'btn-outline-warning' ?>">
        Por Aprobar (<?= $counts['por_aprobar'] ?? 0 ?>)
    </a>
    <a href="<?= url('/activities/my-tasks?status=CORRECCION') ?>" class="btn btn-sm <?= ($activeFilter === 'CORRECCION') ? 'btn-danger' : 'btn-outline-danger' ?>">
        Requiere Corrección (<?= $counts['correccion'] ?? 0 ?>)
    </a>
    <a href="<?= url('/activities/my-tasks?status=APROBADA') ?>" class="btn btn-sm <?= ($activeFilter === 'APROBADA') ? 'btn-success' : 'btn-outline-success' ?>">
        <i class="bi bi-patch-check-fill me-1"></i> Culminadas & Aprobadas (<?= $counts['culminadas'] ?? 0 ?>)
    </a>
</div>

<?php if (empty($tasks)): ?>
    <div class="card border-0 shadow-sm p-5 text-center">
        <?php if ($activeFilter === 'APROBADA'): ?>
            <i class="bi bi-patch-check display-4 text-muted mb-3"></i>
            <h4 class="fw-bold text-dark">No hay tareas culminadas y aprobadas todavía</h4>
            <p class="text-muted">Cuando tus entregables subidos a revisión sean calificados como "APROBADO" por Dirección General, quedarán archivados aquí con su sello de culminación.</p>
            <div class="mt-2">
                <a href="<?= url('/activities/my-tasks') ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Ver Todas Mis Tareas
                </a>
            </div>
        <?php else: ?>
            <i class="bi bi-check2-circle display-4 text-success mb-3"></i>
            <h4 class="fw-bold text-dark">No tienes tareas asignadas con este filtro</h4>
            <p class="text-muted">¡Excelente trabajo! Cuando la Dirección General canalice una nueva tarea a tu perfil, aparecerá listada en este panel.</p>
            <div class="mt-2">
                <a href="<?= url('/activities/my-tasks') ?>" class="btn btn-outline-dark btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Volver a Todas las Tareas
                </a>
            </div>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($tasks as $t): ?>
            <?php 
                $isAprobada = ($t['current_status'] === 'APROBADA');
                $isCorreccion = ($t['current_status'] === 'CORRECCION');
                $isRevision = ($t['current_status'] === 'ENTREGADA_REVISION');
                $isNueva = ($t['current_status'] === 'NUEVA');
                $isProceso = ($t['current_status'] === 'EN_PROCESO');

                $borderClass = match($t['current_status']) {
                    'APROBADA' => 'border-start border-success border-4',
                    'CORRECCION' => 'border-start border-danger border-4',
                    'ENTREGADA_REVISION' => 'border-start border-warning border-4',
                    default => ''
                };
            ?>
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100 <?= $borderClass ?>">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <span class="badge bg-secondary font-monospace"><?= htmlspecialchars($t['activity_type']) ?></span>
                        <div>
                            <?php if ($isAprobada): ?>
                                <span class="badge bg-success px-2 py-1">
                                    <i class="bi bi-patch-check-fill me-1"></i> CULMINADA & APROBADA
                                </span>
                            <?php elseif ($isCorreccion): ?>
                                <span class="badge bg-danger px-2 py-1">
                                    <i class="bi bi-exclamation-octagon-fill me-1"></i> REQUIERE CORRECCIÓN
                                </span>
                            <?php elseif ($isRevision): ?>
                                <span class="badge bg-warning text-dark px-2 py-1">
                                    <i class="bi bi-hourglass-split me-1"></i> EN REVISIÓN DEL JEFE
                                </span>
                            <?php elseif ($isProceso): ?>
                                <span class="badge bg-info text-dark px-2 py-1">
                                    <i class="bi bi-arrow-repeat me-1"></i> EN PRODUCCIÓN
                                </span>
                            <?php else: ?>
                                <span class="badge bg-primary px-2 py-1">
                                    <i class="bi bi-star-fill me-1"></i> NUEVA ASIGNADA
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-dark mb-2"><?= htmlspecialchars($t['title']) ?></h5>

                        <div class="mb-3 p-2 bg-light rounded small border">
                            <div class="row">
                                <div class="col-6">
                                    <span class="text-muted">Realtor:</span> <strong><?= htmlspecialchars($t['company_name']) ?></strong>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted">Zona:</span> <strong><?= htmlspecialchars($t['miami_zone']) ?></strong>
                                </div>
                                <div class="col-6 mt-1">
                                    <span class="text-muted">Prioridad:</span> <span class="badge bg-secondary"><?= htmlspecialchars($t['priority']) ?></span>
                                </div>
                                <div class="col-6 mt-1">
                                    <span class="text-muted">Fecha Límite:</span> <strong><?= htmlspecialchars($t['scheduled_date']) ?></strong>
                                </div>
                                <?php if (!empty($t['contract_code'])): ?>
                                    <div class="col-12 mt-1">
                                        <span class="text-muted">Contrato Vinculado:</span> <span class="badge bg-dark font-monospace"><?= htmlspecialchars($t['contract_code']) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if (!empty($t['description'])): ?>
                            <div class="mb-3 small">
                                <strong>Brief / Instrucciones:</strong>
                                <p class="text-secondary mb-0"><?= nl2br(htmlspecialchars($t['description'])) ?></p>
                            </div>
                        <?php endif; ?>

                        <!-- BANNER DESTACADO PARA TAREAS CULMINADAS Y APROBADAS -->
                        <?php if ($isAprobada): ?>
                            <div class="alert alert-success bg-success bg-opacity-10 border border-success p-3 rounded mb-3">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                    <div class="fw-bold text-success d-flex align-items-center gap-2">
                                        <i class="bi bi-award-fill fs-5"></i>
                                        <span>¡Tarea Culminada y Aprobada por Dirección!</span>
                                    </div>
                                    <span class="badge bg-success text-white small">
                                        <i class="bi bi-check-all me-1"></i> <?= htmlspecialchars($t['last_deliverable']['reviewed_at'] ?? 'Aprobado formalmente') ?>
                                    </span>
                                </div>
                                <?php if (!empty($t['last_deliverable']['review_notes'])): ?>
                                    <div class="small text-dark mb-2">
                                        <strong>Feedback / Felicitaciones de Dirección:</strong>
                                        <div class="fst-italic text-secondary mt-1">"<?= htmlspecialchars($t['last_deliverable']['review_notes']) ?>"</div>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($t['last_deliverable']['deliverable_url'])): ?>
                                    <div class="d-flex align-items-center gap-2 mt-2 pt-2 border-top border-success border-opacity-25 flex-wrap">
                                        <span class="small text-muted">Archivo / Video Final:</span>
                                        <a href="<?= htmlspecialchars($t['last_deliverable']['deliverable_url']) ?>" target="_blank" class="btn btn-sm btn-success fw-semibold">
                                            <i class="bi bi-box-arrow-up-right me-1"></i> Ver Video Aprobado (<?= htmlspecialchars($t['last_deliverable']['deliverable_type'] ?? 'FRAME_IO') ?>)
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>

                        <!-- ALERTA SI HAY CORRECCIONES SOLICITADAS POR EL JEFE -->
                        <?php elseif ($isCorreccion && !empty($t['last_deliverable']['review_notes'])): ?>
                            <div class="alert alert-danger p-3 small mb-3">
                                <strong><i class="bi bi-exclamation-triangle-fill me-1"></i> Corrección Solicitada por el Jefe:</strong>
                                <div class="mt-1 fst-italic">"<?= htmlspecialchars($t['last_deliverable']['review_notes']) ?>"</div>
                                <div class="text-muted mt-1" style="font-size: 0.75rem;">Fecha de revisión: <?= htmlspecialchars($t['last_deliverable']['reviewed_at'] ?? '') ?></div>
                            </div>

                        <!-- ÚLTIMO ENTREGABLE SUBIDO EN REVISIÓN O PROCESO -->
                        <?php elseif (!empty($t['last_deliverable'])): ?>
                            <div class="p-2 border rounded bg-white small mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-muted">Último entregable enviado:</span>
                                    <span class="badge bg-light text-dark border"><?= htmlspecialchars($t['last_deliverable']['deliverable_type'] ?? '') ?></span>
                                </div>
                                <a href="<?= htmlspecialchars($t['last_deliverable']['deliverable_url']) ?>" target="_blank" class="fw-bold d-block text-truncate">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> <?= htmlspecialchars($t['last_deliverable']['deliverable_url']) ?>
                                </a>
                                <?php if (!empty($t['last_deliverable']['notes'])): ?>
                                    <div class="text-muted small mt-1"><em>"<?= htmlspecialchars($t['last_deliverable']['notes']) ?>"</em></div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="card-footer bg-light p-3 border-top d-flex gap-2 justify-content-between align-items-center flex-wrap">
                        <?php if ($isAprobada): ?>
                            <span class="badge bg-success bg-opacity-25 text-success border border-success px-3 py-2">
                                <i class="bi bi-shield-check me-1"></i> Culminada & Archivada
                            </span>
                            <?php if (!empty($t['last_deliverable']['deliverable_url'])): ?>
                                <a href="<?= htmlspecialchars($t['last_deliverable']['deliverable_url']) ?>" target="_blank" class="btn btn-sm btn-outline-success fw-semibold">
                                    <i class="bi bi-play-circle me-1"></i> Ver Entregable Aprobado
                                </a>
                            <?php endif; ?>

                        <?php elseif ($isNueva): ?>
                            <div class="d-flex gap-2">
                                <form action="<?= url('/activities/start-task') ?>" method="POST" class="m-0">
                                    <input type="hidden" name="activity_id" value="<?= $t['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-primary fw-semibold">
                                        <i class="bi bi-play-fill me-1"></i> Iniciar Tarea
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalEditTask<?= $t['id'] ?>" title="Modificar Notas / Fechas">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </div>
                            <button type="button" class="btn btn-sm btn-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#modalDeliverable<?= $t['id'] ?>">
                                <i class="bi bi-upload me-1"></i> Subir Entregable
                            </button>

                        <?php elseif ($isCorreccion): ?>
                            <div class="d-flex align-items-center gap-2">
                                <span class="small text-danger fw-semibold"><i class="bi bi-arrow-repeat me-1"></i>Ajustar versión</span>
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalEditTask<?= $t['id'] ?>" title="Modificar Notas / Fechas">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </div>
                            <button type="button" class="btn btn-sm btn-danger fw-semibold" data-bs-toggle="modal" data-bs-target="#modalDeliverable<?= $t['id'] ?>">
                                <i class="bi bi-cloud-arrow-up me-1"></i> Subir Corrección
                            </button>

                        <?php elseif ($isRevision): ?>
                            <div class="d-flex align-items-center gap-2">
                                <span class="small text-warning text-dark fw-semibold"><i class="bi bi-hourglass-split me-1"></i>Esperando visto bueno</span>
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalEditTask<?= $t['id'] ?>" title="Modificar Notas / Fechas">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalDeliverable<?= $t['id'] ?>">
                                <i class="bi bi-pencil-square me-1"></i> Reemplazar Link
                            </button>

                        <?php else: ?>
                            <div class="d-flex align-items-center gap-2">
                                <span class="small text-muted"><i class="bi bi-clock me-1"></i>En seguimiento</span>
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalEditTask<?= $t['id'] ?>" title="Modificar Notas / Fechas">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </div>
                            <button type="button" class="btn btn-sm btn-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#modalDeliverable<?= $t['id'] ?>">
                                <i class="bi bi-upload me-1"></i> Subir Entregable
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- MODAL SUBIR ENTREGABLE (solo para tareas no culminadas o para actualizar link) -->
            <?php if (!$isAprobada): ?>
                <div class="modal fade" id="modalDeliverable<?= $t['id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="<?= url('/activities/submit-deliverable') ?>" method="POST">
                                <input type="hidden" name="activity_id" value="<?= $t['id'] ?>">

                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title fw-bold">
                                        <i class="bi bi-cloud-arrow-up me-2"></i>
                                        <?= $isCorreccion ? 'Subir Corrección de Entregable' : 'Subir Entregable Audiovisual' ?>
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Plataforma de Almacenamiento *</label>
                                        <select name="deliverable_type" class="form-select" required>
                                            <option value="FRAME_IO">Frame.io (Revisión de Video)</option>
                                            <option value="GOOGLE_DRIVE">Google Drive</option>
                                            <option value="DROPBOX">Dropbox</option>
                                            <option value="OTRO">Otro Link Externo</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">URL del Entregable *</label>
                                        <input type="url" name="deliverable_url" class="form-control" required placeholder="https://frame.io/v/xyz o https://drive.google.com/...">
                                        <div class="form-text">Asegúrate de que los permisos de enlace permitan visualización o descarga.</div>
                                    </div>

                                    <div class="mb-0">
                                        <label class="form-label small fw-semibold">Notas para el Jefe (Opcional)</label>
                                        <textarea name="notes" class="form-control" rows="3" placeholder="Ej: Versión v2 con corrección de audio y recorte de 3 segundos en intro..."></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary fw-semibold">
                                        <i class="bi bi-send me-1"></i> Enviar a Revisión del Jefe
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- MODAL MODIFICAR DETALLES DE TAREA (EMPLEADO) -->
                <div class="modal fade" id="modalEditTask<?= $t['id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="<?= url('/activities/update') ?>" method="POST">
                                <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                <div class="modal-header bg-dark text-white">
                                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Modificar Tarea</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4 text-start">
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Título de la Tarea</label>
                                        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($t['title']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Fecha Límite</label>
                                        <input type="date" name="scheduled_date" class="form-control" value="<?= htmlspecialchars($t['scheduled_date']) ?>" required>
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label small fw-semibold">Brief / Notas Creativas</label>
                                        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($t['description'] ?? '') ?></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary fw-semibold"><i class="bi bi-save me-1"></i> Guardar Cambios</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        <?php endforeach; ?>
    </div>
<?php endif; ?>
