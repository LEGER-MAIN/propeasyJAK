<?php
/**
 * Vista: Mis Reportes de Irregularidad
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 */

/**
 * Obtener el color CSS para el estado del reporte
 */
function getEstadoColor($estado) {
    switch ($estado) {
        case 'pendiente':
            return 'bg-yellow-100 text-yellow-800';
        case 'atendido':
            return 'bg-green-100 text-green-800';
        case 'descartado':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

/**
 * Obtener el texto del tipo de reporte
 */
function getTipoReporteTexto($tipo) {
    $tipos = [
        'queja_agente' => 'Queja Agente',
        'problema_plataforma' => 'Problema Plataforma',
        'informacion_falsa' => 'Información Falsa',
        'otro' => 'Otro'
    ];
    
    return $tipos[$tipo] ?? 'Desconocido';
}
?>

<!-- Contenido principal -->
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header con breadcrumb -->
        <div class="mb-8">
            <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-4">
                <a href="/" class="hover:text-primary-600 transition-colors">Inicio</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <a href="/dashboard" class="hover:text-primary-600 transition-colors">Dashboard</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <span class="text-gray-900 font-medium">Mis Reportes</span>
            </nav>
            
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3 mb-2">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-500 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-clipboard-list text-white text-xl"></i>
                        </div>
                        Mis Reportes
                    </h1>
                    <p class="text-gray-600 text-lg">
                        Historial de reportes de irregularidades enviados
                    </p>
                </div>
                
                <a href="/reportes/crear" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white rounded-lg font-medium shadow-lg transition-all duration-200 hover:shadow-xl"
                   style="color: white;"
                   onmouseover="this.style.color='white'"
                   onmouseout="this.style.color='white'">
                    <i class="fas fa-plus"></i>
                    Nuevo Reporte
                </a>
            </div>
        </div>

        <!-- Listado de reportes -->
        <?php if (empty($reportes)): ?>
            <!-- Estado vacío -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="flex flex-col items-center justify-center py-20">
                    <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-clipboard-list text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">No tienes reportes</h3>
                    <p class="text-gray-600 text-center max-w-md mb-8">
                        Aún no has enviado ningún reporte de irregularidad. Si encuentras algún problema, 
                        no dudes en reportarlo para ayudarnos a mejorar la plataforma.
                    </p>
                    <a href="/reportes/crear" 
                       class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white rounded-lg font-medium shadow-lg transition-all duration-200 hover:shadow-xl"
                       style="color: white;"
                       onmouseover="this.style.color='white'"
                       onmouseout="this.style.color='white'">
                        <i class="fas fa-plus"></i>
                        Crear mi primer reporte
                    </a>
                </div>
            </div>
        <?php else: ?>
            <!-- Grid de reportes -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <?php foreach ($reportes as $reporte): ?>
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 group">
                        <!-- Header de la tarjeta -->
                        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-600 to-indigo-700">
                            <div class="flex items-center justify-between mb-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium <?= getEstadoColor($reporte['estado']) ?>">
                                    <?= ucfirst($reporte['estado']) ?>
                                </span>
                                <span class="text-xs text-white">
                                    <?= date('d/m/Y', strtotime($reporte['fecha_reporte'])) ?>
                                </span>
                            </div>
                            <h3 class="text-lg font-semibold text-white line-clamp-2" title="<?= htmlspecialchars($reporte['titulo']) ?>">
                                <?= htmlspecialchars($reporte['titulo']) ?>
                            </h3>
                        </div>
                        
                        <!-- Contenido de la tarjeta -->
                        <div class="p-6">
                            <!-- Descripción -->
                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                <?= htmlspecialchars(substr($reporte['descripcion'], 0, 150)) ?>
                                <?= strlen($reporte['descripcion']) > 150 ? '...' : '' ?>
                            </p>
                            
                            <!-- Tipo de reporte -->
                            <div class="mb-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <?= getTipoReporteTexto($reporte['tipo_reporte']) ?>
                                </span>
                            </div>
                            
                            <!-- Indicadores adicionales -->
                            <div class="space-y-2 mb-4">
                                <?php if ($reporte['archivo_adjunto']): ?>
                                    <div class="flex items-center gap-2 text-sm text-gray-500">
                                        <i class="fas fa-paperclip text-gray-400"></i>
                                        <span>Con archivo adjunto</span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($reporte['respuesta_admin']): ?>
                                    <div class="flex items-center gap-2 p-3 bg-green-50 border border-green-200 rounded-lg">
                                        <i class="fas fa-reply text-green-500"></i>
                                        <span class="text-sm font-medium text-green-800">Respuesta recibida</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Botón de acción -->
                            <a href="/reportes/mostrar/<?= $reporte['id'] ?>" 
                               class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-lg font-medium transition-colors group-hover:bg-gray-900"
                               style="color: white;"
                               onmouseover="this.style.color='white'"
                               onmouseout="this.style.color='white'">
                                <i class="fas fa-eye"></i>
                                Ver Detalles
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Información adicional -->
            <div class="mt-8 bg-gradient-to-r from-blue-600 to-indigo-700 border border-blue-500 rounded-lg p-6">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-info-circle text-white text-lg"></i>
                    </div>
                    <div>
                        <h6 class="font-semibold text-white mb-3 text-lg">Información sobre tus reportes</h6>
                        <ul class="text-sm text-blue-100 space-y-2">
                            <li class="flex items-start gap-3">
                                <div class="w-5 h-5 bg-blue-400 rounded-full flex items-center justify-center mt-0.5 flex-shrink-0">
                                    <i class="fas fa-clock text-white text-xs"></i>
                                </div>
                                <span>Los reportes en estado "Pendiente" están siendo revisados por nuestro equipo</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="w-5 h-5 bg-green-400 rounded-full flex items-center justify-center mt-0.5 flex-shrink-0">
                                    <i class="fas fa-check text-white text-xs"></i>
                                </div>
                                <span>Los reportes "Atendidos" han sido procesados y recibirás una respuesta</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="w-5 h-5 bg-red-400 rounded-full flex items-center justify-center mt-0.5 flex-shrink-0">
                                    <i class="fas fa-times text-white text-xs"></i>
                                </div>
                                <span>Los reportes "Descartados" no requieren acción adicional</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div> 
