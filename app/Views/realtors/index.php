<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold mb-1"><i class="bi bi-building text-primary me-2"></i>Gestión de Clientes Realtors (Miami)</h2>
        <p class="text-muted mb-0">Directorio de agentes inmobiliarios, agencias y brokerages asociados en el Condado de Miami-Dade</p>
    </div>
    <button type="button" class="btn btn-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#modalNewRealtor">
        <i class="bi bi-plus-lg me-1"></i> + Nuevo Realtor
    </button>
</div>

<!-- Tabla de Realtors -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($realtors)): ?>
            <div class="text-center py-5 text-muted">
                No hay clientes realtors registrados actualmente.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th>Inmobiliaria / Brokerage</th>
                            <th>Persona de Contacto</th>
                            <th>Zona de Miami</th>
                            <th>Contacto Directo</th>
                            <th>Enlaces & Recursos</th>
                            <th>Redes Sociales</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($realtors as $r): ?>
                            <?php
                                $social = json_decode($r['social_handles'] ?? '{}', true) ?: [];
                            ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($r['company_name']) ?></strong>
                                    <div class="small text-muted font-monospace">ID: #<?= $r['id'] ?></div>
                                </td>
                                <td><?= htmlspecialchars($r['contact_person']) ?></td>
                                <td>
                                    <span class="badge bg-light text-dark border"><?= htmlspecialchars($r['miami_zone']) ?></span>
                                </td>
                                <td>
                                    <div class="small"><i class="bi bi-telephone me-1 text-muted"></i><?= htmlspecialchars($r['phone']) ?></div>
                                    <div class="small"><i class="bi bi-envelope me-1 text-muted"></i><?= htmlspecialchars($r['email']) ?></div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <?php if (!empty($r['dropbox_url'])): ?>
                                            <a href="<?= htmlspecialchars($r['dropbox_url']) ?>" target="_blank" rel="noopener noreferrer" class="badge bg-primary-subtle text-primary border border-primary-subtle text-decoration-none d-inline-flex align-items-center gap-1" style="width: fit-content;">
                                                <i class="bi bi-dropbox"></i> Dropbox Recursos
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small">Sin Dropbox</span>
                                        <?php endif; ?>

                                        <?php if (!empty($r['credentials_url'])): ?>
                                            <a href="<?= htmlspecialchars($r['credentials_url']) ?>" target="_blank" rel="noopener noreferrer" class="badge bg-secondary-subtle text-dark border border-secondary-subtle text-decoration-none d-inline-flex align-items-center gap-1" style="width: fit-content;">
                                                <i class="bi bi-shield-lock-fill text-warning"></i> Bóveda Credenciales
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if (!empty($social['instagram'])): ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="bi bi-instagram me-1"></i><?= htmlspecialchars($social['instagram']) ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($social['tiktok'])): ?>
                                        <span class="badge bg-dark-subtle text-dark border"><i class="bi bi-tiktok me-1"></i><?= htmlspecialchars($social['tiktok']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group shadow-sm">
                                        <!-- Botón VER Proyectos -->
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="openRealtorProjects(<?= (int)$r['id'] ?>)" title="Ver proyectos y actividades de este Realtor">
                                            <i class="bi bi-eye-fill me-1"></i> Ver
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#modalEditRealtor<?= $r['id'] ?>" title="Modificar datos del Realtor">
                                            <i class="bi bi-pencil me-1"></i> Editar
                                        </button>
                                        <?php if (\Config\Auth::isJefe()): ?>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalDeleteRealtor<?= $r['id'] ?>" title="Eliminar Realtor (Exclusivo Jefe)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>

                            <!-- MODAL EDITAR REALTOR -->
                            <div class="modal fade" id="modalEditRealtor<?= $r['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="<?= url('/realtors/update') ?>" method="POST">
                                            <input type="hidden" name="id" value="<?= $r['id'] ?>">

                                            <div class="modal-header bg-dark text-white">
                                                <h5 class="modal-title fw-bold">Editar Cliente Realtor</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Nombre de la Inmobiliaria / Brokerage *</label>
                                                    <input type="text" name="company_name" class="form-control" value="<?= htmlspecialchars($r['company_name']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Persona de Contacto *</label>
                                                    <input type="text" name="contact_person" class="form-control" value="<?= htmlspecialchars($r['contact_person']) ?>" required>
                                                </div>
                                                <div class="row g-2 mb-3">
                                                    <div class="col-6">
                                                        <label class="form-label small fw-semibold">Teléfono *</label>
                                                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($r['phone']) ?>" required>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label small fw-semibold">Email *</label>
                                                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($r['email']) ?>" required>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Zona Principal de Miami *</label>
                                                    <select name="miami_zone" class="form-select" required>
                                                        <?php foreach ($zones as $z): ?>
                                                            <option value="<?= htmlspecialchars($z) ?>" <?= ($r['miami_zone'] === $z) ? 'selected' : '' ?>><?= htmlspecialchars($z) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <!-- ENLACES OPERATIVOS -->
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold text-primary">
                                                        <i class="bi bi-dropbox me-1"></i> Enlace a Carpeta Dropbox (Recursos & Material)
                                                    </label>
                                                    <input type="url" name="dropbox_url" class="form-control" value="<?= htmlspecialchars($r['dropbox_url'] ?? '') ?>" placeholder="https://www.dropbox.com/sh/...">
                                                    <div class="form-text small">Los editores y creativos tendrán acceso directo desde sus tareas a este enlace.</div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold text-secondary">
                                                        <i class="bi bi-shield-lock-fill text-warning me-1"></i> Enlace Bóveda / Documento de Credenciales (Claves y Correos)
                                                    </label>
                                                    <input type="url" name="credentials_url" class="form-control" value="<?= htmlspecialchars($r['credentials_url'] ?? '') ?>" placeholder="https://vault... o enlace a documento seguro">
                                                    <div class="form-text small">Enlace confidencial donde almacenas los accesos del cliente.</div>
                                                </div>

                                                <div class="row g-2 mb-3">
                                                    <div class="col-6">
                                                        <label class="form-label small fw-semibold">Instagram Handle</label>
                                                        <input type="text" name="instagram" class="form-control" value="<?= htmlspecialchars($social['instagram'] ?? '') ?>">
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label small fw-semibold">TikTok Handle</label>
                                                        <input type="text" name="tiktok" class="form-control" value="<?= htmlspecialchars($social['tiktok'] ?? '') ?>">
                                                    </div>
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label small fw-semibold">Estado de la Cuenta</label>
                                                    <select name="status" class="form-select">
                                                        <option value="ACTIVO" <?= ($r['status'] === 'ACTIVO') ? 'selected' : '' ?>>Activo</option>
                                                        <option value="INACTIVO" <?= ($r['status'] === 'INACTIVO') ? 'selected' : '' ?>>Inactivo</option>
                                                        <option value="PROSPECTO" <?= ($r['status'] === 'PROSPECTO') ? 'selected' : '' ?>>Prospecto</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary fw-semibold">Guardar Cambios</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <?php if (\Config\Auth::isJefe()): ?>
                                <!-- MODAL ELIMINAR REALTOR (SOLO JEFE) -->
                                <div class="modal fade" id="modalDeleteRealtor<?= $r['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <form action="<?= url('/realtors/delete') ?>" method="POST">
                                                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                                <div class="modal-header bg-danger text-white py-3">
                                                    <h5 class="modal-title fw-bold fs-6"><i class="bi bi-trash-fill me-2"></i>Eliminar Cliente Realtor</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4 text-center">
                                                    <div class="p-3 bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex mb-3">
                                                        <i class="bi bi-exclamation-triangle-fill fs-1"></i>
                                                    </div>
                                                    <h6 class="fw-bold text-dark mb-2">¿Confirmas la eliminación definitiva?</h6>
                                                    <p class="text-muted small mb-0">
                                                        Se eliminará permanentemente al Realtor <strong><?= htmlspecialchars($r['company_name']) ?></strong> (<?= htmlspecialchars($r['contact_person']) ?>).
                                                    </p>
                                                </div>
                                                <div class="modal-footer bg-light py-2">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-danger btn-sm fw-bold px-3">Confirmar y Eliminar</button>
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

<!-- MODAL NUEVO REALTOR -->
<div class="modal fade" id="modalNewRealtor" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= url('/realtors/store') ?>" method="POST">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-building-add me-2"></i>Registrar Nuevo Cliente Realtor</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Inmobiliaria / Brokerage *</label>
                        <input type="text" name="company_name" class="form-control" required placeholder="Ej: Douglas Elliman Real Estate">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nombre del Agente / Contacto *</label>
                        <input type="text" name="contact_person" class="form-control" required placeholder="Ej: Jonathan Meyer">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Teléfono *</label>
                            <input type="text" name="phone" class="form-control" required placeholder="+1 (305) 555-0100">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Email Corporativo *</label>
                            <input type="email" name="email" class="form-control" required placeholder="agente@elliman.com">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Zona Primaria en Miami *</label>
                        <select name="miami_zone" class="form-select" required>
                            <?php foreach ($zones as $z): ?>
                                <option value="<?= htmlspecialchars($z) ?>"><?= htmlspecialchars($z) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- ENLACES OPERATIVOS -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-primary">
                            <i class="bi bi-dropbox me-1"></i> Enlace a Carpeta Dropbox (Recursos & Material)
                        </label>
                        <input type="url" name="dropbox_url" class="form-control" placeholder="https://www.dropbox.com/sh/...">
                        <div class="form-text small">Los editores y trabajadores podrán acceder con un clic a este Dropbox para descargar material.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">
                            <i class="bi bi-shield-lock-fill text-warning me-1"></i> Enlace Bóveda / Documento de Credenciales (Claves y Correos)
                        </label>
                        <input type="url" name="credentials_url" class="form-control" placeholder="https://vault... o enlace a documento seguro">
                        <div class="form-text small">Enlace confidencial donde almacenas los correos y contraseñas de este Realtor.</div>
                    </div>

                    <div class="row g-2 mb-0">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Instagram</label>
                            <input type="text" name="instagram" class="form-control" placeholder="@realtor_miami">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">TikTok</label>
                            <input type="text" name="tiktok" class="form-control" placeholder="@realtor_miami">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-semibold">Registrar Cliente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL VER PROYECTOS DEL REALTOR -->
<div class="modal fade" id="modalViewProjects" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <div>
                    <h5 class="modal-title fw-bold mb-0" id="vpCompanyName">Cargando...</h5>
                    <span class="small text-secondary" id="vpContactPerson"></span> &bull; <span class="small text-secondary" id="vpMiamiZone"></span>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Accesos Rápidos de Recursos -->
                <div class="p-3 bg-light rounded border mb-4">
                    <div class="fw-semibold text-dark small text-uppercase mb-2">Accesos Directos del Cliente</div>
                    <div class="d-flex flex-wrap gap-2">
                        <span id="vpDropboxBtn"></span>
                        <span id="vpCredentialsBtn"></span>
                    </div>
                </div>

                <!-- Lista de Proyectos / Actividades -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-kanban me-2 text-primary"></i>Proyectos y Actividades en Curso</h6>
                    <span class="badge bg-secondary" id="vpProjectsCount">0 actividades</span>
                </div>

                <div id="vpProjectsContainer">
                    <div class="text-center py-4 text-muted">
                        <div class="spinner-border spinner-border-sm text-primary me-2"></div> Cargando proyectos...
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
function openRealtorProjects(realtorId) {
    const modalEl = document.getElementById('modalViewProjects');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();

    document.getElementById('vpCompanyName').textContent = 'Cargando...';
    document.getElementById('vpContactPerson').textContent = '';
    document.getElementById('vpMiamiZone').textContent = '';
    document.getElementById('vpDropboxBtn').innerHTML = '';
    document.getElementById('vpCredentialsBtn').innerHTML = '';
    document.getElementById('vpProjectsContainer').innerHTML = '<div class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-primary me-2"></div> Cargando proyectos...</div>';

    fetch(window.appUrl('/realtors/projects?id=' + realtorId))
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                document.getElementById('vpProjectsContainer').innerHTML = '<div class="alert alert-danger">' + data.error + '</div>';
                return;
            }

            const r = data.realtor;
            const projects = data.projects || [];

            document.getElementById('vpCompanyName').textContent = r.company_name;
            document.getElementById('vpContactPerson').textContent = r.contact_person + ' (' + r.phone + ')';
            document.getElementById('vpMiamiZone').textContent = r.miami_zone;

            // Botón Dropbox
            if (r.dropbox_url) {
                document.getElementById('vpDropboxBtn').innerHTML = `
                    <a href="${r.dropbox_url}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-dropbox me-1"></i> Abrir Carpeta Dropbox
                    </a>
                `;
            } else {
                document.getElementById('vpDropboxBtn').innerHTML = '<span class="badge bg-light text-muted border">Sin Dropbox configurado</span>';
            }

            // Botón Credenciales
            if (r.credentials_url) {
                document.getElementById('vpCredentialsBtn').innerHTML = `
                    <a href="${r.credentials_url}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-dark">
                        <i class="bi bi-shield-lock-fill text-warning me-1"></i> Ver Bóveda de Credenciales
                    </a>
                `;
            } else {
                document.getElementById('vpCredentialsBtn').innerHTML = '<span class="badge bg-light text-muted border">Sin Bóveda configurada</span>';
            }

            // Proyectos
            document.getElementById('vpProjectsCount').textContent = projects.length + ' actividad(es)';

            if (projects.length === 0) {
                document.getElementById('vpProjectsContainer').innerHTML = `
                    <div class="text-center py-4 text-muted border rounded bg-white">
                        <i class="bi bi-inbox fs-2 text-secondary d-block mb-1"></i>
                        No hay actividades registradas con este cliente aún.
                    </div>
                `;
                return;
            }

            let html = '<div class="table-responsive"><table class="table table-sm table-hover align-middle border mb-0">';
            html += '<thead class="table-light small text-uppercase"><tr><th>Actividad / Proyecto</th><th>Tipo</th><th>Responsable</th><th>Estado</th><th>Fecha</th></tr></thead><tbody>';

            projects.forEach(p => {
                let badgeClass = 'bg-secondary';
                if (p.current_status === 'APROBADA') badgeClass = 'bg-success';
                else if (p.current_status === 'EN_PROCESO') badgeClass = 'bg-primary';
                else if (p.current_status === 'ENTREGADA_REVISION') badgeClass = 'bg-warning text-dark';
                else if (p.current_status === 'CORRECCION') badgeClass = 'bg-danger';

                html += `
                    <tr>
                        <td>
                            <strong class="text-dark">${p.title}</strong>
                            ${p.description ? `<div class="small text-muted text-truncate" style="max-width: 250px;">${p.description}</div>` : ''}
                        </td>
                        <td><span class="badge bg-light text-dark border">${p.activity_type}</span></td>
                        <td><small>${p.employee_name}</small></td>
                        <td><span class="badge ${badgeClass}">${p.current_status}</span></td>
                        <td><small class="text-muted font-monospace">${p.scheduled_date || p.created_at}</small></td>
                    </tr>
                `;
            });

            html += '</tbody></table></div>';
            document.getElementById('vpProjectsContainer').innerHTML = html;
        })
        .catch(err => {
            document.getElementById('vpProjectsContainer').innerHTML = '<div class="alert alert-danger">Error al cargar proyectos: ' + err.message + '</div>';
        });
}
</script>
