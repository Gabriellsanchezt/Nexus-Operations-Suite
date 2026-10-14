<?php
/**
 * Componente: Tablero de Producción Audiovisual Escalable
 * Orden por tiempo (lo más reciente arriba según va llegando),
 * barra de búsqueda en tiempo real y botones de filtro interactivo.
 */
$boardActivities = $activities ?? [];
?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 border-bottom">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-kanban-fill text-primary me-2"></i>Tablero de Producción (En Tiempo Real)
                </h5>
                <span class="small text-muted">Orden cronológico: lo más reciente arriba conforme va ingresando al sistema</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnSortOrder" onclick="toggleSortOrder()" title="Alternar orden temporal">
                    <i class="bi bi-sort-down me-1"></i> <span id="sortOrderLabel">Más recientes primero</span>
                </button>
                <a href="<?= url('/activities') ?>" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-arrow-up-right-square me-1"></i> Ir al Tablero Completo
                </a>
            </div>
        </div>
    </div>

    <!-- Barra de Búsqueda y Botones de Filtro -->
    <div class="card-body p-3 bg-light border-bottom">
        <div class="row g-2 align-items-center">
            <!-- Barra de Búsqueda -->
            <div class="col-12 col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="dashBoardSearch" class="form-control border-start-0" placeholder="Buscar por título, Realtor o especialista..." oninput="filterBoardActivities()">
                </div>
            </div>

            <!-- Botones de Filtro por Estado -->
            <div class="col-12 col-md-7">
                <div class="d-flex flex-wrap gap-1 justify-content-md-end" id="boardFilterButtons">
                    <button type="button" class="btn btn-sm btn-dark filter-btn active" data-status="" onclick="setBoardFilter('', this)">
                        Todas (<span id="countAll"><?= count($boardActivities) ?></span>)
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary filter-btn" data-status="NUEVA" onclick="setBoardFilter('NUEVA', this)">
                        Nuevas
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-info filter-btn" data-status="EN_PROCESO" onclick="setBoardFilter('EN_PROCESO', this)">
                        En Proceso
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-warning filter-btn" data-status="ENTREGADA_REVISION" onclick="setBoardFilter('ENTREGADA_REVISION', this)">
                        Por Revisar
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-success filter-btn" data-status="APROBADA" onclick="setBoardFilter('APROBADA', this)">
                        Culminadas
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista / Tabla Escalable del Tablero -->
    <div class="card-body p-0">
        <?php if (empty($boardActivities)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox display-5 text-muted d-block mb-2"></i>
                No hay actividades registradas en el flujo de producción actualmente.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="dashBoardTable">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th style="width: 140px;">Llegada / Registro</th>
                            <th>Tarea & Insumos</th>
                            <th>Cliente Realtor</th>
                            <th>Responsable</th>
                            <th>Prioridad</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="dashBoardTbody">
                        <?php foreach ($boardActivities as $act): ?>
                            <?php
                                $statusClass = match($act['current_status']) {
                                    'APROBADA' => 'bg-success text-white',
                                    'EN_PROCESO' => 'bg-info text-dark',
                                    'ENTREGADA_REVISION' => 'bg-warning text-dark',
                                    'CORRECCION' => 'bg-danger text-white',
                                    default => 'bg-primary text-white'
                                };

                                $prioClass = match($act['priority']) {
                                    'URGENTE' => 'bg-danger text-white',
                                    'ALTA' => 'bg-warning text-dark',
                                    default => 'bg-secondary text-white'
                                };

                                $createdAtTimestamp = strtotime($act['created_at']);
                                $timeAgo = date('d/m/Y H:i', $createdAtTimestamp);
                            ?>
                            <tr class="board-item"
                                data-created="<?= $createdAtTimestamp ?>"
                                data-status="<?= htmlspecialchars($act['current_status']) ?>"
                                data-search="<?= strtolower(htmlspecialchars($act['title'] . ' ' . $act['company_name'] . ' ' . ($act['employee_name'] ?? ''))) ?>">
                                
                                <td>
                                    <span class="badge bg-light text-dark border font-monospace small">
                                        <i class="bi bi-clock-history me-1 text-primary"></i> <?= $timeAgo ?>
                                    </span>
                                </td>

                                <td>
                                    <strong class="text-dark d-block"><?= htmlspecialchars($act['title']) ?></strong>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <span class="badge bg-light text-dark border font-monospace small"><?= htmlspecialchars($act['activity_type']) ?></span>
                                        <?php if (!empty($act['dropbox_url'])): ?>
                                            <a href="<?= htmlspecialchars($act['dropbox_url']) ?>" target="_blank" rel="noopener noreferrer" class="badge bg-primary-subtle text-primary border border-primary-subtle text-decoration-none d-inline-flex align-items-center gap-1">
                                                <i class="bi bi-dropbox"></i> Dropbox
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <td>
                                    <strong class="text-dark"><?= htmlspecialchars($act['company_name']) ?></strong>
                                    <div class="small text-muted"><?= htmlspecialchars($act['miami_zone']) ?></div>
                                </td>

                                <td>
                                    <span class="small text-dark fw-semibold">
                                        <i class="bi bi-person-fill text-muted me-1"></i><?= htmlspecialchars($act['employee_name'] ?? 'Sin asignar') ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="badge <?= $prioClass ?>"><?= htmlspecialchars($act['priority']) ?></span>
                                </td>

                                <td>
                                    <span class="badge <?= $statusClass ?>"><?= htmlspecialchars($act['current_status']) ?></span>
                                </td>

                                <td class="text-end">
                                    <a href="<?= url('/activities') ?>" class="btn btn-sm btn-outline-dark" title="Ver en Tablero Completo">
                                        <i class="bi bi-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
let currentBoardStatusFilter = '';
let isDescendingSort = true; // Por defecto: Más recientes primero (FIFO/Tiempo)

function setBoardFilter(status, btnElement) {
    currentBoardStatusFilter = status;
    document.querySelectorAll('#boardFilterButtons .filter-btn').forEach(b => {
        b.classList.remove('btn-dark', 'btn-primary', 'btn-info', 'btn-warning', 'btn-success');
        b.classList.add('btn-outline-secondary');
    });

    if (btnElement) {
        btnElement.classList.remove('btn-outline-secondary');
        if (status === '') btnElement.classList.add('btn-dark');
        else if (status === 'NUEVA') btnElement.classList.add('btn-primary');
        else if (status === 'EN_PROCESO') btnElement.classList.add('btn-info');
        else if (status === 'ENTREGADA_REVISION') btnElement.classList.add('btn-warning');
        else if (status === 'APROBADA') btnElement.classList.add('btn-success');
    }

    filterBoardActivities();
}

function filterBoardActivities() {
    const q = (document.getElementById('dashBoardSearch')?.value || '').toLowerCase().trim();
    const rows = document.querySelectorAll('#dashBoardTbody .board-item');

    rows.forEach(row => {
        const rowStatus = row.getAttribute('data-status') || '';
        const rowSearch = row.getAttribute('data-search') || '';

        const matchesStatus = (currentBoardStatusFilter === '' || rowStatus === currentBoardStatusFilter);
        const matchesQuery = (q === '' || rowSearch.includes(q));

        row.style.display = (matchesStatus && matchesQuery) ? '' : 'none';
    });
}

function toggleSortOrder() {
    isDescendingSort = !isDescendingSort;
    const label = document.getElementById('sortOrderLabel');
    const icon = document.querySelector('#btnSortOrder i');
    const tbody = document.getElementById('dashBoardTbody');
    if (!tbody) return;

    const rows = Array.from(tbody.querySelectorAll('.board-item'));

    rows.sort((a, b) => {
        const timeA = parseInt(a.getAttribute('data-created') || '0', 10);
        const timeB = parseInt(b.getAttribute('data-created') || '0', 10);
        return isDescendingSort ? (timeB - timeA) : (timeA - timeB);
    });

    rows.forEach(r => tbody.appendChild(r));

    if (isDescendingSort) {
        label.textContent = 'Más recientes primero';
        icon.className = 'bi bi-sort-down me-1';
    } else {
        label.textContent = 'Más antiguos primero';
        icon.className = 'bi bi-sort-up me-1';
    }
}
</script>
