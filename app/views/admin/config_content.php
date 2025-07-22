<?php
/**
 * Contenido de Configuración del Sistema - Administrador
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este archivo contiene solo el contenido de configuración del sistema, sin estructura HTML completa
 */

// El rol ya fue verificado en el AdminController
?>

<!-- Configuración General -->
<div class="content-card">
    <h4 class="mb-4">
        <i class="fas fa-cog"></i> Configuración General
    </h4>
    
    <form method="POST" action="/admin/config">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="site_name" class="form-label">Nombre del Sitio</label>
                    <input type="text" class="form-control" id="site_name" name="site_name" 
                           value="<?= htmlspecialchars($config['site_name']) ?>" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="site_description" class="form-label">Descripción del Sitio</label>
                    <input type="text" class="form-control" id="site_description" name="site_description" 
                           value="<?= htmlspecialchars($config['site_description']) ?>">
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" 
                               <?= $config['maintenance_mode'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="maintenance_mode">
                            Modo Mantenimiento
                        </label>
                    </div>
                    <small class="text-muted">Activa el modo mantenimiento para realizar actualizaciones</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="registration_enabled" name="registration_enabled" 
                               <?= $config['registration_enabled'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="registration_enabled">
                            Registro Habilitado
                        </label>
                    </div>
                    <small class="text-muted">Permite a nuevos usuarios registrarse</small>
                </div>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Guardar Configuración General
        </button>
    </form>
</div>

<!-- Configuración de Propiedades -->
<div class="content-card">
    <h4 class="mb-4">
        <i class="fas fa-home"></i> Configuración de Propiedades
    </h4>
    
    <form method="POST" action="/admin/config">
        <div class="row">
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="max_properties_per_user" class="form-label">Máximo Propiedades por Usuario</label>
                    <input type="number" class="form-control" id="max_properties_per_user" name="max_properties_per_user" 
                           value="<?= $config['max_properties_per_user'] ?>" min="1" max="100">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="max_images_per_property" class="form-label">Máximo Imágenes por Propiedad</label>
                    <input type="number" class="form-control" id="max_images_per_property" name="max_images_per_property" 
                           value="<?= $config['max_images_per_property'] ?>" min="1" max="50">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="commission_rate" class="form-label">Tasa de Comisión (%)</label>
                    <input type="number" class="form-control" id="commission_rate" name="commission_rate" 
                           value="<?= $config['commission_rate'] ?>" min="0" max="100" step="0.1">
                </div>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Guardar Configuración de Propiedades
        </button>
    </form>
</div>

<!-- Configuración de Notificaciones -->
<div class="content-card">
    <h4 class="mb-4">
        <i class="fas fa-bell"></i> Configuración de Notificaciones
    </h4>
    
    <form method="POST" action="/admin/config">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" 
                               <?= $config['email_notifications'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="email_notifications">
                            Notificaciones por Email
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="sms_notifications" name="sms_notifications" 
                               <?= $config['sms_notifications'] ?? false ? 'checked' : '' ?>>
                        <label class="form-check-label" for="sms_notifications">
                            Notificaciones por SMS
                        </label>
                    </div>
                </div>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Guardar Configuración de Notificaciones
        </button>
    </form>
</div>

<!-- Configuración de Email SMTP -->
<div class="content-card">
    <h4 class="mb-4">
        <i class="fas fa-envelope"></i> Configuración de Email SMTP
    </h4>
    
    <form method="POST" action="/admin/config/email">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="smtp_host" class="form-label">Servidor SMTP</label>
                    <input type="text" class="form-control" id="smtp_host" name="smtp_host" 
                           value="<?= $config['smtp_host'] ?? '' ?>" placeholder="smtp.gmail.com">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="smtp_port" class="form-label">Puerto SMTP</label>
                    <input type="number" class="form-control" id="smtp_port" name="smtp_port" 
                           value="<?= $config['smtp_port'] ?? '587' ?>" placeholder="587">
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="smtp_username" class="form-label">Usuario SMTP</label>
                    <input type="email" class="form-control" id="smtp_username" name="smtp_username" 
                           value="<?= $config['smtp_username'] ?? '' ?>" placeholder="tu-email@gmail.com">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="smtp_password" class="form-label">Contraseña SMTP</label>
                    <input type="password" class="form-control" id="smtp_password" name="smtp_password" 
                           value="<?= $config['smtp_password'] ?? '' ?>">
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="smtp_encryption" name="smtp_encryption" 
                               <?= $config['smtp_encryption'] ?? true ? 'checked' : '' ?>>
                        <label class="form-check-label" for="smtp_encryption">
                            Usar Encriptación TLS
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <button type="button" class="btn btn-outline-info" onclick="testEmailConfig()">
                        <i class="fas fa-paper-plane"></i> Probar Configuración
                    </button>
                </div>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Guardar Configuración SMTP
        </button>
    </form>
</div>

<!-- Configuración de Seguridad -->
<div class="content-card">
    <h4 class="mb-4">
        <i class="fas fa-shield-alt"></i> Configuración de Seguridad
    </h4>
    
    <form method="POST" action="/admin/config/security">
        <div class="row">
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="session_lifetime" class="form-label">Duración de Sesión (minutos)</label>
                    <input type="number" class="form-control" id="session_lifetime" name="session_lifetime" 
                           value="<?= $config['session_lifetime'] ?? 120 ?>" min="15" max="1440">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="max_login_attempts" class="form-label">Máximo Intentos de Login</label>
                    <input type="number" class="form-control" id="max_login_attempts" name="max_login_attempts" 
                           value="<?= $config['max_login_attempts'] ?? 5 ?>" min="3" max="10">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="lockout_duration" class="form-label">Duración de Bloqueo (minutos)</label>
                    <input type="number" class="form-control" id="lockout_duration" name="lockout_duration" 
                           value="<?= $config['lockout_duration'] ?? 30 ?>" min="5" max="1440">
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="two_factor_auth" name="two_factor_auth" 
                               <?= $config['two_factor_auth'] ?? false ? 'checked' : '' ?>>
                        <label class="form-check-label" for="two_factor_auth">
                            Autenticación de Dos Factores (2FA)
                        </label>
                    </div>
                    <small class="text-muted">Recomendado para agentes y administradores</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="force_https" name="force_https" 
                               <?= $config['force_https'] ?? false ? 'checked' : '' ?>>
                        <label class="form-check-label" for="force_https">
                            Forzar HTTPS
                        </label>
                    </div>
                    <small class="text-muted">Redirige todo el tráfico a HTTPS</small>
                </div>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Guardar Configuración de Seguridad
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
            <button type="button" class="btn btn-danger-zone w-100 mb-2" onclick="systemReset()">
                <i class="fas fa-redo"></i> Reset del Sistema
            </button>
        </div>
    </div>
    
    <div class="alert alert-warning mt-3">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>Advertencia:</strong> Las acciones en esta zona pueden afectar el funcionamiento del sistema. 
        Asegúrate de tener un backup antes de proceder.
    </div>
</div>

<!-- Scripts específicos -->
<script>
    function testEmailConfig() {
        if (confirm('¿Deseas enviar un email de prueba para verificar la configuración SMTP?')) {
            // Implementar lógica de prueba de email
            alert('Función de prueba de email en desarrollo');
        }
    }
    
    function clearCache() {
        if (confirm('¿Estás seguro de que quieres limpiar el cache del sistema?\n\nEsto puede mejorar el rendimiento pero también puede causar una breve interrupción.')) {
            // Implementar limpieza de cache
            alert('Función de limpieza de cache en desarrollo');
        }
    }
    
    function optimizeDatabase() {
        if (confirm('¿Estás seguro de que quieres optimizar la base de datos?\n\nEsta acción puede tomar varios minutos.')) {
            // Implementar optimización de base de datos
            alert('Función de optimización de base de datos en desarrollo');
        }
    }
    
    function systemReset() {
        if (confirm('⚠️ ADVERTENCIA CRÍTICA ⚠️\n\n¿Estás seguro de que quieres hacer un reset completo del sistema?\n\nEsta acción:\n- Eliminará todos los datos\n- Restaurará configuraciones por defecto\n- NO SE PUEDE DESHACER\n\n¿CONFIRMAS esta acción?')) {
            if (confirm('¿CONFIRMAS por segunda vez? Esta es tu última oportunidad para cancelar.')) {
                // Implementar reset del sistema
                alert('Función de reset del sistema en desarrollo');
            }
        }
    }
</script> 