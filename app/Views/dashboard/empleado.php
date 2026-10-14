<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1">¡Hola, <?= htmlspecialchars($user['full_name']) ?>!</h2>
            <p class="text-muted mb-0">Espacio de trabajo operativo &bull; <span class="badge bg-dark"><?= htmlspecialchars(\Config\Auth::getRoleLabel()) ?></span></p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= url('/activities/my-tasks') ?>" class="btn btn-primary fw-semibold">
                <i class="bi bi-play-circle-fill me-1"></i> Mis Tareas
            </a>
            <a href="<?= url('/contracts/final') ?>" class="btn btn-outline-danger fw-semibold">
                <i class="bi bi-file-earmark-pdf me-1"></i> Contratos Finales
            </a>
            <a href="<?= url('/email-marketing') ?>" class="btn btn-outline-success fw-semibold">
                <i class="bi bi-envelope-paper-fill me-1"></i> Email Marketing
            </a>
            <a href="<?= url('/agenda') ?>" class="btn btn-outline-dark fw-semibold">
                <i class="bi bi-calendar-event-fill me-1"></i> Agenda Reuniones
            </a>
        </div>
    </div>
</div>

<!-- Tarjeta de Perfil y Especialidades Activas -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="fw-bold mb-1 text-dark">Especialidades & Habilitaciones Activas</h5>
                <p class="text-muted small mb-3">Tus tareas asignadas y permisos corresponden a tus áreas de especialidad:</p>
                <div class="d-flex flex-wrap gap-2">
                    <?php if (!empty($user['specialties_details'])): ?>
                        <?php foreach ($user['specialties_details'] as $spec): ?>
                            <div class="badge bg-light text-dark border p-2 text-start">
                                <i class="bi bi-shield-check text-success me-1"></i>
                                <strong><?= htmlspecialchars($spec['name']) ?></strong>
                                <div class="small text-muted font-monospace"><?= htmlspecialchars($spec['code']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span class="text-muted small">Sin especialidades técnicas registradas.</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <div class="p-3 bg-light rounded text-center border">
                    <div class="small text-muted">Cuenta Corporativa</div>
                    <div class="fw-bold text-dark"><?= htmlspecialchars($user['email']) ?></div>
                    <span class="badge bg-success mt-2">SESIÓN ACTIVA</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TABLERO DE PRODUCCIÓN ESCALABLE -->
<?php
$activities = $myTasks ?? [];
require APP_PATH . '/Views/dashboard/partials/production_board.php';
?>

<!-- MINI CALENDARIO INTERACTIVO DE ACTIVIDADES DEL MES -->
<?php
$calendarActivities = $myTasks ?? [];
$isJefeView = false;
$calendarTitle = 'Mi Calendario de Actividades del Mes';
$calendarSubtitle = 'Fechas programadas de tus rodajes, entregas y fechas límite asignadas';
require APP_PATH . '/Views/dashboard/partials/mini_calendar.php';
?>
