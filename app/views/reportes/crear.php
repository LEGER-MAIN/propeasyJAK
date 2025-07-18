<?php
/**
 * Vista: Crear Reporte de Irregularidad
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 */

$csrfToken = generateCSRFToken();
?>

<!-- Contenido principal -->
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header con breadcrumb -->
        <div class="mb-8">
            <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-4">
                <a href="/" class="hover:text-primary-600 transition-colors">Inicio</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <a href="/dashboard" class="hover:text-primary-600 transition-colors">Dashboard</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <span class="text-gray-900 font-medium">Reportar Irregularidad</span>
            </nav>
            
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900 flex items-center justify-center gap-3 mb-2">
                    <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-orange-500 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                    </div>
                    Reportar Irregularidad
                </h1>
                <p class="text-gray-600 text-lg">
                    Ayúdanos a mantener la calidad de nuestra plataforma
                </p>
            </div>
        </div>

        <!-- Formulario -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-red-50 to-orange-50">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-clipboard-list text-red-500"></i>
                    Formulario de Reporte
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Si has experimentado algún problema con un agente inmobiliario, con la plataforma, 
                    o has encontrado información falsa, por favor reporta la situación. 
                    Tu reporte será revisado y nos pondremos en contacto contigo.
                </p>
            </div>
            
            <div class="p-6">
                <form action="/reportes/guardar" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    
                    <!-- Tipo de Reporte -->
                    <div>
                        <label for="tipo_reporte" class="block text-sm font-medium text-gray-700 mb-2">
                            Tipo de Reporte <span class="text-red-500">*</span>
                        </label>
                        <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors" 
                                id="tipo_reporte" name="tipo_reporte" required>
                            <option value="">Selecciona el tipo de reporte</option>
                            <?php foreach ($tiposReporte as $valor => $texto): ?>
                                <option value="<?= htmlspecialchars($valor) ?>">
                                    <?= htmlspecialchars($texto) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Título -->
                    <div>
                        <label for="titulo" class="block text-sm font-medium text-gray-700 mb-2">
                            Título del Reporte <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors" 
                               id="titulo" name="titulo" 
                               placeholder="Describe brevemente el problema" 
                               maxlength="255" required>
                        <p class="mt-1 text-sm text-gray-500">Máximo 255 caracteres</p>
                    </div>

                    <!-- Descripción -->
                    <div>
                        <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                            Descripción Detallada <span class="text-red-500">*</span>
                        </label>
                        <textarea class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors resize-none" 
                                  id="descripcion" name="descripcion" 
                                  rows="6" 
                                  placeholder="Describe detalladamente la irregularidad o problema que has experimentado..." 
                                  required></textarea>
                        <p class="mt-1 text-sm text-gray-500">Mínimo 10 caracteres. Sé específico para ayudarnos a resolver el problema.</p>
                    </div>

                    <!-- Archivo Adjunto -->
                    <div>
                        <label for="archivo_adjunto" class="block text-sm font-medium text-gray-700 mb-2">
                            Archivo Adjunto <span class="text-gray-500">(Opcional)</span>
                        </label>
                        <div class="flex items-center justify-center w-full">
                            <label for="archivo_adjunto" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                    <p class="mb-2 text-sm text-gray-500">
                                        <span class="font-semibold">Haz clic para subir</span> o arrastra y suelta
                                    </p>
                                    <p class="text-xs text-gray-500">JPG, JPEG, PNG, GIF, PDF (máx. 5MB)</p>
                                </div>
                                <input id="archivo_adjunto" name="archivo_adjunto" type="file" class="hidden" 
                                       accept=".jpg,.jpeg,.png,.gif,.pdf">
                            </label>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">
                            Puedes adjuntar capturas de pantalla, documentos o imágenes que respalden tu reporte.
                        </p>
                    </div>

                    <!-- Información Adicional -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-info-circle text-blue-500 mt-1"></i>
                            <div>
                                <h6 class="font-medium text-blue-900 mb-2">Información Importante</h6>
                                <ul class="text-sm text-blue-800 space-y-1">
                                    <li class="flex items-start gap-2">
                                        <i class="fas fa-check text-blue-500 mt-1 text-xs"></i>
                                        <span>Todos los reportes son revisados por nuestro equipo de administración</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <i class="fas fa-check text-blue-500 mt-1 text-xs"></i>
                                        <span>Mantendremos la confidencialidad de tu información</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <i class="fas fa-check text-blue-500 mt-1 text-xs"></i>
                                        <span>Te notificaremos cuando tu reporte sea atendido</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <i class="fas fa-exclamation-triangle text-orange-500 mt-1 text-xs"></i>
                                        <span>Los reportes falsos o maliciosos pueden resultar en la suspensión de tu cuenta</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-4">
                        <a href="/dashboard" 
                           class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">
                            <i class="fas fa-arrow-left"></i>
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white rounded-lg font-medium shadow-lg transition-all duration-200 hover:shadow-xl">
                            <i class="fas fa-paper-plane"></i>
                            Enviar Reporte
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const descripcion = document.getElementById('descripcion');
    const titulo = document.getElementById('titulo');
    const archivoInput = document.getElementById('archivo_adjunto');
    const fileLabel = document.querySelector('label[for="archivo_adjunto"]');
    
    // Validación en tiempo real para descripción
    descripcion.addEventListener('input', function() {
        const longitud = this.value.length;
        if (longitud < 10 && longitud > 0) {
            this.classList.add('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
            this.classList.remove('border-gray-300', 'focus:ring-primary-500', 'focus:border-primary-500');
        } else {
            this.classList.remove('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
            this.classList.add('border-gray-300', 'focus:ring-primary-500', 'focus:border-primary-500');
        }
    });
    
    // Validación en tiempo real para título
    titulo.addEventListener('input', function() {
        const longitud = this.value.length;
        if (longitud > 255) {
            this.classList.add('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
            this.classList.remove('border-gray-300', 'focus:ring-primary-500', 'focus:border-primary-500');
        } else {
            this.classList.remove('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
            this.classList.add('border-gray-300', 'focus:ring-primary-500', 'focus:border-primary-500');
        }
    });
    
    // Validación de archivo
    archivoInput.addEventListener('change', function() {
        const archivo = this.files[0];
        if (archivo) {
            // Validar tamaño (5MB)
            if (archivo.size > 5 * 1024 * 1024) {
                alert('El archivo es demasiado grande. El tamaño máximo es 5MB.');
                this.value = '';
                return;
            }
            
            // Validar extensión
            const extension = archivo.name.split('.').pop().toLowerCase();
            const extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
            if (!extensionesPermitidas.includes(extension)) {
                alert('Tipo de archivo no permitido. Solo se permiten: JPG, JPEG, PNG, GIF, PDF.');
                this.value = '';
                return;
            }
            
            // Mostrar nombre del archivo seleccionado
            const fileName = archivo.name;
            fileLabel.innerHTML = `
                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                    <i class="fas fa-file-alt text-3xl text-green-500 mb-2"></i>
                    <p class="mb-2 text-sm text-gray-600 font-medium">${fileName}</p>
                    <p class="text-xs text-gray-500">Archivo seleccionado</p>
                </div>
            `;
        }
    });
    
    // Validación del formulario
    form.addEventListener('submit', function(e) {
        let errores = [];
        
        // Validar descripción
        if (descripcion.value.length < 10) {
            errores.push('La descripción debe tener al menos 10 caracteres');
        }
        
        // Validar título
        if (titulo.value.length > 255) {
            errores.push('El título no puede exceder 255 caracteres');
        }
        
        if (errores.length > 0) {
            e.preventDefault();
            alert('Por favor, corrige los siguientes errores:\n\n' + errores.join('\n'));
        }
    });
});
</script> 