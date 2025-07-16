<?php
// Capturar el contenido para pasarlo al layout
ob_start();
?>

<!-- Hero Section -->
<section class="relative bg-gradient-to-r from-primary-600 to-primary-800 text-white">
    <div class="absolute inset-0 bg-black opacity-20"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">
                Encuentra tu <span class="text-yellow-300">hogar ideal</span>
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-primary-100">
                La plataforma inmobiliaria más confiable.
            </p>
            
            <!-- Búsqueda rápida -->
            <div class="max-w-5xl mx-auto">
                <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-2xl border border-white/20 p-8">
                    <form action="/properties" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="space-y-2">
                            <label for="tipo" class="block text-sm font-semibold text-gray-800 mb-2">
                                <i class="fas fa-home mr-2 text-primary-600"></i>Tipo de Propiedad
                            </label>
                            <select name="tipo" id="tipo" class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 text-gray-700 font-medium shadow-sm hover:border-gray-300">
                                <option value="">Todos los tipos</option>
                                <option value="casa">🏠 Casa</option>
                                <option value="apartamento">🏢 Apartamento</option>
                                <option value="terreno">🌱 Terreno</option>
                                <option value="local_comercial">🏪 Local Comercial</option>
                                <option value="oficina">🏢 Oficina</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label for="ciudad" class="block text-sm font-semibold text-gray-800 mb-2">
                                <i class="fas fa-map-marker-alt mr-2 text-primary-600"></i>Ciudad
                            </label>
                            <input type="text" name="ciudad" id="ciudad" placeholder="Ej: Santo Domingo" 
                                   class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 text-gray-700 font-medium shadow-sm hover:border-gray-300 placeholder-gray-400">
                        </div>
                        <div class="space-y-2">
                            <label for="precio_max" class="block text-sm font-semibold text-gray-800 mb-2">
                                <i class="fas fa-dollar-sign mr-2 text-primary-600"></i>Precio Máximo
                            </label>
                            <input type="number" name="precio_max" id="precio_max" placeholder="Ej: 500,000" min="0" step="1000"
                                   class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 text-gray-700 font-medium shadow-sm hover:border-gray-300 placeholder-gray-400">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white px-6 py-3 rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl font-semibold text-lg">
                                <i class="fas fa-search mr-2"></i> Buscar Propiedades
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Estadísticas -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="text-3xl font-bold text-primary-600 mb-2"><?= number_format($stats['total_propiedades']) ?></div>
                <div class="text-gray-600">Propiedades</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-primary-600 mb-2"><?= number_format($stats['propiedades_activas']) ?></div>
                <div class="text-gray-600">Disponibles</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-primary-600 mb-2"><?= number_format($stats['total_agentes']) ?></div>
                <div class="text-gray-600">Agentes</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-primary-600 mb-2"><?= number_format($stats['total_clientes']) ?></div>
                <div class="text-gray-600">Clientes Satisfechos</div>
            </div>
        </div>
    </div>
</section>

<!-- Propiedades destacadas -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Propiedades Destacadas</h2>
            <?php 
            // Verificar si hay propiedades con favoritos
            $hayFavoritos = false;
            if (!empty($propiedadesDestacadas)) {
                foreach ($propiedadesDestacadas as $propiedad) {
                    if ($propiedad['total_favoritos'] > 0) {
                        $hayFavoritos = true;
                        break;
                    }
                }
            }
            ?>
            <p class="text-xl text-gray-600">
                <?php if ($hayFavoritos): ?>
                    Las mejores opciones seleccionadas para ti
                <?php else: ?>
                    Las propiedades más recientes
                <?php endif; ?>
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if (!empty($propiedadesDestacadas)): ?>
                <?php foreach ($propiedadesDestacadas as $propiedad): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                        <div class="h-48 bg-gray-200 flex items-center justify-center relative">
                            <?php if (!empty($propiedad['imagen_principal'])): ?>
                                <img src="<?= htmlspecialchars($propiedad['imagen_principal']) ?>" 
                                     alt="<?= htmlspecialchars($propiedad['titulo']) ?>" 
                                     class="w-full h-full object-cover">
                            <?php else: ?>
                                <i class="fas fa-home text-gray-400 text-4xl"></i>
                            <?php endif; ?>
                            
                            <!-- Badge de favoritos (solo si hay favoritos) -->
                            <?php if ($propiedad['total_favoritos'] > 0): ?>
                                <div class="absolute top-3 right-3 bg-red-500 text-white px-2 py-1 rounded-full text-xs font-medium flex items-center">
                                    <i class="fas fa-heart mr-1"></i>
                                    <?= $propiedad['total_favoritos'] ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2"><?= htmlspecialchars($propiedad['titulo']) ?></h3>
                            <p class="text-gray-600 mb-2">
                                <?= $propiedad['habitaciones'] ?> habitaciones, 
                                <?= $propiedad['banos'] ?> baños, 
                                <?= $propiedad['metros_cuadrados'] ?>m²
                            </p>
                            <p class="text-gray-500 text-sm mb-4">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                <?= htmlspecialchars($propiedad['ciudad']) ?>, <?= htmlspecialchars($propiedad['sector']) ?>
                            </p>
                            <div class="mb-4">
                                <span class="text-2xl font-bold text-primary-600">
                                    $<?= number_format($propiedad['precio'], 0, ',', '.') ?>
                                </span>
                            </div>
                            <div class="mt-4">
                                <a href="/properties/<?= $propiedad['id'] ?>" 
                                   class="w-full bg-primary-600 hover:bg-primary-700 text-white text-center py-2 px-4 rounded-md transition-colors duration-200 block">
                                    Ver Detalles
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Mensaje cuando no hay propiedades -->
                <div class="col-span-full text-center py-12">
                    <div class="bg-gray-50 rounded-lg p-8">
                        <i class="fas fa-home text-gray-400 text-6xl mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">No hay propiedades disponibles</h3>
                        <p class="text-gray-600 mb-6">Aún no hay propiedades publicadas en la plataforma.</p>
                        <a href="/properties" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-md transition-colors duration-200">
                            Ver Propiedades
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-12">
            <a href="/properties" class="bg-primary-600 hover:bg-primary-700 text-white px-8 py-3 rounded-md text-lg font-medium transition-colors duration-200">
                Ver Mas Propiedades
            </a>
        </div>
    </div>
</section>

<!-- Características principales -->
<section class="py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">¿Por qué elegir <?= APP_NAME ?>?</h2>
            <p class="text-xl text-gray-600">Descubre las ventajas de nuestra plataforma</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Característica 1 -->
            <div class="text-center p-6">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-search text-primary-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Búsqueda Avanzada</h3>
                <p class="text-gray-600">Encuentra propiedades con filtros específicos por precio, ubicación, características y más.</p>
            </div>
            
            <!-- Característica 2 -->
            <div class="text-center p-6">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shield-alt text-primary-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Verificación Segura</h3>
                <p class="text-gray-600">Todas las propiedades son verificadas por nuestros agentes certificados.</p>
            </div>
            
            <!-- Característica 3 -->
            <div class="text-center p-6">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-comments text-primary-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Comunicación Directa</h3>
                <p class="text-gray-600">Chatea directamente con agentes y agenda visitas sin intermediarios.</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonios -->
<section class="py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">
                <?php if (isAuthenticated()): ?>
                    Bienvenido de vuelta a <?= APP_NAME ?>
                <?php else: ?>
                    Lo que dicen nuestros clientes
                <?php endif; ?>
            </h2>
            <p class="text-xl text-gray-600">
                <?php if (isAuthenticated()): ?>
                    Continúa explorando las mejores propiedades para ti
                <?php else: ?>
                    Experiencias reales de usuarios satisfechos
                <?php endif; ?>
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Testimonio 1 -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center mr-4">
                        <span class="text-primary-600 font-bold">M</span>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">María García</h4>
                        <p class="text-gray-600 text-sm">Cliente</p>
                    </div>
                </div>
                <p class="text-gray-600">"Encontré mi casa ideal en solo 2 semanas. El proceso fue muy fácil y el agente fue muy profesional."</p>
                <div class="flex text-yellow-400 mt-4">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
            </div>
            
            <!-- Testimonio 2 -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center mr-4">
                        <span class="text-primary-600 font-bold">J</span>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">Juan Pérez</h4>
                        <p class="text-gray-600 text-sm">Agente</p>
                    </div>
                </div>
                <p class="text-gray-600">"Como agente, esta plataforma me ha ayudado a conectar con más clientes y gestionar mis propiedades eficientemente."</p>
                <div class="flex text-yellow-400 mt-4">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
            </div>
            
            <!-- Testimonio 3 -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center mr-4">
                        <span class="text-primary-600 font-bold">C</span>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">Carlos Rodríguez</h4>
                        <p class="text-gray-600 text-sm">Cliente</p>
                    </div>
                </div>
                <p class="text-gray-600">"Excelente servicio al cliente. El chat integrado me permitió resolver todas mis dudas rápidamente."</p>
                <div class="flex text-yellow-400 mt-4">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section - Solo para usuarios no autenticados -->
<?php if (!isAuthenticated()): ?>
<section class="py-16 bg-primary-600 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold mb-4">¿Listo para encontrar tu hogar ideal?</h2>
        <p class="text-xl mb-8 text-primary-100">Únete a miles de usuarios que ya confían en <?= APP_NAME ?></p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/register" class="bg-white text-primary-600 hover:bg-gray-100 px-8 py-3 rounded-md text-lg font-medium transition-colors duration-200">
                Registrarse Gratis
            </a>
            <a href="/properties" class="border-2 border-white text-white hover:bg-white hover:text-primary-600 px-8 py-3 rounded-md text-lg font-medium transition-colors duration-200">
                Ver Propiedades
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php
// Capturar el contenido y pasarlo al layout
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?> 