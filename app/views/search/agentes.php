<?php
/**
 * Vista: Búsqueda de Agentes
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Esta vista permite a los clientes buscar agentes por nombre y ciudad
 */

$content = ob_start();
?>

<!-- Hero Section -->
<div class="bg-gradient-to-r from-azul-marino to-azul-marino-hover text-white" style="background: linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center">
            <h1 class="text-4xl font-bold mb-4" style="color: var(--text-light);">Encuentra tu Agente Ideal</h1>
            <p class="text-xl mb-8" style="color: rgba(255, 255, 255, 0.9);">
                Conecta con agentes inmobiliarios profesionales en tu zona
            </p>
            
            <!-- Estadísticas rápidas -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto">
                <div class="text-center">
                    <div class="text-3xl font-bold" style="color: var(--text-light);"><?= $total ?></div>
                    <div style="color: rgba(255, 255, 255, 0.8);">Agentes</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold" style="color: var(--text-light);"><?= count(array_unique(array_column($agentes, 'ciudad'))) ?></div>
                    <div style="color: rgba(255, 255, 255, 0.8);">Ciudades</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold" style="color: var(--text-light);"><?= array_sum(array_column($agentes, 'total_propiedades')) ?></div>
                    <div style="color: rgba(255, 255, 255, 0.8);">Propiedades</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold" style="color: var(--text-light);"><?= array_sum(array_column($agentes, 'total_vendidas')) ?></div>
                    <div style="color: rgba(255, 255, 255, 0.8);">Vendidas</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros de Búsqueda -->
<div class="bg-white border-b border-gray-200" style="background-color: var(--bg-light); border-bottom-color: var(--color-gris-claro);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <form method="GET" action="/buscar-agentes" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Nombre del Agente -->
                <div>
                    <label for="nombre" class="block text-sm font-medium mb-1" style="color: var(--text-primary);">Nombre del Agente</label>
                    <input type="text" name="nombre" id="nombre" 
                           value="<?= htmlspecialchars($nombre) ?>" 
                           placeholder="Buscar por nombre o apellido..."
                           class="w-full rounded-md shadow-sm transition-all duration-200"
                           style="border: 1px solid var(--color-gris-claro); color: var(--text-primary);"
                           onfocus="this.style.borderColor='var(--color-azul-marino)'; this.style.boxShadow='0 0 0 3px rgba(29, 53, 87, 0.1)';"
                           onblur="this.style.borderColor='var(--color-gris-claro)'; this.style.boxShadow='none';">
                </div>
                
                <!-- Ciudad -->
                <div>
                    <label for="ciudad" class="block text-sm font-medium mb-1" style="color: var(--text-primary);">Ciudad</label>
                    <select name="ciudad" id="ciudad" class="w-full rounded-md shadow-sm transition-all duration-200"
                            style="border: 1px solid var(--color-gris-claro); color: var(--text-primary);"
                            onfocus="this.style.borderColor='var(--color-azul-marino)'; this.style.boxShadow='0 0 0 3px rgba(29, 53, 87, 0.1)';"
                            onblur="this.style.borderColor='var(--color-gris-claro)'; this.style.boxShadow='none';">
                        <option value="">Todas las ciudades</option>
                        <!-- Se llenará con JavaScript -->
                    </select>
                </div>
                
                <!-- Botones -->
                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 px-4 py-2 rounded-md font-medium transition-all duration-200 hover:transform hover:scale-105"
                            style="background: linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%); color: var(--text-light);"
                            onmouseover="this.style.background='linear-gradient(135deg, var(--color-azul-marino-hover) 0%, var(--color-azul-marino) 100%)';"
                            onmouseout="this.style.background='linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%)';">
                        <i class="fas fa-search mr-2"></i>Buscar
                    </button>
                    <a href="/buscar-agentes" class="px-4 py-2 rounded-md font-medium transition-all duration-200 hover:transform hover:scale-105"
                       style="background-color: var(--color-gris-claro); color: var(--text-primary);"
                       onmouseover="this.style.backgroundColor='var(--color-azul-marino-light)';"
                       onmouseout="this.style.backgroundColor='var(--color-gris-claro)';">
                        <i class="fas fa-times mr-2"></i>Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Listado de Agentes -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Resultados -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold" style="color: var(--color-azul-marino);">
            Agentes Inmobiliarios
            <?php if (!empty($nombre) || !empty($ciudad)): ?>
                <span class="text-lg font-normal" style="color: var(--text-secondary);">(<?= $total ?> resultados)</span>
            <?php endif; ?>
        </h2>
        
        <!-- Filtros activos -->
        <?php if (!empty($nombre) || !empty($ciudad)): ?>
            <div class="flex space-x-2">
                <?php if (!empty($nombre)): ?>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                          style="background-color: rgba(29, 53, 87, 0.1); color: var(--color-azul-marino);">
                        <i class="fas fa-user mr-1"></i><?= htmlspecialchars($nombre) ?>
                    </span>
                <?php endif; ?>
                <?php if (!empty($ciudad)): ?>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                          style="background-color: rgba(42, 157, 143, 0.1); color: var(--color-verde-esmeralda);">
                        <i class="fas fa-map-marker-alt mr-1"></i><?= htmlspecialchars($ciudad) ?>
                    </span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if (empty($agentes)): ?>
        <!-- Estado vacío -->
        <div class="text-center py-12">
            <div class="w-24 h-24 mx-auto mb-4 rounded-full flex items-center justify-center"
                 style="background-color: rgba(29, 53, 87, 0.1);">
                <i class="fas fa-user-tie text-4xl" style="color: var(--color-azul-marino);"></i>
            </div>
            <h3 class="text-lg font-medium mb-2" style="color: var(--color-azul-marino);">No se encontraron agentes</h3>
            <p class="mb-6" style="color: var(--text-secondary);">
                <?php if (!empty($nombre) || !empty($ciudad)): ?>
                    Intenta ajustar los filtros de búsqueda para encontrar más resultados.
                <?php else: ?>
                    No hay agentes registrados en el sistema en este momento.
                <?php endif; ?>
            </p>
            <?php if (!empty($nombre) || !empty($ciudad)): ?>
                <a href="/buscar-agentes" class="px-4 py-2 rounded-md font-medium transition-all duration-200 hover:transform hover:scale-105"
                   style="background: linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%); color: var(--text-light);"
                   onmouseover="this.style.background='linear-gradient(135deg, var(--color-azul-marino-hover) 0%, var(--color-azul-marino) 100%)';"
                   onmouseout="this.style.background='linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%)';">
                    Ver todos los agentes
                </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <!-- Grid de agentes -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($agentes as $agente): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-all duration-200 hover:transform hover:scale-105"
                     style="background-color: var(--bg-light); border: 1px solid var(--color-gris-claro);">
                    <!-- Header del agente -->
                    <div class="p-6 border-b border-gray-200" style="border-bottom-color: var(--color-gris-claro);">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-16 h-16 rounded-full flex items-center justify-center"
                                     style="background: linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%);">
                                    <span class="text-white text-xl font-bold">
                                        <?= strtoupper(substr($agente['nombre'], 0, 1) . substr($agente['apellido'], 0, 1)) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-semibold" style="color: var(--color-azul-marino);">
                                    <?= htmlspecialchars($agente['nombre'] . ' ' . $agente['apellido']) ?>
                                </h3>
                                <p class="text-sm" style="color: var(--text-secondary);">
                                    <i class="fas fa-map-marker-alt mr-1" style="color: var(--color-dorado-suave);"></i>
                                    <?= htmlspecialchars($agente['ciudad'] ?? 'Ubicación no especificada') ?>
                                    <?php if (!empty($agente['sector'])): ?>
                                        - <?= htmlspecialchars($agente['sector']) ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Información de contacto -->
                    <div class="p-6 space-y-3">
                        <div class="flex items-center text-sm" style="color: var(--text-secondary);">
                            <i class="fas fa-envelope w-4 mr-3" style="color: var(--color-azul-marino);"></i>
                            <span><?= htmlspecialchars($agente['email']) ?></span>
                        </div>
                        <?php if (!empty($agente['telefono'])): ?>
                            <div class="flex items-center text-sm" style="color: var(--text-secondary);">
                                <i class="fas fa-phone w-4 mr-3" style="color: var(--color-verde-esmeralda);"></i>
                                <span><?= htmlspecialchars($agente['telefono']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Estadísticas -->
                    <div class="px-6 py-4 border-t border-gray-200" style="background-color: rgba(29, 53, 87, 0.05); border-top-color: var(--color-gris-claro);">
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div>
                                <div class="text-2xl font-bold" style="color: var(--color-azul-marino);"><?= $agente['total_propiedades'] ?></div>
                                <div class="text-xs" style="color: var(--text-secondary);">ACTIVAS</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold" style="color: var(--color-verde-esmeralda);"><?= $agente['total_vendidas'] ?></div>
                                <div class="text-xs" style="color: var(--text-secondary);">VENDIDAS</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Acciones -->
                    <div class="p-6 space-y-3">
                        <a href="/properties?agente=<?= $agente['id'] ?>" 
                           class="w-full px-4 py-2 rounded-md font-medium transition-all duration-200 hover:transform hover:scale-105 text-center block"
                           style="background: linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%); color: var(--text-light);"
                           onmouseover="this.style.background='linear-gradient(135deg, var(--color-azul-marino-hover) 0%, var(--color-azul-marino) 100%)';"
                           onmouseout="this.style.background='linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%)';">
                            <i class="fas fa-home mr-2"></i>Ver Propiedades
                        </a>
                        <button type="button" 
                                onclick="iniciarChat(<?= $agente['id'] ?>, '<?= htmlspecialchars($agente['nombre'] . ' ' . $agente['apellido']) ?>')"
                                class="w-full px-4 py-2 rounded-md font-medium transition-all duration-200 hover:transform hover:scale-105"
                                style="background: linear-gradient(135deg, var(--color-verde-esmeralda) 0%, var(--color-verde-esmeralda-hover) 100%); color: var(--text-light);"
                                onmouseover="this.style.background='linear-gradient(135deg, var(--color-verde-esmeralda-hover) 0%, var(--color-verde-esmeralda) 100%)';"
                                onmouseout="this.style.background='linear-gradient(135deg, var(--color-verde-esmeralda) 0%, var(--color-verde-esmeralda-hover) 100%)';">
                            <i class="fas fa-comments mr-2"></i>Contactar
                        </button>
                    </div>
                    
                    <!-- Footer -->
                    <div class="px-6 py-3 border-t border-gray-200" style="background-color: rgba(221, 226, 230, 0.3); border-top-color: var(--color-gris-claro);">
                        <div class="text-xs" style="color: var(--text-secondary);">
                            <i class="fas fa-calendar-alt mr-1" style="color: var(--color-dorado-suave);"></i>
                            Registrado: <?= date('d/m/Y', strtotime($agente['fecha_registro'])) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
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
                                   class="px-3 py-2 text-sm font-medium border rounded-md transition-all duration-200 hover:transform hover:scale-105"
                                   style="<?= $i === $page ? 'color: var(--color-azul-marino); background-color: rgba(29, 53, 87, 0.1); border-color: var(--color-azul-marino);' : 'color: var(--text-secondary); background-color: var(--bg-light); border-color: var(--color-gris-claro);' ?>"
                                   onmouseover="<?= $i !== $page ? 'this.style.backgroundColor=\'rgba(29, 53, 87, 0.05)\';' : '' ?>"
                                   onmouseout="<?= $i !== $page ? 'this.style.backgroundColor=\'var(--bg-light)\';' : '' ?>">
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
                <p>¿Deseas iniciar una conversación con <strong id="agenteNombre"></strong>?</p>
                <p class="text-sm text-gray-600 mt-2">Se creará una nueva solicitud de compra y podrás chatear directamente con el agente.</p>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="cerrarModal('chatModal')" class="px-4 py-2 rounded-md transition-all duration-200 hover:transform hover:scale-105"
                        style="background-color: var(--color-gris-claro); color: var(--text-primary);"
                        onmouseover="this.style.backgroundColor='var(--color-azul-marino-light)';"
                        onmouseout="this.style.backgroundColor='var(--color-gris-claro)';">
                    Cancelar
                </button>
                <button type="button" id="confirmarChat" class="px-4 py-2 rounded-md transition-all duration-200 hover:transform hover:scale-105"
                        style="background: linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%); color: var(--text-light);"
                        onmouseover="this.style.background='linear-gradient(135deg, var(--color-azul-marino-hover) 0%, var(--color-azul-marino) 100%)';"
                        onmouseout="this.style.background='linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%)';">
                    <i class="fas fa-comments mr-2"></i>Iniciar Chat
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cargar ciudades en el select
    cargarCiudades();
    
    // Establecer valor seleccionado en el select de ciudades
    const ciudadSelect = document.getElementById('ciudad');
    const ciudadActual = '<?= htmlspecialchars($ciudad) ?>';
    if (ciudadActual) {
        setTimeout(() => {
            ciudadSelect.value = ciudadActual;
        }, 100);
    }
});

function cargarCiudades() {
    fetch('/api/ciudades')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const ciudadSelect = document.getElementById('ciudad');
                data.ciudades.forEach(ciudad => {
                    const option = document.createElement('option');
                    option.value = ciudad;
                    option.textContent = ciudad;
                    ciudadSelect.appendChild(option);
                });
            }
        })
        .catch(error => console.error('Error al cargar ciudades:', error));
}

let agenteSeleccionado = null;

function iniciarChat(agenteId, agenteNombre) {
    agenteSeleccionado = agenteId;
    document.getElementById('agenteNombre').textContent = agenteNombre;
    
    document.getElementById('chatModal').classList.remove('hidden');
}

function cerrarModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// Cerrar modal al hacer clic fuera de él
document.addEventListener('click', function(event) {
    const modal = document.getElementById('chatModal');
    if (modal && !modal.classList.contains('hidden')) {
        if (event.target === modal) {
            cerrarModal('chatModal');
        }
    }
});

document.getElementById('confirmarChat').addEventListener('click', function() {
    if (agenteSeleccionado) {
        // Redirigir al chat o crear nueva solicitud
        window.location.href = `/chat?nuevo_agente=${agenteSeleccionado}`;
    }
});

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