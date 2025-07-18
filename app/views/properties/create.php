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
                    <span class="font-medium" style="color: var(--text-secondary);">Publicar Propiedad</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="text-center mb-8">
        <div class="mx-auto h-16 w-16 rounded-2xl flex items-center justify-center mb-4 shadow-lg" style="background: linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%);">
            <i class="fas fa-plus text-white text-2xl"></i>
        </div>
        <h1 class="text-4xl font-bold mb-3" style="color: var(--color-azul-marino);">Publicar Nueva Propiedad</h1>
        <p class="text-lg" style="color: var(--text-secondary);">Completa el formulario para publicar tu propiedad en nuestra plataforma</p>
    </div>

    <!-- Formulario -->
    <div class="rounded-2xl shadow-2xl p-8 border" style="background-color: var(--bg-light); border-color: var(--color-gris-claro);">
        <form action="/properties" method="POST" enctype="multipart/form-data" class="space-y-8">
            <!-- Información básica -->
            <div class="p-6 rounded-xl border" style="background: linear-gradient(135deg, rgba(29, 53, 87, 0.05) 0%, rgba(29, 53, 87, 0.1) 100%); border-color: var(--color-azul-marino-light);">
                <h3 class="text-xl font-bold mb-6 flex items-center" style="color: var(--color-azul-marino);">
                    <i class="fas fa-info-circle mr-3" style="color: var(--color-azul-marino);"></i>
                    Información Básica
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Título -->
                    <div class="md:col-span-2">
                        <label for="titulo" class="block text-base font-semibold mb-2" style="color: var(--text-primary);">
                            <i class="fas fa-tag mr-2" style="color: var(--color-azul-marino);"></i>
                            Título de la Propiedad <span style="color: var(--danger);">*</span>
                        </label>
                        <input type="text" name="titulo" id="titulo" required
                               placeholder="Ej: Hermosa casa en Bella Vista"
                               class="w-full px-4 py-3 text-lg border-2 rounded-xl focus:outline-none transition-all duration-200"
                               style="border-color: var(--color-gris-claro); color: var(--text-primary); placeholder-color: var(--text-muted);">
                    </div>
                    
                    <!-- Tipo de propiedad -->
                    <div>
                        <label for="tipo" class="block text-base font-semibold mb-2" style="color: var(--text-primary);">
                            <i class="fas fa-home mr-2" style="color: var(--color-azul-marino);"></i>
                            Tipo de Propiedad <span style="color: var(--danger);">*</span>
                        </label>
                        <select name="tipo" id="tipo" required
                                class="w-full px-4 py-3 text-lg border-2 rounded-xl focus:outline-none transition-all duration-200 appearance-none"
                                style="border-color: var(--color-gris-claro); background-color: var(--bg-light); color: var(--text-primary);">
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
                        <label for="estado_propiedad" class="block text-base font-semibold mb-2" style="color: var(--text-primary);">
                            <i class="fas fa-star mr-2" style="color: var(--color-dorado-suave);"></i>
                            Estado de la Propiedad
                        </label>
                        <select name="estado_propiedad" id="estado_propiedad"
                                class="w-full px-4 py-3 text-lg border-2 rounded-xl focus:outline-none transition-all duration-200 appearance-none"
                                style="border-color: var(--color-gris-claro); background-color: var(--bg-light); color: var(--text-primary);">
                            <option value="bueno">✅ Bueno</option>
                            <option value="excelente">⭐ Excelente</option>
                            <option value="regular">⚠️ Regular</option>
                            <option value="necesita_reparacion">🔧 Necesita Reparación</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Precio y moneda -->
            <div class="p-6 rounded-xl border" style="background: linear-gradient(135deg, rgba(42, 157, 143, 0.05) 0%, rgba(42, 157, 143, 0.1) 100%); border-color: var(--color-verde-esmeralda-light);">
                <h3 class="text-xl font-bold mb-6 flex items-center" style="color: var(--color-verde-esmeralda);">
                    <i class="fas fa-dollar-sign mr-3" style="color: var(--color-verde-esmeralda);"></i>
                    Precio
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Precio -->
                    <div>
                        <label for="precio" class="block text-base font-semibold mb-2" style="color: var(--text-primary);">
                            <i class="fas fa-money-bill-wave mr-2" style="color: var(--color-verde-esmeralda);"></i>
                            Precio <span style="color: var(--danger);">*</span>
                        </label>
                        <input type="number" name="precio" id="precio" required min="0" step="1000"
                               placeholder="0"
                               class="w-full px-4 py-3 text-lg border-2 rounded-xl focus:outline-none transition-all duration-200"
                               style="border-color: var(--color-gris-claro); color: var(--text-primary); placeholder-color: var(--text-muted);">
                    </div>
                    
                    <!-- Moneda -->
                    <div>
                        <label for="moneda" class="block text-base font-semibold mb-2" style="color: var(--text-primary);">
                            <i class="fas fa-coins mr-2" style="color: var(--color-verde-esmeralda);"></i>
                            Moneda
                        </label>
                        <select name="moneda" id="moneda"
                                class="w-full px-4 py-3 text-lg border-2 rounded-xl focus:outline-none transition-all duration-200 appearance-none"
                                style="border-color: var(--color-gris-claro); background-color: var(--bg-light); color: var(--text-primary);">
                            <option value="USD">💵 USD - Dólar Estadounidense</option>
                            <option value="DOP">💲 DOP - Peso Dominicano</option>
                            <option value="EUR">💶 EUR - Euro</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Ubicación -->
            <div class="p-6 rounded-xl border" style="background: linear-gradient(135deg, rgba(233, 196, 106, 0.05) 0%, rgba(233, 196, 106, 0.1) 100%); border-color: var(--color-dorado-suave-light);">
                <h3 class="text-xl font-bold mb-6 flex items-center" style="color: var(--color-dorado-suave);">
                    <i class="fas fa-map-marker-alt mr-3" style="color: var(--color-dorado-suave);"></i>
                    Ubicación
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Ciudad -->
                    <div>
                        <label for="ciudad" class="block text-base font-semibold mb-2" style="color: var(--text-primary);">
                            <i class="fas fa-city mr-2" style="color: var(--color-dorado-suave);"></i>
                            Ciudad <span style="color: var(--danger);">*</span>
                        </label>
                        <input type="text" name="ciudad" id="ciudad" required
                               placeholder="Ej: Santo Domingo"
                               class="w-full px-4 py-3 text-lg border-2 rounded-xl focus:outline-none transition-all duration-200"
                               style="border-color: var(--color-gris-claro); color: var(--text-primary); placeholder-color: var(--text-muted);">
                    </div>
                    
                    <!-- Sector -->
                    <div>
                        <label for="sector" class="block text-base font-semibold mb-2" style="color: var(--text-primary);">
                            <i class="fas fa-map mr-2" style="color: var(--color-dorado-suave);"></i>
                            Sector <span style="color: var(--danger);">*</span>
                        </label>
                        <input type="text" name="sector" id="sector" required
                               placeholder="Ej: Bella Vista"
                               class="w-full px-4 py-3 text-lg border-2 rounded-xl focus:outline-none transition-all duration-200"
                               style="border-color: var(--color-gris-claro); color: var(--text-primary); placeholder-color: var(--text-muted);">
                    </div>
                    
                    <!-- Dirección -->
                    <div class="md:col-span-2">
                        <label for="direccion" class="block text-base font-semibold mb-2" style="color: var(--text-primary);">
                            <i class="fas fa-map-pin mr-2" style="color: var(--color-dorado-suave);"></i>
                            Dirección Completa <span style="color: var(--danger);">*</span>
                        </label>
                        <textarea name="direccion" id="direccion" rows="3" required
                                  placeholder="Ej: Calle Principal #123, entre Calle A y Calle B"
                                  class="w-full px-4 py-3 text-lg border-2 rounded-xl focus:outline-none transition-all duration-200 resize-none"
                                  style="border-color: var(--color-gris-claro); color: var(--text-primary); placeholder-color: var(--text-muted);"></textarea>
                    </div>
                </div>
            </div>

            <!-- Características -->
            <div class="p-6 rounded-xl border" style="background: linear-gradient(135deg, rgba(221, 226, 230, 0.3) 0%, rgba(221, 226, 230, 0.5) 100%); border-color: var(--color-gris-claro);">
                <h3 class="text-xl font-bold mb-6 flex items-center" style="color: var(--color-azul-marino);">
                    <i class="fas fa-ruler-combined mr-3" style="color: var(--color-azul-marino);"></i>
                    Características
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <!-- Metros cuadrados -->
                    <div>
                        <label for="metros_cuadrados" class="block text-base font-semibold mb-2" style="color: var(--text-primary);">
                            <i class="fas fa-vector-square mr-2" style="color: var(--color-azul-marino);"></i>
                            Metros Cuadrados <span style="color: var(--danger);">*</span>
                        </label>
                        <input type="number" name="metros_cuadrados" id="metros_cuadrados" required min="0" step="0.01"
                               placeholder="0"
                               class="w-full px-4 py-3 text-lg border-2 rounded-xl focus:outline-none transition-all duration-200"
                               style="border-color: var(--color-gris-claro); color: var(--text-primary); placeholder-color: var(--text-muted);">
                    </div>
                    
                    <!-- Habitaciones -->
                    <div>
                        <label for="habitaciones" class="block text-base font-semibold mb-2" style="color: var(--text-primary);">
                            <i class="fas fa-bed mr-2" style="color: var(--color-azul-marino);"></i>
                            Habitaciones <span style="color: var(--danger);">*</span>
                        </label>
                        <input type="number" name="habitaciones" id="habitaciones" required min="0"
                               placeholder="0"
                               class="w-full px-4 py-3 text-lg border-2 rounded-xl focus:outline-none transition-all duration-200"
                               style="border-color: var(--color-gris-claro); color: var(--text-primary); placeholder-color: var(--text-muted);">
                    </div>
                    
                    <!-- Baños -->
                    <div>
                        <label for="banos" class="block text-base font-semibold mb-2" style="color: var(--text-primary);">
                            <i class="fas fa-bath mr-2" style="color: var(--color-azul-marino);"></i>
                            Baños <span style="color: var(--danger);">*</span>
                        </label>
                        <input type="number" name="banos" id="banos" required min="0"
                               placeholder="0"
                               class="w-full px-4 py-3 text-lg border-2 rounded-xl focus:outline-none transition-all duration-200"
                               style="border-color: var(--color-gris-claro); color: var(--text-primary); placeholder-color: var(--text-muted);">
                    </div>
                    
                    <!-- Estacionamientos -->
                    <div>
                        <label for="estacionamientos" class="block text-base font-semibold mb-2" style="color: var(--text-primary);">
                            <i class="fas fa-car mr-2" style="color: var(--color-azul-marino);"></i>
                            Estacionamientos
                        </label>
                        <input type="number" name="estacionamientos" id="estacionamientos" min="0"
                               placeholder="0"
                               class="w-full px-4 py-3 text-lg border-2 rounded-xl focus:outline-none transition-all duration-200"
                               style="border-color: var(--color-gris-claro); color: var(--text-primary); placeholder-color: var(--text-muted);">
                    </div>
                </div>
            </div>

            <!-- Descripción -->
            <div>
                <h3 class="text-lg font-semibold mb-4" style="color: var(--color-azul-marino);">Descripción</h3>
                
                <div>
                    <label for="descripcion" class="block text-sm font-medium mb-1" style="color: var(--text-primary);">
                        Descripción Detallada <span style="color: var(--danger);">*</span>
                    </label>
                    <textarea name="descripcion" id="descripcion" rows="6" required
                              placeholder="Describe tu propiedad en detalle. Incluye características especiales, amenidades cercanas, estado de la propiedad, etc."
                              class="w-full rounded-md shadow-sm"
                              style="border-color: var(--color-gris-claro); color: var(--text-primary); placeholder-color: var(--text-muted);"></textarea>
                    <p class="mt-1 text-sm" style="color: var(--text-secondary);">
                        Proporciona una descripción detallada para atraer más compradores interesados.
                    </p>
                </div>
            </div>

            <!-- Imágenes -->
            <div>
                <h3 class="text-lg font-semibold mb-4" style="color: var(--color-azul-marino);">Imágenes</h3>
                
                <div>
                    <label for="imagenes" class="block text-sm font-medium mb-1" style="color: var(--text-primary);">
                        Subir Imágenes
                    </label>
                    <input type="file" name="imagenes[]" id="imagenes" multiple accept="image/*,.webp"
                           class="w-full rounded-md shadow-sm"
                           style="border-color: var(--color-gris-claro); color: var(--text-primary);">
                    <p class="mt-1 text-sm" style="color: var(--text-secondary);">
                        Puedes subir múltiples imágenes. La primera imagen será la imagen principal.
                        Formatos permitidos: JPG, PNG, GIF, WebP. Tamaño máximo: 5MB por imagen.
                    </p>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="rounded-lg p-4" style="background-color: rgba(29, 53, 87, 0.05); border: 1px solid var(--color-azul-marino-light);">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle" style="color: var(--color-azul-marino);"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium" style="color: var(--color-azul-marino);">Información Importante</h3>
                        <div class="mt-2 text-sm" style="color: var(--text-primary);">
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
            <div class="flex justify-end space-x-4 pt-6 border-t" style="border-color: var(--color-gris-claro);">
                <a href="/properties" 
                   class="px-6 py-2 rounded-md font-medium transition-colors"
                   style="background-color: var(--color-gris-claro); color: var(--text-primary);">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-6 py-2 rounded-md font-medium transition-colors"
                        style="background: linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%); color: var(--text-light);">
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