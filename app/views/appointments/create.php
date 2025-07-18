<?php
// Vista: Crear Nueva Cita
// El controlador ya maneja la captura de contenido, no necesitamos ob_start() aquí
?>

<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Crear Nueva Cita</h1>
        
        <form method="POST" action="/appointments/store" class="space-y-6">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            
            <!-- Solicitud -->
            <div>
                <label for="solicitud_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Solicitud <span class="text-red-500">*</span>
                </label>
                <select name="solicitud_id" id="solicitud_id" required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Seleccionar solicitud...</option>
                    <?php if (!empty($solicitudes)): ?>
                        <?php foreach ($solicitudes as $solicitud): ?>
                            <?php 
                            $propiedad = $this->propertyModel->getById($solicitud['propiedad_id']);
                            $cliente = $this->userModel->getById($solicitud['cliente_id']);
                            $selected = ($solicitud && $solicitud['id'] == $solicitud['id']) ? 'selected' : '';
                            ?>
                            <option value="<?= $solicitud['id'] ?>" <?= $selected ?>>
                                Solicitud #<?= $solicitud['id'] ?> - 
                                <?= htmlspecialchars($cliente['nombre'] ?? 'Cliente') ?> - 
                                <?= htmlspecialchars($propiedad['titulo'] ?? 'Propiedad') ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Fecha -->
            <div>
                <label for="fecha_cita" class="block text-sm font-medium text-gray-700 mb-2">
                    Fecha <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="date" name="fecha_cita" id="fecha_cita" required
                           min="<?= date('Y-m-d') ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <i class="fas fa-calendar text-gray-400"></i>
                    </div>
                </div>
            </div>

            <!-- Hora -->
            <div>
                <label for="hora_cita" class="block text-sm font-medium text-gray-700 mb-2">
                    Hora <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="time" name="hora_cita" id="hora_cita" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <i class="fas fa-clock text-gray-400"></i>
                    </div>
                </div>
            </div>

            <!-- Lugar -->
            <div>
                <label for="lugar" class="block text-sm font-medium text-gray-700 mb-2">
                    Lugar <span class="text-red-500">*</span>
                </label>
                <input type="text" name="lugar" id="lugar" required
                       placeholder="Ej: Oficina principal, Propiedad, etc."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>

            <!-- Tipo de Cita -->
            <div>
                <label for="tipo_cita" class="block text-sm font-medium text-gray-700 mb-2">
                    Tipo de Cita <span class="text-red-500">*</span>
                </label>
                <select name="tipo_cita" id="tipo_cita" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Seleccionar tipo...</option>
                    <option value="visita_propiedad">Visita a la propiedad</option>
                    <option value="reunion_oficina">Reunión en oficina</option>
                    <option value="video_llamada">Videollamada</option>
                    <option value="llamada_telefonica">Llamada telefónica</option>
                    <option value="firma_documentos">Firma de documentos</option>
                    <option value="otro">Otro</option>
                </select>
            </div>

            <!-- Observaciones -->
            <div>
                <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">
                    Observaciones
                </label>
                <textarea name="observaciones" id="observaciones" rows="4"
                          placeholder="Detalles adicionales sobre la cita..."
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-4 pt-6">
                <a href="/appointments" 
                   class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    Crear Cita
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-seleccionar solicitud si se proporciona en la URL
    const urlParams = new URLSearchParams(window.location.search);
    const solicitudId = urlParams.get('solicitud_id');
    if (solicitudId) {
        const select = document.getElementById('solicitud_id');
        select.value = solicitudId;
    }
    
    // Validación de fecha y hora
    const fechaInput = document.getElementById('fecha_cita');
    const horaInput = document.getElementById('hora_cita');
    
    fechaInput.addEventListener('change', function() {
        const fecha = this.value;
        const hora = horaInput.value;
        
        if (fecha && hora) {
            const fechaHora = new Date(fecha + 'T' + hora);
            const ahora = new Date();
            
            if (fechaHora <= ahora) {
                alert('La fecha y hora deben ser futuras.');
                this.value = '';
                horaInput.value = '';
            }
        }
    });
    
    horaInput.addEventListener('change', function() {
        const fecha = fechaInput.value;
        const hora = this.value;
        
        if (fecha && hora) {
            const fechaHora = new Date(fecha + 'T' + hora);
            const ahora = new Date();
            
            if (fechaHora <= ahora) {
                alert('La fecha y hora deben ser futuras.');
                fechaInput.value = '';
                this.value = '';
            }
        }
    });
});
</script> 