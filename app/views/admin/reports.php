<?php
/**
 * Reportes - Panel Administrativo
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Vista para generar y visualizar reportes detallados del sistema
 */

// Verificar que el usuario sea administrador
if (!hasRole(ROLE_ADMIN)) {
    redirect('/dashboard');
}

$reportType = $_GET['type'] ?? 'general';
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
        
        .report-card {
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: bold;
        }
        
        .export-btn {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border: none;
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
                    <a class="nav-link" href="/admin/dashboard">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a class="nav-link" href="/admin/users">
                        <i class="fas fa-users"></i> Usuarios
                    </a>
                    <a class="nav-link active" href="/admin/reports">
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
                    <h1><i class="fas fa-chart-bar"></i> Reportes del Sistema</h1>
                    <div>
                        <button class="btn export-btn" onclick="exportReport()">
                            <i class="fas fa-download"></i> Exportar Reporte
                        </button>
                    </div>
                </div>
                
                <!-- Navegación de Reportes -->
                <div class="card mb-4">
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="reportTabs">
                            <li class="nav-item">
                                <a class="nav-link <?= $reportType === 'general' ? 'active' : '' ?>" href="?type=general">
                                    <i class="fas fa-chart-pie"></i> General
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $reportType === 'ventas' ? 'active' : '' ?>" href="?type=ventas">
                                    <i class="fas fa-dollar-sign"></i> Ventas
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $reportType === 'usuarios' ? 'active' : '' ?>" href="?type=usuarios">
                                    <i class="fas fa-users"></i> Usuarios
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $reportType === 'propiedades' ? 'active' : '' ?>" href="?type=propiedades">
                                    <i class="fas fa-home"></i> Propiedades
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $reportType === 'citas' ? 'active' : '' ?>" href="?type=citas">
                                    <i class="fas fa-calendar"></i> Citas
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Contenido del Reporte -->
                <?php if ($reportType === 'general'): ?>
                    <!-- Reporte General -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card report-card bg-primary text-white">
                                <div class="card-body text-center">
                                    <div class="stats-number"><?= number_format($data['stats']['total_propiedades']) ?></div>
                                    <div>Total Propiedades</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card report-card bg-success text-white">
                                <div class="card-body text-center">
                                    <div class="stats-number"><?= number_format($data['stats']['total_ventas']) ?></div>
                                    <div>Total Ventas (USD)</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card report-card bg-info text-white">
                                <div class="card-body text-center">
                                    <div class="stats-number"><?= number_format($data['stats']['total_agentes']) ?></div>
                                    <div>Agentes Activos</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card report-card bg-warning text-white">
                                <div class="card-body text-center">
                                    <div class="stats-number"><?= number_format($data['stats']['total_clientes']) ?></div>
                                    <div>Clientes Registrados</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card report-card">
                                <div class="card-header">
                                    <h5><i class="fas fa-chart-line"></i> Actividad del Sistema</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="activityChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card report-card">
                                <div class="card-header">
                                    <h5><i class="fas fa-chart-pie"></i> Distribución por Tipo</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="distributionChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                <?php elseif ($reportType === 'ventas'): ?>
                    <!-- Reporte de Ventas -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card report-card">
                                <div class="card-header">
                                    <h5><i class="fas fa-chart-bar"></i> Ventas por Mes</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="salesChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card report-card">
                                <div class="card-header">
                                    <h5><i class="fas fa-chart-pie"></i> Ventas por Agente</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="agentSalesChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card report-card">
                                <div class="card-header">
                                    <h5><i class="fas fa-table"></i> Detalle de Ventas</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Agente</th>
                                                    <th>Propiedades Vendidas</th>
                                                    <th>Total Ventas</th>
                                                    <th>Comisiones</th>
                                                    <th>Promedio por Venta</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($data['comisiones_por_agente'])): ?>
                                                    <?php foreach ($data['comisiones_por_agente'] as $agente): ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($agente['agente_nombre']) ?></td>
                                                            <td><?= number_format($agente['total_ventas']) ?></td>
                                                            <td>$<?= number_format($agente['total_ventas']) ?></td>
                                                            <td>$<?= number_format($agente['total_comisiones']) ?></td>
                                                            <td>$<?= number_format($agente['total_ventas'] > 0 ? $agente['total_ventas'] / $agente['total_ventas'] : 0) ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center">No hay datos de ventas disponibles</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                <?php elseif ($reportType === 'usuarios'): ?>
                    <!-- Reporte de Usuarios -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card report-card">
                                <div class="card-header">
                                    <h5><i class="fas fa-chart-line"></i> Registro de Usuarios por Mes</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="usersChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card report-card">
                                <div class="card-header">
                                    <h5><i class="fas fa-chart-pie"></i> Distribución por Rol</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="rolesChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                <?php elseif ($reportType === 'propiedades'): ?>
                    <!-- Reporte de Propiedades -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card report-card">
                                <div class="card-header">
                                    <h5><i class="fas fa-chart-bar"></i> Propiedades por Ciudad</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="citiesChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card report-card">
                                <div class="card-header">
                                    <h5><i class="fas fa-chart-pie"></i> Propiedades por Tipo</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="propertyTypesChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                <?php elseif ($reportType === 'citas'): ?>
                    <!-- Reporte de Citas -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card report-card">
                                <div class="card-header">
                                    <h5><i class="fas fa-chart-line"></i> Citas por Mes</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="appointmentsChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card report-card">
                                <div class="card-header">
                                    <h5><i class="fas fa-chart-pie"></i> Estado de Citas</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="appointmentStatusChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Función para exportar reporte
        function exportReport() {
            const reportType = '<?= $reportType ?>';
            const url = `/admin/reports/export?type=${reportType}`;
            window.open(url, '_blank');
        }
        
        // Gráficos (ejemplo para el reporte general)
        <?php if ($reportType === 'general'): ?>
        // Gráfico de actividad
        const activityCtx = document.getElementById('activityChart').getContext('2d');
        new Chart(activityCtx, {
            type: 'line',
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
                datasets: [{
                    label: 'Propiedades',
                    data: [12, 19, 3, 5, 2, 3],
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }, {
                    label: 'Usuarios',
                    data: [5, 8, 12, 15, 18, 22],
                    borderColor: 'rgb(255, 99, 132)',
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
        
        // Gráfico de distribución
        const distributionCtx = document.getElementById('distributionChart').getContext('2d');
        new Chart(distributionCtx, {
            type: 'doughnut',
            data: {
                labels: ['Casas', 'Apartamentos', 'Terrenos', 'Oficinas'],
                datasets: [{
                    data: [30, 25, 20, 25],
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0'
                    ]
                }]
            },
            options: {
                responsive: true
            }
        });
        <?php endif; ?>
    </script>
</body>
</html> 