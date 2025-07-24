<?php
/**
 * Vista: Página Acerca de
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 */

// Capturar el contenido para pasarlo al layout
ob_start();
?>

<!-- Hero Section -->
<section class="bg-gradient-to-r from-primary-800 to-primary-900 text-white py-20" style="background: linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-6">Acerca de <?= APP_NAME ?></h1>
        <p class="text-xl text-primary-100 max-w-3xl mx-auto" style="color: var(--text-light);">
            Conectando sueños inmobiliarios con realidades desde República Dominicana
        </p>
    </div>
</section>

<!-- Misión y Visión -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Misión -->
            <div class="text-center lg:text-left">
                <div class="w-16 h-16 bg-primary-600 rounded-full flex items-center justify-center mx-auto lg:mx-0 mb-6" style="background-color: var(--color-azul-marino);">
                    <i class="fas fa-bullseye text-white text-2xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Nuestra Misión</h2>
                <p class="text-lg text-gray-600 leading-relaxed">
                    Facilitar el proceso de compra, venta y alquiler de propiedades en República Dominicana, 
                    proporcionando una plataforma confiable y eficiente que conecte a clientes y agentes inmobiliarios 
                    de manera transparente y segura.
                </p>
            </div>
            
            <!-- Visión -->
            <div class="text-center lg:text-left">
                <div class="w-16 h-16 bg-primary-600 rounded-full flex items-center justify-center mx-auto lg:mx-0 mb-6" style="background-color: var(--color-azul-marino);">
                    <i class="fas fa-eye text-white text-2xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Nuestra Visión</h2>
                <p class="text-lg text-gray-600 leading-relaxed">
                    Ser la plataforma líder en el mercado inmobiliario dominicano, reconocida por la innovación, 
                    transparencia y excelencia en el servicio, contribuyendo al desarrollo del sector inmobiliario 
                    del país.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Valores -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Nuestros Valores</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Los principios que guían nuestro trabajo y relación con clientes y agentes
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Transparencia -->
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4" style="background-color: var(--color-verde-esmeralda);">
                    <i class="fas fa-shield-alt text-white"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Transparencia</h3>
                <p class="text-gray-600">Información clara y honesta en todas nuestras transacciones</p>
            </div>
            
            <!-- Confianza -->
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4" style="background-color: var(--color-azul-marino);">
                    <i class="fas fa-handshake text-white"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Confianza</h3>
                <p class="text-gray-600">Construimos relaciones duraderas basadas en la confianza mutua</p>
            </div>
            
            <!-- Innovación -->
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center mx-auto mb-4" style="background-color: var(--color-dorado-suave);">
                    <i class="fas fa-lightbulb text-white"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Innovación</h3>
                <p class="text-gray-600">Siempre buscamos nuevas formas de mejorar nuestros servicios</p>
            </div>
            
            <!-- Excelencia -->
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-star text-white"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Excelencia</h3>
                <p class="text-gray-600">Comprometidos con la calidad en cada detalle de nuestro servicio</p>
            </div>
        </div>
    </div>
</section>

<!-- Historia -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Nuestra Historia</h2>
                <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                    <?= APP_NAME ?> nació de la visión de revolucionar el mercado inmobiliario dominicano. 
                    Fundada en 2024, nuestra plataforma surgió de la necesidad de crear un espacio digital 
                    donde clientes y agentes pudieran conectarse de manera eficiente y transparente.
                </p>
                <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                    Desde nuestros inicios, nos hemos comprometido con la innovación tecnológica y la 
                    excelencia en el servicio, estableciendo nuevos estándares en la industria inmobiliaria 
                    del país.
                </p>
                <p class="text-lg text-gray-600 leading-relaxed">
                    Hoy, somos orgullosos de servir a miles de usuarios que confían en nuestra plataforma 
                    para encontrar su hogar ideal o gestionar sus propiedades.
                </p>
            </div>
            <div class="relative">
                <div class="bg-primary-600 rounded-lg p-8 text-white text-center" style="background-color: var(--color-azul-marino);">
                    <i class="fas fa-chart-line text-6xl mb-4"></i>
                    <h3 class="text-2xl font-bold mb-2">Crecimiento Constante</h3>
                    <p class="text-primary-100" style="color: var(--text-light);">
                        Más de 1000 propiedades listadas y cientos de clientes satisfechos
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Equipo -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Nuestro Equipo</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Profesionales apasionados por la tecnología y el sector inmobiliario
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- CEO -->
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <div class="w-24 h-24 bg-gray-300 rounded-full mx-auto mb-4 flex items-center justify-center">
                    <i class="fas fa-user-tie text-3xl text-gray-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-1">Alejandro Santos Estrella</h3>
                <p class="text-primary-600 mb-3" style="color: var(--color-azul-marino);">CEO & Fundador</p>
                <p class="text-gray-600 text-sm">
                    Líder visionario con más de 10 años de experiencia en tecnología y desarrollo de software
                </p>
            </div>
            
            <!-- CTO -->
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <div class="w-24 h-24 bg-gray-300 rounded-full mx-auto mb-4 flex items-center justify-center">
                    <i class="fas fa-code text-3xl text-gray-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-1">Jefferson Leger</h3>
                <p class="text-primary-600 mb-3" style="color: var(--color-azul-marino);">CTO & Desarrollador</p>
                <p class="text-gray-600 text-sm">
                    Experto en desarrollo web y arquitectura de software con pasión por la innovación
                </p>
            </div>
            
            <!-- Marketing -->
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <div class="w-24 h-24 bg-gray-300 rounded-full mx-auto mb-4 flex items-center justify-center">
                    <i class="fas fa-bullhorn text-3xl text-gray-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-1">Kelvin Encarnacion</h3>
                <p class="text-primary-600 mb-3" style="color: var(--color-azul-marino);">Marketing Digital</p>
                <p class="text-gray-600 text-sm">
                    Especialistas en estrategias digitales y crecimiento de marca
                </p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-primary-600 text-white" style="background: linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold mb-4">¿Listo para unirte a <?= APP_NAME ?>?</h2>
        <p class="text-xl mb-8 text-primary-100" style="color: var(--text-light);">
            Descubre cómo podemos ayudarte a encontrar tu hogar ideal o gestionar tus propiedades
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/register" class="bg-white text-primary-600 hover:bg-gray-100 px-8 py-3 rounded-md text-lg font-medium transition-colors duration-200" style="background-color: var(--bg-light); color: var(--color-azul-marino);">
                Registrarse Gratis
            </a>
            <a href="/properties" class="border-2 border-white text-white hover:bg-white hover:text-primary-600 px-8 py-3 rounded-md text-lg font-medium transition-colors duration-200">
                Ver Propiedades
            </a>
        </div>
    </div>
</section>

<?php
// Obtener el contenido capturado y pasarlo al layout
$content = ob_get_clean();

// Incluir el layout principal
include APP_PATH . '/views/layouts/main.php';
?>
