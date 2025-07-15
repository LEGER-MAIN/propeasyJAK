<?php
/**
 * Vista: Búsqueda de Clientes
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Esta vista permite a los agentes buscar clientes por nombre
 */

$content = ob_start();
?>

<!-- Hero Section -->
<div class="bg-gradient-to-r from-primary-600 to-primary-800 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center">
            <h1 class="text-4xl font-bold mb-4">Gestiona tus Clientes</h1>
            <p class="text-xl text-primary-100 mb-8">
                Encuentra y conecta con clientes potenciales para tus propiedades
            </p>
            
            <!-- Estadísticas rápidas -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto">
                <div class="text-center">
                    <div class="text-3xl font-bold"><?= $total ?></div>
                    <div class="text-primary-200">Clientes</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold"><?= count(array_unique(array_column($clientes, 'ciudad'))) ?></div>
                    <div class="text-primary-200">Ciudades</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold"><?= array_sum(array_column($clientes, 'total_solicitudes')) ?></div>
                    <div class="text-primary-200">Solicitudes</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold"><?= count(array_filter($clientes, function($c) { return !empty($c['telefono']); })) ?></div>
                    <div class="text-primary-200">Con Teléfono</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros de Búsqueda -->
<div class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <form method="GET" action="/buscar-clientes" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Nombre del Cliente -->
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre del Cliente</label>
                    <input type="text" name="nombre" id="nombre" 
                           value="<?= htmlspecialchars($nombre) ?>" 
                           placeholder="Buscar por nombre o apellido..."
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                </div>
                
                <!-- Botones -->
                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md font-medium transition-colors">
                        <i class="fas fa-search mr-2"></i>Buscar
                    </button>
                    <a href="/buscar-clientes" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md font-medium transition-colors">
                        <i class="fas fa-times mr-2"></i>Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Listado de Clientes -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Resultados -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">
            Clientes Registrados
            <?php if (!empty($nombre)): ?>
                <span class="text-lg font-normal text-gray-600">(<?= $total ?> resultados)</span>
            <?php endif; ?>
        </h2>
        
        <!-- Filtros activos -->
        <?php if (!empty($nombre)): ?>
            <div class="flex space-x-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary-100 text-primary-800">
                    <i class="fas fa-user mr-1"></i><?= htmlspecialchars($nombre) ?>
                </span>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if (empty($clientes)): ?>
        <!-- Estado vacío -->
        <div class="text-center py-12">
            <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                <i class="fas fa-users text-4xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No se encontraron clientes</h3>
            <p class="text-gray-600 mb-6">
                <?php if (!empty($nombre)): ?>
                    Intenta ajustar los filtros de búsqueda para encontrar más resultados.
                <?php else: ?>
                    No hay clientes registrados en el sistema en este momento.
                <?php endif; ?>
            </p>
            <?php if (!empty($nombre)): ?>
                <a href="/buscar-clientes" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md font-medium transition-colors">
                    Ver todos los clientes
                </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <!-- Tabla de clientes -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cliente
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Contacto
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ubicación
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Solicitudes
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Registro
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($clientes as $cliente): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-green-600 flex items-center justify-center">
                                                <span class="text-white text-sm font-bold">
                                                    <?= strtoupper(substr($cliente['nombre'], 0, 1) . substr($cliente['apellido'], 0, 1)) ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']) ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                ID: <?= $cliente['id'] ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <div class="flex items-center">
                                            <i class="fas fa-envelope w-4 mr-2 text-gray-400"></i>
                                            <?= htmlspecialchars($cliente['email']) ?>
                                        </div>
                                    </div>
                                    <?php if (!empty($cliente['telefono'])): ?>
                                        <div class="text-sm text-gray-500 mt-1">
                                            <div class="flex items-center">
                                                <i class="fas fa-phone w-4 mr-2 text-gray-400"></i>
                                                <?= htmlspecialchars($cliente['telefono']) ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?php if (!empty($cliente['ciudad'])): ?>
                                            <div class="flex items-center">
                                                <i class="fas fa-map-marker-alt w-4 mr-2 text-gray-400"></i>
                                                <?= htmlspecialchars($cliente['ciudad']) ?>
                                            </div>
                                            <?php if (!empty($cliente['sector'])): ?>
                                                <div class="text-sm text-gray-500 mt-1">
                                                    <?= htmlspecialchars($cliente['sector']) ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-gray-400">No especificada</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <?= $cliente['total_solicitudes'] ?> solicitudes
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= date('d/m/Y', strtotime($cliente['fecha_registro'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button type="button" 
                                                onclick="verSolicitudes(<?= $cliente['id'] ?>, '<?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']) ?>')"
                                                class="text-primary-600 hover:text-primary-900 transition-colors"
                                                title="Ver solicitudes">
                                            <i class="fas fa-list"></i>
                                        </button>
                                        <button type="button" 
                                                onclick="iniciarChat(<?= $cliente['id'] ?>, '<?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']) ?>')"
                                                class="text-green-600 hover:text-green-900 transition-colors"
                                                title="Contactar">
                                            <i class="fas fa-comments"></i>
                                        </button>
                                        <button type="button" 
                                                onclick="verPerfil(<?= $cliente['id'] ?>)"
                                                class="text-blue-600 hover:text-blue-900 transition-colors"
                                                title="Ver perfil">
                                            <i class="fas fa-user"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Paginación -->
        <?php if ($totalPages > 1): ?>
            <div class="mt-8">
                <nav class="flex justify-center">
                    <ul class="flex space-x-1">
                        <?php if ($page > 1): ?>
                            <li>
                                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" 
                                   class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                    <i class="fas fa-chevron-left"></i> Anterior
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <li>
                                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
                                   class="px-3 py-2 text-sm font-medium <?= $i === $page ? 'text-primary-600 bg-primary-50 border-primary-500' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-50' ?> border rounded-md">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <li>
                                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" 
                                   class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                    Siguiente <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Modal para iniciar chat -->
<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" id="chatModal">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Iniciar Conversación</h3>
                <button type="button" onclick="cerrarModal('chatModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mb-4">
                <p>¿Deseas iniciar una conversación con <strong id="clienteNombre"></strong>?</p>
                <p class="text-sm text-gray-600 mt-2">Se creará una nueva solicitud de compra y podrás chatear directamente con el cliente.</p>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="cerrarModal('chatModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                    Cancelar
                </button>
                <button type="button" id="confirmarChat" class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 transition-colors">
                    <i class="fas fa-comments mr-2"></i>Iniciar Chat
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver solicitudes -->
<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" id="solicitudesModal">
    <div class="relative top-10 mx-auto p-5 border w-4/5 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Solicitudes de <span id="clienteNombreSolicitudes"></span></h3>
                <button type="button" onclick="cerrarModal('solicitudesModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="solicitudesContent">
                <div class="text-center py-8">
                    <i class="fas fa-spinner fa-spin fa-2x text-gray-400 mb-3"></i>
                    <p class="text-gray-600">Cargando solicitudes...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let clienteSeleccionado = null;

function iniciarChat(clienteId, clienteNombre) {
    clienteSeleccionado = clienteId;
    document.getElementById('clienteNombre').textContent = clienteNombre;
    
    document.getElementById('chatModal').classList.remove('hidden');
}

function verSolicitudes(clienteId, clienteNombre) {
    document.getElementById('clienteNombreSolicitudes').textContent = clienteNombre;
    
    // Mostrar modal
    document.getElementById('solicitudesModal').classList.remove('hidden');
    
    // Cargar solicitudes del cliente
    fetch(`/api/solicitudes-cliente/${clienteId}`)
        .then(response => response.json())
        .then(data => {
            const content = document.getElementById('solicitudesContent');
            if (data.success && data.solicitudes.length > 0) {
                content.innerHTML = `
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Propiedad</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                ${data.solicitudes.map(solicitud => `
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-900">${solicitud.titulo_propiedad}</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-${getEstadoBadgeColor(solicitud.estado)}-100 text-${getEstadoBadgeColor(solicitud.estado)}-800">
                                                ${solicitud.estado}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">${new Date(solicitud.fecha_solicitud).toLocaleDateString()}</td>
                                        <td class="px-4 py-3">
                                            <a href="/chat/${solicitud.id}" class="text-primary-600 hover:text-primary-900 text-sm font-medium">
                                                <i class="fas fa-comments mr-1"></i>Chat
                                            </a>
                                        </td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
            } else {
                content.innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-inbox fa-2x text-gray-400 mb-3"></i>
                        <p class="text-gray-600">No hay solicitudes registradas</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            document.getElementById('solicitudesContent').innerHTML = `
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                    Error al cargar las solicitudes
                </div>
            `;
        });
}

function cerrarModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// Cerrar modal al hacer clic fuera de él
document.addEventListener('click', function(event) {
    const modals = ['chatModal', 'solicitudesModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal && !modal.classList.contains('hidden')) {
            if (event.target === modal) {
                cerrarModal(modalId);
            }
        }
    });
});

function verPerfil(clienteId) {
    window.open(`/perfil-cliente/${clienteId}`, '_blank');
}

document.getElementById('confirmarChat').addEventListener('click', function() {
    if (clienteSeleccionado) {
        // Redirigir al chat o crear nueva solicitud
        window.location.href = `/chat?nuevo_cliente=${clienteSeleccionado}`;
    }
});

function getEstadoBadgeColor(estado) {
    const colors = {
        'nuevo': 'blue',
        'en_revision': 'yellow',
        'reunion_agendada': 'indigo',
        'cerrado': 'gray'
    };
    return colors[estado] || 'gray';
}

// Búsqueda en tiempo real (opcional)
let searchTimeout;
document.getElementById('nombre').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        if (this.value.length >= 2 || this.value.length === 0) {
            document.querySelector('form').submit();
        }
    }, 500);
});
</script>

<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?> 