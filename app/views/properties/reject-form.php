<?php
/**
 * Vista de Formulario de Rechazo de Propiedad
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 */

require_once APP_PATH . '/helpers/PropertyHelper.php';
require_once APP_PATH . '/views/layouts/main.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-danger text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-times"></i>
                            Rechazar Propiedad
                        </h4>
                        <a href="/properties/pending-validation" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Información de la propiedad -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <?php if ($property['imagen_principal']): ?>
                                <img src="<?= htmlspecialchars($property['imagen_principal']) ?>" 
                                     class="img-fluid rounded" 
                                     alt="<?= htmlspecialchars($property['titulo']) ?>">
                            <?php else: ?>
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                     style="height: 200px;">
                                    <i class="fas fa-home fa-3x text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-8">
                            <h5 class="card-title"><?= htmlspecialchars($property['titulo']) ?></h5>
                            
                            <div class="row mb-3">
                                <div class="col-6">
                                    <small class="text-muted">Tipo</small>
                                    <div class="fw-bold"><?= getPropertyTypeDisplayName($property['tipo']) ?></div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Precio</small>
                                    <div class="fw-bold text-primary">$<?= number_format($property['precio'], 2) ?></div>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-6">
                                    <small class="text-muted">Ubicación</small>
                                    <div class="fw-bold">
                                        <?= htmlspecialchars($property['ciudad']) ?>, <?= htmlspecialchars($property['sector']) ?>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Metros²</small>
                                    <div class="fw-bold"><?= $property['metros_cuadrados'] ?> m²</div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-4">
                                    <small class="text-muted">Habitaciones</small>
                                    <div class="fw-bold"><?= $property['habitaciones'] ?></div>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted">Baños</small>
                                    <div class="fw-bold"><?= $property['banos'] ?></div>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted">Estacionamientos</small>
                                    <div class="fw-bold"><?= $property['estacionamientos'] ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Información del cliente -->
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="fas fa-user"></i> Cliente Vendedor
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong><?= htmlspecialchars($property['cliente_nombre'] . ' ' . $property['cliente_apellido']) ?></strong><br>
                                <small class="text-muted">
                                    <i class="fas fa-envelope"></i> <?= htmlspecialchars($property['cliente_email']) ?>
                                </small>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <i class="fas fa-phone"></i> <?= htmlspecialchars($property['cliente_telefono']) ?>
                                </small><br>
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i> Creada: <?= date('d/m/Y H:i', strtotime($property['fecha_creacion'])) ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Formulario de rechazo -->
                    <form action="/properties/reject/<?= $property['id'] ?>" method="POST">
                        <div class="mb-3">
                            <label for="motivo" class="form-label">
                                Motivo del rechazo <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" 
                                      id="motivo" 
                                      name="motivo" 
                                      rows="6"
                                      required
                                      placeholder="Explica detalladamente el motivo del rechazo. Esta información será visible para el cliente vendedor..."></textarea>
                            <div class="form-text">
                                <i class="fas fa-info-circle"></i>
                                Este motivo será visible para el cliente vendedor y se registrará en el sistema.
                            </div>
                        </div>
                        
                        <!-- Motivos comunes (opcionales) -->
                        <div class="mb-3">
                            <label class="form-label">Motivos comunes (haz clic para agregar):</label>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm" 
                                        onclick="addMotivo('Información incompleta o incorrecta')">
                                    Información incompleta
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" 
                                        onclick="addMotivo('Precio no acorde al mercado')">
                                    Precio no acorde
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" 
                                        onclick="addMotivo('Falta documentación legal')">
                                    Falta documentación
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" 
                                        onclick="addMotivo('Propiedad no cumple estándares de calidad')">
                                    No cumple estándares
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" 
                                        onclick="addMotivo('Ubicación no disponible para venta')">
                                    Ubicación no disponible
                                </button>
                            </div>
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Importante:</strong> Esta acción no se puede deshacer. Una vez rechazada, 
                            la propiedad no aparecerá en el listado público y el cliente será notificado.
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="/properties/pending-validation" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-times"></i> Rechazar Propiedad
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Función para agregar motivos comunes al textarea
function addMotivo(motivo) {
    const textarea = document.getElementById('motivo');
    const currentValue = textarea.value;
    
    if (currentValue.trim() === '') {
        textarea.value = motivo;
    } else {
        textarea.value = currentValue + '\n\n' + motivo;
    }
    
    // Auto-resize
    textarea.style.height = 'auto';
    textarea.style.height = textarea.scrollHeight + 'px';
    
    // Enfocar el textarea
    textarea.focus();
}

// Auto-resize del textarea
document.getElementById('motivo').addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = this.scrollHeight + 'px';
});

// Confirmación antes de enviar
document.querySelector('form').addEventListener('submit', function(e) {
    const motivo = document.getElementById('motivo').value.trim();
    
    if (motivo === '') {
        e.preventDefault();
        alert('Debes proporcionar un motivo para rechazar la propiedad.');
        return;
    }
    
    if (!confirm('¿Estás seguro de que quieres rechazar esta propiedad? Esta acción no se puede deshacer.')) {
        e.preventDefault();
    }
});
</script> 
