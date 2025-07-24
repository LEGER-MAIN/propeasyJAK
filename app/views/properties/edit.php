<?php
/**
 * Vista: Editar Propiedad
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Esta vista muestra el formulario para editar una propiedad existente
 */

// Incluir el layout principal
$content = ob_start();
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <a href="/properties/show/<?= $property['id'] ?>" class="text-gray-700 hover:text-primary-600">
                        <?= htmlspecialchars($property['titulo']) ?>
                    </a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-gray-500">Editar</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Editar Propiedad</h1>
        <p class="text-gray-600">Actualiza la información de tu propiedad</p>
    </div>

    <!-- Formulario -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="/properties/<?= $property['id'] ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <!-- Información básica -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Información Básica</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Título -->
                    <div class="md:col-span-2">
                        <label for="titulo" class="block text-sm font-medium text-gray-700 mb-1">
                            Título de la Propiedad <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="titulo" id="titulo" required
                               value="<?= htmlspecialchars($property['titulo']) ?>"
                               placeholder="Ej: Hermosa casa en Bella Vista"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    
                    <!-- Tipo de propiedad -->
                    <div>
                        <label for="tipo" class="block text-sm font-medium text-gray-700 mb-1">
                            Tipo de Propiedad <span class="text-red-500">*</span>
                        </label>
                        <select name="tipo" id="tipo" required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Selecciona un tipo</option>
                            <option value="casa" <?= ($property['tipo'] === 'casa') ? 'selected' : '' ?>>Casa</option>
                            <option value="apartamento" <?= ($property['tipo'] === 'apartamento') ? 'selected' : '' ?>>Apartamento</option>
                            <option value="terreno" <?= ($property['tipo'] === 'terreno') ? 'selected' : '' ?>>Terreno</option>
                            <option value="local_comercial" <?= ($property['tipo'] === 'local_comercial') ? 'selected' : '' ?>>Local Comercial</option>
                            <option value="oficina" <?= ($property['tipo'] === 'oficina') ? 'selected' : '' ?>>Oficina</option>
                        </select>
                    </div>
                    
                    <!-- Estado de la propiedad -->
                    <div>
                        <label for="estado_propiedad" class="block text-sm font-medium text-gray-700 mb-1">
                            Estado de la Propiedad
                        </label>
                        <select name="estado_propiedad" id="estado_propiedad"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                            <option value="bueno" <?= ($property['estado_propiedad'] === 'bueno') ? 'selected' : '' ?>>Bueno</option>
                            <option value="excelente" <?= ($property['estado_propiedad'] === 'excelente') ? 'selected' : '' ?>>Excelente</option>
                            <option value="regular" <?= ($property['estado_propiedad'] === 'regular') ? 'selected' : '' ?>>Regular</option>
                            <option value="necesita_reparacion" <?= ($property['estado_propiedad'] === 'necesita_reparacion') ? 'selected' : '' ?>>Necesita Reparación</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Precio y moneda -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Precio</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Precio -->
                    <div>
                        <label for="precio" class="block text-sm font-medium text-gray-700 mb-1">
                            Precio <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="precio" id="precio" required min="0" step="1000"
                               value="<?= htmlspecialchars($property['precio']) ?>"
                               placeholder="0"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    
                    <!-- Moneda -->
                    <div>
                        <label for="moneda" class="block text-sm font-medium text-gray-700 mb-1">
                            Moneda
                        </label>
                        <select name="moneda" id="moneda" required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                            <option value="USD" selected>USD - Dólar Estadounidense</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Ubicación -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Ubicación</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Ciudad -->
                    <div>
                        <label for="ciudad" class="block text-sm font-medium text-gray-700 mb-1">
                            Ciudad <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="ciudad" id="ciudad" required
                               value="<?= htmlspecialchars($property['ciudad']) ?>"
                               placeholder="Ej: Santo Domingo"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    
                    <!-- Sector -->
                    <div>
                        <label for="sector" class="block text-sm font-medium text-gray-700 mb-1">
                            Sector <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="sector" id="sector" required
                               value="<?= htmlspecialchars($property['sector']) ?>"
                               placeholder="Ej: Bella Vista"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    
                    <!-- Dirección -->
                    <div class="md:col-span-2">
                        <label for="direccion" class="block text-sm font-medium text-gray-700 mb-1">
                            Dirección Completa <span class="text-red-500">*</span>
                        </label>
                        <textarea name="direccion" id="direccion" rows="2" required
                                  placeholder="Ej: Calle Principal #123, entre Calle A y Calle B"
                                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500"><?= htmlspecialchars($property['direccion']) ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Características -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Características</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Metros cuadrados -->
                    <div>
                        <label for="metros_cuadrados" class="block text-sm font-medium text-gray-700 mb-1">
                            Metros Cuadrados <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="metros_cuadrados" id="metros_cuadrados" required min="0" step="0.01"
                               value="<?= htmlspecialchars($property['metros_cuadrados']) ?>"
                               placeholder="0"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    
                    <!-- Habitaciones -->
                    <div>
                        <label for="habitaciones" class="block text-sm font-medium text-gray-700 mb-1">
                            Habitaciones <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="habitaciones" id="habitaciones" required min="0"
                               value="<?= htmlspecialchars($property['habitaciones']) ?>"
                               placeholder="0"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    
                    <!-- Baños -->
                    <div>
                        <label for="banos" class="block text-sm font-medium text-gray-700 mb-1">
                            Baños <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="banos" id="banos" required min="0"
                               value="<?= htmlspecialchars($property['banos']) ?>"
                               placeholder="0"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    
                    <!-- Estacionamientos -->
                    <div>
                        <label for="estacionamientos" class="block text-sm font-medium text-gray-700 mb-1">
                            Estacionamientos
                        </label>
                        <input type="number" name="estacionamientos" id="estacionamientos" min="0"
                               value="<?= htmlspecialchars($property['estacionamientos']) ?>"
                               placeholder="0"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
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
                              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500"><?= htmlspecialchars($property['descripcion']) ?></textarea>
                    <p class="mt-1 text-sm text-gray-500">
                        Proporciona una descripción detallada para atraer más compradores interesados.
                    </p>
                </div>
            </div>

            <!-- Imágenes actuales -->
            <?php if (!empty($property['imagenes'])): ?>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Imágenes Actuales</h3>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <?php foreach ($property['imagenes'] as $imagen): ?>
                            <div class="relative">
                                <img src="<?= htmlspecialchars($imagen['ruta']) ?>" 
                                     alt="Imagen de la propiedad"
                                     class="w-full h-32 object-cover rounded-lg">
                                
                                <?php if ($imagen['es_principal']): ?>
                                    <div class="absolute top-2 left-2">
                                        <span class="bg-primary-600 text-white px-2 py-1 rounded text-xs font-medium">
                                            Principal
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <p class="mt-2 text-sm text-gray-500">
                        Las imágenes actuales se mantendrán. Puedes agregar nuevas imágenes a continuación.
                    </p>
                </div>
            <?php endif; ?>

            <!-- Nuevas imágenes -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Agregar Nuevas Imágenes</h3>
                
                <div>
                    <label for="imagenes" class="block text-sm font-medium text-gray-700 mb-1">
                        Subir Imágenes
                    </label>
                    <input type="file" name="imagenes[]" id="imagenes" multiple accept="image/*,.webp"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                    <p class="mt-1 text-sm text-gray-500">
                        Puedes subir múltiples imágenes. Las nuevas imágenes se agregarán a las existentes.
                        Formatos permitidos: JPG, PNG, GIF, WebP. Tamaño máximo: 5MB por imagen.
                    </p>
                </div>
            </div>

            <!-- Estado de publicación -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-gray-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-gray-800">Estado de Publicación</h3>
                        <div class="mt-2 text-sm text-gray-700">
                            <p><strong>Estado actual:</strong> 
                                <?php
                                $estadoText = '';
                                switch ($property['estado_publicacion']) {
                                    case 'activa':
                                        $estadoText = 'Activa (visible públicamente)';
                                        break;
                                    case 'en_revision':
                                        $estadoText = 'En Revisión (pendiente de validación)';
                                        break;
                                    case 'vendida':
                                        $estadoText = 'Vendida (no disponible)';
                                        break;
                                    case 'rechazada':
                                        $estadoText = 'Rechazada (no disponible)';
                                        break;
                                }
                                echo $estadoText;
                                ?>
                            </p>
                            <p class="mt-1">El estado de publicación no se puede cambiar desde este formulario.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="<?= hasRole(ROLE_AGENTE) ? '/properties/agent/list' : '/properties/show/' . $property['id'] ?>" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-md font-medium transition-colors">
                    Cancelar
                </a>
                <button type="submit" 
                        class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-2 rounded-md font-medium transition-colors">
                    <i class="fas fa-save mr-2"></i>Actualizar Propiedad
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
