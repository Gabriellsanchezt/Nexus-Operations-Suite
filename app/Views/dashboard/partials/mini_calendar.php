<?php
/**
 * Componente Parcial: Mini Calendario Interactivo de Actividades del Mes
 * Compatible con Vista del Jefe (todas las actividades) y Vista de Empleados (sus actividades asignadas)
 *
 * Variables esperadas:
 * - $calendarActivities: array con las actividades a mostrar
 * - $isJefeView: boolean indicando si es vista de Jefe o de Empleado
 * - $calendarTitle: string con título del módulo
 * - $calendarSubtitle: string con subtítulo descriptivo
 */

$isJefe = !empty($isJefeView);
$title = $calendarTitle ?? ($isJefe ? 'Calendario Operativo de la Agencia (Miami-Dade)' : 'Mi Calendario de Producción del Mes');
$subtitle = $calendarSubtitle ?? ($isJefe ? 'Supervisión de rodajes, entregas y fechas límite de todo el equipo creativo' : 'Fechas programadas de rodajes, entregas y fechas límite asignadas a tu perfil');
$activitiesJson = json_encode($calendarActivities ?? []);
?>

<div class="card border-0 shadow-sm mb-4 overflow-hidden">
    <!-- Cabecera del Módulo -->
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle p-2 rounded-3">
                <i class="bi bi-calendar-event-fill fs-5"></i>
            </span>
            <div>
                <h5 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($title) ?></h5>
                <small class="text-muted"><?= htmlspecialchars($subtitle) ?></small>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-dark rounded-pill px-3 py-2" id="calTotalMonthActivitiesBadge">
                <?= count($calendarActivities ?? []) ?> actividades este mes
            </span>
            <?php if ($isJefe): ?>
                <a href="<?= url('/activities') ?>" class="btn btn-sm btn-outline-primary fw-semibold">
                    <i class="bi bi-kanban me-1"></i> Tablero Completo
                </a>
            <?php else: ?>
                <a href="<?= url('/activities/my-tasks') ?>" class="btn btn-sm btn-outline-success fw-semibold">
                    <i class="bi bi-play-circle me-1"></i> Panel de Entregas
                </a>
            <?php endif; ?>
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
                            <h6 class="fw-bold mb-0 text-dark text-capitalize" id="calCurrentMonthLabel" style="font-size: 1.05rem;">
                                Octubre 2026
                            </h6>
                            <button type="button" class="btn btn-sm btn-light border py-0 px-2 small text-muted" onclick="calGoToday()">
                                Hoy
                            </button>
                        </div>

                        <div class="d-flex gap-1">
                            <button type="button" class="calendar-nav-btn" onclick="calPrevMonth()" title="Mes anterior">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <button type="button" class="calendar-nav-btn" onclick="calNextMonth()" title="Mes siguiente">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Cuadrícula de Días del Calendario -->
                    <div class="calendar-grid p-0 mb-3" id="calGridDaysContainer">
                        <!-- Generado dinámicamente con JavaScript -->
                    </div>

                    <!-- Leyenda de Colores por Tipo de Actividad -->
                    <div class="p-2 bg-light rounded-3 border small">
                        <div class="text-muted fw-bold mb-2 text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px;">
                            Convención de Actividades:
                        </div>
                        <div class="d-flex flex-wrap gap-2" style="font-size: 0.75rem;">
                            <span class="d-inline-flex align-items-center gap-1">
                                <span class="event-dot dot-video"></span> Edición Video
                            </span>
                            <span class="d-inline-flex align-items-center gap-1">
                                <span class="event-dot dot-camera"></span> Rodaje Dron / Cámara
                            </span>
                            <span class="d-inline-flex align-items-center gap-1">
                                <span class="event-dot dot-carousel"></span> Carruseles
                            </span>
                            <span class="d-inline-flex align-items-center gap-1">
                                <span class="event-dot dot-cm"></span> Redes / CM
                            </span>
                            <span class="d-inline-flex align-items-center gap-1">
                                <span class="event-dot dot-urgent"></span> Urgente
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA: AGENDA DETALLADA DE ACTIVIDADES -->
            <div class="col-12 col-lg-7 bg-light bg-opacity-50">
                <div class="p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark" id="calScheduleHeaderTitle">
                                <i class="bi bi-clock-history text-primary me-1"></i> Agenda de Actividades del Mes
                            </h6>
                            <small class="text-muted" id="calScheduleSubtitle">
                                Haz clic en cualquier día del calendario para filtrar por fecha
                            </small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary d-none" id="calResetDayFilterBtn" onclick="calResetFilter()">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Ver Todo el Mes
                        </button>
                    </div>

                    <!-- Lista de Actividades -->
                    <div class="activity-schedule-list p-0" id="calActivitiesListContainer">
                        <!-- Inyectado por JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================================
     JAVASCRIPT: LÓGICA REACTIVA DEL MINI CALENDARIO
     ========================================================================= -->
<script>
// Datos pasados desde PHP
const calRawActivities = <?= $activitiesJson ?>;
const isJefeContext = <?= $isJefe ? 'true' : 'false' ?>;
const calBaseUrl = <?= json_encode(url('')) ?>;

// Variables de estado del calendario
let calCurrentYear = new Date().getFullYear();
let calCurrentMonth = new Date().getMonth(); // 0-11
let calSelectedDate = null; // null = ver todo el mes actual, o 'YYYY-MM-DD'

const monthNamesEs = [
    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
];

const dayNamesHeader = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];

// Agrupar actividades por fecha (YYYY-MM-DD)
function getActivitiesByDateMap() {
    const map = {};
    calRawActivities.forEach(act => {
        const d = act.scheduled_date;
        if (!map[d]) {
            map[d] = [];
        }
        map[d].push(act);
    });
    return map;
}

// Inicializar el calendario
function calInit() {
    // Si hay actividades, centrar el calendario en el mes de la primera actividad o en la fecha actual
    if (calRawActivities.length > 0) {
        const firstDate = new Date(calRawActivities[0].scheduled_date + 'T00:00:00');
        if (!isNaN(firstDate)) {
            calCurrentYear = firstDate.getFullYear();
            calCurrentMonth = firstDate.getMonth();
        }
    }
    calRender();
}

function calPrevMonth() {
    calCurrentMonth--;
    if (calCurrentMonth < 0) {
        calCurrentMonth = 11;
        calCurrentYear--;
    }
    calSelectedDate = null;
    calRender();
}

function calNextMonth() {
    calCurrentMonth++;
    if (calCurrentMonth > 11) {
        calCurrentMonth = 0;
        calCurrentYear++;
    }
    calSelectedDate = null;
    calRender();
}

function calGoToday() {
    const today = new Date();
    calCurrentYear = today.getFullYear();
    calCurrentMonth = today.getMonth();
    calSelectedDate = today.toISOString().split('T')[0];
    calRender();
}

function calResetFilter() {
    calSelectedDate = null;
    calRender();
}

function calSelectDate(dateStr) {
    if (calSelectedDate === dateStr) {
        // Toggle: si ya estaba seleccionado, desseleccionar para ver todo el mes
        calSelectedDate = null;
    } else {
        calSelectedDate = dateStr;
    }
    calRender();
}

// Renderizar la cuadrícula del calendario y la lista de actividades
function calRender() {
    // 1. Actualizar título del mes
    document.getElementById('calCurrentMonthLabel').innerText = `${monthNamesEs[calCurrentMonth]} ${calCurrentYear}`;

    const grid = document.getElementById('calGridDaysContainer');
    grid.innerHTML = '';

    // Renderizar cabeceras de días de la semana (Lun, Mar, ...)
    dayNamesHeader.forEach(dName => {
        const hCell = document.createElement('div');
        hCell.className = 'calendar-weekday';
        hCell.innerText = dName;
        grid.appendChild(hCell);
    });

    // Calcular días del mes
    const firstDayIndex = new Date(calCurrentYear, calCurrentMonth, 1).getDay(); // 0 = Domingo, 1 = Lunes
    const adjustedFirstDay = (firstDayIndex === 0) ? 6 : (firstDayIndex - 1); // 0 = Lunes, 6 = Domingo
    const totalDaysInMonth = new Date(calCurrentYear, calCurrentMonth + 1, 0).getDate();

    // Días vacíos al inicio del mes
    for (let i = 0; i < adjustedFirstDay; i++) {
        const emptyCell = document.createElement('div');
        emptyCell.className = 'calendar-day empty-day';
        grid.appendChild(emptyCell);
    }

    const todayStr = new Date().toISOString().split('T')[0];
    const actMap = getActivitiesByDateMap();

    // Días del mes (1 ... N)
    for (let day = 1; day <= totalDaysInMonth; day++) {
        const dateStr = `${calCurrentYear}-${String(calCurrentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const dayCell = document.createElement('div');
        dayCell.className = 'calendar-day';
        dayCell.innerText = day;

        if (dateStr === todayStr) {
            dayCell.classList.add('today');
        }

        if (calSelectedDate === dateStr) {
            dayCell.classList.add('selected');
        }

        // Si hay actividades en este día
        if (actMap[dateStr] && actMap[dateStr].length > 0) {
            dayCell.classList.add('has-events');

            const dotsContainer = document.createElement('div');
            dotsContainer.className = 'calendar-event-dots';

            // Mostrar hasta 3 puntitos indicativos
            actMap[dateStr].slice(0, 3).forEach(act => {
                const dot = document.createElement('span');
                dot.className = 'event-dot';

                if (act.priority === 'URGENTE') {
                    dot.classList.add('dot-urgent');
                } else if (act.activity_type === 'EDICION_VIDEO') {
                    dot.classList.add('dot-video');
                } else if (act.activity_type === 'RODAJE_CAMARA') {
                    dot.classList.add('dot-camera');
                } else if (act.activity_type === 'CARRUSEL_FOTO') {
                    dot.classList.add('dot-carousel');
                } else {
                    dot.classList.add('dot-cm');
                }

                dotsContainer.appendChild(dot);
            });

            dayCell.appendChild(dotsContainer);
        }

        dayCell.onclick = () => calSelectDate(dateStr);
        grid.appendChild(dayCell);
    }

    // 2. Renderizar lista de actividades a la derecha
    calRenderActivitiesList(actMap);
}

function calRenderActivitiesList(actMap) {
    const listContainer = document.getElementById('calActivitiesListContainer');
    const headerTitle = document.getElementById('calScheduleHeaderTitle');
    const subtitle = document.getElementById('calScheduleSubtitle');
    const resetBtn = document.getElementById('calResetDayFilterBtn');

    let filteredActivities = [];

    if (calSelectedDate) {
        // Filtrado por fecha específica
        filteredActivities = actMap[calSelectedDate] || [];
        const dateObj = new Date(calSelectedDate + 'T00:00:00');
        const formattedDate = dateObj.toLocaleDateString('es-ES', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });

        headerTitle.innerHTML = `<i class="bi bi-calendar-check-fill text-primary me-1"></i> ${formattedDate}`;
        subtitle.innerText = `${filteredActivities.length} actividad(es) agendada(s) para este día`;
        resetBtn.classList.remove('d-none');
    } else {
        // Todo el mes actual visible
        const currentMonthPrefix = `${calCurrentYear}-${String(calCurrentMonth + 1).padStart(2, '0')}`;
        filteredActivities = calRawActivities.filter(a => a.scheduled_date && a.scheduled_date.startsWith(currentMonthPrefix));

        headerTitle.innerHTML = `<i class="bi bi-clock-history text-primary me-1"></i> Agenda de ${monthNamesEs[calCurrentMonth]} ${calCurrentYear}`;
        subtitle.innerText = `Mostrando ${filteredActivities.length} actividad(es) programadas en este ciclo`;
        resetBtn.classList.add('d-none');
    }

    // Actualizar badge superior
    const totalMonth = calRawActivities.filter(a => a.scheduled_date && a.scheduled_date.startsWith(`${calCurrentYear}-${String(calCurrentMonth + 1).padStart(2, '0')}`)).length;
    document.getElementById('calTotalMonthActivitiesBadge').innerText = `${totalMonth} actividades en ${monthNamesEs[calCurrentMonth]}`;

    listContainer.innerHTML = '';

    if (filteredActivities.length === 0) {
        listContainer.innerHTML = `
            <div class="text-center py-5 bg-white rounded-3 border">
                <i class="bi bi-calendar2-check text-muted display-6 d-block mb-2"></i>
                <h6 class="fw-bold text-dark mb-1">Sin actividades para esta fecha</h6>
                <p class="text-muted small mb-0">No hay grabaciones ni entregas programadas.</p>
            </div>
        `;
        return;
    }

    filteredActivities.forEach(act => {
        // Clase de borde según tipo o prioridad
        let borderClass = 'border-video';
        if (act.priority === 'URGENTE') borderClass = 'border-urgent';
        else if (act.activity_type === 'RODAJE_CAMARA') borderClass = 'border-camera';
        else if (act.activity_type === 'CARRUSEL_FOTO') borderClass = 'border-carousel';
        else if (act.activity_type === 'COMMUNITY_MANAGEMENT') borderClass = 'border-cm';

        // Badge de estado
        let statusBadge = 'bg-secondary';
        if (act.current_status === 'NUEVA') statusBadge = 'bg-primary';
        else if (act.current_status === 'EN_PROCESO') statusBadge = 'bg-info text-dark';
        else if (act.current_status === 'ENTREGADA_REVISION') statusBadge = 'bg-warning text-dark';
        else if (act.current_status === 'APROBADA') statusBadge = 'bg-success';
        else if (act.current_status === 'CORRECCION') statusBadge = 'bg-danger';

        // Badge de prioridad
        let prioBadge = 'bg-secondary';
        if (act.priority === 'URGENTE') prioBadge = 'bg-danger';
        else if (act.priority === 'ALTA') prioBadge = 'bg-warning text-dark';
        else prioBadge = 'bg-light text-dark border';

        const item = document.createElement('div');
        item.className = `activity-schedule-item ${borderClass}`;

        const specialistHtml = isJefeContext
            ? `<div class="small text-dark mt-1"><i class="bi bi-person-badge text-secondary me-1"></i><strong>Asignado a:</strong> ${act.employee_name || 'Sin asignar'}</div>`
            : '';

        const actionBtnHtml = isJefeContext
            ? `<a href="${calBaseUrl}/activities" class="btn btn-sm btn-outline-dark mt-2" style="font-size: 0.78rem;">
                 <i class="bi bi-eye me-1"></i> Ver en Tablero
               </a>`
            : `<a href="${calBaseUrl}/activities/my-tasks" class="btn btn-sm btn-primary mt-2" style="font-size: 0.78rem;">
                 <i class="bi bi-upload me-1"></i> Gestionar Entrega
               </a>`;

        item.innerHTML = `
            <div class="d-flex justify-content-between align-items-start mb-1 flex-wrap gap-1">
                <div>
                    <span class="badge ${prioBadge} small">${act.priority}</span>
                    <span class="badge bg-light text-dark border font-monospace small ms-1">${act.activity_type}</span>
                </div>
                <div>
                    <span class="badge ${statusBadge} small">${act.current_status}</span>
                </div>
            </div>

            <h6 class="fw-bold text-dark mb-1 mt-2">${act.title}</h6>

            <div class="small text-muted mb-1">
                <i class="bi bi-building me-1"></i><strong>${act.company_name}</strong> &bull; <i class="bi bi-geo-alt text-danger"></i>${act.miami_zone}
            </div>

            ${specialistHtml}

            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                <small class="text-muted"><i class="bi bi-calendar-event me-1"></i>Fecha: <strong>${act.scheduled_date}</strong></small>
                ${actionBtnHtml}
            </div>
        `;

        listContainer.appendChild(item);
    });
}

// Iniciar al cargar el DOM
document.addEventListener('DOMContentLoaded', () => {
    calInit();
});
</script>
