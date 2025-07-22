<?php
/**
 * Contenido de Gestión de Usuarios - Administrador
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este archivo contiene solo el contenido de gestión de usuarios, sin estructura HTML completa
 */

// El rol ya fue verificado en el AdminController
?>

<!-- Resumen de Estadísticas -->
<div class="stats-summary">
    <div class="row text-center">
        <div class="col-md-3">
            <h3><?= number_format($stats['total'] ?? 0) ?></h3>
            <p class="mb-0">Total Usuarios</p>
        </div>
        <div class="col-md-3">
            <h3><?= number_format($stats['admins'] ?? 0) ?></h3>
            <p class="mb-0">Administradores</p>
        </div>
        <div class="col-md-3">
            <h3><?= number_format($stats['agentes'] ?? 0) ?></h3>
            <p class="mb-0">Agentes</p>
        </div>
        <div class="col-md-3">
            <h3><?= number_format($stats['clientes'] ?? 0) ?></h3>
            <p class="mb-0">Clientes</p>
        </div>
    </div>
</div>

<!-- Filtros y Búsqueda -->
<div class="filter-section">
    <div class="row align-items-center">
        <div class="col-md-4">
            <label for="roleFilter" class="form-label">Filtrar por Rol:</label>
            <select class="form-select" id="roleFilter">
                <option value="">Todos los roles</option>
                <option value="admin">Administradores</option>
                <option value="agente">Agentes</option>
                <option value="cliente">Clientes</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="statusFilter" class="form-label">Filtrar por Estado:</label>
            <select class="form-select" id="statusFilter">
                <option value="">Todos los estados</option>
                <option value="activo">Activos</option>
                <option value="suspendido">Suspendidos</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="searchUser" class="form-label">Buscar Usuario:</label>
            <input type="text" class="form-control" id="searchUser" placeholder="Nombre, email...">
        </div>
    </div>
</div>

<!-- Lista de Usuarios -->
<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="fas fa-list"></i> Lista de Usuarios
        </h4>
        <div>
            <button class="btn btn-success" onclick="exportUsers()">
                <i class="fas fa-download"></i> Exportar
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover" id="usersTable">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Fecha Registro</th>
                    <th>Último Acceso</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <tr class="user-row <?= $user['estado'] === 'suspendido' ? 'table-danger' : '' ?>">
                            <td><?= $user['id'] ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-user-circle fa-2x text-muted"></i>
                                    </div>
                                    <div>
                                        <strong><?= htmlspecialchars(($user['nombre'] ?? '') . ' ' . ($user['apellido'] ?? '')) ?></strong>
                                        <br>
                                        <small class="text-muted">@<?= htmlspecialchars($user['username'] ?? $user['email'] ?? 'usuario') ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($user['email'] ?? '') ?></td>
                            <td>
                                <span class="role-badge role-<?= $user['rol'] ?? 'cliente' ?>">
                                    <?= ucfirst($user['rol'] ?? 'cliente') ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-<?= $user['estado'] ?? 'activo' ?>">
                                    <?= ucfirst($user['estado'] ?? 'activo') ?>
                                </span>
                            </td>
                            <td><?= isset($user['fecha_registro']) ? date('d/m/Y H:i', strtotime($user['fecha_registro'])) : 'N/A' ?></td>
                            <td><?= isset($user['ultimo_acceso']) && $user['ultimo_acceso'] ? date('d/m/Y H:i', strtotime($user['ultimo_acceso'])) : 'Nunca' ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            onclick="editUser(<?= $user['id'] ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    
                                    <?php if (($user['estado'] ?? 'activo') === 'activo'): ?>
                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                                onclick="blockUser(<?= $user['id'] ?>, '<?= htmlspecialchars(($user['nombre'] ?? '') . ' ' . ($user['apellido'] ?? '')) ?>')">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                onclick="unblockUser(<?= $user['id'] ?>, '<?= htmlspecialchars(($user['nombre'] ?? '') . ' ' . ($user['apellido'] ?? '')) ?>')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    <?php endif; ?>
                                    

                                    
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteUser(<?= $user['id'] ?>, '<?= htmlspecialchars(($user['nombre'] ?? '') . ' ' . ($user['apellido'] ?? '')) ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay usuarios registrados</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>



<!-- Estilos específicos -->
<style>
.role-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: bold;
    text-transform: uppercase;
}

.role-admin {
    background-color: #dc3545;
    color: white;
}

.role-agente {
    background-color: #fd7e14;
    color: white;
}

.role-cliente {
    background-color: #20c997;
    color: white;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: bold;
    text-transform: uppercase;
}

.status-activo {
    background-color: #198754;
    color: white;
}

.status-suspendido {
    background-color: #6c757d;
    color: white;
}

.filter-section {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
}

.content-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
</style>

<!-- Scripts específicos -->
<script>
    // Inicializar DataTable
    $(document).ready(function() {
        $('#usersTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            },
            pageLength: 25,
            order: [[0, 'desc']]
        });
    });

    // Funciones de gestión de usuarios
    function editUser(userId) {
        window.location.href = `/admin/users?action=edit&id=${userId}`;
    }

    function blockUser(userId, userName) {
        if (confirm(`¿Estás seguro de que quieres BLOQUEAR al usuario "${userName}"?\n\nEsta acción impedirá que el usuario acceda al sistema.`)) {
            window.location.href = `/admin/users?action=block&id=${userId}`;
        }
    }

    function unblockUser(userId, userName) {
        if (confirm(`¿Estás seguro de que quieres DESBLOQUEAR al usuario "${userName}"?\n\nEsta acción permitirá que el usuario acceda nuevamente al sistema.`)) {
            window.location.href = `/admin/users?action=unblock&id=${userId}`;
        }
    }



    function deleteUser(userId, userName) {
        if (confirm(`¿Estás seguro de que quieres ELIMINAR PERMANENTEMENTE al usuario "${userName}"?\n\n⚠️ ESTA ACCIÓN NO SE PUEDE DESHACER ⚠️\n\nSe eliminarán todos los datos asociados al usuario.`)) {
            if (confirm('¿CONFIRMAS la eliminación? Esta es tu última oportunidad para cancelar.')) {
                window.location.href = `/admin/users?action=delete&id=${userId}`;
            }
        }
    }

    function exportUsers() {
        // Exportar usuarios a CSV
        window.location.href = '/admin/users?action=export';
    }

    // Filtros
    $('#roleFilter, #statusFilter').change(function() {
        const role = $('#roleFilter').val();
        const status = $('#statusFilter').val();
        
        $('#usersTable tbody tr').each(function() {
            const row = $(this);
            const userRole = row.find('td:nth-child(4)').text().toLowerCase().trim();
            const userStatus = row.find('td:nth-child(5)').text().toLowerCase().trim();
            
            let show = true;
            
            if (role && userRole !== role) show = false;
            if (status && userStatus !== status) show = false;
            
            row.toggle(show);
        });
    });

    $('#searchUser').keyup(function() {
        const searchTerm = $(this).val().toLowerCase();
        
        $('#usersTable tbody tr').each(function() {
            const row = $(this);
            const userName = row.find('td:nth-child(2)').text().toLowerCase();
            const userEmail = row.find('td:nth-child(3)').text().toLowerCase();
            
            if (userName.includes(searchTerm) || userEmail.includes(searchTerm)) {
                row.show();
            } else {
                row.hide();
            }
        });
    });
</script> 