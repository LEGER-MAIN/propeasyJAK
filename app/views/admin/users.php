<?php
/**
 * Gestión de Usuarios - Panel Administrativo
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Vista para gestionar usuarios del sistema (agentes, clientes, administradores)
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
        
        .user-card {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .status-badge {
            font-size: 0.8rem;
            padding: 5px 10px;
        }
        
        .role-badge {
            font-size: 0.7rem;
            padding: 3px 8px;
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
                    <a class="nav-link active" href="/admin/users">
                        <i class="fas fa-users"></i> Usuarios
                    </a>
                    <a class="nav-link" href="/admin/reports">
                        <i class="fas fa-chart-bar"></i> Reportes
                    </a>
                    <a class="nav-link" href="/admin/config">
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
                    <h1><i class="fas fa-users"></i> Gestión de Usuarios</h1>
                    <div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="fas fa-plus"></i> Agregar Usuario
                        </button>
                    </div>
                </div>
                
                <!-- Filtros -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Rol</label>
                                <select name="role" class="form-select">
                                    <option value="">Todos los roles</option>
                                    <option value="admin" <?= ($_GET['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrador</option>
                                    <option value="agente" <?= ($_GET['role'] ?? '') === 'agente' ? 'selected' : '' ?>>Agente</option>
                                    <option value="cliente" <?= ($_GET['role'] ?? '') === 'cliente' ? 'selected' : '' ?>>Cliente</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Estado</label>
                                <select name="status" class="form-select">
                                    <option value="">Todos los estados</option>
                                    <option value="activo" <?= ($_GET['status'] ?? '') === 'activo' ? 'selected' : '' ?>>Activo</option>
                                    <option value="inactivo" <?= ($_GET['status'] ?? '') === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                                    <option value="pendiente" <?= ($_GET['status'] ?? '') === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Buscar</label>
                                <input type="text" name="search" class="form-control" placeholder="Nombre, email..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Lista de Usuarios -->
                <div class="row">
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card user-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <h5 class="card-title mb-1">
                                                    <?= htmlspecialchars($user['nombre'] . ' ' . $user['apellido']) ?>
                                                </h5>
                                                <p class="text-muted mb-0">
                                                    <i class="fas fa-envelope"></i> <?= htmlspecialchars($user['email']) ?>
                                                </p>
                                            </div>
                                            <div class="text-end">
                                                <?php
                                                $roleClass = '';
                                                switch ($user['rol']) {
                                                    case 'admin':
                                                        $roleClass = 'bg-danger';
                                                        break;
                                                    case 'agente':
                                                        $roleClass = 'bg-primary';
                                                        break;
                                                    case 'cliente':
                                                        $roleClass = 'bg-success';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge <?= $roleClass ?> role-badge">
                                                    <?= ucfirst($user['rol']) ?>
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <p class="mb-1">
                                                <i class="fas fa-phone"></i> <?= htmlspecialchars($user['telefono']) ?>
                                            </p>
                                            <p class="mb-1">
                                                <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($user['ciudad']) ?>
                                            </p>
                                            <p class="mb-0">
                                                <i class="fas fa-calendar"></i> Registrado: <?= date('d/m/Y', strtotime($user['fecha_registro'])) ?>
                                            </p>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <?php
                                                $statusClass = $user['estado'] === 'activo' ? 'bg-success' : 
                                                             ($user['estado'] === 'inactivo' ? 'bg-danger' : 'bg-warning');
                                                ?>
                                                <span class="badge <?= $statusClass ?> status-badge">
                                                    <?= ucfirst($user['estado']) ?>
                                                </span>
                                            </div>
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-outline-primary" onclick="editUser(<?= $user['id'] ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(<?= $user['id'] ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle"></i> No se encontraron usuarios con los filtros aplicados.
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Paginación -->
                <?php if (isset($pagination) && $pagination['pages'] > 1): ?>
                    <nav aria-label="Paginación de usuarios">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $pagination['pages']; $i++): ?>
                                <li class="page-item <?= $i == $pagination['page'] ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&role=<?= $_GET['role'] ?? '' ?>&status=<?= $_GET['status'] ?? '' ?>&search=<?= $_GET['search'] ?? '' ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Modal para agregar/editar usuario -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="userForm" method="POST" action="/admin/users/add">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nombre *</label>
                                    <input type="text" name="nombre" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Apellido *</label>
                                    <input type="text" name="apellido" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email *</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Teléfono *</label>
                                    <input type="tel" name="telefono" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Rol *</label>
                                    <select name="rol" class="form-select" required>
                                        <option value="">Seleccionar rol</option>
                                        <option value="admin">Administrador</option>
                                        <option value="agente">Agente</option>
                                        <option value="cliente">Cliente</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Estado *</label>
                                    <select name="estado" class="form-select" required>
                                        <option value="activo">Activo</option>
                                        <option value="inactivo">Inactivo</option>
                                        <option value="pendiente">Pendiente</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Ciudad</label>
                            <input type="text" name="ciudad" class="form-control">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Contraseña *</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Confirmar Contraseña *</label>
                            <input type="password" name="password_confirm" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function editUser(userId) {
            // Implementar edición de usuario
            alert('Función de edición para usuario ID: ' + userId);
        }
        
        function deleteUser(userId) {
            if (confirm('¿Está seguro de que desea eliminar este usuario?')) {
                // Implementar eliminación de usuario
                alert('Función de eliminación para usuario ID: ' + userId);
            }
        }
        
        // Validación del formulario
        document.getElementById('userForm').addEventListener('submit', function(e) {
            const password = document.querySelector('input[name="password"]').value;
            const passwordConfirm = document.querySelector('input[name="password_confirm"]').value;
            
            if (password !== passwordConfirm) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
            }
        });
    </script>
</body>
</html> 