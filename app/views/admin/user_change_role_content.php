<?php
/**
 * Vista para cambiar rol de usuario
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 */
?>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="fas fa-user-tag"></i> Cambiar Rol de Usuario
        </h4>
        <a href="/admin/users" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="alert alert-info">
        <h6><i class="fas fa-info-circle"></i> Información del Usuario</h6>
        <p class="mb-0">
            <strong>Nombre:</strong> <?= htmlspecialchars($user['nombre'] . ' ' . $user['apellido']) ?><br>
            <strong>Email:</strong> <?= htmlspecialchars($user['email']) ?><br>
            <strong>Rol Actual:</strong> 
            <span class="badge bg-<?= $user['rol'] === 'admin' ? 'danger' : ($user['rol'] === 'agente' ? 'primary' : 'secondary') ?>">
                <?= ucfirst($user['rol']) ?>
            </span>
        </p>
    </div>

    <form method="POST" action="/admin/users/change-role/<?= $user['id'] ?>" class="needs-validation" novalidate>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="new_role" class="form-label">
                        <strong>Nuevo Rol *</strong>
                    </label>
                    <select class="form-select" id="new_role" name="new_role" required>
                        <option value="">Selecciona un rol</option>
                        <option value="cliente" <?= $user['rol'] === 'cliente' ? 'selected' : '' ?>>Cliente</option>
                        <option value="agente" <?= $user['rol'] === 'agente' ? 'selected' : '' ?>>Agente Inmobiliario</option>
                        <option value="admin" <?= $user['rol'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                    </select>
                    <div class="form-text">
                        <i class="fas fa-info-circle"></i> Selecciona el nuevo rol que tendrá el usuario en el sistema.
                    </div>
                    <div class="invalid-feedback">
                        Selecciona un rol válido
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="change_reason" class="form-label">
                <strong>Motivo del Cambio (Opcional)</strong>
            </label>
            <textarea class="form-control" id="change_reason" name="change_reason" rows="4" 
                      placeholder="Describe el motivo del cambio de rol. Esta información puede ser enviada al usuario por correo electrónico."></textarea>
            <div class="form-text">
                <i class="fas fa-info-circle"></i> Si proporcionas un motivo, se enviará una notificación por correo al usuario.
            </div>
        </div>

        <div class="alert alert-warning">
            <h6><i class="fas fa-exclamation-triangle"></i> Importante</h6>
            <ul class="mb-0">
                <li>El cambio de rol afectará los permisos y funcionalidades disponibles para el usuario</li>
                <li>Los administradores tienen acceso completo al sistema</li>
                <li>Los agentes pueden gestionar propiedades y citas</li>
                <li>Los clientes pueden buscar propiedades y solicitar citas</li>
                <?php if ($user['id'] == $_SESSION['user_id']): ?>
                <li><strong class="text-danger">⚠️ No puedes cambiar tu propio rol de administrador</strong></li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="d-flex justify-content-between">
            <a href="/admin/users" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancelar
            </a>
            <button type="submit" class="btn btn-info" <?= $user['id'] == $_SESSION['user_id'] ? 'disabled' : '' ?>>
                <i class="fas fa-user-tag"></i> Confirmar Cambio de Rol
            </button>
        </div>
    </form>
</div>

<script>
// Validación del formulario
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                } else {
                    // Validación adicional
                    var newRole = document.getElementById('new_role').value;
                    var currentRole = '<?= $user['rol'] ?>';
                    
                    if (newRole === currentRole) {
                        event.preventDefault();
                        alert('El nuevo rol debe ser diferente al rol actual');
                        return;
                    }
                    
                    // Confirmación final
                    var userName = '<?= htmlspecialchars($user['nombre'] . ' ' . $user['apellido']) ?>';
                    var reason = document.getElementById('change_reason').value.trim();
                    
                    var confirmMessage = `¿Estás seguro de que quieres cambiar el rol de "${userName}"?\n\n` +
                                        `Rol actual: ${currentRole}\n` +
                                        `Nuevo rol: ${newRole}\n` +
                                        (reason ? `Motivo: ${reason}\n` : '') +
                                        '\n⚠️ Esta acción puede enviar una notificación por correo al usuario.';
                    
                    if (!confirm(confirmMessage)) {
                        event.preventDefault();
                        return;
                    }
                }
                
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();
</script> 