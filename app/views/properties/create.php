<?php
/**
 * Vista: Crear Propiedad
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Esta vista muestra el formulario para crear una nueva propiedad
 */

// Incluir el layout principal
$content = ob_start();
?>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2 md:space-x-4">
            <li class="inline-flex items-center">
                <a href="/" class="text-gray-700 hover:text-blue-600 font-medium">
                    <i class="fas fa-home mr-2"></i>Inicio
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <a href="/properties" class="text-gray-700 hover:text-blue-600 font-medium">Propiedades</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-gray-500 font-medium">Publicar Propiedad</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="text-center mb-8">
        <div class="mx-auto h-16 w-16 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg">
            <i class="fas fa-plus text-white text-2xl"></i>
        </div>
        <h1 class="text-4xl font-bold text-gray-900 mb-3">Publicar Nueva Propiedad</h1>
        <p class="text-lg text-gray-700">Completa el formulario para publicar tu propiedad en nuestra plataforma</p>
    </div>

    <!-- Formulario -->
    <div class="bg-white rounded-2xl shadow-2xl p-8 border border-gray-100">
        <form action="/properties" method="POST" enctype="multipart/form-data" class="space-y-8">
            <!-- Información básica -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-xl border border-blue-100">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                    Información Básica
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Título -->
                    <div class="md:col-span-2">
                        <label for="titulo" class="block text-base font-semibold text-gray-800 mb-2">
                            <i class="fas fa-tag text-blue-600 mr-2"></i>
                            Título de la Propiedad <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="titulo" id="titulo" required
                               placeholder="Ej: Hermosa casa en Bella Vista"
                               class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 placeholder-gray-400">
                    </div>
                    
                    <!-- Tipo de propiedad -->
                    <div>
                        <label for="tipo" class="block text-base font-semibold text-gray-800 mb-2">
                            <i class="fas fa-home text-blue-600 mr-2"></i>
                            Tipo de Propiedad <span class="text-red-500">*</span>
                        </label>
                        <select name="tipo" id="tipo" required
                                class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 appearance-none bg-white">
                            <option value="">Selecciona un tipo</option>
                            <option value="casa">🏠 Casa</option>
                            <option value="apartamento">🏢 Apartamento</option>
                            <option value="terreno">🌱 Terreno</option>
                            <option value="local_comercial">🏪 Local Comercial</option>
                            <option value="oficina">🏢 Oficina</option>
                        </select>
                    </div>
                    
                    <!-- Estado de la propiedad -->
                    <div>
                        <label for="estado_propiedad" class="block text-base font-semibold text-gray-800 mb-2">
                            <i class="fas fa-star text-blue-600 mr-2"></i>
                            Estado de la Propiedad
                        </label>
                        <select name="estado_propiedad" id="estado_propiedad"
                                class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 appearance-none bg-white">
                            <option value="bueno">✅ Bueno</option>
                            <option value="excelente">⭐ Excelente</option>
                            <option value="regular">⚠️ Regular</option>
                            <option value="necesita_reparacion">🔧 Necesita Reparación</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Precio y moneda -->
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-6 rounded-xl border border-green-100">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-dollar-sign text-green-600 mr-3"></i>
                    Precio
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Precio -->
                    <div>
                        <label for="precio" class="block text-base font-semibold text-gray-800 mb-2">
                            <i class="fas fa-money-bill-wave text-green-600 mr-2"></i>
                            Precio <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="precio" id="precio" required min="0" step="1000"
                               placeholder="0"
                               class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 placeholder-gray-400">
                    </div>
                    
                    <!-- Moneda -->
                    <div>
                        <label for="moneda" class="block text-base font-semibold text-gray-800 mb-2">
                            <i class="fas fa-coins text-green-600 mr-2"></i>
                            Moneda
                        </label>
                        <select name="moneda" id="moneda"
                                class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 appearance-none bg-white">
                            <option value="USD">💵 USD - Dólar Estadounidense</option>
                            <option value="DOP">💲 DOP - Peso Dominicano</option>
                            <option value="EUR">💶 EUR - Euro</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Ubicación -->
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 p-6 rounded-xl border border-purple-100">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-map-marker-alt text-purple-600 mr-3"></i>
                    Ubicación
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Ciudad -->
                    <div>
                        <label for="ciudad" class="block text-base font-semibold text-gray-800 mb-2">
                            <i class="fas fa-city text-purple-600 mr-2"></i>
                            Ciudad <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="ciudad" id="ciudad" required
                               placeholder="Ej: Santo Domingo"
                               class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 placeholder-gray-400">
                    </div>
                    
                    <!-- Sector -->
                    <div>
                        <label for="sector" class="block text-base font-semibold text-gray-800 mb-2">
                            <i class="fas fa-map text-purple-600 mr-2"></i>
                            Sector <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="sector" id="sector" required
                               placeholder="Ej: Bella Vista"
                               class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 placeholder-gray-400">
                    </div>
                    
                    <!-- Dirección -->
                    <div class="md:col-span-2">
                        <label for="direccion" class="block text-base font-semibold text-gray-800 mb-2">
                            <i class="fas fa-map-pin text-purple-600 mr-2"></i>
                            Dirección Completa <span class="text-red-500">*</span>
                        </label>
                        <textarea name="direccion" id="direccion" rows="3" required
                                  placeholder="Ej: Calle Principal #123, entre Calle A y Calle B"
                                  class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 placeholder-gray-400 resize-none"></textarea>
                    </div>
                </div>
            </div>

            <!-- Características -->
            <div class="bg-gradient-to-r from-orange-50 to-yellow-50 p-6 rounded-xl border border-orange-100">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-ruler-combined text-orange-600 mr-3"></i>
                    Características
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <!-- Metros cuadrados -->
                    <div>
                        <label for="metros_cuadrados" class="block text-base font-semibold text-gray-800 mb-2">
                            <i class="fas fa-vector-square text-orange-600 mr-2"></i>
                            Metros Cuadrados <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="metros_cuadrados" id="metros_cuadrados" required min="0" step="0.01"
                               placeholder="0"
                               class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 placeholder-gray-400">
                    </div>
                    
                    <!-- Habitaciones -->
                    <div>
                        <label for="habitaciones" class="block text-base font-semibold text-gray-800 mb-2">
                            <i class="fas fa-bed text-orange-600 mr-2"></i>
                            Habitaciones <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="habitaciones" id="habitaciones" required min="0"
                               placeholder="0"
                               class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 placeholder-gray-400">
                    </div>
                    
                    <!-- Baños -->
                    <div>
                        <label for="banos" class="block text-base font-semibold text-gray-800 mb-2">
                            <i class="fas fa-bath text-orange-600 mr-2"></i>
                            Baños <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="banos" id="banos" required min="0"
                               placeholder="0"
                               class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 placeholder-gray-400">
                    </div>
                    
                    <!-- Estacionamientos -->
                    <div>
                        <label for="estacionamientos" class="block text-base font-semibold text-gray-800 mb-2">
                            <i class="fas fa-car text-orange-600 mr-2"></i>
                            Estacionamientos
                        </label>
                        <input type="number" name="estacionamientos" id="estacionamientos" min="0"
                               placeholder="0"
                               class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 placeholder-gray-400">
                    </div>
                </div>
            </div>

            <!-- Descripción -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Descripción</h3>
                
                <div>
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">
                        Descripción Detallada <span class="text-red-500">*</span>
                    </label>
                    <textarea name="descripcion" id="descripcion" rows="6" required
                              placeholder="Describe tu propiedad en detalle. Incluye características especiales, amenidades cercanas, estado de la propiedad, etc."
                              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500"></textarea>
                    <p class="mt-1 text-sm text-gray-500">
                        Proporciona una descripción detallada para atraer más compradores interesados.
                    </p>
                </div>
            </div>

            <!-- Imágenes -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Imágenes</h3>
                
                <div>
                    <label for="imagenes" class="block text-sm font-medium text-gray-700 mb-1">
                        Subir Imágenes
                    </label>
                    <input type="file" name="imagenes[]" id="imagenes" multiple accept="image/*,.webp"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                    <p class="mt-1 text-sm text-gray-500">
                        Puedes subir múltiples imágenes. La primera imagen será la imagen principal.
                        Formatos permitidos: JPG, PNG, GIF, WebP. Tamaño máximo: 5MB por imagen.
                    </p>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Información Importante</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Tu propiedad será revisada por un agente antes de ser publicada.</li>
                                <li>Recibirás un token de validación que debes compartir con el agente.</li>
                                <li>Una vez validada, tu propiedad aparecerá en el listado público.</li>
                                <li>Puedes editar o eliminar tu propiedad en cualquier momento.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="/properties" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-md font-medium transition-colors">
                    Cancelar
                </a>
                <button type="submit" 
                        class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-2 rounded-md font-medium transition-colors">
                    <i class="fas fa-save mr-2"></i>Publicar Propiedad
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Script para validación del formulario -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    
    form.addEventListener('submit', function(e) {
        const titulo = document.getElementById('titulo').value.trim();
        const tipo = document.getElementById('tipo').value;
        const precio = document.getElementById('precio').value;
        const ciudad = document.getElementById('ciudad').value.trim();
        const sector = document.getElementById('sector').value.trim();
        const direccion = document.getElementById('direccion').value.trim();
        const metrosCuadrados = document.getElementById('metros_cuadrados').value;
        const habitaciones = document.getElementById('habitaciones').value;
        const banos = document.getElementById('banos').value;
        const descripcion = document.getElementById('descripcion').value.trim();
        
        let isValid = true;
        let errorMessage = '';
        
        // Validar campos obligatorios
        if (!titulo) {
            errorMessage += 'El título es obligatorio.\n';
            isValid = false;
        }
        
        if (!tipo) {
            errorMessage += 'Debes seleccionar un tipo de propiedad.\n';
            isValid = false;
        }
        
        if (!precio || precio <= 0) {
            errorMessage += 'El precio debe ser mayor a 0.\n';
            isValid = false;
        }
        
        if (!ciudad) {
            errorMessage += 'La ciudad es obligatoria.\n';
            isValid = false;
        }
        
        if (!sector) {
            errorMessage += 'El sector es obligatorio.\n';
            isValid = false;
        }
        
        if (!direccion) {
            errorMessage += 'La dirección es obligatoria.\n';
            isValid = false;
        }
        
        if (!metrosCuadrados || metrosCuadrados <= 0) {
            errorMessage += 'Los metros cuadrados deben ser mayor a 0.\n';
            isValid = false;
        }
        
        if (habitaciones < 0) {
            errorMessage += 'El número de habitaciones no puede ser negativo.\n';
            isValid = false;
        }
        
        if (banos < 0) {
            errorMessage += 'El número de baños no puede ser negativo.\n';
            isValid = false;
        }
        
        if (!descripcion) {
            errorMessage += 'La descripción es obligatoria.\n';
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            alert('Por favor corrige los siguientes errores:\n\n' + errorMessage);
        }
    });
});
</script>

<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?> 