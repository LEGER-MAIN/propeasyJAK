<?php
/**
 * Vista: Dashboard del Agente
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Esta vista muestra el dashboard principal del agente con estadísticas y actividades
 */
?>

<div class="py-8" style="background-color: var(--bg-primary);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold" style="color: var(--text-primary);">Dashboard del Agente</h1>
                    <p class="mt-2" style="color: var(--text-secondary);">Bienvenido, <?= htmlspecialchars($_SESSION['user_nombre'] . ' ' . $_SESSION['user_apellido']) ?></p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="/agente/perfil-publico" class="inline-flex items-center px-4 py-2 border rounded-md shadow-sm text-sm font-medium transition-all duration-200"
                       style="border-color: var(--color-gris-claro); color: var(--text-primary); background-color: var(--bg-light);"
                       onmouseover="this.style.backgroundColor='var(--color-gris-claro)'; this.style.transform='translateY(-2px)'"
                       onmouseout="this.style.backgroundColor='var(--bg-light)'; this.style.transform='translateY(0)'">
                        <i class="fas fa-user-edit mr-2"></i>
                        Ver Perfil Público
                    </a>
                </div>
            </div>
        </div>

        <!-- Mensajes Flash -->
        <?php include APP_PATH . '/views/components/flash-messages.php'; ?>

        <!-- Estadísticas Principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Propiedades -->
            <div class="overflow-hidden shadow rounded-lg" style="background: linear-gradient(135deg, var(--bg-light) 0%, rgba(255, 255, 255, 0.9) 100%); border: 1px solid var(--color-gris-claro); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-md flex items-center justify-center" style="background-color: var(--color-azul-marino);">
                                <i class="fas fa-home text-white"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium truncate" style="color: var(--text-secondary);">Total Propiedades</dt>
                                <dd class="text-lg font-medium" style="color: var(--text-primary);"><?= $stats['propiedades'] ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Propiedades Activas -->
            <div class="overflow-hidden shadow rounded-lg" style="background: linear-gradient(135deg, var(--bg-light) 0%, rgba(255, 255, 255, 0.9) 100%); border: 1px solid var(--color-gris-claro); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-md flex items-center justify-center" style="background-color: var(--color-verde-esmeralda);">
                                <i class="fas fa-check text-white"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium truncate" style="color: var(--text-secondary);">Propiedades Activas</dt>
                                <dd class="text-lg font-medium" style="color: var(--text-primary);"><?= $stats['propiedades_activas'] ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Propiedades Vendidas -->
            <div class="overflow-hidden shadow rounded-lg" style="background: linear-gradient(135deg, var(--bg-light) 0%, rgba(255, 255, 255, 0.9) 100%); border: 1px solid var(--color-gris-claro); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-md flex items-center justify-center" style="background-color: var(--color-dorado-suave);">
                                <i class="fas fa-dollar-sign text-white"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium truncate" style="color: var(--text-secondary);">Propiedades Vendidas</dt>
                                <dd class="text-lg font-medium" style="color: var(--text-primary);"><?= $stats['propiedades_vendidas'] ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Solicitudes -->
            <div class="overflow-hidden shadow rounded-lg" style="background: linear-gradient(135deg, var(--bg-light) 0%, rgba(255, 255, 255, 0.9) 100%); border: 1px solid var(--color-gris-claro); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-md flex items-center justify-center" style="background-color: var(--color-gris-claro);">
                                <i class="fas fa-file-alt" style="color: var(--text-primary);"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium truncate" style="color: var(--text-secondary);">Solicitudes</dt>
                                <dd class="text-lg font-medium" style="color: var(--text-primary);"><?= $stats['solicitudes'] ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Propiedades Recientes -->
            <div class="shadow rounded-lg" style="background: linear-gradient(135deg, var(--bg-light) 0%, rgba(255, 255, 255, 0.9) 100%); border: 1px solid var(--color-gris-claro); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);">
                <div class="px-6 py-4 border-b" style="border-bottom-color: var(--color-gris-claro);">
                    <h2 class="text-lg font-semibold" style="color: var(--text-primary);">Propiedades Recientes</h2>
                    <p class="mt-1 text-sm" style="color: var(--text-secondary);">Tus últimas propiedades publicadas</p>
                </div>
                <div class="p-6">
                    <?php if (empty($recentProperties)): ?>
                        <div class="text-center py-8">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center" style="background-color: var(--color-gris-claro);">
                                <i class="fas fa-home text-2xl" style="color: var(--text-secondary);"></i>
                            </div>
                            <h3 class="text-lg font-medium mb-2" style="color: var(--text-primary);">No hay propiedades</h3>
                            <p class="mb-4" style="color: var(--text-secondary);">Aún no has publicado ninguna propiedad.</p>
                            <a href="/properties/create" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm transition-all duration-200"
                               style="background: linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%); color: white;"
                               onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(29, 53, 87, 0.3)'"
                               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                <i class="fas fa-plus mr-2"></i>
                                Publicar Propiedad
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($recentProperties as $property): ?>
                                <div class="flex items-center space-x-4 p-4 border rounded-lg transition-all duration-200" style="border-color: var(--color-gris-claro);" onmouseover="this.style.backgroundColor='var(--bg-secondary)'" onmouseout="this.style.backgroundColor='transparent'">
                                    <?php if (!empty($property['imagen_principal'])): ?>
                                        <img src="<?= htmlspecialchars($property['imagen_principal']) ?>" 
                                             alt="<?= htmlspecialchars($property['titulo']) ?>" 
                                             class="w-16 h-16 object-cover rounded-lg">
                                    <?php else: ?>
                                        <div class="w-16 h-16 rounded-lg flex items-center justify-center" style="background-color: var(--color-gris-claro);">
                                            <i class="fas fa-home" style="color: var(--text-secondary);"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm font-medium truncate" style="color: var(--text-primary);">
                                            <?= htmlspecialchars($property['titulo']) ?>
                                        </h3>
                                        <p class="text-sm" style="color: var(--text-secondary);">
                                            <?= htmlspecialchars($property['ciudad'] . ', ' . $property['sector']) ?>
                                        </p>
                                        <p class="text-sm font-medium" style="color: var(--color-azul-marino);">
                                            $<?= number_format($property['precio']) ?>
                                        </p>
                                    </div>
                                    
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="<?= getEstadoPropiedadBadgeStyle($property['estado_publicacion']) ?>">
                                            <?= getEstadoPropiedadDisplayName($property['estado_publicacion']) ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="mt-6 text-center">
                            <a href="/properties/agent/list" class="text-sm font-medium transition-all duration-200" style="color: var(--color-azul-marino);" onmouseover="this.style.color='var(--color-azul-marino-hover)'" onmouseout="this.style.color='var(--color-azul-marino)'">
                                Ver todas las propiedades →
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Solicitudes Recientes -->
            <div class="shadow rounded-lg" style="background: linear-gradient(135deg, var(--bg-light) 0%, rgba(255, 255, 255, 0.9) 100%); border: 1px solid var(--color-gris-claro); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);">
                <div class="px-6 py-4 border-b" style="border-bottom-color: var(--color-gris-claro);">
                    <h2 class="text-lg font-semibold" style="color: var(--text-primary);">Solicitudes Recientes</h2>
                    <p class="mt-1 text-sm" style="color: var(--text-secondary);">Últimas solicitudes de compra recibidas</p>
                </div>
                <div class="p-6">
                    <?php if (empty($recentSolicitudes)): ?>
                        <div class="text-center py-8">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center" style="background-color: var(--color-gris-claro);">
                                <i class="fas fa-file-alt text-2xl" style="color: var(--text-secondary);"></i>
                            </div>
                            <h3 class="text-lg font-medium mb-2" style="color: var(--text-primary);">No hay solicitudes</h3>
                            <p style="color: var(--text-secondary);">Aún no has recibido solicitudes de compra.</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($recentSolicitudes as $solicitud): ?>
                                <div class="flex items-center space-x-4 p-4 border rounded-lg transition-all duration-200" style="border-color: var(--color-gris-claro);" onmouseover="this.style.backgroundColor='var(--bg-secondary)'" onmouseout="this.style.backgroundColor='transparent'">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: rgba(29, 53, 87, 0.1);">
                                            <i class="fas fa-user" style="color: var(--color-azul-marino);"></i>
                                        </div>
                                    </div>
                                    
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm font-medium" style="color: var(--text-primary);">
                                            <?= htmlspecialchars($solicitud['nombre_cliente']) ?>
                                        </h3>
                                        <p class="text-sm" style="color: var(--text-secondary);">
                                            <?= htmlspecialchars($solicitud['titulo_propiedad']) ?>
                                        </p>
                                        <p class="text-xs" style="color: var(--text-muted);">
                                            <?= date('d/m/Y H:i', strtotime($solicitud['fecha_solicitud'])) ?>
                                        </p>
                                    </div>
                                    
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="<?= getEstadoSolicitudBadgeStyle($solicitud['estado']) ?>">
                                            <?= ucfirst(str_replace('_', ' ', $solicitud['estado'])) ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="mt-6 text-center">
                            <a href="/solicitudes" class="text-sm font-medium transition-all duration-200" style="color: var(--color-azul-marino);" onmouseover="this.style.color='var(--color-azul-marino-hover)'" onmouseout="this.style.color='var(--color-azul-marino)'">
                                Ver todas las solicitudes →
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Acciones Rápidas -->
        <div class="mt-8 shadow rounded-lg" style="background: linear-gradient(135deg, var(--bg-light) 0%, rgba(255, 255, 255, 0.9) 100%); border: 1px solid var(--color-gris-claro); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);">
            <div class="px-6 py-4 border-b" style="border-bottom-color: var(--color-gris-claro);">
                <h2 class="text-lg font-semibold" style="color: var(--text-primary);">Acciones Rápidas</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="/properties/create" class="flex items-center p-4 border rounded-lg transition-all duration-200" style="border-color: var(--color-gris-claro);" onmouseover="this.style.backgroundColor='var(--bg-secondary)'" onmouseout="this.style.backgroundColor='transparent'">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: rgba(29, 53, 87, 0.1);">
                                <i class="fas fa-plus" style="color: var(--color-azul-marino);"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium" style="color: var(--text-primary);">Publicar Propiedad</h3>
                            <p class="text-sm" style="color: var(--text-secondary);">Crear una nueva publicación</p>
                        </div>
                    </a>
                    
                    <a href="/properties/pending-validation" class="flex items-center p-4 border rounded-lg transition-all duration-200" style="border-color: var(--color-gris-claro);" onmouseover="this.style.backgroundColor='var(--bg-secondary)'" onmouseout="this.style.backgroundColor='transparent'">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: rgba(233, 196, 106, 0.1);">
                                <i class="fas fa-clock" style="color: var(--color-dorado-suave);"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium" style="color: var(--text-primary);">Validar Propiedades</h3>
                            <p class="text-sm" style="color: var(--text-secondary);">Revisar propiedades pendientes</p>
                        </div>
                    </a>
                    
                    <a href="/profile" class="flex items-center p-4 border rounded-lg transition-all duration-200" style="border-color: var(--color-gris-claro);" onmouseover="this.style.backgroundColor='var(--bg-secondary)'" onmouseout="this.style.backgroundColor='transparent'">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: rgba(42, 157, 143, 0.1);">
                                <i class="fas fa-user-edit" style="color: var(--color-verde-esmeralda);"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium" style="color: var(--text-primary);">Editar Perfil</h3>
                            <p class="text-sm" style="color: var(--text-secondary);">Actualizar información personal</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Funciones auxiliares para badges
function getEstadoPropiedadBadgeStyle($estado) {
    switch ($estado) {
        case 'activa':
            return 'background-color: var(--color-verde-esmeralda); color: white; border: 1px solid var(--color-verde-esmeralda);';
        case 'en_revision':
            return 'background-color: #fef3c7; color: #92400e; border: 1px solid #f59e0b; font-weight: 600;';
        case 'rechazada':
        case 'inactiva':
            return 'background-color: var(--color-rojo-error); color: white; border: 1px solid var(--color-rojo-error);';
        default:
            return 'background-color: rgba(221, 226, 230, 0.3); color: var(--text-secondary); border: 1px solid var(--color-gris-claro);';
    }
}

function getEstadoSolicitudBadgeStyle($estado) {
    switch ($estado) {
        case 'nuevo':
            return 'background-color: rgba(29, 53, 87, 0.1); color: var(--color-azul-marino); border: 1px solid var(--color-azul-marino);';
        case 'en_revision':
            return 'background-color: rgba(233, 196, 106, 0.1); color: var(--color-dorado-suave); border: 1px solid var(--color-dorado-suave);';
        case 'aceptada':
        case 'reunion_agendada':
            return 'background-color: var(--color-verde-esmeralda); color: white; border: 1px solid var(--color-verde-esmeralda);';
        default:
            return 'background-color: rgba(221, 226, 230, 0.3); color: var(--text-secondary); border: 1px solid var(--color-gris-claro);';
    }
}

function getEstadoPropiedadDisplayName($estado) {
    switch ($estado) {
        case 'activa':
            return 'Activa';
        case 'vendida':
            return 'Vendida';
        case 'en_revision':
            return 'En Revisión';
        case 'rechazada':
            return 'Rechazada';
        case 'inactiva':
            return 'Inactiva';
        default:
            return ucfirst(str_replace('_', ' ', $estado));
    }
}
?> 