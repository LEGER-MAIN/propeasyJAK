<?php
/**
 * Vista: Detalle de Propiedad
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Esta vista muestra los detalles completos de una propiedad
 */

require_once APP_PATH . '/helpers/PropertyHelper.php';

// Incluir el layout principal
$content = ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="/" class="font-medium transition-colors" style="color: var(--text-primary);">
                    <i class="fas fa-home mr-2" style="color: var(--color-azul-marino);"></i>Inicio
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right mx-2" style="color: var(--color-gris-claro);"></i>
                    <a href="/properties" class="font-medium transition-colors" style="color: var(--text-primary);">Propiedades</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right mx-2" style="color: var(--color-gris-claro);"></i>
                    <span style="color: var(--text-secondary);"><?= htmlspecialchars($property['titulo']) ?></span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Columna principal -->
        <div class="lg:col-span-2">
            <!-- Galería de imágenes -->
            <div class="rounded-lg shadow-lg overflow-hidden mb-6" style="background-color: var(--bg-light); border: 1px solid var(--color-gris-claro);">
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
                            <div class="p-4 border-t" style="border-color: var(--color-gris-claro);">
                                <div class="flex space-x-2 overflow-x-auto">
                                    <?php foreach ($property['imagenes'] as $index => $imagen): ?>
                                        <button onclick="changeMainImage('<?= htmlspecialchars($imagen['ruta']) ?>')" 
                                                class="flex-shrink-0 w-20 h-20 border-2 border-transparent rounded-lg overflow-hidden transition-all duration-200 hover:transform hover:scale-105"
                                                style="border-color: transparent;"
                                                onmouseover="this.style.borderColor='var(--color-azul-marino)'; this.style.boxShadow='0 4px 8px rgba(29, 53, 87, 0.3)';"
                                                onmouseout="this.style.borderColor='transparent'; this.style.boxShadow='none';">
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
                    <div class="h-96 flex items-center justify-center" style="background-color: var(--color-gris-claro);">
                        <div class="text-center">
                            <i class="fas fa-home text-6xl mb-4" style="color: var(--text-secondary);"></i>
                            <p style="color: var(--text-secondary);">No hay imágenes disponibles</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Información de la propiedad -->
            <div class="rounded-lg shadow-lg p-6 mb-6" style="background-color: var(--bg-light); border: 1px solid var(--color-gris-claro);">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h1 class="text-3xl font-bold mb-2" style="color: var(--color-azul-marino);"><?= htmlspecialchars($property['titulo']) ?></h1>
                        <div class="flex items-center mb-2" style="color: var(--text-secondary);">
                            <i class="fas fa-map-marker-alt mr-2" style="color: var(--color-dorado-suave);"></i>
                            <span><?= htmlspecialchars($property['ciudad']) ?>, <?= htmlspecialchars($property['sector']) ?></span>
                        </div>
                        <div class="flex items-center" style="color: var(--text-secondary);">
                            <i class="fas fa-calendar mr-2" style="color: var(--color-verde-esmeralda);"></i>
                            <span>Publicada el <?= date('d/m/Y', strtotime($property['fecha_creacion'])) ?></span>
                        </div>
                    </div>
                    
                    <!-- Precio y Favorito -->
                    <div class="text-right">
                        <div class="text-3xl font-bold" style="color: var(--color-verde-esmeralda);">
                            $<?= number_format($property['precio']) ?>
                        </div>
                        <div class="text-sm" style="color: var(--text-secondary);"><?= $property['moneda'] ?></div>
                        
                        <!-- Botón de favorito -->
                        <?php if (isAuthenticated()): ?>
                        <div class="mt-3">
                            <button class="btn btn-outline-success favorite-toggle px-4 py-2 rounded-md flex items-center" 
                                    data-propiedad-id="<?= $property['id'] ?>"
                                    title="Agregar a favoritos">
                                <i class="far fa-heart mr-2"></i>
                                <span>Agregar a Favoritos</span>
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Badges -->
                <div class="flex flex-wrap gap-2 mb-6">
                    <span class="px-3 py-1 rounded-full text-sm font-medium" style="background-color: rgba(29, 53, 87, 0.1); color: var(--color-azul-marino);">
                                                 <?= getPropertyTypeDisplayName($property['tipo']) ?>
                    </span>
                    <span class="px-3 py-1 rounded-full text-sm font-medium" style="background-color: rgba(233, 196, 106, 0.2); color: var(--color-dorado-suave);">
                        Estado: <?= ucfirst(str_replace('_', ' ', $property['estado_propiedad'])) ?>
                    </span>
                    <?php if ($property['estado_publicacion'] === 'activa'): ?>
                        <span class="px-3 py-1 rounded-full text-sm font-medium" style="background-color: rgba(42, 157, 143, 0.1); color: var(--color-verde-esmeralda);">
                            Disponible
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Descripción -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3" style="color: var(--color-azul-marino);">Descripción</h3>
                    <p class="leading-relaxed" style="color: var(--text-primary);"><?= nl2br(htmlspecialchars($property['descripcion'])) ?></p>
                </div>

                <!-- Características -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="text-center p-4 rounded-lg" style="background-color: rgba(29, 53, 87, 0.05); border: 1px solid var(--color-azul-marino-light);">
                        <i class="fas fa-ruler-combined text-2xl mb-2" style="color: var(--color-azul-marino);"></i>
                        <div class="text-lg font-semibold" style="color: var(--color-azul-marino);"><?= number_format($property['metros_cuadrados']) ?></div>
                        <div class="text-sm" style="color: var(--text-secondary);">m²</div>
                    </div>
                    
                    <?php if ($property['habitaciones'] > 0): ?>
                        <div class="text-center p-4 rounded-lg" style="background-color: rgba(42, 157, 143, 0.05); border: 1px solid var(--color-verde-esmeralda-light);">
                            <i class="fas fa-bed text-2xl mb-2" style="color: var(--color-verde-esmeralda);"></i>
                            <div class="text-lg font-semibold" style="color: var(--color-verde-esmeralda);"><?= $property['habitaciones'] ?></div>
                            <div class="text-sm" style="color: var(--text-secondary);">Habitaciones</div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($property['banos'] > 0): ?>
                        <div class="text-center p-4 rounded-lg" style="background-color: rgba(233, 196, 106, 0.05); border: 1px solid var(--color-dorado-suave-light);">
                            <i class="fas fa-bath text-2xl mb-2" style="color: var(--color-dorado-suave);"></i>
                            <div class="text-lg font-semibold" style="color: var(--color-dorado-suave);"><?= $property['banos'] ?></div>
                            <div class="text-sm" style="color: var(--text-secondary);">Baños</div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($property['estacionamientos'] > 0): ?>
                        <div class="text-center p-4 rounded-lg" style="background-color: rgba(221, 226, 230, 0.3); border: 1px solid var(--color-gris-claro);">
                            <i class="fas fa-car text-2xl mb-2" style="color: var(--color-azul-marino);"></i>
                            <div class="text-lg font-semibold" style="color: var(--color-azul-marino);"><?= $property['estacionamientos'] ?></div>
                            <div class="text-sm" style="color: var(--text-secondary);">Estacionamientos</div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Dirección -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3" style="color: var(--color-azul-marino);">Ubicación</h3>
                    <div class="p-4 rounded-lg" style="background-color: rgba(233, 196, 106, 0.05); border: 1px solid var(--color-dorado-suave-light);">
                        <div class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-3" style="color: var(--color-dorado-suave);"></i>
                            <div>
                                <p class="font-medium" style="color: var(--text-primary);"><?= htmlspecialchars($property['direccion']) ?></p>
                                <p style="color: var(--text-secondary);"><?= htmlspecialchars($property['sector']) ?>, <?= htmlspecialchars($property['ciudad']) ?></p>
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
                <div class="rounded-lg shadow-lg p-6 mb-6" style="background-color: var(--bg-light); border: 1px solid var(--color-gris-claro);">
                    <h3 class="text-lg font-semibold mb-4" style="color: var(--color-azul-marino);">Agente Responsable</h3>
                    
                    <div class="flex items-center mb-4">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center mr-4" style="background: linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%);">
                            <span class="font-bold text-xl" style="color: var(--text-light);">
                                <?= strtoupper(substr($property['agente_nombre'], 0, 1)) ?>
                            </span>
                        </div>
                        <div>
                            <h4 class="font-semibold" style="color: var(--text-primary);">
                                <?= htmlspecialchars($property['agente_nombre'] . ' ' . $property['agente_apellido']) ?>
                            </h4>
                            <p class="text-sm" style="color: var(--text-secondary);">Agente Inmobiliario</p>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <?php if (!empty($property['agente_telefono'])): ?>
                            <a href="/chat/simple?agent=<?= $property['agente_id'] ?>&property=<?= $property['id'] ?>&v=<?= time() ?>" 
                               class="flex items-center transition-all duration-200 hover:transform hover:scale-105"
                               style="color: var(--text-secondary);"
                               onmouseover="this.style.color='var(--color-verde-esmeralda)'"
                               onmouseout="this.style.color='var(--text-secondary)'"
                               title="Contactar al agente por chat">
                                <i class="fas fa-comments mr-3" style="color: var(--color-verde-esmeralda);"></i>
                                <span>Contactar por chat</span>
                            </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($property['agente_email'])): ?>
                            <div class="flex items-center" style="color: var(--text-secondary);">
                                <i class="fas fa-envelope mr-3" style="color: var(--color-azul-marino);"></i>
                                <span><?= htmlspecialchars($property['agente_email']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mt-6">
                        <?php if (isAuthenticated()): ?>
                            <a href="/chat/simple?agent=<?= $property['agente_id'] ?>&property=<?= $property['id'] ?>" 
                               class="btn btn-primary w-full text-center py-3 px-4 rounded-md flex items-center justify-center"
                               title="Contactar a <?= htmlspecialchars($property['agente_nombre']) ?> por chat"
                               onclick="trackContact('chat', '<?= htmlspecialchars($property['agente_nombre']) ?>')">
                                <i class="fas fa-comments mr-2"></i>Contactar
                            </a>
                        <?php else: ?>
                            <a href="/login?redirect=/properties/show/<?= $property['id'] ?>" 
                               class="btn btn-primary w-full text-center py-3 px-4 rounded-md flex items-center justify-center"
                               title="Inicia sesión para contactar al agente">
                                <i class="fas fa-sign-in-alt mr-2"></i>Iniciar Sesión para Contactar
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Botón de Solicitar Compra -->
            <?php if (isAuthenticated() && $property['estado_publicacion'] === 'activa' && $_SESSION['user_rol'] === 'cliente'): ?>
                <div class="rounded-lg shadow-lg p-6 mb-6" style="background-color: var(--bg-light); border: 1px solid var(--color-gris-claro);">
                    <h3 class="text-lg font-semibold mb-4" style="color: var(--color-azul-marino);">¿Te interesa esta propiedad?</h3>
                    <p class="mb-4" style="color: var(--text-secondary);">Envía una solicitud de compra para que el agente se ponga en contacto contigo.</p>
                    
                    <a href="/solicitudes/create/<?= $property['id'] ?>" 
                       class="btn btn-success w-full text-center py-3 px-4 rounded-md flex items-center justify-center">
                        <i class="fas fa-handshake mr-2"></i>Solicitar Compra
                    </a>
                </div>
            <?php elseif (!isAuthenticated() && $property['estado_publicacion'] === 'activa'): ?>
                <div class="rounded-lg shadow-lg p-6 mb-6" style="background-color: var(--bg-light); border: 1px solid var(--color-gris-claro);">
                    <h3 class="text-lg font-semibold mb-4" style="color: var(--color-azul-marino);">¿Te interesa esta propiedad?</h3>
                    <p class="mb-4" style="color: var(--text-secondary);">Inicia sesión para solicitar la compra de esta propiedad.</p>
                    <a href="/login" class="btn btn-primary w-full text-center py-2 px-4 rounded-md">
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
                // Solo permitir acciones a agentes propietarios, no a clientes
                if ($userRole === 'agente' && $property['agente_id'] == $userId) {
                    $canEdit = true;
                }
                ?>
                <?php if ($canEdit): ?>
                    <div class="rounded-lg shadow-lg p-6" style="background-color: var(--bg-light); border: 1px solid var(--color-gris-claro);">
                        <h3 class="text-lg font-semibold mb-4" style="color: var(--color-azul-marino);">Acciones</h3>
                        <div class="space-y-2">
                            <a href="/properties/edit/<?= $property['id'] ?>" 
                               class="btn btn-primary w-full text-center py-2 px-4 rounded-md">
                                <i class="fas fa-edit mr-2"></i>Editar Propiedad
                            </a>
                            <form action="/properties/delete/<?= $property['id'] ?>" method="POST" 
                                  onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta propiedad?')">
                                <button type="submit" class="btn btn-danger w-full py-2 px-4 rounded-md">
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

// Función para tracking de contactos
function trackContact(type, contact) {
    // Mostrar confirmación visual
    const button = event.target.closest('a');
    const originalText = button.innerHTML;
    
    if (type === 'chat') {
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Abriendo Chat...';
        button.style.backgroundColor = 'var(--color-verde-esmeralda)';
        button.style.color = 'white';
        
        // Restaurar después de 1 segundo
        setTimeout(() => {
            button.innerHTML = originalText;
            button.style.backgroundColor = '';
            button.style.color = '';
        }, 1000);
        
        console.log('Chat contact tracked:', contact);
    }
    
    // Aquí podrías enviar datos a Google Analytics o tu sistema de tracking
    if (typeof gtag !== 'undefined') {
        gtag('event', 'contact_click', {
            'contact_type': type,
            'property_id': <?= $property['id'] ?>,
            'property_title': '<?= addslashes($property['titulo']) ?>'
        });
    }
}

// Mejorar la experiencia de usuario para el botón de chat
document.addEventListener('DOMContentLoaded', function() {
    const chatButtons = document.querySelectorAll('a[href*="/chat/"]');
    
    // Agregar indicadores visuales para dispositivos móviles
    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        chatButtons.forEach(button => {
            button.style.fontSize = '1.1rem';
            button.style.padding = '0.75rem 1rem';
        });
    }
    
    // Agregar tooltips informativos
    chatButtons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.title = 'Abrir chat con el agente';
        });
    });
});
</script>

<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?> 