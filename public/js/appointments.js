/**
 * JavaScript para el Sistema de Citas
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 */

class AppointmentCalendar {
    constructor() {
        this.currentDate = new Date();
        this.selectedDate = null;
        this.appointments = [];
        this.init();
    }

    init() {
        this.loadAppointments();
        this.setupEventListeners();
        this.setupCalendarNavigation();
    }

    /**
     * Cargar citas desde la API
     */
    async loadAppointments() {
        try {
            const response = await fetch('/api/appointments', {
                credentials: 'include'
            });
            if (response.ok) {
                this.appointments = await response.json();
                this.renderCalendar();
            } else {
                console.error('Error cargando citas:', response.statusText);
            }
        } catch (error) {
            console.error('Error cargando citas:', error);
        }
    }

    /**
     * Configurar event listeners
     */
    setupEventListeners() {
        // Event listeners para filtros
        const estadoFilter = document.getElementById('estado');
        if (estadoFilter) {
            estadoFilter.addEventListener('change', () => this.filterAppointments());
        }

        const fechaFilter = document.getElementById('fecha');
        if (fechaFilter) {
            fechaFilter.addEventListener('change', () => this.filterAppointments());
        }

        // Event listeners para acciones de citas
        this.setupAppointmentActions();
    }

    /**
     * Configurar navegación del calendario
     */
    setupCalendarNavigation() {
        const prevBtn = document.querySelector('[data-calendar-prev]');
        const nextBtn = document.querySelector('[data-calendar-next]');
        const todayBtn = document.querySelector('[data-calendar-today]');

        if (prevBtn) {
            prevBtn.addEventListener('click', () => this.navigateMonth(-1));
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => this.navigateMonth(1));
        }

        if (todayBtn) {
            todayBtn.addEventListener('click', () => this.goToToday());
        }
    }

    /**
     * Configurar acciones de citas
     */
    setupAppointmentActions() {
        // Botones de aceptar/rechazar
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-action="accept-appointment"]')) {
                this.acceptAppointment(e.target.dataset.appointmentId);
            }

            if (e.target.matches('[data-action="reject-appointment"]')) {
                this.rejectAppointment(e.target.dataset.appointmentId);
            }

            if (e.target.matches('[data-action="cancel-appointment"]')) {
                this.cancelAppointment(e.target.dataset.appointmentId);
            }

            if (e.target.matches('[data-action="complete-appointment"]')) {
                this.completeAppointment(e.target.dataset.appointmentId);
            }
        });
    }

    /**
     * Navegar entre meses
     */
    navigateMonth(direction) {
        const currentUrl = new URL(window.location);
        let mes = parseInt(currentUrl.searchParams.get('mes')) || this.currentDate.getMonth() + 1;
        let anio = parseInt(currentUrl.searchParams.get('anio')) || this.currentDate.getFullYear();

        mes += direction;

        if (mes > 12) {
            mes = 1;
            anio++;
        } else if (mes < 1) {
            mes = 12;
            anio--;
        }

        currentUrl.searchParams.set('mes', mes);
        currentUrl.searchParams.set('anio', anio);
        window.location.href = currentUrl.toString();
    }

    /**
     * Ir al mes actual
     */
    goToToday() {
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('mes', this.currentDate.getMonth() + 1);
        currentUrl.searchParams.set('anio', this.currentDate.getFullYear());
        window.location.href = currentUrl.toString();
    }

    /**
     * Filtrar citas
     */
    filterAppointments() {
        const estado = document.getElementById('estado')?.value;
        const fecha = document.getElementById('fecha')?.value;

        let url = new URL(window.location);
        
        if (estado) {
            url.searchParams.set('estado', estado);
        } else {
            url.searchParams.delete('estado');
        }

        if (fecha) {
            url.searchParams.set('fecha', fecha);
        } else {
            url.searchParams.delete('fecha');
        }

        url.searchParams.delete('page'); // Resetear paginación
        window.location.href = url.toString();
    }

    /**
     * Aceptar cita
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

            // Si es un redirect (302), recargar la página
            if (response.status === 302 || response.redirected) {
                window.location.reload();
                return;
            }
            
            // Si es una respuesta exitosa
            if (response.ok) {
                window.location.reload();
            } else {
                // Intentar leer el texto de la respuesta para debug
                const text = await response.text();
                alert('Error al aceptar la cita: ' + response.status);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al aceptar la cita: ' + error.message);
        }
    }

    /**
     * Rechazar cita
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

            // Si es un redirect (302), recargar la página
            if (response.status === 302 || response.redirected) {
                window.location.reload();
                return;
            }
            
            // Si es una respuesta exitosa
            if (response.ok) {
                window.location.reload();
            } else {
                // Intentar leer el texto de la respuesta para debug
                const text = await response.text();
                alert('Error al rechazar la cita: ' + response.status);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al rechazar la cita: ' + error.message);
        }
    }

    /**
     * Cancelar cita
     */
    async cancelAppointment(appointmentId) {
        if (!confirm('¿Estás seguro de que quieres cancelar esta cita?')) {
            return;
        }

        try {
            const response = await fetch(`/appointments/${appointmentId}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                credentials: 'include',
                body: `csrf_token=${this.getCSRFToken()}`
            });

            // Si es un redirect (302), recargar la página
            if (response.status === 302 || response.redirected) {
                window.location.reload();
                return;
            }
            
            // Si es una respuesta exitosa
            if (response.ok) {
                window.location.reload();
            } else {
                // Intentar leer el texto de la respuesta para debug
                const text = await response.text();
                alert('Error al cancelar la cita: ' + response.status);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al cancelar la cita: ' + error.message);
        }
    }

    /**
     * Completar cita
     */
    async completeAppointment(appointmentId) {
        if (!confirm('¿Confirmar que la cita se realizó?')) {
            return;
        }

        try {
            const response = await fetch(`/appointments/${appointmentId}/complete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                credentials: 'include',
                body: `csrf_token=${this.getCSRFToken()}`
            });


            
            // Si es un redirect (302), recargar la página
            if (response.status === 302 || response.redirected) {
                window.location.reload();
                return;
            }
            
            // Si es una respuesta exitosa
            if (response.ok) {
                window.location.reload();
            } else {
                // Intentar leer el texto de la respuesta para debug
                const text = await response.text();

                alert('Error al marcar la cita como completada: ' + response.status);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al marcar la cita como completada: ' + error.message);
        }
    }

    /**
     * Renderizar calendario
     */
    renderCalendar() {
        // Esta función se puede expandir para un calendario más interactivo
        // Por ahora, el calendario se renderiza en el servidor

    }

    /**
     * Obtener CSRF token
     */
    getCSRFToken() {
        return document.querySelector('input[name="csrf_token"]')?.value || 
               document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    /**
     * Verificar disponibilidad de horario
     */
    async checkTimeSlotAvailability(agenteId, fecha, hora, excludeId = null) {
        try {
            const params = new URLSearchParams({
                agente_id: agenteId,
                fecha: fecha,
                hora: hora
            });

            if (excludeId) {
                params.append('exclude_id', excludeId);
            }

            const response = await fetch(`/api/appointments/check-availability?${params}`, {
                credentials: 'include'
            });
            const result = await response.json();

            return result.available;
        } catch (error) {
            console.error('Error verificando disponibilidad:', error);
            return false;
        }
    }

    /**
     * Mostrar notificación
     */
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            type === 'warning' ? 'bg-yellow-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 5000);
    }
}

// Funciones de utilidad
const AppointmentUtils = {
    /**
     * Formatear fecha
     */
    formatDate(date, format = 'dd/mm/yyyy') {
        const d = new Date(date);
        const day = String(d.getDate()).padStart(2, '0');
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const year = d.getFullYear();

        return format
            .replace('dd', day)
            .replace('mm', month)
            .replace('yyyy', year);
    },

    /**
     * Formatear hora
     */
    formatTime(time) {
        return time.substring(0, 5); // HH:mm
    },

    /**
     * Obtener estado de cita con color
     */
    getAppointmentStatus(status) {
        const statuses = {
            'propuesta': { label: 'Propuesta', color: 'yellow' },
            'aceptada': { label: 'Aceptada', color: 'green' },
            'rechazada': { label: 'Rechazada', color: 'red' },
            'realizada': { label: 'Realizada', color: 'purple' },
            'cancelada': { label: 'Cancelada', color: 'gray' }
        };

        return statuses[status] || { label: 'Desconocido', color: 'gray' };
    },

    /**
     * Obtener tipo de cita
     */
    getAppointmentType(type) {
        const types = {
            'visita_propiedad': 'Visita a Propiedad',
            'reunion_oficina': 'Reunión en Oficina',
            'video_llamada': 'Video Llamada'
        };

        return types[type] || 'Desconocido';
    },

    /**
     * Validar fecha futura
     */
    isValidFutureDate(date, time) {
        const dateTime = new Date(`${date} ${time}`);
        const now = new Date();
        return dateTime > now;
    },

    /**
     * Calcular tiempo restante hasta la cita
     */
    getTimeUntilAppointment(appointmentDate) {
        const now = new Date();
        const appointment = new Date(appointmentDate);
        const diff = appointment - now;

        if (diff <= 0) {
            return 'La cita ya pasó';
        }

        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

        if (days > 0) {
            return `${days} día${days > 1 ? 's' : ''}, ${hours} hora${hours > 1 ? 's' : ''}`;
        } else if (hours > 0) {
            return `${hours} hora${hours > 1 ? 's' : ''}, ${minutes} minuto${minutes > 1 ? 's' : ''}`;
        } else {
            return `${minutes} minuto${minutes > 1 ? 's' : ''}`;
        }
    }
};

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar calendario si estamos en la página de citas
    if (window.location.pathname.includes('/appointments')) {
        window.appointmentCalendar = new AppointmentCalendar();
    }

    // Configurar validaciones de formularios
    setupFormValidations();
});

/**
 * Configurar validaciones de formularios
 */
function setupFormValidations() {
    const forms = document.querySelectorAll('form[data-validate="appointment"]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateAppointmentForm(this)) {
                e.preventDefault();
            }
        });
    });
}

/**
 * Validar formulario de cita
 */
function validateAppointmentForm(form) {
    const fecha = form.querySelector('[name="fecha_cita"]')?.value;
    const hora = form.querySelector('[name="hora_cita"]')?.value;
    const lugar = form.querySelector('[name="lugar"]')?.value;
    const tipoCita = form.querySelector('[name="tipo_cita"]')?.value;

    let isValid = true;
    let errors = [];

    // Validar fecha y hora
    if (!fecha || !hora) {
        errors.push('Fecha y hora son requeridas');
        isValid = false;
    } else if (!AppointmentUtils.isValidFutureDate(fecha, hora)) {
        errors.push('La fecha y hora deben ser futuras');
        isValid = false;
    }

    // Validar lugar
    if (!lugar || lugar.trim().length < 3) {
        errors.push('El lugar debe tener al menos 3 caracteres');
        isValid = false;
    }

    // Validar tipo de cita
    if (!tipoCita) {
        errors.push('Debe seleccionar un tipo de cita');
        isValid = false;
    }

    // Mostrar errores si los hay
    if (!isValid) {
        alert('Por favor corrija los siguientes errores:\n' + errors.join('\n'));
    }

    return isValid;
}

// Exportar para uso global
window.AppointmentUtils = AppointmentUtils; 