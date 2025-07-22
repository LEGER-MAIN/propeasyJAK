<?php
/**
 * Contenido de Edición de Usuario - Administrador
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 */

// Verificar que el usuario existe
if (!isset($user) || !$user) {
    echo '<div class="alert alert-danger">Usuario no encontrado</div>';
    return;
}
?>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="fas fa-user-edit"></i> Editar Usuario
        </h4>
        <a href="/admin/users" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <form method="POST" action="/admin/users?action=update&id=<?= $user['id'] ?>" class="needs-validation" novalidate>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre *</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" 
                           value="<?= htmlspecialchars($user['nombre'] ?? '') ?>" required>
                    <div class="invalid-feedback">
                        El nombre es requerido
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="apellido" class="form-label">Apellido *</label>
                    <input type="text" class="form-control" id="apellido" name="apellido" 
                           value="<?= htmlspecialchars($user['apellido'] ?? '') ?>" required>
                    <div class="invalid-feedback">
                        El apellido es requerido
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                    <div class="invalid-feedback">
                        Email válido requerido
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input type="tel" class="form-control" id="telefono" name="telefono" 
                           value="<?= htmlspecialchars($user['telefono'] ?? '') ?>">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="rol" class="form-label">Rol *</label>
                    <select class="form-select" id="rol" name="rol" required>
                        <option value="">Seleccionar rol</option>
                        <option value="cliente" <?= ($user['rol'] ?? '') === 'cliente' ? 'selected' : '' ?>>Cliente</option>
                        <option value="agente" <?= ($user['rol'] ?? '') === 'agente' ? 'selected' : '' ?>>Agente</option>
                        <option value="admin" <?= ($user['rol'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrador</option>
                    </select>
                    <div class="invalid-feedback">
                        Selecciona un rol
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="estado" class="form-label">Estado *</label>
                    <select class="form-select" id="estado" name="estado" required>
                        <option value="">Seleccionar estado</option>
                        <option value="activo" <?= ($user['estado'] ?? '') === 'activo' ? 'selected' : '' ?>>Activo</option>
                        <option value="suspendido" <?= ($user['estado'] ?? '') === 'suspendido' ? 'selected' : '' ?>>Suspendido</option>
                    </select>
                    <div class="invalid-feedback">
                        Selecciona un estado
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="ciudad" class="form-label">Ciudad</label>
                    <input type="text" class="form-control" id="ciudad" name="ciudad" 
                           value="<?= htmlspecialchars($user['ciudad'] ?? '') ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="sector" class="form-label">Sector</label>
                    <input type="text" class="form-control" id="sector" name="sector" 
                           value="<?= htmlspecialchars($user['sector'] ?? '') ?>">
                </div>
            </div>
        </div>



        <!-- Información del usuario -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle"></i> Información del Usuario
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <strong>ID:</strong> <?= $user['id'] ?>
                    </div>
                    <div class="col-md-4">
                        <strong>Fecha Registro:</strong> 
                        <?= isset($user['fecha_registro']) ? date('d/m/Y H:i', strtotime($user['fecha_registro'])) : 'N/A' ?>
                    </div>
                    <div class="col-md-4">
                        <strong>Último Acceso:</strong> 
                        <?= isset($user['ultimo_acceso']) && $user['ultimo_acceso'] ? date('d/m/Y H:i', strtotime($user['ultimo_acceso'])) : 'Nunca' ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <div>
                <?php if ($user['estado'] === 'activo'): ?>
                    <button type="button" class="btn btn-warning me-2" onclick="showBlockUserModal(<?= $user['id'] ?>, '<?= htmlspecialchars($user['nombre'] . ' ' . $user['apellido']) ?>')">
                        <i class="fas fa-ban"></i> Bloquear Usuario
                    </button>
                <?php else: ?>
                    <button type="button" class="btn btn-success me-2" onclick="unblockUser(<?= $user['id'] ?>, '<?= htmlspecialchars($user['nombre'] . ' ' . $user['apellido']) ?>')">
                        <i class="fas fa-check"></i> Desbloquear Usuario
                    </button>
                <?php endif; ?>
                <button type="button" class="btn btn-info me-2" onclick="showChangeRoleModal(<?= $user['id'] ?>, '<?= htmlspecialchars($user['nombre'] . ' ' . $user['apellido']) ?>', '<?= $user['rol'] ?>')">
                    <i class="fas fa-user-tag"></i> Cambiar Rol
                </button>
                <button type="button" class="btn btn-danger" onclick="deleteUser(<?= $user['id'] ?>, '<?= htmlspecialchars($user['nombre'] . ' ' . $user['apellido']) ?>')">
                    <i class="fas fa-trash"></i> Eliminar Usuario
                </button>
            </div>
            <div>
                <a href="/admin/users" class="btn btn-secondary me-2">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </div>
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
                }
                

                
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

function deleteUser(userId, userName) {
    if (confirm(`¿Estás seguro de que quieres ELIMINAR PERMANENTEMENTE al usuario "${userName}"?\n\n⚠️ ESTA ACCIÓN NO SE PUEDE DESHACER ⚠️`)) {
        if (confirm('¿CONFIRMAS la eliminación? Esta es tu última oportunidad para cancelar.')) {
            window.location.href = `/admin/users?action=delete&id=${userId}`;
        }
    }
}

function showBlockUserModal(userId, userName) {
    document.getElementById('blockUserModal').style.display = 'block';
    document.getElementById('blockUserName').textContent = userName;
    document.getElementById('blockUserId').value = userId;
}

function hideBlockUserModal() {
    document.getElementById('blockUserModal').style.display = 'none';
    document.getElementById('blockReason').value = '';
}

function blockUser() {
    const reason = document.getElementById('blockReason').value.trim();
    const userId = document.getElementById('blockUserId').value;
    
    if (!reason) {
        alert('Debe proporcionar una razón para el bloqueo');
        return;
    }
    
    if (confirm(`¿Estás seguro de que quieres BLOQUEAR al usuario?\n\nRazón: ${reason}\n\n⚠️ Esta acción enviará una notificación por correo al usuario.`)) {
        // Crear formulario temporal y enviarlo
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/users?action=block&id=' + userId;
        
        const reasonInput = document.createElement('input');
        reasonInput.type = 'hidden';
        reasonInput.name = 'block_reason';
        reasonInput.value = reason;
        
        form.appendChild(reasonInput);
        document.body.appendChild(form);
        form.submit();
    }
}

function unblockUser(userId, userName) {
    if (confirm(`¿Estás seguro de que quieres DESBLOQUEAR al usuario "${userName}"?`)) {
        window.location.href = `/admin/users?action=unblock&id=${userId}`;
    }
}

function showChangeRoleModal(userId, userName, currentRole) {
    document.getElementById('changeRoleModal').style.display = 'block';
    document.getElementById('changeRoleUserName').textContent = userName;
    document.getElementById('changeRoleUserId').value = userId;
    document.getElementById('changeRoleCurrentRole').textContent = currentRole;
    
    // Seleccionar el rol actual en el dropdown
    const roleSelect = document.getElementById('newRole');
    roleSelect.value = currentRole;
}

function hideChangeRoleModal() {
    document.getElementById('changeRoleModal').style.display = 'none';
    document.getElementById('changeRoleReason').value = '';
}

function changeUserRole() {
    const newRole = document.getElementById('newRole').value;
    const reason = document.getElementById('changeRoleReason').value.trim();
    const userId = document.getElementById('changeRoleUserId').value;
    const userName = document.getElementById('changeRoleUserName').textContent;
    const currentRole = document.getElementById('changeRoleCurrentRole').textContent;
    
    if (newRole === currentRole) {
        alert('El nuevo rol debe ser diferente al rol actual');
        return;
    }
    
    const confirmMessage = `¿Estás seguro de que quieres cambiar el rol de "${userName}"?\n\n` +
                          `Rol actual: ${currentRole}\n` +
                          `Nuevo rol: ${newRole}\n` +
                          (reason ? `Motivo: ${reason}\n` : '') +
                          '\n⚠️ Esta acción puede enviar una notificación por correo al usuario.';
    
    if (confirm(confirmMessage)) {
        // Crear formulario temporal y enviarlo
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/users/change-role/' + userId;
        
        const roleInput = document.createElement('input');
        roleInput.type = 'hidden';
        roleInput.name = 'new_role';
        roleInput.value = newRole;
        
        const reasonInput = document.createElement('input');
        reasonInput.type = 'hidden';
        reasonInput.name = 'change_reason';
        reasonInput.value = reason;
        
        form.appendChild(roleInput);
        form.appendChild(reasonInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<!-- Modal para bloquear usuario -->
<div id="blockUserModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-ban"></i> Bloquear Usuario
                </h5>
                <button type="button" class="btn-close" onclick="hideBlockUserModal()"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <h6><i class="fas fa-exclamation-triangle"></i> Advertencia</h6>
                    <p class="mb-0">Estás a punto de bloquear al usuario: <strong id="blockUserName"></strong></p>
                </div>
                
                <div class="mb-3">
                    <label for="blockReason" class="form-label">
                        <strong>Motivo del Bloqueo *</strong>
                    </label>
                    <textarea class="form-control" id="blockReason" rows="4" 
                              placeholder="Describe detalladamente el motivo por el cual se bloquea al usuario. Esta información será enviada al usuario por correo electrónico."></textarea>
                    <div class="form-text">
                        <i class="fas fa-info-circle"></i> Esta descripción será enviada al usuario por correo electrónico para informarle sobre el motivo de su suspensión.
                    </div>
                </div>
                
                <div class="alert alert-info">
                    <h6><i class="fas fa-envelope"></i> Notificación Automática</h6>
                    <p class="mb-0">Al confirmar esta acción, se enviará automáticamente un correo electrónico al usuario informándole sobre la suspensión de su cuenta y el motivo proporcionado.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="hideBlockUserModal()">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-warning" onclick="blockUser()">
                    <i class="fas fa-ban"></i> Confirmar Bloqueo
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Campo oculto para el ID del usuario -->
<input type="hidden" id="blockUserId" value="">

<!-- Modal para cambiar rol de usuario -->
<div id="changeRoleModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user-tag"></i> Cambiar Rol de Usuario
                </h5>
                <button type="button" class="btn-close btn-close-white" onclick="hideChangeRoleModal()"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle"></i> Información</h6>
                    <p class="mb-0">Estás a punto de cambiar el rol del usuario: <strong id="changeRoleUserName"></strong></p>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">
                                <strong>Rol Actual</strong>
                            </label>
                            <div class="form-control-plaintext">
                                <span id="changeRoleCurrentRole" class="badge bg-secondary"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="newRole" class="form-label">
                                <strong>Nuevo Rol *</strong>
                            </label>
                            <select class="form-select" id="newRole" required>
                                <option value="">Selecciona un rol</option>
                                <option value="cliente">Cliente</option>
                                <option value="agente">Agente Inmobiliario</option>
                                <option value="admin">Administrador</option>
                            </select>
                            <div class="form-text">
                                <i class="fas fa-info-circle"></i> Selecciona el nuevo rol que tendrá el usuario en el sistema.
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="changeRoleReason" class="form-label">
                        <strong>Motivo del Cambio (Opcional)</strong>
                    </label>
                    <textarea class="form-control" id="changeRoleReason" rows="3" 
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
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="hideChangeRoleModal()">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-info" onclick="changeUserRole()">
                    <i class="fas fa-user-tag"></i> Confirmar Cambio
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Campos ocultos para el modal de cambio de rol -->
<input type="hidden" id="changeRoleUserId" value=""> 