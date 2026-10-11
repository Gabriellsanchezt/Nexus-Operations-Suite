/**
 * JavaScript Vanilla para Miami Agency Operations
 * Gestión de Popovers, Toasts, Tooltips y Notificaciones Interactivas
 */

// Actualizar insignias de notificación en la campana y en el encabezado del popover
function updateNotificationBadgeCount(newCount) {
    // 1. Insignias numéricas
    const badgeElements = document.querySelectorAll('.notif-badge-count');
    badgeElements.forEach(badge => {
        badge.textContent = newCount;
        if (newCount <= 0) {
            badge.classList.add('d-none');
        } else {
            badge.classList.remove('d-none');
        }
    });

    // 2. Icono de campana (amarillo relleno vs gris neutro)
    const bellIcon = document.getElementById('notifBellIcon');
    if (bellIcon) {
        if (newCount <= 0) {
            bellIcon.className = 'bi bi-bell fs-6';
        } else {
            bellIcon.className = 'bi bi-bell-fill text-warning fs-6';
        }
    }

    // 3. Si llega a 0, alternar lista por estado vacío "¡Todo al día!"
    if (newCount <= 0) {
        document.querySelectorAll('.btn-mark-all-read').forEach(el => el.classList.add('d-none'));
        document.querySelectorAll('.notif-list-container').forEach(el => el.classList.add('d-none'));
        document.querySelectorAll('.notif-empty-state').forEach(el => el.classList.remove('d-none'));
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // 1. Tooltips de Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // 2. Centro de Notificaciones en Popover de Bootstrap
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

        // Cerrar popover automáticamente al hacer clic en cualquier parte fuera de él
        document.addEventListener('click', (e) => {
            const popoverEl = document.querySelector('.popover.popover-notification-panel');
            if (popoverEl && !notifBtn.contains(e.target) && !popoverEl.contains(e.target)) {
                notifPopover.hide();
            }
        });
    }

    // 3. Popovers generales de Bootstrap
    const genericPopovers = document.querySelectorAll('[data-bs-toggle="popover"]:not(#btnNotificationPopover)');
    genericPopovers.forEach(el => {
        new bootstrap.Popover(el, {
            html: true,
            sanitize: false
        });
    });

    // 4. Toasts de Bootstrap para alertas flash
    const toastElements = document.querySelectorAll('.toast');
    toastElements.forEach(toastEl => {
        const bsToast = new bootstrap.Toast(toastEl);
        bsToast.show();
    });

    // 5. Desvanecer alertas tradicionales si existiera alguna residual tras 6 segundos
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) {
                bsAlert.close();
            }
        }, 6000);
    });
});

// Manejo interactivo de Marcar como Vista (Individual y Masivo)
document.addEventListener('click', async (e) => {
    // A) Marcar una notificación individual como vista
    const markSingleBtn = e.target.closest('.btn-mark-notif-read');
    if (markSingleBtn) {
        e.preventDefault();
        e.stopPropagation();

        const notifId = markSingleBtn.getAttribute('data-notif-id');
        if (!notifId) return;

        // Deshabilitar botón visualmente
        markSingleBtn.disabled = true;
        markSingleBtn.innerHTML = '<span class="spinner-border spinner-border-sm text-secondary" style="width: 0.8rem; height: 0.8rem;"></span>';

        // Animación de salida en el popover y en el template
        const matchingItems = document.querySelectorAll(`.notif-item[data-notif-id="${notifId}"]`);
        matchingItems.forEach(item => {
            item.style.transition = 'all 0.25s ease';
            item.style.opacity = '0';
            item.style.transform = 'translateX(20px)';
        });

        // Llamada a la API para persistir en backend
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
            // Remover elementos del DOM
            matchingItems.forEach(item => item.remove());

            // Calcular conteo restante si no vino del servidor
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
        markAllBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" style="width: 0.8rem; height: 0.8rem;"></span>Vistas';

        // Animación para todos los items
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
