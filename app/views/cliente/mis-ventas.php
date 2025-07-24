<?php
/**
 * Vista: Mis Ventas (Propiedades Enviadas)
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Esta vista muestra las propiedades que el cliente ha enviado para venta
 * con sus respectivos tokens de validación
 */

$content = ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold" style="color: var(--color-azul-marino);">
                    <i class="fas fa-home mr-3"></i>Mis Propiedades Enviadas
                </h1>
                <p class="mt-2 text-lg" style="color: var(--text-secondary);">
                    Gestiona las propiedades que has enviado para venta
                </p>
            </div>
            <a href="/properties/create" 
               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white transition-colors hover:opacity-90"
               style="background: linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%); color: white !important;">
                <i class="fas fa-plus mr-2"></i>Nueva Propiedad
            </a>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4" style="border-left-color: var(--color-azul-marino);">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-home text-2xl" style="color: var(--color-azul-marino);"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium" style="color: var(--text-secondary);">Total Enviadas</p>
                    <p class="text-2xl font-bold" style="color: var(--color-azul-marino);"><?= $stats['total'] ?? 0 ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4" style="border-left-color: var(--color-verde-esmeralda);">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-2xl" style="color: var(--color-verde-esmeralda);"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium" style="color: var(--text-secondary);">Activas</p>
                    <p class="text-2xl font-bold" style="color: var(--color-verde-esmeralda);"><?= $stats['activas'] ?? 0 ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4" style="border-left-color: var(--color-dorado-suave);">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-clock text-2xl" style="color: var(--color-dorado-suave);"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium" style="color: var(--text-secondary);">En Revisión</p>
                    <p class="text-2xl font-bold" style="color: var(--color-dorado-suave);"><?= $stats['en_revision'] ?? 0 ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4" style="border-left-color: var(--color-rojo-error);">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-times-circle text-2xl" style="color: var(--color-rojo-error);"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium" style="color: var(--text-secondary);">Rechazadas</p>
                    <p class="text-2xl font-bold" style="color: var(--color-rojo-error);"><?= $stats['rechazadas'] ?? 0 ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de propiedades -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold" style="color: var(--color-azul-marino);">
                <i class="fas fa-list mr-2"></i>Propiedades Enviadas
            </h2>
        </div>
        
        <div class="p-6">
            <!-- Debug info (temporal) -->
            <?php if (isset($_GET['debug'])): ?>
                <div class="mb-4 p-4 bg-yellow-100 border border-yellow-400 rounded">
                    <h4 class="font-bold">Debug Info:</h4>
                    <p>Total propiedades: <?= count($propiedades) ?></p>
                    <p>Estadísticas: <?= json_encode($stats) ?></p>
                    <?php if (!empty($propiedades)): ?>
                        <p>Primera propiedad: <?= json_encode($propiedades[0]) ?></p>
                        <p>IDs de propiedades: <?= implode(', ', array_column($propiedades, 'id')) ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <?php if (empty($propiedades)): ?>
                <div class="text-center py-12">
                    <i class="fas fa-home text-6xl mb-4" style="color: var(--color-gris-claro);"></i>
                    <h3 class="text-xl font-medium mb-2" style="color: var(--text-secondary);">No tienes propiedades enviadas</h3>
                    <p class="text-gray-500 mb-6">Comienza enviando tu primera propiedad para venta</p>
                    <a href="/properties/create" 
                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white transition-colors hover:opacity-90"
                       style="background: linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%); color: white !important;">
                        <i class="fas fa-plus mr-2"></i>Enviar Primera Propiedad
                    </a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <?php foreach ($propiedades as $propiedad): ?>
                        <div class="border border-gray-200 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow bg-white">
                            <!-- Imagen de la propiedad -->
                            <div class="h-40 bg-gray-200 relative">
                                <?php if (!empty($propiedad['foto_propiedad'] ?? '')): ?>
                                                                         <img src="<?= htmlspecialchars($propiedad['foto_propiedad']) ?>" 
                                          alt="<?= htmlspecialchars($propiedad['titulo_propiedad'] ?? 'Propiedad') ?>"
                                          class="w-full h-full object-cover">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fas fa-home text-4xl" style="color: var(--color-gris-claro);"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Estado de la propiedad -->
                                <div class="absolute top-3 right-3">
                                    <?php
                                    $estadoClass = '';
                                    $estadoIcon = '';
                                    $estadoText = '';
                                    
                                    switch ($propiedad['estado'] ?? 'en_revision') {
                                        case 'activa':
                                            $estadoClass = 'bg-green-100 text-green-800 border-green-300';
                                            $estadoIcon = 'fas fa-check-circle';
                                            $estadoText = 'Activa';
                                            break;
                                        case 'en_revision':
                                            $estadoClass = 'bg-yellow-100 text-yellow-800 border-yellow-300';
                                            $estadoIcon = 'fas fa-clock';
                                            $estadoText = 'En Revisión';
                                            break;
                                        case 'rechazada':
                                            $estadoClass = 'bg-red-100 text-red-800 border-red-300';
                                            $estadoIcon = 'fas fa-times-circle';
                                            $estadoText = 'Rechazada';
                                            break;
                                        case 'vendida':
                                            $estadoClass = 'bg-blue-100 text-blue-800 border-blue-300';
                                            $estadoIcon = 'fas fa-tag';
                                            $estadoText = 'Vendida';
                                            break;
                                    }
                                    ?>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold border <?= $estadoClass ?>">
                                        <i class="<?= $estadoIcon ?> mr-1"></i><?= $estadoText ?>
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Información de la propiedad -->
                            <div class="p-3">
                                <h3 class="text-base font-semibold mb-2" style="color: var(--color-azul-marino);">
                                    <?= htmlspecialchars($propiedad['titulo_propiedad'] ?? 'Sin título') ?>
                                </h3>
                                
                                <div class="space-y-1 mb-3 text-xs">
                                    <div class="flex justify-between">
                                        <span class="font-bold text-lg" style="color: var(--color-azul-marino);">
                                            $<?= number_format($propiedad['precio_propiedad'] ?? 0, 0) ?>
                                        </span>
                                    </div>
                                    <div class="text-gray-600">
                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                        <?= htmlspecialchars(($propiedad['ciudad_propiedad'] ?? 'Sin especificar') . ', ' . ($propiedad['sector_propiedad'] ?? 'Sin especificar')) ?>
                                    </div>
                                    <div class="text-gray-600">
                                        <i class="fas fa-home mr-1"></i>
                                        <?= ucfirst($propiedad['tipo_propiedad'] ?? 'Sin especificar') ?> • <?= $propiedad['area_propiedad'] ?? 0 ?> m²
                                    </div>
                                </div>
                                
                                <!-- Token de validación (solo si está en revisión) -->
                                <?php if (($propiedad['estado'] ?? 'en_revision') === 'en_revision' && !empty($propiedad['token_validacion'] ?? '')): ?>
                                    <div class="mt-2 p-2 rounded bg-blue-50 border border-blue-200">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-medium text-blue-800">
                                                <i class="fas fa-key mr-1"></i>Token
                                            </span>
                                            <code class="text-xs font-mono text-blue-600 bg-white px-1 rounded">
                                                <?= htmlspecialchars(substr($propiedad['token_validacion'] ?? '', 0, 8)) ?>...
                                            </code>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Agente asignado y fecha -->
                                <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                                    <div class="flex items-center">
                                        <i class="fas fa-user mr-1"></i>
                                        <span>
                                            <?php if (!empty($propiedad['nombre_agente'] ?? '')): ?>
                                                <?= htmlspecialchars(($propiedad['nombre_agente'] ?? '') . ' ' . ($propiedad['apellido_agente'] ?? '')) ?>
                                            <?php else: ?>
                                                Sin asignar
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar mr-1"></i>
                                        <span><?= date('d/m/Y', strtotime($propiedad['fecha_solicitud'] ?? date('Y-m-d H:i:s'))) ?></span>
                                    </div>
                                </div>
                                
                                <!-- Botón ver detalles -->
                                <div class="mt-3">
                                    <a href="/properties/show/<?= $propiedad['id'] ?? 0 ?>" 
                                       class="w-full inline-flex items-center justify-center px-3 py-2 border border-transparent text-xs font-medium rounded-md text-white transition-colors hover:opacity-90"
                                       style="background: linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%); color: white !important;"
                                       title="Ver detalles de <?= htmlspecialchars($propiedad['titulo_propiedad'] ?? 'propiedad') ?>"
                                       onclick="console.log('Navegando a propiedad ID: <?= $propiedad['id'] ?? 0 ?>')">
                                        <i class="fas fa-eye mr-1"></i>Ver Detalles
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* Forzar texto blanco en botones */
.btn-primary {
    color: white !important;
}

.btn-primary:hover {
    color: white !important;
}

.btn-primary:focus {
    color: white !important;
}

.btn-primary:active {
    color: white !important;
}

/* Estilos específicos para botones de esta página */
a[href*="/properties/create"],
a[href*="/properties/show/"] {
    color: white !important;
}

a[href*="/properties/create"]:hover,
a[href*="/properties/show/"]:hover {
    color: white !important;
}

a[href*="/properties/create"]:focus,
a[href*="/properties/show/"]:focus {
    color: white !important;
}

a[href*="/properties/create"]:active,
a[href*="/properties/show/"]:active {
    color: white !important;
}
</style>

<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?> 