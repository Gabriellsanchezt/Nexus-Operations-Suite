<div class="container-fluid px-0">
    <!-- Encabezado Principal de Pantalla Amplia -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-envelope-paper-heart-fill text-danger me-2"></i>Cronograma & Calendario de Email Marketing
            </h2>
            <p class="text-muted mb-0">Programación, cronograma y fechas estipuladas de subida y envío de emails por cada Realtor</p>
        </div>
        <button type="button" class="btn btn-primary fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNewEmailCampaign">
            <i class="bi bi-calendar-plus-fill me-1"></i> + Programar Email para Realtor
        </button>
    </div>

    <!-- KPIs del Módulo de Email Marketing -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white">
                <span class="text-muted small fw-semibold text-uppercase">Total Campañas</span>
                <h3 class="fw-bold text-dark mb-0"><?= $counts['total'] ?></h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white border-start border-primary border-4">
                <span class="text-muted small fw-semibold text-uppercase">Programadas</span>
                <h3 class="fw-bold text-primary mb-0"><?= $counts['programados'] ?></h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white border-start border-warning border-4">
                <span class="text-muted small fw-semibold text-uppercase">Subidas</span>
                <h3 class="fw-bold text-warning mb-0"><?= $counts['subidos'] ?></h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white border-start border-success border-4">
                <span class="text-muted small fw-semibold text-uppercase">Enviadas</span>
                <h3 class="fw-bold text-success mb-0"><?= $counts['enviados'] ?></h3>
            </div>
        </div>
    </div>

    <!-- Barra de Botones por Realtor (Requerimiento: Botones para ver las fechas estipuladas de cada uno) -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="fw-bold small text-uppercase text-secondary">
                    <i class="bi bi-funnel-fill text-primary me-1"></i> Filtrar Cronograma por Cliente Realtor:
                </span>
                <?php if ($selectedRealtorId): ?>
                    <a href="<?= url('/email-marketing') ?>" class="small text-muted text-decoration-none">Ver Todos</a>
                <?php endif; ?>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="<?= url('/email-marketing') ?>" class="btn btn-sm <?= empty($selectedRealtorId) ? 'btn-dark' : 'btn-outline-dark' ?>">
                    <i class="bi bi-grid-fill me-1"></i> Todos los Clientes (<?= count($campaigns) ?>)
                </a>
                <?php foreach ($realtors as $r): ?>
                    <?php $isActive = ($selectedRealtorId === (int)$r['id']); ?>
                    <a href="<?= url('/email-marketing?realtor_id=' . $r['id']) ?>" 
                       class="btn btn-sm <?= $isActive ? 'btn-primary shadow-sm' : 'btn-outline-secondary' ?>">
                        <i class="bi bi-building me-1"></i> <?= htmlspecialchars($r['company_name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Cronograma en Cuadrícula y Tabla -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-calendar3 text-danger me-2"></i>Fechas Estipuladas de Email Marketing
            </h5>
            <span class="small text-muted">
                <?= count($campaigns) ?> campaña(s) listada(s)
            </span>
        </div>
        <div class="card-body p-0">
            <?php if (empty($campaigns)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-envelope-x display-4 text-muted mb-3 d-block"></i>
                    <h5 class="fw-bold text-dark">No hay fechas de email programadas para este filtro</h5>
                    <p class="text-muted small mb-3">Programa la fecha de subida o envío para coordinar el cronograma del Realtor.</p>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNewEmailCampaign">
                        <i class="bi bi-calendar-plus-fill me-1"></i> Programar Campaña
                    </button>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-uppercase">
                            <tr>
                                <th style="width: 150px;">Fecha Estipulada</th>
                                <th>Cliente Realtor</th>
                                <th>Campaña / Asunto del Email</th>
                                <th>Estado de Ejecución</th>
                                <th>Notas / Observaciones</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($campaigns as $camp): ?>
                                <?php
                                    $stBadge = match($camp['status']) {
                                        'ENVIADO' => 'bg-success text-white',
                                        'SUBIDO' => 'bg-warning text-dark',
                                        default => 'bg-primary text-white'
                                    };
                                    $isPast = (strtotime($camp['scheduled_date']) < strtotime(date('Y-m-d')));
                                ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold <?= $isPast ? 'text-muted' : 'text-danger' ?>">
                                            <i class="bi bi-calendar-event me-1"></i>
                                            <?= htmlspecialchars($camp['scheduled_date']) ?>
                                        </div>
                                        <small class="text-muted font-monospace">
                                            <?= date('l', strtotime($camp['scheduled_date'])) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <strong class="text-dark"><?= htmlspecialchars($camp['company_name']) ?></strong>
                                        <div class="small text-muted"><?= htmlspecialchars($camp['contact_person']) ?> &bull; <?= htmlspecialchars($camp['miami_zone']) ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($camp['title']) ?></div>
                                    </td>
                                    <td>
                                        <!-- Selector rápido de estado -->
                                        <form action="<?= url('/email-marketing/update-status') ?>" method="POST" class="d-inline">
                                            <input type="hidden" name="id" value="<?= $camp['id'] ?>">
                                            <input type="hidden" name="realtor_id" value="<?= $selectedRealtorId ?? '' ?>">
                                            <select name="status" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                                <option value="PROGRAMADO" <?= ($camp['status'] === 'PROGRAMADO') ? 'selected' : '' ?>>Programado</option>
                                                <option value="SUBIDO" <?= ($camp['status'] === 'SUBIDO') ? 'selected' : '' ?>>Subido</option>
                                                <option value="ENVIADO" <?= ($camp['status'] === 'ENVIADO') ? 'selected' : '' ?>>Enviado</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        <?php if (!empty($camp['notes'])): ?>
                                            <small class="text-secondary"><?= htmlspecialchars($camp['notes']) ?></small>
                                        <?php else: ?>
                                            <span class="text-muted small">&mdash;</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <form action="<?= url('/email-marketing/delete') ?>" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta campaña programada?')">
                                            <input type="hidden" name="id" value="<?= $camp['id'] ?>">
                                            <input type="hidden" name="realtor_id" value="<?= $selectedRealtorId ?? '' ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- MODAL PROGRAMAR CAMPAÑA DE EMAIL -->
<div class="modal fade" id="modalNewEmailCampaign" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= url('/email-marketing/store') ?>" method="POST">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-envelope-plus me-2 text-primary"></i>Programar Campaña de Email</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Cliente Realtor *</label>
                        <select name="realtor_id" class="form-select" required>
                            <option value="">-- Seleccionar Realtor --</option>
                            <?php foreach ($realtors as $rel): ?>
                                <option value="<?= $rel['id'] ?>" <?= ($selectedRealtorId === (int)$rel['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($rel['company_name']) ?> (<?= htmlspecialchars($rel['contact_person']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Título o Asunto de la Campaña *</label>
                        <input type="text" name="title" class="form-control" required placeholder="Ej: Newsletter Mensual: Oportunidades Brickell Pre-construcción">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Fecha Estipulada *</label>
                            <input type="date" name="scheduled_date" class="form-control" required value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Estado Inicial</label>
                            <select name="status" class="form-select">
                                <option value="PROGRAMADO">Programado</option>
                                <option value="SUBIDO">Subido</option>
                                <option value="ENVIADO">Enviado</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-semibold">Notas / Indicaciones del Contenido (Opcional)</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Público objetivo, enlaces a piezas o requisitos específicos del Realtor..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-semibold">Guardar en Cronograma</button>
                </div>
            </form>
        </div>
    </div>
</div>
