<?php
/**
 * Vista: Reportes de Citas
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 */

// Verificar permisos de administrador
requireAuth();
requireRole(ROLE_ADMIN);

// Obtener parámetros de filtro
$fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
$fechaFin = $_GET['fecha_fin'] ?? date('Y-m-t');
$agenteId = $_GET['agente_id'] ?? '';
$estado = $_GET['estado'] ?? '';
$tipo = $_GET['tipo'] ?? '';

// Obtener datos
require_once APP_PATH . '/models/Appointment.php';
require_once APP_PATH . '/models/User.php';

$appointmentModel = new Appointment();
$userModel = new User();

try {
    // Obtener citas filtradas
    $citas = $appointmentModel->getFilteredForReport($fechaInicio, $fechaFin, $agenteId, $estado, $tipo);
    
    // Obtener estadísticas
    $stats = $appointmentModel->getReportStats($fechaInicio, $fechaFin, $agenteId);
    
    // Obtener agentes para el filtro
    $agentes = $userModel->getByRole(ROLE_AGENTE);
    
} catch (Exception $e) {
            // error_log("Error obteniendo reporte de citas: " . $e->getMessage());
    $citas = [];
    $stats = [];
    $agentes = [];
}

$pageTitle = 'Reportes de Citas - ' . APP_NAME;
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Reportes de Citas</h1>
            <p class="text-gray-600 mt-2">Análisis y estadísticas de citas del sistema</p>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Filtros</h2>
            <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha Inicio
                    </label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" 
                           value="<?= htmlspecialchars($fechaInicio) ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                
                <div>
                    <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha Fin
                    </label>
                    <input type="date" id="fecha_fin" name="fecha_fin" 
                           value="<?= htmlspecialchars($fechaFin) ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                
                <div>
                    <label for="agente_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Agente
                    </label>
                    <select id="agente_id" name="agente_id" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">Todos los agentes</option>
                        <?php foreach ($agentes as $agente): ?>
                            <option value="<?= $agente['id'] ?>" <?= $agenteId == $agente['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($agente['nombre'] . ' ' . $agente['apellido']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label for="estado" class="block text-sm font-medium text-gray-700 mb-2">
                        Estado
                    </label>
                    <select id="estado" name="estado" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">Todos los estados</option>
                        <option value="propuesta" <?= $estado === 'propuesta' ? 'selected' : '' ?>>Propuesta</option>
                        <option value="aceptada" <?= $estado === 'aceptada' ? 'selected' : '' ?>>Aceptada</option>
                        <option value="rechazada" <?= $estado === 'rechazada' ? 'selected' : '' ?>>Rechazada</option>
                        <option value="cancelada" <?= $estado === 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                        <option value="completada" <?= $estado === 'completada' ? 'selected' : '' ?>>Completada</option>
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                        Aplicar Filtros
                    </button>
                </div>
            </form>
        </div>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Citas</p>
                        <p class="text-2xl font-semibold text-gray-900"><?= $stats['total'] ?? 0 ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Aceptadas</p>
                        <p class="text-2xl font-semibold text-gray-900"><?= $stats['aceptadas'] ?? 0 ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Rechazadas</p>
                        <p class="text-2xl font-semibold text-gray-900"><?= $stats['rechazadas'] ?? 0 ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Completadas</p>
                        <p class="text-2xl font-semibold text-gray-900"><?= $stats['completadas'] ?? 0 ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Citas -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">Detalle de Citas</h2>

                </div>
            </div>
            
            <?php if (!empty($citas)): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fecha
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Agente
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Cliente
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tipo
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Estado
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ubicación
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($citas as $cita): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <?= date('d/m/Y', strtotime($cita['fecha_cita'])) ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?= date('H:i', strtotime($cita['fecha_cita'])) ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($cita['agente_nombre'] . ' ' . $cita['agente_apellido']) ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?= htmlspecialchars($cita['agente_email']) ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($cita['cliente_nombre'] . ' ' . $cita['cliente_apellido']) ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?= htmlspecialchars($cita['cliente_email']) ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                   <?= $cita['tipo_cita'] === 'visita_propiedad' ? 'bg-purple-100 text-purple-800' : 
                                                      ($cita['tipo_cita'] === 'firma_documentos' ? 'bg-green-100 text-green-800' : 
                                                       'bg-blue-100 text-blue-800') ?>">
                                            <?= ucfirst(str_replace('_', ' ', $cita['tipo_cita'])) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                   <?= $cita['estado'] === 'aceptada' ? 'bg-green-100 text-green-800' : 
                                                      ($cita['estado'] === 'propuesta' ? 'bg-yellow-100 text-yellow-800' : 
                                                       ($cita['estado'] === 'cancelada' ? 'bg-red-100 text-red-800' : 
                                                        'bg-gray-100 text-gray-800')) ?>">
                                            <?= ucfirst($cita['estado']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= htmlspecialchars($cita['lugar'] ?? 'No especificada') ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="/appointments/<?= $cita['id'] ?>" 
                                           class="text-blue-600 hover:text-blue-900">Ver Detalles</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <div class="mx-auto h-12 w-12 text-gray-400">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No hay citas</h3>
                    <p class="mt-1 text-sm text-gray-500">No se encontraron citas con los filtros aplicados.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>



<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?> 
