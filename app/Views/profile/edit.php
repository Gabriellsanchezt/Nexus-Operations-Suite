<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-person-gear text-primary me-2"></i>Editar Perfil de Usuario</h2>
            <p class="text-muted mb-0">Actualiza tus datos personales de contacto y cambia tu contraseña de acceso si es necesario</p>
        </div>
        <div>
            <a href="<?= url('/dashboard') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Volver al Dashboard
            </a>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- COLUMNA IZQUIERDA: RESUMEN DEL PERFIL ACTUAL -->
    <div class="col-12 col-lg-4">
        <!-- Tarjeta de Ficha de Usuario -->
        <div class="card border-0 shadow-sm mb-4 text-center overflow-hidden">
            <div class="bg-dark py-4 text-white position-relative" style="background: linear-gradient(135deg, #0d1b2a 0%, #1b263b 100%);">
                <div class="avatar-circle mx-auto mb-3 shadow" style="width: 80px; height: 80px; background: linear-gradient(135deg, #2563eb, #3b82f6); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: #fff; font-weight: 700; border: 3px solid rgba(255,255,255,0.2);">
                    <?= strtoupper(mb_substr($profile['full_name'] ?? 'U', 0, 1)) ?>
                </div>
                <h5 class="fw-bold mb-1"><?= htmlspecialchars($profile['full_name'] ?? 'Usuario') ?></h5>
                <span class="badge <?= ($profile['role_name'] === 'JEFE_ADMIN') ? 'bg-warning text-dark' : 'bg-primary' ?> px-3 py-1 text-uppercase fw-semibold" style="letter-spacing: 0.5px;">
                    <i class="bi bi-shield-lock-fill me-1"></i><?= htmlspecialchars($profile['role_name'] ?? 'EMPLEADO') ?>
                </span>
            </div>

            <div class="card-body p-4 text-start">
                <div class="mb-3 pb-3 border-bottom">
                    <span class="text-muted small text-uppercase fw-semibold d-block">Correo Electrónico</span>
                    <span class="fw-semibold text-dark"><i class="bi bi-envelope me-1 text-primary"></i><?= htmlspecialchars($profile['email']) ?></span>
                </div>

                <div class="mb-3 pb-3 border-bottom">
                    <span class="text-muted small text-uppercase fw-semibold d-block">Teléfono de Contacto</span>
                    <span class="fw-semibold text-dark"><i class="bi bi-telephone me-1 text-success"></i><?= htmlspecialchars($profile['phone'] ?: 'No registrado') ?></span>
                </div>

                <?php if (!empty($profile['hire_date'])): ?>
                    <div class="mb-3 pb-3 border-bottom">
                        <span class="text-muted small text-uppercase fw-semibold d-block">Fecha de Ingreso</span>
                        <span class="text-dark"><i class="bi bi-calendar-check me-1 text-secondary"></i><?= htmlspecialchars($profile['hire_date']) ?></span>
                    </div>
                <?php endif; ?>

                <div class="mb-3">
                    <span class="text-muted small text-uppercase fw-semibold d-block mb-2">Especialidades Asignadas</span>
                    <?php if (empty($profile['specialties'])): ?>
                        <span class="badge bg-light text-muted border">Sin especialidades específicas</span>
                    <?php else: ?>
                        <div class="d-flex flex-wrap gap-1">
                            <?php foreach ($profile['specialties'] as $spec): ?>
                                <span class="badge bg-light text-dark border p-2">
                                    <i class="bi bi-check-circle-fill text-success me-1"></i>
                                    <strong><?= htmlspecialchars($spec['name']) ?></strong>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Tarjeta de Seguridad y Privacidad -->
        <div class="card border-0 shadow-sm p-3 bg-light">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-success-subtle text-success p-2 rounded-circle fs-4 lh-1">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark">Protección de Cuenta</h6>
                    <small class="text-muted">Cifrado de claves mediante Bcrypt y sesiones seguras en cumplimiento con normas 3NF.</small>
                </div>
            </div>
        </div>
    </div>

    <!-- COLUMNA DERECHA: FORMULARIO DE EDICIÓN -->
    <div class="col-12 col-lg-8">
        <form action="<?= url('/profile') ?>" method="POST" id="editProfileForm">
            <!-- SECCIÓN 1: DATOS PERSONALES -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-person-lines-fill text-primary me-2"></i>Información Personal y Contacto
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="full_name" class="form-label fw-semibold text-dark">
                                Nombre Completo <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control border-start-0" id="full_name" name="full_name" 
                                       value="<?= htmlspecialchars($profile['full_name'] ?? '') ?>" required placeholder="Ej: Carlos Mendoza">
                            </div>
                            <small class="text-muted">Nombre visible en tareas, contratos y reportes ejecutivos.</small>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="phone" class="form-label fw-semibold text-dark">
                                Teléfono / Móvil Corporativo
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-telephone"></i></span>
                                <input type="text" class="form-control border-start-0" id="phone" name="phone" 
                                       value="<?= htmlspecialchars($profile['phone'] ?? '') ?>" placeholder="Ej: +1 (305) 555-0199">
                            </div>
                            <small class="text-muted">Canal para avisos y rodajes en locación.</small>
                        </div>

                        <div class="col-12">
                            <label for="email" class="form-label fw-semibold text-dark">
                                Correo Electrónico (Acceso al Sistema) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control border-start-0" id="email" name="email" 
                                       value="<?= htmlspecialchars($profile['email'] ?? '') ?>" required placeholder="nombre@miamiagency.com">
                            </div>
                            <small class="text-muted">Este correo es tu credencial para iniciar sesión en la plataforma.</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 2: CAMBIO DE CONTRASEÑA (OPCIONAL) -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-key-fill text-warning me-2"></i>Seguridad & Cambio de Contraseña
                    </h5>
                    <span class="badge bg-light text-muted border">Opcional</span>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-info border-0 shadow-sm py-2 px-3 mb-4 rounded-3 d-flex align-items-center gap-2">
                        <i class="bi bi-info-circle-fill fs-5 text-info"></i>
                        <small>
                            Si <strong>no deseas cambiar tu contraseña</strong>, simplemente deja estos tres campos en blanco y pulsa en "Guardar Cambios".
                        </small>
                    </div>

                    <div class="row g-3">
                        <!-- Contraseña Actual -->
                        <div class="col-12">
                            <label for="current_password" class="form-label fw-semibold text-dark">
                                Contraseña Actual <small class="text-muted fw-normal">(Requerida solo si vas a cambiar tu clave)</small>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control border-start-0 border-end-0" id="current_password" 
                                       name="current_password" placeholder="Ingresa tu clave actual para autorizar el cambio" autocomplete="current-password">
                                <button type="button" class="btn btn-outline-secondary border-start-0" onclick="togglePasswordVisibility('current_password', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Nueva Contraseña -->
                        <div class="col-12 col-md-6">
                            <label for="new_password" class="form-label fw-semibold text-dark">
                                Nueva Contraseña
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-shield-lock"></i></span>
                                <input type="password" class="form-control border-start-0 border-end-0" id="new_password" 
                                       name="new_password" placeholder="Mínimo 6 caracteres" autocomplete="new-password">
                                <button type="button" class="btn btn-outline-secondary border-start-0" onclick="togglePasswordVisibility('new_password', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <small class="text-muted" id="newPassHelp">Mínimo 6 caracteres alfanuméricos.</small>
                        </div>

                        <!-- Confirmar Nueva Contraseña -->
                        <div class="col-12 col-md-6">
                            <label for="confirm_password" class="form-label fw-semibold text-dark">
                                Confirmar Nueva Contraseña
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-shield-check"></i></span>
                                <input type="password" class="form-control border-start-0 border-end-0" id="confirm_password" 
                                       name="confirm_password" placeholder="Repite la nueva clave" autocomplete="new-password">
                                <button type="button" class="btn btn-outline-secondary border-start-0" onclick="togglePasswordVisibility('confirm_password', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <small class="text-muted" id="confirmPassHelp">Debe coincidir exactamente con la nueva clave.</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BOTONES DE ACCIÓN -->
            <div class="d-flex justify-content-between align-items-center">
                <a href="<?= url('/dashboard') ?>" class="btn btn-outline-secondary px-4">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm">
                    <i class="bi bi-check2-circle me-1"></i> Guardar Cambios en Mi Perfil
                </button>
            </div>
        </form>
    </div>
</div>

<script>
/**
 * Alternar visibilidad de contraseña (mostrar/ocultar)
 */
function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        if (icon) {
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        }
    } else {
        input.type = 'password';
        if (icon) {
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye-eye');
            icon.classList.add('bi-eye');
        }
    }
}

// Validación en tiempo real de coincidencia de contraseñas
document.addEventListener('DOMContentLoaded', () => {
    const newPassInput = document.getElementById('new_password');
    const confirmPassInput = document.getElementById('confirm_password');
    const confirmHelp = document.getElementById('confirmPassHelp');

    function checkMatch() {
        if (!newPassInput.value && !confirmPassInput.value) {
            confirmHelp.innerText = 'Debe coincidir exactamente con la nueva clave.';
            confirmHelp.className = 'text-muted';
            return;
        }

        if (confirmPassInput.value.length > 0) {
            if (newPassInput.value === confirmPassInput.value) {
                confirmHelp.innerHTML = '<span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Las contraseñas coinciden</span>';
            } else {
                confirmHelp.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle-fill me-1"></i>Las contraseñas no coinciden</span>';
            }
        }
    }

    if (newPassInput && confirmPassInput) {
        newPassInput.addEventListener('input', checkMatch);
        confirmPassInput.addEventListener('input', checkMatch);
    }
});
</script>
