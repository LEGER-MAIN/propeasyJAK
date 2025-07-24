<?php
// Vista: Calendario de Citas
// El controlador ya maneja la captura de contenido, no necesitamos ob_start() aquí
?>

<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Calendario de Citas</h1>
                <p class="text-gray-600 mt-2">Visualiza y gestiona tus citas en formato calendario</p>
            </div>
            <div class="flex space-x-3">
                <a href="/appointments" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Lista
                </a>
                <a href="/appointments/create" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Nueva Cita
                </a>
            </div>
        </div>
    </div>

    <!-- Controles del Calendario -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <button id="prevMonth" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    
                    <h2 id="currentMonth" class="text-xl font-semibold text-gray-900">
                        <?= date('F Y') ?>
                    </h2>
                    
                    <button id="nextMonth" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                    
                    <button id="today" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        Hoy
                    </button>
                </div>
                
                <div class="flex items-center space-x-4">
                    <select id="viewType" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="month">Mes</option>
                        <option value="week">Semana</option>
                        <option value="day">Día</option>
                    </select>
                    
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600">Filtrar por estado:</span>
                        <select id="statusFilter" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Todos</option>
                            <option value="propuesta">Propuesta</option>
                            <option value="aceptada">Aceptada</option>
                            <option value="rechazada">Rechazada</option>
                            <option value="realizada">Realizada</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendario -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div id="calendar" class="p-6">
            <!-- El calendario se renderizará aquí con JavaScript -->
            <div class="text-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                <p class="mt-4 text-gray-600">Cargando calendario...</p>
            </div>
        </div>
    </div>

    <!-- Modal para Detalles de Cita -->
    <div id="appointmentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 id="modalTitle" class="text-lg font-medium text-gray-900">Detalles de la Cita</h3>
                    <button id="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div id="modalContent" class="space-y-4">
                    <!-- El contenido se llenará dinámicamente -->
                </div>
                
                <div id="modalActions" class="flex justify-end space-x-3 mt-6">
                    <!-- Las acciones se llenarán dinámicamente -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Incluir FullCalendar CSS y JS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let calendar;
    let currentDate = new Date();
    
    // Inicializar calendario
    function initCalendar() {
        const calendarEl = document.getElementById('calendar');
        
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            headerToolbar: false, // Usamos nuestros propios controles
            height: 'auto',
            selectable: true,
            selectMirror: true,
            dayMaxEvents: true,
            weekends: true,
            events: function(fetchInfo, successCallback, failureCallback) {
                // Cargar eventos desde el servidor
                fetch('/api/appointments?' + new URLSearchParams({
                    start: fetchInfo.startStr,
                    end: fetchInfo.endStr,
                    status: document.getElementById('statusFilter').value
                }))
                .then(response => response.json())
                .then(data => {
                    const events = data.map(cita => ({
                        id: cita.id,
                        title: cita.cliente_nombre + ' ' + cita.cliente_apellido,
                        start: cita.fecha_cita,
                        end: new Date(new Date(cita.fecha_cita).getTime() + 60 * 60 * 1000), // 1 hora
                        backgroundColor: getEventColor(cita.estado),
                        borderColor: getEventColor(cita.estado),
                        extendedProps: {
                            estado: cita.estado,
                            tipo: cita.tipo_cita,
                            lugar: cita.lugar,
                            observaciones: cita.observaciones,
                            cliente_email: cita.cliente_email,
                            cliente_telefono: cita.cliente_telefono,
                            propiedad_titulo: cita.propiedad_titulo
                        }
                    }));
                    successCallback(events);
                })
                .catch(error => {
                    console.error('Error cargando citas:', error);
                    failureCallback(error);
                });
            },
            eventClick: function(info) {
                showAppointmentModal(info.event);
            },
            dateClick: function(info) {
                // Redirigir a crear nueva cita en esa fecha
                window.location.href = '/appointments/create?fecha=' + info.dateStr;
            }
        });
        
        calendar.render();
        updateMonthDisplay();
    }
    
    // Obtener color del evento según estado
    function getEventColor(estado) {
        const colors = {
            'propuesta': '#fbbf24', // yellow-400
            'aceptada': '#10b981', // green-500
            'rechazada': '#ef4444', // red-500
            'realizada': '#3b82f6', // blue-500
            'cancelada': '#6b7280'  // gray-500
        };
        return colors[estado] || '#6b7280';
    }
    
    // Mostrar modal de detalles
    function showAppointmentModal(event) {
        const modal = document.getElementById('appointmentModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalContent = document.getElementById('modalContent');
        const modalActions = document.getElementById('modalActions');
        
        const props = event.extendedProps;
        const startTime = new Date(event.start).toLocaleTimeString('es-ES', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        
        modalTitle.textContent = event.title;
        
        modalContent.innerHTML = `
            <div class="space-y-3">
                <div>
                    <span class="font-medium text-gray-700">Fecha y Hora:</span>
                    <p class="text-gray-900">${new Date(event.start).toLocaleDateString('es-ES')} a las ${startTime}</p>
                </div>
                <div>
                    <span class="font-medium text-gray-700">Estado:</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-${getStatusColorClass(props.estado)}">
                        ${props.estado.charAt(0).toUpperCase() + props.estado.slice(1)}
                    </span>
                </div>
                <div>
                    <span class="font-medium text-gray-700">Tipo:</span>
                    <p class="text-gray-900">${props.tipo.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}</p>
                </div>
                <div>
                    <span class="font-medium text-gray-700">Lugar:</span>
                    <p class="text-gray-900">${props.lugar}</p>
                </div>
                <div>
                    <span class="font-medium text-gray-700">Propiedad:</span>
                    <p class="text-gray-900">${props.propiedad_titulo}</p>
                </div>
                ${props.observaciones ? `
                <div>
                    <span class="font-medium text-gray-700">Observaciones:</span>
                    <p class="text-gray-900">${props.observaciones}</p>
                </div>
                ` : ''}
            </div>
        `;
        
        // Configurar acciones según el estado
        let actionsHTML = `
            <a href="/appointments/${event.id}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                Ver Detalles
            </a>
        `;
        
        if (props.estado === 'propuesta') {
            actionsHTML += `
                <a href="/appointments/${event.id}/edit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                    Editar
                </a>
            `;
        }
        
        modalActions.innerHTML = actionsHTML;
        
        modal.classList.remove('hidden');
    }
    
    // Obtener clase de color para estado
    function getStatusColorClass(estado) {
        const colors = {
            'propuesta': 'yellow-100 text-yellow-800',
            'aceptada': 'green-100 text-green-800',
            'rechazada': 'red-100 text-red-800',
            'realizada': 'blue-100 text-blue-800',
            'cancelada': 'gray-100 text-gray-800'
        };
        return colors[estado] || 'gray-100 text-gray-800';
    }
    
    // Actualizar display del mes
    function updateMonthDisplay() {
        const monthNames = [
            'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];
        document.getElementById('currentMonth').textContent = 
            monthNames[currentDate.getMonth()] + ' ' + currentDate.getFullYear();
    }
    
    // Event listeners
    document.getElementById('prevMonth').addEventListener('click', function() {
        calendar.prev();
        currentDate = calendar.getDate();
        updateMonthDisplay();
    });
    
    document.getElementById('nextMonth').addEventListener('click', function() {
        calendar.next();
        currentDate = calendar.getDate();
        updateMonthDisplay();
    });
    
    document.getElementById('today').addEventListener('click', function() {
        calendar.today();
        currentDate = calendar.getDate();
        updateMonthDisplay();
    });
    
    document.getElementById('viewType').addEventListener('change', function() {
        calendar.changeView(this.value);
    });
    
    document.getElementById('statusFilter').addEventListener('change', function() {
        calendar.refetchEvents();
    });
    
    document.getElementById('closeModal').addEventListener('click', function() {
        document.getElementById('appointmentModal').classList.add('hidden');
    });
    
    // Cerrar modal al hacer clic fuera
    document.getElementById('appointmentModal').addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });
    
    // Inicializar
    initCalendar();
});
</script>

 
