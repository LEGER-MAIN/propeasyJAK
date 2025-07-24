<?php
/**
 * Vista: Propiedades del Agente
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Esta vista muestra las propiedades asignadas a un agente inmobiliario
 */

require_once APP_PATH . '/helpers/PropertyHelper.php';

// Incluir el layout principal
$content = ob_start();
?>

<!-- Contenido Principal -->
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header con diseño moderno -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-home text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold text-gray-900">
                            Mis Propiedades
                        </h1>
                        <p class="text-gray-700 mt-2 text-lg font-medium">Gestiona las propiedades que tienes asignadas</p>
                    </div>
                </div>
                <div class="hidden md:flex items-center space-x-3">
                    <div class="text-right">
                        <p class="text-sm text-gray-600 font-medium">Última actualización</p>
                        <p class="text-sm font-semibold text-gray-900"><?= date('d/m/Y H:i') ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Estadísticas con diseño moderno -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Total</p>
                            <p class="text-3xl font-bold text-gray-900"><?= count($properties) ?></p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center shadow-sm">
                            <i class="fas fa-home text-blue-700 text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-700">En Revisión</p>
                            <p class="text-3xl font-bold text-amber-700"><?= count(array_filter($properties, function($p) { return $p['estado_publicacion'] === 'en_revision'; })) ?></p>
                        </div>
                        <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center shadow-sm">
                            <i class="fas fa-clock text-amber-700 text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Activas</p>
                            <p class="text-3xl font-bold text-emerald-700"><?= count(array_filter($properties, function($p) { return $p['estado_publicacion'] === 'activa'; })) ?></p>
                        </div>
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center shadow-sm">
                            <i class="fas fa-check text-emerald-700 text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Rechazadas</p>
                            <p class="text-3xl font-bold text-red-700"><?= count(array_filter($properties, function($p) { return $p['estado_publicacion'] === 'rechazada'; })) ?></p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center shadow-sm">
                            <i class="fas fa-times text-red-700 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros con diseño moderno -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 mb-8 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-800 to-gray-900 px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-filter mr-3 text-white"></i>
                    Filtros de Propiedades
                </h3>
            </div>
            <div class="p-6">
                <form method="GET" action="/properties/agent/list" class="flex items-center space-x-4">
                    <div class="flex-1">
                        <label for="estado" class="block text-sm font-semibold text-gray-700 mb-2">Estado</label>
                        <select name="estado" id="estado" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Todos los estados</option>
                            <option value="activa" <?= ($estado === 'activa') ? 'selected' : '' ?>>Activas</option>
                            <option value="vendida" <?= ($estado === 'vendida') ? 'selected' : '' ?>>Vendidas</option>
                            <option value="en_revision" <?= ($estado === 'en_revision') ? 'selected' : '' ?>>En Revisión</option>
                            <option value="rechazada" <?= ($estado === 'rechazada') ? 'selected' : '' ?>>Rechazadas</option>
                        </select>
                    </div>
                    
                    <div class="flex items-end space-x-3">
                        <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl">
                            <i class="fas fa-filter mr-2"></i>Filtrar
                        </button>
                        <a href="/properties/pending-validation" class="bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl">
                            <i class="fas fa-clock mr-2"></i>Pendientes
                        </a>
                        <a href="/properties/create" class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl">
                            <i class="fas fa-plus mr-2"></i>Nueva
                        </a>
                    </div>
                </form>
            </div>
        </div>



        <!-- Listado de propiedades con diseño moderno -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-800 to-gray-900 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-list mr-3 text-white"></i>
                        Mis Propiedades
                    </h3>
                    <div class="flex items-center space-x-4">
                        <span class="bg-blue-600 text-white text-sm font-semibold px-3 py-1 rounded-full border border-blue-500">
                            <?= count($properties) ?> propiedades
                        </span>
                        <div class="text-xs text-gray-300">v2.6.1</div>
                    </div>
                </div>
            </div>
            
            <?php if (empty($properties)): ?>
                <div class="text-center py-16">
                    <div class="w-24 h-24 mx-auto mb-6 bg-blue-100 rounded-full flex items-center justify-center shadow-lg">
                        <i class="fas fa-home text-4xl text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">No tienes propiedades asignadas</h3>
                    <p class="text-gray-700 font-medium mb-8 max-w-md mx-auto">
                        <?php if ($estado): ?>
                            No hay propiedades con el estado seleccionado.
                        <?php else: ?>
                            Comienza creando tu primera propiedad o espera a que se te asignen algunas.
                        <?php endif; ?>
                    </p>
                    <a href="/properties/create" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-8 py-3 rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl">
                        <i class="fas fa-plus mr-2"></i>Crear Primera Propiedad
                    </a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                Propiedad
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                Tipo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                Precio
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                Ubicación
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                Estado
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                Fecha
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($properties as $property): ?>
                            <tr class="hover:bg-gray-50 hover:shadow-md transition-all duration-200 rounded-lg">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-12">
                                            <?php if (!empty($property['imagen_principal'])): ?>
                                                <img class="h-12 w-12 rounded-lg object-cover" 
                                                     src="<?= htmlspecialchars($property['imagen_principal']) ?>" 
                                                     alt="<?= htmlspecialchars($property['titulo']) ?>">
                                            <?php else: ?>
                                                <div class="h-12 w-12 rounded-lg bg-gray-200 flex items-center justify-center">
                                                    <i class="fas fa-home text-gray-400"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <a href="/properties/show/<?= $property['id'] ?>" class="hover:text-primary-600">
                                                    <?= htmlspecialchars($property['titulo']) ?>
                                                </a>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                ID: <?= $property['id'] ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-primary-100 text-primary-800 shadow-sm border border-primary-200">
                                        <?= getPropertyTypeDisplayName($property['tipo']) ?>
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        $<?= number_format($property['precio']) ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?= $property['moneda'] ?>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?= htmlspecialchars($property['ciudad']) ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?= htmlspecialchars($property['sector']) ?>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $estadoClass = '';
                                    $estadoText = '';
                                    switch ($property['estado_publicacion']) {
                                        case 'activa':
                                            $estadoClass = 'bg-green-100 text-green-800';
                                            $estadoText = 'Activa';
                                            break;
                                        case 'vendida':
                                            $estadoClass = 'bg-blue-100 text-blue-800';
                                            $estadoText = 'Vendida';
                                            break;
                                        case 'en_revision':
                                            $estadoClass = 'bg-amber-100 text-amber-800 border border-amber-300 font-semibold';
                                            $estadoText = 'En Revisión';
                                            break;
                                        case 'rechazada':
                                            $estadoClass = 'bg-red-100 text-red-800';
                                            $estadoText = 'Rechazada';
                                            break;
                                    }
                                    ?>
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?= $estadoClass ?> shadow-sm">
                                            <?= $estadoText ?>
                                        </span>
                                        <button onclick="showStatusModal(<?= $property['id'] ?>, '<?= $property['estado_publicacion'] ?>', '<?= htmlspecialchars($property['titulo']) ?>')" 
                                                class="text-gray-400 hover:text-blue-600 hover:bg-blue-50 p-1 rounded transition-all duration-200" title="Cambiar estado">
                                            <i class="fas fa-edit text-xs"></i>
                                        </button>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= date('d/m/Y', strtotime($property['fecha_creacion'])) ?>
                                </td>
                                

                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-3">
                                        <a href="/properties/show/<?= $property['id'] ?>" 
                                           class="text-primary-600 hover:text-white hover:bg-primary-600 p-2 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-110" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <?php if ($property['estado_publicacion'] !== 'vendida'): ?>
                                            <a href="/properties/<?= $property['id'] ?>/edit" 
                                               class="text-blue-600 hover:text-white hover:bg-blue-600 p-2 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-110" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if ($property['estado_publicacion'] === 'en_revision'): ?>
                                            <a href="/properties/pending-validation" 
                                               class="text-yellow-600 hover:text-white hover:bg-yellow-600 p-2 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-110" title="Validar">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal para cambiar estado -->
<div id="statusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Cambiar Estado de Propiedad</h3>
                <button onclick="closeStatusModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="statusForm" method="POST" action="/properties/update-status">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                <input type="hidden" id="propertyId" name="property_id">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Propiedad</label>
                    <p id="propertyTitle" class="text-sm text-gray-600 bg-gray-50 p-2 rounded"></p>
                </div>
                
                <div class="mb-4">
                    <label for="newStatus" class="block text-sm font-medium text-gray-700 mb-2">Nuevo Estado</label>
                    <select id="newStatus" name="new_status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        <option value="activa">Activa</option>
                        <option value="vendida">Vendida</option>
                        <option value="en_revision">En Revisión</option>
                        <option value="rechazada">Rechazada</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="statusComment" class="block text-sm font-medium text-gray-700 mb-2">Comentario (opcional)</label>
                    <textarea id="statusComment" name="comment" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500" placeholder="Comentario sobre el cambio de estado..."></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeStatusModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-md transition-colors">
                        <i class="fas fa-save mr-2"></i>Guardar Cambio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showStatusModal(propertyId, currentStatus, propertyTitle) {
    document.getElementById('propertyId').value = propertyId;
    document.getElementById('propertyTitle').textContent = propertyTitle;
    document.getElementById('newStatus').value = currentStatus;
    document.getElementById('statusModal').classList.remove('hidden');
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.add('hidden');
}

// Cerrar modal al hacer clic fuera de él
document.getElementById('statusModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeStatusModal();
    }
});

// Manejar envío del formulario
document.getElementById('statusForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/properties/update-status', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar mensaje de éxito
            alert('Estado actualizado correctamente');
            // Recargar la página para mostrar los cambios
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar el estado');
    });
});
</script>

<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?> 