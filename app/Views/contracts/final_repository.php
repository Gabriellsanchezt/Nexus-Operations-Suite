<!-- Estilos Dedicados para el Repositorio de Contratos Finales -->
<style>
.contract-header-badge {
    background: rgba(45, 106, 79, 0.08);
    color: #2d6a4f;
    border: 1px solid rgba(45, 106, 79, 0.22);
    border-radius: 50rem;
    padding: 0.35rem 0.85rem;
    font-size: 0.8rem;
    font-weight: 600;
}
.kpi-stat-card {
    border-radius: 12px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.kpi-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.05) !important;
}
.realtor-avatar-circle {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    color: #f8fafc;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.12);
}
.zone-pill-btn {
    border-radius: 20px;
    padding: 5px 14px;
    font-size: 0.8rem;
    font-weight: 600;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    transition: all 0.15s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.zone-pill-btn:hover {
    background: #f1f5f9;
    color: #0f172a;
    border-color: #cbd5e1;
}
.zone-pill-btn.active {
    background: #0f172a;
    color: #ffffff;
    border-color: #0f172a;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.15);
}
.search-box-luxury {
    border-radius: 10px;
    border: 1px solid #cbd5e1;
    padding: 10px 16px 10px 42px;
    transition: all 0.2s ease;
    font-size: 0.9rem;
    background-color: #ffffff;
}
.search-box-luxury:focus {
    border-color: #1e3a5f;
    box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.12);
}
.search-icon-pos {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    pointer-events: none;
}
.contract-file-card {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    transition: all 0.2s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
    position: relative;
    overflow: hidden;
}
.contract-file-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 10px 24px -4px rgba(15, 23, 42, 0.08);
    transform: translateY(-2px);
}
.contract-file-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #1e3a5f, #2d6a4f, #475569);
}
.legal-seal-badge {
    background: #fdfaf2;
    color: #854d0e;
    border: 1px solid #fef08a;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.4px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.pulse-indicator {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(45, 106, 79, 0.08);
    color: #2d6a4f;
    padding: 3px 9px;
    border-radius: 20px;
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 0.3px;
    border: 1px solid rgba(45, 106, 79, 0.2);
}
.pulse-dot {
    width: 7px;
    height: 7px;
    background-color: #2d6a4f;
    border-radius: 50%;
    position: relative;
}
.pulse-dot::after {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background-color: #2d6a4f;
    animation: pulseAnimation 2s infinite;
}
@keyframes pulseAnimation {
    0% { transform: scale(1); opacity: 0.9; }
    70% { transform: scale(2.8); opacity: 0; }
    100% { transform: scale(2.8); opacity: 0; }
}
</style>

<!-- 1. Encabezado Ejecutivo de la Sección de Contratos Finales -->
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="contract-header-badge">
                <i class="bi bi-shield-check me-1"></i> Expedientes Formalizados & Vigentes
            </span>
            <span class="text-muted small">&bull; Miami-Dade County Legal Registry</span>
        </div>
        <h4 class="fw-bold mb-1 text-dark">
            <i class="bi bi-file-earmark-lock2-fill text-success me-2"></i>Repositorio Oficial de Contratos Finales
        </h4>
        <p class="text-muted small mb-0">
            Acuerdos contractuales formalizados, vigentes y auditados con Realtors y Brokerages en Miami-Dade.
        </p>
    </div>
    <div class="d-flex gap-2 align-items-center flex-wrap">
        <a href="<?= url('/contracts/activities') ?>" class="btn btn-outline-primary btn-sm fw-semibold shadow-sm">
            <i class="bi bi-clock-history me-1"></i> Estado y Actividades de Contratos
        </a>
        <a href="<?= url('/activities') ?>" class="btn btn-outline-dark btn-sm fw-semibold shadow-sm">
            <i class="bi bi-kanban me-1"></i> Producción Audiovisual
        </a>
        <a href="<?= url('/contracts/create') ?>" class="btn btn-primary btn-sm fw-semibold shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> + Redactar Nuevo Contrato
        </a>
    </div>
</div>

<!-- 2. Filtro Rápido, Barra de Búsqueda y Selector de Vista -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <div class="row g-3 align-items-center justify-content-between">
            <!-- Buscador en tiempo real -->
            <div class="col-12 col-md-5">
                <div class="position-relative">
                    <i class="bi bi-search search-icon-pos"></i>
                    <input type="text" id="liveSearchInput" class="form-control search-box-luxury" placeholder="Buscar por inmobiliaria, broker, código (ej: MIA-CTR)..." onkeyup="filterContractsLive()">
                </div>
            </div>

            <!-- Selector Dropdown de Zonas de Miami -->
            <div class="col-12 col-md-4">
                <form method="GET" action="<?= url('/contracts/final') ?>" id="zoneFilterForm">
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-geo-alt"></i></span>
                        <select name="zone" class="form-select border-start-0" onchange="this.form.submit()">
                            <option value="">Todas las Zonas de Miami (<?= count($contracts) ?>)</option>
                            <?php foreach (MIAMI_ZONES as $zone): ?>
                                <option value="<?= htmlspecialchars($zone) ?>" <?= ($selectedZone === $zone) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($zone) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($selectedZone): ?>
                            <a href="<?= url('/contracts/final') ?>" class="btn btn-outline-secondary" title="Eliminar filtro de zona">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Conmutador de Vista (Tabla vs Tarjetas / Grid) -->
            <div class="col-12 col-md-3 text-md-end">
                <div class="btn-group" role="group">
                    <button type="button" id="btnViewTable" class="btn btn-sm btn-dark fw-semibold active" onclick="switchView('table')">
                        <i class="bi bi-table me-1"></i> Tabla
                    </button>
                    <button type="button" id="btnViewGrid" class="btn btn-sm btn-outline-dark fw-semibold" onclick="switchView('grid')">
                        <i class="bi bi-grid-fill me-1"></i> Expedientes
                    </button>
                </div>
            </div>
        </div>

        <!-- Pills de Filtro Rápido por Zona -->
        <div class="d-flex flex-wrap gap-2 mt-3 pt-3 border-top">
            <span class="small text-muted fw-semibold align-self-center me-1"><i class="bi bi-funnel"></i> Zonas:</span>
            <a href="<?= url('/contracts/final') ?>" class="zone-pill-btn <?= empty($selectedZone) ? 'active' : '' ?>">
                Todas
            </a>
            <?php foreach (['Brickell / Financial District', 'Miami Beach / South Beach', 'Coral Gables', 'Sunny Isles Beach'] as $topZone): ?>
                <a href="<?= url('/contracts/final?zone=' . urlencode($topZone)) ?>" class="zone-pill-btn <?= ($selectedZone === $topZone) ? 'active' : '' ?>">
                    <?= htmlspecialchars($topZone) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- =========================================================================
     VISTA 1: TABLA CORPORATIVA DETALLADA (DEFAULT)
     ========================================================================= -->
<div id="viewContainerTable" class="card border-0 shadow-sm overflow-hidden mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <h5 class="fw-bold mb-0 text-dark">Expedientes Contractuales Registrados</h5>
            <span class="badge bg-dark rounded-pill" id="contractsCountBadge"><?= count($contracts) ?> contratos</span>
        </div>
        <small class="text-muted"><i class="bi bi-shield-lock-fill text-success me-1"></i>Validación Notarial Miami Media Agency LLC</small>
    </div>

    <div class="card-body p-0">
        <?php if (empty($contracts)): ?>
            <div class="text-center py-5">
                <div class="p-3 bg-light text-muted mx-auto mb-3 rounded-circle d-inline-flex">
                    <i class="bi bi-folder-x fs-1"></i>
                </div>
                <h5 class="fw-bold text-dark">No se encontraron contratos aprobados</h5>
                <p class="text-muted small">No hay acuerdos que coincidan con la zona o búsqueda especificada.</p>
                <a href="<?= url('/contracts/final') ?>" class="btn btn-outline-primary btn-sm">Ver todos los contratos</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="contractsMainTable">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th style="width: 140px;">Código</th>
                            <th>Inmobiliaria / Broker</th>
                            <th>Zona Miami</th>
                            <th>Vigencia & Plazo</th>
                            <th>Tipo de Acuerdo</th>
                            <th>Estado & Auditoría</th>
                            <th class="text-end" style="min-width: 200px;">Acciones Legales</th>
                        </tr>
                    </thead>
                    <tbody id="contractsTableBody">
                        <?php foreach ($contracts as $c): ?>
                            <?php
                                $words = explode(' ', $c['company_name']);
                                $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                            ?>
                            <tr class="contract-row-item"
                                data-code="<?= strtolower(htmlspecialchars($c['contract_code'])) ?>"
                                data-company="<?= strtolower(htmlspecialchars($c['company_name'])) ?>"
                                data-contact="<?= strtolower(htmlspecialchars($c['contact_person'])) ?>"
                                data-zone="<?= strtolower(htmlspecialchars($c['miami_zone'])) ?>">
                                <!-- Código de Contrato -->
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="badge bg-dark font-monospace px-2 py-1 text-start" style="letter-spacing: 0.5px;">
                                            <?= htmlspecialchars($c['contract_code']) ?>
                                        </span>
                                        <span class="text-muted" style="font-size: 0.7rem; margin-top: 4px;">
                                            <i class="bi bi-hash"></i> ID-<?= $c['id'] ?>
                                        </span>
                                    </div>
                                </td>

                                <!-- Realtor / Inmobiliaria con Monograma -->
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="realtor-avatar-circle flex-shrink-0">
                                            <?= $initials ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark text-truncate" style="max-width: 260px;">
                                                <?= htmlspecialchars($c['company_name']) ?>
                                            </div>
                                            <div class="small text-muted d-flex align-items-center gap-2">
                                                <span><i class="bi bi-person-fill text-secondary"></i> <?= htmlspecialchars($c['contact_person']) ?></span>
                                                <?php if (!empty($c['realtor_phone'])): ?>
                                                    <span>&bull;</span>
                                                    <span><i class="bi bi-telephone text-secondary"></i> <?= htmlspecialchars($c['realtor_phone']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Zona de Miami -->
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1 d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-geo-alt-fill text-danger"></i> <?= htmlspecialchars($c['miami_zone']) ?>
                                    </span>
                                </td>

                                <!-- Vigencia -->
                                <td>
                                    <div class="small fw-semibold text-dark">
                                        <?= date('d M, Y', strtotime($c['start_date'])) ?> &rarr; <?= date('d M, Y', strtotime($c['end_date'])) ?>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <div class="progress flex-grow-1" style="height: 5px; width: 90px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: 100%;"></div>
                                        </div>
                                        <span class="text-muted" style="font-size: 0.7rem;">12 meses</span>
                                    </div>
                                </td>

                                <!-- Tipo de Acuerdo -->
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1">
                                        <i class="bi bi-camera-reels me-1 text-primary"></i>Producción Mensual
                                    </span>
                                    <span class="text-muted small d-block" style="font-size: 0.72rem;">
                                        Acuerdo Operativo
                                    </span>
                                </td>

                                <!-- Estado & Auditoría -->
                                <td>
                                    <div class="pulse-indicator mb-1">
                                        <span class="pulse-dot"></span> VIGENTE
                                    </div>
                                    <div class="small text-muted" style="font-size: 0.72rem;">
                                        <i class="bi bi-check2-circle text-primary"></i> Por <?= htmlspecialchars($c['generator_employee_name']) ?>
                                    </div>
                                </td>

                                <!-- Acciones Legales -->
                                <td class="text-end">
                                    <div class="btn-group shadow-sm">
                                        <button type="button" class="btn btn-sm btn-outline-dark" onclick="openAuditModal(<?= htmlspecialchars(json_encode($c)) ?>)" title="Inspeccionar auditoría rápida">
                                            <i class="bi bi-search me-1"></i> Auditoría
                                        </button>
                                        <a href="<?= url('/contracts/view?id=' . $c['id']) ?>" target="_blank" class="btn btn-sm btn-outline-primary fw-semibold" title="Abrir documento legal completo">
                                            <i class="bi bi-file-earmark-text me-1"></i> Ver
                                        </a>
                                        <a href="<?= url('/contracts/view?id=' . $c['id'] . '&print=true') ?>" target="_blank" class="btn btn-sm btn-dark" title="Imprimir o Descargar PDF formal">
                                            <i class="bi bi-printer-fill me-1"></i> PDF
                                        </a>
                                        <?php if (\Config\Auth::isJefe()): ?>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalDeleteContract<?= $c['id'] ?>" title="Eliminar Contrato (Exclusivo Jefe)">
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
        <?php endif; ?>
    </div>
</div>

<!-- =========================================================================
     VISTA 2: EXPEDIENTES EN CUADRÍCULA / CARDS (OPCIÓN INTERACTIVA)
     ========================================================================= -->
<div id="viewContainerGrid" class="row g-4 mb-4 d-none">
    <?php foreach ($contracts as $c): ?>
        <?php
            $words = explode(' ', $c['company_name']);
            $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
        ?>
        <div class="col-12 col-md-6 col-xl-4 contract-card-item"
             data-code="<?= strtolower(htmlspecialchars($c['contract_code'])) ?>"
             data-company="<?= strtolower(htmlspecialchars($c['company_name'])) ?>"
             data-contact="<?= strtolower(htmlspecialchars($c['contact_person'])) ?>"
             data-zone="<?= strtolower(htmlspecialchars($c['miami_zone'])) ?>">
            <div class="contract-file-card p-4">
                <!-- Header de Tarjeta -->
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="badge bg-dark font-monospace px-2 py-1">
                        <?= htmlspecialchars($c['contract_code']) ?>
                    </span>
                    <div class="pulse-indicator">
                        <span class="pulse-dot"></span> VIGENTE
                    </div>
                </div>

                <!-- Broker y Avatar -->
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="realtor-avatar-circle fs-5">
                        <?= $initials ?>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0 text-truncate" style="max-width: 220px;">
                            <?= htmlspecialchars($c['company_name']) ?>
                        </h6>
                        <small class="text-muted"><i class="bi bi-person me-1"></i><?= htmlspecialchars($c['contact_person']) ?></small>
                    </div>
                </div>

                <!-- Zona y Detalles -->
                <div class="p-3 bg-light rounded-3 mb-3 border">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small text-muted">Zona de Miami:</span>
                        <span class="badge bg-white text-dark border">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i><?= htmlspecialchars($c['miami_zone']) ?>
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small text-muted">Plan de Trabajo:</span>
                        <span class="badge bg-white text-dark border"><i class="bi bi-camera-reels text-primary me-1"></i>Producción Mensual</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small text-muted">Plazo Vigente:</span>
                        <span class="small text-dark fw-semibold"><?= date('M Y', strtotime($c['start_date'])) ?> - <?= date('M Y', strtotime($c['end_date'])) ?></span>
                    </div>
                </div>

                <?php if (!empty($c['last_review_note'])): ?>
                    <div class="small fst-italic text-muted bg-white p-2 rounded border mb-3">
                        <i class="bi bi-chat-quote me-1 text-primary"></i>"<?= htmlspecialchars($c['last_review_note']) ?>"
                    </div>
                <?php endif; ?>

                <!-- Footer y Botones -->
                <div class="mt-auto pt-3 border-top d-flex gap-2 justify-content-between align-items-center">
                    <small class="text-muted" style="font-size: 0.72rem;">
                        <i class="bi bi-check2-circle text-success me-1"></i>Por <?= htmlspecialchars($c['generator_employee_name']) ?>
                    </small>
                    <div class="d-flex gap-1">
                        <a href="<?= url('/contracts/view?id=' . $c['id']) ?>" target="_blank" class="btn btn-sm btn-outline-primary" title="Ver documento">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="<?= url('/contracts/view?id=' . $c['id'] . '&print=true') ?>" target="_blank" class="btn btn-sm btn-dark" title="PDF">
                            <i class="bi bi-printer-fill me-1"></i> PDF
                        </a>
                        <?php if (\Config\Auth::isJefe()): ?>
                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalDeleteContract<?= $c['id'] ?>" title="Eliminar Contrato (Exclusivo Jefe)">
                                <i class="bi bi-trash"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Modales de Confirmación de Eliminación de Contrato (Exclusivo Jefe) -->
<?php if (\Config\Auth::isJefe()): ?>
    <?php foreach ($contracts as $c): ?>
        <div class="modal fade" id="modalDeleteContract<?= $c['id'] ?>" tabindex="-1">
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
                                <i class="bi bi-info-circle me-1"></i> Expediente: <strong><?= htmlspecialchars($c['contract_code']) ?></strong>. Esta acción desvinculará actividades asociadas y purgará de forma irreversible el acuerdo legal.
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

<!-- =========================================================================
     MODAL DE AUDITORÍA Y TRAZABILIDAD RÁPIDA DE CONTRATO
     ========================================================================= -->
<div class="modal fade" id="modalAuditContract" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white p-4">
                <div>
                    <span class="badge bg-warning text-dark font-monospace mb-1" id="auditModalCode">CÓDIGO</span>
                    <h5 class="modal-title fw-bold text-white mb-0" id="auditModalCompany">Inmobiliaria</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="legal-seal-badge"><i class="bi bi-shield-check"></i> ESTADO APROBADO</span>
                    <span class="pulse-indicator"><span class="pulse-dot"></span> REGISTRADO EN 3NF</span>
                </div>

                <div class="list-group list-group-flush border rounded-3 mb-3 small">
                    <div class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Contacto Principal:</span>
                        <strong id="auditModalContact">-</strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Zona de Miami:</span>
                        <strong id="auditModalZone">-</strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Plan de Trabajo:</span>
                        <span class="badge bg-light text-dark border" id="auditModalFee">Producción Mensual</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Vigencia:</span>
                        <span id="auditModalDates">-</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Redactor Responsable:</span>
                        <span id="auditModalGenerator">-</span>
                    </div>
                </div>

                <div class="p-3 bg-light rounded-3 border">
                    <h6 class="fw-bold small text-muted text-uppercase mb-1"><i class="bi bi-pen-fill text-primary me-1"></i>Dictamen del Jefe:</h6>
                    <p class="small text-dark mb-0 fst-italic" id="auditModalNote">"Sin notas adicionales registradas."</p>
                </div>
            </div>
            <div class="modal-footer bg-light p-3 border-top">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
                <a href="#" id="auditModalViewBtn" target="_blank" class="btn btn-primary btn-sm fw-semibold">
                    <i class="bi bi-file-earmark-text me-1"></i> Abrir Contrato Legal Completo
                </a>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================================
     JAVASCRIPT VANILLA: FILTRADO EN VIVO Y CONMUTADOR DE VISTAS
     ========================================================================= -->
<script>
// Conmutar entre Vista Tabla y Vista Grid
function switchView(viewType) {
    const tableContainer = document.getElementById('viewContainerTable');
    const gridContainer = document.getElementById('viewContainerGrid');
    const btnTable = document.getElementById('btnViewTable');
    const btnGrid = document.getElementById('btnViewGrid');

    if (viewType === 'grid') {
        tableContainer.classList.add('d-none');
        gridContainer.classList.remove('d-none');
        btnTable.classList.remove('active', 'btn-dark');
        btnTable.classList.add('btn-outline-dark');
        btnGrid.classList.add('active', 'btn-dark');
        btnGrid.classList.remove('btn-outline-dark');
    } else {
        tableContainer.classList.remove('d-none');
        gridContainer.classList.add('d-none');
        btnGrid.classList.remove('active', 'btn-dark');
        btnGrid.classList.add('btn-outline-dark');
        btnTable.classList.add('active', 'btn-dark');
        btnTable.classList.remove('btn-outline-dark');
    }
}

// Búsqueda instantánea en vivo por texto (Filtra filas de tabla y tarjetas simultáneamente)
function filterContractsLive() {
    const query = document.getElementById('liveSearchInput').value.toLowerCase().trim();

    // Filtrar tabla
    const tableRows = document.querySelectorAll('.contract-row-item');
    let visibleCount = 0;
    tableRows.forEach(row => {
        const code = row.getAttribute('data-code') || '';
        const company = row.getAttribute('data-company') || '';
        const contact = row.getAttribute('data-contact') || '';
        const zone = row.getAttribute('data-zone') || '';

        if (code.includes(query) || company.includes(query) || contact.includes(query) || zone.includes(query)) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    // Filtrar cards
    const gridCards = document.querySelectorAll('.contract-card-item');
    gridCards.forEach(card => {
        const code = card.getAttribute('data-code') || '';
        const company = card.getAttribute('data-company') || '';
        const contact = card.getAttribute('data-contact') || '';
        const zone = card.getAttribute('data-zone') || '';

        if (code.includes(query) || company.includes(query) || contact.includes(query) || zone.includes(query)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });

    // Actualizar badge de conteo
    const badge = document.getElementById('contractsCountBadge');
    if (badge) {
        badge.innerText = visibleCount + ' contratos';
    }
}

// Abrir modal de auditoría rápida
function openAuditModal(contract) {
    document.getElementById('auditModalCode').innerText = contract.contract_code;
    document.getElementById('auditModalCompany').innerText = contract.company_name;
    document.getElementById('auditModalContact').innerText = contract.contact_person;
    document.getElementById('auditModalZone').innerText = contract.miami_zone;
    document.getElementById('auditModalFee').innerText = 'Producción Mensual Activa';
    document.getElementById('auditModalDates').innerText = contract.start_date + ' al ' + contract.end_date;
    document.getElementById('auditModalGenerator').innerText = contract.generator_employee_name || 'Personal Legal';
    document.getElementById('auditModalNote').innerText = contract.last_review_note ? ('"' + contract.last_review_note + '"') : '"Sin notas adicionales registradas."';
    document.getElementById('auditModalViewBtn').href = (typeof window.appUrl === 'function')
        ? window.appUrl('/contracts/view?id=' + contract.id)
        : ('/contracts/view?id=' + contract.id);

    const modal = new bootstrap.Modal(document.getElementById('modalAuditContract'));
    modal.show();
}
</script>
