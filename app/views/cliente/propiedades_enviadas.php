<?php
/**
 * Vista: Mis Ventas (Propiedades Enviadas por el Cliente)
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Esta vista muestra las propiedades que el cliente ha enviado para publicación
 */

// Verificar que las variables estén definidas
$data = $data ?? [];
$propiedades = $data['propiedades'] ?? [];
$total_propiedades = $data['total_propiedades'] ?? 0;
?>



    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-primary-600 to-primary-800 text-white" style="background: linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center">
                <h1 class="text-4xl font-bold mb-4" style="color: white !important;">Mis <span style="color: var(--color-dorado-suave);">Ventas</span></h1>
                <p class="text-xl text-primary-100 mb-8" style="color: var(--text-light);">
                    Gestiona las propiedades que has enviado para publicación
                </p>
                
                <!-- Estadísticas rápidas -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto">
                    <div class="text-center">
                        <div class="text-3xl font-bold" style="color: white !important; text-shadow: 2px 2px 4px rgba(0,0,0,0.5); font-weight: 800;"><?= $total_propiedades ?></div>
                        <div class="text-primary-200" style="color: var(--text-light) !important; font-weight: 600;">Total</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold" style="color: white !important; text-shadow: 2px 2px 4px rgba(0,0,0,0.5); font-weight: 800;">
                            <?= count(array_filter($propiedades, function($p) { return ($p['estado_publicacion'] ?? $p['estado']) === 'activa'; })) ?>
                        </div>
                        <div class="text-primary-200" style="color: var(--text-light) !important; font-weight: 600;">Activas</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold" style="color: white !important; text-shadow: 2px 2px 4px rgba(0,0,0,0.5); font-weight: 800;">
                            <?= count(array_filter($propiedades, function($p) { return ($p['estado_publicacion'] ?? $p['estado']) === 'en_revision'; })) ?>
                        </div>
                        <div class="text-primary-200" style="color: var(--text-light) !important; font-weight: 600;">En Revisión</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold" style="color: white !important; text-shadow: 2px 2px 4px rgba(0,0,0,0.5); font-weight: 800;">
                            <?= count(array_filter($propiedades, function($p) { return ($p['estado_publicacion'] ?? $p['estado']) === 'vendida'; })) ?>
                        </div>
                        <div class="text-primary-200" style="color: var(--text-light) !important; font-weight: 600;">Vendidas</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Mensajes Flash -->
        <?php include APP_PATH . '/views/components/flash-messages.php'; ?>

        <!-- Header de la sección -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2" style="color: var(--color-azul-marino);">
                    Propiedades Enviadas
                </h2>
                <p class="text-gray-600" style="color: var(--text-secondary);">
                    <?= $total_propiedades ?> propiedad<?= $total_propiedades != 1 ? 'es' : '' ?> enviada<?= $total_propiedades != 1 ? 's' : '' ?> para publicación
                </p>
            </div>
            
            <!-- Botón para enviar nueva propiedad -->
            <div class="mt-4 md:mt-0">
                <a href="/properties/create" 
                   class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg"
                   style="background-color: var(--color-azul-marino);">
                    <i class="fas fa-plus mr-2"></i>
                    Enviar Nueva Propiedad
                </a>
            </div>
        </div>

        <!-- Filtros por Estado -->
        <div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2" style="color: var(--color-azul-marino);">
                        Filtrar por Estado
                    </h3>
                    <p class="text-sm text-gray-600" style="color: var(--text-secondary);">
                        Selecciona un estado para filtrar las propiedades
                    </p>
                </div>
                
                <div class="flex flex-wrap gap-2">
                    <!-- Filtro: Todos -->
                    <button onclick="filtrarPropiedades('todos')" 
                            class="filtro-estado px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 border-2 active" 
                            data-estado="todos"
                            style="border-color: var(--color-azul-marino); color: var(--color-azul-marino); background-color: var(--color-azul-marino-light);">
                        <i class="fas fa-list mr-2"></i>Todos
                        <span class="ml-2 bg-white text-blue-600 px-2 py-1 rounded-full text-xs font-bold"><?= $total_propiedades ?></span>
                    </button>
                    
                    <!-- Filtro: Activas -->
                    <button onclick="filtrarPropiedades('activa')" 
                            class="filtro-estado px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 border-2" 
                            data-estado="activa"
                            style="border-color: var(--color-verde); color: var(--color-verde);">
                        <i class="fas fa-check-circle mr-2"></i>Activas
                        <span class="ml-2 bg-green-100 text-green-600 px-2 py-1 rounded-full text-xs font-bold"><?= count(array_filter($propiedades, function($p) { return ($p['estado_publicacion'] ?? $p['estado']) === 'activa'; })) ?></span>
                    </button>
                    
                    <!-- Filtro: En Revisión -->
                    <button onclick="filtrarPropiedades('en_revision')" 
                            class="filtro-estado px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 border-2" 
                            data-estado="en_revision"
                            style="border-color: #f59e0b; color: #f59e0b;">
                        <i class="fas fa-clock mr-2"></i>En Revisión
                        <span class="ml-2 bg-yellow-100 text-yellow-600 px-2 py-1 rounded-full text-xs font-bold"><?= count(array_filter($propiedades, function($p) { return ($p['estado_publicacion'] ?? $p['estado']) === 'en_revision'; })) ?></span>
                    </button>
                    
                    <!-- Filtro: Vendidas -->
                    <button onclick="filtrarPropiedades('vendida')" 
                            class="filtro-estado px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 border-2" 
                            data-estado="vendida"
                            style="border-color: #3b82f6; color: #3b82f6;">
                        <i class="fas fa-trophy mr-2"></i>Vendidas
                        <span class="ml-2 bg-blue-100 text-blue-600 px-2 py-1 rounded-full text-xs font-bold"><?= count(array_filter($propiedades, function($p) { return ($p['estado_publicacion'] ?? $p['estado']) === 'vendida'; })) ?></span>
                    </button>
                    
                    <!-- Filtro: Rechazadas -->
                    <button onclick="filtrarPropiedades('rechazada')" 
                            class="filtro-estado px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 border-2" 
                            data-estado="rechazada"
                            style="border-color: #ef4444; color: #ef4444;">
                        <i class="fas fa-times-circle mr-2"></i>Rechazadas
                        <span class="ml-2 bg-red-100 text-red-600 px-2 py-1 rounded-full text-xs font-bold"><?= count(array_filter($propiedades, function($p) { return ($p['estado_publicacion'] ?? $p['estado']) === 'rechazada'; })) ?></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Lista de Propiedades -->
        <div id="propiedades-container">
            <?php if (!empty($propiedades)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($propiedades as $propiedad): ?>
                    <?php
                    $estado = $propiedad['estado'] ?? $propiedad['estado_publicacion'] ?? 'en_revision';
                    ?>
                    <div class="propiedad-card bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300" data-estado="<?= $estado ?>">
                        <!-- Imagen de la propiedad -->
                        <div class="relative h-48 bg-gray-200">
                            <?php if (!empty($propiedad['foto_propiedad']) || !empty($propiedad['imagen_principal'])): ?>
                                <img src="<?= htmlspecialchars($propiedad['foto_propiedad'] ?? $propiedad['imagen_principal']) ?>" 
                                     alt="<?= htmlspecialchars($propiedad['titulo_propiedad'] ?? $propiedad['titulo'] ?? 'Propiedad') ?>" 
                                     class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-home text-4xl text-gray-400"></i>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Badge de tipo -->
                            <div class="absolute top-2 left-2">
                                <span class="bg-primary-600 text-white px-2 py-1 rounded text-xs font-medium" style="background-color: var(--color-azul-marino);">
                                    <?= ucfirst($propiedad['tipo'] ?? 'Propiedad') ?>
                                </span>
                            </div>
                            
                            <!-- Badge de estado -->
                            <div class="absolute top-2 right-2">
                                <?php
                                $estadoColors = [
                                    'en_revision' => 'bg-yellow-500',
                                    'activa' => 'bg-green-500',
                                    'vendida' => 'bg-blue-500',
                                    'rechazada' => 'bg-red-500',
                                    'inactiva' => 'bg-gray-500'
                                ];
                                $estado = $propiedad['estado'] ?? $propiedad['estado_publicacion'] ?? 'en_revision';
                                $estadoColor = $estadoColors[$estado] ?? 'bg-gray-500';
                                
                                $estadoTextos = [
                                    'en_revision' => 'En Revisión',
                                    'activa' => 'Activa',
                                    'vendida' => 'Vendida',
                                    'rechazada' => 'Rechazada',
                                    'inactiva' => 'Inactiva'
                                ];
                                $estadoTexto = $estadoTextos[$estado] ?? 'Desconocido';
                                ?>
                                <span class="<?= $estadoColor ?> text-white px-2 py-1 rounded text-xs font-medium">
                                    <?= $estadoTexto ?>
                                </span>
                            </div>
                            
                            <!-- Precio -->
                            <div class="absolute bottom-2 right-2">
                                <span class="bg-white text-primary-600 px-3 py-1 rounded-lg text-sm font-bold shadow-md" style="color: var(--color-azul-marino);">
                                    $<?= number_format($propiedad['precio_propiedad'] ?? $propiedad['precio'] ?? 0, 0, ',', '.') ?>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Información de la propiedad -->
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                                <a href="/properties/<?= $propiedad['id'] ?>" class="hover:text-primary-600 transition-colors" style="color: var(--text-primary);">
                                    <?= htmlspecialchars($propiedad['titulo_propiedad'] ?? $propiedad['titulo'] ?? 'Sin título') ?>
                                </a>
                            </h3>
                            
                            <!-- Ubicación -->
                            <div class="flex items-center text-gray-500 text-sm mb-3">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                <span><?= htmlspecialchars($propiedad['ciudad_propiedad'] ?? $propiedad['ciudad'] ?? 'Sin especificar') ?>, <?= htmlspecialchars($propiedad['sector_propiedad'] ?? $propiedad['sector'] ?? 'Sin especificar') ?></span>
                            </div>
                            
                            <!-- Características -->
                            <div class="flex items-center justify-between text-sm text-gray-600 mb-4">
                                <div class="flex space-x-4">
                                    <?php if (($propiedad['habitaciones'] ?? 0) > 0): ?>
                                        <span class="flex items-center">
                                            <i class="fas fa-bed mr-1"></i>
                                            <?= $propiedad['habitaciones'] ?> hab.
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if (($propiedad['banos'] ?? 0) > 0): ?>
                                        <span class="flex items-center">
                                            <i class="fas fa-bath mr-1"></i>
                                            <?= $propiedad['banos'] ?> baños
                                        </span>
                                    <?php endif; ?>
                                    
                                    <span class="flex items-center">
                                        <i class="fas fa-ruler-combined mr-1"></i>
                                        <?= number_format($propiedad['area_propiedad'] ?? $propiedad['metros_cuadrados'] ?? 0, 0) ?> m²
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Información del agente -->
                            <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                                <div class="flex items-center">
                                    <?php if (!empty($propiedad['foto_agente']) || !empty($propiedad['agente_foto'])): ?>
                                        <img src="<?= htmlspecialchars($propiedad['foto_agente'] ?? $propiedad['agente_foto']) ?>" 
                                             alt="<?= htmlspecialchars(($propiedad['nombre_agente'] ?? '') . ' ' . ($propiedad['apellido_agente'] ?? '')) ?>" 
                                             class="w-8 h-8 rounded-full mr-3">
                                    <?php else: ?>
                                        <div class="w-8 h-8 bg-gray-300 rounded-full mr-3 flex items-center justify-center">
                                            <i class="fas fa-user text-gray-600 text-sm"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div>
                                        <div class="font-medium text-gray-800 text-sm">
                                            <?= htmlspecialchars(($propiedad['nombre_agente'] ?? '') . ' ' . ($propiedad['apellido_agente'] ?? 'Sin asignar')) ?>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Enviada el <?= date('d/m/Y', strtotime($propiedad['fecha_solicitud'] ?? $propiedad['fecha_creacion'] ?? date('Y-m-d H:i:s'))) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Botones de acción -->
                            <div class="flex space-x-2 mt-4">
                                <a href="/properties/<?= $propiedad['id'] ?>" 
                                   class="flex-1 bg-primary-600 hover:bg-primary-700 text-white text-center py-2 px-4 rounded-md transition-colors font-medium"
                                   style="background-color: var(--color-azul-marino);">
                                    <i class="fas fa-eye mr-2"></i>Ver
                                </a>
                                
                                <?php 
                                $estado = $propiedad['estado'] ?? $propiedad['estado_publicacion'] ?? 'en_revision';
                                if ($estado === 'en_revision'): ?>
                                    <button class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md transition-colors font-medium">
                                        <i class="fas fa-clock mr-1"></i>Pendiente
                                    </button>
                                <?php elseif ($estado === 'activa'): ?>
                                    <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md transition-colors font-medium">
                                        <i class="fas fa-check mr-1"></i>Activa
                                    </button>
                                <?php elseif ($estado === 'vendida'): ?>
                                    <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition-colors font-medium">
                                        <i class="fas fa-trophy mr-1"></i>Vendida
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
                <a href="/properties/create" 
                   class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg"
                   style="background-color: var(--color-azul-marino);">
                    <i class="fas fa-plus mr-2"></i>Enviar Nueva Propiedad
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- JavaScript para los filtros -->
    <script>
        function filtrarPropiedades(estado) {
            // Obtener todos los botones de filtro
            const botonesFiltro = document.querySelectorAll('.filtro-estado');
            const propiedades = document.querySelectorAll('.propiedad-card');
            
            // Remover clase active de todos los botones
            botonesFiltro.forEach(boton => {
                boton.classList.remove('active');
                boton.style.backgroundColor = '';
                boton.style.color = boton.style.borderColor;
            });
            
            // Agregar clase active al botón seleccionado
            const botonActivo = document.querySelector(`[data-estado="${estado}"]`);
            if (botonActivo) {
                botonActivo.classList.add('active');
                botonActivo.style.backgroundColor = 'var(--color-azul-marino-light)';
                botonActivo.style.color = 'var(--color-azul-marino)';
            }
            
            // Filtrar las propiedades
            propiedades.forEach(propiedad => {
                if (estado === 'todos') {
                    propiedad.style.display = 'block';
                    propiedad.style.opacity = '1';
                    propiedad.style.transform = 'scale(1)';
                } else {
                    const estadoPropiedad = propiedad.getAttribute('data-estado');
                    if (estadoPropiedad === estado) {
                        propiedad.style.display = 'block';
                        propiedad.style.opacity = '1';
                        propiedad.style.transform = 'scale(1)';
                    } else {
                        propiedad.style.display = 'none';
                        propiedad.style.opacity = '0';
                        propiedad.style.transform = 'scale(0.95)';
                    }
                }
            });
            
            // Actualizar contador de propiedades mostradas
            actualizarContadorPropiedades(estado);
        }
        
        function actualizarContadorPropiedades(estado) {
            const propiedadesVisibles = document.querySelectorAll('.propiedad-card[style*="display: block"], .propiedad-card:not([style*="display: none"])');
            const contador = propiedadesVisibles.length;
            
            // Actualizar el texto del contador en el header
            const headerTexto = document.querySelector('.text-gray-600');
            if (headerTexto) {
                if (estado === 'todos') {
                    headerTexto.textContent = `${contador} propiedad${contador != 1 ? 'es' : ''} enviada${contador != 1 ? 's' : ''} para publicación`;
                } else {
                    const estadoTexto = {
                        'activa': 'Activas',
                        'en_revision': 'En Revisión',
                        'vendida': 'Vendidas',
                        'rechazada': 'Rechazadas'
                    };
                    headerTexto.textContent = `${contador} propiedad${contador != 1 ? 'es' : ''} ${estadoTexto[estado] || estado}`;
                }
            }
        }
        
        // Inicializar con el filtro "Todos" activo
        document.addEventListener('DOMContentLoaded', function() {
            filtrarPropiedades('todos');
        });
    </script>