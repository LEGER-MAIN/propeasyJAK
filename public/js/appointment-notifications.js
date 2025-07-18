/**
 * JavaScript para Notificaciones de Citas Pendientes
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 */

class AppointmentNotifications {
    constructor() {
        this.checkInterval = null;
        this.lastCheck = null;
        this.isModalOpen = false;
        this.init();
    }

    init() {
        // Solo inicializar si el usuario está logueado y es cliente
        if (this.shouldInitialize()) {
            this.checkPendingAppointments();
            this.startPeriodicCheck();
            this.setupEventListeners();
        }
    }

    shouldInitialize() {
        // Verificar si el usuario está logueado y es cliente
        const userType = document.body.getAttribute('data-user-type');
        return userType === 'cliente';
    }

    /**
     * Verificar citas pendientes de aceptación
     */
    async checkPendingAppointments() {
        try {
            const response = await fetch('/api/appointments/pending', {
                credentials: 'include'
            });

            if (response.ok) {
                const data = await response.json();
                
                if (data.success && data.count > 0) {
                    this.showNotificationModal(data.citas);
                }
            }
        } catch (error) {
            console.error('Error verificando citas pendientes:', error);
        }
    }

    /**
     * Iniciar verificación periódica
     */
    startPeriodicCheck() {
        // Verificar cada 30 segundos
        this.checkInterval = setInterval(() => {
            this.checkPendingAppointments();
        }, 30000);
    }

    /**
     * Configurar event listeners
     */
    setupEventListeners() {
        // Event listeners para el modal
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-action="accept-appointment-modal"]')) {
                this.acceptAppointment(e.target.dataset.appointmentId);
            }

            if (e.target.matches('[data-action="reject-appointment-modal"]')) {
                this.rejectAppointment(e.target.dataset.appointmentId);
            }

            if (e.target.matches('[data-action="close-notification-modal"]')) {
                this.closeModal();
            }
        });

        // Cerrar modal con Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isModalOpen) {
                this.closeModal();
            }
        });
    }

    /**
     * Mostrar modal de notificaciones
     */
    showNotificationModal(citas) {
        if (this.isModalOpen) {
            return; // No mostrar múltiples modales
        }

        this.isModalOpen = true;
        
        const modalHTML = this.generateModalHTML(citas);
        
        // Crear y mostrar el modal
        const modalContainer = document.createElement('div');
        modalContainer.id = 'appointment-notification-modal';
        modalContainer.innerHTML = modalHTML;
        
        document.body.appendChild(modalContainer);
        
        // Animar entrada
        setTimeout(() => {
            modalContainer.classList.add('show');
        }, 100);
    }

    /**
     * Generar HTML del modal
     */
    generateModalHTML(citas) {
        const citasHTML = citas.map(cita => `
            <div class="appointment-item" data-appointment-id="${cita.id}">
                <div class="appointment-header">
                    <h4>📅 ${cita.tipo}</h4>
                    <span class="appointment-date">${cita.fecha}</span>
                </div>
                <div class="appointment-details">
                    <p><strong>Propiedad:</strong> ${cita.propiedad_titulo}</p>
                    <p><strong>Ubicación:</strong> ${cita.lugar}</p>
                    <p><strong>Agente:</strong> ${cita.agente_nombre}</p>
                    <p><strong>Teléfono:</strong> ${cita.agente_telefono}</p>
                    ${cita.observaciones ? `<p><strong>Observaciones:</strong> ${cita.observaciones}</p>` : ''}
                </div>
                <div class="appointment-actions">
                    <button class="btn btn-accept" data-action="accept-appointment-modal" data-appointment-id="${cita.id}">
                        ✅ Aceptar
                    </button>
                    <button class="btn btn-reject" data-action="reject-appointment-modal" data-appointment-id="${cita.id}">
                        ❌ Rechazar
                    </button>
                </div>
            </div>
        `).join('');

        return `
            <div class="modal-overlay">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>📅 Propuestas de Cita Pendientes</h3>
                        <button class="close-btn" data-action="close-notification-modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>Tienes <strong>${citas.length}</strong> propuesta(s) de cita esperando tu respuesta:</p>
                        <div class="appointments-list">
                            ${citasHTML}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-action="close-notification-modal">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Aceptar cita desde el modal
     */
    async acceptAppointment(appointmentId) {
        if (!confirm('¿Estás seguro de que quieres aceptar esta cita?')) {
            return;
        }

        try {
            const response = await fetch(`/appointments/${appointmentId}/accept`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                credentials: 'include',
                body: `csrf_token=${this.getCSRFToken()}`
            });

            if (response.ok || response.status === 302) {
                this.showSuccessMessage('Cita aceptada exitosamente');
                this.closeModal();
                // Recargar la página para actualizar el estado
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                this.showErrorMessage('Error al aceptar la cita');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showErrorMessage('Error al aceptar la cita');
        }
    }

    /**
     * Rechazar cita desde el modal
     */
    async rejectAppointment(appointmentId) {
        if (!confirm('¿Estás seguro de que quieres rechazar esta cita?')) {
            return;
        }

        try {
            const response = await fetch(`/appointments/${appointmentId}/reject`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                credentials: 'include',
                body: `csrf_token=${this.getCSRFToken()}`
            });

            if (response.ok || response.status === 302) {
                this.showSuccessMessage('Cita rechazada exitosamente');
                this.closeModal();
                // Recargar la página para actualizar el estado
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                this.showErrorMessage('Error al rechazar la cita');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showErrorMessage('Error al rechazar la cita');
        }
    }

    /**
     * Cerrar modal
     */
    closeModal() {
        const modal = document.getElementById('appointment-notification-modal');
        if (modal) {
            modal.classList.remove('show');
            setTimeout(() => {
                modal.remove();
                this.isModalOpen = false;
            }, 300);
        }
    }

    /**
     * Obtener CSRF token
     */
    getCSRFToken() {
        return document.querySelector('input[name="csrf_token"]')?.value || 
               document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    /**
     * Mostrar mensaje de éxito
     */
    showSuccessMessage(message) {
        this.showNotification(message, 'success');
    }

    /**
     * Mostrar mensaje de error
     */
    showErrorMessage(message) {
        this.showNotification(message, 'error');
    }

    /**
     * Mostrar notificación
     */
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 5000);
    }

    /**
     * Detener verificación periódica
     */
    destroy() {
        if (this.checkInterval) {
            clearInterval(this.checkInterval);
        }
        this.closeModal();
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.appointmentNotifications = new AppointmentNotifications();
}); 