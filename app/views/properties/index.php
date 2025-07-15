<?php
/**
 * Vista: Listado de Propiedades
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Esta vista muestra el listado público de propiedades con filtros de búsqueda
 */

// Incluir el layout principal
$content = ob_start();
?>

<!-- Hero Section -->
<div class="bg-gradient-to-r from-primary-600 to-primary-800 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center">
            <h1 class="text-4xl font-bold mb-4">Encuentra tu Propiedad Ideal</h1>
            <p class="text-xl text-primary-100 mb-8">
                Explora nuestra amplia selección de propiedades en venta
            </p>
            
            <!-- Estadísticas rápidas -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto">
                <div class="text-center">
                    <div class="text-3xl font-bold"><?= number_format($stats['total']) ?></div>
                    <div class="text-primary-200">Propiedades</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold"><?= number_format($stats['activas']) ?></div>
                    <div class="text-primary-200">Disponibles</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold"><?= number_format($stats['vendidas']) ?></div>
                    <div class="text-primary-200">Vendidas</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold"><?= number_format($stats['en_revision']) ?></div>
                    <div class="text-primary-200">En Revisión</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros de Búsqueda -->
<div class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <form method="GET" action="/properties" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Tipo de Propiedad -->
                <div>
                    <label for="tipo" class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                    <select name="tipo" id="tipo" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Todos los tipos</option>
                        <option value="casa" <?= ($filters['tipo'] === 'casa') ? 'selected' : '' ?>>Casa</option>
                        <option value="apartamento" <?= ($filters['tipo'] === 'apartamento') ? 'selected' : '' ?>>Apartamento</option>
                        <option value="terreno" <?= ($filters['tipo'] === 'terreno') ? 'selected' : '' ?>>Terreno</option>
                        <option value="local_comercial" <?= ($filters['tipo'] === 'local_comercial') ? 'selected' : '' ?>>Local Comercial</option>
                        <option value="oficina" <?= ($filters['tipo'] === 'oficina') ? 'selected' : '' ?>>Oficina</option>
                    </select>
                </div>
                
                <!-- Ciudad -->
                <div>
                    <label for="ciudad" class="block text-sm font-medium text-gray-700 mb-1">Ciudad</label>
                    <input type="text" name="ciudad" id="ciudad" value="<?= htmlspecialchars($filters['ciudad']) ?>" 
                           placeholder="Ej: Santo Domingo" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                </div>
                
                <!-- Sector -->
                <div>
                    <label for="sector" class="block text-sm font-medium text-gray-700 mb-1">Sector</label>
                    <input type="text" name="sector" id="sector" value="<?= htmlspecialchars($filters['sector']) ?>" 
                           placeholder="Ej: Bella Vista" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                </div>
                
                <!-- Precio Mínimo -->
                <div>
                    <label for="precio_min" class="block text-sm font-medium text-gray-700 mb-1">Precio Mínimo</label>
                    <input type="number" name="precio_min" id="precio_min" value="<?= htmlspecialchars($filters['precio_min']) ?>" 
                           placeholder="USD" min="0" step="1000"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                </div>
                
                <!-- Precio Máximo -->
                <div>
                    <label for="precio_max" class="block text-sm font-medium text-gray-700 mb-1">Precio Máximo</label>
                    <input type="number" name="precio_max" id="precio_max" value="<?= htmlspecialchars($filters['precio_max']) ?>" 
                           placeholder="USD" min="0" step="1000"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                </div>
                
                <!-- Habitaciones -->
                <div>
                    <label for="habitaciones" class="block text-sm font-medium text-gray-700 mb-1">Mín. Habitaciones</label>
                    <select name="habitaciones" id="habitaciones" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Cualquier cantidad</option>
                        <option value="1" <?= ($filters['habitaciones'] === '1') ? 'selected' : '' ?>>1+</option>
                        <option value="2" <?= ($filters['habitaciones'] === '2') ? 'selected' : '' ?>>2+</option>
                        <option value="3" <?= ($filters['habitaciones'] === '3') ? 'selected' : '' ?>>3+</option>
                        <option value="4" <?= ($filters['habitaciones'] === '4') ? 'selected' : '' ?>>4+</option>
                        <option value="5" <?= ($filters['habitaciones'] === '5') ? 'selected' : '' ?>>5+</option>
                    </select>
                </div>
                
                <!-- Baños -->
                <div>
                    <label for="banos" class="block text-sm font-medium text-gray-700 mb-1">Mín. Baños</label>
                    <select name="banos" id="banos" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Cualquier cantidad</option>
                        <option value="1" <?= ($filters['banos'] === '1') ? 'selected' : '' ?>>1+</option>
                        <option value="2" <?= ($filters['banos'] === '2') ? 'selected' : '' ?>>2+</option>
                        <option value="3" <?= ($filters['banos'] === '3') ? 'selected' : '' ?>>3+</option>
                        <option value="4" <?= ($filters['banos'] === '4') ? 'selected' : '' ?>>4+</option>
                    </select>
                </div>
                
                <!-- Botones -->
                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md font-medium transition-colors">
                        <i class="fas fa-search mr-2"></i>Buscar
                    </button>
                    <a href="/properties" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md font-medium transition-colors">
                        <i class="fas fa-times mr-2"></i>Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Listado de Propiedades -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Resultados -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">
            Propiedades Disponibles
            <?php if (!empty(array_filter($filters))): ?>
                <span class="text-lg font-normal text-gray-600">(<?= count($properties) ?> resultados)</span>
            <?php endif; ?>
        </h2>
        
        <?php if (isAuthenticated()): ?>
            <a href="/properties/create" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>Publicar Propiedad
            </a>
        <?php endif; ?>
    </div>
    
    <?php if (empty($properties)): ?>
        <!-- Estado vacío -->
        <div class="text-center py-12">
            <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                <i class="fas fa-home text-4xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No se encontraron propiedades</h3>
            <p class="text-gray-600 mb-6">
                <?php if (!empty(array_filter($filters))): ?>
                    Intenta ajustar los filtros de búsqueda para encontrar más resultados.
                <?php else: ?>
                    No hay propiedades disponibles en este momento.
                <?php endif; ?>
            </p>
            <?php if (!empty(array_filter($filters))): ?>
                <a href="/properties" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md font-medium transition-colors">
                    Ver todas las propiedades
                </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <!-- Grid de propiedades -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($properties as $property): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <!-- Imagen de la propiedad -->
                    <div class="relative h-48 bg-gray-200">
                        <?php if (!empty($property['imagen_principal'])): ?>
                            <img src="<?= htmlspecialchars($property['imagen_principal']) ?>" 
                                 alt="<?= htmlspecialchars($property['titulo']) ?>"
                                 class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-home text-4xl text-gray-400"></i>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Badge de tipo -->
                        <div class="absolute top-2 left-2">
                            <span class="bg-primary-600 text-white px-2 py-1 rounded text-xs font-medium">
                                <?= ucfirst(str_replace('_', ' ', $property['tipo'])) ?>
                            </span>
                        </div>
                        
                        <!-- Precio -->
                        <div class="absolute bottom-2 right-2">
                            <span class="bg-white text-primary-600 px-3 py-1 rounded-lg text-sm font-bold shadow-md">
                                $<?= number_format($property['precio']) ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Información de la propiedad -->
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                            <a href="/properties/show/<?= $property['id'] ?>" class="hover:text-primary-600 transition-colors">
                                <?= htmlspecialchars($property['titulo']) ?>
                            </a>
                        </h3>
                        
                        <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                            <?= htmlspecialchars(substr($property['descripcion'], 0, 100)) ?>...
                        </p>
                        
                        <!-- Ubicación -->
                        <div class="flex items-center text-gray-500 text-sm mb-3">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            <span><?= htmlspecialchars($property['ciudad']) ?>, <?= htmlspecialchars($property['sector']) ?></span>
                        </div>
                        
                        <!-- Características -->
                        <div class="flex items-center justify-between text-sm text-gray-600 mb-4">
                            <div class="flex space-x-4">
                                <?php if ($property['habitaciones'] > 0): ?>
                                    <span class="flex items-center">
                                        <i class="fas fa-bed mr-1"></i>
                                        <?= $property['habitaciones'] ?> hab.
                                    </span>
                                <?php endif; ?>
                                
                                <?php if ($property['banos'] > 0): ?>
                                    <span class="flex items-center">
                                        <i class="fas fa-bath mr-1"></i>
                                        <?= $property['banos'] ?> baños
                                    </span>
                                <?php endif; ?>
                                
                                <span class="flex items-center">
                                    <i class="fas fa-ruler-combined mr-1"></i>
                                    <?= number_format($property['metros_cuadrados']) ?> m²
                                </span>
                            </div>
                        </div>
                        
                        <!-- Agente (si está asignado) -->
                        <?php if (!empty($property['agente_nombre'])): ?>
                            <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center mr-2">
                                        <span class="text-primary-600 font-bold text-sm">
                                            <?= strtoupper(substr($property['agente_nombre'], 0, 1)) ?>
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($property['agente_nombre'] . ' ' . $property['agente_apellido']) ?>
                                        </p>
                                        <p class="text-xs text-gray-500">Agente</p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Botón de acción -->
                        <div class="mt-4">
                            <a href="/properties/show/<?= $property['id'] ?>" 
                               class="w-full bg-primary-600 hover:bg-primary-700 text-white text-center py-2 px-4 rounded-md font-medium transition-colors">
                                Ver Detalles
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Paginación -->
        <?php if ($totalPages > 1): ?>
            <div class="mt-8 flex justify-center">
                <nav class="flex items-center space-x-2">
                    <?php if ($page > 1): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" 
                           class="px-3 py-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-md transition-colors">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
                           class="px-3 py-2 rounded-md transition-colors <?= $i === $page ? 'bg-primary-600 text-white' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" 
                           class="px-3 py-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-md transition-colors">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </nav>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Estilos adicionales -->
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?> 