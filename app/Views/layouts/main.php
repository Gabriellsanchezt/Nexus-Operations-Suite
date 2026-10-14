<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Miami Agency Operations') ?></title>
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS (con cache-busting dinámico) -->
    <link rel="stylesheet" href="<?= asset_url('/assets/css/style.css') ?>">
    <script>
        window.APP_BASE_URL = "<?= defined('BASE_URL') ? BASE_URL : '' ?>";
        window.appUrl = function(path) {
            path = '/' + path.replace(/^\/+/, '');
            return window.APP_BASE_URL + path;
        };
    </script>
</head>
<body class="bg-light">

<?php
// Obtener ruta actual normalizada para resaltar enlace activo en el sidebar
$rawUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
foreach (['/miami-agency-ops/public', '/miami-agency-ops'] as $prefix) {
    if (str_starts_with($rawUri, $prefix)) {
        $rawUri = substr($rawUri, strlen($prefix));
        break;
    }
}
$currentRoute = '/' . ltrim($rawUri, '/');
if ($currentRoute === '//' || $currentRoute === '') $currentRoute = '/dashboard';
$currentStatusParam = $_GET['status'] ?? '';
?>

<div class="app-layout">
    <!-- Backdrop oscuro para móvil al abrir el slider bar -->
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

    <!-- =========================================================================
         SLIDER BAR (SIDEBAR / BARRA LATERAL DESLIZABLE Y FIJA)
         ========================================================================= -->
    <aside class="app-sidebar" id="appSidebar">
        <!-- 1. Cabecera del Sidebar con Marca -->
        <div class="app-sidebar-header">
            <a href="<?= url('/dashboard') ?>" class="sidebar-brand">
                <div class="sidebar-brand-badge">
                    <i class="bi bi-camera-reels-fill"></i>
                </div>
                <div>
                    <div class="sidebar-brand-text">MIAMI LUXURY</div>
                    <span class="sidebar-brand-sub">MEDIA & OPERATIONS</span>
                </div>
            </a>
            <!-- Botón de cerrar barra lateral (disponible en laptop, desktop y móvil) -->
            <button type="button" class="btn btn-sm btn-sidebar-close" onclick="toggleSidebar()" title="Cerrar / Ocultar barra lateral">
                <i class="bi bi-x-lg fs-6"></i>
            </button>
        </div>

        <!-- 2. Perfil de Usuario en el Sidebar -->
        <div class="app-sidebar-user">
            <div class="d-flex align-items-center gap-3">
                <div class="sidebar-user-avatar">
                    <?= strtoupper(substr($currentUser['full_name'] ?? 'U', 0, 1)) ?>
                </div>
                <div class="overflow-hidden">
                    <div class="fw-bold text-white small text-truncate"><?= htmlspecialchars($currentUser['full_name'] ?? 'Usuario') ?></div>
                        <?php 
                        $userRole = $currentUser['role'] ?? '';
                        $roleBadgeClass = match($userRole) {
                            'JEFE', 'JEFE_ADMIN' => 'bg-danger text-white',
                            'ASISTENTE' => 'bg-primary text-white',
                            'ESTRATEGA_MARKETING' => 'bg-success text-white',
                            'SOCIAL_MANAGER' => 'bg-info text-dark',
                            default => 'bg-secondary text-white'
                        };
                        ?>
                        <span class="badge <?= $roleBadgeClass ?> text-uppercase" style="font-size: 0.65rem;"><?= htmlspecialchars(\Config\Auth::getRoleLabel($userRole)) ?></span>
                </div>
            </div>
            <?php if (!empty($currentUser['specialties'])): ?>
                <div class="d-flex flex-wrap gap-1 mt-2">
                    <?php foreach ($currentUser['specialties'] as $spec): ?>
                        <span class="badge bg-secondary bg-opacity-50 text-light" style="font-size: 0.62rem;"><?= htmlspecialchars($spec) ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- 3. Enlaces de Navegación del Sidebar -->
        <div class="sidebar-nav-container">
            <!-- 1. Panel General -->
            <div class="sidebar-heading">Panel Principal</div>
            <a href="<?= url('/dashboard') ?>" class="sidebar-link <?= ($currentRoute === '/dashboard' || $currentRoute === '/') ? 'active' : '' ?>">
                <div class="sidebar-link-content">
                    <i class="bi bi-speedometer2 sidebar-link-icon"></i>
                    <span>Dashboard</span>
                </div>
            </a>

            <!-- 2. Producción Creativa & Audiovisual -->
            <div class="sidebar-heading">Producción Audiovisual</div>
            <?php if (\Config\Auth::isJefe()): ?>
                <a href="<?= url('/activities') ?>" class="sidebar-link <?= ($currentRoute === '/activities' && empty($currentStatusParam)) ? 'active' : '' ?>">
                    <div class="sidebar-link-content">
                        <i class="bi bi-kanban sidebar-link-icon"></i>
                        <span>Tablero Producción</span>
                    </div>
                </a>
                <a href="<?= url('/activities?status=APROBADA') ?>" class="sidebar-link <?= ($currentRoute === '/activities' && $currentStatusParam === 'APROBADA') ? 'active' : '' ?>">
                    <div class="sidebar-link-content">
                        <i class="bi bi-patch-check-fill sidebar-link-icon"></i>
                        <span>Actividades Culminadas</span>
                    </div>
                    <span class="badge sidebar-pill-badge">Aprobadas</span>
                </a>
            <?php else: ?>
                <a href="<?= url('/activities/my-tasks') ?>" class="sidebar-link <?= ($currentRoute === '/activities/my-tasks' && empty($currentStatusParam)) ? 'active' : '' ?>">
                    <div class="sidebar-link-content">
                        <i class="bi bi-play-circle-fill sidebar-link-icon"></i>
                        <span>Mis Tareas</span>
                    </div>
                </a>
                <a href="<?= url('/activities/my-tasks?status=APROBADA') ?>" class="sidebar-link <?= ($currentRoute === '/activities/my-tasks' && $currentStatusParam === 'APROBADA') ? 'active' : '' ?>">
                    <div class="sidebar-link-content">
                        <i class="bi bi-patch-check-fill sidebar-link-icon"></i>
                        <span>Mis Tareas Culminadas</span>
                    </div>
                    <span class="badge sidebar-pill-badge">Aprobadas</span>
                </a>
                <a href="<?= url('/activities') ?>" class="sidebar-link <?= ($currentRoute === '/activities' && empty($currentStatusParam)) ? 'active' : '' ?>">
                    <div class="sidebar-link-content">
                        <i class="bi bi-kanban sidebar-link-icon"></i>
                        <span>Tablero General</span>
                    </div>
                </a>
            <?php endif; ?>

            <!-- 3. Marketing & Agenda -->
            <div class="sidebar-heading">Marketing & Agenda</div>
            <a href="<?= url('/email-marketing') ?>" class="sidebar-link <?= ($currentRoute === '/email-marketing') ? 'active' : '' ?>">
                <div class="sidebar-link-content">
                    <i class="bi bi-envelope-paper-fill sidebar-link-icon"></i>
                    <span>Email Marketing</span>
                </div>
                <span class="badge sidebar-pill-badge">Cronograma</span>
            </a>
            <a href="<?= url('/agenda') ?>" class="sidebar-link <?= ($currentRoute === '/agenda' || $currentRoute === '/meetings') ? 'active' : '' ?>">
                <div class="sidebar-link-content">
                    <i class="bi bi-calendar-check-fill sidebar-link-icon"></i>
                    <span>Agenda de Reuniones</span>
                </div>
                <span class="badge sidebar-pill-badge">Google Sync</span>
            </a>

            <!-- 4. Directorio & Equipo -->
            <div class="sidebar-heading">Directorio & Equipo</div>
            <a href="<?= url('/realtors') ?>" class="sidebar-link <?= ($currentRoute === '/realtors') ? 'active' : '' ?>">
                <div class="sidebar-link-content">
                    <i class="bi bi-building sidebar-link-icon"></i>
                    <span>Realtors Miami</span>
                </div>
            </a>
            <?php if (\Config\Auth::isJefe()): ?>
                <a href="<?= url('/team') ?>" class="sidebar-link <?= ($currentRoute === '/team') ? 'active' : '' ?>">
                    <div class="sidebar-link-content">
                        <i class="bi bi-people sidebar-link-icon"></i>
                        <span>Equipo & Roles</span>
                    </div>
                </a>
            <?php endif; ?>

            <!-- 5. Contratos Finales (Al final antes de Reportes) -->
            <div class="sidebar-heading">Documentación</div>
            <a href="<?= url('/contracts/final') ?>" class="sidebar-link <?= ($currentRoute === '/contracts/final') ? 'active' : '' ?>">
                <div class="sidebar-link-content">
                    <i class="bi bi-file-earmark-pdf-fill sidebar-link-icon"></i>
                    <span>Contratos Finales</span>
                </div>
                <span class="badge sidebar-pill-badge bg-danger text-white">PDF</span>
            </a>

            <!-- 6. Analítica & Reportes (Solo Jefe) -->
            <?php if (\Config\Auth::isJefe()): ?>
                <div class="sidebar-heading">Analítica & Supervisión</div>
                <a href="<?= url('/reports') ?>" class="sidebar-link <?= ($currentRoute === '/reports') ? 'active' : '' ?>">
                    <div class="sidebar-link-content">
                        <i class="bi bi-graph-up-arrow sidebar-link-icon"></i>
                        <span>Reportes Ejecutivos</span>
                    </div>
                    <span class="badge sidebar-pill-badge">15 / 30 Días</span>
                </a>
            <?php endif; ?>
        </div>

        <!-- 4. Pie del Sidebar -->
        <div class="app-sidebar-footer">
            <div class="d-flex align-items-center justify-content-between mb-2 text-muted small" style="font-size: 0.72rem;">
                <span class="d-flex align-items-center gap-1">
                    <span class="pulse-dot"></span> 3NF Protegido
                </span>
                <span class="font-monospace">Miami-Dade</span>
            </div>
            <a href="<?= url('/logout') ?>" class="btn-sidebar-logout" title="Cerrar sesión en este equipo">
                <i class="bi bi-box-arrow-right"></i>
                <span>Cerrar Sesión</span>
            </a>
        </div>
    </aside>

    <!-- =========================================================================
         CONTENEDOR PRINCIPAL DERECHO (TOPBAR + CONTENIDO)
         ========================================================================= -->
    <div class="app-main-wrapper" id="appMainWrapper">
        <!-- Topbar Superior Slim -->
        <header class="app-topbar">
            <div class="d-flex align-items-center gap-3">
                <!-- Botón Toggle Deslizable (Slider) -->
                <button type="button" class="btn-sidebar-toggle" id="sidebarToggleBtn" onclick="toggleSidebar()" title="Mostrar u Ocultar Barra Lateral">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div>
                    <span class="text-muted small d-none d-sm-inline">Agencia &bull; Miami Luxury Operations</span>
                    <h6 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($pageTitle ?? 'Panel de Control') ?></h6>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <!-- Fecha y Hora en Tiempo Real (Hora Oficial de Venezuela UTC-4) -->
                <?php
                    $nowVe = new DateTime('now', new DateTimeZone('America/Caracas'));
                    $diasVe = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
                    $mesesVe = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                    $diaSemanaVe = $diasVe[(int)$nowVe->format('w')];
                    $diaNumVe = $nowVe->format('j');
                    $mesNomVe = $mesesVe[(int)$nowVe->format('n')];
                    $anioNumVe = $nowVe->format('Y');
                    $horaVe = $nowVe->format('h:i:s A');
                    $fechaTextoVe = "{$diaSemanaVe}, {$diaNumVe} {$mesNomVe} {$anioNumVe}";
                ?>
                <span class="badge bg-light text-dark border px-3 py-2 d-none d-md-inline-flex align-items-center gap-2" 
                      id="liveVenezuelaClockBadge" 
                      title="Hora Oficial de Venezuela (UTC-4)">
                    <i class="bi bi-clock-history text-primary fs-6"></i>
                    <span id="liveVenezuelaClockText">
                        <?= $fechaTextoVe ?> &bull; <strong class="text-primary font-monospace"><?= $horaVe ?></strong>
                    </span>
                </span>

                <!-- Botón de Notificaciones Interactivas (Popover de Bootstrap) -->
                <?php 
                    $unreadNotifCount = count($systemNotifications ?? []); 
                ?>
                <div class="position-relative">
                    <button class="btn btn-sm btn-outline-secondary position-relative d-flex align-items-center justify-content-center p-2 rounded-circle" 
                            id="btnNotificationPopover" 
                            type="button"
                            title="Notificaciones del Sistema"
                            aria-label="Notificaciones del Sistema">
                        <i class="bi bi-bell<?= $unreadNotifCount > 0 ? '-fill text-warning' : '' ?> fs-6" id="notifBellIcon"></i>
                        <span id="notifBellBadge" 
                              class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light notif-badge-count <?= $unreadNotifCount === 0 ? 'd-none' : '' ?>" 
                              style="font-size: 0.65rem;">
                            <?= $unreadNotifCount ?>
                            <span class="visually-hidden">notificaciones pendientes</span>
                        </span>
                    </button>
                </div>

                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle fs-6"></i>
                        <span class="d-none d-sm-inline"><?= htmlspecialchars($currentUser['full_name'] ?? 'Usuario') ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><h6 class="dropdown-header small text-uppercase"><?= htmlspecialchars($currentUser['role'] ?? '') ?></h6></li>
                        <li>
                            <a class="dropdown-item <?= ($currentRoute === '/profile') ? 'active' : '' ?>" href="<?= url('/profile') ?>">
                                <i class="bi bi-person-gear me-2 text-primary"></i>Editar Perfil
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= url('/logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Plantillas Ocultas para el Popover de Notificaciones (Bootstrap) -->
        <div id="notificationPopoverTitle" class="d-none">
            <div class="d-flex align-items-center justify-content-between w-100 py-1">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-bold text-dark small text-uppercase">
                        <i class="bi bi-bell-fill text-warning me-1"></i> Notificaciones
                    </span>
                    <span class="badge bg-danger rounded-pill notif-badge-count <?= $unreadNotifCount === 0 ? 'd-none' : '' ?>" id="notifHeaderCount">
                        <?= $unreadNotifCount ?>
                    </span>
                </div>
                <button type="button" 
                        class="btn btn-link btn-sm text-decoration-none p-0 text-muted small fw-normal btn-mark-all-read <?= $unreadNotifCount === 0 ? 'd-none' : '' ?>" 
                        title="Marcar todas como vistas">
                    <i class="bi bi-check2-all me-1 text-primary"></i>Marcar todas
                </button>
            </div>
        </div>

        <div id="notificationPopoverContent" class="d-none">
            <div class="notification-popover-wrapper" id="notifPopoverWrapper" style="width: 340px; max-width: 85vw;">
                <div class="notif-empty-state text-center py-4 text-muted <?= $unreadNotifCount > 0 ? 'd-none' : '' ?>">
                    <i class="bi bi-check2-circle text-success fs-2 d-block mb-2"></i>
                    <div class="fw-semibold small">¡Todo al día!</div>
                    <div class="text-muted" style="font-size: 0.75rem;">Todas las notificaciones han sido vistas.</div>
                </div>

                <div class="list-group list-group-flush rounded-3 notif-list-container <?= $unreadNotifCount === 0 ? 'd-none' : '' ?>">
                    <?php foreach (($systemNotifications ?? []) as $notif): ?>
                        <div class="list-group-item list-group-item-action p-2 border-bottom d-flex align-items-start gap-2 notif-item" data-notif-id="<?= htmlspecialchars($notif['id']) ?>">
                            <div class="p-2 rounded-circle bg-<?= $notif['type'] ?> bg-opacity-10 text-<?= $notif['type'] ?> fs-5 lh-1 flex-shrink-0 mt-1">
                                <i class="bi <?= $notif['icon'] ?>"></i>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <a href="<?= htmlspecialchars($notif['link']) ?>" class="fw-bold small text-dark text-truncate text-decoration-none">
                                        <?= htmlspecialchars($notif['title']) ?>
                                    </a>
                                    <span class="badge bg-<?= $notif['type'] ?> bg-opacity-25 text-<?= $notif['type'] ?> border border-<?= $notif['type'] ?> border-opacity-25 rounded-pill" style="font-size: 0.65rem;">
                                        <?= htmlspecialchars($notif['badge']) ?>
                                    </span>
                                </div>
                                <p class="mb-1 text-muted small lh-sm" style="font-size: 0.78rem;">
                                    <?= htmlspecialchars($notif['message']) ?>
                                </p>
                                <div class="d-flex align-items-center justify-content-between mt-1">
                                    <a href="<?= htmlspecialchars($notif['link']) ?>" class="text-primary fw-semibold d-inline-flex align-items-center gap-1 text-decoration-none" style="font-size: 0.72rem;">
                                        <?= htmlspecialchars($notif['action_text'] ?? 'Ver detalles') ?> &rarr;
                                    </a>
                                    <span class="text-muted" style="font-size: 0.68rem;"><?= htmlspecialchars($notif['time'] ?? '') ?></span>
                                </div>
                            </div>
                            <button type="button" 
                                    class="btn btn-sm btn-outline-secondary btn-mark-notif-read border-0 p-1 rounded-circle flex-shrink-0 text-muted" 
                                    data-notif-id="<?= htmlspecialchars($notif['id']) ?>" 
                                    title="Marcar como vista">
                                <i class="bi bi-check-lg fs-5"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="p-2 bg-light text-center border-top rounded-bottom">
                    <small class="text-muted" style="font-size: 0.72rem;">
                        <i class="bi bi-shield-check text-success me-1"></i> Miami Agency Operations
                    </small>
                </div>
            </div>
        </div>

        <!-- Notificaciones Dinámicas Flotantes (Bootstrap Toasts - No estáticas) -->
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1090;">
            <?php if (!empty($_SESSION['flash_success'])): ?>
                <div class="toast align-items-center text-bg-success border-0 shadow-lg mb-2" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
                    <div class="d-flex">
                        <div class="toast-body d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill fs-5"></i>
                            <div><?= htmlspecialchars($_SESSION['flash_success']) ?></div>
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
                <?php unset($_SESSION['flash_success']); ?>
            <?php endif; ?>

            <?php if (!empty($_SESSION['flash_error'])): ?>
                <div class="toast align-items-center text-bg-danger border-0 shadow-lg mb-2" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="7000">
                    <div class="d-flex">
                        <div class="toast-body d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                            <div><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
                <?php unset($_SESSION['flash_error']); ?>
            <?php endif; ?>

            <?php if (!empty($_SESSION['flash_warning'])): ?>
                <div class="toast align-items-center text-bg-warning border-0 shadow-lg mb-2" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="6000">
                    <div class="d-flex">
                        <div class="toast-body d-flex align-items-center gap-2 text-dark">
                            <i class="bi bi-exclamation-circle-fill fs-5"></i>
                            <div><?= htmlspecialchars($_SESSION['flash_warning']) ?></div>
                        </div>
                        <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
                <?php unset($_SESSION['flash_warning']); ?>
            <?php endif; ?>

            <?php if (!empty($_SESSION['flash_info'])): ?>
                <div class="toast align-items-center text-bg-info border-0 shadow-lg mb-2" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
                    <div class="d-flex">
                        <div class="toast-body d-flex align-items-center gap-2 text-dark">
                            <i class="bi bi-info-circle-fill fs-5"></i>
                            <div><?= htmlspecialchars($_SESSION['flash_info']) ?></div>
                        </div>
                        <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
                <?php unset($_SESSION['flash_info']); ?>
            <?php endif; ?>
        </div>

        <!-- Contenido Principal -->
        <main class="container-fluid px-4 py-3 flex-grow-1">
            <?= $content ?>
        </main>

        <!-- Pie de Página -->
        <footer class="bg-white border-top py-3 mt-auto text-center text-muted small">
            <div class="container-fluid px-4">
                Miami Luxury Media Agency &copy; <?= date('Y') ?> | Operaciones Inmobiliarias, Contratos & Producción Audiovisual (Miami-Dade, FL)
            </div>
        </footer>
    </div>
</div>

<script>
    window.APP_CONFIG = {
        baseUrl: '<?= url('') ?>',
        markReadUrl: '<?= url('/api/notifications/mark-read') ?>'
    };
</script>
<!-- Bootstrap 5.3.3 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom Application JS (con cache-busting dinámico) -->
<script src="<?= asset_url('/assets/js/app.js') ?>"></script>

<!-- Script para el control del Slider Bar en Laptop, Desktop y Móvil -->
<script>
function toggleSidebar() {
    const sidebar = document.getElementById('appSidebar');
    const mainWrapper = document.getElementById('appMainWrapper');
    const backdrop = document.getElementById('sidebarBackdrop');
    const isDesktop = window.innerWidth >= 992;

    if (isDesktop) {
        // En Laptop y Pantallas Grandes: Alternar estado colapsado / expandido
        const isCollapsed = sidebar.classList.toggle('sidebar-collapsed');
        if (mainWrapper) {
            mainWrapper.classList.toggle('main-expanded', isCollapsed);
        }
        try {
            localStorage.setItem('sidebar_collapsed_pref', isCollapsed ? '1' : '0');
        } catch(e) {}
    } else {
        // En Móvil y Tablets: Deslizar con telón de fondo
        const isOpen = sidebar.classList.toggle('sidebar-open');
        if (backdrop) {
            backdrop.classList.toggle('show', isOpen);
        }
    }
}

// Restaurar preferencia en Laptop / Desktop al cargar la página
document.addEventListener('DOMContentLoaded', () => {
    if (window.innerWidth >= 992) {
        try {
            if (localStorage.getItem('sidebar_collapsed_pref') === '1') {
                const sidebar = document.getElementById('appSidebar');
                const mainWrapper = document.getElementById('appMainWrapper');
                if (sidebar) sidebar.classList.add('sidebar-collapsed');
                if (mainWrapper) mainWrapper.classList.add('main-expanded');
            }
        } catch(e) {}
    }
});

// Reloj interactivo en tiempo real con Zona Horaria de Venezuela (America/Caracas, UTC-4)
function updateVenezuelaClock() {
    const el = document.getElementById('liveVenezuelaClockText');
    if (!el) return;

    const now = new Date();
    try {
        const dateOptions = {
            timeZone: 'America/Caracas',
            weekday: 'short',
            day: 'numeric',
            month: 'short',
            year: 'numeric'
        };
        const timeOptions = {
            timeZone: 'America/Caracas',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true
        };

        const dateStr = new Intl.DateTimeFormat('es-VE', dateOptions).format(now);
        const timeStr = new Intl.DateTimeFormat('es-VE', timeOptions).format(now);
        const formattedDate = dateStr.charAt(0).toUpperCase() + dateStr.slice(1);

        el.innerHTML = `${formattedDate} &bull; <strong class="text-primary font-monospace">${timeStr}</strong>`;
    } catch (e) {}
}

setInterval(updateVenezuelaClock, 1000);
</script>

</body>
</html>
