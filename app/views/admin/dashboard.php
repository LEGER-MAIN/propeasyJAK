<?php
/**
 * Dashboard Administrativo
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Vista principal del panel de administrador con estadísticas globales
 * y métricas del sistema.
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
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .stats-card.success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        
        .stats-card.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .stats-card.info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .stats-card.primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stats-label {
            font-size: 1rem;
            opacity: 0.9;
        }
        
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .activity-item {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-time {
            font-size: 0.8rem;
            color: #666;
        }
        
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
                    <a class="nav-link active" href="/admin/dashboard">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a class="nav-link" href="/admin/users">
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
                    <h1><i class="fas fa-tachometer-alt"></i> Dashboard Administrativo</h1>
                    <div class="text-muted">
                        <i class="fas fa-calendar"></i> <?= date('d/m/Y H:i') ?>
                    </div>
                </div>
                
                <!-- Estadísticas Principales -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stats-card primary">
                            <div class="stats-number"><?= number_format($stats['total_propiedades']) ?></div>
                            <div class="stats-label">
                                <i class="fas fa-home"></i> Total Propiedades
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stats-card success">
                            <div class="stats-number"><?= number_format($stats['propiedades_activas']) ?></div>
                            <div class="stats-label">
                                <i class="fas fa-check-circle"></i> Propiedades Activas
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stats-card info">
                            <div class="stats-number"><?= number_format($stats['total_agentes']) ?></div>
                            <div class="stats-label">
                                <i class="fas fa-user-tie"></i> Agentes Registrados
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stats-card warning">
                            <div class="stats-number"><?= number_format($stats['total_clientes']) ?></div>
                            <div class="stats-label">
                                <i class="fas fa-users"></i> Clientes Registrados
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Segunda fila de estadísticas -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stats-card info">
                            <div class="stats-number"><?= number_format($stats['total_ventas']) ?></div>
                            <div class="stats-label">
                                <i class="fas fa-dollar-sign"></i> Total Ventas (USD)
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stats-card success">
                            <div class="stats-number"><?= number_format($stats['comisiones_generadas']) ?></div>
                            <div class="stats-label">
                                <i class="fas fa-percentage"></i> Comisiones Generadas
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stats-card warning">
                            <div class="stats-number"><?= number_format($stats['total_solicitudes']) ?></div>
                            <div class="stats-label">
                                <i class="fas fa-file-alt"></i> Solicitudes Totales
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stats-card primary">
                            <div class="stats-number"><?= number_format($stats['total_citas']) ?></div>
                            <div class="stats-label">
                                <i class="fas fa-calendar-check"></i> Citas Programadas
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Gráficos -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="chart-container">
                            <h5><i class="fas fa-chart-line"></i> Propiedades por Mes</h5>
                            <canvas id="propertiesChart"></canvas>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="chart-container">
                            <h5><i class="fas fa-chart-pie"></i> Propiedades por Tipo</h5>
                            <canvas id="propertyTypesChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="chart-container">
                            <h5><i class="fas fa-chart-bar"></i> Ventas por Mes</h5>
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="chart-container">
                            <h5><i class="fas fa-chart-area"></i> Usuarios por Mes</h5>
                            <canvas id="usersChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Actividades Recientes y Propiedades Más Vistas -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="chart-container">
                            <h5><i class="fas fa-clock"></i> Actividades Recientes</h5>
                            <div class="activity-list">
                                <?php if (!empty($recentActivities['propiedades_recientes'])): ?>
                                    <?php foreach (array_slice($recentActivities['propiedades_recientes'], 0, 5) as $propiedad): ?>
                                        <div class="activity-item">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <strong>Nueva propiedad:</strong> <?= htmlspecialchars($propiedad['titulo']) ?>
                                                </div>
                                                <div class="activity-time">
                                                    <?= date('d/m H:i', strtotime($propiedad['fecha_creacion'])) ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted">No hay actividades recientes</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="chart-container">
                            <h5><i class="fas fa-eye"></i> Propiedades Más Vistas</h5>
                            <div class="activity-list">
                                <?php if (!empty($stats['propiedades_mas_vistas'])): ?>
                                    <?php foreach ($stats['propiedades_mas_vistas'] as $propiedad): ?>
                                        <div class="activity-item">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <strong><?= htmlspecialchars($propiedad['titulo']) ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?= htmlspecialchars($propiedad['ciudad']) ?></small>
                                                </div>
                                                <div class="text-end">
                                                    <div class="fw-bold">$<?= number_format($propiedad['precio']) ?></div>
                                                    <small class="text-muted"><?= $propiedad['vistas'] ?> vistas</small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted">No hay datos de vistas disponibles</p>
                                <?php endif; ?>
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
        // Datos para los gráficos (estos vendrían del backend)
        const chartData = <?= json_encode($chartData) ?>;
        
        // Gráfico de propiedades por mes
        const propertiesCtx = document.getElementById('propertiesChart').getContext('2d');
        new Chart(propertiesCtx, {
            type: 'line',
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                datasets: [{
                    label: 'Propiedades',
                    data: [12, 19, 3, 5, 2, 3, 7, 8, 9, 10, 11, 12],
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Gráfico de tipos de propiedades
        const typesCtx = document.getElementById('propertyTypesChart').getContext('2d');
        new Chart(typesCtx, {
            type: 'doughnut',
            data: {
                labels: ['Casa', 'Apartamento', 'Terreno', 'Oficina', 'Local'],
                datasets: [{
                    data: [30, 25, 20, 15, 10],
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF'
                    ]
                }]
            },
            options: {
                responsive: true
            }
        });
        
        // Gráfico de ventas por mes
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'bar',
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                datasets: [{
                    label: 'Ventas (USD)',
                    data: [65000, 59000, 80000, 81000, 56000, 55000, 40000, 45000, 50000, 60000, 70000, 75000],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgb(54, 162, 235)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Gráfico de usuarios por mes
        const usersCtx = document.getElementById('usersChart').getContext('2d');
        new Chart(usersCtx, {
            type: 'line',
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                datasets: [{
                    label: 'Usuarios',
                    data: [5, 8, 12, 15, 18, 22, 25, 28, 30, 35, 40, 45],
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html> 