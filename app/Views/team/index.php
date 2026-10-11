<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold mb-1">
            <i class="bi bi-people-fill text-dark me-2"></i>Gestión de Personal, Trabajadores y Roles
        </h2>
        <p class="text-muted mb-0">Control total para Dirección General: Creación, modificación y suspensión de trabajadores, roles y cargos operativos</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <button type="button" class="btn btn-outline-dark fw-semibold" data-bs-toggle="modal" data-bs-target="#modalNewRole">
            <i class="bi bi-shield-plus me-1"></i> + Nuevo Rol
        </button>
        <button type="button" class="btn btn-outline-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#modalNewSpecialty">
            <i class="bi bi-tag-fill me-1"></i> + Nueva Especialidad
        </button>
        <button type="button" class="btn btn-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#modalNewTeam">
            <i class="bi bi-person-plus-fill me-1"></i> + Registrar Trabajador
        </button>
    </div>
</div>

<!-- Tarjetas KPI del Personal -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total Equipo</span>
                    <h3 class="fw-bold mb-0 text-dark"><?= $stats['total_employees'] ?? count($employees) ?></h3>
                </div>
                <div class="p-2 bg-light rounded-circle text-primary fs-4">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Trabajadores Activos</span>
                    <h3 class="fw-bold mb-0 text-success"><?= $stats['active_employees'] ?? 0 ?></h3>
                </div>
                <div class="p-2 bg-success bg-opacity-10 rounded-circle text-success fs-4">
                    <i class="bi bi-person-check-fill"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Suspendidos</span>
                    <h3 class="fw-bold mb-0 text-danger"><?= $stats['suspended_employees'] ?? 0 ?></h3>
                </div>
                <div class="p-2 bg-danger bg-opacity-10 rounded-circle text-danger fs-4">
                    <i class="bi bi-person-x-fill"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Roles & Especialidades</span>
                    <h3 class="fw-bold mb-0 text-dark"><?= ($stats['total_roles'] ?? count($roles)) + ($stats['total_specialties'] ?? count($specialties)) ?></h3>
                </div>
                <div class="p-2 bg-info bg-opacity-10 rounded-circle text-info fs-4">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pestañas de Navegación: Trabajadores vs Roles & Especialidades -->
<ul class="nav nav-pills mb-4 border-bottom pb-3">
    <li class="nav-item">
        <a class="nav-link <?= ($activeTab === 'workers') ? 'active' : '' ?> fw-semibold" href="<?= url('/team?tab=workers') ?>">
            <i class="bi bi-person-badge-fill me-1"></i> Trabajadores & Personal Operativo
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= ($activeTab === 'roles') ? 'active' : '' ?> fw-semibold" href="<?= url('/team?tab=roles') ?>">
            <i class="bi bi-shield-check me-1"></i> Catálogo de Roles & Especialidades
        </a>
    </li>
</ul>

<?php if ($activeTab === 'workers'): ?>
    <!-- =========================================================================
         PESTAÑA 1: GESTIÓN DE TRABAJADORES (CREAR, MODIFICAR, SUSPENDER)
         ========================================================================= -->

    <!-- Filtros de Estado del Trabajador -->
    <div class="d-flex flex-wrap gap-2 mb-3 align-items-center justify-content-between">
        <div class="d-flex gap-2">
            <a href="<?= url('/team?tab=workers') ?>" class="btn btn-sm <?= empty($statusFilter) ? 'btn-dark' : 'btn-outline-dark' ?>">
                Todos (<?= $stats['total_employees'] ?? 0 ?>)
            </a>
            <a href="<?= url('/team?tab=workers&status=ACTIVO') ?>" class="btn btn-sm <?= ($statusFilter === 'ACTIVO') ? 'btn-success' : 'btn-outline-success' ?>">
                <i class="bi bi-check-circle me-1"></i> Activos (<?= $stats['active_employees'] ?? 0 ?>)
            </a>
            <a href="<?= url('/team?tab=workers&status=SUSPENDIDO') ?>" class="btn btn-sm <?= ($statusFilter === 'SUSPENDIDO') ? 'btn-danger' : 'btn-outline-danger' ?>">
                <i class="bi bi-slash-circle me-1"></i> Suspendidos (<?= $stats['suspended_employees'] ?? 0 ?>)
            </a>
        </div>
        <div class="text-muted small">
            Total en vista: <strong><?= count($employees) ?></strong> trabajadores
        </div>
    </div>

    <!-- Tabla de Personal -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th>Trabajador</th>
                            <th>Email (Acceso)</th>
                            <th>Teléfono</th>
                            <th>Rol del Sistema</th>
                            <th>Especialidades Asignadas</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones de Dirección</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($employees)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-people display-6 d-block mb-2"></i>
                                    No se encontraron trabajadores con el filtro seleccionado.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($employees as $emp): ?>
                                <?php
                                    $currentAdminId = (int)(\Config\Auth::id() ?? $_SESSION['user_id'] ?? 0);
                                    $isSelf = ($currentAdminId === (int)$emp['user_id']);
                                    $isSuspended = ($emp['user_status'] === 'SUSPENDIDO');
                                ?>
                                <tr class="<?= $isSuspended ? 'bg-danger bg-opacity-10' : '' ?>">
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($emp['full_name']) ?></div>
                                        <div class="small text-muted font-monospace">
                                            ID #<?= $emp['employee_id'] ?> &bull; Alta: <?= htmlspecialchars($emp['hire_date'] ?: 'N/D') ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="font-monospace small"><?= htmlspecialchars($emp['email']) ?></span>
                                    </td>
                                    <td>
                                        <span class="small"><?= htmlspecialchars($emp['phone'] ?: '-') ?></span>
                                    </td>
                                    <td>
                                        <?php if ($emp['role_name'] === 'JEFE_ADMIN'): ?>
                                            <span class="badge bg-danger">JEFE_ADMIN</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?= htmlspecialchars($emp['role_name']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($emp['role_name'] === 'JEFE_ADMIN'): ?>
                                            <span class="badge bg-dark">ACCESO TOTAL (SUPER-ADMIN)</span>
                                        <?php elseif (empty($emp['specialties'])): ?>
                                            <span class="text-muted small">Sin especialidades</span>
                                        <?php else: ?>
                                            <div class="d-flex flex-wrap gap-1">
                                                <?php foreach ($emp['specialties'] as $sp): ?>
                                                    <?php
                                                        $spBadge = match($sp['code']) {
                                                            'EDITOR_VIDEO' => 'bg-success',
                                                            'DISENADOR_CARRUSELES' => 'bg-info text-dark',
                                                            'CAMAROGRAFO' => 'bg-dark',
                                                            'COMMUNITY_MANAGER' => 'bg-warning text-dark',
                                                            default => 'bg-secondary'
                                                        };
                                                    ?>
                                                    <span class="badge <?= $spBadge ?>">
                                                        <?= htmlspecialchars($sp['name']) ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($isSuspended): ?>
                                            <span class="badge bg-danger bg-opacity-25 text-danger border border-danger">
                                                <i class="bi bi-slash-circle-fill me-1"></i> SUSPENDIDO
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-success bg-opacity-25 text-success border border-success">
                                                <i class="bi bi-check-circle-fill me-1"></i> ACTIVO
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-1 align-items-center">
                                            <!-- Botón Modificar Ficha del Trabajador -->
                                            <button type="button" class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#modalEditWorker<?= $emp['user_id'] ?>" title="Modificar datos, rol y especialidades">
                                                <i class="bi bi-pencil-square me-1"></i> Modificar
                                            </button>

                                            <!-- Botón Suspender / Reactivar -->
                                            <?php if ($isSelf): ?>
                                                <span class="badge bg-light text-muted border px-2 py-1 small" title="Tu propia sesión">
                                                    (Tú)
                                                </span>
                                            <?php else: ?>
                                                <form action="<?= url('/team/toggle-status') ?>" method="POST" class="d-inline m-0" onsubmit="return confirm('<?= $isSuspended ? '¿Desea reactivar a este trabajador en el sistema?' : '¿Está seguro de SUSPENDER a este trabajador? No podrá iniciar sesión ni realizar tareas.' ?>')">
                                                    <input type="hidden" name="user_id" value="<?= $emp['user_id'] ?>">
                                                    <?php if ($isSuspended): ?>
                                                        <button type="submit" class="btn btn-sm btn-success fw-semibold" title="Reactivar acceso al sistema">
                                                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reactivar
                                                        </button>
                                                    <?php else: ?>
                                                        <button type="submit" class="btn btn-sm btn-outline-danger fw-semibold" title="Suspender acceso al sistema">
                                                            <i class="bi bi-slash-circle me-1"></i> Suspender
                                                        </button>
                                                    <?php endif; ?>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>

                                <!-- MODAL MODIFICAR TRABAJADOR -->
                                <div class="modal fade" id="modalEditWorker<?= $emp['user_id'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <form action="<?= url('/team/update') ?>" method="POST">
                                                <input type="hidden" name="user_id" value="<?= $emp['user_id'] ?>">

                                                <div class="modal-header bg-dark text-white">
                                                    <h5 class="modal-title fw-bold">
                                                        <i class="bi bi-person-gear me-2"></i>Modificar Ficha: <?= htmlspecialchars($emp['full_name']) ?>
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4 text-start">
                                                    <div class="row g-3 mb-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-semibold">Nombre Completo *</label>
                                                            <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($emp['full_name']) ?>" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-semibold">Teléfono</label>
                                                            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($emp['phone'] ?? '') ?>">
                                                        </div>
                                                    </div>

                                                    <div class="row g-3 mb-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-semibold">Correo Electrónico (Login) *</label>
                                                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($emp['email']) ?>" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-semibold">Cambiar Contraseña (Opcional)</label>
                                                            <input type="password" name="password" class="form-control" placeholder="Dejar en blanco para mantener la actual">
                                                            <div class="form-text">Solo ingrese un valor si desea resetear su clave de acceso.</div>
                                                        </div>
                                                    </div>

                                                    <div class="row g-3 mb-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-semibold">Rol del Sistema *</label>
                                                            <select name="role_id" class="form-select" required <?= ($emp['role_name'] === 'JEFE_ADMIN') ? 'disabled' : '' ?>>
                                                                <?php foreach ($roles as $rl): ?>
                                                                    <option value="<?= $rl['id'] ?>" <?= ((int)$emp['role_id'] === (int)$rl['id']) ? 'selected' : '' ?>>
                                                                        <?= htmlspecialchars($rl['name']) ?> - <?= htmlspecialchars($rl['description']) ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                            <?php if ($emp['role_name'] === 'JEFE_ADMIN'): ?>
                                                                <input type="hidden" name="role_id" value="<?= $emp['role_id'] ?>">
                                                                <div class="form-text text-danger">El rol JEFE_ADMIN no puede ser degradado desde este panel.</div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-semibold">Fecha de Contratación</label>
                                                            <input type="date" name="hire_date" class="form-control" value="<?= htmlspecialchars($emp['hire_date'] ?: date('Y-m-d')) ?>">
                                                        </div>
                                                    </div>

                                                    <?php if ($emp['role_name'] !== 'JEFE_ADMIN'): ?>
                                                        <div class="mb-0">
                                                            <label class="form-label small fw-semibold text-dark">Especialidades Profesionales y Permisos Modulares</label>
                                                            <p class="small text-muted mb-2">Marque las especialidades que determinan qué pantallas y actividades puede ejecutar el trabajador:</p>
                                                            <?php $empSpecIds = array_column($emp['specialties'], 'id'); ?>
                                                            <div class="row g-2">
                                                                <?php foreach ($specialties as $sp): ?>
                                                                    <div class="col-md-6">
                                                                        <div class="p-2 border rounded bg-light">
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="checkbox" name="specialties[]" value="<?= $sp['id'] ?>" id="edit_sp_<?= $emp['user_id'] ?>_<?= $sp['id'] ?>" <?= in_array($sp['id'], $empSpecIds) ? 'checked' : '' ?>>
                                                                                <label class="form-check-label small" for="edit_sp_<?= $emp['user_id'] ?>_<?= $sp['id'] ?>">
                                                                                    <strong><?= htmlspecialchars($sp['name']) ?></strong>
                                                                                    <span class="font-monospace text-primary ms-1" style="font-size: 0.72rem;">(<?= htmlspecialchars($sp['code']) ?>)</span>
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="alert alert-light border small text-muted">
                                                            <i class="bi bi-info-circle me-1"></i> Como JEFE_ADMIN, este usuario cuenta con permisos universales para todos los módulos sin requerir especialidades restringidas.
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary fw-semibold">
                                                        <i class="bi bi-check2 me-1"></i> Guardar Cambios
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- =========================================================================
         PESTAÑA 2: GESTIÓN DE ROLES Y ESPECIALIDADES (CREAR, MODIFICAR, SUSPENDER)
         ========================================================================= -->

    <!-- SECCIÓN A: ROLES DEL SISTEMA -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-shield-lock-fill text-primary me-2"></i>Roles Base del Sistema (RBAC)
                </h5>
                <small class="text-muted">Determinan el nivel de autorización estructural (Administración vs Empleado)</small>
            </div>
            <button type="button" class="btn btn-sm btn-outline-dark fw-semibold" data-bs-toggle="modal" data-bs-target="#modalNewRole">
                <i class="bi bi-plus-lg me-1"></i> + Crear Rol
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th style="width: 250px;">Nombre del Rol</th>
                            <th>Descripción y Alcance</th>
                            <th style="width: 140px;">Estado</th>
                            <th class="text-end" style="width: 200px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($roles as $rl): ?>
                            <?php 
                                $isRoleSuspended = (($rl['status'] ?? 'ACTIVO') === 'SUSPENDIDO');
                                $isSystemRole = in_array($rl['name'], ['JEFE_ADMIN', 'EMPLEADO'], true);
                            ?>
                            <tr class="<?= $isRoleSuspended ? 'bg-danger bg-opacity-10' : '' ?>">
                                <td>
                                    <span class="badge <?= ($rl['name'] === 'JEFE_ADMIN') ? 'bg-danger' : 'bg-dark' ?> fs-6 font-monospace">
                                        <?= htmlspecialchars($rl['name']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?= htmlspecialchars($rl['description'] ?? 'Sin descripción') ?>
                                </td>
                                <td>
                                    <?php if ($isRoleSuspended): ?>
                                        <span class="badge bg-danger bg-opacity-25 text-danger border border-danger">
                                            <i class="bi bi-slash-circle-fill me-1"></i> SUSPENDIDO
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-success bg-opacity-25 text-success border border-success">
                                            <i class="bi bi-check-circle-fill me-1"></i> ACTIVO
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1 align-items-center">
                                        <button type="button" class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#modalEditRole<?= $rl['id'] ?>">
                                            <i class="bi bi-pencil me-1"></i> Modificar
                                        </button>
                                        <?php if ($rl['name'] === 'JEFE_ADMIN'): ?>
                                            <span class="badge bg-light text-muted border px-2 py-1 small">Vital</span>
                                        <?php else: ?>
                                            <form action="<?= url('/team/roles/toggle-status') ?>" method="POST" class="d-inline m-0" onsubmit="return confirm('<?= $isRoleSuspended ? '¿Reactivar este rol?' : '¿Suspender este rol? Los usuarios asociados no podrán autenticarse correctamente.' ?>')">
                                                <input type="hidden" name="role_id" value="<?= $rl['id'] ?>">
                                                <?php if ($isRoleSuspended): ?>
                                                    <button type="submit" class="btn btn-sm btn-success fw-semibold">
                                                        <i class="bi bi-check-lg me-1"></i> Activar
                                                    </button>
                                                <?php else: ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger fw-semibold">
                                                        <i class="bi bi-slash-circle me-1"></i> Suspender
                                                    </button>
                                                <?php endif; ?>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>

                            <!-- MODAL MODIFICAR ROL -->
                            <div class="modal fade" id="modalEditRole<?= $rl['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="<?= url('/team/roles/update') ?>" method="POST">
                                            <input type="hidden" name="role_id" value="<?= $rl['id'] ?>">
                                            <div class="modal-header bg-dark text-white">
                                                <h5 class="modal-title fw-bold">Modificar Rol: <?= htmlspecialchars($rl['name']) ?></h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4 text-start">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Nombre del Rol (Identificador) *</label>
                                                    <input type="text" name="name" class="form-control font-monospace" value="<?= htmlspecialchars($rl['name']) ?>" required <?= $isSystemRole ? 'readonly' : '' ?>>
                                                    <?php if ($isSystemRole): ?>
                                                        <div class="form-text text-muted">Los roles del sistema (JEFE_ADMIN / EMPLEADO) no pueden renombrarse para proteger la integridad lógica.</div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label small fw-semibold">Descripción del Rol *</label>
                                                    <textarea name="description" class="form-control" rows="3" required><?= htmlspecialchars($rl['description']) ?></textarea>
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

                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- SECCIÓN B: ESPECIALIDADES Y CARGOS OPERATIVOS -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-tag-fill text-success me-2"></i>Especialidades & Cargos Operativos
                </h5>
                <small class="text-muted">Habilitan las herramientas específicas para cada creativo o administrativo (Contratos, Edición, Dron, etc.)</small>
            </div>
            <button type="button" class="btn btn-sm btn-outline-success fw-semibold" data-bs-toggle="modal" data-bs-target="#modalNewSpecialty">
                <i class="bi bi-plus-lg me-1"></i> + Crear Especialidad
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th style="width: 220px;">Código Técnico</th>
                            <th>Nombre del Cargo / Especialidad</th>
                            <th>Descripción</th>
                            <th style="width: 140px;">Estado</th>
                            <th class="text-end" style="width: 200px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($specialties as $sp): ?>
                            <?php $isSpecSuspended = (($sp['status'] ?? 'ACTIVO') === 'SUSPENDIDO'); ?>
                            <tr class="<?= $isSpecSuspended ? 'bg-danger bg-opacity-10' : '' ?>">
                                <td>
                                    <span class="badge bg-secondary font-monospace"><?= htmlspecialchars($sp['code']) ?></span>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($sp['name']) ?></strong>
                                </td>
                                <td>
                                    <small class="text-muted"><?= htmlspecialchars($sp['description']) ?></small>
                                </td>
                                <td>
                                    <?php if ($isSpecSuspended): ?>
                                        <span class="badge bg-danger bg-opacity-25 text-danger border border-danger">
                                            <i class="bi bi-slash-circle-fill me-1"></i> SUSPENDIDO
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-success bg-opacity-25 text-success border border-success">
                                            <i class="bi bi-check-circle-fill me-1"></i> ACTIVO
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1 align-items-center">
                                        <button type="button" class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#modalEditSpec<?= $sp['id'] ?>">
                                            <i class="bi bi-pencil me-1"></i> Modificar
                                        </button>
                                        <form action="<?= url('/team/specialties/toggle-status') ?>" method="POST" class="d-inline m-0" onsubmit="return confirm('<?= $isSpecSuspended ? '¿Reactivar esta especialidad operativa?' : '¿Suspender esta especialidad? No podrá ser asignada a nuevos proyectos o trabajadores.' ?>')">
                                            <input type="hidden" name="specialty_id" value="<?= $sp['id'] ?>">
                                            <?php if ($isSpecSuspended): ?>
                                                <button type="submit" class="btn btn-sm btn-success fw-semibold">
                                                    <i class="bi bi-check-lg me-1"></i> Activar
                                                </button>
                                            <?php else: ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger fw-semibold">
                                                    <i class="bi bi-slash-circle me-1"></i> Suspender
                                                </button>
                                            <?php endif; ?>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- MODAL MODIFICAR ESPECIALIDAD -->
                            <div class="modal fade" id="modalEditSpec<?= $sp['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="<?= url('/team/specialties/update') ?>" method="POST">
                                            <input type="hidden" name="specialty_id" value="<?= $sp['id'] ?>">
                                            <div class="modal-header bg-dark text-white">
                                                <h5 class="modal-title fw-bold">Modificar Especialidad: <?= htmlspecialchars($sp['name']) ?></h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4 text-start">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Código de Identificación *</label>
                                                    <input type="text" name="code" class="form-control font-monospace" value="<?= htmlspecialchars($sp['code']) ?>" required placeholder="EJ: ANALISTA_DATOS">
                                                    <div class="form-text">Solo mayúsculas y guiones bajos (A-Z, 0-9, _).</div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Nombre Descriptivo del Cargo *</label>
                                                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($sp['name']) ?>" required placeholder="Ej: Analista de Datos Inmobiliarios">
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label small fw-semibold">Descripción de Responsabilidades</label>
                                                    <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($sp['description']) ?></textarea>
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

                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php endif; ?>

<!-- =========================================================================
     MODALES GLOBALES: REGISTRAR TRABAJADOR, CREAR ROL, CREAR ESPECIALIDAD
     ========================================================================= -->

<!-- MODAL REGISTRAR NUEVO TRABAJADOR -->
<div class="modal fade" id="modalNewTeam" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= url('/team/store') ?>" method="POST">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-person-plus-fill me-2"></i>Registrar Nuevo Trabajador
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Nombre Completo *</label>
                            <input type="text" name="full_name" class="form-control" required placeholder="Ej: Andrés Villalobos">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Teléfono</label>
                            <input type="text" name="phone" class="form-control" placeholder="+1 (305) 555-0199">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Correo Electrónico (Login) *</label>
                            <input type="email" name="email" class="form-control" required placeholder="andres@miamiagency.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Contraseña Inicial *</label>
                            <input type="password" name="password" class="form-control" required placeholder="••••••••">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Rol del Sistema *</label>
                        <select name="role_id" class="form-select" required>
                            <?php foreach ($roles as $rl): ?>
                                <?php if (($rl['status'] ?? 'ACTIVO') === 'ACTIVO'): ?>
                                    <option value="<?= $rl['id'] ?>" <?= ($rl['name'] === 'EMPLEADO') ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($rl['name']) ?> - <?= htmlspecialchars($rl['description']) ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-semibold">Especialidades Profesionales a Asignar</label>
                        <div class="row g-2">
                            <?php foreach ($specialties as $sp): ?>
                                <?php if (($sp['status'] ?? 'ACTIVO') === 'ACTIVO'): ?>
                                    <div class="col-md-6">
                                        <div class="p-2 border rounded bg-light">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="specialties[]" value="<?= $sp['id'] ?>" id="new_sp_<?= $sp['id'] ?>">
                                                <label class="form-check-label small" for="new_sp_<?= $sp['id'] ?>">
                                                    <strong><?= htmlspecialchars($sp['name']) ?></strong><br>
                                                    <span class="font-monospace text-muted" style="font-size: 0.75rem;"><?= htmlspecialchars($sp['code']) ?></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-semibold">
                        <i class="bi bi-person-check-fill me-1"></i> Crear Trabajador y Ficha Laboral
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL CREAR ROL -->
<div class="modal fade" id="modalNewRole" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= url('/team/roles/store') ?>" method="POST">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-shield-plus me-2"></i>Crear Nuevo Rol del Sistema</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nombre del Rol *</label>
                        <input type="text" name="name" class="form-control font-monospace" required placeholder="EJ: SUPERVISOR_PRODUCCION">
                        <div class="form-text">Se normalizará a mayúsculas.</div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-semibold">Descripción del Rol *</label>
                        <textarea name="description" class="form-control" rows="3" required placeholder="Descripción de alcance, privilegios y responsabilidades..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-dark fw-semibold">Crear Rol</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL CREAR ESPECIALIDAD -->
<div class="modal fade" id="modalNewSpecialty" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= url('/team/specialties/store') ?>" method="POST">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-tag-fill me-2"></i>Crear Nueva Especialidad Operativa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Código Técnico *</label>
                        <input type="text" name="code" class="form-control font-monospace" required placeholder="EJ: COPYWRITER_CREATIVO">
                        <div class="form-text">Solo mayúsculas y guiones bajos (A-Z, 0-9, _).</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nombre del Cargo / Especialidad *</label>
                        <input type="text" name="name" class="form-control" required placeholder="Ej: Copywriter Creativo Inmobiliario">
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-semibold">Descripción de Tareas</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Descripción de herramientas asignadas y alcance..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success fw-semibold">Crear Especialidad</button>
                </div>
            </form>
        </div>
    </div>
</div>
