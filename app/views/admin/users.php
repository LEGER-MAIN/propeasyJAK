<?php
/**
 * Gestión Completa de Usuarios - Administrador
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Vista para gestionar usuarios con control total (bloquear, eliminar, cambiar roles)
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
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
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
        
        .user-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid var(--admin-primary);
            transition: transform 0.3s ease;
        }
        
        .user-card:hover {
            transform: translateY(-2px);
        }
        
        .user-card.suspended {
            border-left-color: var(--admin-danger);
            opacity: 0.7;
        }
        
        .user-card.admin {
            border-left-color: var(--admin-danger);
        }
        
        .user-card.agent {
            border-left-color: var(--admin-warning);
        }
        
        .user-card.client {
            border-left-color: var(--admin-success);
        }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-active {
            background: var(--admin-success);
            color: white;
        }
        
        .status-suspended {
            background: var(--admin-danger);
            color: white;
        }
        
        .role-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .role-admin {
            background: var(--admin-danger);
            color: white;
        }
        
        .role-agent {
            background: var(--admin-warning);
            color: white;
        }
        
        .role-client {
            background: var(--admin-success);
            color: white;
        }
        
        .action-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            margin: 2px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .action-btn:hover {
            transform: translateY(-1px);
            color: white;
        }
        
        .btn-edit {
            background: var(--admin-info);
            color: white;
        }
        
        .btn-block {
            background: var(--admin-warning);
            color: white;
        }
        
        .btn-unblock {
            background: var(--admin-success);
            color: white;
        }
        
        .btn-delete {
            background: var(--admin-danger);
            color: white;
        }
        
        .btn-change-role {
            background: var(--admin-primary);
            color: white;
        }
        
        .stats-summary {
            background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .filter-section {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
                        <i class="fas fa-users"></i> Gestión de Usuarios
                    </h1>
                    <small>Control total de usuarios del sistema</small>
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
                        <a class="nav-link active" href="/admin/users?action=list">
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
                            <h3><?= number_format(count($users)) ?></h3>
                            <p class="mb-0">Total Usuarios</p>
                        </div>
                        <div class="col-md-3">
                            <h3><?= number_format(array_filter($users, fn($u) => $u['rol'] === 'admin')->count()) ?></h3>
                            <p class="mb-0">Administradores</p>
                        </div>
                        <div class="col-md-3">
                            <h3><?= number_format(array_filter($users, fn($u) => $u['rol'] === 'agente')->count()) ?></h3>
                            <p class="mb-0">Agentes</p>
                        </div>
                        <div class="col-md-3">
                            <h3><?= number_format(array_filter($users, fn($u) => $u['rol'] === 'cliente')->count()) ?></h3>
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
                                                        <strong><?= htmlspecialchars($user['nombre'] . ' ' . $user['apellido']) ?></strong>
                                                        <br>
                                                        <small class="text-muted">@<?= htmlspecialchars($user['username']) ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($user['email']) ?></td>
                                            <td>
                                                <span class="role-badge role-<?= $user['rol'] ?>">
                                                    <?= ucfirst($user['rol']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="status-badge status-<?= $user['estado'] ?>">
                                                    <?= ucfirst($user['estado']) ?>
                                                </span>
                                            </td>
                                            <td><?= date('d/m/Y H:i', strtotime($user['fecha_registro'])) ?></td>
                                            <td><?= $user['ultimo_acceso'] ? date('d/m/Y H:i', strtotime($user['ultimo_acceso'])) : 'Nunca' ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                                            onclick="editUser(<?= $user['id'] ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    
                                                    <?php if ($user['estado'] === 'activo'): ?>
                                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                                                onclick="blockUser(<?= $user['id'] ?>, '<?= htmlspecialchars($user['nombre'] . ' ' . $user['apellido']) ?>')">
                                                            <i class="fas fa-ban"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                                onclick="unblockUser(<?= $user['id'] ?>, '<?= htmlspecialchars($user['nombre'] . ' ' . $user['apellido']) ?>')">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                    

                                                    
                                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                onclick="deleteUser(<?= $user['id'] ?>, '<?= htmlspecialchars($user['nombre'] . ' ' . $user['apellido']) ?>')">
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
            </div>
        </div>
    </div>



    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
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
            // Implementar exportación de usuarios
            alert('Función de exportación en desarrollo');
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
</body>
</html> 
