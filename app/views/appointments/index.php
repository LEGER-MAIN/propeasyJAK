<?php
// Vista: Listado de Citas
// El controlador ya maneja la captura de contenido, no necesitamos ob_start() aquí
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Mis Citas</h1>
                <p class="text-gray-600 mt-2">Gestiona todas tus citas programadas</p>
            </div>
            <a href="/appointments/create" 
               class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors">
                Nueva Cita
            </a>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="filtro_fecha" class="block text-sm font-medium text-gray-700 mb-2">
                        Filtrar por Fecha
                    </label>
                    <select id="filtro_fecha" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">Todas las fechas</option>
                        <option value="hoy">Hoy</option>
                        <option value="semana">Esta semana</option>
                        <option value="mes">Este mes</option>
                    </select>
                </div>
                <div>
                    <label for="filtro_estado" class="block text-sm font-medium text-gray-700 mb-2">
                        Estado
                    </label>
                    <select id="filtro_estado" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">Todos los estados</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="confirmada">Confirmada</option>
                        <option value="completada">Completada</option>
                        <option value="cancelada">Cancelada</option>
                    </select>
                </div>
                <div>
                    <label for="filtro_tipo" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipo de Cita
                    </label>
                    <select id="filtro_tipo" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">Todos los tipos</option>
                        <option value="consulta">Consulta</option>
                        <option value="visita_propiedad">Visita a Propiedad</option>
                        <option value="firma_documentos">Firma de Documentos</option>
                        <option value="negociacion">Negociación</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button id="aplicar_filtros" 
                            class="w-full bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">
                        Aplicar Filtros
                    </button>
                </div>
            </div>
        </div>

        <!-- Lista de Citas -->
        <div class="bg-white rounded-lg shadow-sm">
            <?php if (isset($citas) && is_array($citas) && count($citas) > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Cliente
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fecha y Hora
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
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <span class="text-blue-600 font-medium">
                                                        <?= strtoupper(substr($cita['cliente_nombre'] ?? 'C', 0, 1)) ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?= htmlspecialchars($cita['cliente_nombre'] ?? 'Cliente') ?>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    <?= htmlspecialchars($cita['cliente_email'] ?? '') ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <?= date('d/m/Y', strtotime($cita['fecha_cita'])) ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?= date('H:i', strtotime($cita['fecha_cita'])) ?>
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
                                        <div class="flex justify-end space-x-2">
                                            <a href="/appointments/<?= $cita['id'] ?>" 
                                               class="text-blue-600 hover:text-blue-900">Ver</a>
                                            <a href="/appointments/<?= $cita['id'] ?>/edit" 
                                               class="text-indigo-600 hover:text-indigo-900">Editar</a>
                                            <button onclick="cancelarCita(<?= $cita['id'] ?>)" 
                                                    class="text-red-600 hover:text-red-900">Cancelar</button>
                                        </div>
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
                    <p class="mt-1 text-sm text-gray-500">Comienza creando tu primera cita.</p>
                    <div class="mt-6">
                        <a href="/appointments/create" 
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Crear Cita
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function cancelarCita(citaId) {
    if (confirm('¿Estás seguro de que quieres cancelar esta cita?')) {
        fetch(`/appointments/${citaId}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error al cancelar la cita: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cancelar la cita');
        });
    }
}

document.getElementById('aplicar_filtros').addEventListener('click', function() {
    const fecha = document.getElementById('filtro_fecha').value;
    const estado = document.getElementById('filtro_estado').value;
    const tipo = document.getElementById('filtro_tipo').value;
    
    let url = '/appointments?';
    if (fecha) url += `fecha=${fecha}&`;
    if (estado) url += `estado=${estado}&`;
    if (tipo) url += `tipo=${tipo}&`;
    
    window.location.href = url;
});
</script> 