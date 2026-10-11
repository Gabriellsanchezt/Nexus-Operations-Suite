<?php
$isEditing = !empty($editContract);
$formAction = $isEditing ? url('/contracts/update') : url('/contracts/store');
$selectedTemplateId = $editContract['template_id'] ?? ($templates[0]['id'] ?? 1);
$selectedRealtorId = $editContract['realtor_id'] ?? '';
$selectedMonthlyFee = $editContract['monthly_fee'] ?? '3500.00';
$selectedStartDate = $editContract['start_date'] ?? date('Y-m-d');
$selectedEndDate = $editContract['end_date'] ?? date('Y-m-d', strtotime('+1 year'));
$selectedServices = $editContract['services_description'] ?? '4 Reels cinematográficos 4K con dron + 1 Walkthrough de 2 minutos mensuales en locación acordada.';
?>

<!-- Encabezado de Página -->
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1 small fw-semibold">
                <i class="bi bi-shield-check me-1"></i> Módulo Legal Inmobiliario
            </span>
            <span class="text-muted small">&bull; Miami-Dade County, FL</span>
        </div>
        <h4 class="fw-bold mb-1 text-dark">
            <i class="bi bi-file-earmark-diff-fill text-primary me-2"></i>Generador Dinámico de Contratos
        </h4>
        <p class="text-muted small mb-0">
            Redacción automatizada, parametrización de alcance y previsualización de acuerdos de producción audiovisual con Realtors.
        </p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <a href="<?= url('/contracts/activities') ?>" class="btn btn-outline-primary btn-sm fw-semibold shadow-sm">
            <i class="bi bi-clock-history me-1"></i> Ver Actividades de Contratos
        </a>
        <?php if (!empty($isJefe)): ?>
            <a href="<?= url('/contracts/final') ?>" class="btn btn-outline-success btn-sm fw-semibold">
                <i class="bi bi-archive me-1"></i> Contratos Finales
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Alerta Si Se Está Editando un Contrato Negado o Borrador -->
<?php if ($isEditing): ?>
    <?php if ($editContract['status'] === 'RECHAZADO'): ?>
        <div class="alert alert-danger border-0 shadow-sm d-flex align-items-start gap-3 p-3 mb-4 rounded-3">
            <div class="p-2 bg-danger bg-opacity-25 text-danger rounded-circle fs-4 lh-1 flex-shrink-0">
                <i class="bi bi-exclamation-octagon-fill"></i>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h6 class="fw-bold text-danger mb-1">
                        Subsanando Contrato Negado: <?= htmlspecialchars($editContract['contract_code']) ?>
                    </h6>
                    <span class="badge bg-danger rounded-pill">Estado: Culminado & Negado</span>
                </div>
                <div class="small text-dark mt-1">
                    <strong>Motivo / Observaciones de Dirección:</strong>
                    <div class="p-2 bg-white rounded border border-danger border-opacity-25 mt-1 fst-italic text-danger-emphasis">
                        "<?= htmlspecialchars($editContract['last_review_note'] ?? $editContract['feedback_notes'] ?? 'Ajustar términos del acuerdo antes de re-aprobar.') ?>"
                    </div>
                </div>
                <div class="small text-muted mt-2">
                    Realiza los ajustes requeridos en las actividades o fechas y presiona <strong>"Enviar a Revisión del Jefe"</strong> para solicitar su aprobación.
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info border-0 shadow-sm d-flex align-items-center gap-3 p-3 mb-4 rounded-3">
            <i class="bi bi-pencil-square fs-3 text-info"></i>
            <div>
                <h6 class="fw-bold mb-1 text-dark">Editando Borrador: <?= htmlspecialchars($editContract['contract_code']) ?></h6>
                <div class="small text-muted">Modifica los parámetros necesarios y guárdalo o envíalo a revisión de Dirección.</div>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Regla de Flujo de Aprobación -->
<div class="card border-0 bg-light shadow-sm mb-4">
    <div class="card-body p-3 d-flex align-items-center gap-3">
        <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-circle fs-4 lh-1 flex-shrink-0">
            <i class="bi bi-info-circle-fill"></i>
        </div>
        <div class="small text-secondary">
            <strong>Flujo de Emisión:</strong> Puedes guardar tu contrato como <span class="badge bg-secondary">BORRADOR</span> para continuar redactándolo. Al presionar <strong>"Enviar a Revisión del Jefe"</strong>, pasará al estado <span class="badge bg-warning text-dark">EN REVISIÓN</span> y se transferirá al panel ejecutivo de Dirección para su decisión final de aprobación o negación con observaciones.
        </div>
    </div>
</div>

<div class="row g-4 mb-5">
    <!-- 1. Columna Izquierda: Formulario de Parametrización -->
    <div class="col-12 col-xl-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-sliders text-primary me-2"></i>Parámetros del Contrato
                </h6>
                <span class="badge bg-light text-muted border small">Paso 1 de 2</span>
            </div>
            <div class="card-body p-4">
                <form id="contractForm" action="<?= $formAction ?>" method="POST">
                    <?php if ($isEditing): ?>
                        <input type="hidden" name="contract_id" value="<?= $editContract['id'] ?>">
                    <?php endif; ?>

                    <!-- Plantilla Base de Contrato -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark d-flex align-items-center justify-content-between">
                            <span>Plantilla Base de Contrato <span class="text-danger">*</span></span>
                            <span class="text-muted small" style="font-size: 0.7rem;">Modelos Estándar</span>
                        </label>
                        <select name="template_id" id="template_id" class="form-select" required onchange="updateLivePreview()">
                            <?php foreach ($templates as $tpl): ?>
                                <option value="<?= $tpl['id'] ?>" <?= ($tpl['id'] == $selectedTemplateId) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($tpl['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Cliente Realtor / Brokerage Registrado -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark d-flex align-items-center justify-content-between">
                            <span>Cliente Realtor / Brokerage <span class="text-danger">*</span></span>
                            <a href="<?= url('/realtors') ?>" target="_blank" class="text-primary small text-decoration-none" style="font-size: 0.72rem;">+ Nuevo Realtor</a>
                        </label>
                        <select name="realtor_id" id="realtor_id" class="form-select" required onchange="onRealtorSelect(this)">
                            <option value="">-- Seleccionar Realtor Registrado --</option>
                            <?php foreach ($realtors as $r): ?>
                                <option value="<?= $r['id'] ?>"
                                        data-company="<?= htmlspecialchars($r['company_name']) ?>"
                                        data-contact="<?= htmlspecialchars($r['contact_person']) ?>"
                                        data-zone="<?= htmlspecialchars($r['miami_zone']) ?>"
                                        <?= ($r['id'] == $selectedRealtorId) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($r['company_name']) ?> (<?= htmlspecialchars($r['contact_person']) ?> - <?= htmlspecialchars($r['miami_zone']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Paquetes Audiovisuales Preconfigurados -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">
                            Paquete Audiovisual Predefinido <span class="text-muted fw-normal">(Opcional)</span>
                        </label>
                        <select id="presetPackage" class="form-select bg-light" onchange="applyPresetPackage(this.value)">
                            <option value="">-- Personalizado / Cargar paquete estándar --</option>
                            <?php foreach (REALTOR_PACKAGES as $k => $pkg): ?>
                                <option value="<?= $k ?>" data-fee="<?= $pkg['default_fee'] ?>" data-desc="<?= htmlspecialchars($pkg['deliverables']) ?>">
                                    <?= htmlspecialchars($pkg['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Modalidad Operativa -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">
                            Modalidad del Acuerdo Operativo
                        </label>
                        <div class="p-2 bg-light rounded border text-muted small d-flex align-items-center gap-2">
                            <i class="bi bi-camera-reels-fill text-primary"></i>
                            <span>Plan Integral de Producción Audiovisual (Miami-Dade)</span>
                        </div>
                        <input type="hidden" name="monthly_fee" id="monthly_fee" value="<?= htmlspecialchars((string)($selectedMonthlyFee ?: '0')) ?>">
                    </div>

                    <!-- Vigencia del Contrato (Fechas) -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small text-dark">Fecha de Inicio <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="<?= htmlspecialchars($selectedStartDate) ?>" required onchange="updateLivePreview()">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small text-dark">Fecha de Culminación <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="<?= htmlspecialchars($selectedEndDate) ?>" required onchange="updateLivePreview()">
                        </div>
                    </div>

                    <!-- Alcance de Servicios y Entregables -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold small text-dark">
                            Alcance de Servicios y Entregables del Paquete <span class="text-danger">*</span>
                        </label>
                        <textarea name="services_description" id="services_description" class="form-control" rows="4" required oninput="updateLivePreview()" placeholder="Detallar videos 4K, reels, fotos HDR y coberturas mensuales..."><?= htmlspecialchars($selectedServices) ?></textarea>
                    </div>

                    <!-- Acciones del Formulario -->
                    <div class="d-flex flex-column gap-2">
                        <button type="submit" name="submit_mode" value="send_review" class="btn btn-primary py-2 fw-semibold shadow-sm" onclick="return confirm('¿Confirmas el envío formal a revisión del Jefe?')">
                            <i class="bi bi-send-check-fill me-1"></i> Enviar a Revisión del Jefe
                        </button>
                        <button type="submit" name="submit_mode" value="draft" class="btn btn-outline-secondary py-2">
                            <i class="bi bi-floppy me-1"></i> Guardar como Borrador
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 2. Columna Derecha: Previsualización en Vivo en Hoja Corporativa -->
    <div class="col-12 col-xl-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-eye-fill text-success me-2"></i>Previsualización en Vivo del Contrato
                    </h6>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 small">Tiempo Real</span>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="updateLivePreview()" title="Refrescar Previsualización">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
            
            <div class="card-body p-3 bg-light overflow-auto" style="max-height: 720px; background-color: #f1f5f9 !important;">
                <!-- Simulación de Hoja de Papel Legal Oficial -->
                <div class="contract-paper-document p-4 p-md-5 bg-white border rounded shadow-sm mx-auto" style="max-width: 720px; min-height: 600px; font-family: 'Georgia', serif;">
                    <!-- Membrete Corporativo -->
                    <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
                        <div>
                            <div class="fw-bold text-dark text-uppercase tracking-wider" style="font-size: 1.05rem; letter-spacing: 1px;">
                                MIAMI LUXURY MEDIA AGENCY LLC
                            </div>
                            <div class="text-muted text-uppercase" style="font-size: 0.68rem; font-family: sans-serif; letter-spacing: 0.5px;">
                                Producción Audiovisual Inmobiliaria &bull; Miami-Dade County, Florida
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-dark font-monospace" style="font-size: 0.72rem;">
                                <?= $editContract['contract_code'] ?? 'MIA-CTR-' . date('Y') . '-DRAFT' ?>
                            </span>
                            <div class="text-muted" style="font-size: 0.68rem; font-family: sans-serif;">
                                <?= date('F j, Y') ?>
                            </div>
                        </div>
                    </div>

                    <!-- Contenedor del Cuerpo Dinámico Compilado -->
                    <div id="livePreviewContainer">
                        <div class="text-center py-5 text-muted">
                            <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                            <p class="small mb-0" style="font-family: sans-serif;">Compilando parámetros del contrato...</p>
                        </div>
                    </div>

                    <!-- Pie de Firma del Contrato -->
                    <div class="border-top pt-4 mt-5 d-flex justify-content-between flex-wrap gap-4" style="font-family: sans-serif; font-size: 0.75rem;">
                        <div class="text-center" style="min-width: 180px;">
                            <div class="border-top border-dark pt-1 fw-bold text-dark">MIAMI LUXURY MEDIA</div>
                            <div class="text-muted">Dirección Ejecutiva</div>
                        </div>
                        <div class="text-center" style="min-width: 180px;">
                            <div class="border-top border-dark pt-1 fw-bold text-dark" id="previewSignatureRealtor">EL REALTOR / BROKERAGE</div>
                            <div class="text-muted">Representante Autorizado</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================================
     3. TABLERO INTEGRADO DE ACTIVIDADES Y RESOLUCIONES CULMINADAS
     ========================================================================= -->
<div class="card border-0 shadow-sm" id="actividades">
    <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h6 class="fw-bold mb-1 text-dark">
                <i class="bi bi-clock-history text-primary me-2"></i>Actividades y Estado de Culminación de Contratos
            </h6>
            <p class="text-muted small mb-0">
                Historial completo de borradores, contratos en revisión y resoluciones finales (Aprobadas & Negadas).
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?= url('/contracts/activities') ?>" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-arrows-fullscreen me-1"></i> Ver Tablero Completo
            </a>
        </div>
    </div>

    <!-- Pestañas de Filtrado Rápido -->
    <div class="px-3 pt-3 border-bottom bg-light">
        <ul class="nav nav-tabs border-0" id="contractsCreateTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active small fw-semibold" onclick="filterCreateList('ALL', this)">
                    Todos (<?= count($allContracts ?? []) ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link small fw-semibold text-success" onclick="filterCreateList('APROBADO', this)">
                    <i class="bi bi-check-circle me-1"></i> Culminadas Aprobadas (<?= count(array_filter($allContracts ?? [], fn($c) => $c['status'] === 'APROBADO')) ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link small fw-semibold text-danger" onclick="filterCreateList('RECHAZADO', this)">
                    <i class="bi bi-x-circle me-1"></i> Culminadas Negadas (<?= count(array_filter($allContracts ?? [], fn($c) => $c['status'] === 'RECHAZADO')) ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link small fw-semibold text-warning-emphasis" onclick="filterCreateList('EN_REVISION', this)">
                    <i class="bi bi-hourglass-split me-1"></i> En Revisión (<?= count(array_filter($allContracts ?? [], fn($c) => $c['status'] === 'EN_REVISION')) ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link small fw-semibold text-secondary" onclick="filterCreateList('BORRADOR', this)">
                    <i class="bi bi-file-earmark me-1"></i> Borradores (<?= count(array_filter($allContracts ?? [], fn($c) => $c['status'] === 'BORRADOR')) ?>)
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-0">
        <?php if (empty($allContracts)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-folder2-open fs-2 d-block mb-2 opacity-50"></i>
                <div class="fw-semibold small">Aún no hay contratos registrados</div>
                <div class="text-muted" style="font-size: 0.75rem;">Completa el formulario superior para generar tu primer contrato.</div>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="createContractsTable">
                    <thead class="table-light small text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        <tr>
                            <th class="ps-4">Código & Fecha</th>
                            <th>Realtor / Brokerage</th>
                            <th>Tipo de Acuerdo</th>
                            <th>Estado</th>
                            <th>Observaciones del Jefe</th>
                            <th class="text-end pe-4">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allContracts as $c): ?>
                            <tr class="create-contract-row" data-status="<?= htmlspecialchars($c['status']) ?>">
                                <td class="ps-4">
                                    <div class="fw-bold font-monospace text-primary small">
                                        <?= htmlspecialchars($c['contract_code']) ?>
                                    </div>
                                    <div class="text-muted small" style="font-size: 0.72rem;">
                                        <?= date('d/m/Y', strtotime($c['updated_at'] ?? $c['created_at'])) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($c['company_name']) ?></div>
                                    <div class="small text-muted" style="font-size: 0.75rem;">
                                        <?= htmlspecialchars($c['miami_zone']) ?> &bull; <?= htmlspecialchars($c['contact_person']) ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1">
                                        <i class="bi bi-camera-reels text-primary me-1"></i>Producción Mensual
                                    </span>
                                </td>
                                <td>
                                    <?php if ($c['status'] === 'APROBADO'): ?>
                                        <span class="badge bg-success bg-opacity-15 text-success border border-success border-opacity-25 rounded-pill px-2 py-1 small">
                                            <i class="bi bi-check-circle-fill me-1"></i> Aprobado
                                        </span>
                                    <?php elseif ($c['status'] === 'RECHAZADO'): ?>
                                        <span class="badge bg-danger bg-opacity-15 text-danger border border-danger border-opacity-25 rounded-pill px-2 py-1 small">
                                            <i class="bi bi-x-circle-fill me-1"></i> Negado
                                        </span>
                                    <?php elseif ($c['status'] === 'EN_REVISION'): ?>
                                        <span class="badge bg-warning bg-opacity-15 text-warning-emphasis border border-warning border-opacity-25 rounded-pill px-2 py-1 small">
                                            <i class="bi bi-hourglass-split me-1"></i> En Revisión
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary bg-opacity-15 text-secondary border border-secondary border-opacity-25 rounded-pill px-2 py-1 small">
                                            <i class="bi bi-pencil me-1"></i> Borrador
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td style="max-width: 260px;">
                                    <?php if (!empty($c['feedback_notes'])): ?>
                                        <div class="small <?= ($c['status'] === 'RECHAZADO') ? 'text-danger fw-semibold' : 'text-muted fst-italic' ?>">
                                            <i class="bi <?= ($c['status'] === 'RECHAZADO') ? 'bi-exclamation-triangle-fill' : 'bi-chat-left-text' ?> me-1"></i>
                                            <?= htmlspecialchars($c['feedback_notes']) ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-1">
                                        <?php if ($c['status'] === 'APROBADO'): ?>
                                            <a href="<?= url('/contracts/view?id=' . $c['id']) ?>" target="_blank" class="btn btn-sm btn-outline-success" title="Ver Contrato PDF">
                                                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Ver PDF
                                            </a>
                                        <?php elseif ($c['status'] === 'RECHAZADO'): ?>
                                            <a href="<?= url('/contracts/create?edit_id=' . $c['id']) ?>" class="btn btn-sm btn-outline-danger" title="Editar y corregir borrador negado">
                                                <i class="bi bi-pencil-square me-1"></i> Subsanar
                                            </a>
                                            <a href="<?= url('/contracts/view?id=' . $c['id']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary" title="Ver documento">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        <?php elseif ($c['status'] === 'BORRADOR'): ?>
                                            <form action="<?= url('/contracts/submit-existing') ?>" method="POST" class="d-inline" onsubmit="return confirm('¿Enviar este borrador al Jefe para aprobación final?')">
                                                <input type="hidden" name="contract_id" value="<?= $c['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-success" title="Enviar a Revisión">
                                                    <i class="bi bi-send-fill me-1"></i> Enviar
                                                </button>
                                            </form>
                                            <a href="<?= url('/contracts/create?edit_id=' . $c['id']) ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= url('/contracts/view?id=' . $c['id']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary" title="Ver documento en revisión">
                                                <i class="bi bi-eye"></i>
                                            </a>
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
     JAVASCRIPT DE PREVISUALIZACIÓN Y COMPILACIÓN EN TIEMPO REAL
     ========================================================================= -->
<script>
let currentRealtorData = {
    company: 'One Sotheby’s International Realty',
    contact: 'Elena Rostova',
    zone: 'Brickell / Financial District'
};

function onRealtorSelect(select) {
    const opt = select.options[select.selectedIndex];
    if (opt && opt.value) {
        currentRealtorData = {
            company: opt.getAttribute('data-company') || '',
            contact: opt.getAttribute('data-contact') || '',
            zone: opt.getAttribute('data-zone') || ''
        };
        const sigEl = document.getElementById('previewSignatureRealtor');
        if (sigEl) {
            sigEl.textContent = (currentRealtorData.company || 'EL REALTOR / BROKERAGE').toUpperCase();
        }
    }
    updateLivePreview();
}

function applyPresetPackage(pkgKey) {
    const select = document.getElementById('presetPackage');
    const opt = select.options[select.selectedIndex];
    if (opt && opt.value) {
        const fee = opt.getAttribute('data-fee');
        const desc = opt.getAttribute('data-desc');
        if (fee) document.getElementById('monthly_fee').value = fee;
        if (desc) document.getElementById('services_description').value = desc;
        updateLivePreview();
    }
}

function updateLivePreview() {
    const templateId = document.getElementById('template_id').value;
    const realtorId = document.getElementById('realtor_id').value;
    const monthlyFee = document.getElementById('monthly_fee').value;
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const services = document.getElementById('services_description').value;

    const payload = new FormData();
    payload.append('template_id', templateId);
    payload.append('realtor_id', realtorId);
    payload.append('monthly_fee', monthlyFee);
    payload.append('start_date', startDate);
    payload.append('end_date', endDate);
    payload.append('services_description', services);
    payload.append('company_name', currentRealtorData.company);
    payload.append('contact_person', currentRealtorData.contact);
    payload.append('miami_zone', currentRealtorData.zone);

    const previewUrl = '<?= url('/contracts/preview-ajax') ?>';

    fetch(previewUrl, {
        method: 'POST',
        body: payload
    })
    .then(r => r.json())
    .then(data => {
        if (data && data.success) {
            const container = document.getElementById('livePreviewContainer');
            if (container) {
                container.innerHTML = data.html;
            }
        }
    })
    .catch(err => {
        console.error("Error al actualizar previsualización:", err);
    });
}

function filterCreateList(status, btn) {
    document.querySelectorAll('#contractsCreateTabs .nav-link').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    const rows = document.querySelectorAll('.create-contract-row');
    rows.forEach(row => {
        const rowStatus = row.getAttribute('data-status');
        if (status === 'ALL' || rowStatus === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Inicializar datos al cargar la página
document.addEventListener('DOMContentLoaded', () => {
    const realtorSelect = document.getElementById('realtor_id');
    if (realtorSelect && realtorSelect.selectedIndex > 0) {
        onRealtorSelect(realtorSelect);
    } else {
        updateLivePreview();
    }
});
</script>
