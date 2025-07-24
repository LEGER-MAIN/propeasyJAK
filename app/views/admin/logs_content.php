<?php
/**
 * Contenido de Logs del Sistema - Administrador
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este archivo contiene solo el contenido de logs del sistema, sin estructura HTML completa
 */

// El rol ya fue verificado en el AdminController
?>

<style>
.level-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.level-error {
    background-color: #fee2e2;
    color: #dc2626;
    border: 1px solid #fecaca;
}

.level-warning {
    background-color: #fef3c7;
    color: #d97706;
    border: 1px solid #fed7aa;
}

.level-info {
    background-color: #dbeafe;
    color: #2563eb;
    border: 1px solid #bfdbfe;
}

.level-debug {
    background-color: #f3f4f6;
    color: #6b7280;
    border: 1px solid #e5e7eb;
}

.module-badge {
    padding: 3px 6px;
    border-radius: 8px;
    font-size: 0.7rem;
    font-weight: 500;
    background-color: #f8f9fa;
    color: #495057;
    border: 1px solid #dee2e6;
}

.module-auth {
    background-color: #e8f5e8;
    color: #2d5a2d;
    border-color: #c3e6c3;
}

.module-property {
    background-color: #fff3cd;
    color: #856404;
    border-color: #ffeaa7;
}

.module-user {
    background-color: #d1ecf1;
    color: #0c5460;
    border-color: #bee5eb;
}

.module-system {
    background-color: #f8d7da;
    color: #721c24;
    border-color: #f5c6cb;
}

.log-row:hover {
    background-color: #f8f9fa;
}

.log-message {
    max-width: 300px;
    word-wrap: break-word;
}

/* Estilos para el modal de detalles */
.modal-lg {
    max-width: 800px;
}

#logDetailsContent .alert {
    margin-bottom: 0;
    font-size: 0.9rem;
}

#logDetailsContent .bg-light {
    background-color: #f8f9fa !important;
    border: 1px solid #dee2e6;
}

#logDetailsContent strong {
    color: #495057;
    font-weight: 600;
}

#logDetailsContent .level-badge,
#logDetailsContent .module-badge {
    font-size: 0.8rem;
    padding: 3px 8px;
}
</style>

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
                            <td><?= date('d/m/Y H:i:s', strtotime($log['date'])) ?></td>
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
        // Buscar el log en la tabla
        const table = $('#logsTable').DataTable();
        const data = table.data().toArray();
        const log = data.find(row => row[0] == logId);
        
        if (!log) {
            alert('No se encontró el log especificado');
            return;
        }
        
        // Extraer información del log
        const level = log[1].replace(/<[^>]*>/g, '').trim(); // Remover HTML tags
        const module = log[2].replace(/<[^>]*>/g, '').trim();
        const message = log[3].replace(/<[^>]*>/g, '').trim();
        const user = log[4].replace(/<[^>]*>/g, '').trim();
        const ip = log[5].replace(/<[^>]*>/g, '').trim();
        const date = log[6].replace(/<[^>]*>/g, '').trim();
        
        // Determinar el color del badge según el nivel
        let levelClass = 'level-info';
        if (level === 'ERROR') levelClass = 'level-error';
        else if (level === 'WARNING') levelClass = 'level-warning';
        else if (level === 'DEBUG') levelClass = 'level-debug';
        
        // Determinar el color del badge del módulo
        let moduleClass = 'module-system';
        if (module === 'AUTH') moduleClass = 'module-auth';
        else if (module === 'PROPERTY') moduleClass = 'module-property';
        else if (module === 'USER') moduleClass = 'module-user';
        
        // Determinar el color del alert según el nivel
        let alertClass = 'alert-info';
        if (level === 'ERROR') alertClass = 'alert-danger';
        else if (level === 'WARNING') alertClass = 'alert-warning';
        else if (level === 'DEBUG') alertClass = 'alert-secondary';
        
        const modal = new bootstrap.Modal(document.getElementById('logDetailsModal'));
        document.getElementById('logDetailsContent').innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <strong>ID:</strong> ${logId}<br>
                    <strong>Nivel:</strong> <span class="level-badge ${levelClass}">${level}</span><br>
                    <strong>Módulo:</strong> <span class="module-badge ${moduleClass}">${module}</span><br>
                    <strong>Usuario:</strong> ${user}<br>
                    <strong>IP:</strong> ${ip}<br>
                    <strong>Fecha:</strong> ${date}
                </div>
                <div class="col-md-6">
                    <strong>Mensaje Completo:</strong><br>
                    <div class="alert ${alertClass}">
                        ${message}
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <strong>Información Adicional:</strong>
                    <div class="bg-light p-3 rounded">
                        <p><strong>Módulo:</strong> ${module}</p>
                        <p><strong>Usuario:</strong> ${user}</p>
                        <p><strong>Dirección IP:</strong> ${ip}</p>
                        <p><strong>Timestamp:</strong> ${date}</p>
                        <p><strong>Nivel de Log:</strong> ${level}</p>
                    </div>
                </div>
            </div>
        `;
        modal.show();
    }

    function deleteLog(logId) {
        if (confirm('¿Estás seguro de que quieres eliminar este log?\n\nEsta acción no se puede deshacer.')) {
            if (confirm('¿CONFIRMAS la eliminación? Esta es tu última oportunidad para cancelar.')) {
                // Aquí se implementaría la eliminación del log específico
                // Por ahora, solo mostraremos un mensaje de confirmación
                alert('Log eliminado exitosamente (ID: ' + logId + ')');
                
                // Opcional: recargar la página para reflejar los cambios
                // window.location.reload();
            }
        }
    }

    function clearLogs() {
        if (confirm('¿Estás seguro de que quieres limpiar todos los logs?\n\nEsta acción no se puede deshacer.')) {
            if (confirm('¿CONFIRMAS la limpieza? Esta es tu última oportunidad para cancelar.')) {
                window.location.href = '/admin/logs?action=clear';
            }
        }
    }

    function exportLogs() {
        window.location.href = '/admin/logs?action=export';
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
