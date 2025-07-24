<?php
/**
 * Contenido de Gestión de Reportes - Administrador
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este archivo contiene solo el contenido de gestión de reportes, sin estructura HTML completa
 */

// El rol ya fue verificado en el AdminController

/**
 * Función helper para convertir tipos de reporte a formato legible
 */
function getReportTypeDisplayName($type) {
    $typeMap = [
        'queja_agente' => 'Queja contra Agente',
        'problema_plataforma' => 'Problema con la Plataforma',
        'informacion_falsa' => 'Información Falsa',
        'otro' => 'Otro'
    ];
    
    return $typeMap[$type] ?? ucfirst(str_replace('_', ' ', $type));
}

/**
 * Función helper para obtener prioridad de reporte
 */
function getReportPriority($report) {
    // Por ahora, retornar 'media' como predeterminado
    // En el futuro, esto podría venir de la base de datos
    return 'media';
}
?>

<!-- Resumen de Estadísticas -->
<div class="stats-summary">
    <div class="row text-center">
        <div class="col-md-3">
            <h3><?= number_format($totalReports ?? 0) ?></h3>
            <p class="mb-0">Total Reportes</p>
        </div>
        <div class="col-md-3">
            <h3><?= number_format($pendingReports ?? 0) ?></h3>
            <p class="mb-0">Pendientes</p>
        </div>
        <div class="col-md-3">
            <h3><?= number_format($resolvedReports ?? 0) ?></h3>
            <p class="mb-0">Resueltos</p>
        </div>
        <div class="col-md-3">
            <h3><?= number_format($dismissedReports ?? 0) ?></h3>
            <p class="mb-0">Descartados</p>
        </div>
    </div>
</div>

<!-- Filtros y Búsqueda -->
<div class="filter-section">
    <form id="filterForm" method="GET" action="/admin/reports">
        <div class="row align-items-end">
            <div class="col-md-2">
                <label for="statusFilter" class="form-label">Estado:</label>
                <select class="form-select" id="statusFilter" name="status">
                    <option value="">Todos los estados</option>
                    <option value="pendiente" <?= ($_GET['status'] ?? '') === 'pendiente' ? 'selected' : '' ?>>Pendientes</option>
                    <option value="atendido" <?= ($_GET['status'] ?? '') === 'atendido' ? 'selected' : '' ?>>Resueltos</option>
                    <option value="descartado" <?= ($_GET['status'] ?? '') === 'descartado' ? 'selected' : '' ?>>Descartados</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="priorityFilter" class="form-label">Prioridad:</label>
                <select class="form-select" id="priorityFilter" name="priority">
                    <option value="">Todas las prioridades</option>
                    <option value="alta" <?= ($_GET['priority'] ?? '') === 'alta' ? 'selected' : '' ?>>Alta</option>
                    <option value="media" <?= ($_GET['priority'] ?? '') === 'media' ? 'selected' : '' ?>>Media</option>
                    <option value="baja" <?= ($_GET['priority'] ?? '') === 'baja' ? 'selected' : '' ?>>Baja</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="typeFilter" class="form-label">Tipo:</label>
                <select class="form-select" id="typeFilter" name="type">
                    <option value="">Todos los tipos</option>
                    <option value="queja_agente" <?= ($_GET['type'] ?? '') === 'queja_agente' ? 'selected' : '' ?>>Queja contra Agente</option>
                    <option value="problema_plataforma" <?= ($_GET['type'] ?? '') === 'problema_plataforma' ? 'selected' : '' ?>>Problema con la Plataforma</option>
                    <option value="informacion_falsa" <?= ($_GET['type'] ?? '') === 'informacion_falsa' ? 'selected' : '' ?>>Información Falsa</option>
                    <option value="otro" <?= ($_GET['type'] ?? '') === 'otro' ? 'selected' : '' ?>>Otro</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="searchReport" class="form-label">Buscar:</label>
                <input type="text" class="form-control" id="searchReport" name="search" placeholder="Título, descripción..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
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
                    <a href="/admin/reports" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Lista de Reportes -->
<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="fas fa-flag"></i> Lista de Reportes
        </h4>
        <div>
            <button class="btn btn-success" onclick="exportReports()">
                <i class="fas fa-download"></i> Exportar
            </button>
        </div>
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
                        <tr class="report-row <?= ($report['estado'] ?? 'pendiente') === 'pendiente' ? 'table-warning' : (($report['estado'] ?? 'pendiente') === 'resuelto' ? 'table-success' : 'table-secondary') ?>">
                            <td><?= $report['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($report['titulo']) ?></strong>
                            </td>
                            <td>
                                <div class="report-description">
                                    <?= htmlspecialchars(substr($report['descripcion'], 0, 100)) ?>
                                    <?= strlen($report['descripcion']) > 100 ? '...' : '' ?>
                                </div>
                            </td>
                            <td>
                                <span class="type-badge type-<?= $report['tipo_reporte'] ?>">
                                    <?= getReportTypeDisplayName($report['tipo_reporte']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="priority-badge priority-media">
                                    <?= getReportPriority($report) ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-<?= $report['estado'] ?>">
                                    <?= ucfirst($report['estado']) ?>
                                </span>
                            </td>
                            <td>
                                <small><?= htmlspecialchars(($report['nombre'] ?? '') . ' ' . ($report['apellido'] ?? '') ?: 'Anónimo') ?></small>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($report['fecha_reporte'])) ?></td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="/admin/reports/view/<?= $report['id'] ?>" 
                                       class="btn btn-sm btn-info" 
                                       title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-success" 
                                            onclick="resolveReport(<?= $report['id'] ?>, '<?= htmlspecialchars($report['titulo'], ENT_QUOTES) ?>')"
                                            title="Resolver reporte">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn btn-sm btn-warning" 
                                            onclick="dismissReport(<?= $report['id'] ?>, '<?= htmlspecialchars($report['titulo'], ENT_QUOTES) ?>')"
                                            title="Descartar reporte">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn btn-sm btn-danger" 
                                            onclick="deleteReport(<?= $report['id'] ?>, '<?= htmlspecialchars($report['titulo'], ENT_QUOTES) ?>')"
                                            title="Eliminar reporte">
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



<!-- Scripts específicos -->
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

    // Funciones de gestión de reportes - Versión con modales
    function viewReport(reportId) {
        window.location.href = `/admin/reports/view/${reportId}`;
    }

    function resolveReport(reportId, reportTitle) {
        // console.log removed
        
        // Configurar la modal de resolución
        document.getElementById('resolveReportTitle').textContent = reportTitle;
        document.getElementById('resolveReportId').value = reportId;
        document.getElementById('resolveResponse').value = '';
        
        // Mostrar la modal
        $('#resolveReportModal').modal('show');
    }

    function dismissReport(reportId, reportTitle) {
        // console.log removed
        
        // Configurar la modal de descarte
        document.getElementById('dismissReportTitle').textContent = reportTitle;
        document.getElementById('dismissReportId').value = reportId;
        document.getElementById('dismissReason').value = '';
        
        // Mostrar la modal
        $('#dismissReportModal').modal('show');
    }

    function deleteReport(reportId, reportTitle) {
        // console.log removed
        
        // Configurar la modal de eliminación
        document.getElementById('deleteReportTitle').textContent = reportTitle;
        document.getElementById('deleteReportId').value = reportId;
        
        // Mostrar la modal
        $('#deleteReportModal').modal('show');
    }

    // Funciones para manejar el envío de las modales
    function submitResolveReport() {
        const reportId = document.getElementById('resolveReportId').value;
        const respuesta = document.getElementById('resolveResponse').value.trim();
        
        if (respuesta === '') {
            alert('Por favor ingresa una respuesta de resolución.');
            return;
        }
        
        // console.log removed
        const url = `/admin/reports?action=resolve&id=${reportId}&respuesta=${encodeURIComponent(respuesta)}`;
        window.location.href = url;
    }

    function submitDismissReport() {
        const reportId = document.getElementById('dismissReportId').value;
        const motivo = document.getElementById('dismissReason').value.trim();
        
        if (motivo === '') {
            alert('Por favor ingresa un motivo de descarte.');
            return;
        }
        
        // console.log removed
        const url = `/admin/reports?action=dismiss&id=${reportId}&motivo=${encodeURIComponent(motivo)}`;
        window.location.href = url;
    }

    function submitDeleteReport() {
        const reportId = document.getElementById('deleteReportId').value;
        
        // console.log removed
        const url = `/admin/reports?action=delete&id=${reportId}`;
        window.location.href = url;
    }

    function exportReports() {
        // Construir URL de exportación con filtros actuales
        const url = new URL('/admin/reports', window.location.origin);
        url.searchParams.set('action', 'export');
        
        // Agregar filtros actuales
        const status = document.getElementById('statusFilter').value;
        const priority = document.getElementById('priorityFilter').value;
        const type = document.getElementById('typeFilter').value;
        const search = document.getElementById('searchReport').value;
        
        if (status) url.searchParams.set('status', status);
        if (priority) url.searchParams.set('priority', priority);
        if (type) url.searchParams.set('type', type);
        if (search) url.searchParams.set('search', search);
        
        // Descargar archivo
        window.location.href = url.toString();
    }

    // Mejorar funcionalidad del buscador
    $(document).ready(function() {
        // Búsqueda en tiempo real (opcional)
        let searchTimeout;
        $('#searchReport').on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                $('#filterForm').submit();
            }, 500); // Esperar 500ms después de que el usuario deje de escribir
        });

        // Auto-submit al cambiar filtros
        $('#statusFilter, #priorityFilter, #typeFilter').on('change', function() {
            $('#filterForm').submit();
        });

        // Mostrar indicador de filtros activos
        const urlParams = new URLSearchParams(window.location.search);
        const hasFilters = urlParams.has('status') || urlParams.has('priority') || urlParams.has('type') || urlParams.has('search');
        
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
            if (urlParams.get('status')) {
                const statusText = $('#statusFilter option:selected').text();
                activeFilters.push('Estado: ' + statusText);
            }
            if (urlParams.get('priority')) {
                const priorityText = $('#priorityFilter option:selected').text();
                activeFilters.push('Prioridad: ' + priorityText);
            }
            if (urlParams.get('type')) {
                const typeText = $('#typeFilter option:selected').text();
                activeFilters.push('Tipo: ' + typeText);
            }
            if (urlParams.get('search')) {
                activeFilters.push('Búsqueda: "' + urlParams.get('search') + '"');
            }
            
            $('#activeFilters').text(activeFilters.join(', '));
        }

        // Mejorar la experiencia de búsqueda
        $('#searchReport').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                e.preventDefault();
                $('#filterForm').submit();
            }
        });

        // Botón de limpiar filtros
        $('.btn-outline-secondary').on('click', function(e) {
            e.preventDefault();
            window.location.href = '/admin/reports';
        });
    });

    // Función para mostrar estadísticas de búsqueda
    function showSearchStats() {
        const totalRows = $('#reportsTable tbody tr').length;
        const filteredRows = $('#reportsTable tbody tr:visible').length;
        
        if (totalRows !== filteredRows) {
            // Mostrar estadísticas de filtrado
            const statsDiv = $('<div class="alert alert-info mt-3" role="alert">' +
                '<i class="fas fa-info-circle"></i> ' +
                `Mostrando ${filteredRows} de ${totalRows} reportes` +
                '</div>');
            
            $('.content-card').prepend(statsDiv);
        }
    }
</script>

<!-- Modal para Resolver Reporte -->
<div class="modal fade" id="resolveReportModal" tabindex="-1" aria-labelledby="resolveReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="resolveReportModalLabel">
                    <i class="fas fa-check-circle"></i> Resolver Reporte
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Reporte:</strong> <span id="resolveReportTitle"></span>
                </div>
                
                <div class="mb-3">
                    <label for="resolveResponse" class="form-label">
                        <i class="fas fa-comment"></i> Respuesta de Resolución
                    </label>
                    <textarea class="form-control" id="resolveResponse" rows="4" 
                              placeholder="Ingresa la respuesta de resolución para este reporte..."></textarea>
                    <div class="form-text">
                        Esta respuesta será visible para el usuario que reportó el problema.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success" onclick="submitResolveReport()">
                    <i class="fas fa-check"></i> Resolver Reporte
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Descartar Reporte -->
<div class="modal fade" id="dismissReportModal" tabindex="-1" aria-labelledby="dismissReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="dismissReportModalLabel">
                    <i class="fas fa-times-circle"></i> Descartar Reporte
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Reporte:</strong> <span id="dismissReportTitle"></span>
                </div>
                
                <div class="mb-3">
                    <label for="dismissReason" class="form-label">
                        <i class="fas fa-comment"></i> Motivo del Descarte
                    </label>
                    <textarea class="form-control" id="dismissReason" rows="4" 
                              placeholder="Ingresa el motivo por el cual se descarta este reporte..."></textarea>
                    <div class="form-text">
                        Este motivo será registrado en el historial del reporte.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-warning" onclick="submitDismissReport()">
                    <i class="fas fa-times"></i> Descartar Reporte
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Eliminar Reporte -->
<div class="modal fade" id="deleteReportModal" tabindex="-1" aria-labelledby="deleteReportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteReportModalLabel">
                    <i class="fas fa-trash"></i> Eliminar Reporte
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>⚠️ ADVERTENCIA ⚠️</strong>
                </div>
                
                <p>¿Estás seguro de que quieres <strong>ELIMINAR PERMANENTEMENTE</strong> el reporte:</p>
                <div class="alert alert-warning">
                    <strong id="deleteReportTitle"></strong>
                </div>
                
                <p class="text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>ESTA ACCIÓN NO SE PUEDE DESHACER</strong>
                </p>
                
                <p>El reporte será eliminado completamente de la base de datos.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" onclick="submitDeleteReport()">
                    <i class="fas fa-trash"></i> Eliminar Permanentemente
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Campos ocultos para almacenar IDs -->
<input type="hidden" id="resolveReportId" value="">
<input type="hidden" id="dismissReportId" value="">
<input type="hidden" id="deleteReportId" value=""> 
