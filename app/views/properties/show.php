<?php
/**
 * Vista: Detalle de Propiedad
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Esta vista muestra los detalles completos de una propiedad
 */

// Incluir el layout principal
$content = ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="/" class="text-gray-700 hover:text-primary-600">
                    <i class="fas fa-home mr-2"></i>Inicio
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <a href="/properties" class="text-gray-700 hover:text-primary-600">Propiedades</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-gray-500"><?= htmlspecialchars($property['titulo']) ?></span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Columna principal -->
        <div class="lg:col-span-2">
            <!-- Galería de imágenes -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <?php if (!empty($property['imagenes'])): ?>
                    <div class="relative">
                        <!-- Imagen principal -->
                        <div class="h-96 bg-gray-200">
                            <img id="main-image" src="<?= htmlspecialchars($property['imagenes'][0]['ruta']) ?>" 
                                 alt="<?= htmlspecialchars($property['titulo']) ?>"
                                 class="w-full h-full object-cover">
                        </div>
                        
                        <!-- Miniaturas -->
                        <?php if (count($property['imagenes']) > 1): ?>
                            <div class="p-4 border-t border-gray-200">
                                <div class="flex space-x-2 overflow-x-auto">
                                    <?php foreach ($property['imagenes'] as $index => $imagen): ?>
                                        <button onclick="changeMainImage('<?= htmlspecialchars($imagen['ruta']) ?>')" 
                                                class="flex-shrink-0 w-20 h-20 border-2 border-transparent hover:border-primary-500 rounded-lg overflow-hidden transition-colors">
                                            <img src="<?= htmlspecialchars($imagen['ruta']) ?>" 
                                                 alt="Imagen <?= $index + 1 ?>"
                                                 class="w-full h-full object-cover">
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="h-96 bg-gray-200 flex items-center justify-center">
                        <div class="text-center">
                            <i class="fas fa-home text-6xl text-gray-400 mb-4"></i>
                            <p class="text-gray-500">No hay imágenes disponibles</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Información de la propiedad -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2"><?= htmlspecialchars($property['titulo']) ?></h1>
                        <div class="flex items-center text-gray-600 mb-2">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            <span><?= htmlspecialchars($property['ciudad']) ?>, <?= htmlspecialchars($property['sector']) ?></span>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-calendar mr-2"></i>
                            <span>Publicada el <?= date('d/m/Y', strtotime($property['fecha_creacion'])) ?></span>
                        </div>
                    </div>
                    
                    <!-- Precio -->
                    <div class="text-right">
                        <div class="text-3xl font-bold text-primary-600">
                            $<?= number_format($property['precio']) ?>
                        </div>
                        <div class="text-sm text-gray-500"><?= $property['moneda'] ?></div>
                    </div>
                </div>

                <!-- Badges -->
                <div class="flex flex-wrap gap-2 mb-6">
                    <span class="bg-primary-100 text-primary-800 px-3 py-1 rounded-full text-sm font-medium">
                        <?= ucfirst(str_replace('_', ' ', $property['tipo'])) ?>
                    </span>
                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                        Estado: <?= ucfirst(str_replace('_', ' ', $property['estado_propiedad'])) ?>
                    </span>
                    <?php if ($property['estado_publicacion'] === 'activa'): ?>
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                            Disponible
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Descripción -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Descripción</h3>
                    <p class="text-gray-700 leading-relaxed"><?= nl2br(htmlspecialchars($property['descripcion'])) ?></p>
                </div>

                <!-- Características -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <i class="fas fa-ruler-combined text-2xl text-primary-600 mb-2"></i>
                        <div class="text-lg font-semibold text-gray-900"><?= number_format($property['metros_cuadrados']) ?></div>
                        <div class="text-sm text-gray-600">m²</div>
                    </div>
                    
                    <?php if ($property['habitaciones'] > 0): ?>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <i class="fas fa-bed text-2xl text-primary-600 mb-2"></i>
                            <div class="text-lg font-semibold text-gray-900"><?= $property['habitaciones'] ?></div>
                            <div class="text-sm text-gray-600">Habitaciones</div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($property['banos'] > 0): ?>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <i class="fas fa-bath text-2xl text-primary-600 mb-2"></i>
                            <div class="text-lg font-semibold text-gray-900"><?= $property['banos'] ?></div>
                            <div class="text-sm text-gray-600">Baños</div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($property['estacionamientos'] > 0): ?>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <i class="fas fa-car text-2xl text-primary-600 mb-2"></i>
                            <div class="text-lg font-semibold text-gray-900"><?= $property['estacionamientos'] ?></div>
                            <div class="text-sm text-gray-600">Estacionamientos</div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Dirección -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Ubicación</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-map-marker-alt text-primary-600 mt-1 mr-3"></i>
                            <div>
                                <p class="font-medium text-gray-900"><?= htmlspecialchars($property['direccion']) ?></p>
                                <p class="text-gray-600"><?= htmlspecialchars($property['sector']) ?>, <?= htmlspecialchars($property['ciudad']) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Información del agente -->
            <?php if (!empty($property['agente_nombre'])): ?>
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Agente Responsable</h3>
                    
                    <div class="flex items-center mb-4">
                        <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mr-4">
                            <span class="text-primary-600 font-bold text-xl">
                                <?= strtoupper(substr($property['agente_nombre'], 0, 1)) ?>
                            </span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">
                                <?= htmlspecialchars($property['agente_nombre'] . ' ' . $property['agente_apellido']) ?>
                            </h4>
                            <p class="text-sm text-gray-600">Agente Inmobiliario</p>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <?php if (!empty($property['agente_telefono'])): ?>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-phone mr-3 text-primary-600"></i>
                                <span><?= htmlspecialchars($property['agente_telefono']) ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($property['agente_email'])): ?>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-envelope mr-3 text-primary-600"></i>
                                <span><?= htmlspecialchars($property['agente_email']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mt-6 space-y-2">
                        <a href="mailto:<?= htmlspecialchars($property['agente_email']) ?>" 
                           class="w-full bg-primary-600 hover:bg-primary-700 text-white text-center py-2 px-4 rounded-md font-medium transition-colors">
                            <i class="fas fa-envelope mr-2"></i>Contactar por Email
                        </a>
                        
                        <?php if (!empty($property['agente_telefono'])): ?>
                            <a href="tel:<?= htmlspecialchars($property['agente_telefono']) ?>" 
                               class="w-full bg-green-600 hover:bg-green-700 text-white text-center py-2 px-4 rounded-md font-medium transition-colors">
                                <i class="fas fa-phone mr-2"></i>Llamar
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Formulario de interés -->
            <?php if (isAuthenticated() && $property['estado_publicacion'] === 'activa'): ?>
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">¿Te interesa esta propiedad?</h3>
                    
                    <form action="/properties/interest/<?= $property['id'] ?>" method="POST" class="space-y-4">
                        <div>
                            <label for="mensaje" class="block text-sm font-medium text-gray-700 mb-1">Mensaje (opcional)</label>
                            <textarea name="mensaje" id="mensaje" rows="3" 
                                      placeholder="Cuéntanos más sobre tu interés en esta propiedad..."
                                      class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500"></textarea>
                        </div>
                        
                        <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white py-2 px-4 rounded-md font-medium transition-colors">
                            <i class="fas fa-heart mr-2"></i>Expresar Interés
                        </button>
                    </form>
                </div>
            <?php elseif (!isAuthenticated() && $property['estado_publicacion'] === 'activa'): ?>
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">¿Te interesa esta propiedad?</h3>
                    <p class="text-gray-600 mb-4">Inicia sesión para expresar tu interés en esta propiedad.</p>
                    <a href="/login" class="w-full bg-primary-600 hover:bg-primary-700 text-white text-center py-2 px-4 rounded-md font-medium transition-colors">
                        Iniciar Sesión
                    </a>
                </div>
            <?php endif; ?>

            <!-- Acciones para propietarios -->
            <?php if (isAuthenticated()): ?>
                <?php 
                $userRole = $_SESSION['user_rol'];
                $userId = $_SESSION['user_id'];
                $canEdit = false;
                
                if ($userRole === 'cliente' && $property['cliente_vendedor_id'] == $userId) {
                    $canEdit = true;
                } elseif ($userRole === 'agente' && $property['agente_id'] == $userId) {
                    $canEdit = true;
                }
                ?>
                
                <?php if ($canEdit): ?>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Acciones</h3>
                        
                        <div class="space-y-2">
                            <a href="/properties/edit/<?= $property['id'] ?>" 
                               class="w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-md font-medium transition-colors">
                                <i class="fas fa-edit mr-2"></i>Editar Propiedad
                            </a>
                            
                            <form action="/properties/delete/<?= $property['id'] ?>" method="POST" 
                                  onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta propiedad?')">
                                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-md font-medium transition-colors">
                                    <i class="fas fa-trash mr-2"></i>Eliminar Propiedad
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Script para cambiar imagen principal -->
<script>
function changeMainImage(imageSrc) {
    document.getElementById('main-image').src = imageSrc;
}
</script>

<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?> 