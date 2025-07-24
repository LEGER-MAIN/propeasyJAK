<?php
// Capturar el contenido para pasarlo al layout
ob_start();
?>

<!-- Hero Section -->
<section class="relative bg-gradient-to-r from-primary-600 to-primary-800 text-white" style="background: linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%);">
    <div class="absolute inset-0 bg-black opacity-20"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6" style="color: white !important;">
                Encuentra tu <span style="color: var(--color-dorado-suave);">hogar ideal</span>
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-primary-100" style="color: var(--text-light);">
                La plataforma inmobiliaria más confiable.
            </p>
            
            <!-- Búsqueda rápida -->
            <div class="max-w-5xl mx-auto">
                <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 p-8 hero-section">
                    <form action="/properties" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="space-y-2">
                            <label for="tipo" class="block text-sm font-semibold mb-2" style="color: var(--color-azul-marino) !important; font-weight: 700; font-size: 14px;">
                                <i class="fas fa-home mr-2" style="color: var(--color-dorado-suave) !important;"></i>Tipo de Propiedad
                            </label>
                            <select name="tipo" id="tipo" class="w-full px-4 py-3 bg-white border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 text-gray-800 font-medium shadow-sm hover:border-gray-400">
                                <option value="">Todos los tipos</option>
                                <option value="casa">🏠 Casa</option>
                                <option value="apartamento">🏢 Apartamento</option>
                                <option value="terreno">🌱 Terreno</option>
                                <option value="local_comercial">🏪 Local Comercial</option>
                                <option value="oficina">🏢 Oficina</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label for="ciudad" class="block text-sm font-semibold mb-2" style="color: var(--color-azul-marino) !important; font-weight: 700; font-size: 14px;">
                                <i class="fas fa-map-marker-alt mr-2" style="color: var(--color-dorado-suave) !important;"></i>Ciudad
                            </label>
                            <input type="text" name="ciudad" id="ciudad" placeholder="Ej: Santo Domingo" 
                                   class="w-full px-4 py-3 bg-white border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 text-gray-800 font-medium shadow-sm hover:border-gray-400 placeholder-gray-500">
                        </div>
                        <div class="space-y-2">
                            <label for="precio_max" class="block text-sm font-semibold mb-2" style="color: var(--color-azul-marino) !important; font-weight: 700; font-size: 14px;">
                                <i class="fas fa-dollar-sign mr-2" style="color: var(--color-dorado-suave) !important;"></i>Precio Máximo
                            </label>
                            <input type="number" name="precio_max" id="precio_max" placeholder="Ej: 500,000" min="0" step="1000"
                                   class="w-full px-4 py-3 bg-white border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 text-gray-800 font-medium shadow-sm hover:border-gray-400 placeholder-gray-500">
                        </div>
                        <div class="flex items-end space-x-3">
                            <button type="submit" 
                                    class="flex-1 px-6 py-4 rounded-xl font-semibold text-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl"
                                    style="background: linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%); color: white; border: none; min-height: 56px;">
                                <i class="fas fa-search"></i>
                            </button>
                            <button type="button" 
                                    id="limpiarFiltros" 
                                    class="px-6 py-4 rounded-xl font-semibold text-lg transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg"
                                    style="background-color: white; color: var(--text-primary); border: 2px solid var(--color-gris-claro); min-height: 56px;">
                                <i class="fas fa-times mr-2"></i> Limpiar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Estadísticas -->
<section class="py-16 bg-gray-50" style="background-color: var(--bg-primary);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="text-3xl font-bold mb-2" style="color: var(--color-azul-marino) !important;"><?= number_format($stats['total_propiedades']) ?></div>
                <div class="text-gray-600" style="color: var(--text-secondary) !important;">Propiedades</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold mb-2" style="color: var(--color-verde-esmeralda) !important;"><?= number_format($stats['propiedades_activas']) ?></div>
                <div class="text-gray-600" style="color: var(--text-secondary) !important;">Disponibles</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold mb-2" style="color: var(--color-verde-esmeralda) !important;"><?= number_format($stats['total_agentes']) ?></div>
                <div class="text-gray-600" style="color: var(--text-secondary) !important;">Agentes</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold mb-2" style="color: var(--color-dorado-suave) !important;"><?= number_format($stats['total_clientes']) ?></div>
                <div class="text-gray-600" style="color: var(--text-secondary) !important;">Clientes Satisfechos</div>
            </div>
        </div>
    </div>
</section>

<!-- Propiedades destacadas -->
<section class="py-16 bg-gray-50" style="background-color: var(--bg-primary);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4" style="color: var(--color-azul-marino);">Propiedades Destacadas</h2>
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
            <p class="text-xl text-gray-600" style="color: var(--text-secondary);">
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
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300" style="background-color: var(--bg-light); border: 1px solid var(--color-gris-claro);">
                        <div class="h-48 bg-gray-200 flex items-center justify-center relative" style="background-color: var(--color-gris-claro);">
                            <?php if (!empty($propiedad['imagen_principal'])): ?>
                                <img src="<?= htmlspecialchars($propiedad['imagen_principal']) ?>" 
                                     alt="<?= htmlspecialchars($propiedad['titulo']) ?>" 
                                     class="w-full h-full object-cover">
                            <?php else: ?>
                                <i class="fas fa-home text-gray-400 text-4xl" style="color: var(--text-muted);"></i>
                            <?php endif; ?>
                            
                            <!-- Badge de favoritos (solo si hay favoritos) -->
                            <?php if ($propiedad['total_favoritos'] > 0): ?>
                                <div class="absolute top-3 right-3 bg-red-500 text-white px-2 py-1 rounded-full text-xs font-medium flex items-center" style="background-color: var(--color-verde-esmeralda);">
                                    <i class="fas fa-heart mr-1"></i>
                                    <?= $propiedad['total_favoritos'] ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2" style="color: var(--text-primary);"><?= htmlspecialchars($propiedad['titulo']) ?></h3>
                            <p class="text-gray-600 mb-2" style="color: var(--text-secondary);">
                                <?= $propiedad['habitaciones'] ?> habitaciones, 
                                <?= $propiedad['banos'] ?> baños, 
                                <?= $propiedad['metros_cuadrados'] ?>m²
                            </p>
                            <p class="text-gray-500 text-sm mb-4" style="color: var(--text-muted);">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                <?= htmlspecialchars($propiedad['ciudad']) ?>, <?= htmlspecialchars($propiedad['sector']) ?>
                            </p>
                            <div class="mb-4">
                                <span class="text-2xl font-bold text-primary-600" style="color: var(--color-azul-marino);">
                                    $<?= number_format($propiedad['precio'], 0, ',', '.') ?>
                                </span>
                            </div>
                            <div class="mt-4">
                                <a href="/properties/show/<?= $propiedad['id'] ?>" 
                                   class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md font-medium transition-all duration-200 hover:transform hover:scale-105 text-center"
                                   style="background: linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%); color: var(--text-light);"
                                   onmouseover="this.style.background='linear-gradient(135deg, var(--color-azul-marino-hover) 0%, var(--color-azul-marino) 100%)'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(29, 53, 87, 0.3)'"
                                   onmouseout="this.style.background='linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%)'; this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                    <i class="fas fa-eye"></i>
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
                        <p class="text-gray-600">Aún no hay propiedades publicadas en la plataforma.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($propiedadesDestacadas)): ?>
        <div class="text-center mt-12">
            <a href="/properties" class="bg-primary-600 hover:bg-primary-700 text-white px-8 py-3 rounded-md text-lg font-medium transition-colors duration-200" style="background-color: var(--color-azul-marino);">
                Ver Mas Propiedades
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Características principales -->
<section class="py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4" style="color: var(--color-azul-marino);">¿Por qué elegir <?= APP_NAME ?>?</h2>
            <p class="text-xl text-gray-600" style="color: var(--text-secondary);">Descubre las ventajas de nuestra plataforma</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Característica 1 -->
            <div class="text-center p-6">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4" style="background-color: rgba(29, 53, 87, 0.1);">
                    <i class="fas fa-search text-primary-600 text-2xl" style="color: var(--color-azul-marino);"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2" style="color: var(--text-primary);">Búsqueda Avanzada</h3>
                <p class="text-gray-600" style="color: var(--text-secondary);">Encuentra propiedades con filtros específicos por precio, ubicación, características y más.</p>
            </div>
            
            <!-- Característica 2 -->
            <div class="text-center p-6">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4" style="background-color: rgba(42, 157, 143, 0.1);">
                    <i class="fas fa-shield-alt text-primary-600 text-2xl" style="color: var(--color-verde-esmeralda);"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2" style="color: var(--text-primary);">Verificación Segura</h3>
                <p class="text-gray-600" style="color: var(--text-secondary);">Todas las propiedades son verificadas por nuestros agentes certificados.</p>
            </div>
            
            <!-- Característica 3 -->
            <div class="text-center p-6">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4" style="background-color: rgba(233, 196, 106, 0.1);">
                    <i class="fas fa-comments text-primary-600 text-2xl" style="color: var(--color-dorado-suave);"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2" style="color: var(--text-primary);">Comunicación Directa</h3>
                <p class="text-gray-600" style="color: var(--text-secondary);">Chatea directamente con agentes y agenda visitas sin intermediarios.</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonios -->
<section class="py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4" style="color: var(--color-azul-marino);">
                <?php if (isAuthenticated()): ?>
                    Bienvenido de vuelta a <?= APP_NAME ?>
                <?php else: ?>
                    Lo que dicen nuestros clientes
                <?php endif; ?>
            </h2>
            <p class="text-xl text-gray-600" style="color: var(--text-secondary);">
                <?php if (isAuthenticated()): ?>
                    Continúa explorando las mejores propiedades para ti
                <?php else: ?>
                    Experiencias reales de usuarios satisfechos
                <?php endif; ?>
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Testimonio 1 -->
            <div class="bg-white p-6 rounded-lg shadow-md" style="background-color: var(--bg-light); border: 1px solid var(--color-gris-claro);">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center mr-4" style="background-color: rgba(29, 53, 87, 0.1);">
                        <span class="text-primary-600 font-bold" style="color: var(--color-azul-marino);">M</span>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900" style="color: var(--text-primary);">María García</h4>
                        <p class="text-gray-600 text-sm" style="color: var(--text-secondary);">Cliente</p>
                    </div>
                </div>
                <p class="text-gray-600" style="color: var(--text-secondary);">"Encontré mi casa ideal en solo 2 semanas. El proceso fue muy fácil y el agente fue muy profesional."</p>
                <div class="flex text-yellow-400 mt-4" style="color: var(--color-dorado-suave);">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
            </div>
            
            <!-- Testimonio 2 -->
            <div class="bg-white p-6 rounded-lg shadow-md" style="background-color: var(--bg-light); border: 1px solid var(--color-gris-claro);">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center mr-4" style="background-color: rgba(42, 157, 143, 0.1);">
                        <span class="text-primary-600 font-bold" style="color: var(--color-verde-esmeralda);">J</span>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900" style="color: var(--text-primary);">Juan Pérez</h4>
                        <p class="text-gray-600 text-sm" style="color: var(--text-secondary);">Agente</p>
                    </div>
                </div>
                <p class="text-gray-600" style="color: var(--text-secondary);">"Como agente, esta plataforma me ha ayudado a conectar con más clientes y gestionar mis propiedades eficientemente."</p>
                <div class="flex text-yellow-400 mt-4" style="color: var(--color-dorado-suave);">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
            </div>
            
            <!-- Testimonio 3 -->
            <div class="bg-white p-6 rounded-lg shadow-md" style="background-color: var(--bg-light); border: 1px solid var(--color-gris-claro);">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center mr-4" style="background-color: rgba(233, 196, 106, 0.1);">
                        <span class="text-primary-600 font-bold" style="color: var(--color-dorado-suave);">C</span>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900" style="color: var(--text-primary);">Carlos Rodríguez</h4>
                        <p class="text-gray-600 text-sm" style="color: var(--text-secondary);">Cliente</p>
                    </div>
                </div>
                <p class="text-gray-600" style="color: var(--text-secondary);">"Excelente servicio al cliente. El chat integrado me permitió resolver todas mis dudas rápidamente."</p>
                <div class="flex text-yellow-400 mt-4" style="color: var(--color-dorado-suave);">
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
<section class="py-16 bg-primary-600 text-white" style="background: linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold mb-4">¿Listo para encontrar tu hogar ideal?</h2>
        <p class="text-xl mb-8 text-primary-100" style="color: var(--text-light);">Únete a miles de usuarios que ya confían en <?= APP_NAME ?></p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/register" class="bg-white text-primary-600 hover:bg-gray-100 px-8 py-3 rounded-md text-lg font-medium transition-colors duration-200" style="background-color: var(--bg-light); color: var(--color-azul-marino);">
                Registrarse Gratis
            </a>
            <a href="/properties" class="border-2 border-white text-white hover:bg-white hover:text-primary-600 px-8 py-3 rounded-md text-lg font-medium transition-colors duration-200" style="border-color: var(--text-light); color: var(--text-light);">
                Ver Propiedades
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Botón de limpiar filtros
    const limpiarFiltrosBtn = document.getElementById('limpiarFiltros');
    if (limpiarFiltrosBtn) {
        limpiarFiltrosBtn.addEventListener('click', function() {
            // Limpiar todos los campos del formulario
            document.getElementById('tipo').value = '';
            document.getElementById('ciudad').value = '';
            document.getElementById('precio_max').value = '';
            
            // Efecto visual de confirmación mejorado
            const originalText = this.innerHTML;
            const originalBg = this.style.background;
            const originalColor = this.style.color;
            const originalBorder = this.style.border;
            
            this.innerHTML = '<i class="fas fa-check mr-2"></i>¡Limpiado!';
            this.style.background = 'var(--color-verde-esmeralda)';
            this.style.color = 'white';
            this.style.border = '2px solid var(--color-verde-esmeralda)';
            this.style.transform = 'scale(0.95)';
            
            setTimeout(() => {
                this.innerHTML = originalText;
                this.style.background = originalBg;
                this.style.color = originalColor;
                this.style.border = originalBorder;
                this.style.transform = 'scale(1)';
            }, 1500);
            
            // Enfocar el primer campo después de limpiar
            setTimeout(() => {
                document.getElementById('tipo').focus();
            }, 1600);
        });
    }
    
    // Mejorar la experiencia del formulario de búsqueda
    const searchForm = document.querySelector('form[action="/properties"]');
    if (searchForm) {
        const submitBtn = searchForm.querySelector('button[type="submit"]');
        
        // Efecto de carga al enviar el formulario
        searchForm.addEventListener('submit', function() {
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Buscando...';
            submitBtn.disabled = true;
            
            // Restaurar después de un tiempo (en caso de que no se envíe)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 3000);
        });
        
        // Validación en tiempo real
        const inputs = searchForm.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                const hasValues = Array.from(inputs).some(input => input.value.trim() !== '');
                submitBtn.style.opacity = hasValues ? '1' : '0.8';
            });
        });
    }
});
</script>

<?php
// Capturar el contenido y pasarlo al layout
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?> 
