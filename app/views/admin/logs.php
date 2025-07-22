<?php
/**
 * Logs del Sistema - Administrador
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Vista para visualizar y gestionar logs del sistema
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
        
        .log-entry {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid var(--admin-primary);
            transition: transform 0.3s ease;
        }
        
        .log-entry:hover {
            transform: translateY(-2px);
        }
        
        .log-entry.error {
            border-left-color: var(--admin-danger);
            background: #fff5f5;
        }
        
        .log-entry.warning {
            border-left-color: var(--admin-warning);
            background: #fffbf0;
        }
        
        .log-entry.info {
            border-left-color: var(--admin-info);
            background: #f0f8ff;
        }
        
        .log-entry.success {
            border-left-color: var(--admin-success);
            background: #f0fff4;
        }
        
        .log-level {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .level-error {
            background: var(--admin-danger);
            color: white;
        }
        
        .level-warning {
            background: var(--admin-warning);
            color: white;
        }
        
        .level-info {
            background: var(--admin-info);
            color: white;
        }
        
        .level-success {
            background: var(--admin-success);
            color: white;
        }
        
        .level-debug {
            background: #6c757d;
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
        
        .log-message {
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            border-left: 3px solid #dee2e6;
            margin-top: 10px;
        }
        
        .log-stack-trace {
            font-family: 'Courier New', monospace;
            font-size: 0.8rem;
            background: #2d3748;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 5px;
            margin-top: 10px;
            max-height: 200px;
            overflow-y: auto;
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
                        <i class="fas fa-file-alt"></i> Logs del Sistema
                    </h1>
                    <small>Monitoreo y análisis de logs del sistema</small>
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
                        <a class="nav-link" href="/admin/reports?action=list">
                            <i class="fas fa-flag"></i> Gestión de Reportes
                        </a>
                        <a class="nav-link active" href="/admin/logs">
                            <i class="fas fa-file-alt"></i> Logs del Sistema
                        </a>
                        <a class="nav-link" href="/admin/backup">
                            <i class="fas fa-database"></i> Backup & Restore
                        </a>
                        <a class="nav-link" href="/admin/config">
                            <i class="fas fa-cog"></i> Configuración
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
                            <h3><?= number_format(count($logs)) ?></h3>
                            <p class="mb-0">Total Logs</p>
                        </div>
                        <div class="col-md-3">
                            <h3><?= number_format(array_filter($logs, fn($l) => $l['level'] === 'error')->count()) ?></h3>
                            <p class="mb-0">Errores</p>
                        </div>
                        <div class="col-md-3">
                            <h3><?= number_format(array_filter($logs, fn($l) => $l['level'] === 'warning')->count()) ?></h3>
                            <p class="mb-0">Advertencias</p>
                        </div>
                        <div class="col-md-3">
                            <h3><?= number_format(array_filter($logs, fn($l) => $l['level'] === 'info')->count()) ?></h3>
                            <p class="mb-0">Información</p>
                        </div>
                    </div>
                </div>

                <!-- Filtros y Búsqueda -->
                <div class="filter-section">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <label for="levelFilter" class="form-label">Filtrar por Nivel:</label>
                            <select class="form-select" id="levelFilter">
                                <option value="">Todos los niveles</option>
                                <option value="error">Error</option>
                                <option value="warning">Warning</option>
                                <option value="info">Info</option>
                                <option value="success">Success</option>
                                <option value="debug">Debug</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="dateFilter" class="form-label">Filtrar por Fecha:</label>
                            <select class="form-select" id="dateFilter">
                                <option value="">Todas las fechas</option>
                                <option value="today">Hoy</option>
                                <option value="yesterday">Ayer</option>
                                <option value="week">Esta semana</option>
                                <option value="month">Este mes</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="moduleFilter" class="form-label">Filtrar por Módulo:</label>
                            <select class="form-select" id="moduleFilter">
                                <option value="">Todos los módulos</option>
                                <option value="auth">Autenticación</option>
                                <option value="property">Propiedades</option>
                                <option value="user">Usuarios</option>
                                <option value="payment">Pagos</option>
                                <option value="system">Sistema</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="searchLog" class="form-label">Buscar en Logs:</label>
                            <input type="text" class="form-control" id="searchLog" placeholder="Mensaje, usuario...">
                        </div>
                    </div>
                </div>

                <!-- Lista de Logs -->
                <div class="content-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">
                            <i class="fas fa-list"></i> Lista de Logs
                        </h4>
                        <div>
                            <button class="btn btn-warning me-2" onclick="clearLogs()">
                                <i class="fas fa-trash"></i> Limpiar Logs
                            </button>
                            <button class="btn btn-success" onclick="exportLogs()">
                                <i class="fas fa-download"></i> Exportar
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover" id="logsTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nivel</th>
                                    <th>Módulo</th>
                                    <th>Mensaje</th>
                                    <th>Usuario</th>
                                    <th>IP</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($logs)): ?>
                                    <?php foreach ($logs as $log): ?>
                                        <tr class="log-row <?= $log['level'] === 'error' ? 'table-danger' : ($log['level'] === 'warning' ? 'table-warning' : '') ?>">
                                            <td><?= $log['id'] ?></td>
                                            <td>
                                                <span class="log-level level-<?= $log['level'] ?>">
                                                    <?= ucfirst($log['level']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <?= ucfirst($log['module'] ?? 'Sistema') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="log-message-preview">
                                                    <?= htmlspecialchars(substr($log['message'], 0, 100)) ?>
                                                    <?= strlen($log['message']) > 100 ? '...' : '' ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if (!empty($log['user_id'])): ?>
                                                    <div>
                                                        <strong><?= htmlspecialchars($log['user_name'] ?? 'Usuario') ?></strong>
                                                        <br>
                                                        <small class="text-muted">ID: <?= $log['user_id'] ?></small>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted">Sistema</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <code><?= htmlspecialchars($log['ip_address'] ?? 'N/A') ?></code>
                                            </td>
                                            <td><?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                                            onclick="viewLogDetails(<?= $log['id'] ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            onclick="deleteLog(<?= $log['id'] ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No hay logs disponibles</p>
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

    <!-- Modal para Detalles del Log -->
    <div class="modal fade" id="logDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-file-alt"></i> Detalles del Log
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="logDetailsContent">
                        <!-- El contenido se cargará dinámicamente -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
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
            $('#logsTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                pageLength: 50,
                order: [[0, 'desc']]
            });
        });

        // Funciones de gestión de logs
        function viewLogDetails(logId) {
            // Simular carga de detalles del log
            const modal = new bootstrap.Modal(document.getElementById('logDetailsModal'));
            const content = document.getElementById('logDetailsContent');
            
            content.innerHTML = `
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            `;
            
            modal.show();
            
            // Simular carga de datos
            setTimeout(() => {
                content.innerHTML = `
                    <div class="log-entry">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="log-level level-error">Error</span>
                                <span class="badge bg-secondary ms-2">Sistema</span>
                            </div>
                            <small class="text-muted">ID: ${logId}</small>
                        </div>
                        
                        <h6>Mensaje:</h6>
                        <div class="log-message">
                            Error en la conexión a la base de datos: Connection timeout after 30 seconds
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <strong>Usuario:</strong> Sistema<br>
                                <strong>IP:</strong> 192.168.1.100<br>
                                <strong>Fecha:</strong> ${new Date().toLocaleString()}
                            </div>
                            <div class="col-md-6">
                                <strong>Módulo:</strong> Database<br>
                                <strong>Nivel:</strong> Error<br>
                                <strong>Archivo:</strong> Database.php:45
                            </div>
                        </div>
                        
                        <h6 class="mt-3">Stack Trace:</h6>
                        <div class="log-stack-trace">
                            #0 /var/www/html/app/core/Database.php(45): PDO->__construct()
                            #1 /var/www/html/app/core/Database.php(23): Database->connect()
                            #2 /var/www/html/app/controllers/PropertyController.php(12): Database->__construct()
                            #3 /var/www/html/public/index.php(34): PropertyController->index()
                        </div>
                    </div>
                `;
            }, 1000);
        }

        function deleteLog(logId) {
            if (confirm(`¿Estás seguro de que quieres eliminar el log ID ${logId}?\n\nEsta acción no se puede deshacer.`)) {
                // Implementar eliminación de log
                alert('Log eliminado exitosamente');
                location.reload();
            }
        }

        function clearLogs() {
            if (confirm('¿Estás seguro de que quieres LIMPIAR TODOS los logs?\n\n⚠️ ESTA ACCIÓN NO SE PUEDE DESHACER ⚠️')) {
                if (confirm('¿CONFIRMAS la limpieza de todos los logs?')) {
                    // Implementar limpieza de logs
                    alert('Todos los logs han sido eliminados');
                    location.reload();
                }
            }
        }

        function exportLogs() {
            // Implementar exportación de logs
            alert('Función de exportación en desarrollo');
        }

        // Filtros
        $('#levelFilter, #dateFilter, #moduleFilter').change(function() {
            const level = $('#levelFilter').val();
            const date = $('#dateFilter').val();
            const module = $('#moduleFilter').val();
            
            $('#logsTable tbody tr').each(function() {
                const row = $(this);
                const logLevel = row.find('td:nth-child(2)').text().toLowerCase().trim();
                const logModule = row.find('td:nth-child(3)').text().toLowerCase().trim();
                const logDate = row.find('td:nth-child(7)').text();
                
                let show = true;
                
                if (level && logLevel !== level) show = false;
                if (module && logModule !== module) show = false;
                if (date) {
                    const today = new Date();
                    const logDateObj = new Date(logDate.split('/').reverse().join('-'));
                    
                    switch(date) {
                        case 'today':
                            if (logDateObj.toDateString() !== today.toDateString()) show = false;
                            break;
                        case 'yesterday':
                            const yesterday = new Date(today);
                            yesterday.setDate(yesterday.getDate() - 1);
                            if (logDateObj.toDateString() !== yesterday.toDateString()) show = false;
                            break;
                        case 'week':
                            const weekAgo = new Date(today);
                            weekAgo.setDate(weekAgo.getDate() - 7);
                            if (logDateObj < weekAgo) show = false;
                            break;
                        case 'month':
                            const monthAgo = new Date(today);
                            monthAgo.setMonth(monthAgo.getMonth() - 1);
                            if (logDateObj < monthAgo) show = false;
                            break;
                    }
                }
                
                row.toggle(show);
            });
        });

        $('#searchLog').keyup(function() {
            const searchTerm = $(this).val().toLowerCase();
            
            $('#logsTable tbody tr').each(function() {
                const row = $(this);
                const logMessage = row.find('td:nth-child(4)').text().toLowerCase();
                const logUser = row.find('td:nth-child(5)').text().toLowerCase();
                
                if (logMessage.includes(searchTerm) || logUser.includes(searchTerm)) {
                    row.show();
                } else {
                    row.hide();
                }
            });
        });
    </script>
</body>
</html> 