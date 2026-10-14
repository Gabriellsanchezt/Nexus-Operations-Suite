<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold mb-1">
            <i class="bi bi-calendar-check-fill text-primary me-2"></i>Agenda de Reuniones
        </h2>
        <p class="text-muted mb-0">Coordinación de llamadas y citas operativas vinculadas con Google Calendar y notificaciones móviles</p>
    </div>
    <button type="button" class="btn btn-primary fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNewMeeting">
        <i class="bi bi-plus-lg me-1"></i> + Agendar Nueva Reunión
    </button>
</div>

<!-- Banner Explicativo de Sincronización Móvil -->
<div class="card border-0 shadow-sm mb-4 bg-primary bg-opacity-10 border-start border-primary border-4">
    <div class="card-body p-3">
        <div class="d-flex align-items-center gap-3">
            <div class="p-2 bg-primary text-white rounded-circle d-none d-sm-block">
                <i class="bi bi-phone-vibrate-fill fs-4"></i>
            </div>
            <div>
                <strong class="text-primary d-block">Sincronización Automática con tu Teléfono Móvil & Correo</strong>
                <p class="text-dark small mb-0">
                    Al presionar el botón <strong>"Añadir a Google Calendar"</strong> en cualquier reunión, el evento se sincronizará automáticamente con tu cuenta de Google. Tu teléfono celular (Android o iPhone) y tu correo electrónico te notificarán con recordatorios antes de que comience la reunión.
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
                <p class="text-muted small mb-3">Agenda una reunión para sincronizarla inmediatamente con tu calendario y recibir alertas en tu móvil.</p>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNewMeeting">
                    <i class="bi bi-plus-lg me-1"></i> Agendar Reunión
                </button>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th style="width: 170px;">Fecha & Hora</th>
                            <th>Reunión / Propósito</th>
                            <th>Cliente / Participante</th>
                            <th>Enlace Virtual</th>
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
                                        <i class="bi bi-clock me-1 text-warning"></i> <?= date('h:i A', $dt) ?> (<?= (int)$m['duration_minutes'] ?>m)
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
                                        <strong><?= htmlspecialchars($m['company_name']) ?></strong>
                                        <div class="small text-muted"><?= htmlspecialchars($m['contact_person']) ?> &bull; <?= htmlspecialchars($m['realtor_phone']) ?></div>
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark border">Reunión Interna de Agencia</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if (!empty($m['meeting_link'])): ?>
                                        <a href="<?= htmlspecialchars($m['meeting_link']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-camera-video-fill"></i> Unirse a Llamada
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">Sin enlace virtual</span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-end">
                                    <div class="btn-group shadow-sm">
                                        <!-- Botón Google Calendar (Abre y notifica en el teléfono móvil) -->
                                        <a href="<?= $m['google_calendar_url'] ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-primary fw-semibold" title="Sincronizar y recibir notificación en el teléfono">
                                            <i class="bi bi-google me-1"></i> Google Calendar
                                        </a>

                                        <!-- Descargar archivo iCalendar (.ics) nativo -->
                                        <a href="<?= url('/agenda/download-ics?id=' . $m['id']) ?>" class="btn btn-sm btn-outline-secondary" title="Descargar archivo .ics para calendario">
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

<!-- MODAL NUEVA REUNIÓN -->
<div class="modal fade" id="modalNewMeeting" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= url('/agenda/store') ?>" method="POST">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-calendar-plus me-2 text-primary"></i>Agendar Reunión</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Título de la Reunión *</label>
                        <input type="text" name="title" class="form-control" required placeholder="Ej: Revisión de Estrategia Audiovisual con Elena">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Cliente Realtor Asociado (Opcional)</label>
                        <select name="realtor_id" class="form-select">
                            <option value="">-- Ninguno (Reunión Interna de Equipo) --</option>
                            <?php foreach ($realtors as $rel): ?>
                                <option value="<?= $rel['id'] ?>">
                                    <?= htmlspecialchars($rel['company_name']) ?> (<?= htmlspecialchars($rel['contact_person']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

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

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Duración Estimada</label>
                            <select name="duration_minutes" class="form-select">
                                <option value="15">15 minutos</option>
                                <option value="30" selected>30 minutos</option>
                                <option value="45">45 minutos</option>
                                <option value="60">1 hora</option>
                                <option value="90">1 hora y media</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Link de Google Meet / Zoom</label>
                            <input type="url" name="meeting_link" class="form-control" placeholder="https://meet.google.com/...">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Descripción / Agenda de Temas (Opcional)</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Puntos a tratar en la llamada..."></textarea>
                    </div>

                    <div class="p-3 bg-light rounded border small text-muted">
                        <i class="bi bi-bell-fill text-warning me-1"></i>
                        <strong>Notificación Móvil:</strong> Al guardar, podrás sincronizarla en 1 clic con Google Calendar para que tu teléfono te avise antes de la reunión.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-semibold"><i class="bi bi-calendar-check me-1"></i> Guardar en Agenda</button>
                </div>
            </form>
        </div>
    </div>
</div>
