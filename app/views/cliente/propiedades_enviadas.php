<?php
/**
 * Vista: Mis Ventas (Propiedades Enviadas por el Cliente)
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Esta vista muestra las propiedades que el cliente ha enviado para publicación
 */
?>

<div class="bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900">Mis Ventas</h1>
                <p class="mt-2 text-gray-600">Gestiona las propiedades que has enviado para publicación</p>
            </div>
        </div>

        <!-- Mensajes Flash -->
        <?php include APP_PATH . '/views/components/flash-messages.php'; ?>

        <!-- Estadísticas -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600 mb-2"><?= $data['total_propiedades'] ?></div>
                    <div class="text-gray-600">Total de Propiedades</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600 mb-2">
                        <?= count(array_filter($data['propiedades'], function($p) { return $p['estado_publicacion'] === 'activa'; })) ?>
                    </div>
                    <div class="text-gray-600">Propiedades Activas</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-yellow-600 mb-2">
                        <?= count(array_filter($data['propiedades'], function($p) { return $p['estado_publicacion'] === 'en_revision'; })) ?>
                    </div>
                    <div class="text-gray-600">En Revisión</div>
                </div>
            </div>
        </div>

        <!-- Lista de Propiedades -->
        <?php if (!empty($data['propiedades'])): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($data['propiedades'] as $propiedad): ?>
                    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden">
                        <!-- Imagen de la propiedad -->
                        <div class="relative h-48 bg-gray-200">
                            <?php if (!empty($propiedad['imagen_principal'])): ?>
                                <img src="<?= htmlspecialchars($propiedad['imagen_principal']) ?>" 
                                     alt="<?= htmlspecialchars($propiedad['titulo']) ?>" 
                                     class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="flex items-center justify-center h-full">
                                    <i class="fas fa-home text-4xl text-gray-400"></i>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Badge de estado -->
                            <div class="absolute top-4 right-4">
                                <?php
                                $estadoColors = [
                                    'en_revision' => 'bg-yellow-100 text-yellow-800',
                                    'activa' => 'bg-green-100 text-green-800',
                                    'vendida' => 'bg-blue-100 text-blue-800',
                                    'rechazada' => 'bg-red-100 text-red-800',
                                    'inactiva' => 'bg-gray-100 text-gray-800'
                                ];
                                $estadoColor = $estadoColors[$propiedad['estado_publicacion']] ?? 'bg-gray-100 text-gray-800';
                                ?>
                                <span class="px-3 py-1 rounded-full text-sm font-medium <?= $estadoColor ?>">
                                    <?= $propiedad['estado_publicacion_texto'] ?>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Contenido de la propiedad -->
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">
                                <?= htmlspecialchars($propiedad['titulo']) ?>
                            </h3>
                            
                            <p class="text-gray-600 mb-3">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                <?= htmlspecialchars($propiedad['ciudad']) ?>, <?= htmlspecialchars($propiedad['sector']) ?>
                            </p>
                            
                            <div class="text-2xl font-bold text-blue-600 mb-3">
                                <?= $propiedad['precio_formateado'] ?>
                            </div>
                            
                            <!-- Características -->
                            <div class="grid grid-cols-3 gap-4 mb-4 text-sm text-gray-600">
                                <div>
                                    <i class="fas fa-ruler-combined mr-1"></i>
                                    <?= $propiedad['metros_cuadrados'] ?> m²
                                </div>
                                <div>
                                    <i class="fas fa-bed mr-1"></i>
                                    <?= $propiedad['habitaciones'] ?> hab.
                                </div>
                                <div>
                                    <i class="fas fa-bath mr-1"></i>
                                    <?= $propiedad['banos'] ?> baños
                                </div>
                            </div>
                            
                            <!-- Información del agente -->
                            <div class="border-t pt-4 mb-4">
                                <div class="flex items-center">
                                    <?php if (!empty($propiedad['agente_foto'])): ?>
                                        <img src="<?= htmlspecialchars($propiedad['agente_foto']) ?>" 
                                             alt="<?= htmlspecialchars($propiedad['agente_nombre_completo']) ?>" 
                                             class="w-8 h-8 rounded-full mr-3">
                                    <?php else: ?>
                                        <div class="w-8 h-8 bg-gray-300 rounded-full mr-3 flex items-center justify-center">
                                            <i class="fas fa-user text-gray-600 text-sm"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div>
                                        <div class="font-medium text-gray-800">
                                            <?= htmlspecialchars($propiedad['agente_nombre_completo']) ?>
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            Enviada el <?= $propiedad['fecha_creacion_formateada'] ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Botones de acción -->
                            <div class="flex space-x-2">
                                <a href="/properties/show/<?= $propiedad['id'] ?>" 
                                   class="flex-1 bg-primary-600 hover:bg-primary-700 text-white text-center py-2 px-4 rounded-md transition-colors">
                                    <i class="fas fa-eye mr-2"></i>Ver
                                </a>
                                
                                <?php if ($propiedad['estado_publicacion'] === 'en_revision'): ?>
                                    <button class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md transition-colors">
                                        <i class="fas fa-clock mr-1"></i>Pendiente
                                    </button>
                                <?php elseif ($propiedad['estado_publicacion'] === 'activa'): ?>
                                    <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md transition-colors">
                                        <i class="fas fa-check mr-1"></i>Activa
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Estado vacío -->
            <div class="text-center py-12">
                <div class="mb-6">
                    <i class="fas fa-home text-6xl text-gray-300"></i>
                </div>
                <h3 class="text-2xl font-semibold text-gray-600 mb-4">No tienes propiedades enviadas</h3>
                <p class="text-gray-500 mb-8">Cuando envíes propiedades para publicación, aparecerán aquí.</p>
                <a href="/properties/create" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                    <i class="fas fa-plus mr-2"></i>Enviar Nueva Propiedad
                </a>
            </div>
        <?php endif; ?>
    </div>
</div> 