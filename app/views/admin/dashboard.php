<?php
/**
 * Panel de Control Total - Administrador
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Dashboard administrativo con control total del sistema
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
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
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
        
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-left: 5px solid;
            transition: transform 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .stats-card.primary { border-left-color: var(--admin-primary); }
        .stats-card.success { border-left-color: var(--admin-success); }
        .stats-card.warning { border-left-color: var(--admin-warning); }
        .stats-card.danger { border-left-color: var(--admin-danger); }
        .stats-card.info { border-left-color: var(--admin-info); }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .stats-label {
            font-size: 1rem;
            color: #666;
            margin-bottom: 5px;
        }
        
        .stats-change {
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .stats-change.positive { color: var(--admin-success); }
        .stats-change.negative { color: var(--admin-danger); }
        
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .activity-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            transition: background 0.3s ease;
        }
        
        .activity-item:hover {
            background: #f8f9fa;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }
        
        .alert-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-left: 5px solid;
        }
        
        .alert-card.warning { border-left-color: var(--admin-warning); }
        .alert-card.danger { border-left-color: var(--admin-danger); }
        .alert-card.info { border-left-color: var(--admin-info); }
        
        .quick-actions {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .action-btn {
            background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            margin: 5px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            color: white;
        }
        
        .action-btn.success { background: linear-gradient(135deg, var(--admin-success) 0%, #2ecc71 100%); }
        .action-btn.warning { background: linear-gradient(135deg, var(--admin-warning) 0%, #f1c40f 100%); }
        .action-btn.danger { background: linear-gradient(135deg, var(--admin-danger) 0%, #c0392b 100%); }
        .action-btn.info { background: linear-gradient(135deg, var(--admin-info) 0%, #2980b9 100%); }
        
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
                        <i class="fas fa-shield-alt"></i> Panel de Control Total
                    </h1>
                    <small>Administración completa del sistema</small>
                </div>
                <div class="col-md-6 text-end">
                    <div class="d-flex justify-content-end align-items-center">
                        <div class="me-3">
                            <i class="fas fa-clock"></i> <?= date('d/m/Y H:i') ?>
                        </div>
                        <div class="me-3">
                            <span class="status-indicator status-online"></span>
                            Sistema Online
                        </div>
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
                        <a class="nav-link active" href="/admin/dashboard">
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
                <!-- Alertas del Sistema -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="mb-3">
                            <i class="fas fa-bell text-warning"></i> 
                            Alertas del Sistema
                        </h5>
                        
                        <?php if (!empty($alerts)): ?>
                            <div class="row">
                                <?php foreach ($alerts as $alert): ?>
                                    <div class="col-md-6">
                                        <div class="alert-card <?= $alert['type'] ?>">
                                            <div class="d-flex align-items-center">
                                                <i class="<?= $alert['icon'] ?> fa-2x me-3"></i>
                                                <div>
                                                    <h6 class="mb-1"><?= $alert['title'] ?></h6>
                                                    <p class="mb-0"><?= $alert['message'] ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                <p class="mb-0">Sistema funcionando correctamente</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Acciones Rápidas -->
                <div class="quick-actions">
                    <h5 class="mb-3">
                        <i class="fas fa-bolt"></i> Acciones Rápidas
                    </h5>
                    <div class="d-flex flex-wrap">
                        <a href="/admin/users?action=list" class="action-btn">
                            <i class="fas fa-users"></i> Gestionar Usuarios
                        </a>
                        <a href="/admin/properties?action=list" class="action-btn success">
                            <i class="fas fa-home"></i> Gestionar Propiedades
                        </a>
                        <a href="/admin/reports?action=list" class="action-btn warning">
                            <i class="fas fa-flag"></i> Revisar Reportes
                        </a>
                        <a href="/admin/logs" class="action-btn info">
                            <i class="fas fa-file-alt"></i> Ver Logs
                        </a>
                        <a href="/admin/backup?action=create" class="action-btn danger">
                            <i class="fas fa-database"></i> Crear Backup
                        </a>
                        <a href="/admin/config" class="action-btn">
                            <i class="fas fa-cog"></i> Configuración
                        </a>
                    </div>
                </div>

                <!-- Estadísticas Principales -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stats-card primary">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-users fa-2x text-primary"></i>
                                </div>
                                <div>
                                    <div class="stats-number"><?= number_format($stats['total_usuarios']) ?></div>
                                    <div class="stats-label">Total Usuarios</div>
                                    <div class="stats-change positive">
                                        <i class="fas fa-arrow-up"></i> +<?= $stats['usuarios_nuevos_hoy'] ?> hoy
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stats-card success">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-home fa-2x text-success"></i>
                                </div>
                                <div>
                                    <div class="stats-number"><?= number_format($stats['total_propiedades']) ?></div>
                                    <div class="stats-label">Total Propiedades</div>
                                    <div class="stats-change positive">
                                        <i class="fas fa-arrow-up"></i> +<?= $stats['propiedades_nuevas_hoy'] ?> hoy
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stats-card warning">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-clock fa-2x text-warning"></i>
                                </div>
                                <div>
                                    <div class="stats-number"><?= number_format($stats['propiedades_en_revision']) ?></div>
                                    <div class="stats-label">Pendientes de Revisión</div>
                                    <div class="stats-change warning">
                                        <i class="fas fa-exclamation-triangle"></i> <?= $stats['propiedades_pendientes_hoy'] ?> nuevas hoy
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stats-card danger">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-flag fa-2x text-danger"></i>
                                </div>
                                <div>
                                    <div class="stats-number"><?= number_format($stats['reportes_pendientes']) ?></div>
                                    <div class="stats-label">Reportes Pendientes</div>
                                    <div class="stats-change negative">
                                        <i class="fas fa-arrow-up"></i> +<?= $stats['reportes_nuevos_hoy'] ?> hoy
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Segunda fila de estadísticas -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stats-card info">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-user-tie fa-2x text-info"></i>
                                </div>
                                <div>
                                    <div class="stats-number"><?= number_format($stats['total_agentes']) ?></div>
                                    <div class="stats-label">Agentes Activos</div>
                                    <div class="stats-change positive">
                                        <i class="fas fa-check"></i> <?= $stats['agentes_activos'] ?> activos
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stats-card primary">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-calendar-check fa-2x text-primary"></i>
                                </div>
                                <div>
                                    <div class="stats-number"><?= number_format($stats['total_citas']) ?></div>
                                    <div class="stats-label">Total Citas</div>
                                    <div class="stats-change positive">
                                        <i class="fas fa-arrow-up"></i> <?= $stats['citas_hoy'] ?> hoy
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stats-card success">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-comments fa-2x text-success"></i>
                                </div>
                                <div>
                                    <div class="stats-number"><?= number_format($stats['conversaciones_activas']) ?></div>
                                    <div class="stats-label">Chats Activos</div>
                                    <div class="stats-change positive">
                                        <i class="fas fa-arrow-up"></i> <?= $stats['mensajes_hoy'] ?> mensajes hoy
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stats-card warning">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-heart fa-2x text-warning"></i>
                                </div>
                                <div>
                                    <div class="stats-number"><?= number_format($stats['total_favoritos']) ?></div>
                                    <div class="stats-label">Favoritos</div>
                                    <div class="stats-change positive">
                                        <i class="fas fa-star"></i> Activos
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Gráficos -->
                    <div class="col-md-8">
                        <div class="chart-container">
                            <h5 class="mb-3">
                                <i class="fas fa-chart-line"></i> Actividad del Sistema
                            </h5>
                            <canvas id="activityChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                    
                    <!-- Actividades Recientes -->
                    <div class="col-md-4">
                        <div class="chart-container">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">
                                    <i class="fas fa-history"></i> Actividades Recientes
                                </h5>
                                <a href="/admin/activities" class="btn btn-sm btn-outline-primary">Ver Todas</a>
                            </div>
                            <div class="activities-list">
                                <?php if (!empty($recentActivities)): ?>
                                    <?php foreach (array_slice($recentActivities, 0, 8) as $activity): ?>
                                        <div class="activity-item">
                                            <div class="d-flex align-items-center">
                                                <div class="activity-icon me-3" style="background: var(--admin-<?= $activity['color'] ?>);">
                                                    <i class="<?= $activity['icon'] ?>"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="fw-bold"><?= $activity['action'] ?></div>
                                                    <div class="text-muted small"><?= $activity['description'] ?></div>
                                                    <div class="text-muted small">
                                                        <i class="fas fa-clock"></i> <?= date('d/m H:i', strtotime($activity['time'])) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <p>No hay actividades recientes</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Gráfico de actividad del sistema
        const ctx = document.getElementById('activityChart').getContext('2d');
        const activityChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                datasets: [{
                    label: 'Usuarios',
                    data: [<?= implode(',', array_column($chartData['usuarios_por_mes'] ?? [], 'total')) ?>],
                    borderColor: '#2c3e50',
                    backgroundColor: 'rgba(44, 62, 80, 0.1)',
                    tension: 0.4
                }, {
                    label: 'Propiedades',
                    data: [<?= implode(',', array_column($chartData['propiedades_por_mes'] ?? [], 'total')) ?>],
                    borderColor: '#27ae60',
                    backgroundColor: 'rgba(39, 174, 96, 0.1)',
                    tension: 0.4
                }, {
                    label: 'Ventas',
                    data: [<?= implode(',', array_column($chartData['ventas_por_mes'] ?? [], 'total')) ?>],
                    borderColor: '#f39c12',
                    backgroundColor: 'rgba(243, 156, 18, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Tendencias del Sistema'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Actualizar datos en tiempo real cada 30 segundos
        setInterval(function() {
            // Aquí se haría una llamada AJAX para actualizar las estadísticas
            // console.log removed
        }, 30000);
    </script>
</body>
</html> 
