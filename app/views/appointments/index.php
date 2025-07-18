<?php
// Vista: Listado de Citas
// El controlador ya maneja la captura de contenido, no necesitamos ob_start() aquí
?>

<div class="container mx-auto px-4 py-8" style="background-color: var(--bg-primary);">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold" style="color: var(--text-primary);">Mis Citas</h1>
                <p class="mt-2" style="color: var(--text-secondary);">Gestiona todas tus citas programadas</p>
            </div>
            <a href="/appointments/create" 
               class="px-6 py-2 rounded-md transition-all duration-200"
               style="background: linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%); color: white;"
               onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(29, 53, 87, 0.3)'"
               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                Nueva Cita
            </a>
        </div>

        <!-- Filtros -->
        <div class="rounded-lg shadow-sm p-6 mb-6" style="background: linear-gradient(135deg, var(--bg-light) 0%, rgba(255, 255, 255, 0.9) 100%); border: 1px solid var(--color-gris-claro); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="filtro_fecha" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                        Filtrar por Fecha
                    </label>
                    <select id="filtro_fecha" class="w-full px-3 py-2 border rounded-md transition-all duration-200" style="border-color: var(--color-gris-claro);" onfocus="this.style.borderColor='var(--color-azul-marino)'; this.style.boxShadow='0 0 0 3px rgba(29, 53, 87, 0.1)'" onblur="this.style.borderColor='var(--color-gris-claro)'; this.style.boxShadow='none'">
                        <option value="">Todas las fechas</option>
                        <option value="hoy">Hoy</option>
                        <option value="semana">Esta semana</option>
                        <option value="mes">Este mes</option>
                    </select>
                </div>
                <div>
                    <label for="filtro_estado" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                        Estado
                    </label>
                    <select id="filtro_estado" class="w-full px-3 py-2 border rounded-md transition-all duration-200" style="border-color: var(--color-gris-claro);" onfocus="this.style.borderColor='var(--color-azul-marino)'; this.style.boxShadow='0 0 0 3px rgba(29, 53, 87, 0.1)'" onblur="this.style.borderColor='var(--color-gris-claro)'; this.style.boxShadow='none'">
                        <option value="">Todos los estados</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="confirmada">Confirmada</option>
                        <option value="completada">Completada</option>
                        <option value="cancelada">Cancelada</option>
                    </select>
                </div>
                <div>
                    <label for="filtro_tipo" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                        Tipo de Cita
                    </label>
                    <select id="filtro_tipo" class="w-full px-3 py-2 border rounded-md transition-all duration-200" style="border-color: var(--color-gris-claro);" onfocus="this.style.borderColor='var(--color-azul-marino)'; this.style.boxShadow='0 0 0 3px rgba(29, 53, 87, 0.1)'" onblur="this.style.borderColor='var(--color-gris-claro)'; this.style.boxShadow='none'">
                        <option value="">Todos los tipos</option>
                        <option value="consulta">Consulta</option>
                        <option value="visita_propiedad">Visita a Propiedad</option>
                        <option value="firma_documentos">Firma de Documentos</option>
                        <option value="negociacion">Negociación</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button id="aplicar_filtros" 
                            class="w-full px-4 py-2 rounded-md transition-all duration-200"
                            style="background: linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%); color: white;"
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(29, 53, 87, 0.3)'"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                        Aplicar Filtros
                    </button>
                </div>
            </div>
        </div>

        <!-- Lista de Citas -->
        <div class="rounded-lg shadow-sm" style="background: linear-gradient(135deg, var(--bg-light) 0%, rgba(255, 255, 255, 0.9) 100%); border: 1px solid var(--color-gris-claro); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);">
            <?php if (isset($citas) && is_array($citas) && count($citas) > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y" style="border-top-color: var(--color-gris-claro);">
                        <thead style="background: linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%);">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: white;">
                                    Cliente
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: white;">
                                    Fecha y Hora
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: white;">
                                    Tipo
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: white;">
                                    Estado
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: white;">
                                    Ubicación
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider" style="color: white;">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody style="background-color: var(--bg-light);" class="divide-y" style="border-top-color: var(--color-gris-claro);">
                            <?php foreach ($citas as $cita): ?>
                                <tr class="transition-all duration-200" onmouseover="this.style.backgroundColor='var(--bg-secondary)'" onmouseout="this.style.backgroundColor='var(--bg-light)'">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full flex items-center justify-center" style="background-color: rgba(29, 53, 87, 0.1);">
                                                    <span class="font-medium" style="color: var(--color-azul-marino);">
                                                        <?= strtoupper(substr($cita['cliente_nombre'] ?? 'C', 0, 1)) ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium" style="color: var(--text-primary);">
                                                    <?= htmlspecialchars($cita['cliente_nombre'] ?? 'Cliente') ?>
                                                </div>
                                                <div class="text-sm" style="color: var(--text-secondary);">
                                                    <?= htmlspecialchars($cita['cliente_email'] ?? '') ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm" style="color: var(--text-primary);">
                                            <?= date('d/m/Y', strtotime($cita['fecha_cita'])) ?>
                                        </div>
                                        <div class="text-sm" style="color: var(--text-secondary);">
                                            <?= date('H:i', strtotime($cita['fecha_cita'])) ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" style="<?= getTipoCitaBadgeStyle($cita['tipo_cita']) ?>">
                                            <?= ucfirst(str_replace('_', ' ', $cita['tipo_cita'])) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" style="<?= getEstadoCitaBadgeStyle($cita['estado']) ?>">
                                            <?= ucfirst($cita['estado']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: var(--text-primary);">
                                        <?= htmlspecialchars($cita['lugar'] ?? 'No especificada') ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-3">
                                            <a href="/appointments/<?= $cita['id'] ?>" 
                                               class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium transition-all duration-200 border"
                                               style="border-color: var(--color-azul-marino); color: var(--color-azul-marino); background-color: rgba(29, 53, 87, 0.05);"
                                               onmouseover="this.style.backgroundColor='var(--color-azul-marino)'; this.style.color='white'; this.style.transform='translateY(-1px)'"
                                               onmouseout="this.style.backgroundColor='rgba(29, 53, 87, 0.05)'; this.style.color='var(--color-azul-marino)'; this.style.transform='translateY(0)'">
                                                Ver
                                            </a>
                                            <a href="/appointments/<?= $cita['id'] ?>/edit" 
                                               class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium transition-all duration-200 border"
                                               style="border-color: var(--color-verde-esmeralda); color: var(--color-verde-esmeralda); background-color: rgba(42, 157, 143, 0.05);"
                                               onmouseover="this.style.backgroundColor='var(--color-verde-esmeralda)'; this.style.color='white'; this.style.transform='translateY(-1px)'"
                                               onmouseout="this.style.backgroundColor='rgba(42, 157, 143, 0.05)'; this.style.color='var(--color-verde-esmeralda)'; this.style.transform='translateY(0)'">
                                                Editar
                                            </a>
                                            <button onclick="cancelarCita(<?= $cita['id'] ?>)" 
                                                    class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium transition-all duration-200 border"
                                                    style="border-color: var(--color-rojo-error); color: var(--color-rojo-error); background-color: rgba(220, 53, 69, 0.05);"
                                                    onmouseover="this.style.backgroundColor='var(--color-rojo-error)'; this.style.color='white'; this.style.transform='translateY(-1px)'"
                                                    onmouseout="this.style.backgroundColor='rgba(220, 53, 69, 0.05)'; this.style.color='var(--color-rojo-error)'; this.style.transform='translateY(0)'">
                                                Cancelar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <div class="mx-auto h-12 w-12" style="color: var(--color-gris-claro);">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="mt-2 text-sm font-medium" style="color: var(--text-primary);">No hay citas</h3>
                    <p class="mt-1 text-sm" style="color: var(--text-secondary);">Comienza creando tu primera cita.</p>
                    <div class="mt-6">
                        <a href="/appointments/create" 
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md transition-all duration-200"
                           style="background: linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%); color: white;"
                           onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(29, 53, 87, 0.3)'"
                           onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                            Crear Cita
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Funciones auxiliares para badges
function getTipoCitaBadgeStyle($tipo) {
    switch ($tipo) {
        case 'visita_propiedad':
            return 'background-color: rgba(233, 196, 106, 0.1); color: var(--color-dorado-suave); border: 1px solid var(--color-dorado-suave);';
        case 'firma_documentos':
            return 'background-color: var(--color-verde-esmeralda); color: white; border: 1px solid var(--color-verde-esmeralda);';
        case 'consulta':
        case 'negociacion':
        default:
            return 'background-color: rgba(29, 53, 87, 0.1); color: var(--color-azul-marino); border: 1px solid var(--color-azul-marino);';
    }
}

function getEstadoCitaBadgeStyle($estado) {
    switch ($estado) {
        case 'aceptada':
            return 'background-color: var(--color-verde-esmeralda); color: white; border: 1px solid var(--color-verde-esmeralda);';
        case 'propuesta':
            return 'background-color: rgba(233, 196, 106, 0.1); color: var(--color-dorado-suave); border: 1px solid var(--color-dorado-suave);';
        case 'cancelada':
            return 'background-color: var(--color-rojo-error); color: white; border: 1px solid var(--color-rojo-error);';
        case 'pendiente':
        case 'confirmada':
        case 'completada':
        default:
            return 'background-color: rgba(221, 226, 230, 0.3); color: var(--text-secondary); border: 1px solid var(--color-gris-claro);';
    }
}
?>

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