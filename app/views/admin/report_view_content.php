<?php
/**
 * Vista de Reporte Individual - Administrador
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 */

// Verificar que el reporte existe
if (!$report) {
    echo '<div class="alert alert-danger">Reporte no encontrado</div>';
    return;
}

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
 * Función helper para obtener el estado en formato legible
 */
function getReportStatusDisplayName($status) {
    $statusMap = [
        'pendiente' => 'Pendiente',
        'atendido' => 'Atendido',
        'descartado' => 'Descartado'
    ];
    
    return $statusMap[$status] ?? ucfirst($status);
}
?>

<!-- Encabezado del Reporte -->
<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">
                <i class="fas fa-flag text-primary"></i> 
                Reporte #<?= $report['id'] ?>
            </h3>
            <p class="text-muted mb-0">
                <i class="fas fa-calendar"></i> 
                Creado el <?= date('d/m/Y H:i', strtotime($report['fecha_reporte'])) ?>
            </p>
        </div>
        <div>
            <a href="/admin/reports" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver a Reportes
            </a>
        </div>
    </div>

    <!-- Información Principal -->
    <div class="row">
        <div class="col-md-8">
            <!-- Título y Descripción -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt"></i> Detalles del Reporte
                    </h5>
                </div>
                <div class="card-body">
                    <h4 class="card-title"><?= htmlspecialchars($report['titulo']) ?></h4>
                    <div class="mb-3">
                        <strong>Descripción:</strong>
                        <p class="mt-2"><?= nl2br(htmlspecialchars($report['descripcion'])) ?></p>
                    </div>
                    
                    <?php if (!empty($report['archivo_adjunto'])): ?>
                        <div class="mb-3">
                            <strong>Archivo Adjunto:</strong>
                            <div class="mt-2">
                                <a href="/uploads/reportes/<?= htmlspecialchars($report['archivo_adjunto']) ?>" 
                                   target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download"></i> Descargar Archivo
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Respuesta del Administrador -->
            <?php if (!empty($report['respuesta_admin'])): ?>
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-reply"></i> Respuesta del Administrador
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">
                            <strong>Respuesta:</strong>
                        </p>
                        <p><?= nl2br(htmlspecialchars($report['respuesta_admin'])) ?></p>
                        
                        <?php if (!empty($report['fecha_respuesta'])): ?>
                            <small class="text-muted">
                                <i class="fas fa-clock"></i> 
                                Respondido el <?= date('d/m/Y H:i', strtotime($report['fecha_respuesta'])) ?>
                            </small>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-md-4">
            <!-- Información del Estado -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle"></i> Estado del Reporte
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Estado:</strong>
                        <div class="mt-2">
                            <span class="badge bg-<?= $report['estado'] === 'pendiente' ? 'warning' : ($report['estado'] === 'atendido' ? 'success' : 'secondary') ?> fs-6">
                                <?= getReportStatusDisplayName($report['estado']) ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Tipo de Reporte:</strong>
                        <div class="mt-2">
                            <span class="badge bg-primary fs-6">
                                <?= getReportTypeDisplayName($report['tipo_reporte']) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del Usuario -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user"></i> Usuario que Reportó
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Nombre:</strong>
                        <p class="mb-1"><?= htmlspecialchars($report['nombre'] . ' ' . $report['apellido']) ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Email:</strong>
                        <p class="mb-1">
                            <a href="mailto:<?= htmlspecialchars($report['email']) ?>">
                                <?= htmlspecialchars($report['email']) ?>
                            </a>
                        </p>
                    </div>
                    
                    <?php if (!empty($report['telefono'])): ?>
                        <div class="mb-3">
                            <strong>Teléfono:</strong>
                            <p class="mb-1">
                                <a href="tel:<?= htmlspecialchars($report['telefono']) ?>">
                                    <?= htmlspecialchars($report['telefono']) ?>
                                </a>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Acciones del Administrador -->
            <?php if ($report['estado'] === 'pendiente'): ?>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-tools"></i> Acciones
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-success" 
                                    onclick="resolveReport(<?= $report['id'] ?>, '<?= htmlspecialchars($report['titulo']) ?>')">
                                <i class="fas fa-check"></i> Resolver Reporte
                            </button>
                            
                            <button type="button" class="btn btn-warning" 
                                    onclick="dismissReport(<?= $report['id'] ?>, '<?= htmlspecialchars($report['titulo']) ?>')">
                                <i class="fas fa-times"></i> Descartar Reporte
                            </button>
                            
                            <button type="button" class="btn btn-danger" 
                                    onclick="deleteReport(<?= $report['id'] ?>, '<?= htmlspecialchars($report['titulo']) ?>')">
                                <i class="fas fa-trash"></i> Eliminar Reporte
                            </button>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-tools"></i> Acciones
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-danger" 
                                    onclick="deleteReport(<?= $report['id'] ?>, '<?= htmlspecialchars($report['titulo']) ?>')">
                                <i class="fas fa-trash"></i> Eliminar Reporte
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>



<!-- Scripts específicos -->
<script>
    // Funciones de gestión de reportes
    function resolveReport(reportId, reportTitle) {
        const respuesta = prompt(`Resolver reporte: "${reportTitle}"\n\nIngresa la respuesta de resolución:`);
        if (respuesta !== null && respuesta.trim() !== '') {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/reports';
            
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'resolve';
            
            const reportIdInput = document.createElement('input');
            reportIdInput.type = 'hidden';
            reportIdInput.name = 'report_id';
            reportIdInput.value = reportId;
            
            const respuestaInput = document.createElement('input');
            respuestaInput.type = 'hidden';
            respuestaInput.name = 'respuesta';
            respuestaInput.value = respuesta;
            
            form.appendChild(actionInput);
            form.appendChild(reportIdInput);
            form.appendChild(respuestaInput);
            document.body.appendChild(form);
            form.submit();
        }
    }

    function dismissReport(reportId, reportTitle) {
        const motivo = prompt(`Descartar reporte: "${reportTitle}"\n\nIngresa el motivo del descarte:`);
        if (motivo !== null && motivo.trim() !== '') {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/reports';
            
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'dismiss';
            
            const reportIdInput = document.createElement('input');
            reportIdInput.type = 'hidden';
            reportIdInput.name = 'report_id';
            reportIdInput.value = reportId;
            
            const motivoInput = document.createElement('input');
            motivoInput.type = 'hidden';
            motivoInput.name = 'motivo';
            motivoInput.value = motivo;
            
            form.appendChild(actionInput);
            form.appendChild(reportIdInput);
            form.appendChild(motivoInput);
            document.body.appendChild(form);
            form.submit();
        }
    }

    function deleteReport(reportId, reportTitle) {
        if (confirm(`¿Estás seguro de que quieres ELIMINAR PERMANENTEMENTE el reporte "${reportTitle}"?\n\n⚠️ ESTA ACCIÓN NO SE PUEDE DESHACER ⚠️`)) {
            if (confirm('¿CONFIRMAS la eliminación? Esta es tu última oportunidad para cancelar.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/reports';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete';
                
                const reportIdInput = document.createElement('input');
                reportIdInput.type = 'hidden';
                reportIdInput.name = 'report_id';
                reportIdInput.value = reportId;
                
                form.appendChild(actionInput);
                form.appendChild(reportIdInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
    }
</script> 