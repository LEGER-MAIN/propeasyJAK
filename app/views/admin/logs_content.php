<?php
/**
 * Contenido de Logs del Sistema - Administrador
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este archivo contiene solo el contenido de logs del sistema, sin estructura HTML completa
 */

// El rol ya fue verificado en el AdminController
?>

<!-- Filtros de Logs -->
<div class="filter-section">
    <div class="row align-items-center">
        <div class="col-md-3">
            <label for="logLevelFilter" class="form-label">Filtrar por Nivel:</label>
            <select class="form-select" id="logLevelFilter">
                <option value="">Todos los niveles</option>
                <option value="ERROR">Error</option>
                <option value="WARNING">Advertencia</option>
                <option value="INFO">Información</option>
                <option value="DEBUG">Debug</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="logModuleFilter" class="form-label">Filtrar por Módulo:</label>
            <select class="form-select" id="logModuleFilter">
                <option value="">Todos los módulos</option>
                <option value="auth">Autenticación</option>
                <option value="property">Propiedades</option>
                <option value="user">Usuarios</option>
                <option value="system">Sistema</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="logDateFrom" class="form-label">Desde:</label>
            <input type="date" class="form-control" id="logDateFrom">
        </div>
        <div class="col-md-3">
            <label for="logDateTo" class="form-label">Hasta:</label>
            <input type="date" class="form-control" id="logDateTo">
        </div>
    </div>
</div>

<!-- Lista de Logs -->
<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="fas fa-file-alt"></i> Logs del Sistema
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
                        <tr class="log-row log-level-<?= strtolower($log['level']) ?>">
                            <td><?= $log['id'] ?></td>
                            <td>
                                <span class="level-badge level-<?= strtolower($log['level']) ?>">
                                    <?= $log['level'] ?>
                                </span>
                            </td>
                            <td>
                                <span class="module-badge module-<?= $log['module'] ?>">
                                    <?= ucfirst($log['module']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="log-message">
                                    <?= htmlspecialchars(substr($log['message'], 0, 100)) ?>
                                    <?= strlen($log['message']) > 100 ? '...' : '' ?>
                                </div>
                            </td>
                            <td>
                                <small><?= htmlspecialchars($log['user'] ?? 'Sistema') ?></small>
                            </td>
                            <td>
                                <small><?= htmlspecialchars($log['ip'] ?? 'N/A') ?></small>
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
                            <p class="text-muted">No hay logs registrados</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
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

<!-- Scripts específicos -->
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
        document.getElementById('logDetailsContent').innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <strong>ID:</strong> ${logId}<br>
                    <strong>Nivel:</strong> <span class="level-badge level-error">ERROR</span><br>
                    <strong>Módulo:</strong> <span class="module-badge module-system">Sistema</span><br>
                    <strong>Usuario:</strong> admin@propeasy.com<br>
                    <strong>IP:</strong> 192.168.1.100<br>
                    <strong>Fecha:</strong> ${new Date().toLocaleString()}
                </div>
                <div class="col-md-6">
                    <strong>Mensaje Completo:</strong><br>
                    <div class="alert alert-danger">
                        Error en la conexión a la base de datos: Connection timeout after 30 seconds
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <strong>Stack Trace:</strong>
                    <pre class="bg-light p-3 rounded" style="max-height: 200px; overflow-y: auto;">
Error: Connection timeout after 30 seconds
    at Database.connect (/app/core/Database.php:45)
    at AdminController.dashboard (/app/controllers/AdminController.php:67)
    at Router.executeControllerMethod (/app/core/Router.php:189)
    at Router.run (/app/core/Router.php:95)
    at index.php:25</pre>
                </div>
            </div>
        `;
        modal.show();
    }

    function deleteLog(logId) {
        if (confirm('¿Estás seguro de que quieres eliminar este log?')) {
            // Implementar eliminación de log
            alert('Función de eliminación de log en desarrollo');
        }
    }

    function clearLogs() {
        if (confirm('¿Estás seguro de que quieres limpiar todos los logs?\n\nEsta acción no se puede deshacer.')) {
            if (confirm('¿CONFIRMAS la limpieza? Esta es tu última oportunidad para cancelar.')) {
                // Implementar limpieza de logs
                alert('Función de limpieza de logs en desarrollo');
            }
        }
    }

    function exportLogs() {
        // Implementar exportación de logs
        alert('Función de exportación de logs en desarrollo');
    }

    // Filtros
    $('#logLevelFilter, #logModuleFilter').change(function() {
        const level = $('#logLevelFilter').val();
        const module = $('#logModuleFilter').val();
        
        $('#logsTable tbody tr').each(function() {
            const row = $(this);
            const logLevel = row.find('td:nth-child(2)').text().toLowerCase().trim();
            const logModule = row.find('td:nth-child(3)').text().toLowerCase().trim();
            
            let show = true;
            
            if (level && logLevel !== level.toLowerCase()) show = false;
            if (module && logModule !== module.toLowerCase()) show = false;
            
            row.toggle(show);
        });
    });

    $('#logDateFrom, #logDateTo').change(function() {
        const fromDate = $('#logDateFrom').val();
        const toDate = $('#logDateTo').val();
        
        if (fromDate || toDate) {
            $('#logsTable tbody tr').each(function() {
                const row = $(this);
                const logDate = row.find('td:nth-child(7)').text();
                const dateObj = new Date(logDate.split('/').reverse().join('-'));
                
                let show = true;
                
                if (fromDate && dateObj < new Date(fromDate)) show = false;
                if (toDate && dateObj > new Date(toDate)) show = false;
                
                row.toggle(show);
            });
        }
    });
</script> 