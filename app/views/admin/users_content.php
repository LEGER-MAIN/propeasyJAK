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
    <form method="GET" action="/admin/users" id="searchForm">
        <input type="hidden" name="action" value="list">
        <div class="row align-items-end">
            <div class="col-md-3">
                <label for="roleFilter" class="form-label">Filtrar por Rol:</label>
                <select class="form-select" id="roleFilter" name="role">
                    <option value="">Todos los roles</option>
                    <option value="admin" <?= ($_GET['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Administradores</option>
                    <option value="agente" <?= ($_GET['role'] ?? '') === 'agente' ? 'selected' : '' ?>>Agentes</option>
                    <option value="cliente" <?= ($_GET['role'] ?? '') === 'cliente' ? 'selected' : '' ?>>Clientes</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="statusFilter" class="form-label">Filtrar por Estado:</label>
                <select class="form-select" id="statusFilter" name="status">
                    <option value="">Todos los estados</option>
                    <option value="activo" <?= ($_GET['status'] ?? '') === 'activo' ? 'selected' : '' ?>>Activos</option>
                    <option value="suspendido" <?= ($_GET['status'] ?? '') === 'suspendido' ? 'selected' : '' ?>>Suspendidos</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="searchUser" class="form-label">Buscar Usuario:</label>
                <input type="text" class="form-control" id="searchUser" name="search" placeholder="Nombre, email..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-12">
                <?php if (!empty($_GET['search']) || !empty($_GET['role']) || !empty($_GET['status'])): ?>
                    <div class="alert alert-info alert-sm mb-2">
                        <i class="fas fa-filter"></i> 
                        <strong>Filtros activos:</strong>
                        <?php if (!empty($_GET['search'])): ?>
                            <span class="badge bg-primary me-1">Búsqueda: "<?= htmlspecialchars($_GET['search']) ?>"</span>
                        <?php endif; ?>
                        <?php if (!empty($_GET['role'])): ?>
                            <span class="badge bg-info me-1">Rol: <?= ucfirst($_GET['role']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($_GET['status'])): ?>
                            <span class="badge bg-warning me-1">Estado: <?= ucfirst($_GET['status']) ?></span>
                        <?php endif; ?>
                        <a href="/admin/users?action=list" class="btn btn-outline-secondary btn-sm ms-2">
                            <i class="fas fa-times"></i> Limpiar Filtros
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<!-- Lista de Usuarios -->
<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">
                <i class="fas fa-list"></i> Lista de Usuarios
            </h4>
            <small class="text-muted">
                <?php if (!empty($_GET['search']) || !empty($_GET['role']) || !empty($_GET['status'])): ?>
                    Mostrando <?= count($users) ?> resultado<?= count($users) !== 1 ? 's' : '' ?>
                    <?php if (count($users) === 0): ?>
                        - No se encontraron usuarios con los filtros aplicados
                    <?php endif; ?>
                <?php else: ?>
                    Total: <?= count($users) ?> usuario<?= count($users) !== 1 ? 's' : '' ?>
                <?php endif; ?>
            </small>
        </div>
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
                                        <strong>
                                            <?php 
                                            $fullName = ($user['nombre'] ?? '') . ' ' . ($user['apellido'] ?? '');
                                            if (!empty($_GET['search'])) {
                                                $searchTerm = $_GET['search'];
                                                $highlightedName = str_ireplace($searchTerm, '<span class="search-highlight">' . $searchTerm . '</span>', htmlspecialchars($fullName));
                                                echo $highlightedName;
                                            } else {
                                                echo htmlspecialchars($fullName);
                                            }
                                            ?>
                                        </strong>
                                        <br>
                                        <small class="text-muted">
                                            @<?php 
                                            $username = $user['username'] ?? $user['email'] ?? 'usuario';
                                            if (!empty($_GET['search'])) {
                                                $searchTerm = $_GET['search'];
                                                $highlightedUsername = str_ireplace($searchTerm, '<span class="search-highlight">' . $searchTerm . '</span>', htmlspecialchars($username));
                                                echo $highlightedUsername;
                                            } else {
                                                echo htmlspecialchars($username);
                                            }
                                            ?>
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php 
                                $email = $user['email'] ?? '';
                                if (!empty($_GET['search']) && !empty($email)) {
                                    $searchTerm = $_GET['search'];
                                    $highlightedEmail = str_ireplace($searchTerm, '<span class="search-highlight">' . $searchTerm . '</span>', htmlspecialchars($email));
                                    echo $highlightedEmail;
                                } else {
                                    echo htmlspecialchars($email);
                                }
                                ?>
                            </td>
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
    border: 1px solid #e9ecef;
}

.filter-section form {
    margin-bottom: 0;
}

.alert-sm {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
}

.search-highlight {
    background-color: #fff3cd;
    padding: 2px 4px;
    border-radius: 3px;
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
        // Exportar usuarios a CSV con filtros aplicados
        const searchParams = new URLSearchParams(window.location.search);
        searchParams.set('action', 'export');
        window.location.href = '/admin/users?' + searchParams.toString();
    }

    // Búsqueda en tiempo real (opcional - para mejor UX)
    let searchTimeout;
    $('#searchUser').on('input', function() {
        clearTimeout(searchTimeout);
        const searchTerm = $(this).val();
        
        // Si el campo está vacío, no hacer búsqueda automática
        if (searchTerm.length === 0) {
            return;
        }
        
        // Esperar 500ms después de que el usuario deje de escribir
        searchTimeout = setTimeout(function() {
            $('#searchForm').submit();
        }, 500);
    });
    
    // Búsqueda al presionar Enter
    $('#searchUser').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            e.preventDefault();
            $('#searchForm').submit();
        }
    });
    
    // Auto-submit al cambiar filtros
    $('#roleFilter, #statusFilter').change(function() {
        $('#searchForm').submit();
    });
</script> 
