<?php
// Vista: Detalle de Cita
// El controlador ya maneja la captura de contenido, no necesitamos ob_start() aquí
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Detalle de Cita</h1>
                <p class="text-gray-600 mt-2">Información completa de la cita programada</p>
            </div>
            <div class="flex space-x-3">
                <a href="/appointments" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    Volver
                </a>
                <a href="/appointments/<?= $cita['id'] ?? '' ?>/edit" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    Editar
                </a>
            </div>
        </div>

        <?php if (isset($cita) && is_array($cita)): ?>
            <!-- Información Principal -->
            <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Información del Cliente -->
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Información del Cliente</h2>
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12">
                                    <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                                        <span class="text-blue-600 font-medium text-lg">
                                            <?= strtoupper(substr($cita['cliente_nombre'] ?? 'C', 0, 1)) ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-lg font-medium text-gray-900">
                                        <?= htmlspecialchars($cita['cliente_nombre'] ?? 'Cliente') ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Cliente
                                    </div>
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <div class="flex items-center text-sm">
                                    <svg class="h-4 w-4 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-gray-600"><?= htmlspecialchars($cita['cliente_email'] ?? 'No disponible') ?></span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <svg class="h-4 w-4 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    <span class="text-gray-600"><?= htmlspecialchars($cita['cliente_telefono'] ?? 'No disponible') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información de la Cita -->
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Detalles de la Cita</h2>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">Estado:</span>
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                                           <?= $cita['estado'] === 'aceptada' ? 'bg-green-100 text-green-800' : 
                                              ($cita['estado'] === 'propuesta' ? 'bg-yellow-100 text-yellow-800' : 
                                               ($cita['estado'] === 'cancelada' ? 'bg-red-100 text-red-800' : 
                                                ($cita['estado'] === 'realizada' ? 'bg-blue-100 text-blue-800' :
                                                 'bg-gray-100 text-gray-800'))) ?>">
                                    <?= ucfirst($cita['estado']) ?>
                                </span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">Tipo:</span>
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                                           <?= $cita['tipo_cita'] === 'visita_propiedad' ? 'bg-purple-100 text-purple-800' : 
                                              ($cita['tipo_cita'] === 'firma_documentos' ? 'bg-green-100 text-green-800' : 
                                               'bg-blue-100 text-blue-800') ?>">
                                    <?= ucfirst(str_replace('_', ' ', $cita['tipo_cita'])) ?>
                                </span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">Fecha y Hora:</span>
                                <span class="text-sm text-gray-900"><?= date('d/m/Y H:i', strtotime($cita['fecha_cita'])) ?></span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">Lugar:</span>
                                <span class="text-sm text-gray-900"><?= htmlspecialchars($cita['lugar'] ?? 'No especificado') ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información Adicional -->
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Información Adicional</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Propiedad</label>
                        <div class="text-sm text-gray-900 bg-gray-50 p-3 rounded-md">
                            <?= htmlspecialchars($cita['propiedad_titulo'] ?? 'No especificada') ?>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Creada el</label>
                        <div class="text-sm text-gray-900 bg-gray-50 p-3 rounded-md">
                            <?= date('d/m/Y H:i', strtotime($cita['fecha_creacion'] ?? 'now')) ?>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($cita['observaciones'])): ?>
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                        <div class="text-sm text-gray-900 bg-gray-50 p-4 rounded-md">
                            <?= nl2br(htmlspecialchars($cita['observaciones'])) ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Acciones -->
            <div class="mt-8 flex justify-center space-x-4">
                <?php if ($cita['estado'] === 'propuesta'): ?>
                    <button onclick="aceptarCita(<?= $cita['id'] ?>)" 
                            class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                        Aceptar Cita
                    </button>
                    <button onclick="rechazarCita(<?= $cita['id'] ?>)" 
                            class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                        Rechazar Cita
                    </button>
                <?php endif; ?>
                
                <?php if ($cita['estado'] === 'aceptada'): ?>
                    <button onclick="completarCita(<?= $cita['id'] ?>)" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        Marcar como Completada
                    </button>
                <?php endif; ?>
                
                <?php if ($cita['estado'] !== 'cancelada' && $cita['estado'] !== 'realizada'): ?>
                    <button onclick="cancelarCita(<?= $cita['id'] ?>)" 
                            class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                        Cancelar Cita
                    </button>
                <?php endif; ?>
                
                <a href="/appointments/<?= $cita['id'] ?>/edit" 
                   class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    Editar Cita
                </a>
            </div>

        <?php else: ?>
            <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                <div class="mx-auto h-12 w-12 text-gray-400 mb-4">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Cita no encontrada</h3>
                <p class="text-gray-500 mb-6">La cita que buscas no existe o ha sido eliminada.</p>
                <a href="/appointments" 
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Volver a Mis Citas
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function aceptarCita(citaId) {
    if (confirm('¿Estás seguro de que quieres aceptar esta cita?')) {
        fetch(`/appointments/${citaId}/accept`, {
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
                alert('Error al aceptar la cita: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al aceptar la cita');
        });
    }
}

function rechazarCita(citaId) {
    if (confirm('¿Estás seguro de que quieres rechazar esta cita?')) {
        fetch(`/appointments/${citaId}/reject`, {
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
                alert('Error al rechazar la cita: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al rechazar la cita');
        });
    }
}

function completarCita(citaId) {
    if (confirm('¿Estás seguro de que quieres marcar esta cita como completada?')) {
        fetch(`/appointments/${citaId}/complete`, {
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
                alert('Error al completar la cita: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al completar la cita');
        });
    }
}

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
</script>

 