<div class="container-fluid px-0">
    <!-- Encabezado Principal de Pantalla Amplia -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h1 class="module-title">
                <i class="bi bi-envelope-paper-heart-fill text-danger"></i> Email Marketing
            </h1>
            <p class="module-subtitle">Cronograma interactivo y fechas estipuladas de subida y envío por Realtor</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNewEmailCampaign">
                <i class="bi bi-calendar-plus-fill me-1"></i> + Programar Email para Realtor
            </button>
        </div>
    </div>

    <!-- KPIs Ejecutivos del Módulo (Estilo Dashboard Corporativo) -->
    <div class="row g-3 mb-4">
        <!-- Total Campañas -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card exec-kpi-card kpi-navy h-100">
                <div class="card-body p-3">
                    <span class="text-muted small fw-semibold text-uppercase">Total Campañas</span>
                    <div class="d-flex align-items-center justify-content-between mt-2">
                        <h3 class="fw-bold mb-0 text-dark"><?= $counts['total'] ?></h3>
                        <div class="exec-icon-box exec-icon-navy">
                            <i class="bi bi-envelope-paper-heart-fill"></i>
                        </div>
                    </div>
                    <span class="small text-muted mt-2 d-inline-block">Estrategias agendadas</span>
                </div>
            </div>
        </div>

        <!-- Programadas -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card exec-kpi-card kpi-steel h-100">
                <div class="card-body p-3">
                    <span class="text-muted small fw-semibold text-uppercase">Programadas</span>
                    <div class="d-flex align-items-center justify-content-between mt-2">
                        <h3 class="fw-bold mb-0 text-primary"><?= $counts['programados'] ?></h3>
                        <div class="exec-icon-box exec-icon-steel">
                            <i class="bi bi-clock-history"></i>
                        </div>
                    </div>
                    <span class="small text-muted mt-2 d-inline-block">En calendario estipulado</span>
                </div>
            </div>
        </div>

        <!-- Subidas -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card exec-kpi-card kpi-bronze h-100">
                <div class="card-body p-3">
                    <span class="text-muted small fw-semibold text-uppercase">Subidas</span>
                    <div class="d-flex align-items-center justify-content-between mt-2">
                        <h3 class="fw-bold mb-0 text-warning"><?= $counts['subidos'] ?></h3>
                        <div class="exec-icon-box exec-icon-bronze">
                            <i class="bi bi-cloud-arrow-up-fill"></i>
                        </div>
                    </div>
                    <span class="small text-muted mt-2 d-inline-block">Listas en plataforma</span>
                </div>
            </div>
        </div>

        <!-- Enviadas -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card exec-kpi-card kpi-sage h-100">
                <div class="card-body p-3">
                    <span class="text-muted small fw-semibold text-uppercase">Enviadas</span>
                    <div class="d-flex align-items-center justify-content-between mt-2">
                        <h3 class="fw-bold mb-0 text-success"><?= $counts['enviados'] ?></h3>
                        <div class="exec-icon-box exec-icon-sage">
                            <i class="bi bi-send-check-fill"></i>
                        </div>
                    </div>
                    <span class="small text-muted mt-2 d-inline-block">Distribuidas a clientes</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Barra de Filtros por Cliente Realtor -->
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
                    <i class="bi bi-grid-fill me-1"></i> Todos los Clientes (<?= $counts['total'] ?>)
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

    <!-- CALENDARIO INTERACTIVO DE EMAIL MARKETING (ESTILO DASHBOARD) -->
    <div class="card border-0 shadow-sm mb-4 overflow-hidden">
        <!-- Cabecera del Calendario -->
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle p-2 rounded-3">
                    <i class="bi bi-calendar3 fs-5"></i>
                </span>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Calendario de Email Marketing (Miami-Dade)</h5>
                    <small class="text-muted">Supervisión interactiva de fechas de subida y envío por cliente Realtor</small>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-dark rounded-pill px-3 py-2" id="emailCalTotalMonthBadge">
                    <?= count($campaigns) ?> campañas en vista
                </span>
                <button type="button" class="btn btn-sm btn-outline-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#modalNewEmailCampaign">
                    <i class="bi bi-plus-circle me-1"></i> Programar Envío
                </button>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="row g-0">
                <!-- COLUMNA IZQUIERDA: EL MINI CALENDARIO -->
                <div class="col-12 col-lg-5 border-end">
                    <div class="p-3 p-md-4">
                        <!-- Controles del Mes: Anterior, Mes/Año, Siguiente, Hoy -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <h6 class="fw-bold mb-0 text-dark text-capitalize" id="emailCalCurrentMonthLabel" style="font-size: 1.05rem;">
                                    Octubre 2026
                                </h6>
                                <button type="button" class="btn btn-sm btn-light border py-0 px-2 small text-muted" onclick="emailCalGoToday()">
                                    Hoy
                                </button>
                            </div>

                            <div class="d-flex gap-1">
                                <button type="button" class="calendar-nav-btn" onclick="emailCalPrevMonth()" title="Mes anterior">
                                    <i class="bi bi-chevron-left"></i>
                                </button>
                                <button type="button" class="calendar-nav-btn" onclick="emailCalNextMonth()" title="Mes siguiente">
                                    <i class="bi bi-chevron-right"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Cuadrícula de Días del Calendario -->
                        <div class="calendar-grid p-0 mb-3" id="emailCalGridDaysContainer">
                            <!-- Generado dinámicamente con JavaScript -->
                        </div>

                        <!-- Leyenda de Colores por Estado de Campaña -->
                        <div class="p-2 bg-light rounded-3 border small">
                            <div class="text-muted fw-bold mb-2 text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px;">
                                Convención de Estados de Email:
                            </div>
                            <div class="d-flex flex-wrap gap-2" style="font-size: 0.75rem;">
                                <span class="d-inline-flex align-items-center gap-1">
                                    <span class="event-dot dot-programado"></span> Programado
                                </span>
                                <span class="d-inline-flex align-items-center gap-1">
                                    <span class="event-dot dot-subido"></span> Subido a Plataforma
                                </span>
                                <span class="d-inline-flex align-items-center gap-1">
                                    <span class="event-dot dot-enviado"></span> Enviado al Cliente
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- COLUMNA DERECHA: AGENDA DETALLADA DE CAMPAÑAS -->
                <div class="col-12 col-lg-7 bg-light bg-opacity-50">
                    <div class="p-3 p-md-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="fw-bold mb-0 text-dark" id="emailCalScheduleHeaderTitle">
                                    <i class="bi bi-clock-history text-danger me-1"></i> Agenda de Campañas del Mes
                                </h6>
                                <small class="text-muted" id="emailCalScheduleSubtitle">
                                    Haz clic en cualquier día con campañas para filtrar por fecha
                                </small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary d-none" id="emailCalResetDayFilterBtn" onclick="emailCalResetFilter()">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Ver Todo el Mes
                            </button>
                        </div>

                        <!-- Lista Dinámica de Campañas Agendadas -->
                        <div class="activity-schedule-list p-0" id="emailCalListContainer">
                            <!-- Inyectado por JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- REGISTRO COMPLETO Y GESTIÓN EN TABLA -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-table text-primary me-2"></i>Fechas Estipuladas de Email Marketing
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
                        <label class="form-label small fw-semibold">Cliente Realtor</label>
                        <select name="realtor_id" class="form-select" required
                                data-purpose="Cliente Realtor destinatario de la campaña de correo."
                                data-error-msg="Selecciona el cliente Realtor para la campaña."
                                data-success-msg="Cliente vinculado para la campaña de email.">
                            <option value="">-- Seleccionar Realtor --</option>
                            <?php foreach ($realtors as $rel): ?>
                                <option value="<?= $rel['id'] ?>" <?= ($selectedRealtorId === (int)$rel['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($rel['company_name']) ?> (<?= htmlspecialchars($rel['contact_person']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Título o Asunto de la Campaña</label>
                        <input type="text" name="title" class="form-control" required placeholder="Ej: Newsletter Mensual: Oportunidades Brickell Pre-construcción"
                               data-purpose="Asunto principal o tema del envío de correo."
                               data-error-msg="Ingresa el asunto o título de la campaña."
                               data-success-msg="Asunto de la campaña registrado.">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Fecha Estipulada</label>
                            <input type="date" name="scheduled_date" class="form-control" required value="<?= date('Y-m-d') ?>"
                                   data-purpose="Fecha límite estipulada para la subida y envío."
                                   data-error-msg="Indica la fecha programada para el envío."
                                   data-success-msg="Fecha de envío agendada.">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Estado Inicial</label>
                            <select name="status" class="form-select"
                                    data-purpose="Fase en la que inicia la campaña en el cronograma."
                                    data-success-msg="Estado inicial configurado.">
                                <option value="PROGRAMADO">Programado</option>
                                <option value="SUBIDO">Subido</option>
                                <option value="ENVIADO">Enviado</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-semibold">Notas / Indicaciones del Contenido</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Público objetivo, enlaces a piezas o requisitos específicos del Realtor..."
                                  data-purpose="Opcional. Especificaciones técnicas, enlaces a creatividades o lista de distribución."
                                  data-success-msg="Indicaciones registradas."></textarea>
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

<!-- =========================================================================
     JAVASCRIPT: LÓGICA REACTIVA DEL CALENDARIO DE EMAIL MARKETING
     ========================================================================= -->
<script>
// Datos pasados desde PHP
const emailRawCampaigns = <?= json_encode($campaigns ?? []) ?>;
const emailBaseUrl = <?= json_encode(url('')) ?>;
const emailSelectedRealtorId = <?= json_encode($selectedRealtorId ?? '') ?>;

// Variables de estado del calendario
let emailCalCurrentYear = new Date().getFullYear();
let emailCalCurrentMonth = new Date().getMonth(); // 0-11
let emailCalSelectedDate = null; // null = ver todo el mes actual, o 'YYYY-MM-DD'

const emailMonthNamesEs = [
    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
];

const emailDayNamesHeader = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];

// Agrupar campañas por fecha (YYYY-MM-DD)
function getEmailCampaignsByDateMap() {
    const map = {};
    emailRawCampaigns.forEach(camp => {
        const d = camp.scheduled_date;
        if (!map[d]) {
            map[d] = [];
        }
        map[d].push(camp);
    });
    return map;
}

// Inicializar el calendario
function emailCalInit() {
    if (emailRawCampaigns.length > 0) {
        // Encontrar la primera campaña con fecha válida o fecha actual
        const firstValid = emailRawCampaigns.find(c => c.scheduled_date);
        if (firstValid) {
            const firstDate = new Date(firstValid.scheduled_date + 'T00:00:00');
            if (!isNaN(firstDate)) {
                emailCalCurrentYear = firstDate.getFullYear();
                emailCalCurrentMonth = firstDate.getMonth();
            }
        }
    }
    emailCalRender();
}

function emailCalPrevMonth() {
    emailCalCurrentMonth--;
    if (emailCalCurrentMonth < 0) {
        emailCalCurrentMonth = 11;
        emailCalCurrentYear--;
    }
    emailCalSelectedDate = null;
    emailCalRender();
}

function emailCalNextMonth() {
    emailCalCurrentMonth++;
    if (emailCalCurrentMonth > 11) {
        emailCalCurrentMonth = 0;
        emailCalCurrentYear++;
    }
    emailCalSelectedDate = null;
    emailCalRender();
}

function emailCalGoToday() {
    const today = new Date();
    emailCalCurrentYear = today.getFullYear();
    emailCalCurrentMonth = today.getMonth();
    emailCalSelectedDate = today.toISOString().split('T')[0];
    emailCalRender();
}

function emailCalResetFilter() {
    emailCalSelectedDate = null;
    emailCalRender();
}

function emailCalSelectDate(dateStr) {
    if (emailCalSelectedDate === dateStr) {
        // Toggle: si ya estaba seleccionado, desseleccionar para ver todo el mes
        emailCalSelectedDate = null;
    } else {
        emailCalSelectedDate = dateStr;
    }
    emailCalRender();
}

// Renderizar la cuadrícula del calendario y la lista de campañas
function emailCalRender() {
    // 1. Actualizar título del mes
    const monthLabel = document.getElementById('emailCalCurrentMonthLabel');
    if (monthLabel) {
        monthLabel.innerText = `${emailMonthNamesEs[emailCalCurrentMonth]} ${emailCalCurrentYear}`;
    }

    const grid = document.getElementById('emailCalGridDaysContainer');
    if (!grid) return;
    grid.innerHTML = '';

    // Renderizar cabeceras de días de la semana (Lun, Mar, ...)
    emailDayNamesHeader.forEach(dName => {
        const hCell = document.createElement('div');
        hCell.className = 'calendar-weekday';
        hCell.innerText = dName;
        grid.appendChild(hCell);
    });

    // Calcular días del mes
    const firstDayIndex = new Date(emailCalCurrentYear, emailCalCurrentMonth, 1).getDay(); // 0 = Domingo, 1 = Lunes
    const adjustedFirstDay = (firstDayIndex === 0) ? 6 : (firstDayIndex - 1); // 0 = Lunes, 6 = Domingo
    const totalDaysInMonth = new Date(emailCalCurrentYear, emailCalCurrentMonth + 1, 0).getDate();

    // Días vacíos al inicio del mes
    for (let i = 0; i < adjustedFirstDay; i++) {
        const emptyCell = document.createElement('div');
        emptyCell.className = 'calendar-day empty-day';
        grid.appendChild(emptyCell);
    }

    const todayStr = new Date().toISOString().split('T')[0];
    const campMap = getEmailCampaignsByDateMap();

    // Días del mes (1 ... N)
    for (let day = 1; day <= totalDaysInMonth; day++) {
        const dateStr = `${emailCalCurrentYear}-${String(emailCalCurrentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const dayCell = document.createElement('div');
        dayCell.className = 'calendar-day';
        dayCell.innerText = day;

        if (dateStr === todayStr) {
            dayCell.classList.add('today');
        }

        if (emailCalSelectedDate === dateStr) {
            dayCell.classList.add('selected');
        }

        // Si hay campañas en este día
        if (campMap[dateStr] && campMap[dateStr].length > 0) {
            dayCell.classList.add('has-events');

            const dotsContainer = document.createElement('div');
            dotsContainer.className = 'calendar-event-dots';

            // Mostrar hasta 3 puntitos indicativos
            campMap[dateStr].slice(0, 3).forEach(camp => {
                const dot = document.createElement('span');
                dot.className = 'event-dot';

                if (camp.status === 'ENVIADO') {
                    dot.classList.add('dot-enviado');
                } else if (camp.status === 'SUBIDO') {
                    dot.classList.add('dot-subido');
                } else {
                    dot.classList.add('dot-programado');
                }

                dotsContainer.appendChild(dot);
            });

            dayCell.appendChild(dotsContainer);
        }

        dayCell.onclick = () => emailCalSelectDate(dateStr);
        grid.appendChild(dayCell);
    }

    // 2. Renderizar lista de campañas a la derecha
    emailCalRenderCampaignsList(campMap);
}

function emailCalRenderCampaignsList(campMap) {
    const listContainer = document.getElementById('emailCalListContainer');
    const headerTitle = document.getElementById('emailCalScheduleHeaderTitle');
    const subtitle = document.getElementById('emailCalScheduleSubtitle');
    const resetBtn = document.getElementById('emailCalResetDayFilterBtn');

    if (!listContainer) return;

    let filteredCampaigns = [];

    if (emailCalSelectedDate) {
        // Filtrado por fecha específica
        filteredCampaigns = campMap[emailCalSelectedDate] || [];
        const dateObj = new Date(emailCalSelectedDate + 'T00:00:00');
        const formattedDate = dateObj.toLocaleDateString('es-ES', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });

        if (headerTitle) {
            headerTitle.innerHTML = `<i class="bi bi-calendar-check-fill text-danger me-1"></i> ${formattedDate}`;
        }
        if (subtitle) {
            subtitle.innerText = `${filteredCampaigns.length} campaña(s) agendada(s) para este día`;
        }
        if (resetBtn) {
            resetBtn.classList.remove('d-none');
        }
    } else {
        // Todo el mes actual visible
        const currentMonthPrefix = `${emailCalCurrentYear}-${String(emailCalCurrentMonth + 1).padStart(2, '0')}`;
        filteredCampaigns = emailRawCampaigns.filter(c => c.scheduled_date && c.scheduled_date.startsWith(currentMonthPrefix));

        if (headerTitle) {
            headerTitle.innerHTML = `<i class="bi bi-clock-history text-danger me-1"></i> Agenda de ${emailMonthNamesEs[emailCalCurrentMonth]} ${emailCalCurrentYear}`;
        }
        if (subtitle) {
            subtitle.innerText = `Mostrando ${filteredCampaigns.length} campaña(s) programadas en este ciclo mensual`;
        }
        if (resetBtn) {
            resetBtn.classList.add('d-none');
        }
    }

    // Actualizar badge superior
    const totalMonth = emailRawCampaigns.filter(c => c.scheduled_date && c.scheduled_date.startsWith(`${emailCalCurrentYear}-${String(emailCalCurrentMonth + 1).padStart(2, '0')}`)).length;
    const badgeMonth = document.getElementById('emailCalTotalMonthBadge');
    if (badgeMonth) {
        badgeMonth.innerText = `${totalMonth} campañas en ${emailMonthNamesEs[emailCalCurrentMonth]}`;
    }

    listContainer.innerHTML = '';

    if (filteredCampaigns.length === 0) {
        listContainer.innerHTML = `
            <div class="text-center py-5 bg-white rounded-3 border">
                <i class="bi bi-envelope-check text-muted display-6 d-block mb-2"></i>
                <h6 class="fw-bold text-dark mb-1">Sin campañas para esta fecha</h6>
                <p class="text-muted small mb-0">No hay envíos ni subidas de email programadas.</p>
            </div>
        `;
        return;
    }

    filteredCampaigns.forEach(camp => {
        let borderClass = 'border-programado';
        let statusBadgeClass = 'bg-primary text-white';

        if (camp.status === 'ENVIADO') {
            borderClass = 'border-enviado';
            statusBadgeClass = 'bg-success text-white';
        } else if (camp.status === 'SUBIDO') {
            borderClass = 'border-subido';
            statusBadgeClass = 'bg-warning text-dark';
        }

        const notesHtml = camp.notes 
            ? `<div class="small text-secondary bg-light p-2 rounded mt-2 border"><i class="bi bi-info-circle me-1 text-primary"></i>${escapeHtml(camp.notes)}</div>` 
            : '';

        const item = document.createElement('div');
        item.className = `activity-schedule-item ${borderClass}`;

        item.innerHTML = `
            <div class="d-flex justify-content-between align-items-start mb-1 flex-wrap gap-1">
                <div>
                    <span class="badge ${statusBadgeClass} small fw-bold">${camp.status}</span>
                    <span class="badge bg-light text-dark border font-monospace small ms-1">
                        <i class="bi bi-calendar-event me-1"></i>${camp.scheduled_date}
                    </span>
                </div>
                <form action="${emailBaseUrl}/email-marketing/delete" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta campaña programada?')">
                    <input type="hidden" name="id" value="${camp.id}">
                    <input type="hidden" name="realtor_id" value="${emailSelectedRealtorId}">
                    <button type="submit" class="btn btn-sm btn-link text-danger p-0 border-0" title="Eliminar campaña">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>

            <h6 class="fw-bold text-dark mb-1 mt-2">${escapeHtml(camp.title)}</h6>

            <div class="small text-muted mb-1">
                <i class="bi bi-building me-1 text-primary"></i><strong>${escapeHtml(camp.company_name)}</strong> &bull; 
                <i class="bi bi-person me-1"></i>${escapeHtml(camp.contact_person)} &bull; 
                <i class="bi bi-geo-alt text-danger"></i>${escapeHtml(camp.miami_zone)}
            </div>

            ${notesHtml}

            <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top flex-wrap gap-2">
                <small class="text-muted">Cambiar estado de ejecución:</small>
                <form action="${emailBaseUrl}/email-marketing/update-status" method="POST" class="d-inline">
                    <input type="hidden" name="id" value="${camp.id}">
                    <input type="hidden" name="realtor_id" value="${emailSelectedRealtorId}">
                    <select name="status" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                        <option value="PROGRAMADO" ${camp.status === 'PROGRAMADO' ? 'selected' : ''}>Programado</option>
                        <option value="SUBIDO" ${camp.status === 'SUBIDO' ? 'selected' : ''}>Subido</option>
                        <option value="ENVIADO" ${camp.status === 'ENVIADO' ? 'selected' : ''}>Enviado</option>
                    </select>
                </form>
            </div>
        `;

        listContainer.appendChild(item);
    });
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>"']/g, function(m) {
        switch (m) {
            case '&': return '&amp;';
            case '<': return '&lt;';
            case '>': return '&gt;';
            case '"': return '&quot;';
            case "'": return '&#039;';
            default: return m;
        }
    });
}

// Iniciar al cargar el DOM
document.addEventListener('DOMContentLoaded', () => {
    emailCalInit();
});
</script>
