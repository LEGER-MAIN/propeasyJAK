<?php
// Vista: Editar Cita
// El controlador ya maneja la captura de contenido, no necesitamos ob_start() aquí

// Generar token CSRF
$csrfToken = generateCSRFToken();
?>

<!-- Token CSRF oculto para JavaScript -->
<input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Editar Cita</h1>
                    <p class="text-gray-600 mt-2">Modifica los detalles de la cita</p>
                </div>
                <a href="/appointments/<?= $cita['id'] ?? '' ?>" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    Volver
                </a>
            </div>

            <?php if (isset($cita) && is_array($cita)): ?>
                <form action="/appointments/<?= $cita['id'] ?>/update" method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="cliente_nombre" class="block text-sm font-medium text-gray-700 mb-2">
                                Cliente
                            </label>
                            <input type="text" id="cliente_nombre" 
                                   value="<?= htmlspecialchars($cita['cliente_nombre'] ?? 'Cliente') ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50" 
                                   readonly>
                            <p class="text-sm text-gray-500 mt-1">El cliente no se puede cambiar</p>
                        </div>

                        <div>
                            <label for="fecha_cita" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha
                            </label>
                            <input type="date" name="fecha_cita" id="fecha_cita" required 
                                   value="<?= htmlspecialchars(date('Y-m-d', strtotime($cita['fecha_cita'] ?? ''))) ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="hora_cita" class="block text-sm font-medium text-gray-700 mb-2">
                                Hora
                            </label>
                            <input type="time" name="hora_cita" id="hora_cita" required 
                                   value="<?= htmlspecialchars(date('H:i', strtotime($cita['fecha_cita'] ?? ''))) ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="tipo_cita" class="block text-sm font-medium text-gray-700 mb-2">
                                Tipo de Cita
                            </label>
                            <select name="tipo_cita" id="tipo_cita" required 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccionar tipo</option>
                                <option value="consulta" <?= ($cita['tipo_cita'] === 'consulta') ? 'selected' : '' ?>>Consulta</option>
                                <option value="visita_propiedad" <?= ($cita['tipo_cita'] === 'visita_propiedad') ? 'selected' : '' ?>>Visita a Propiedad</option>
                                <option value="firma_documentos" <?= ($cita['tipo_cita'] === 'firma_documentos') ? 'selected' : '' ?>>Firma de Documentos</option>
                                <option value="negociacion" <?= ($cita['tipo_cita'] === 'negociacion') ? 'selected' : '' ?>>Negociación</option>
                                <option value="video_llamada" <?= ($cita['tipo_cita'] === 'video_llamada') ? 'selected' : '' ?>>Video Llamada</option>
                                <option value="reunion_oficina" <?= ($cita['tipo_cita'] === 'reunion_oficina') ? 'selected' : '' ?>>Reunión Oficina</option>
                                <option value="otro" <?= ($cita['tipo_cita'] === 'otro') ? 'selected' : '' ?>>Otro</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="lugar" class="block text-sm font-medium text-gray-700 mb-2">
                            Lugar
                        </label>
                        <input type="text" name="lugar" id="lugar" 
                               value="<?= htmlspecialchars($cita['lugar'] ?? '') ?>"
                               placeholder="Dirección o lugar de la cita"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">
                            Observaciones
                        </label>
                        <textarea name="observaciones" id="observaciones" rows="4" 
                                  placeholder="Información adicional sobre la cita..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($cita['observaciones'] ?? '') ?></textarea>
                    </div>

                    <div class="flex justify-end space-x-4 pt-6">
                        <a href="/appointments/<?= $cita['id'] ?>" 
                           class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            Actualizar Cita
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div class="text-center py-12">
                    <div class="mx-auto h-12 w-12 text-gray-400 mb-4">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Cita no encontrada</h3>
                    <p class="text-gray-500 mb-6">La cita que intentas editar no existe o ha sido eliminada.</p>
                    <a href="/appointments" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Volver a Mis Citas
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

 
