<?php
/**
 * Vista: Propiedades del Agente
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Esta vista muestra las propiedades asignadas a un agente inmobiliario
 */

// Incluir el layout principal
$content = ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Mis Propiedades</h1>
            <p class="text-gray-600 mt-2">Gestiona las propiedades que tienes asignadas</p>
        </div>
        
        <div class="flex space-x-3">
            <a href="/properties/pending-validation" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md font-medium transition-colors">
                <i class="fas fa-clock mr-2"></i>Pendientes de Validación
            </a>
            <a href="/properties/create" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>Nueva Propiedad
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" action="/properties/agent/list" class="flex items-center space-x-4">
            <div>
                <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="estado" id="estado" class="border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Todos los estados</option>
                    <option value="activa" <?= ($estado === 'activa') ? 'selected' : '' ?>>Activas</option>
                    <option value="vendida" <?= ($estado === 'vendida') ? 'selected' : '' ?>>Vendidas</option>
                    <option value="en_revision" <?= ($estado === 'en_revision') ? 'selected' : '' ?>>En Revisión</option>
                    <option value="rechazada" <?= ($estado === 'rechazada') ? 'selected' : '' ?>>Rechazadas</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md font-medium transition-colors">
                    <i class="fas fa-filter mr-2"></i>Filtrar
                </button>
            </div>
        </form>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-home text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Propiedades</p>
                    <p class="text-2xl font-semibold text-gray-900"><?= count($properties) ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Activas</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        <?= count(array_filter($properties, function($p) { return $p['estado_publicacion'] === 'activa'; })) ?>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">En Revisión</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        <?= count(array_filter($properties, function($p) { return $p['estado_publicacion'] === 'en_revision'; })) ?>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-times-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Rechazadas</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        <?= count(array_filter($properties, function($p) { return $p['estado_publicacion'] === 'rechazada'; })) ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Listado de propiedades -->
    <?php if (empty($properties)): ?>
        <div class="text-center py-12">
            <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                <i class="fas fa-home text-4xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No tienes propiedades asignadas</h3>
            <p class="text-gray-600 mb-6">
                <?php if ($estado): ?>
                    No hay propiedades con el estado seleccionado.
                <?php else: ?>
                    Comienza creando tu primera propiedad o espera a que se te asignen algunas.
                <?php endif; ?>
            </p>
            <a href="/properties/create" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>Crear Primera Propiedad
            </a>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Propiedad
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tipo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Precio
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ubicación
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Favoritos
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($properties as $property): ?>
                            <tr class="hover:bg-gray-50">
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
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-primary-100 text-primary-800">
                                        <?= ucfirst(str_replace('_', ' ', $property['tipo'])) ?>
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
                                            $estadoClass = 'bg-yellow-100 text-yellow-800';
                                            $estadoText = 'En Revisión';
                                            break;
                                        case 'rechazada':
                                            $estadoClass = 'bg-red-100 text-red-800';
                                            $estadoText = 'Rechazada';
                                            break;
                                    }
                                    ?>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?= $estadoClass ?>">
                                        <?= $estadoText ?>
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= date('d/m/Y', strtotime($property['fecha_creacion'])) ?>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex items-center">
                                        <i class="fas fa-heart text-red-500 mr-1"></i>
                                        <span class="favorite-count" data-property-id="<?= $property['id'] ?>">0</span>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="/properties/show/<?= $property['id'] ?>" 
                                           class="text-primary-600 hover:text-primary-900" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <?php if ($property['estado_publicacion'] !== 'vendida'): ?>
                                            <a href="/properties/edit/<?= $property['id'] ?>" 
                                               class="text-blue-600 hover:text-blue-900" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if ($property['estado_publicacion'] === 'en_revision'): ?>
                                            <a href="/properties/pending-validation" 
                                               class="text-yellow-600 hover:text-yellow-900" title="Validar">
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

<!-- Script para cargar contadores de favoritos -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cargar contadores de favoritos para todas las propiedades
    const favoriteCounts = document.querySelectorAll('.favorite-count');
    
    favoriteCounts.forEach(function(element) {
        const propertyId = element.getAttribute('data-property-id');
        
        fetch(`/favorites/count/${propertyId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    element.textContent = data.count;
                }
            })
            .catch(error => console.error('Error al cargar contador de favoritos:', error));
    });
});
</script>

<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?> 