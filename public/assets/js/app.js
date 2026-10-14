/**
 * JavaScript Vanilla para Nexus Operations Suite (Miami Luxury Media)
 * Gestión de Notificaciones en Tiempo Real, Sonido de Alertas (Web Audio API),
 * Popovers, Toasts, Tooltips y Slider Bar
 */

// Conjunto de IDs de notificaciones conocidas en la sesión del cliente
const knownNotificationIds = new Set();
let audioContextInstance = null;
let audioUnlocked = false;

/**
 * Obtener o inicializar contexto de audio compatible
 */
function getAudioContext() {
    if (!audioContextInstance) {
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        if (AudioCtx) {
            audioContextInstance = new AudioCtx();
        }
    }
    if (audioContextInstance && audioContextInstance.state === 'suspended') {
        audioContextInstance.resume();
    }
    return audioContextInstance;
}

// Desbloquear audio con la primera interacción del usuario (requisito de navegadores modernos)
document.addEventListener('click', () => {
    if (!audioUnlocked) {
        const ctx = getAudioContext();
        if (ctx) {
            audioUnlocked = true;
        }
    }
}, { once: true });

/**
 * Reproducir sonido ejecutivo de notificación usando Web Audio API
 * Tono dual armónico refinado (Re5 -> La5) con caída exponencial suave
 */
function playNotificationChime() {
    try {
        const ctx = getAudioContext();
        if (!ctx) return;

        const now = ctx.currentTime;

        // Tono 1: Frecuencia 587.33 Hz (D5)
        const osc1 = ctx.createOscillator();
        const gain1 = ctx.createGain();
        osc1.type = 'sine';
        osc1.frequency.setValueAtTime(587.33, now);
        gain1.gain.setValueAtTime(0.12, now);
        gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.32);
        osc1.connect(gain1);
        gain1.connect(ctx.destination);
        osc1.start(now);
        osc1.stop(now + 0.32);

        // Tono 2: Armónico 880.00 Hz (A5 - quinta superior)
        const osc2 = ctx.createOscillator();
        const gain2 = ctx.createGain();
        osc2.type = 'sine';
        osc2.frequency.setValueAtTime(880.00, now + 0.08);
        gain2.gain.setValueAtTime(0.15, now + 0.08);
        gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.55);
        osc2.connect(gain2);
        gain2.connect(ctx.destination);
        osc2.start(now + 0.08);
        osc2.stop(now + 0.55);

        // Animar campana visualmente con el sonido
        const bellIcon = document.getElementById('notifBellIcon');
        if (bellIcon) {
            bellIcon.classList.remove('bell-ringing');
            void bellIcon.offsetWidth;
            bellIcon.classList.add('bell-ringing');
            setTimeout(() => bellIcon.classList.remove('bell-ringing'), 900);
        }
    } catch (e) {
        console.warn('Alerta sonora omitida por restricción de navegador:', e);
    }
}
window.playNotificationChime = playNotificationChime;

/**
 * Actualizar insignias de notificación en la campana y en el popover
 */
function updateNotificationBadgeCount(newCount) {
    const badgeElements = document.querySelectorAll('.notif-badge-count');
    badgeElements.forEach(badge => {
        badge.textContent = newCount;
        if (newCount <= 0) {
            badge.classList.add('d-none');
        } else {
            badge.classList.remove('d-none');
        }
    });

    const bellIcon = document.getElementById('notifBellIcon');
    if (bellIcon) {
        if (newCount <= 0) {
            bellIcon.className = 'bi bi-bell fs-6';
        } else {
            bellIcon.className = 'bi bi-bell-fill text-warning fs-6';
        }
    }

    if (newCount <= 0) {
        document.querySelectorAll('.btn-mark-all-read').forEach(el => el.classList.add('d-none'));
        document.querySelectorAll('.notif-list-container').forEach(el => el.classList.add('d-none'));
        document.querySelectorAll('.notif-empty-state').forEach(el => el.classList.remove('d-none'));
    } else {
        document.querySelectorAll('.btn-mark-all-read').forEach(el => el.classList.remove('d-none'));
        document.querySelectorAll('.notif-list-container').forEach(el => el.classList.remove('d-none'));
        document.querySelectorAll('.notif-empty-state').forEach(el => el.classList.add('d-none'));
    }
}

/**
 * Mostrar un Toast dinámico para nueva notificación recibida
 */
function showNotificationToast(title, message, type = 'info', link = '#') {
    const toastContainer = document.querySelector('.toast-container') || createToastContainer();

    const toastEl = document.createElement('div');
    toastEl.className = 'toast align-items-center border-0 shadow-lg mb-2 show bg-white';
    toastEl.setAttribute('role', 'alert');
    toastEl.setAttribute('aria-live', 'assertive');
    toastEl.setAttribute('aria-atomic', 'true');
    toastEl.style.borderLeft = '4px solid #1e3a5f';

    toastEl.innerHTML = `
        <div class="toast-header bg-light border-bottom-0 py-2">
            <i class="bi bi-bell-fill text-warning me-2 fs-6"></i>
            <strong class="me-auto text-dark small text-truncate" style="max-width: 180px;">${escapeHtml(title)}</strong>
            <small class="text-muted font-monospace" style="font-size: 0.7rem;">Ahora</small>
            <button type="button" class="btn-close ms-2 mb-1" data-bs-dismiss="toast" aria-label="Cerrar"></button>
        </div>
        <div class="toast-body py-2 px-3 small text-secondary d-flex align-items-center justify-content-between gap-2">
            <div>${escapeHtml(message)}</div>
            ${link && link !== '#' ? `<a href="${escapeHtml(link)}" class="btn btn-sm btn-outline-primary py-0 px-2 flex-shrink-0" style="font-size: 0.72rem;">Ver</a>` : ''}
        </div>
    `;

    toastContainer.appendChild(toastEl);
    const bsToast = new bootstrap.Toast(toastEl, { delay: 6000 });
    bsToast.show();

    toastEl.addEventListener('hidden.bs.toast', () => {
        toastEl.remove();
    });
}

function createToastContainer() {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '1090';
        document.body.appendChild(container);
    }
    return container;
}

function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

/**
 * Consulta periódica de notificaciones en tiempo real
 */
async function pollLiveNotifications() {
    const endpoint = (window.APP_CONFIG && window.APP_CONFIG.notificationsUrl) || '/api/notifications';
    try {
        const response = await fetch(endpoint, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) return;

        const data = await response.json();
        if (!data || !data.success || !Array.isArray(data.notifications)) return;

        let hasNewNotification = false;
        const incomingNotifs = data.notifications;

        incomingNotifs.forEach(notif => {
            if (!knownNotificationIds.has(notif.id)) {
                knownNotificationIds.add(notif.id);
                hasNewNotification = true;
                // Mostrar Toast y alertar
                showNotificationToast(notif.title, notif.message, notif.type, notif.link);
            }
        });

        if (hasNewNotification) {
            playNotificationChime();
            updateNotificationBadgeCount(data.count);
            rebuildPopoverNotificationList(incomingNotifs);
        }
    } catch (err) {
        // Silencioso para no saturar consola en reconexiones
    }
}

/**
 * Reconstruir la lista del popover cuando llegan notificaciones en tiempo real
 */
function rebuildPopoverNotificationList(notifications) {
    const contentWrapper = document.getElementById('notificationPopoverContent');
    if (!contentWrapper) return;

    const listContainers = document.querySelectorAll('.notif-list-container');
    if (notifications.length === 0) {
        updateNotificationBadgeCount(0);
        return;
    }

    let itemsHtml = '';
    notifications.forEach(notif => {
        itemsHtml += `
            <div class="list-group-item list-group-item-action p-2 border-bottom d-flex align-items-start gap-2 notif-item" data-notif-id="${escapeHtml(notif.id)}">
                <div class="p-2 rounded-circle bg-${escapeHtml(notif.type)} bg-opacity-10 text-${escapeHtml(notif.type)} fs-5 lh-1 flex-shrink-0 mt-1">
                    <i class="bi ${escapeHtml(notif.icon)}"></i>
                </div>
                <div class="flex-grow-1 overflow-hidden">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <a href="${escapeHtml(notif.link)}" class="fw-bold small text-dark text-truncate text-decoration-none">
                            ${escapeHtml(notif.title)}
                        </a>
                        <span class="badge bg-${escapeHtml(notif.type)} bg-opacity-25 text-${escapeHtml(notif.type)} border border-${escapeHtml(notif.type)} border-opacity-25 rounded-pill" style="font-size: 0.65rem;">
                            ${escapeHtml(notif.badge)}
                        </span>
                    </div>
                    <p class="mb-1 text-muted small lh-sm" style="font-size: 0.78rem;">
                        ${escapeHtml(notif.message)}
                    </p>
                    <div class="d-flex align-items-center justify-content-between mt-1">
                        <a href="${escapeHtml(notif.link)}" class="text-primary fw-semibold d-inline-flex align-items-center gap-1 text-decoration-none" style="font-size: 0.72rem;">
                            ${escapeHtml(notif.action_text || 'Ver detalles')} &rarr;
                        </a>
                        <span class="text-muted" style="font-size: 0.68rem;">${escapeHtml(notif.time || '')}</span>
                    </div>
                </div>
                <button type="button" 
                        class="btn btn-sm btn-outline-secondary btn-mark-notif-read border-0 p-1 rounded-circle flex-shrink-0 text-muted" 
                        data-notif-id="${escapeHtml(notif.id)}" 
                        title="Marcar como vista">
                    <i class="bi bi-check-lg fs-5"></i>
                </button>
            </div>
        `;
    });

    listContainers.forEach(container => {
        container.innerHTML = itemsHtml;
    });

    updateNotificationBadgeCount(notifications.length);
}

// Inicialización de componentes al cargar la página
document.addEventListener('DOMContentLoaded', () => {
    // 1. Registrar IDs ya presentes en la plantilla para no disparar alertas redundantes al recargar
    document.querySelectorAll('.notif-item').forEach(item => {
        const id = item.getAttribute('data-notif-id');
        if (id) knownNotificationIds.add(id);
    });

    // 2. Tooltips de Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // 3. Popover del Centro de Notificaciones
    const notifBtn = document.getElementById('btnNotificationPopover');
    if (notifBtn) {
        const titleEl = document.getElementById('notificationPopoverTitle');
        const contentEl = document.getElementById('notificationPopoverContent');

        const notifPopover = new bootstrap.Popover(notifBtn, {
            html: true,
            sanitize: false,
            placement: 'bottom-end',
            customClass: 'popover-notification-panel shadow-lg border-0',
            title: function () {
                return titleEl ? titleEl.innerHTML : 'Notificaciones';
            },
            content: function () {
                return contentEl ? contentEl.innerHTML : '<div class="p-3 text-muted">Sin notificaciones</div>';
            }
        });

        // Cerrar popover automáticamente al hacer clic fuera
        document.addEventListener('click', (e) => {
            const popoverEl = document.querySelector('.popover.popover-notification-panel');
            if (popoverEl && !notifBtn.contains(e.target) && !popoverEl.contains(e.target)) {
                notifPopover.hide();
            }
        });
    }

    // 4. Toasts flash iniciales
    const toastElements = document.querySelectorAll('.toast');
    toastElements.forEach(toastEl => {
        const bsToast = new bootstrap.Toast(toastEl);
        bsToast.show();
    });

    // 5. Iniciar sondeo en tiempo real de notificaciones cada 15 segundos
    if (window.APP_CONFIG && window.APP_CONFIG.notificationsUrl) {
        setInterval(pollLiveNotifications, 15000);
    }
});

// Interacción para Marcar como Vista (Individual y Masivo)
document.addEventListener('click', async (e) => {
    // A) Marcar una notificación individual como vista
    const markSingleBtn = e.target.closest('.btn-mark-notif-read');
    if (markSingleBtn) {
        e.preventDefault();
        e.stopPropagation();

        const notifId = markSingleBtn.getAttribute('data-notif-id');
        if (!notifId) return;

        markSingleBtn.disabled = true;
        markSingleBtn.innerHTML = '<span class="spinner-border spinner-border-sm text-secondary" style="width: 0.8rem; height: 0.8rem;"></span>';

        const matchingItems = document.querySelectorAll(`.notif-item[data-notif-id="${notifId}"]`);
        matchingItems.forEach(item => {
            item.style.transition = 'all 0.25s ease';
            item.style.opacity = '0';
            item.style.transform = 'translateX(20px)';
        });

        const endpoint = (window.APP_CONFIG && window.APP_CONFIG.markReadUrl) || '/api/notifications/mark-read';
        let remainingCount = -1;

        try {
            const res = await apiRequest(endpoint, 'POST', { id: notifId });
            if (res && typeof res.remaining_count === 'number') {
                remainingCount = res.remaining_count;
            }
        } catch (err) {
            console.error('Error al marcar notificación:', err);
        }

        setTimeout(() => {
            matchingItems.forEach(item => item.remove());
            if (remainingCount < 0) {
                const visibleInPopover = document.querySelectorAll('.popover .notif-item').length;
                remainingCount = visibleInPopover;
            }
            updateNotificationBadgeCount(remainingCount);
        }, 250);

        return;
    }

    // B) Marcar todas las notificaciones como vistas
    const markAllBtn = e.target.closest('.btn-mark-all-read');
    if (markAllBtn) {
        e.preventDefault();
        e.stopPropagation();

        markAllBtn.disabled = true;
        markAllBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" style="width: 0.8rem; height: 0.8rem;"></span>';

        const allItems = document.querySelectorAll('.notif-item');
        allItems.forEach(item => {
            item.style.transition = 'all 0.25s ease';
            item.style.opacity = '0';
        });

        const endpoint = (window.APP_CONFIG && window.APP_CONFIG.markReadUrl) || '/api/notifications/mark-read';
        try {
            await apiRequest(endpoint, 'POST', { all: true });
        } catch (err) {
            console.error('Error al marcar todas las notificaciones:', err);
        }

        setTimeout(() => {
            allItems.forEach(item => item.remove());
            updateNotificationBadgeCount(0);
        }, 250);

        return;
    }
});

/**
 * Función genérica para envíos AJAX seguros con JSON
 */
async function apiRequest(url, method = 'POST', data = {}) {
    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        });
        return await response.json();
    } catch (error) {
        console.error('Error en llamada API:', error);
        throw error;
    }
}
