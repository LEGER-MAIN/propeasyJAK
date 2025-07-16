/**
 * JavaScript para el módulo de solicitudes de compra
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 */

class SolicitudesManager {
    constructor() {
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadStats();
    }

    bindEvents() {
        // Validación de formulario de solicitud
        const solicitudForm = document.querySelector('form[action="/solicitudes"]');
        if (solicitudForm) {
            solicitudForm.addEventListener('submit', this.handleSolicitudSubmit.bind(this));
        }

        // Validación de presupuestos
        const presupuestoMin = document.getElementById('presupuesto_min');
        const presupuestoMax = document.getElementById('presupuesto_max');
        
        if (presupuestoMin && presupuestoMax) {
            presupuestoMin.addEventListener('input', this.validarPresupuestos.bind(this));
            presupuestoMax.addEventListener('input', this.validarPresupuestos.bind(this));
        }

        // Formulario de actualización de estado
        const updateStatusForm = document.querySelector('form[action*="/update-status"]');
        if (updateStatusForm) {
            updateStatusForm.addEventListener('submit', this.handleStatusUpdate.bind(this));
        }

        // Botones de eliminación
        const deleteButtons = document.querySelectorAll('form[action*="/delete"]');
        deleteButtons.forEach(form => {
            form.addEventListener('submit', this.handleDelete.bind(this));
        });

        // Filtros de solicitudes
        const filterSelects = document.querySelectorAll('.solicitud-filter');
        filterSelects.forEach(select => {
            select.addEventListener('change', this.handleFilterChange.bind(this));
        });
    }

    handleSolicitudSubmit(e) {
        const presupuestoMin = parseFloat(document.getElementById('presupuesto_min')?.value) || 0;
        const presupuestoMax = parseFloat(document.getElementById('presupuesto_max')?.value) || 0;
        
        if (presupuestoMax > 0 && presupuestoMin > 0 && presupuestoMax < presupuestoMin) {
            e.preventDefault();
            this.showAlert('El presupuesto máximo debe ser mayor que el mínimo.', 'error');
            return false;
        }
        
        if (!confirm('¿Estás seguro de que quieres enviar esta solicitud de compra?')) {
            e.preventDefault();
            return false;
        }

        // Mostrar indicador de carga
        const submitButton = e.target.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Enviando...';
        }
    }

    validarPresupuestos() {
        const presupuestoMin = parseFloat(document.getElementById('presupuesto_min')?.value) || 0;
        const presupuestoMax = parseFloat(document.getElementById('presupuesto_max')?.value) || 0;
        
        if (presupuestoMax > 0 && presupuestoMin > 0 && presupuestoMax < presupuestoMin) {
            document.getElementById('presupuesto_max').setCustomValidity('El presupuesto máximo debe ser mayor que el mínimo');
        } else {
            document.getElementById('presupuesto_max').setCustomValidity('');
        }
    }

    handleStatusUpdate(e) {
        const estado = e.target.querySelector('select[name="estado"]').value;
        const respuesta = e.target.querySelector('textarea[name="respuesta"]').value;
        
        if (!estado) {
            e.preventDefault();
            this.showAlert('Debes seleccionar un estado.', 'error');
            return false;
        }

        if (!confirm('¿Estás seguro de que quieres actualizar el estado de esta solicitud?')) {
            e.preventDefault();
            return false;
        }

        // Mostrar indicador de carga
        const submitButton = e.target.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Actualizando...';
        }
    }

    handleDelete(e) {
        if (!confirm('¿Estás seguro de que quieres eliminar esta solicitud? Esta acción no se puede deshacer.')) {
            e.preventDefault();
            return false;
        }

        // Mostrar indicador de carga
        const submitButton = e.target.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Eliminando...';
        }
    }

    handleFilterChange(e) {
        const filterType = e.target.dataset.filter;
        const value = e.target.value;
        
        // Construir URL con filtros
        const url = new URL(window.location);
        if (value) {
            url.searchParams.set(filterType, value);
        } else {
            url.searchParams.delete(filterType);
        }
        
        // Recargar página con filtros
        window.location.href = url.toString();
    }

    async loadStats() {
        try {
            const response = await fetch('/api/solicitudes/stats');
            const data = await response.json();
            
            if (data.success) {
                this.updateStatsDisplay(data.estadisticas);
            }
        } catch (error) {
            console.error('Error al cargar estadísticas:', error);
        }
    }

    updateStatsDisplay(stats) {
        // Actualizar contadores en el dashboard si existen
        const totalElement = document.getElementById('stats-total');
        const nuevasElement = document.getElementById('stats-nuevas');
        const revisionElement = document.getElementById('stats-revision');
        const reunionElement = document.getElementById('stats-reunion');
        const cerradasElement = document.getElementById('stats-cerradas');

        if (totalElement) totalElement.textContent = stats.total_solicitudes || 0;
        if (nuevasElement) nuevasElement.textContent = stats.solicitudes_nuevas || 0;
        if (revisionElement) revisionElement.textContent = stats.solicitudes_revision || 0;
        if (reunionElement) reunionElement.textContent = stats.solicitudes_reunion || 0;
        if (cerradasElement) cerradasElement.textContent = stats.solicitudes_cerradas || 0;
    }

    showAlert(message, type = 'info') {
        // Crear elemento de alerta
        const alertDiv = document.createElement('div');
        alertDiv.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 ${
            type === 'error' ? 'bg-red-500 text-white' :
            type === 'success' ? 'bg-green-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        
        alertDiv.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-${type === 'error' ? 'exclamation-triangle' : type === 'success' ? 'check-circle' : 'info-circle'} mr-2"></i>
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        document.body.appendChild(alertDiv);
        
        // Auto-remover después de 5 segundos
        setTimeout(() => {
            if (alertDiv.parentElement) {
                alertDiv.remove();
            }
        }, 5000);
    }

    // Método para marcar solicitudes como leídas
    async markAsRead(solicitudId) {
        try {
            const response = await fetch(`/api/solicitudes/${solicitudId}/mark-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            if (data.success) {
                // Actualizar indicador visual si es necesario
                const unreadIndicator = document.querySelector(`[data-solicitud-id="${solicitudId}"] .unread-indicator`);
                if (unreadIndicator) {
                    unreadIndicator.classList.add('hidden');
                }
            }
        } catch (error) {
            console.error('Error al marcar como leída:', error);
        }
    }

    // Método para exportar solicitudes
    exportSolicitudes(format = 'csv') {
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('export', format);
        
        window.location.href = currentUrl.toString();
    }

    // Método para buscar solicitudes
    async searchSolicitudes(query) {
        try {
            const response = await fetch(`/api/solicitudes/search?q=${encodeURIComponent(query)}`);
            const data = await response.json();
            
            if (data.success) {
                this.updateSolicitudesList(data.solicitudes);
            }
        } catch (error) {
            console.error('Error en la búsqueda:', error);
        }
    }

    updateSolicitudesList(solicitudes) {
        const container = document.getElementById('solicitudes-list');
        if (!container) return;

        if (solicitudes.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">No se encontraron solicitudes</p>
                </div>
            `;
            return;
        }

        // Actualizar lista con los resultados
        container.innerHTML = solicitudes.map(solicitud => this.renderSolicitudItem(solicitud)).join('');
    }

    renderSolicitudItem(solicitud) {
        const estadoClass = this.getEstadoBadgeClass(solicitud.estado);
        const estadoText = this.getEstadoText(solicitud.estado);
        
        return `
            <div class="border-b border-gray-200 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <h3 class="text-lg font-medium text-gray-900">${solicitud.titulo_propiedad}</h3>
                        <p class="text-sm text-gray-500">${solicitud.ciudad_propiedad}, ${solicitud.sector_propiedad}</p>
                        <p class="text-sm text-gray-500">Fecha: ${new Date(solicitud.fecha_solicitud).toLocaleDateString()}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${estadoClass}">
                            ${estadoText}
                        </span>
                        <a href="/solicitudes/${solicitud.id}" class="text-primary-600 hover:text-primary-900 text-sm font-medium">
                            Ver detalles
                        </a>
                    </div>
                </div>
            </div>
        `;
    }

    getEstadoBadgeClass(estado) {
        const classes = {
            'nuevo': 'bg-yellow-100 text-yellow-800',
            'en_revision': 'bg-blue-100 text-blue-800',
            'reunion_agendada': 'bg-green-100 text-green-800',
            'cerrado': 'bg-gray-100 text-gray-800'
        };
        return classes[estado] || 'bg-gray-100 text-gray-800';
    }

    getEstadoText(estado) {
        const texts = {
            'nuevo': 'Nuevo',
            'en_revision': 'En Revisión',
            'reunion_agendada': 'Reunión Agendada',
            'cerrado': 'Cerrado'
        };
        return texts[estado] || 'Desconocido';
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    new SolicitudesManager();
});

// Exportar para uso global
window.SolicitudesManager = SolicitudesManager; 