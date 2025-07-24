<?php
/**
 * Contenido de Gestión de Propiedades - Administrador
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este archivo contiene solo el contenido de gestión de propiedades, sin estructura HTML completa
 */

// El rol ya fue verificado en el AdminController
require_once APP_PATH . '/helpers/PropertyHelper.php';
?>

<!-- Resumen de Estadísticas -->
<div class="stats-summary">
    <div class="row text-center">
        <div class="col-md-3">
            <h3><?= number_format($totalProperties ?? 0) ?></h3>
            <p class="mb-0">Total Propiedades</p>
        </div>
        <div class="col-md-3">
            <h3><?= number_format($activeProperties ?? 0) ?></h3>
            <p class="mb-0">Activas</p>
        </div>
        <div class="col-md-3">
            <h3><?= number_format($reviewProperties ?? 0) ?></h3>
            <p class="mb-0">En Revisión</p>
        </div>
        <div class="col-md-3">
            <h3><?= number_format($soldProperties ?? 0) ?></h3>
            <p class="mb-0">Vendidas</p>
        </div>
    </div>
</div>

<!-- Filtros y Búsqueda -->
<div class="filter-section">
    <form id="filterForm" method="GET" action="/admin/properties">
        <div class="row align-items-end">
            <div class="col-md-2">
                <label for="statusFilter" class="form-label">Estado:</label>
                <select class="form-select" id="statusFilter" name="status">
                    <option value="">Todos los estados</option>
                    <option value="activa" <?= ($_GET['status'] ?? '') === 'activa' ? 'selected' : '' ?>>Activas</option>
                    <option value="en_revision" <?= ($_GET['status'] ?? '') === 'en_revision' ? 'selected' : '' ?>>En Revisión</option>
                    <option value="vendida" <?= ($_GET['status'] ?? '') === 'vendida' ? 'selected' : '' ?>>Vendidas</option>
                    <option value="rechazada" <?= ($_GET['status'] ?? '') === 'rechazada' ? 'selected' : '' ?>>Rechazadas</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="typeFilter" class="form-label">Tipo:</label>
                <select class="form-select" id="typeFilter" name="type">
                    <option value="">Todos los tipos</option>
                    <?php 
                    $propertyTypes = getUniquePropertyTypes();
                    foreach ($propertyTypes as $value => $label): 
                    ?>
                        <option value="<?= $value ?>" <?= ($_GET['type'] ?? '') === $value ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="cityFilter" class="form-label">Ciudad:</label>
                <select class="form-select" id="cityFilter" name="city">
                    <option value="">Todas las ciudades</option>
                    <?php 
                    $cities = array_unique(array_column($properties, 'ciudad'));
                    foreach ($cities as $city): 
                    ?>
                        <option value="<?= htmlspecialchars($city) ?>" <?= ($_GET['city'] ?? '') === $city ? 'selected' : '' ?>><?= htmlspecialchars($city) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="searchProperty" class="form-label">Buscar:</label>
                <input type="text" class="form-control" id="searchProperty" name="search" placeholder="Título, dirección..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </div>
            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <a href="/admin/properties" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Lista de Propiedades -->
<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="fas fa-home"></i> Lista de Propiedades
        </h4>
        <div>
            <button class="btn btn-success" onclick="exportProperties()">
                <i class="fas fa-download"></i> Exportar
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover" id="propertiesTable">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Imagen</th>
                    <th>Propiedad</th>
                    <th>Precio</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th>Agente</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($properties)): ?>
                    <?php foreach ($properties as $property): ?>
                        <tr class="property-row <?= ($property['estado'] ?? 'activa') === 'rechazada' ? 'table-danger' : (($property['estado'] ?? 'activa') === 'en_revision' ? 'table-warning' : '') ?>">
                            <td><?= $property['id'] ?></td>
                            <td>
                                <?php 
                                $imagenPath = '';
                                if (!empty($property['imagen_principal'])) {
                                    $imagenPath = '/uploads/properties/' . $property['imagen_principal'];
                                } elseif (!empty($property['imagen'])) {
                                    $imagenPath = '/uploads/properties/' . $property['imagen'];
                                } elseif (!empty($property['foto'])) {
                                    $imagenPath = '/uploads/properties/' . $property['foto'];
                                }
                                
                        
                                if (empty($imagenPath) && !empty($property['id'])) {
                                    // Intentar obtener la primera imagen disponible
                                    $imagenPath = '/uploads/properties/687fb18eecc97_17531989909699.webp';
                                }
                                ?>
                                
                                <?php if (!empty($imagenPath)): ?>
                                    <img src="<?= htmlspecialchars($imagenPath) ?>" 
                                         alt="Imagen de propiedad" 
                                         class="property-thumbnail" 
                                         style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="property-thumbnail-placeholder" 
                                         style="width: 60px; height: 40px; background: #f8f9fa; border-radius: 4px; display: none; align-items: center; justify-content: center;">
                                        <i class="fas fa-home text-muted"></i>
                                    </div>
                                <?php else: ?>
                                    <div class="property-thumbnail-placeholder" 
                                         style="width: 60px; height: 40px; background: #f8f9fa; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-home text-muted"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div>
                                    <strong><?= htmlspecialchars($property['titulo']) ?></strong>
                                    <br>
                                    <small class="text-muted"><?= htmlspecialchars($property['direccion']) ?></small>
                                </div>
                            </td>
                            <td>
                                <strong class="text-success">$<?= number_format($property['precio']) ?></strong>
                            </td>
                            <td>
                                <span class="type-badge type-<?= $property['tipo'] ?>">
                                    <?= getPropertyTypeDisplayName($property['tipo']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-<?= $property['estado'] ?? 'activa' ?>">
                                    <?= ucfirst(str_replace('_', ' ', $property['estado'] ?? 'activa')) ?>
                                </span>
                            </td>
                            <td>
                                <small><?= htmlspecialchars($property['agente_nombre'] ?? $property['agente_id'] ?? 'N/A') ?></small>
                            </td>
                            <td><?= isset($property['fecha_creacion']) ? date('d/m/Y', strtotime($property['fecha_creacion'])) : 'N/A' ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            onclick="viewProperty(<?= $property['id'] ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    
                                    <button type="button" class="btn btn-sm btn-outline-warning" 
                                            onclick="toggleFavorite(<?= $property['id'] ?>, '<?= htmlspecialchars($property['titulo'] ?? 'Propiedad') ?>')"
                                            title="Agregar/Quitar de Favoritos">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                    
                                    <?php if (($property['estado'] ?? 'activa') === 'en_revision'): ?>
                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                onclick="approveProperty(<?= $property['id'] ?>, '<?= htmlspecialchars($property['titulo'] ?? 'Propiedad') ?>')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="rejectProperty(<?= $property['id'] ?>, '<?= htmlspecialchars($property['titulo'] ?? 'Propiedad') ?>')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php endif; ?>
                                    
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            onclick="deleteProperty(<?= $property['id'] ?>, '<?= htmlspecialchars($property['titulo'] ?? 'Propiedad') ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <i class="fas fa-home fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay propiedades registradas</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Sección de Propiedades Favoritas -->
<div class="content-card mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">
            <i class="fas fa-heart text-danger"></i> Propiedades Favoritas
        </h5>
        <span class="badge bg-danger">0</span>
    </div>
    
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        <strong>Información:</strong> Las propiedades marcadas como favoritas aparecerán aquí para acceso rápido.
        <br>
        <small class="text-muted">Haz clic en el botón <i class="fas fa-heart"></i> de cualquier propiedad para agregarla a favoritos.</small>
    </div>
    
    <div class="row" id="favoritesContainer">
        <div class="col-12 text-center py-4">
            <i class="fas fa-heart fa-3x text-muted mb-3"></i>
            <p class="text-muted">No hay propiedades favoritas</p>
            <small class="text-muted">Las propiedades que marques como favoritas aparecerán aquí</small>
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
    // Inicializar DataTable
    $(document).ready(function() {
        $('#propertiesTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            },
            pageLength: 25,
            order: [[0, 'desc']]
        });
        
        // Cargar favoritos al inicializar
        loadFavorites();
    });

    function loadFavorites() {
        fetch('/admin/favorites')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateFavoritesSection(data.favorites, data.total_favorites);
            }
        })
        .catch(error => {
            console.error('Error al cargar favoritos:', error);
        });
    }

    // Funciones de gestión de propiedades
    function viewProperty(propertyId) {
        window.location.href = `/admin/properties?action=view&id=${propertyId}`;
    }

    function approveProperty(propertyId, propertyTitle) {
        if (confirm(`¿Estás seguro de que quieres APROBAR la propiedad "${propertyTitle}"?\n\nEsta acción hará que la propiedad sea visible para los clientes.`)) {
            window.location.href = `/admin/properties?action=approve&id=${propertyId}`;
        }
    }

    function rejectProperty(propertyId, propertyTitle) {
        document.getElementById('rejectPropertyId').value = propertyId;
        document.getElementById('rejectPropertyTitle').textContent = propertyTitle;
        document.getElementById('rejectReason').value = '';
        
        const modal = new bootstrap.Modal(document.getElementById('rejectPropertyModal'));
        modal.show();
    }

    function featureProperty(propertyId, propertyTitle) {
        if (confirm(`¿Estás seguro de que quieres DESTACAR la propiedad "${propertyTitle}"?\n\nEsta propiedad aparecerá en posiciones destacadas.`)) {
            window.location.href = `/admin/properties?action=feature&id=${propertyId}`;
        }
    }

    function deleteProperty(propertyId, propertyTitle) {
        if (confirm(`¿Estás seguro de que quieres ELIMINAR PERMANENTEMENTE la propiedad "${propertyTitle}"?\n\n⚠️ ESTA ACCIÓN NO SE PUEDE DESHACER ⚠️\n\nSe eliminarán todas las imágenes y datos asociados.`)) {
            if (confirm('¿CONFIRMAS la eliminación? Esta es tu última oportunidad para cancelar.')) {
                window.location.href = `/admin/properties?action=delete&id=${propertyId}`;
            }
        }
    }

    function exportProperties() {
        // Construir URL de exportación con filtros actuales
        const currentUrl = new URL(window.location);
        const params = new URLSearchParams(currentUrl.search);
        params.set('action', 'export');
        
        // Redirigir a la exportación
        window.location.href = '/admin/properties?' + params.toString();
    }

    function toggleFavorite(propertyId, propertyTitle) {
        // Mostrar indicador de carga
        const button = event.target.closest('button');
        const originalIcon = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        button.disabled = true;
        
        // Realizar petición AJAX
        fetch('/admin/favorites/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `property_id=${propertyId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar el botón
                if (data.is_favorite) {
                    button.classList.remove('btn-outline-warning');
                    button.classList.add('btn-warning');
                    button.innerHTML = '<i class="fas fa-heart"></i>';
                } else {
                    button.classList.remove('btn-warning');
                    button.classList.add('btn-outline-warning');
                    button.innerHTML = '<i class="fas fa-heart"></i>';
                }
                
                // Actualizar la sección de favoritos
                updateFavoritesSection(data.favorites, data.total_favorites);
                
                // Mostrar mensaje de éxito
                showNotification(data.message, 'success');
            } else {
                // Restaurar botón original
                button.innerHTML = originalIcon;
                button.disabled = false;
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            button.innerHTML = originalIcon;
            button.disabled = false;
            showNotification('Error al procesar la solicitud', 'error');
        });
    }

    function updateFavoritesSection(favorites, totalFavorites) {
        const container = document.getElementById('favoritesContainer');
        const badge = document.querySelector('.content-card .badge');
        
        // Actualizar contador
        if (badge) {
            badge.textContent = totalFavorites;
        }
        
        if (totalFavorites === 0) {
            container.innerHTML = `
                <div class="col-12 text-center py-4">
                    <i class="fas fa-heart fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No hay propiedades favoritas</p>
                    <small class="text-muted">Las propiedades que marques como favoritas aparecerán aquí</small>
                </div>
            `;
        } else {
            let html = '';
            favorites.forEach(favorite => {
                html += `
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="card-title mb-0">${favorite.titulo}</h6>
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            onclick="removeFavorite(${favorite.favorito_id}, ${favorite.propiedad_id})">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <p class="card-text small text-muted">${favorite.direccion}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-primary fw-bold">$${parseInt(favorite.precio).toLocaleString()}</span>
                                    <span class="badge bg-secondary">${getPropertyTypeDisplayName(favorite.tipo)}</span>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">Agregado: ${new Date(favorite.fecha_agregado).toLocaleDateString()}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        }
    }

    function removeFavorite(favoriteId, propertyId) {
        if (confirm('¿Estás seguro de que quieres quitar esta propiedad de favoritos?')) {
            fetch('/admin/favorites/remove', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `favorite_id=${favoriteId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateFavoritesSection(data.favorites, data.total_favorites);
                    showNotification(data.message, 'success');
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error al quitar de favoritos', 'error');
            });
        }
    }

    function showNotification(message, type = 'info') {
        // Crear notificación
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Remover automáticamente después de 5 segundos
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }

    function getPropertyTypeDisplayName(type) {
        const typeMap = {
            'casa': 'Casa',
            'apartamento': 'Apartamento',
            'terreno': 'Terreno',
            'comercial': 'Local Comercial',
            'local_comercial': 'Local Comercial',
            'oficina': 'Oficina',
            'bodega': 'Bodega',
            'estacionamiento': 'Estacionamiento'
        };
        
        return typeMap[type] || type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
    }

    // Mostrar indicador de filtros activos
    $(document).ready(function() {
        const urlParams = new URLSearchParams(window.location.search);
        const hasFilters = urlParams.has('status') || urlParams.has('type') || urlParams.has('city') || urlParams.has('search');
        
        if (hasFilters) {
            // Agregar indicador visual de filtros activos
            const filterIndicator = $('<div class="alert alert-info alert-dismissible fade show mt-3" role="alert">' +
                '<i class="fas fa-filter"></i> <strong>Filtros activos:</strong> ' +
                '<span id="activeFilters"></span>' +
                '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                '</div>');
            
            $('.filter-section').after(filterIndicator);
            
            // Mostrar filtros activos
            let activeFilters = [];
            if (urlParams.get('status')) activeFilters.push('Estado: ' + $('#statusFilter option:selected').text());
            if (urlParams.get('type')) activeFilters.push('Tipo: ' + $('#typeFilter option:selected').text());
            if (urlParams.get('city')) activeFilters.push('Ciudad: ' + $('#cityFilter option:selected').text());
            if (urlParams.get('search')) activeFilters.push('Búsqueda: "' + urlParams.get('search') + '"');
            
            $('#activeFilters').text(activeFilters.join(', '));
        }
    });
</script> 
