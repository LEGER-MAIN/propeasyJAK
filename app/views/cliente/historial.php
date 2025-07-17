<?php
/**
 * Vista: Historial del Cliente
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Esta vista muestra el historial de actividades del cliente
 */
?>

<div class="bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Mi Historial</h1>
                    <p class="mt-2 text-gray-600">Revisa todas tus actividades en la plataforma</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="/cliente/dashboard" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Mensajes Flash -->
        <?php include APP_PATH . '/views/components/flash-messages.php'; ?>

        <!-- Historial de Actividades -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Actividades Recientes</h2>
                <p class="mt-1 text-sm text-gray-600">Todas tus interacciones con la plataforma</p>
            </div>
            
            <div class="p-6">
                <?php if (!empty($actividades)): ?>
                    <div class="space-y-4">
                        <?php foreach ($actividades as $actividad): ?>
                            <div class="flex items-start space-x-4 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <div class="flex-shrink-0">
                                    <?php if ($actividad['tipo'] === 'solicitud'): ?>
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-handshake text-blue-600"></i>
                                        </div>
                                    <?php elseif ($actividad['tipo'] === 'favorito'): ?>
                                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-heart text-red-600"></i>
                                        </div>
                                    <?php else: ?>
                                        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-info text-gray-600"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-medium text-gray-900">
                                        <?= htmlspecialchars($actividad['descripcion']) ?>
                                    </h3>
                                    <div class="flex items-center space-x-2 mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            <?= $actividad['estado'] === 'pendiente' ? 'bg-yellow-100 text-yellow-800' : 
                                               ($actividad['estado'] === 'aprobada' ? 'bg-green-100 text-green-800' : 
                                               'bg-gray-100 text-gray-800') ?>">
                                            <?= ucfirst($actividad['estado']) ?>
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            <?= date('d/m/Y H:i', strtotime($actividad['fecha'])) ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="flex-shrink-0">
                                    <?php if ($actividad['tipo'] === 'solicitud'): ?>
                                        <a href="/solicitudes/show/<?= $actividad['data']['id'] ?>" 
                                           class="text-primary-600 hover:text-primary-500 text-sm font-medium">
                                            Ver
                                        </a>
                                    <?php elseif ($actividad['tipo'] === 'favorito'): ?>
                                        <a href="/properties/show/<?= $actividad['data']['propiedad_id'] ?>" 
                                           class="text-primary-600 hover:text-primary-500 text-sm font-medium">
                                            Ver
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Paginación -->
                    <div class="mt-8 flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Mostrando <?= count($actividades) ?> actividades
                        </div>
                        <div class="flex space-x-2">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?= $page - 1 ?>" 
                                   class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    Anterior
                                </a>
                            <?php endif; ?>
                            
                            <a href="?page=<?= $page + 1 ?>" 
                               class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Siguiente
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-history text-gray-400 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No hay actividades</h3>
                        <p class="text-gray-500 mb-4">Aún no has realizado ninguna actividad en la plataforma</p>
                        <a href="/properties" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 transition-colors">
                            <i class="fas fa-search mr-2"></i>
                            Explorar Propiedades
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div> 