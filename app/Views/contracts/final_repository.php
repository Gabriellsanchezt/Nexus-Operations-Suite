<!-- Almacén de Contratos Finales (PDFs Concluidos) -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h1 class="module-title"><i class="bi bi-file-earmark-pdf-fill text-danger"></i> Contratos Finales</h1>
        <p class="module-subtitle">Repositorio de documentos contractuales concluidos en PDF por Realtor</p>
    </div>
    <button type="button" class="btn btn-primary fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalUploadContract">
        <i class="bi bi-cloud-arrow-up-fill me-1"></i> + Subir Contrato Concluido (PDF)
    </button>
</div>

<!-- KPIs Informativos Operativos -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-4">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-3">
                    <i class="bi bi-archive-fill fs-4"></i>
                </div>
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Contratos Guardados</span>
                    <h3 class="fw-bold text-dark mb-0"><?= (int)($stats['total'] ?? count($contracts)) ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-4">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 bg-success bg-opacity-10 text-success rounded-3">
                    <i class="bi bi-building-check fs-4"></i>
                </div>
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Realtors con Contrato</span>
                    <h3 class="fw-bold text-dark mb-0"><?= (int)($stats['realtors_count'] ?? 0) ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-4">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 bg-secondary bg-opacity-10 text-dark rounded-3">
                    <i class="bi bi-calendar2-check-fill fs-4"></i>
                </div>
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Subidos Últimos 30 Días</span>
                    <h3 class="fw-bold text-dark mb-0"><?= (int)($stats['recent_uploads'] ?? 0) ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Barra de Filtros y Búsqueda -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="contractSearchInput" class="form-control border-start-0" placeholder="Buscar por título de contrato o inmobiliaria..." oninput="filterContracts()">
                </div>
            </div>
            <div class="col-12 col-md-6 text-md-end">
                <div class="d-inline-flex align-items-center gap-2">
                    <span class="small text-muted fw-semibold">Filtrar por Realtor:</span>
                    <select class="form-select form-select-sm w-auto" onchange="filterByRealtor(this.value)">
                        <option value="">Todos los Realtors</option>
                        <?php foreach ($realtors as $rel): ?>
                            <option value="<?= $rel['id'] ?>" <?= ($selectedRealtorId === (int)$rel['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($rel['company_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Listado de Contratos Concluidos -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($contracts)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-file-earmark-pdf display-4 text-muted mb-3 d-block"></i>
                <h5 class="fw-bold text-dark">No hay contratos finales subidos actualmente</h5>
                <p class="text-muted small mb-3">Sube el documento PDF concluido asociándolo al Realtor correspondiente.</p>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalUploadContract">
                    <i class="bi bi-cloud-arrow-up-fill me-1"></i> Subir Primer Contrato
                </button>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="contractsTable">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th>Contrato Final</th>
                            <th>Realtor Asociado</th>
                            <th>Fecha de Subida (Automática)</th>
                            <th>Subido Por</th>
                            <th>Notas / Observaciones</th>
                            <th class="text-end">Documento</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contracts as $c): ?>
                            <tr class="contract-row" data-realtor="<?= (int)$c['realtor_id'] ?>" data-search="<?= strtolower(htmlspecialchars($c['contract_title'] . ' ' . $c['company_name'] . ' ' . $c['contact_person'])) ?>">
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="p-2 bg-danger bg-opacity-10 text-danger rounded">
                                            <i class="bi bi-file-earmark-pdf-fill fs-5"></i>
                                        </div>
                                        <div>
                                            <strong class="text-dark d-block"><?= htmlspecialchars($c['contract_title']) ?></strong>
                                            <span class="small text-muted font-monospace"><?= htmlspecialchars($c['pdf_filename']) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <strong class="text-dark"><?= htmlspecialchars($c['company_name']) ?></strong>
                                    <div class="small text-muted"><?= htmlspecialchars($c['contact_person']) ?> &bull; <?= htmlspecialchars($c['miami_zone']) ?></div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border font-monospace">
                                        <i class="bi bi-clock-history me-1 text-primary"></i>
                                        <?= date('Y-m-d H:i', strtotime($c['created_at'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="bi bi-person-check me-1"></i>
                                        <?= htmlspecialchars($c['uploader_name'] ?? 'Dirección') ?>
                                    </small>
                                </td>
                                <td>
                                    <?php if (!empty($c['notes'])): ?>
                                        <span class="small text-secondary"><?= htmlspecialchars($c['notes']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">&mdash;</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group shadow-sm">
                                        <a href="<?= url('/contracts/view-pdf?id=' . $c['id']) ?>" target="_blank" class="btn btn-sm btn-outline-danger" title="Ver documento PDF en pestaña nueva">
                                            <i class="bi bi-eye-fill me-1"></i> Ver PDF
                                        </a>
                                        <?php if (\Config\Auth::isJefe()): ?>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="confirmDeleteContract(<?= $c['id'] ?>, '<?= addslashes($c['contract_title']) ?>')" title="Eliminar contrato">
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

<!-- MODAL SUBIR CONTRATO CONCLUIDO (PDF) -->
<div class="modal fade" id="modalUploadContract" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= url('/contracts/store') ?>" method="POST" enctype="multipart/form-data">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-file-earmark-arrow-up me-2 text-danger"></i>Subir Contrato Concluido</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Asociar a Cliente Realtor</label>
                        <select name="realtor_id" class="form-select" required
                                data-purpose="Cliente Realtor titular del contrato legal."
                                data-error-msg="Selecciona el cliente Realtor al que corresponde el contrato."
                                data-success-msg="Cliente Realtor asociado correctamente.">
                            <option value="">-- Seleccionar Realtor --</option>
                            <?php foreach ($realtors as $rel): ?>
                                <option value="<?= $rel['id'] ?>" <?= ($selectedRealtorId === (int)$rel['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($rel['company_name']) ?> (<?= htmlspecialchars($rel['contact_person']) ?> - <?= htmlspecialchars($rel['miami_zone']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Título o Identificador del Contrato</label>
                        <input type="text" name="contract_title" class="form-control" required placeholder="Ej: Acuerdo Anual de Producción Audiovisual 2026"
                               data-purpose="Nombre descriptivo para identificar el documento en el repositorio."
                               data-error-msg="Ingresa el título o identificador del contrato."
                               data-success-msg="Título del contrato registrado.">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Adjuntar Archivo Concluido (PDF)</label>
                        <input type="file" name="contract_pdf" class="form-control" accept=".pdf,application/pdf" required
                               data-purpose="Documento digitalizado firmado en formato PDF."
                               data-error-msg="Debes adjuntar un archivo en formato PDF."
                               data-success-msg="Archivo PDF seleccionado listo para almacenar.">
                        <div class="form-text small">Solo se admiten archivos en formato .PDF concluidos legalmente.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Notas u Observaciones Operativas</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Detalles relevantes sobre el alcance o firma..."
                                  data-purpose="Opcional. Observaciones sobre cláusulas, vigencia o alcance del contrato."
                                  data-success-msg="Observaciones registradas."></textarea>
                    </div>

                    <div class="p-3 bg-light rounded border text-muted small">
                        <i class="bi bi-info-circle-fill text-primary me-1"></i>
                        <strong>Fecha de Subida Automática:</strong> El sistema calculará y registrará automáticamente la fecha y hora exacta del día en que se almacena el documento.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-semibold"><i class="bi bi-cloud-arrow-up-fill me-1"></i> Guardar y Almacenar Contrato</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- FORMULARIO OCULTO PARA ELIMINAR CONTRATO -->
<form id="deleteContractForm" action="<?= url('/contracts/delete') ?>" method="POST" style="display: none;">
    <input type="hidden" name="id" id="deleteContractId" value="">
</form>

<script>
function filterContracts() {
    const q = document.getElementById('contractSearchInput').value.toLowerCase().trim();
    const rows = document.querySelectorAll('.contract-row');
    rows.forEach(row => {
        const text = row.getAttribute('data-search') || '';
        row.style.display = text.includes(q) ? '' : 'none';
    });
}

function filterByRealtor(realtorId) {
    if (realtorId) {
        window.location.href = window.appUrl('/contracts/final?realtor_id=' + encodeURIComponent(realtorId));
    } else {
        window.location.href = window.appUrl('/contracts/final');
    }
}

function confirmDeleteContract(id, title) {
    if (confirm('¿Confirmas que deseas eliminar el contrato final "' + title + '" del almacén? El archivo PDF se borrará del servidor.')) {
        document.getElementById('deleteContractId').value = id;
        document.getElementById('deleteContractForm').submit();
    }
}
</script>
