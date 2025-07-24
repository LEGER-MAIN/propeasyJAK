<?php
/**
 * Gestión Completa de Propiedades - Administrador
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Vista para gestionar propiedades con control total (aprobar, rechazar, eliminar, destacar)
 */

// Verificar que el usuario sea administrador
if (!hasRole(ROLE_ADMIN)) {
    redirect('/dashboard');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        :root {
            --admin-primary: #2c3e50;
            --admin-secondary: #34495e;
            --admin-success: #27ae60;
            --admin-warning: #f39c12;
            --admin-danger: #e74c3c;
            --admin-info: #3498db;
        }
        
        body {
            background: #ecf0f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .admin-header {
            background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .admin-sidebar {
            background: white;
            min-height: 100vh;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            position: fixed;
            width: 250px;
            z-index: 1000;
        }
        
        .admin-content {
            margin-left: 250px;
            padding: 20px;
        }
        
        .nav-link {
            color: var(--admin-primary);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 5px 10px;
            transition: all 0.3s ease;
            border: none;
        }
        
        .nav-link:hover, .nav-link.active {
            background: var(--admin-primary);
            color: white;
            transform: translateX(5px);
        }
        
        .content-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .property-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid var(--admin-primary);
            transition: transform 0.3s ease;
        }
        
        .property-card:hover {
            transform: translateY(-2px);
        }
        
        .property-card.pending {
            border-left-color: var(--admin-warning);
        }
        
        .property-card.approved {
            border-left-color: var(--admin-success);
        }
        
        .property-card.rejected {
            border-left-color: var(--admin-danger);
        }
        
        .property-card.featured {
            border-left-color: var(--admin-info);
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-activa {
            background: var(--admin-success);
            color: white;
        }
        
        .status-en_revision {
            background: var(--admin-warning);
            color: white;
        }
        
        .status-rechazada {
            background: var(--admin-danger);
            color: white;
        }
        
        .status-vendida {
            background: var(--admin-info);
            color: white;
        }
        
        .type-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .type-casa {
            background: var(--admin-primary);
            color: white;
        }
        
        .type-apartamento {
            background: var(--admin-info);
            color: white;
        }
        
        .type-terreno {
            background: var(--admin-success);
            color: white;
        }
        
        .type-oficina {
            background: var(--admin-warning);
            color: white;
        }
        
        .action-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            margin: 2px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .action-btn:hover {
            transform: translateY(-1px);
            color: white;
        }
        
        .btn-edit {
            background: var(--admin-info);
            color: white;
        }
        
        .btn-approve {
            background: var(--admin-success);
            color: white;
        }
        
        .btn-reject {
            background: var(--admin-warning);
            color: white;
        }
        
        .btn-delete {
            background: var(--admin-danger);
            color: white;
        }
        
        .btn-feature {
            background: var(--admin-primary);
            color: white;
        }
        
        .stats-summary {
            background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .filter-section {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .property-image {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .price-highlight {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--admin-success);
        }
        
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .admin-sidebar.show {
                transform: translateX(0);
            }
            
            .admin-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="admin-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="mb-0">
                        <i class="fas fa-home"></i> Gestión de Propiedades
                    </h1>
                    <small>Control total de propiedades del sistema</small>
                </div>
                <div class="col-md-6 text-end">
                    <div class="d-flex justify-content-end align-items-center">
                        <a href="/admin/dashboard" class="btn btn-outline-light btn-sm me-2">
                            <i class="fas fa-arrow-left"></i> Volver al Dashboard
                        </a>
                        <a href="/logout" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 admin-sidebar">
                <div class="p-3">
                    <h5 class="mb-4">
                        <i class="fas fa-cogs"></i> Control Panel
                    </h5>
                    
                    <nav class="nav flex-column">
                        <a class="nav-link" href="/admin/dashboard">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a class="nav-link" href="/admin/users?action=list">
                            <i class="fas fa-users"></i> Gestión de Usuarios
                        </a>
                        <a class="nav-link active" href="/admin/properties?action=list">
                            <i class="fas fa-home"></i> Gestión de Propiedades
                        </a>
                        <a class="nav-link" href="/admin/reports?action=list">
                            <i class="fas fa-flag"></i> Gestión de Reportes
                        </a>

                        <hr>
                        <a class="nav-link" href="/dashboard">
                            <i class="fas fa-home"></i> Volver al Sistema
                        </a>
                    </nav>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 admin-content">
                <!-- Resumen de Estadísticas -->
                <div class="stats-summary">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <h3><?= number_format(count($properties)) ?></h3>
                            <p class="mb-0">Total Propiedades</p>
                        </div>
                        <div class="col-md-3">
                            <h3><?= number_format(array_filter($properties, fn($p) => $p['estado'] === 'activa')->count()) ?></h3>
                            <p class="mb-0">Activas</p>
                        </div>
                        <div class="col-md-3">
                            <h3><?= number_format(array_filter($properties, fn($p) => $p['estado'] === 'en_revision')->count()) ?></h3>
                            <p class="mb-0">En Revisión</p>
                        </div>
                        <div class="col-md-3">
                            <h3><?= number_format(array_filter($properties, fn($p) => $p['estado'] === 'vendida')->count()) ?></h3>
                            <p class="mb-0">Vendidas</p>
                        </div>
                    </div>
                </div>

                <!-- Filtros y Búsqueda -->
                <div class="filter-section">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <label for="statusFilter" class="form-label">Filtrar por Estado:</label>
                            <select class="form-select" id="statusFilter">
                                <option value="">Todos los estados</option>
                                <option value="activa">Activas</option>
                                <option value="en_revision">En Revisión</option>
                                <option value="rechazada">Rechazadas</option>
                                <option value="vendida">Vendidas</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="typeFilter" class="form-label">Filtrar por Tipo:</label>
                            <select class="form-select" id="typeFilter">
                                <option value="">Todos los tipos</option>
                                <option value="casa">Casa</option>
                                <option value="apartamento">Apartamento</option>
                                <option value="terreno">Terreno</option>
                                <option value="oficina">Oficina</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="cityFilter" class="form-label">Filtrar por Ciudad:</label>
                            <select class="form-select" id="cityFilter">
                                <option value="">Todas las ciudades</option>
                                <?php
                                $cities = array_unique(array_column($properties, 'ciudad'));
                                foreach ($cities as $city):
                                ?>
                                    <option value="<?= htmlspecialchars($city) ?>"><?= htmlspecialchars($city) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="searchProperty" class="form-label">Buscar Propiedad:</label>
                            <input type="text" class="form-control" id="searchProperty" placeholder="Título, descripción...">
                        </div>
                    </div>
                </div>

                <!-- Lista de Propiedades -->
                <div class="content-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">
                            <i class="fas fa-list"></i> Lista de Propiedades
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
                                        <tr class="property-row <?= $property['estado'] === 'en_revision' ? 'table-warning' : ($property['estado'] === 'rechazada' ? 'table-danger' : '') ?>">
                                            <td><?= $property['id'] ?></td>
                                            <td>
                                                <?php if (!empty($property['imagen_principal'])): ?>
                                                    <img src="/uploads/properties/<?= htmlspecialchars($property['imagen_principal']) ?>" 
                                                         alt="Imagen" class="property-image">
                                                <?php else: ?>
                                                    <div class="property-image bg-light d-flex align-items-center justify-content-center">
                                                        <i class="fas fa-home text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong><?= htmlspecialchars($property['titulo']) ?></strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        <i class="fas fa-map-marker-alt"></i> 
                                                        <?= htmlspecialchars($property['ciudad']) ?>, <?= htmlspecialchars($property['provincia']) ?>
                                                    </small>
                                                    <br>
                                                    <small class="text-muted">
                                                        <i class="fas fa-ruler-combined"></i> 
                                                        <?= number_format($property['metros_cuadrados']) ?> m²
                                                    </small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="price-highlight">
                                                    $<?= number_format($property['precio']) ?>
                                                </div>
                                                <?php if ($property['estado'] === 'vendida'): ?>
                                                    <small class="text-success">
                                                        <i class="fas fa-check-circle"></i> Vendida
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="type-badge type-<?= $property['tipo'] ?>">
                                                    <?= ucfirst($property['tipo']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="status-badge status-<?= $property['estado'] ?>">
                                                    <?= ucfirst(str_replace('_', ' ', $property['estado'])) ?>
                                                </span>
                                                <?php if ($property['destacada']): ?>
                                                    <br>
                                                    <small class="text-info">
                                                        <i class="fas fa-star"></i> Destacada
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong><?= htmlspecialchars($property['agente_nombre']) ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?= htmlspecialchars($property['agente_email']) ?></small>
                                                </div>
                                            </td>
                                            <td><?= date('d/m/Y H:i', strtotime($property['fecha_creacion'])) ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                                            onclick="viewProperty(<?= $property['id'] ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    
                                                    <?php if ($property['estado'] === 'en_revision'): ?>
                                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                                onclick="approveProperty(<?= $property['id'] ?>, '<?= htmlspecialchars($property['titulo']) ?>')">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                                                onclick="rejectProperty(<?= $property['id'] ?>, '<?= htmlspecialchars($property['titulo']) ?>')">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($property['estado'] === 'activa'): ?>
                                                        <button type="button" class="btn btn-sm btn-outline-info" 
                                                                onclick="featureProperty(<?= $property['id'] ?>, '<?= htmlspecialchars($property['titulo']) ?>')">
                                                            <i class="fas fa-star"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                    
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            onclick="deleteProperty(<?= $property['id'] ?>, '<?= htmlspecialchars($property['titulo']) ?>')">
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
            </div>
        </div>
    </div>

    <!-- Modal para Rechazar Propiedad -->
    <div class="modal fade" id="rejectPropertyModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-times-circle"></i> Rechazar Propiedad
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
                        <button type="submit" class="btn btn-warning">Rechazar Propiedad</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
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
        });

        // Funciones de gestión de propiedades
        function viewProperty(propertyId) {
            window.open(`/properties/show/${propertyId}`, '_blank');
        }

        function approveProperty(propertyId, propertyTitle) {
            if (confirm(`¿Estás seguro de que quieres APROBAR la propiedad "${propertyTitle}"?\n\nEsta acción hará que la propiedad sea visible públicamente.`)) {
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
            if (confirm(`¿Estás seguro de que quieres DESTACAR la propiedad "${propertyTitle}"?\n\nEsta propiedad aparecerá en posiciones destacadas del sitio.`)) {
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
            // Implementar exportación de propiedades
            alert('Función de exportación en desarrollo');
        }

        // Filtros
        $('#statusFilter, #typeFilter, #cityFilter').change(function() {
            const status = $('#statusFilter').val();
            const type = $('#typeFilter').val();
            const city = $('#cityFilter').val();
            
            $('#propertiesTable tbody tr').each(function() {
                const row = $(this);
                const propertyStatus = row.find('td:nth-child(6)').text().toLowerCase().trim();
                const propertyType = row.find('td:nth-child(5)').text().toLowerCase().trim();
                const propertyCity = row.find('td:nth-child(3)').text().toLowerCase();
                
                let show = true;
                
                if (status && propertyStatus !== status) show = false;
                if (type && propertyType !== type) show = false;
                if (city && !propertyCity.includes(city.toLowerCase())) show = false;
                
                row.toggle(show);
            });
        });

        $('#searchProperty').keyup(function() {
            const searchTerm = $(this).val().toLowerCase();
            
            $('#propertiesTable tbody tr').each(function() {
                const row = $(this);
                const propertyTitle = row.find('td:nth-child(3)').text().toLowerCase();
                
                if (propertyTitle.includes(searchTerm)) {
                    row.show();
                } else {
                    row.hide();
                }
            });
        });
    </script>
</body>
</html> 
