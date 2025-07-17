<?php
/**
 * Vista: Dashboard del Agente
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Esta vista muestra el dashboard principal del agente con estadísticas y actividades
 */
?>

<div class="bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Dashboard del Agente</h1>
                    <p class="mt-2 text-gray-600">Bienvenido, <?= htmlspecialchars($_SESSION['user_nombre'] . ' ' . $_SESSION['user_apellido']) ?></p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="/agente/perfil-publico" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <i class="fas fa-user-edit mr-2"></i>
                        Gestionar Perfil Público
                    </a>
                </div>
            </div>
        </div>

        <!-- Mensajes Flash -->
        <?php include APP_PATH . '/views/components/flash-messages.php'; ?>

        <!-- Estadísticas Principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Propiedades -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-home text-white"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Propiedades</dt>
                                <dd class="text-lg font-medium text-gray-900"><?= $stats['propiedades'] ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Propiedades Activas -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-check text-white"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Propiedades Activas</dt>
                                <dd class="text-lg font-medium text-gray-900"><?= $stats['propiedades_activas'] ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Propiedades Vendidas -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-dollar-sign text-white"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Propiedades Vendidas</dt>
                                <dd class="text-lg font-medium text-gray-900"><?= $stats['propiedades_vendidas'] ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Solicitudes -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-file-alt text-white"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Solicitudes</dt>
                                <dd class="text-lg font-medium text-gray-900"><?= $stats['solicitudes'] ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Propiedades Recientes -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Propiedades Recientes</h2>
                    <p class="mt-1 text-sm text-gray-600">Tus últimas propiedades publicadas</p>
                </div>
                <div class="p-6">
                    <?php if (empty($recentProperties)): ?>
                        <div class="text-center py-8">
                            <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-home text-2xl text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No hay propiedades</h3>
                            <p class="text-gray-600 mb-4">Aún no has publicado ninguna propiedad.</p>
                            <a href="/properties/create" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700">
                                <i class="fas fa-plus mr-2"></i>
                                Publicar Propiedad
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($recentProperties as $property): ?>
                                <div class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg">
                                    <?php if (!empty($property['imagen_principal'])): ?>
                                        <img src="<?= htmlspecialchars($property['imagen_principal']) ?>" 
                                             alt="<?= htmlspecialchars($property['titulo']) ?>" 
                                             class="w-16 h-16 object-cover rounded-lg">
                                    <?php else: ?>
                                        <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-home text-gray-400"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm font-medium text-gray-900 truncate">
                                            <?= htmlspecialchars($property['titulo']) ?>
                                        </h3>
                                        <p class="text-sm text-gray-500">
                                            <?= htmlspecialchars($property['ciudad'] . ', ' . $property['sector']) ?>
                                        </p>
                                        <p class="text-sm font-medium text-primary-600">
                                            $<?= number_format($property['precio']) ?>
                                        </p>
                                    </div>
                                    
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                   <?= $property['estado_publicacion'] === 'activa' ? 'bg-green-100 text-green-800' : 
                                                      ($property['estado_publicacion'] === 'en_revision' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') ?>">
                                            <?= ucfirst($property['estado_publicacion']) ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="mt-6 text-center">
                            <a href="/properties/agent/list" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                Ver todas las propiedades →
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Solicitudes Recientes -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Solicitudes Recientes</h2>
                    <p class="mt-1 text-sm text-gray-600">Últimas solicitudes de compra recibidas</p>
                </div>
                <div class="p-6">
                    <?php if (empty($recentSolicitudes)): ?>
                        <div class="text-center py-8">
                            <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-file-alt text-2xl text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No hay solicitudes</h3>
                            <p class="text-gray-600">Aún no has recibido solicitudes de compra.</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($recentSolicitudes as $solicitud): ?>
                                <div class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-primary-600"></i>
                                        </div>
                                    </div>
                                    
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($solicitud['nombre_cliente']) ?>
                                        </h3>
                                        <p class="text-sm text-gray-500">
                                            <?= htmlspecialchars($solicitud['titulo_propiedad']) ?>
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            <?= date('d/m/Y H:i', strtotime($solicitud['fecha_solicitud'])) ?>
                                        </p>
                                    </div>
                                    
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                   <?= $solicitud['estado'] === 'nuevo' ? 'bg-blue-100 text-blue-800' : 
                                                      ($solicitud['estado'] === 'en_revision' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') ?>">
                                            <?= ucfirst(str_replace('_', ' ', $solicitud['estado'])) ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="mt-6 text-center">
                            <a href="/solicitudes" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                Ver todas las solicitudes →
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Acciones Rápidas -->
        <div class="mt-8 bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Acciones Rápidas</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="/properties/create" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-plus text-primary-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">Publicar Propiedad</h3>
                            <p class="text-sm text-gray-500">Crear una nueva publicación</p>
                        </div>
                    </a>
                    
                    <a href="/properties/pending-validation" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clock text-yellow-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">Validar Propiedades</h3>
                            <p class="text-sm text-gray-500">Revisar propiedades pendientes</p>
                        </div>
                    </a>
                    
                    <a href="/agente/perfil-publico" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user-edit text-blue-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">Editar Perfil</h3>
                            <p class="text-sm text-gray-500">Actualizar información pública</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div> 