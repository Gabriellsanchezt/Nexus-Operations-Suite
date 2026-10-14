<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold mb-1">
            <i class="bi bi-calendar-check-fill text-primary me-2"></i>Agenda de Reuniones
        </h2>
        <p class="text-muted mb-0">Coordinación de llamadas y citas operativas asignadas por trabajador y sincronizadas con Google Calendar y notificaciones móviles</p>
    </div>
    <button type="button" class="btn btn-primary fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNewMeeting">
        <i class="bi bi-plus-lg me-1"></i> + Agendar Nueva Reunión
    </button>
</div>

<!-- Banner Explicativo de Sincronización Móvil y Recordatorio para Administrador -->
<div class="card border-0 shadow-sm mb-4 bg-primary bg-opacity-10 border-start border-primary border-4">
    <div class="card-body p-3">
        <div class="d-flex align-items-center gap-3">
            <div class="p-2 bg-primary text-white rounded-circle d-none d-sm-block">
                <i class="bi bi-phone-vibrate-fill fs-4"></i>
            </div>
            <div>
                <strong class="text-primary d-block">Alarma y Recordatorio Centralizado en tu Teléfono Móvil</strong>
                <p class="text-dark small mb-0">
                    Cada reunión se asigna a un trabajador específico. Al pulsar <strong>"Google Calendar"</strong> o descargar el archivo <strong>.ics</strong>, la alarma se programará directamente en tu teléfono móvil y correo con todos los números telefónicos y datos del trabajador y del cliente, para que puedas llamarles o escribirles y recordarles su cita puntualmente.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Listado de Reuniones -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-calendar-event text-secondary me-2"></i>Reuniones Programadas
        </h5>
        <span class="badge bg-secondary"><?= count($meetings) ?> evento(s)</span>
    </div>

    <div class="card-body p-0">
        <?php if (empty($meetings)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-calendar2-x display-4 text-muted mb-3 d-block"></i>
                <h5 class="fw-bold text-dark">No hay reuniones agendadas en este momento</h5>
                <p class="text-muted small mb-3">Agenda una reunión indicando el trabajador asignado para sincronizarla con su correo y móvil.</p>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNewMeeting">
                    <i class="bi bi-plus-lg me-1"></i> Agendar Reunión
                </button>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th style="width: 170px;">Fecha & Inicio</th>
                            <th>Reunión / Propósito</th>
                            <th>Cliente (Realtor)</th>
                            <th>Trabajador Asignado (Correo Móvil)</th>
                            <th class="text-end">Sincronización Móvil</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($meetings as $m): ?>
                            <?php
                                $dt = strtotime("{$m['meeting_date']} {$m['meeting_time']}");
                                $isPast = ($dt < time());
                            ?>
                            <tr class="<?= $isPast ? 'text-muted' : '' ?>">
                                <td>
                                    <div class="fw-bold <?= $isPast ? 'text-secondary' : 'text-primary' ?>">
                                        <i class="bi bi-calendar3 me-1"></i> <?= htmlspecialchars($m['meeting_date']) ?>
                                    </div>
                                    <span class="badge bg-dark font-monospace">
                                        <i class="bi bi-clock me-1 text-warning"></i> <?= date('h:i A', $dt) ?>
                                    </span>
                                </td>

                                <td>
                                    <strong class="text-dark d-block"><?= htmlspecialchars($m['title']) ?></strong>
                                    <?php if (!empty($m['description'])): ?>
                                        <small class="text-muted"><?= htmlspecialchars($m['description']) ?></small>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if (!empty($m['company_name'])): ?>
                                        <strong class="text-dark d-block"><?= htmlspecialchars($m['company_name']) ?></strong>
                                        <div class="small text-muted">
                                            <?= htmlspecialchars($m['contact_person']) ?>
                                            <?php if (!empty($m['realtor_email'])): ?>
                                                &bull; <a href="mailto:<?= htmlspecialchars($m['realtor_email']) ?>" class="text-decoration-none text-muted"><i class="bi bi-envelope"></i> <?= htmlspecialchars($m['realtor_email']) ?></a>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark border">Reunión Interna de Agencia</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if (!empty($m['employee_name'])): ?>
                                        <div class="fw-semibold text-dark"><?= htmlspecialchars($m['employee_name']) ?></div>
                                        <div class="d-flex align-items-center gap-1 mt-1">
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace" style="font-size: 0.75rem;">
                                                <i class="bi bi-envelope-at-fill me-1"></i><?= htmlspecialchars($m['employee_email'] ?? '') ?>
                                            </span>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-warning-emphasis border">Sin Asignar</span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-end">
                                    <div class="btn-group shadow-sm">
                                        <!-- Botón Google Calendar (Abre y programa la alarma en el teléfono móvil del gestor) -->
                                        <a href="<?= $m['google_calendar_url'] ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-primary fw-semibold" title="Programar alarma en mi teléfono y correo con datos de contacto">
                                            <i class="bi bi-google me-1"></i> Google Calendar
                                        </a>

                                        <!-- Descargar archivo iCalendar (.ics) nativo -->
                                        <a href="<?= url('/agenda/download-ics?id=' . $m['id']) ?>" class="btn btn-sm btn-outline-secondary" title="Descargar archivo .ics con alarmas nativas a 30m y 15m">
                                            <i class="bi bi-download"></i>
                                        </a>

                                        <!-- Eliminar reunión -->
                                        <form action="<?= url('/agenda/delete') ?>" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta reunión de la agenda?')">
                                            <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar reunión">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
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

<!-- MODAL NUEVA REUNIÓN (SOLO CAMPOS ESTRICTAMENTE REQUERIDOS: TÍTULO, CLIENTE, TRABAJADOR/CORREO, FECHA, HORA DE INICIO, DESCRIPCIÓN) -->
<div class="modal fade" id="modalNewMeeting" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <form action="<?= url('/agenda/store') ?>" method="POST">
                <div class="modal-header bg-dark text-white border-bottom border-primary border-3">
                    <h5 class="modal-title fw-bold"><i class="bi bi-calendar-plus me-2 text-primary"></i>Agendar Nueva Reunión</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- 1. Título de la reunión -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Título de la Reunión *</label>
                        <input type="text" name="title" class="form-control" required placeholder="Ej: Revisión de Estrategia Audiovisual">
                    </div>

                    <!-- 2. Cliente Realtor -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Cliente (Realtor)</label>
                        <select name="realtor_id" class="form-select">
                            <option value="">-- Ninguno (Reunión Interna de Agencia) --</option>
                            <?php foreach ($realtors as $rel): ?>
                                <option value="<?= $rel['id'] ?>">
                                    <?= htmlspecialchars($rel['company_name']) ?> &mdash; <?= htmlspecialchars($rel['contact_person']) ?> (<?= htmlspecialchars($rel['email']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- 3. Trabajador Asignado (Correo al que se asociará la reunión) -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Trabajador Asignado (Responsable de la Reunión) *</label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">-- Seleccionar Trabajador Responsable --</option>
                            <?php foreach ($employees as $emp): ?>
                                <option value="<?= $emp['employee_id'] ?>">
                                    <?= htmlspecialchars($emp['full_name']) ?> &mdash; <?= htmlspecialchars($emp['email']) ?> (<?= htmlspecialchars($emp['role_name']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted" style="font-size: 0.72rem;">Sus datos de contacto se incorporarán en la alarma de tu calendario para llamarle y recordarle la cita.</small>
                    </div>

                    <!-- 4. Fecha y Hora de Inicio -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Fecha *</label>
                            <input type="date" name="meeting_date" class="form-control" required value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Hora de Inicio *</label>
                            <input type="time" name="meeting_time" class="form-control" required value="10:00">
                        </div>
                    </div>

                    <!-- 5. Descripción -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Descripción</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Puntos a tratar y objetivo de la reunión..."></textarea>
                    </div>

                    <div class="p-3 bg-light rounded border small text-muted">
                        <i class="bi bi-phone-fill text-primary me-1"></i>
                        <strong>Alarma Personalizada en tu Móvil:</strong> Al pulsar <strong>Google Calendar</strong>, el evento se abrirá en tu cuenta con la alarma configurada y los teléfonos y correos de contacto del trabajador y cliente en el cuerpo de la cita para llamarlos puntualmente.
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-semibold"><i class="bi bi-calendar-check me-1"></i> Guardar en Agenda</button>
                </div>
            </form>
        </div>
    </div>
</div>
