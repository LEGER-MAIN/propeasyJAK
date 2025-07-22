<?php
/**
 * Contenido de Backup del Sistema - Administrador
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este archivo contiene solo el contenido de backup del sistema, sin estructura HTML completa
 */

// El rol ya fue verificado en el AdminController
?>

<!-- Crear Nuevo Backup -->
<div class="content-card">
    <h4 class="mb-4">
        <i class="fas fa-plus-circle"></i> Crear Nuevo Backup
    </h4>
    
    <form method="POST" action="/admin/backup/create">
        <div class="row">
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="backup_type" class="form-label">Tipo de Backup:</label>
                    <select class="form-select" id="backup_type" name="backup_type" required>
                        <option value="full">Backup Completo</option>
                        <option value="database">Solo Base de Datos</option>
                        <option value="files">Solo Archivos</option>
                        <option value="incremental">Backup Incremental</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="backup_compression" class="form-label">Compresión:</label>
                    <select class="form-select" id="backup_compression" name="backup_compression">
                        <option value="none">Sin compresión</option>
                        <option value="gzip">GZIP</option>
                        <option value="zip">ZIP</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="backup_encryption" class="form-label">Encriptación:</label>
                    <select class="form-select" id="backup_encryption" name="backup_encryption">
                        <option value="none">Sin encriptación</option>
                        <option value="aes256">AES-256</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="backup_description" class="form-label">Descripción:</label>
                    <textarea class="form-control" id="backup_description" name="backup_description" rows="3" 
                              placeholder="Descripción opcional del backup..."></textarea>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="backup_notify" name="backup_notify" checked>
                        <label class="form-check-label" for="backup_notify">
                            Notificar cuando termine
                        </label>
                    </div>
                </div>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-download"></i> Crear Backup
        </button>
    </form>
</div>

<!-- Lista de Backups -->
<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="fas fa-database"></i> Backups Disponibles
        </h4>
        <div>
            <button class="btn btn-info me-2" onclick="refreshBackups()">
                <i class="fas fa-sync"></i> Actualizar
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
                    <th>Tamaño</th>
                    <th>Estado</th>
                    <th>Duración</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($backups)): ?>
                    <?php foreach ($backups as $backup): ?>
                        <tr class="backup-row">
                            <td><?= $backup['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($backup['name']) ?></strong>
                                <?php if (!empty($backup['description'])): ?>
                                    <br><small class="text-muted"><?= htmlspecialchars($backup['description']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="type-badge type-<?= $backup['type'] ?>">
                                    <?= ucfirst($backup['type']) ?>
                                </span>
                            </td>
                            <td><?= formatBytes($backup['size']) ?></td>
                            <td>
                                <span class="status-badge status-<?= $backup['status'] ?>">
                                    <?= ucfirst($backup['status']) ?>
                                </span>
                            </td>
                            <td><?= $backup['duration'] ?>s</td>
                            <td><?= date('d/m/Y H:i', strtotime($backup['created_at'])) ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            onclick="downloadBackup(<?= $backup['id'] ?>)">
                                        <i class="fas fa-download"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-success" 
                                            onclick="restoreBackup(<?= $backup['id'] ?>, '<?= htmlspecialchars($backup['name']) ?>')">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-info" 
                                            onclick="testBackup(<?= $backup['id'] ?>)">
                                        <i class="fas fa-vial"></i>
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
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="backup_frequency" class="form-label">Frecuencia de Backup:</label>
                    <select class="form-select" id="backup_frequency" name="backup_frequency">
                        <option value="daily">Diario</option>
                        <option value="weekly">Semanal</option>
                        <option value="monthly">Mensual</option>
                        <option value="manual">Manual</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="backup_retention" class="form-label">Retención (días):</label>
                    <input type="number" class="form-control" id="backup_retention" name="backup_retention" 
                           value="<?= $config['backup_retention'] ?? 30 ?>" min="1" max="365">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="max_backups" class="form-label">Máximo Backups:</label>
                    <input type="number" class="form-control" id="max_backups" name="max_backups" 
                           value="<?= $config['max_backups'] ?? 10 ?>" min="1" max="100">
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="auto_backup" name="auto_backup" 
                               <?= $config['auto_backup'] ?? true ? 'checked' : '' ?>>
                        <label class="form-check-label" for="auto_backup">
                            Backup Automático
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="backup_notifications" name="backup_notifications" 
                               <?= $config['backup_notifications'] ?? true ? 'checked' : '' ?>>
                        <label class="form-check-label" for="backup_notifications">
                            Notificaciones de Backup
                        </label>
                    </div>
                </div>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Guardar Configuración
        </button>
    </form>
</div>

<!-- Zona de Peligro -->
<div class="danger-zone">
    <h4 class="mb-3">
        <i class="fas fa-exclamation-triangle"></i> Zona de Peligro
    </h4>
    
    <div class="row">
        <div class="col-md-4">
            <button type="button" class="btn btn-danger-zone w-100 mb-2" onclick="restoreFromFile()">
                <i class="fas fa-upload"></i> Restaurar desde Archivo
            </button>
        </div>
        <div class="col-md-4">
            <button type="button" class="btn btn-danger-zone w-100 mb-2" onclick="testAllBackups()">
                <i class="fas fa-vial"></i> Probar Todos los Backups
            </button>
        </div>
        <div class="col-md-4">
            <button type="button" class="btn btn-danger-zone w-100 mb-2" onclick="emergencyBackup()">
                <i class="fas fa-exclamation"></i> Backup de Emergencia
            </button>
        </div>
    </div>
    
    <div class="alert alert-warning mt-3">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>Advertencia:</strong> Las acciones en esta zona pueden afectar el funcionamiento del sistema. 
        Asegúrate de tener un backup antes de proceder.
    </div>
</div>

<!-- Modal para Restaurar Backup -->
<div class="modal fade" id="restoreBackupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-undo text-warning"></i> Restaurar Backup
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="restoreBackupForm" method="POST" action="/admin/backup/restore">
                <div class="modal-body">
                    <input type="hidden" id="restoreBackupId" name="backup_id">
                    <p>¿Estás seguro de que quieres restaurar el backup: <strong id="restoreBackupName"></strong>?</p>
                    
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>¡ADVERTENCIA!</strong> Esta acción:
                        <ul class="mb-0 mt-2">
                            <li>Reemplazará todos los datos actuales</li>
                            <li>NO SE PUEDE DESHACER</li>
                            <li>Puede causar pérdida de datos</li>
                        </ul>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="restore_confirm" required>
                            <label class="form-check-label" for="restore_confirm">
                                Confirmo que entiendo los riesgos y quiero proceder
                            </label>
                        </div>
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

<!-- Scripts específicos -->
<script>
    // Inicializar DataTable
    $(document).ready(function() {
        $('#backupsTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            },
            pageLength: 25,
            order: [[0, 'desc']]
        });
    });

    // Funciones de gestión de backups
    function downloadBackup(backupId) {
        window.location.href = `/admin/backup/download/${backupId}`;
    }

    function restoreBackup(backupId, backupName) {
        document.getElementById('restoreBackupId').value = backupId;
        document.getElementById('restoreBackupName').textContent = backupName;
        document.getElementById('restore_confirm').checked = false;
        
        const modal = new bootstrap.Modal(document.getElementById('restoreBackupModal'));
        modal.show();
    }

    function testBackup(backupId) {
        if (confirm('¿Deseas probar la integridad de este backup?')) {
            // Implementar prueba de backup
            alert('Función de prueba de backup en desarrollo');
        }
    }

    function deleteBackup(backupId, backupName) {
        if (confirm(`¿Estás seguro de que quieres eliminar el backup "${backupName}"?\n\nEsta acción no se puede deshacer.`)) {
            if (confirm('¿CONFIRMAS la eliminación? Esta es tu última oportunidad para cancelar.')) {
                // Implementar eliminación de backup
                alert('Función de eliminación de backup en desarrollo');
            }
        }
    }

    function refreshBackups() {
        location.reload();
    }

    function restoreFromFile() {
        if (confirm('¿Deseas restaurar desde un archivo de backup?\n\nEsta acción reemplazará todos los datos actuales.')) {
            // Implementar restauración desde archivo
            alert('Función de restauración desde archivo en desarrollo');
        }
    }

    function testAllBackups() {
        if (confirm('¿Deseas probar la integridad de todos los backups?\n\nEsta acción puede tomar varios minutos.')) {
            // Implementar prueba de todos los backups
            alert('Función de prueba de todos los backups en desarrollo');
        }
    }

    function emergencyBackup() {
        if (confirm('¿Deseas crear un backup de emergencia?\n\nEste backup se creará inmediatamente sin compresión para mayor velocidad.')) {
            // Implementar backup de emergencia
            alert('Función de backup de emergencia en desarrollo');
        }
    }

    // Función para formatear bytes
    function formatBytes(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';
        
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }
</script> 