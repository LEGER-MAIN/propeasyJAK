<?php
/**
 * Backup & Restore - Administrador
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Vista para gestionar backups y restauraciones del sistema
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
        
        .backup-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid var(--admin-primary);
            transition: transform 0.3s ease;
        }
        
        .backup-card:hover {
            transform: translateY(-2px);
        }
        
        .backup-card.complete {
            border-left-color: var(--admin-success);
        }
        
        .backup-card.in-progress {
            border-left-color: var(--admin-warning);
        }
        
        .backup-card.failed {
            border-left-color: var(--admin-danger);
        }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-complete {
            background: var(--admin-success);
            color: white;
        }
        
        .status-in-progress {
            background: var(--admin-warning);
            color: white;
        }
        
        .status-failed {
            background: var(--admin-danger);
            color: white;
        }
        
        .progress-bar-custom {
            height: 8px;
            border-radius: 4px;
            background: #e9ecef;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--admin-success) 0%, #2ecc71 100%);
            transition: width 0.3s ease;
        }
        
        .stats-summary {
            background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .danger-zone {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
        }
        
        .danger-zone h4 {
            color: white;
            margin-bottom: 15px;
        }
        
        .btn-danger-zone {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid white;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-danger-zone:hover {
            background: white;
            color: var(--admin-danger);
        }
        
        .file-size {
            font-family: 'Courier New', monospace;
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.8rem;
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
                        <i class="fas fa-database"></i> Backup & Restore
                    </h1>
                    <small>Gestión de respaldos y restauraciones del sistema</small>
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
                        <a class="nav-link" href="/admin/logs">
                            <i class="fas fa-file-alt"></i> Logs del Sistema
                        </a>
                        <a class="nav-link active" href="/admin/backup">
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
                            <h3><?= number_format(count($backups)) ?></h3>
                            <p class="mb-0">Total Backups</p>
                        </div>
                        <div class="col-md-3">
                            <h3><?= number_format(array_filter($backups, fn($b) => $b['status'] === 'complete')->count()) ?></h3>
                            <p class="mb-0">Completados</p>
                        </div>
                        <div class="col-md-3">
                            <h3><?= number_format(array_filter($backups, fn($b) => $b['status'] === 'in-progress')->count()) ?></h3>
                            <p class="mb-0">En Progreso</p>
                        </div>
                        <div class="col-md-3">
                            <h3><?= number_format(array_filter($backups, fn($b) => $b['status'] === 'failed')->count()) ?></h3>
                            <p class="mb-0">Fallidos</p>
                        </div>
                    </div>
                </div>

                <!-- Crear Nuevo Backup -->
                <div class="content-card">
                    <h4 class="mb-4">
                        <i class="fas fa-plus-circle"></i> Crear Nuevo Backup
                    </h4>
                    
                    <form method="POST" action="/admin/backup?action=create">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="backupType" class="form-label">Tipo de Backup</label>
                                    <select class="form-select" id="backupType" name="backup_type" required>
                                        <option value="">Seleccionar tipo</option>
                                        <option value="full">Backup Completo</option>
                                        <option value="database">Solo Base de Datos</option>
                                        <option value="files">Solo Archivos</option>
                                        <option value="incremental">Backup Incremental</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="backupName" class="form-label">Nombre del Backup</label>
                                    <input type="text" class="form-control" id="backupName" name="backup_name" 
                                           placeholder="backup_<?= date('Y-m-d_H-i-s') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="backupDescription" class="form-label">Descripción</label>
                                    <input type="text" class="form-control" id="backupDescription" name="backup_description" 
                                           placeholder="Descripción opcional del backup">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="compressBackup" name="compress_backup" checked>
                                    <label class="form-check-label" for="compressBackup">
                                        Comprimir Backup
                                    </label>
                                    <small class="form-text text-muted d-block">
                                        Reduce el tamaño del archivo de backup
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="encryptBackup" name="encrypt_backup">
                                    <label class="form-check-label" for="encryptBackup">
                                        Encriptar Backup
                                    </label>
                                    <small class="form-text text-muted d-block">
                                        Protege el backup con encriptación
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-database"></i> Crear Backup
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Lista de Backups -->
                <div class="content-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">
                            <i class="fas fa-list"></i> Lista de Backups
                        </h4>
                        <div>
                            <button class="btn btn-warning me-2" onclick="cleanOldBackups()">
                                <i class="fas fa-broom"></i> Limpiar Antiguos
                            </button>
                            <button class="btn btn-success" onclick="exportBackupList()">
                                <i class="fas fa-download"></i> Exportar Lista
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover" id="backupsTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                    <th>Tamaño</th>
                                    <th>Fecha Creación</th>
                                    <th>Duración</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($backups)): ?>
                                    <?php foreach ($backups as $backup): ?>
                                        <tr class="backup-row <?= $backup['status'] === 'failed' ? 'table-danger' : ($backup['status'] === 'in-progress' ? 'table-warning' : '') ?>">
                                            <td><?= $backup['id'] ?></td>
                                            <td>
                                                <div>
                                                    <strong><?= htmlspecialchars($backup['name']) ?></strong>
                                                    <?php if (!empty($backup['description'])): ?>
                                                        <br>
                                                        <small class="text-muted"><?= htmlspecialchars($backup['description']) ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <?= ucfirst($backup['type']) ?>
                                                </span>
                                                <?php if ($backup['compressed']): ?>
                                                    <br>
                                                    <small class="text-success">
                                                        <i class="fas fa-compress"></i> Comprimido
                                                    </small>
                                                <?php endif; ?>
                                                <?php if ($backup['encrypted']): ?>
                                                    <br>
                                                    <small class="text-info">
                                                        <i class="fas fa-lock"></i> Encriptado
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="status-badge status-<?= $backup['status'] ?>">
                                                    <?= ucfirst(str_replace('-', ' ', $backup['status'])) ?>
                                                </span>
                                                <?php if ($backup['status'] === 'in-progress'): ?>
                                                    <br>
                                                    <div class="progress-bar-custom mt-2">
                                                        <div class="progress-fill" style="width: <?= $backup['progress'] ?? 0 ?>%"></div>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="file-size">
                                                    <?= formatFileSize($backup['size'] ?? 0) ?>
                                                </span>
                                            </td>
                                            <td><?= date('d/m/Y H:i:s', strtotime($backup['created_at'])) ?></td>
                                            <td>
                                                <?php if ($backup['duration']): ?>
                                                    <?= formatDuration($backup['duration']) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <?php if ($backup['status'] === 'complete'): ?>
                                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                onclick="downloadBackup(<?= $backup['id'] ?>)">
                                                            <i class="fas fa-download"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                                onclick="restoreBackup(<?= $backup['id'] ?>, '<?= htmlspecialchars($backup['name']) ?>')">
                                                            <i class="fas fa-undo"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                    
                                                    <button type="button" class="btn btn-sm btn-outline-info" 
                                                            onclick="viewBackupDetails(<?= $backup['id'] ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            onclick="deleteBackup(<?= $backup['id'] ?>, '<?= htmlspecialchars($backup['name']) ?>')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fas fa-database fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No hay backups disponibles</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Configuración de Backup -->
                <div class="content-card">
                    <h4 class="mb-4">
                        <i class="fas fa-cog"></i> Configuración de Backup
                    </h4>
                    
                    <form method="POST" action="/admin/backup/config">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="backupFrequency" class="form-label">Frecuencia de Backup Automático</label>
                                    <select class="form-select" id="backupFrequency" name="backup_frequency">
                                        <option value="disabled">Deshabilitado</option>
                                        <option value="daily">Diario</option>
                                        <option value="weekly">Semanal</option>
                                        <option value="monthly">Mensual</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="retentionDays" class="form-label">Días de Retención</label>
                                    <input type="number" class="form-control" id="retentionDays" name="retention_days" 
                                           value="30" min="1" max="365">
                                    <small class="form-text text-muted">
                                        Número de días que se mantienen los backups automáticos
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="backupTime" class="form-label">Hora de Backup Automático</label>
                                    <input type="time" class="form-control" id="backupTime" name="backup_time" 
                                           value="02:00">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="maxBackups" class="form-label">Máximo de Backups</label>
                                    <input type="number" class="form-control" id="maxBackups" name="max_backups" 
                                           value="10" min="1" max="100">
                                    <small class="form-text text-muted">
                                        Número máximo de backups a mantener
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Configuración
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Zona de Peligro -->
                <div class="danger-zone">
                    <h4>
                        <i class="fas fa-exclamation-triangle"></i> Zona de Peligro
                    </h4>
                    <p class="mb-4">Estas acciones pueden afectar gravemente el funcionamiento del sistema.</p>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <button type="button" class="btn btn-danger-zone w-100 mb-2" onclick="restoreFromFile()">
                                <i class="fas fa-upload"></i> Restaurar desde Archivo
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-danger-zone w-100 mb-2" onclick="testBackup()">
                                <i class="fas fa-vial"></i> Probar Backup
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-danger-zone w-100 mb-2" onclick="emergencyBackup()">
                                <i class="fas fa-exclamation"></i> Backup de Emergencia
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Restaurar Backup -->
    <div class="modal fade" id="restoreBackupModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-undo"></i> Restaurar Backup
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="restoreBackupForm" method="POST" action="/admin/backup?action=restore">
                    <div class="modal-body">
                        <input type="hidden" id="restoreBackupId" name="backup_id">
                        <p>Restaurar backup: <strong id="restoreBackupName"></strong></p>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Advertencia:</strong> Esta acción sobrescribirá todos los datos actuales del sistema.
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="confirmRestore" required>
                            <label class="form-check-label" for="confirmRestore">
                                Confirmo que entiendo que esta acción es irreversible
                            </label>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="backupBeforeRestore" checked>
                            <label class="form-check-label" for="backupBeforeRestore">
                                Crear backup antes de restaurar
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Restaurar Backup</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Funciones de gestión de backups
        function downloadBackup(backupId) {
            window.location.href = `/admin/backup?action=download&id=${backupId}`;
        }

        function restoreBackup(backupId, backupName) {
            document.getElementById('restoreBackupId').value = backupId;
            document.getElementById('restoreBackupName').textContent = backupName;
            document.getElementById('confirmRestore').checked = false;
            document.getElementById('backupBeforeRestore').checked = true;
            
            const modal = new bootstrap.Modal(document.getElementById('restoreBackupModal'));
            modal.show();
        }

        function viewBackupDetails(backupId) {
            // Implementar vista de detalles del backup
            alert('Función de detalles del backup en desarrollo');
        }

        function deleteBackup(backupId, backupName) {
            if (confirm(`¿Estás seguro de que quieres eliminar el backup "${backupName}"?\n\nEsta acción no se puede deshacer.`)) {
                if (confirm('¿CONFIRMAS la eliminación del backup?')) {
                    window.location.href = `/admin/backup?action=delete&id=${backupId}`;
                }
            }
        }

        function cleanOldBackups() {
            if (confirm('¿Estás seguro de que quieres limpiar los backups antiguos?\n\nEsto eliminará los backups que excedan la configuración de retención.')) {
                if (confirm('¿CONFIRMAS la limpieza de backups antiguos?')) {
                    window.location.href = `/admin/backup?action=clean`;
                }
            }
        }

        function exportBackupList() {
            // Implementar exportación de lista de backups
            alert('Función de exportación en desarrollo');
        }

        function restoreFromFile() {
            if (confirm('¿Deseas restaurar desde un archivo de backup?\n\nEsto abrirá un selector de archivos.')) {
                // Implementar restauración desde archivo
                alert('Función de restauración desde archivo en desarrollo');
            }
        }

        function testBackup() {
            if (confirm('¿Deseas probar la integridad del último backup?\n\nEsto verificará que el backup sea válido.')) {
                // Implementar prueba de backup
                alert('Función de prueba de backup en desarrollo');
            }
        }

        function emergencyBackup() {
            if (confirm('⚠️ BACKUP DE EMERGENCIA ⚠️\n\n¿Estás seguro de que quieres crear un backup de emergencia?\n\nEsto puede tomar varios minutos y puede afectar el rendimiento del sistema.')) {
                if (confirm('¿CONFIRMAS el backup de emergencia?')) {
                    window.location.href = `/admin/backup?action=emergency`;
                }
            }
        }

        // Funciones auxiliares
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function formatDuration(seconds) {
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;
            
            if (hours > 0) {
                return `${hours}h ${minutes}m ${secs}s`;
            } else if (minutes > 0) {
                return `${minutes}m ${secs}s`;
            } else {
                return `${secs}s`;
            }
        }

        // Actualizar progreso en tiempo real
        function updateProgress() {
            const progressBars = document.querySelectorAll('.progress-fill');
            progressBars.forEach(bar => {
                const currentWidth = parseInt(bar.style.width) || 0;
                if (currentWidth < 100) {
                    bar.style.width = (currentWidth + Math.random() * 10) + '%';
                }
            });
        }

        // Actualizar progreso cada 2 segundos
        setInterval(updateProgress, 2000);
    </script>
</body>
</html>

<?php
// Funciones auxiliares PHP
function formatFileSize($bytes) {
    if ($bytes === 0) return '0 B';
    $k = 1024;
    $sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = floor(log($bytes) / log($k));
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}

function formatDuration($seconds) {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $secs = $seconds % 60;
    
    if ($hours > 0) {
        return "{$hours}h {$minutes}m {$secs}s";
    } elseif ($minutes > 0) {
        return "{$minutes}m {$secs}s";
    } else {
        return "{$secs}s";
    }
}
?> 
