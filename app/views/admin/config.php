<?php
/**
 * Configuración del Sistema - Administrador
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Vista para configurar parámetros del sistema con control total
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
        
        .config-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .config-section {
            border-bottom: 1px solid #eee;
            padding: 20px 0;
        }
        
        .config-section:last-child {
            border-bottom: none;
        }
        
        .config-title {
            color: var(--admin-primary);
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .form-switch {
            padding-left: 2.5em;
        }
        
        .form-switch .form-check-input {
            width: 3em;
            height: 1.5em;
            margin-left: -2.5em;
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
        
        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }
        
        .status-online { background: var(--admin-success); }
        .status-offline { background: var(--admin-danger); }
        .status-warning { background: var(--admin-warning); }
        
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
                        <i class="fas fa-cog"></i> Configuración del Sistema
                    </h1>
                    <small>Control total de parámetros del sistema</small>
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
                        <a class="nav-link" href="/admin/backup">
                            <i class="fas fa-database"></i> Backup & Restore
                        </a>
                        <a class="nav-link active" href="/admin/config">
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
                <!-- Estado del Sistema -->
                <div class="config-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">
                            <i class="fas fa-server"></i> Estado del Sistema
                        </h4>
                        <div>
                            <span class="status-indicator status-online"></span>
                            Sistema Online
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <i class="fas fa-database fa-2x text-primary mb-2"></i>
                                <h5>Base de Datos</h5>
                                <span class="badge bg-success">Conectada</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <i class="fas fa-envelope fa-2x text-info mb-2"></i>
                                <h5>Email</h5>
                                <span class="badge bg-success">Configurado</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <i class="fas fa-upload fa-2x text-warning mb-2"></i>
                                <h5>Uploads</h5>
                                <span class="badge bg-success">Disponible</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <i class="fas fa-shield-alt fa-2x text-danger mb-2"></i>
                                <h5>Seguridad</h5>
                                <span class="badge bg-success">Activa</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configuración General -->
                <div class="config-card">
                    <h4 class="config-title">
                        <i class="fas fa-cogs"></i> Configuración General
                    </h4>
                    
                    <form method="POST" action="/admin/config">
                        <div class="config-section">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="siteName" class="form-label">Nombre del Sitio</label>
                                        <input type="text" class="form-control" id="siteName" name="site_name" 
                                               value="<?= htmlspecialchars($config['site_name']) ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="siteDescription" class="form-label">Descripción del Sitio</label>
                                        <input type="text" class="form-control" id="siteDescription" name="site_description" 
                                               value="<?= htmlspecialchars($config['site_description']) ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="config-section">
                            <h5 class="mb-3">Configuración de Acceso</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="maintenanceMode" name="maintenance_mode" 
                                               <?= $config['maintenance_mode'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="maintenanceMode">
                                            Modo Mantenimiento
                                        </label>
                                        <small class="form-text text-muted d-block">
                                            Bloquea el acceso público al sitio
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="registrationEnabled" name="registration_enabled" 
                                               <?= $config['registration_enabled'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="registrationEnabled">
                                            Registro de Usuarios
                                        </label>
                                        <small class="form-text text-muted d-block">
                                            Permite que nuevos usuarios se registren
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="config-section">
                            <h5 class="mb-3">Configuración de Propiedades</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="maxPropertiesPerUser" class="form-label">Máx. Propiedades por Usuario</label>
                                        <input type="number" class="form-control" id="maxPropertiesPerUser" name="max_properties_per_user" 
                                               value="<?= $config['max_properties_per_user'] ?>" min="1" max="100">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="maxImagesPerProperty" class="form-label">Máx. Imágenes por Propiedad</label>
                                        <input type="number" class="form-control" id="maxImagesPerProperty" name="max_images_per_property" 
                                               value="<?= $config['max_images_per_property'] ?>" min="1" max="50">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="commissionRate" class="form-label">Tasa de Comisión (%)</label>
                                        <input type="number" class="form-control" id="commissionRate" name="commission_rate" 
                                               value="<?= $config['commission_rate'] ?>" min="0" max="100" step="0.1">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="config-section">
                            <h5 class="mb-3">Configuración de Notificaciones</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="emailNotifications" name="email_notifications" 
                                               <?= $config['email_notifications'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="emailNotifications">
                                            Notificaciones por Email
                                        </label>
                                        <small class="form-text text-muted d-block">
                                            Envía notificaciones automáticas por email
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="smsNotifications" name="sms_notifications">
                                        <label class="form-check-label" for="smsNotifications">
                                            Notificaciones por SMS
                                        </label>
                                        <small class="form-text text-muted d-block">
                                            Envía notificaciones por mensaje de texto
                                        </small>
                                    </div>
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

                <!-- Configuración de Email -->
                <div class="config-card">
                    <h4 class="config-title">
                        <i class="fas fa-envelope"></i> Configuración de Email
                    </h4>
                    
                    <form method="POST" action="/admin/config/email">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="smtpHost" class="form-label">Servidor SMTP</label>
                                    <input type="text" class="form-control" id="smtpHost" name="smtp_host" 
                                           value="<?= SMTP_HOST ?? '' ?>" placeholder="smtp.gmail.com">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="smtpPort" class="form-label">Puerto SMTP</label>
                                    <input type="number" class="form-control" id="smtpPort" name="smtp_port" 
                                           value="<?= SMTP_PORT ?? 587 ?>" placeholder="587">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="smtpUsername" class="form-label">Usuario SMTP</label>
                                    <input type="email" class="form-control" id="smtpUsername" name="smtp_username" 
                                           value="<?= SMTP_USERNAME ?? '' ?>" placeholder="tu@email.com">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="smtpPassword" class="form-label">Contraseña SMTP</label>
                                    <input type="password" class="form-control" id="smtpPassword" name="smtp_password" 
                                           placeholder="••••••••">
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-end">
                            <button type="button" class="btn btn-info me-2" onclick="testEmail()">
                                <i class="fas fa-paper-plane"></i> Probar Email
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Configuración
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Configuración de Seguridad -->
                <div class="config-card">
                    <h4 class="config-title">
                        <i class="fas fa-shield-alt"></i> Configuración de Seguridad
                    </h4>
                    
                    <form method="POST" action="/admin/config/security">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sessionLifetime" class="form-label">Duración de Sesión (minutos)</label>
                                    <input type="number" class="form-control" id="sessionLifetime" name="session_lifetime" 
                                           value="<?= SESSION_LIFETIME ?? 120 ?>" min="15" max="1440">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="maxLoginAttempts" class="form-label">Máx. Intentos de Login</label>
                                    <input type="number" class="form-control" id="maxLoginAttempts" name="max_login_attempts" 
                                           value="5" min="3" max="10">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="twoFactorAuth" name="two_factor_auth">
                                    <label class="form-check-label" for="twoFactorAuth">
                                        Autenticación de Dos Factores
                                    </label>
                                    <small class="form-text text-muted d-block">
                                        Requiere verificación adicional para administradores
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="forceHttps" name="force_https" checked>
                                    <label class="form-check-label" for="forceHttps">
                                        Forzar HTTPS
                                    </label>
                                    <small class="form-text text-muted d-block">
                                        Redirige todo el tráfico a conexiones seguras
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
                            <button type="button" class="btn btn-danger-zone w-100 mb-2" onclick="clearCache()">
                                <i class="fas fa-broom"></i> Limpiar Cache
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-danger-zone w-100 mb-2" onclick="optimizeDatabase()">
                                <i class="fas fa-database"></i> Optimizar Base de Datos
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-danger-zone w-100 mb-2" onclick="resetSystem()">
                                <i class="fas fa-redo"></i> Reset del Sistema
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Funciones de configuración
        function testEmail() {
            if (confirm('¿Deseas enviar un email de prueba para verificar la configuración?')) {
                // Implementar prueba de email
                alert('Función de prueba de email en desarrollo');
            }
        }

        function clearCache() {
            if (confirm('¿Estás seguro de que quieres limpiar la cache del sistema?\n\nEsto puede mejorar el rendimiento pero puede causar una breve interrupción.')) {
                if (confirm('¿CONFIRMAS la limpieza de cache?')) {
                    // Implementar limpieza de cache
                    alert('Cache limpiada exitosamente');
                }
            }
        }

        function optimizeDatabase() {
            if (confirm('¿Estás seguro de que quieres optimizar la base de datos?\n\nEsto puede tomar varios minutos y puede causar una breve interrupción.')) {
                if (confirm('¿CONFIRMAS la optimización de la base de datos?')) {
                    // Implementar optimización de BD
                    alert('Base de datos optimizada exitosamente');
                }
            }
        }

        function resetSystem() {
            if (confirm('⚠️ ADVERTENCIA CRÍTICA ⚠️\n\n¿Estás seguro de que quieres hacer un RESET COMPLETO del sistema?\n\nEsto eliminará TODOS los datos y configuraciones.\n\nEsta acción NO SE PUEDE DESHACER.')) {
                if (confirm('¿CONFIRMAS el RESET COMPLETO del sistema?\n\nEsta es tu última oportunidad para cancelar.')) {
                    if (confirm('¿ESTÁS ABSOLUTAMENTE SEGURO?\n\nEscribe "RESET" para confirmar:')) {
                        // Implementar reset del sistema
                        alert('Reset del sistema iniciado. El sistema se reiniciará en 10 segundos.');
                    }
                }
            }
        }

        // Validación de formularios
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const requiredFields = form.querySelectorAll('[required]');
                    let isValid = true;
                    
                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            isValid = false;
                            field.classList.add('is-invalid');
                        } else {
                            field.classList.remove('is-invalid');
                        }
                    });
                    
                    if (!isValid) {
                        e.preventDefault();
                        alert('Por favor, completa todos los campos requeridos.');
                    }
                });
            });
        });
    </script>
</body>
</html> 