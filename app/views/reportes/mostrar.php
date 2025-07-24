<?php
/**
 * Vista: Mostrar Detalles del Reporte
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
 * Obtener el color del alert para el estado
 */
function getEstadoAlertColor($estado) {
    switch ($estado) {
        case 'pendiente':
            return 'bg-yellow-50 border-yellow-200 text-yellow-800';
        case 'atendido':
            return 'bg-green-50 border-green-200 text-green-800';
        case 'descartado':
            return 'bg-red-50 border-red-200 text-red-800';
        default:
            return 'bg-blue-50 border-blue-200 text-blue-800';
    }
}

/**
 * Obtener el icono para el estado
 */
function getEstadoIcon($estado) {
    switch ($estado) {
        case 'pendiente':
            return 'clock';
        case 'atendido':
            return 'check-circle';
        case 'descartado':
            return 'times-circle';
        default:
            return 'question-circle';
    }
}

/**
 * Obtener la descripción del estado
 */
function getEstadoDescripcion($estado) {
    switch ($estado) {
        case 'pendiente':
            return 'Tu reporte está siendo revisado por nuestro equipo. Te notificaremos cuando sea atendido.';
        case 'atendido':
            return 'Tu reporte ha sido procesado y atendido. Si tienes más preguntas, no dudes en contactarnos.';
        case 'descartado':
            return 'Tu reporte ha sido revisado y no requiere acción adicional.';
        default:
            return 'Estado del reporte desconocido.';
    }
}

/**
 * Obtener el texto del tipo de reporte
 */
function getTipoReporteTexto($tipo) {
    $tipos = [
        'queja_agente' => 'Queja contra Agente',
        'problema_plataforma' => 'Problema con la Plataforma',
        'informacion_falsa' => 'Información Falsa',
        'otro' => 'Otro'
    ];
    
    return $tipos[$tipo] ?? 'Desconocido';
}
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
                <a href="/reportes/mis-reportes" class="hover:text-primary-600 transition-colors">Mis Reportes</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <span class="text-gray-900 font-medium">Detalles del Reporte</span>
            </nav>
            
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3 mb-2">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-500 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-clipboard-list text-white text-xl"></i>
                        </div>
                        Detalles del Reporte
                    </h1>
                    <p class="text-gray-600 text-lg">
                        Información completa del reporte #<?= $reporte['id'] ?>
                    </p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="/reportes/mis-reportes" 
                       class="inline-flex items-center gap-2 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors"
                       style="color: #374151;"
                       onmouseover="this.style.color='#374151'"
                       onmouseout="this.style.color='#374151'">
                        <i class="fas fa-arrow-left"></i>
                        Volver
                    </a>
                    <?php if ($reporte['estado'] === 'pendiente'): ?>
                        <a href="/reportes/crear" 
                           class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white rounded-lg font-medium shadow-lg transition-all duration-200 hover:shadow-xl"
                           style="color: white;"
                           onmouseover="this.style.color='white'"
                           onmouseout="this.style.color='white'">
                            <i class="fas fa-plus"></i>
                            Nuevo Reporte
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Contenido principal del reporte -->
        <div class="space-y-6">
            <!-- Tarjeta principal del reporte -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                <!-- Header de la tarjeta -->
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-600 to-indigo-600">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <h2 class="text-xl font-semibold text-white flex items-center gap-2">
                            <i class="fas fa-exclamation-triangle text-white"></i>
                            <?= htmlspecialchars($reporte['titulo']) ?>
                        </h2>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <?= ucfirst($reporte['estado']) ?>
                        </span>
                    </div>
                </div>
                
                <div class="p-6 space-y-6">
                    <!-- Información del reporte y usuario -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Información del Reporte -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center gap-2">
                                <i class="fas fa-info-circle text-blue-500"></i>
                                Información del Reporte
                            </h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-600">ID:</span>
                                    <span class="text-gray-900">#<?= $reporte['id'] ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-600">Tipo:</span>
                                    <span class="text-gray-900"><?= getTipoReporteTexto($reporte['tipo_reporte']) ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-600">Fecha:</span>
                                    <span class="text-gray-900"><?= date('d/m/Y H:i', strtotime($reporte['fecha_reporte'])) ?></span>
                                </div>
                                <?php if ($reporte['fecha_respuesta']): ?>
                                    <div class="flex justify-between">
                                        <span class="font-medium text-gray-600">Respondido:</span>
                                        <span class="text-gray-900"><?= date('d/m/Y H:i', strtotime($reporte['fecha_respuesta'])) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Información del Usuario -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center gap-2">
                                <i class="fas fa-user text-green-500"></i>
                                Información del Usuario
                            </h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-600">Nombre:</span>
                                    <span class="text-gray-900"><?= htmlspecialchars($reporte['nombre'] . ' ' . $reporte['apellido']) ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-600">Email:</span>
                                    <span class="text-gray-900"><?= htmlspecialchars($reporte['email']) ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-600">Teléfono:</span>
                                    <span class="text-gray-900"><?= htmlspecialchars($reporte['telefono']) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center gap-2">
                            <i class="fas fa-align-left text-purple-500"></i>
                            Descripción del Problema
                        </h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-700 whitespace-pre-line"><?= htmlspecialchars($reporte['descripcion']) ?></p>
                        </div>
                    </div>

                    <!-- Archivo Adjunto -->
                    <?php if ($reporte['archivo_adjunto']): ?>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center gap-2">
                                <i class="fas fa-paperclip text-orange-500"></i>
                                Archivo Adjunto
                            </h3>
                            <div class="flex items-center gap-3 p-4 bg-orange-50 border border-orange-200 rounded-lg">
                                <i class="fas fa-file-alt text-orange-500 text-xl"></i>
                                <div class="flex-1">
                                    <p class="font-medium text-orange-900"><?= htmlspecialchars($reporte['archivo_adjunto']) ?></p>
                                    <p class="text-sm text-orange-700">Archivo adjunto al reporte</p>
                                </div>
                                <a href="/uploads/reportes/<?= htmlspecialchars($reporte['archivo_adjunto']) ?>" 
                                   target="_blank" 
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium transition-colors"
                                   style="color: white;"
                                   onmouseover="this.style.color='white'"
                                   onmouseout="this.style.color='white'">
                                    <i class="fas fa-download"></i>
                                    Descargar
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Respuesta del Administrador -->
                    <?php if ($reporte['respuesta_admin']): ?>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center gap-2">
                                <i class="fas fa-reply text-green-500"></i>
                                Respuesta del Administrador
                            </h3>
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <div class="flex justify-between items-start mb-3">
                                    <span class="font-medium text-green-900">Respuesta:</span>
                                    <?php if ($reporte['admin_nombre']): ?>
                                        <span class="text-sm text-green-700">
                                            Por: <?= htmlspecialchars($reporte['admin_nombre'] . ' ' . $reporte['admin_apellido']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-green-800 whitespace-pre-line"><?= htmlspecialchars($reporte['respuesta_admin']) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Estado Actual -->
                    <div class="border rounded-lg p-4 <?= getEstadoAlertColor($reporte['estado']) ?>">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-<?= getEstadoIcon($reporte['estado']) ?> text-xl mt-1"></i>
                            <div>
                                <h4 class="font-semibold mb-2">Estado: <?= ucfirst($reporte['estado']) ?></h4>
                                <p class="text-sm"><?= getEstadoDescripcion($reporte['estado']) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-600 to-indigo-600">
                    <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                        <i class="fas fa-info-circle text-white"></i>
                        Información Importante
                    </h3>
                </div>
                <div class="p-6 bg-blue-50">
                    <ul class="space-y-2 text-gray-800">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check text-green-600 mt-1 text-xs"></i>
                            <span>Todos los reportes son revisados por nuestro equipo de administración</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check text-green-600 mt-1 text-xs"></i>
                            <span>Mantendremos la confidencialidad de tu información</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check text-green-600 mt-1 text-xs"></i>
                            <span>Si tienes preguntas adicionales, puedes crear un nuevo reporte</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-exclamation-triangle text-orange-600 mt-1 text-xs"></i>
                            <span>Los reportes falsos o maliciosos pueden resultar en la suspensión de tu cuenta</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div> 