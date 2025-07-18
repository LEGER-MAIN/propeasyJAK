<?php
$titulo = 'Administración de Reportes';
include APP_PATH . '/views/layouts/main.php';
?>

<div class="container-fluid mt-4">
    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="fas fa-clipboard-list me-2"></i>
            Administración de Reportes
        </h2>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary" onclick="exportarReportes()">
                <i class="fas fa-download me-1"></i>
                Exportar
            </button>
            <button class="btn btn-primary" onclick="mostrarEstadisticas()">
                <i class="fas fa-chart-bar me-1"></i>
                Estadísticas
            </button>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?php echo $estadisticas['total_reportes'] ?? 0; ?></h4>
                            <small>Total Reportes</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clipboard-list fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?php echo $estadisticas['pendientes'] ?? 0; ?></h4>
                            <small>Pendientes</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?php echo $estadisticas['atendidos'] ?? 0; ?></h4>
                            <small>Atendidos</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?php echo $estadisticas['descartados'] ?? 0; ?></h4>
                            <small>Descartados</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-filter me-2"></i>
                Filtros
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="/reportes/admin" class="row g-3">
                <div class="col-md-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select class="form-select" id="estado" name="estado">
                        <option value="">Todos los estados</option>
                        <?php foreach ($estados as $valor => $texto): ?>
                            <option value="<?php echo $valor; ?>" <?php echo ($_GET['estado'] ?? '') === $valor ? 'selected' : ''; ?>>
                                <?php echo $texto; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="tipo_reporte" class="form-label">Tipo de Reporte</label>
                    <select class="form-select" id="tipo_reporte" name="tipo_reporte">
                        <option value="">Todos los tipos</option>
                        <?php foreach ($tiposReporte as $valor => $texto): ?>
                            <option value="<?php echo $valor; ?>" <?php echo ($_GET['tipo_reporte'] ?? '') === $valor ? 'selected' : ''; ?>>
                                <?php echo $texto; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="fecha_desde" class="form-label">Desde</label>
                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" 
                           value="<?php echo $_GET['fecha_desde'] ?? ''; ?>">
                </div>
                <div class="col-md-2">
                    <label for="fecha_hasta" class="form-label">Hasta</label>
                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" 
                           value="<?php echo $_GET['fecha_hasta'] ?? ''; ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="d-flex gap-2 w-100">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="fas fa-search me-1"></i>
                            Filtrar
                        </button>
                        <a href="/reportes/admin" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Reportes -->
    <div class="card shadow">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-list me-2"></i>
                Lista de Reportes
            </h6>
        </div>
        <div class="card-body p-0">
            <?php if (empty($reportes)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No se encontraron reportes</h5>
                    <p class="text-muted">No hay reportes que coincidan con los filtros aplicados.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Tipo</th>
                                <th>Título</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reportes as $reporte): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">#<?php echo $reporte['id']; ?></span>
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?php echo htmlspecialchars($reporte['nombre'] . ' ' . $reporte['apellido']); ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo htmlspecialchars($reporte['email']); ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?php echo getTipoReporteTexto($reporte['tipo_reporte']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;" 
                                             title="<?php echo htmlspecialchars($reporte['titulo']); ?>">
                                            <?php echo htmlspecialchars($reporte['titulo']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo getEstadoColor($reporte['estado']); ?>">
                                            <?php echo ucfirst($reporte['estado']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small>
                                            <?php echo date('d/m/Y H:i', strtotime($reporte['fecha_reporte'])); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary" 
                                                    onclick="verReporte(<?php echo $reporte['id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <?php if ($reporte['estado'] === 'pendiente'): ?>
                                                <button type="button" class="btn btn-outline-success" 
                                                        onclick="responderReporte(<?php echo $reporte['id']; ?>)">
                                                    <i class="fas fa-reply"></i>
                                                </button>
                                            <?php endif; ?>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="eliminarReporte(<?php echo $reporte['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal para Ver Reporte -->
<div class="modal fade" id="modalReporte" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles del Reporte</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalReporteBody">
                <!-- Contenido dinámico -->
            </div>
        </div>
    </div>
</div>

<!-- Modal para Responder Reporte -->
<div class="modal fade" id="modalResponder" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Responder Reporte</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formResponder">
                <div class="modal-body">
                    <input type="hidden" id="reporte_id" name="reporte_id">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div class="mb-3">
                        <label for="estado" class="form-label">Nuevo Estado</label>
                        <select class="form-select" id="estado_respuesta" name="estado" required>
                            <?php foreach ($estados as $valor => $texto): ?>
                                <option value="<?php echo $valor; ?>"><?php echo $texto; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="respuesta" class="form-label">Respuesta</label>
                        <textarea class="form-control" id="respuesta" name="respuesta" rows="5" 
                                  placeholder="Escribe tu respuesta al usuario..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Enviar Respuesta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Estadísticas -->
<div class="modal fade" id="modalEstadisticas" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Estadísticas de Reportes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Por Estado</h6>
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Pendientes</span>
                                <span class="badge bg-warning"><?php echo $estadisticas['pendientes'] ?? 0; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Atendidos</span>
                                <span class="badge bg-success"><?php echo $estadisticas['atendidos'] ?? 0; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Descartados</span>
                                <span class="badge bg-danger"><?php echo $estadisticas['descartados'] ?? 0; ?></span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Por Tipo</h6>
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Quejas de Agente</span>
                                <span class="badge bg-info"><?php echo $estadisticas['quejas_agente'] ?? 0; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Problemas de Plataforma</span>
                                <span class="badge bg-info"><?php echo $estadisticas['problemas_plataforma'] ?? 0; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Información Falsa</span>
                                <span class="badge bg-info"><?php echo $estadisticas['informacion_falsa'] ?? 0; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Otros</span>
                                <span class="badge bg-info"><?php echo $estadisticas['otros'] ?? 0; ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Funciones JavaScript
function verReporte(id) {
    fetch(`/reportes/mostrar/${id}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('modalReporteBody').innerHTML = html;
            new bootstrap.Modal(document.getElementById('modalReporte')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar el reporte');
        });
}

function responderReporte(id) {
    document.getElementById('reporte_id').value = id;
    new bootstrap.Modal(document.getElementById('modalResponder')).show();
}

function eliminarReporte(id) {
    if (confirm('¿Estás seguro de que deseas eliminar este reporte? Esta acción no se puede deshacer.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/reportes/eliminar';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = 'csrf_token';
        csrfToken.value = '<?php echo generateCSRFToken(); ?>';
        
        const reporteId = document.createElement('input');
        reporteId.type = 'hidden';
        reporteId.name = 'reporte_id';
        reporteId.value = id;
        
        form.appendChild(csrfToken);
        form.appendChild(reporteId);
        document.body.appendChild(form);
        form.submit();
    }
}

function mostrarEstadisticas() {
    new bootstrap.Modal(document.getElementById('modalEstadisticas')).show();
}

function exportarReportes() {
    // Implementar exportación a CSV/Excel
    alert('Función de exportación en desarrollo');
}

// Manejar formulario de respuesta
document.getElementById('formResponder').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/reportes/actualizar-estado', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Respuesta enviada correctamente');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al enviar la respuesta');
    });
});
</script>

<?php
/**
 * Obtener el color CSS para el estado del reporte
 */
function getEstadoColor($estado) {
    switch ($estado) {
        case 'pendiente':
            return 'warning';
        case 'atendido':
            return 'success';
        case 'descartado':
            return 'danger';
        default:
            return 'secondary';
    }
}

/**
 * Obtener el texto del tipo de reporte
 */
function getTipoReporteTexto($tipo) {
    $tipos = [
        'queja_agente' => 'Queja Agente',
        'problema_plataforma' => 'Problema Plataforma',
        'informacion_falsa' => 'Información Falsa',
        'otro' => 'Otro'
    ];
    
    return $tipos[$tipo] ?? 'Desconocido';
}
?> 