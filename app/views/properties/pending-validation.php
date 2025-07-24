<?php
/**
 * Vista: Propiedades Pendientes de Validación - Diseño Moderno
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 */

require_once APP_PATH . '/helpers/PropertyHelper.php';

// Verificar que las variables necesarias estén definidas
if (!isset($properties)) $properties = [];
if (!isset($stats)) $stats = ['total' => 0, 'en_revision' => 0, 'activas' => 0, 'rechazadas' => 0];
if (!isset($search)) $search = '';

$content = ob_start();
?>

<!-- Contenido Principal -->
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header con diseño moderno -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-gradient-to-r from-amber-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-clock text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold text-gray-900">
                            Pendientes de Validación
                        </h1>
                        <p class="text-gray-700 mt-2 text-lg font-medium">Gestiona las propiedades enviadas por los clientes</p>
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
                            <p class="text-3xl font-bold text-gray-900"><?= $stats['total'] ?? 0 ?></p>
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
                            <p class="text-3xl font-bold text-amber-700"><?= $stats['en_revision'] ?? 0 ?></p>
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
                            <p class="text-3xl font-bold text-emerald-700"><?= $stats['activas'] ?? 0 ?></p>
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
                            <p class="text-3xl font-bold text-red-700"><?= $stats['rechazadas'] ?? 0 ?></p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center shadow-sm">
                            <i class="fas fa-times text-red-700 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Buscador con diseño moderno -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 mb-8 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-800 to-gray-900 px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-search mr-3 text-white"></i>
                    Buscar Propiedades
                </h3>
            </div>
            <div class="p-6">
                <form method="GET" action="/properties/pending-validation" class="space-y-4">
                    <div class="flex flex-col lg:flex-row gap-4">
                        <div class="flex-1">
                            <label for="search" class="block text-sm font-semibold text-gray-700 mb-2">
                                Buscar por nombre del cliente o token de validación
                            </label>
                            <div class="relative">
                                <input 
                                    type="text" 
                                    id="search" 
                                    name="search" 
                                    value="<?= htmlspecialchars($search) ?>"
                                    placeholder="Ejemplo: 'Jefferson' o '45a6cc7f06a52b77086adfdd9e99b859ae4472647f7e584e78254d28f3c0ebdf'"
                                    class="w-full pl-12 pr-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-lg shadow-sm"
                                >
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-500 text-lg"></i>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 lg:items-end">
                            <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-8 py-4 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center shadow-lg hover:shadow-xl">
                                <i class="fas fa-search mr-2"></i>Buscar
                            </button>
                            <a href="/properties/pending-validation" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-8 py-4 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center shadow-sm">
                                <i class="fas fa-times mr-2"></i>Limpiar
                            </a>
                            <a href="/dashboard" class="bg-gray-800 hover:bg-gray-900 text-white px-8 py-4 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center shadow-lg hover:shadow-xl">
                                <i class="fas fa-arrow-left mr-2"></i>Dashboard
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Resultados con diseño moderno -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-800 to-gray-900 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <h3 class="text-xl font-semibold text-white">
                            Propiedades Pendientes
                        </h3>
                        <span class="property-count bg-blue-600 text-white text-sm font-semibold px-3 py-1 rounded-full border border-blue-500">
                            <?= count($properties) ?> propiedades
                        </span>
                    </div>
                    <!-- Versión actualizada para forzar recarga del cache -->
                    <div class="text-xs text-gray-300">v2.6.1</div>
                </div>
            </div>

            <?php if (empty($properties)): ?>
                <!-- Estado vacío con diseño moderno -->
                <div class="p-12 text-center">
                    <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <i class="fas fa-info-circle text-3xl text-blue-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">
                        <?php if (!empty($search)): ?>
                            No se encontraron propiedades
                        <?php else: ?>
                            No hay propiedades pendientes
                        <?php endif; ?>
                    </h3>
                    <p class="text-gray-700 text-lg max-w-md mx-auto font-medium">
                        <?php if (!empty($search)): ?>
                            Intenta con otros términos de búsqueda o revisa la ortografía.
                        <?php else: ?>
                            No tienes propiedades pendientes de validación en este momento.
                        <?php endif; ?>
                    </p>
                </div>
            <?php else: ?>
                <!-- Tabla con diseño moderno -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-800">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Propiedad</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Cliente</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Token</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($properties as $property): ?>
                                <tr id="row-<?= $property['id'] ?>" class="hover:bg-gray-50 transition-all duration-200 border-b border-gray-100">
                                    <td class="px-6 py-6">
                                        <div class="flex items-center space-x-4">
                                            <div class="flex-shrink-0 h-16 w-16">
                                                <?php if (!empty($property['imagen_principal'])): ?>
                                                    <img class="h-16 w-16 rounded-xl object-cover shadow-sm" 
                                                         src="<?= htmlspecialchars($property['imagen_principal']) ?>" 
                                                         alt="<?= htmlspecialchars($property['titulo']) ?>">
                                                <?php else: ?>
                                                    <div class="h-16 w-16 rounded-xl bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center shadow-sm">
                                                        <i class="fas fa-home text-gray-400 text-xl"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-lg font-semibold text-gray-900 truncate">
                                                    <?= htmlspecialchars($property['titulo']) ?>
                                                </h4>
                                                <p class="text-sm text-gray-600">
                                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                                    <?= htmlspecialchars($property['ciudad']) ?>, <?= htmlspecialchars($property['sector']) ?>
                                                </p>
                                                <p class="text-sm font-medium text-primary-600">
                                                    $<?= number_format($property['precio'], 2) ?> - <?= getPropertyTypeDisplayName($property['tipo']) ?>
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6">
                                        <div class="space-y-1">
                                            <p class="text-sm font-semibold text-gray-900">
                                                <?= htmlspecialchars(($property['cliente_nombre'] ?? '') . ' ' . ($property['cliente_apellido'] ?? '')) ?>
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                <i class="fas fa-envelope mr-1"></i>
                                                <?= htmlspecialchars($property['cliente_email'] ?? '') ?>
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                <i class="fas fa-phone mr-1"></i>
                                                <?= htmlspecialchars($property['cliente_telefono'] ?? '') ?>
                                            </p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 font-mono">
                                            <?= htmlspecialchars(substr($property['token_validacion'], 0, 16)) ?>...
                                        </span>
                                    </td>
                                    <td class="px-6 py-6 text-sm text-gray-600">
                                        <div class="flex items-center">
                                            <i class="fas fa-calendar mr-2 text-gray-400"></i>
                                            <?= date('d/m/Y H:i', strtotime($property['fecha_creacion'])) ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6 text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-300">
                                            <i class="fas fa-clock mr-1"></i>En Revisión
                                        </span>
                                    </td>
                                    <td class="px-6 py-6 text-right">
                                        <div class="flex items-center justify-end space-x-2">
                                            <button onclick="validateProperty(<?= $property['id'] ?>, this)"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-semibold rounded-lg text-emerald-700 bg-emerald-100 hover:bg-emerald-200 transition-colors duration-200 shadow-sm">
                                                <i class="fas fa-check mr-1"></i>Validar
                                            </button>
                                            <button onclick="rejectProperty(<?= $property['id'] ?>, this)"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-semibold rounded-lg text-red-700 bg-red-100 hover:bg-red-200 transition-colors duration-200 shadow-sm">
                                                <i class="fas fa-times mr-1"></i>Rechazar
                                            </button>
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
</div>

    <!-- Estilos adicionales -->
    <style>
        .loading-spinner {
            display: inline-flex;
            align-items: center;
        }
        
        .loading-spinner.hidden {
            display: none;
        }
        
        .button-text.hidden {
            display: none;
        }
        
        /* Animación de fade para los modales */
        .modal-fade-in {
            animation: modalFadeIn 0.3s ease-out;
        }
        
        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(-10px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
    </style>
    
    <!-- Scripts JavaScript -->
    <script>
function validateProperty(propertyId, btn) {
    btn.disabled = true;
    btn.textContent = 'Procesando...';
    
    fetch(`/properties/${propertyId}/validate`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Mostrar mensaje de éxito
            alert(data.message);
            // Eliminar la fila directamente
            const row = btn.closest('tr');
            if (row) {
                row.remove();
                updatePropertyCount();
            }
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión');
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = 'Validar';
    });
}

function rejectProperty(propertyId, btn) {
    btn.disabled = true;
    btn.textContent = 'Procesando...';
    
    fetch(`/properties/${propertyId}/reject`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Mostrar mensaje de éxito
            alert(data.message);
            // Eliminar la fila directamente
            const row = btn.closest('tr');
            if (row) {
                row.remove();
                updatePropertyCount();
            }
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión');
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = 'Rechazar';
    });
}

function updatePropertyCount() {
    // Contar las filas que quedan en la tabla (todas las que están en el DOM)
    const totalRows = document.querySelectorAll('tbody tr').length;
    
    // Actualizar el contador en el encabezado
    const countElement = document.querySelector('.property-count');
    if (countElement) {
        if (totalRows === 0) {
            countElement.textContent = '0 propiedades';
        } else if (totalRows === 1) {
            countElement.textContent = '1 propiedad';
        } else {
            countElement.textContent = totalRows + ' propiedades';
        }
    }
}




</script>

<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?> 