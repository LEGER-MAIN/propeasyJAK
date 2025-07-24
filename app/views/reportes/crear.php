<?php
/**
 * Vista: Crear Reporte de Irregularidad
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 */

$csrfToken = generateCSRFToken();
?>

<!-- Contenido principal -->
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        
        <!-- Header con breadcrumb mejorado -->
        <div class="mb-10">
            <nav class="flex items-center space-x-2 text-sm text-gray-600 mb-6">
                <a href="/" class="hover:text-blue-600 transition-colors duration-200 flex items-center gap-1">
                    <i class="fas fa-home text-xs"></i>
                    Inicio
                </a>
                <i class="fas fa-chevron-right text-xs text-gray-400"></i>
                <a href="/dashboard" class="hover:text-blue-600 transition-colors duration-200">Dashboard</a>
                <i class="fas fa-chevron-right text-xs text-gray-400"></i>
                <span class="text-gray-900 font-semibold">Reportar Irregularidad</span>
            </nav>
            
            <!-- Header principal con diseño moderno -->
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-red-500 via-orange-500 to-red-600 rounded-2xl shadow-xl mb-6">
                    <i class="fas fa-exclamation-triangle text-white text-2xl"></i>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-4">
                    Reportar Irregularidad
                </h1>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto leading-relaxed">
                    Ayúdanos a mantener la calidad y confiabilidad de nuestra plataforma
                </p>
            </div>
        </div>

        <!-- Formulario con diseño moderno -->
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden">
            <!-- Header del formulario -->
            <div class="px-8 py-6 bg-gradient-to-r from-red-500 via-orange-500 to-red-600">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-clipboard-list text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Formulario de Reporte</h2>
                        <p class="text-red-100 mt-1">
                            Tu reporte nos ayuda a mejorar la experiencia de todos
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Contenido del formulario -->
            <div class="p-8">
                <form action="/reportes/guardar" method="POST" enctype="multipart/form-data" class="space-y-8">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    
                    <!-- Tipo de Reporte -->
                    <div class="space-y-3">
                        <label for="tipo_reporte" class="block text-lg font-semibold text-gray-800">
                            Tipo de Reporte <span class="text-red-500">*</span>
                        </label>
                        <select class="w-full px-6 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all duration-200 text-lg bg-white" 
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
                    <div class="space-y-3">
                        <label for="titulo" class="block text-lg font-semibold text-gray-800">
                            Título del Reporte <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               class="w-full px-6 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all duration-200 text-lg" 
                               id="titulo" name="titulo" 
                               placeholder="Describe brevemente el problema" 
                               maxlength="255" required>
                        <p class="text-sm text-gray-500 flex items-center gap-2">
                            <i class="fas fa-info-circle text-blue-500"></i>
                            Máximo 255 caracteres
                        </p>
                    </div>

                    <!-- Descripción -->
                    <div class="space-y-3">
                        <label for="descripcion" class="block text-lg font-semibold text-gray-800">
                            Descripción Detallada <span class="text-red-500">*</span>
                        </label>
                        <textarea class="w-full px-6 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all duration-200 resize-none text-lg" 
                                  id="descripcion" name="descripcion" 
                                  rows="8" 
                                  placeholder="Describe detalladamente la irregularidad o problema que has experimentado..." 
                                  required></textarea>
                        <p class="text-sm text-gray-500 flex items-center gap-2">
                            <i class="fas fa-lightbulb text-yellow-500"></i>
                            Mínimo 10 caracteres. Sé específico para ayudarnos a resolver el problema.
                        </p>
                    </div>

                    <!-- Archivo Adjunto -->
                    <div class="space-y-3">
                        <label for="archivo_adjunto" class="block text-lg font-semibold text-gray-800">
                            Archivo Adjunto <span class="text-gray-500 text-base">(Opcional)</span>
                        </label>
                        <div class="flex items-center justify-center w-full">
                            <label for="archivo_adjunto" class="flex flex-col items-center justify-center w-full h-40 border-3 border-dashed border-gray-300 rounded-2xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all duration-200 hover:border-blue-400 hover:shadow-lg">
                                <div class="flex flex-col items-center justify-center pt-6 pb-6">
                                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg">
                                        <i class="fas fa-cloud-upload-alt text-white text-2xl"></i>
                                    </div>
                                    <p class="mb-2 text-lg text-gray-700 font-semibold">
                                        Haz clic para subir o arrastra y suelta
                                    </p>
                                    <p class="text-sm text-gray-500">JPG, JPEG, PNG, GIF, PDF (máx. 5MB)</p>
                                </div>
                                <input id="archivo_adjunto" name="archivo_adjunto" type="file" class="hidden" 
                                       accept=".jpg,.jpeg,.png,.gif,.pdf">
                            </label>
                        </div>
                        <p class="text-sm text-gray-500 flex items-center gap-2">
                            <i class="fas fa-paperclip text-gray-400"></i>
                            Puedes adjuntar capturas de pantalla, documentos o imágenes que respalden tu reporte.
                        </p>
                    </div>

                    <!-- Información Adicional con diseño mejorado -->
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 border-2 border-blue-500 rounded-2xl p-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center shadow-lg backdrop-blur-sm">
                                <i class="fas fa-shield-alt text-white text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h6 class="text-xl font-bold text-white mb-4">Proceso de Revisión</h6>
                                <ul class="space-y-4">
                                    <li class="flex items-start gap-3">
                                        <div class="w-6 h-6 bg-green-400 rounded-full flex items-center justify-center mt-0.5 flex-shrink-0">
                                            <i class="fas fa-check text-white text-xs"></i>
                                        </div>
                                        <div>
                                            <span class="text-white font-semibold block">Revisión Profesional</span>
                                            <span class="text-blue-100 text-sm">Cada reporte es analizado por nuestro equipo especializado</span>
                                        </div>
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <div class="w-6 h-6 bg-green-400 rounded-full flex items-center justify-center mt-0.5 flex-shrink-0">
                                            <i class="fas fa-check text-white text-xs"></i>
                                        </div>
                                        <div>
                                            <span class="text-white font-semibold block">Confidencialidad Garantizada</span>
                                            <span class="text-blue-100 text-sm">Tu información personal permanece completamente segura</span>
                                        </div>
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <div class="w-6 h-6 bg-green-400 rounded-full flex items-center justify-center mt-0.5 flex-shrink-0">
                                            <i class="fas fa-check text-white text-xs"></i>
                                        </div>
                                        <div>
                                            <span class="text-white font-semibold block">Seguimiento Completo</span>
                                            <span class="text-blue-100 text-sm">Recibirás notificaciones sobre el estado de tu reporte</span>
                                        </div>
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <div class="w-6 h-6 bg-orange-400 rounded-full flex items-center justify-center mt-0.5 flex-shrink-0">
                                            <i class="fas fa-exclamation-triangle text-white text-xs"></i>
                                        </div>
                                        <div>
                                            <span class="text-white font-semibold block">Uso Responsable</span>
                                            <span class="text-orange-100 text-sm">Los reportes falsos pueden resultar en acciones disciplinarias</span>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Botones con diseño moderno -->
                    <div class="flex flex-col sm:flex-row gap-6 pt-6">
                        <a href="/dashboard" 
                           class="flex-1 sm:flex-none inline-flex items-center justify-center gap-3 px-8 py-4 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold text-lg transition-all duration-200 hover:shadow-lg hover:scale-105">
                            <i class="fas fa-arrow-left"></i>
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="flex-1 sm:flex-none inline-flex items-center justify-center gap-3 px-8 py-4 bg-gradient-to-r from-red-500 via-orange-500 to-red-600 hover:from-red-600 hover:via-orange-600 hover:to-red-700 text-white rounded-xl font-semibold text-lg shadow-xl transition-all duration-200 hover:shadow-2xl hover:scale-105 transform">
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
            this.classList.add('border-red-500', 'focus:ring-red-100', 'focus:border-red-500');
            this.classList.remove('border-gray-200', 'focus:ring-blue-100', 'focus:border-blue-500');
        } else {
            this.classList.remove('border-red-500', 'focus:ring-red-100', 'focus:border-red-500');
            this.classList.add('border-gray-200', 'focus:ring-blue-100', 'focus:border-blue-500');
        }
    });
    
    // Validación en tiempo real para título
    titulo.addEventListener('input', function() {
        const longitud = this.value.length;
        if (longitud > 255) {
            this.classList.add('border-red-500', 'focus:ring-red-100', 'focus:border-red-500');
            this.classList.remove('border-gray-200', 'focus:ring-blue-100', 'focus:border-blue-500');
        } else {
            this.classList.remove('border-red-500', 'focus:ring-red-100', 'focus:border-red-500');
            this.classList.add('border-gray-200', 'focus:ring-blue-100', 'focus:border-blue-500');
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
                <div class="flex flex-col items-center justify-center pt-6 pb-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg">
                        <i class="fas fa-file-alt text-white text-2xl"></i>
                    </div>
                    <p class="mb-2 text-lg text-gray-700 font-semibold">${fileName}</p>
                    <p class="text-sm text-gray-500">Archivo seleccionado</p>
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