<?php
/**
 * Contenido de Vista de Propiedad - Administrador
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este archivo contiene solo el contenido para ver una propiedad individual, sin estructura HTML completa
 */

// El rol ya fue verificado en el AdminController
require_once APP_PATH . '/helpers/PropertyHelper.php';
?>

<!-- Información de la Propiedad -->
<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="fas fa-home"></i> Detalles de la Propiedad
        </h4>
        <div>
            <a href="/admin/properties" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="property-images mb-4">
                <?php if (!empty($property['imagen_principal'])): ?>
                    <img src="<?= htmlspecialchars($property['imagen_principal']) ?>" 
                         alt="Imagen principal" class="img-fluid rounded" style="max-height: 400px; width: 100%; object-fit: cover;">
                <?php elseif (!empty($property['imagenes']) && is_array($property['imagenes']) && count($property['imagenes']) > 0): ?>
                    <img src="<?= htmlspecialchars($property['imagenes'][0]['ruta']) ?>" 
                         alt="Imagen principal" class="img-fluid rounded" style="max-height: 400px; width: 100%; object-fit: cover;">
                <?php else: ?>
                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 400px;">
                        <i class="fas fa-home fa-4x text-muted"></i>
                    </div>
                <?php endif; ?>
                
                <!-- Galería de imágenes adicionales -->
                <?php if (!empty($property['imagenes']) && is_array($property['imagenes']) && count($property['imagenes']) > 1): ?>
                    <div class="mt-3">
                        <h6>Galería de Imágenes</h6>
                        <div class="row">
                            <?php foreach ($property['imagenes'] as $index => $imagen): ?>
                                <?php if ($index < 6): // Mostrar máximo 6 imágenes ?>
                                    <div class="col-md-2 col-4 mb-2">
                                        <img src="<?= htmlspecialchars($imagen['ruta']) ?>" 
                                             alt="Imagen <?= $index + 1 ?>" 
                                             class="img-fluid rounded" 
                                             style="height: 80px; width: 100%; object-fit: cover; cursor: pointer;"
                                             onclick="showImageModal('<?= htmlspecialchars($imagen['ruta']) ?>', '<?= htmlspecialchars($property['titulo'] ?? '') ?>')">
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($property['imagenes']) > 6): ?>
                            <small class="text-muted">+<?= count($property['imagenes']) - 6 ?> imágenes más</small>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="property-details">
                <h3><?= htmlspecialchars($property['titulo'] ?? 'N/A') ?></h3>
                <p class="text-muted"><?= htmlspecialchars($property['direccion'] ?? 'N/A') ?></p>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <strong>Precio:</strong> $<?= number_format($property['precio'] ?? 0) ?>
                    </div>
                    <div class="col-md-6">
                        <strong>Tipo:</strong> 
                        <span class="type-badge type-<?= $property['tipo'] ?? 'N/A' ?>">
                            <?= getPropertyTypeDisplayName($property['tipo'] ?? 'N/A') ?>
                        </span>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <strong>Estado:</strong> 
                        <span class="status-badge status-<?= $property['estado_propiedad'] ?? 'N/A' ?>">
                            <?= ucfirst(str_replace('_', ' ', $property['estado_propiedad'] ?? 'N/A')) ?>
                        </span>
                    </div>
                    <div class="col-md-6">
                        <strong>Fecha de Creación:</strong> <?= isset($property['fecha_creacion']) ? date('d/m/Y H:i', strtotime($property['fecha_creacion'])) : 'N/A' ?>
                    </div>
                </div>

                <div class="property-description mb-4">
                    <h5>Descripción</h5>
                    <p><?= nl2br(htmlspecialchars($property['descripcion'] ?? '')) ?></p>
                </div>

                <div class="property-features mb-4">
                    <h5>Características</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Habitaciones:</strong> <?= $property['habitaciones'] ?? 'N/A' ?>
                        </div>
                        <div class="col-md-3">
                            <strong>Baños:</strong> <?= $property['banos'] ?? 'N/A' ?>
                        </div>
                        <div class="col-md-3">
                            <strong>Área:</strong> <?= $property['metros_cuadrados'] ?? 'N/A' ?> m²
                        </div>
                        <div class="col-md-3">
                            <strong>Estacionamientos:</strong> <?= $property['estacionamientos'] ?? 'N/A' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="property-sidebar">
                <!-- Información del Agente -->
                <div class="content-card mb-4">
                    <h5>
                        <i class="fas fa-user"></i> Información del Agente
                    </h5>
                    <div class="agent-info">
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <i class="fas fa-user-circle fa-2x text-muted"></i>
                            </div>
                            <div>
                                <strong><?= htmlspecialchars($property['agente_nombre'] ?? 'N/A') ?></strong>
                                <br>
                                <small class="text-muted"><?= htmlspecialchars($property['agente_email'] ?? 'N/A') ?></small>
                            </div>
                        </div>
                        <div class="agent-stats">
                            <small>
                                <strong>Propiedades:</strong> <?= $property['agente_propiedades_count'] ?? 0 ?><br>
                                <strong>Miembro desde:</strong> <?= !empty($property['agente_fecha_registro']) ? date('d/m/Y', strtotime($property['agente_fecha_registro'])) : 'N/A' ?>
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Acciones Administrativas -->
                <div class="content-card mb-4">
                    <h5>
                        <i class="fas fa-cogs"></i> Acciones Administrativas
                    </h5>
                    <div class="d-grid gap-2">
                        <?php if (($property['estado_propiedad'] ?? '') === 'en_revision'): ?>
                            <button type="button" class="btn btn-success" onclick="approveProperty(<?= $property['id'] ?? 0 ?>)">
                                <i class="fas fa-check"></i> Aprobar Propiedad
                            </button>
                            <button type="button" class="btn btn-danger" onclick="rejectProperty(<?= $property['id'] ?? 0 ?>, '<?= htmlspecialchars($property['titulo'] ?? '') ?>')">
                                <i class="fas fa-times"></i> Rechazar Propiedad
                            </button>
                        <?php endif; ?>
                        
                        <?php if (($property['estado_propiedad'] ?? '') === 'activa'): ?>
                            <button type="button" class="btn btn-warning" onclick="featureProperty(<?= $property['id'] ?? 0 ?>)">
                                <i class="fas fa-star"></i> Destacar Propiedad
                            </button>
                        <?php endif; ?>
                        
                        <button type="button" class="btn btn-danger" onclick="deleteProperty(<?= $property['id'] ?? 0 ?>, '<?= htmlspecialchars($property['titulo'] ?? '') ?>')">
                            <i class="fas fa-trash"></i> Eliminar Propiedad
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para mostrar imagen en tamaño completo -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Imagen" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<!-- Modal para Rechazar Propiedad -->
<div class="modal fade" id="rejectPropertyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-times-circle text-danger"></i> Rechazar Propiedad
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectPropertyForm" method="POST" action="/admin/properties?action=reject">
                <div class="modal-body">
                    <input type="hidden" id="rejectPropertyId" name="property_id">
                    <p>Rechazar propiedad: <strong id="rejectPropertyTitle"></strong></p>
                    <div class="mb-3">
                        <label for="rejectReason" class="form-label">Motivo del Rechazo:</label>
                        <textarea class="form-control" id="rejectReason" name="motivo" rows="4" required 
                                  placeholder="Especifica el motivo del rechazo..."></textarea>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Advertencia:</strong> Esta acción notificará al agente sobre el rechazo de su propiedad.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Rechazar Propiedad</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts específicos -->
<script>
    function showImageModal(imageSrc, title) {
        document.getElementById('modalImage').src = imageSrc;
        document.getElementById('imageModalTitle').textContent = title || 'Imagen de la Propiedad';
        new bootstrap.Modal(document.getElementById('imageModal')).show();
    }

    function approveProperty(propertyId) {
        if (confirm('¿Estás seguro de que deseas aprobar esta propiedad?')) {
            window.location.href = '/admin/properties?action=approve&id=' + propertyId;
        }
    }

    function rejectProperty(propertyId, propertyTitle) {
        document.getElementById('rejectPropertyId').value = propertyId;
        document.getElementById('rejectPropertyTitle').textContent = propertyTitle;
        new bootstrap.Modal(document.getElementById('rejectPropertyModal')).show();
    }

    function featureProperty(propertyId) {
        if (confirm('¿Estás seguro de que deseas destacar esta propiedad?')) {
            window.location.href = '/admin/properties?action=feature&id=' + propertyId;
        }
    }

    function deleteProperty(propertyId, propertyTitle) {
        if (confirm('¿Estás seguro de que deseas eliminar la propiedad "' + propertyTitle + '"? Esta acción no se puede deshacer.')) {
            window.location.href = '/admin/properties?action=delete&id=' + propertyId;
        }
    }
</script> 
