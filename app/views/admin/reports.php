<?php
/**
 * Gestión Completa de Reportes - Administrador
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Vista para gestionar reportes con control total (resolver, descartar, eliminar)
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
        
        .report-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid var(--admin-primary);
            transition: transform 0.3s ease;
        }
        
        .report-card:hover {
            transform: translateY(-2px);
        }
        
        .report-card.pending {
            border-left-color: var(--admin-warning);
        }
        
        .report-card.resolved {
            border-left-color: var(--admin-success);
        }
        
        .report-card.dismissed {
            border-left-color: var(--admin-danger);
        }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-pendiente {
            background: var(--admin-warning);
            color: white;
        }
        
        .status-atendido {
            background: var(--admin-success);
            color: white;
        }
        
        .status-descartado {
            background: var(--admin-danger);
            color: white;
        }
        
        .priority-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .priority-alta {
            background: var(--admin-danger);
            color: white;
        }
        
        .priority-media {
            background: var(--admin-warning);
            color: white;
        }
        
        .priority-baja {
            background: var(--admin-success);
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
        
        .btn-view {
            background: var(--admin-info);
            color: white;
        }
        
        .btn-resolve {
            background: var(--admin-success);
            color: white;
        }
        
        .btn-dismiss {
            background: var(--admin-warning);
            color: white;
        }
        
        .btn-delete {
            background: var(--admin-danger);
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
        
        .report-description {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
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
                        <i class="fas fa-flag"></i> Gestión de Reportes
                    </h1>
                    <small>Control total de reportes del sistema</small>
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
                        <a class="nav-link" href="/admin/properties?action=list">
                            <i class="fas fa-home"></i> Gestión de Propiedades
                        </a>
                        <a class="nav-link active" href="/admin/reports?action=list">
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
                            <h3><?= number_format(count($reports)) ?></h3>
                            <p class="mb-0">Total Reportes</p>
                        </div>
                        <div class="col-md-3">
                            <h3><?= number_format(array_filter($reports, fn($r) => $r['estado'] === 'pendiente')->count()) ?></h3>
                            <p class="mb-0">Pendientes</p>
                        </div>
                        <div class="col-md-3">
                            <h3><?= number_format(array_filter($reports, fn($r) => $r['estado'] === 'atendido')->count()) ?></h3>
                            <p class="mb-0">Atendidos</p>
                        </div>
                        <div class="col-md-3">
                            <h3><?= number_format(array_filter($reports, fn($r) => $r['estado'] === 'descartado')->count()) ?></h3>
                            <p class="mb-0">Descartados</p>
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
                                <option value="pendiente">Pendientes</option>
                                <option value="atendido">Atendidos</option>
                                <option value="descartado">Descartados</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="priorityFilter" class="form-label">Filtrar por Prioridad:</label>
                            <select class="form-select" id="priorityFilter">
                                <option value="">Todas las prioridades</option>
                                <option value="alta">Alta</option>
                                <option value="media">Media</option>
                                <option value="baja">Baja</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="typeFilter" class="form-label">Filtrar por Tipo:</label>
                            <select class="form-select" id="typeFilter">
                                <option value="">Todos los tipos</option>
                                <option value="irregularidad">Irregularidad</option>
                                <option value="spam">Spam</option>
                                <option value="inapropiado">Inapropiado</option>
                                <option value="fraude">Fraude</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="searchReport" class="form-label">Buscar Reporte:</label>
                            <input type="text" class="form-control" id="searchReport" placeholder="Título, descripción...">
                        </div>
                    </div>
                </div>

                <!-- Lista de Reportes -->
                <div class="content-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">
                            <i class="fas fa-list"></i> Lista de Reportes
                        </h4>

                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover" id="reportsTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Título</th>
                                    <th>Descripción</th>
                                    <th>Tipo</th>
                                    <th>Prioridad</th>
                                    <th>Estado</th>
                                    <th>Reportado por</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($reports)): ?>
                                    <?php foreach ($reports as $report): ?>
                                        <tr class="report-row <?= $report['estado'] === 'pendiente' ? 'table-warning' : ($report['estado'] === 'descartado' ? 'table-danger' : 'table-success') ?>">
                                            <td><?= $report['id'] ?></td>
                                            <td>
                                                <strong><?= htmlspecialchars($report['titulo']) ?></strong>
                                                <?php if ($report['estado'] === 'pendiente'): ?>
                                                    <br>
                                                    <small class="text-warning">
                                                        <i class="fas fa-clock"></i> Requiere atención
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="report-description" title="<?= htmlspecialchars($report['descripcion']) ?>">
                                                    <?= htmlspecialchars(substr($report['descripcion'], 0, 100)) ?>
                                                    <?= strlen($report['descripcion']) > 100 ? '...' : '' ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <?= ucfirst($report['tipo']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="priority-badge priority-<?= $report['prioridad'] ?>">
                                                    <?= ucfirst($report['prioridad']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="status-badge status-<?= $report['estado'] ?>">
                                                    <?= ucfirst($report['estado']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong><?= htmlspecialchars($report['usuario_nombre']) ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?= htmlspecialchars($report['usuario_email']) ?></small>
                                                </div>
                                            </td>
                                            <td><?= date('d/m/Y H:i', strtotime($report['fecha_reporte'])) ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                                            onclick="viewReport(<?= $report['id'] ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    
                                                    <?php if ($report['estado'] === 'pendiente'): ?>
                                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                                onclick="resolveReport(<?= $report['id'] ?>, '<?= htmlspecialchars($report['titulo']) ?>')">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                                                onclick="dismissReport(<?= $report['id'] ?>, '<?= htmlspecialchars($report['titulo']) ?>')">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                    
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            onclick="deleteReport(<?= $report['id'] ?>, '<?= htmlspecialchars($report['titulo']) ?>')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <i class="fas fa-flag fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No hay reportes registrados</p>
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

    <!-- Modal para Resolver Reporte -->
    <div class="modal fade" id="resolveReportModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-check-circle"></i> Resolver Reporte
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="resolveReportForm" method="POST" action="/admin/reports?action=resolve">
                    <div class="modal-body">
                        <input type="hidden" id="resolveReportId" name="report_id">
                        <p>Resolver reporte: <strong id="resolveReportTitle"></strong></p>
                        <div class="mb-3">
                            <label for="resolveResponse" class="form-label">Respuesta de Resolución:</label>
                            <textarea class="form-control" id="resolveResponse" name="respuesta" rows="4" required 
                                      placeholder="Describe las acciones tomadas para resolver el reporte..."></textarea>
                        </div>
                        <div class="alert alert-success">
                            <i class="fas fa-info-circle"></i>
                            <strong>Información:</strong> Esta respuesta será enviada al usuario que reportó el problema.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Resolver Reporte</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Descartar Reporte -->
    <div class="modal fade" id="dismissReportModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-times-circle"></i> Descartar Reporte
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="dismissReportForm" method="POST" action="/admin/reports?action=dismiss">
                    <div class="modal-body">
                        <input type="hidden" id="dismissReportId" name="report_id">
                        <p>Descartar reporte: <strong id="dismissReportTitle"></strong></p>
                        <div class="mb-3">
                            <label for="dismissReason" class="form-label">Motivo del Descarto:</label>
                            <textarea class="form-control" id="dismissReason" name="motivo" rows="4" required 
                                      placeholder="Especifica el motivo por el cual se descarta este reporte..."></textarea>
                        </div>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Advertencia:</strong> Esta acción marcará el reporte como descartado y no se tomarán acciones adicionales.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Descartar Reporte</button>
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
            $('#reportsTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                pageLength: 25,
                order: [[0, 'desc']]
            });
        });

        // Funciones de gestión de reportes
        function viewReport(reportId) {
            window.location.href = `/admin/reports?action=view&id=${reportId}`;
        }

        function resolveReport(reportId, reportTitle) {
            document.getElementById('resolveReportId').value = reportId;
            document.getElementById('resolveReportTitle').textContent = reportTitle;
            document.getElementById('resolveResponse').value = '';
            
            const modal = new bootstrap.Modal(document.getElementById('resolveReportModal'));
            modal.show();
        }

        function dismissReport(reportId, reportTitle) {
            document.getElementById('dismissReportId').value = reportId;
            document.getElementById('dismissReportTitle').textContent = reportTitle;
            document.getElementById('dismissReason').value = '';
            
            const modal = new bootstrap.Modal(document.getElementById('dismissReportModal'));
            modal.show();
        }

        function deleteReport(reportId, reportTitle) {
            if (confirm(`¿Estás seguro de que quieres ELIMINAR PERMANENTEMENTE el reporte "${reportTitle}"?\n\n⚠️ ESTA ACCIÓN NO SE PUEDE DESHACER ⚠️`)) {
                if (confirm('¿CONFIRMAS la eliminación? Esta es tu última oportunidad para cancelar.')) {
                    window.location.href = `/admin/reports?action=delete&id=${reportId}`;
                }
            }
        }



        // Filtros
        $('#statusFilter, #priorityFilter, #typeFilter').change(function() {
            const status = $('#statusFilter').val();
            const priority = $('#priorityFilter').val();
            const type = $('#typeFilter').val();
            
            $('#reportsTable tbody tr').each(function() {
                const row = $(this);
                const reportStatus = row.find('td:nth-child(6)').text().toLowerCase().trim();
                const reportPriority = row.find('td:nth-child(5)').text().toLowerCase().trim();
                const reportType = row.find('td:nth-child(4)').text().toLowerCase().trim();
                
                let show = true;
                
                if (status && reportStatus !== status) show = false;
                if (priority && reportPriority !== priority) show = false;
                if (type && reportType !== type) show = false;
                
                row.toggle(show);
            });
        });

        $('#searchReport').keyup(function() {
            const searchTerm = $(this).val().toLowerCase();
            
            $('#reportsTable tbody tr').each(function() {
                const row = $(this);
                const reportTitle = row.find('td:nth-child(2)').text().toLowerCase();
                const reportDescription = row.find('td:nth-child(3)').text().toLowerCase();
                
                if (reportTitle.includes(searchTerm) || reportDescription.includes(searchTerm)) {
                    row.show();
                } else {
                    row.hide();
                }
            });
        });
    </script>
</body>
</html> 
