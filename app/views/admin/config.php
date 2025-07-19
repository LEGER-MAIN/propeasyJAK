<?php
/**
 * Configuración del Sistema - Panel Administrativo
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Vista para configurar parámetros del sistema
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
        .sidebar {
            background: #f8f9fa;
            min-height: 100vh;
            padding: 20px;
        }
        
        .nav-link {
            color: #333;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 5px;
        }
        
        .nav-link:hover, .nav-link.active {
            background: #007bff;
            color: white;
        }
        
        .config-card {
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .config-section {
            border-left: 4px solid #007bff;
            padding-left: 20px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <h4 class="mb-4">
                    <i class="fas fa-cogs"></i> Admin Panel
                </h4>
                
                <nav class="nav flex-column">
                    <a class="nav-link" href="/admin/dashboard">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a class="nav-link" href="/admin/users">
                        <i class="fas fa-users"></i> Usuarios
                    </a>
                    <a class="nav-link" href="/admin/reports">
                        <i class="fas fa-chart-bar"></i> Reportes
                    </a>
                    <a class="nav-link active" href="/admin/config">
                        <i class="fas fa-cog"></i> Configuración
                    </a>
                    <a class="nav-link" href="/dashboard">
                        <i class="fas fa-home"></i> Volver al Sistema
                    </a>
                    <a class="nav-link" href="/logout">
                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                    </a>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="fas fa-cog"></i> Configuración del Sistema</h1>
                    <div>
                        <button class="btn btn-success" onclick="saveAllConfig()">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </div>
                </div>
                
                <!-- Mensajes Flash -->
                <?php $flashMessages = getFlashMessages(); ?>
                <?php if (!empty($flashMessages)): ?>
                    <?php foreach ($flashMessages as $message): ?>
                        <div class="alert alert-<?= $message['type'] ?> alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($message['message']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <form id="configForm" method="POST" action="/admin/config">
                    <!-- Configuración General -->
                    <div class="config-section">
                        <h3><i class="fas fa-info-circle"></i> Configuración General</h3>
                        <div class="card config-card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Nombre de la Aplicación</label>
                                            <input type="text" name="app_name" class="form-control" value="<?= htmlspecialchars($config['app_name']) ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">URL de la Aplicación</label>
                                            <input type="url" name="app_url" class="form-control" value="<?= htmlspecialchars($config['app_url']) ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Tamaño Máximo de Archivo (MB)</label>
                                            <input type="number" name="max_file_size" class="form-control" value="<?= $config['max_file_size'] / (1024 * 1024) ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Tiempo de Sesión (segundos)</label>
                                            <input type="number" name="session_lifetime" class="form-control" value="<?= $config['session_lifetime'] ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Configuración de Email -->
                    <div class="config-section">
                        <h3><i class="fas fa-envelope"></i> Configuración de Email</h3>
                        <div class="card config-card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Servidor SMTP</label>
                                            <input type="text" name="smtp_host" class="form-control" value="<?= htmlspecialchars($config['smtp_host']) ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Puerto SMTP</label>
                                            <input type="number" name="smtp_port" class="form-control" value="<?= $config['smtp_port'] ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Usuario SMTP</label>
                                            <input type="email" name="smtp_user" class="form-control" value="<?= htmlspecialchars($config['smtp_user']) ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Contraseña SMTP</label>
                                            <input type="password" name="smtp_pass" class="form-control" value="<?= htmlspecialchars($config['smtp_pass']) ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Email Remitente</label>
                                            <input type="email" name="smtp_from" class="form-control" value="<?= htmlspecialchars($config['smtp_from']) ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Nombre Remitente</label>
                                            <input type="text" name="smtp_from_name" class="form-control" value="<?= htmlspecialchars($config['smtp_from_name']) ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Configuración de Negocio -->
                    <div class="config-section">
                        <h3><i class="fas fa-briefcase"></i> Configuración de Negocio</h3>
                        <div class="card config-card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Porcentaje de Comisión (%)</label>
                                            <input type="number" name="commission_rate" class="form-control" step="0.01" value="<?= $config['commission_rate'] * 100 ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Moneda Principal</label>
                                            <select name="default_currency" class="form-select">
                                                <option value="USD" <?= ($config['default_currency'] ?? 'USD') === 'USD' ? 'selected' : '' ?>>USD - Dólar Estadounidense</option>
                                                <option value="DOP" <?= ($config['default_currency'] ?? 'USD') === 'DOP' ? 'selected' : '' ?>>DOP - Peso Dominicano</option>
                                                <option value="EUR" <?= ($config['default_currency'] ?? 'USD') === 'EUR' ? 'selected' : '' ?>>EUR - Euro</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="property_approval_required" id="propertyApproval" <?= $config['property_approval_required'] ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="propertyApproval">
                                                    Requerir aprobación de propiedades
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="email_notifications" id="emailNotifications" <?= $config['email_notifications'] ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="emailNotifications">
                                                    Habilitar notificaciones por email
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Configuración de Seguridad -->
                    <div class="config-section">
                        <h3><i class="fas fa-shield-alt"></i> Configuración de Seguridad</h3>
                        <div class="card config-card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Tiempo de Expiración de Token (segundos)</label>
                                            <input type="number" name="token_expiry" class="form-control" value="<?= $config['token_expiry'] ?? 3600 ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Tiempo de Recuperación de Contraseña (segundos)</label>
                                            <input type="number" name="password_reset_expiry" class="form-control" value="<?= $config['password_reset_expiry'] ?? 1800 ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="force_https" id="forceHttps" <?= ($config['force_https'] ?? false) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="forceHttps">
                                                    Forzar conexión HTTPS
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="enable_captcha" id="enableCaptcha" <?= ($config['enable_captcha'] ?? false) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="enableCaptcha">
                                                    Habilitar CAPTCHA en formularios
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Configuración de Mantenimiento -->
                    <div class="config-section">
                        <h3><i class="fas fa-tools"></i> Configuración de Mantenimiento</h3>
                        <div class="card config-card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="maintenance_mode" id="maintenanceMode" <?= ($config['maintenance_mode'] ?? false) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="maintenanceMode">
                                                    Modo de mantenimiento
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Mensaje de Mantenimiento</label>
                                            <textarea name="maintenance_message" class="form-control" rows="3"><?= htmlspecialchars($config['maintenance_message'] ?? 'El sistema está en mantenimiento. Por favor, intente más tarde.') ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Frecuencia de Backup (días)</label>
                                            <input type="number" name="backup_frequency" class="form-control" value="<?= $config['backup_frequency'] ?? 7 ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Retención de Logs (días)</label>
                                            <input type="number" name="log_retention" class="form-control" value="<?= $config['log_retention'] ?? 30 ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botones de Acción -->
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-save"></i> Guardar Configuración
                        </button>
                        <button type="button" class="btn btn-secondary btn-lg me-3" onclick="resetToDefaults()">
                            <i class="fas fa-undo"></i> Restaurar Valores por Defecto
                        </button>
                        <button type="button" class="btn btn-info btn-lg" onclick="testEmailConfig()">
                            <i class="fas fa-envelope"></i> Probar Configuración de Email
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function saveAllConfig() {
            document.getElementById('configForm').submit();
        }
        
        function resetToDefaults() {
            if (confirm('¿Está seguro de que desea restaurar todos los valores por defecto? Esta acción no se puede deshacer.')) {
                // Aquí se implementaría la lógica para restaurar valores por defecto
                alert('Función de restauración de valores por defecto');
            }
        }
        
        function testEmailConfig() {
            if (confirm('¿Desea enviar un email de prueba para verificar la configuración?')) {
                // Aquí se implementaría la lógica para probar la configuración de email
                alert('Función de prueba de configuración de email');
            }
        }
        
        // Validación del formulario
        document.getElementById('configForm').addEventListener('submit', function(e) {
            const commissionRate = parseFloat(document.querySelector('input[name="commission_rate"]').value);
            
            if (commissionRate < 0 || commissionRate > 100) {
                e.preventDefault();
                alert('El porcentaje de comisión debe estar entre 0 y 100');
                return;
            }
            
            const maxFileSize = parseInt(document.querySelector('input[name="max_file_size"]').value);
            if (maxFileSize <= 0) {
                e.preventDefault();
                alert('El tamaño máximo de archivo debe ser mayor a 0');
                return;
            }
        });
    </script>
</body>
</html> 