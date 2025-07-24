<?php
/**
 * Contenido del Dashboard - Administrador
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 *
 * Este archivo contiene solo el contenido del dashboard, sin estructura HTML completa
 */

// El rol ya fue verificado en el AdminController
?>



<!-- Estadísticas Principales -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stats-card primary-gradient">
            <div class="stats-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stats-content">
                <h3><?= number_format(is_numeric($stats['total_usuarios']) ? $stats['total_usuarios'] : 0) ?></h3>
                <p>Total Usuarios</p>
                <div class="stats-trend">
                    <i class="fas fa-arrow-up text-success"></i>
                    <span>+<?= is_numeric($stats['usuarios_nuevos_hoy']) ? $stats['usuarios_nuevos_hoy'] : 0 ?> hoy</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stats-card success-gradient">
            <div class="stats-icon">
                <i class="fas fa-home"></i>
            </div>
            <div class="stats-content">
                <h3><?= number_format(is_numeric($stats['total_propiedades']) ? $stats['total_propiedades'] : 0) ?></h3>
                <p>Total Propiedades</p>
                <div class="stats-trend">
                    <i class="fas fa-arrow-up text-success"></i>
                    <span>+<?= is_numeric($stats['propiedades_nuevas_hoy']) ? $stats['propiedades_nuevas_hoy'] : 0 ?> hoy</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stats-card warning-gradient">
            <div class="stats-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stats-content">
                <h3>$<?= number_format(is_numeric($stats['total_ventas']) ? $stats['total_ventas'] : 0) ?></h3>
                <p>Total Ventas</p>
                <div class="stats-trend">
                    <i class="fas fa-arrow-up text-success"></i>
                    <span>$<?= number_format(is_numeric($stats['ventas_mes_actual']) ? $stats['ventas_mes_actual'] : 0) ?> este mes</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stats-card danger-gradient">
            <div class="stats-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stats-content">
                <h3><?= number_format(is_numeric($stats['total_solicitudes']) ? $stats['total_solicitudes'] : 0) ?></h3>
                <p>Solicitudes</p>
                <div class="stats-trend">
                    <i class="fas fa-clock text-warning"></i>
                    <span><?= is_numeric($stats['solicitudes_nuevas']) ? $stats['solicitudes_nuevas'] : 0 ?> nuevas</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos y Métricas -->
<div class="row mb-4">
    <div class="col-lg-8 mb-3">
        <div class="content-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">
                    <i class="fas fa-chart-line text-primary"></i> 
                    Actividad del Sistema
                </h5>
                <div class="chart-controls">
                    <button class="btn btn-sm btn-primary" onclick="updateChart('month')">Mes</button>
                    <button class="btn btn-sm btn-outline-primary" onclick="updateChart('quarter')">Trimestre</button>
                    <button class="btn btn-sm btn-outline-primary" onclick="updateChart('year')">Año</button>
                </div>
            </div>
            <div class="chart-container" style="position: relative; height: 300px;">
                <canvas id="activityChart"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 mb-3">
        <div class="content-card">
            <h5 class="mb-3">
                <i class="fas fa-chart-pie text-success"></i> 
                Distribución
            </h5>
            <div class="chart-container" style="position: relative; height: 300px;">
                <canvas id="distributionChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Métricas Detalladas -->
<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="content-card">
            <h5 class="mb-3">
                <i class="fas fa-users text-info"></i> 
                Usuarios por Rol
            </h5>
            <div class="metrics-grid">
                <div class="metric-item">
                    <div class="metric-value text-primary"><?= number_format(is_numeric($stats['total_agentes']) ? $stats['total_agentes'] : 0) ?></div>
                    <div class="metric-label">Agentes</div>
                </div>
                <div class="metric-item">
                    <div class="metric-value text-success"><?= number_format(is_numeric($stats['total_clientes']) ? $stats['total_clientes'] : 0) ?></div>
                    <div class="metric-label">Clientes</div>
                </div>
                <div class="metric-item">
                    <div class="metric-value text-danger"><?= number_format(is_numeric($stats['usuarios_suspendidos']) ? $stats['usuarios_suspendidos'] : 0) ?></div>
                    <div class="metric-label">Suspendidos</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-3">
        <div class="content-card">
            <h5 class="mb-3">
                <i class="fas fa-home text-warning"></i> 
                Estado de Propiedades
            </h5>
            <div class="metrics-grid">
                <div class="metric-item">
                    <div class="metric-value text-success"><?= number_format(is_numeric($stats['propiedades_activas']) ? $stats['propiedades_activas'] : 0) ?></div>
                    <div class="metric-label">Activas</div>
                </div>
                <div class="metric-item">
                    <div class="metric-value text-warning"><?= number_format(is_numeric($stats['propiedades_en_revision']) ? $stats['propiedades_en_revision'] : 0) ?></div>
                    <div class="metric-label">En Revisión</div>
                </div>
                <div class="metric-item">
                    <div class="metric-value text-info"><?= number_format(is_numeric($stats['propiedades_vendidas']) ? $stats['propiedades_vendidas'] : 0) ?></div>
                    <div class="metric-label">Vendidas</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Actividades Recientes y Alertas -->
<div class="row">
    <div class="col-lg-8 mb-3">
        <div class="content-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">
                    <i class="fas fa-history text-primary"></i> 
                    Actividades Recientes
                </h5>
                <a href="#" class="btn btn-sm btn-outline-primary">Ver Todas</a>
            </div>
            <div class="activities-list">
                <?php if (isset($recentActivities) && is_array($recentActivities) && !empty($recentActivities)): ?>
                    <?php foreach (array_slice($recentActivities, 0, 6) as $activity): ?>
                        <div class="activity-item">
                            <div class="activity-icon" style="background: var(--admin-<?= $activity['color'] ?>);">
                                <i class="<?= $activity['icon'] ?>"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title"><?= htmlspecialchars($activity['action']) ?></div>
                                <div class="activity-description"><?= htmlspecialchars($activity['description']) ?></div>
                                <div class="activity-time">
                                    <i class="fas fa-clock"></i> 
                                    <?= date('d/m H:i', strtotime($activity['time'])) ?>
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
    
    <div class="col-lg-4 mb-3">
        <div class="content-card">
            <h5 class="mb-3">
                <i class="fas fa-bell text-warning"></i> 
                Alertas del Sistema
            </h5>
            <div class="alerts-list">
                <?php if (isset($alerts) && is_array($alerts) && !empty($alerts)): ?>
                    <?php foreach ($alerts as $alert): ?>
                        <div class="alert alert-<?= $alert['type'] ?> alert-dismissible fade show" role="alert">
                            <i class="<?= $alert['icon'] ?>"></i>
                            <strong><?= htmlspecialchars($alert['title']) ?></strong>
                            <br>
                            <small><?= htmlspecialchars($alert['message']) ?></small>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <p class="mb-0">Sistema funcionando correctamente</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Scripts específicos del dashboard -->
<script>

    // Gráfico de actividad del sistema - Diseño Profesional
    const ctx = document.getElementById('activityChart').getContext('2d');
    
    // Preparar datos para el gráfico con datos más realistas
    const usuariosData = <?= json_encode(array_column($chartData['usuarios_por_mes'] ?? [], 'total')) ?>;
    const propiedadesData = <?= json_encode(array_column($chartData['propiedades_por_mes'] ?? [], 'total')) ?>;
    const ventasData = <?= json_encode(array_column($chartData['ventas_por_mes'] ?? [], 'total')) ?>;
    
    // Preparar datos para el gráfico (inicialmente para el mes/semanas)
    const initialLabels = <?= json_encode($chartData['labels'] ?? ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4']) ?>;
    const initialUsuariosData = <?= json_encode($chartData['usuarios_data'] ?? [0, 0, 0, 0]) ?>;
    const initialPropiedadesData = <?= json_encode($chartData['propiedades_data'] ?? [0, 0, 0, 0]) ?>;
    const initialVentasData = <?= json_encode($chartData['ventas_data'] ?? [0, 0, 0, 0]) ?>;
    
    const activityChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: initialLabels,
            datasets: [{
                label: 'Usuarios',
                data: initialUsuariosData,
                borderColor: '#3498db',
                backgroundColor: 'rgba(52, 152, 219, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#3498db',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8,
                pointHoverBackgroundColor: '#3498db',
                pointHoverBorderColor: '#ffffff',
                pointHoverBorderWidth: 3
            }, {
                label: 'Propiedades',
                data: initialPropiedadesData,
                borderColor: '#27ae60',
                backgroundColor: 'rgba(39, 174, 96, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#27ae60',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8,
                pointHoverBackgroundColor: '#27ae60',
                pointHoverBorderColor: '#ffffff',
                pointHoverBorderWidth: 3
            }, {
                label: 'Ventas',
                data: initialVentasData,
                borderColor: '#f39c12',
                backgroundColor: 'rgba(243, 156, 18, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#f39c12',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8,
                pointHoverBackgroundColor: '#f39c12',
                pointHoverBorderColor: '#ffffff',
                pointHoverBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: {
                            size: 12,
                            weight: '600'
                        }
                    }
                },
                title: {
                    display: true,
                    text: 'Tendencias del Sistema',
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    padding: {
                        top: 10,
                        bottom: 20
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: 'rgba(255, 255, 255, 0.2)',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: true,
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        },
                        padding: 8
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        },
                        padding: 8
                    }
                }
            },
            elements: {
                line: {
                    borderWidth: 3
                }
            }
        }
    });

    // Gráfico de distribución - Diseño Profesional
    const ctxPie = document.getElementById('distributionChart').getContext('2d');
    
    // Datos de distribución de usuarios
    const agentes = <?= is_numeric($stats['total_agentes']) ? $stats['total_agentes'] : 0 ?>;
    const clientes = <?= is_numeric($stats['total_clientes']) ? $stats['total_clientes'] : 0 ?>;
    const admins = <?= is_numeric($stats['total_usuarios']) ? ($stats['total_usuarios'] - ($stats['total_agentes'] ?? 0) - ($stats['total_clientes'] ?? 0)) : 0 ?>;
    
    // Si no hay datos, usar datos de ejemplo
    const hasDistributionData = agentes > 0 || clientes > 0 || admins > 0;
    const distributionData = hasDistributionData ? [agentes, clientes, admins] : [1, 0, 1];
    
    const distributionChart = new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: ['Agentes', 'Clientes', 'Administradores'],
            datasets: [{
                data: distributionData,
                backgroundColor: [
                    'rgba(52, 152, 219, 0.9)',
                    'rgba(39, 174, 96, 0.9)',
                    'rgba(231, 76, 60, 0.9)'
                ],
                borderColor: [
                    'rgba(52, 152, 219, 1)',
                    'rgba(39, 174, 96, 1)',
                    'rgba(231, 76, 60, 1)'
                ],
                borderWidth: 2,
                hoverBackgroundColor: [
                    'rgba(52, 152, 219, 1)',
                    'rgba(39, 174, 96, 1)',
                    'rgba(231, 76, 60, 1)'
                ],
                hoverBorderColor: '#ffffff',
                hoverBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: {
                            size: 12,
                            weight: '600'
                        }
                    }
                },
                title: {
                    display: true,
                    text: hasDistributionData ? 'Distribución de Usuarios' : 'Distribución de Usuarios (Ejemplo)',
                    font: {
                        size: 14,
                        weight: 'bold'
                    },
                    padding: {
                        top: 10,
                        bottom: 15
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: 'rgba(255, 255, 255, 0.2)',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: true,
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            },
            elements: {
                arc: {
                    borderWidth: 2
                }
            }
        }
    });

    // Función para actualizar gráfico con diferentes períodos
    function updateChart(period) {
        // Mostrar indicador de carga
        const chartContainer = document.querySelector('.chart-container');
        chartContainer.style.opacity = '0.6';
        
        // Actualizar el estado activo de los botones
        document.querySelectorAll('.chart-controls .btn').forEach(btn => {
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-outline-primary');
        });
        event.target.classList.remove('btn-outline-primary');
        event.target.classList.add('btn-primary');
        
        // Obtener datos del servidor
        fetch(`/admin/chart-data?period=${period}`)
            .then(response => response.json())
            .then(data => {
                // Actualizar el gráfico con los datos del servidor
                activityChart.data.labels = data.labels;
                activityChart.data.datasets[0].data = data.usuarios;
                activityChart.data.datasets[1].data = data.propiedades;
                activityChart.data.datasets[2].data = data.ventas;
                
                // Actualizar el título del gráfico
                const titles = {
                    'month': 'Tendencias del Sistema - Último Mes',
                    'quarter': 'Tendencias del Sistema - Último Trimestre',
                    'year': 'Tendencias del Sistema - Últimos Años'
                };
                activityChart.options.plugins.title.text = titles[period] || 'Tendencias del Sistema';
                
                // Actualizar el gráfico con animación
                activityChart.update('active');
                
                // Restaurar opacidad
                chartContainer.style.opacity = '1';
                
                // console.log removed
            })
            .catch(error => {
                console.error('Error obteniendo datos:', error);
                
                // Fallback a datos locales si hay error
                let labels, userData, propertyData, salesData;
                
                switch(period) {
                    case 'month':
                        labels = ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4'];
                        userData = [0, 0, 0, 0];
                        propertyData = [0, 0, 0, 0];
                        salesData = [0, 0, 0, 0];
                        break;
                    case 'quarter':
                        labels = ['Ene-Mar', 'Abr-Jun', 'Jul-Sep', 'Oct-Dic'];
                        userData = [0, 0, 0, 0];
                        propertyData = [0, 0, 0, 0];
                        salesData = [0, 0, 0, 0];
                        break;
                    case 'year':
                        labels = ['2020', '2021', '2022', '2023', '2024', '2025'];
                        userData = [0, 0, 0, 0, 0, 0];
                        propertyData = [0, 0, 0, 0, 0, 0];
                        salesData = [0, 0, 0, 0, 0, 0];
                        break;
                    default:
                        labels = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                        userData = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                        propertyData = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                        salesData = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                }
                
                activityChart.data.labels = labels;
                activityChart.data.datasets[0].data = userData;
                activityChart.data.datasets[1].data = propertyData;
                activityChart.data.datasets[2].data = salesData;
                activityChart.update('active');
                
                // Restaurar opacidad
                chartContainer.style.opacity = '1';
            });
    }

    // Actualizar datos en tiempo real cada 30 segundos
    setInterval(function() {
        // Aquí se haría una llamada AJAX para actualizar las estadísticas
        // console.log removed
    }, 30000);
</script>

<style>
/* Estilos específicos del dashboard */

.stats-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.primary-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.success-gradient {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

.warning-gradient {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.danger-gradient {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
}

.stats-icon {
    position: absolute;
    top: 1rem;
    right: 1rem;
    font-size: 2rem;
    opacity: 0.3;
}

.stats-content h3 {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.stats-content p {
    margin-bottom: 0.5rem;
    opacity: 0.9;
}

.stats-trend {
    font-size: 0.9rem;
    opacity: 0.8;
}

.metrics-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.metric-item {
    text-align: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 10px;
}

.metric-value {
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.metric-label {
    font-size: 0.9rem;
    color: #6c757d;
}

.activities-list {
    max-height: 400px;
    overflow-y: auto;
}

.activity-item {
    display: flex;
    align-items: flex-start;
    padding: 1rem;
    border-bottom: 1px solid #f1f3f4;
    transition: background-color 0.3s ease;
}

.activity-item:hover {
    background-color: #f8f9fa;
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
    margin-right: 1rem;
    flex-shrink: 0;
}

.activity-content {
    flex-grow: 1;
}

.activity-title {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.activity-description {
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
}

.activity-time {
    color: #adb5bd;
    font-size: 0.8rem;
}

.alerts-list {
    max-height: 300px;
    overflow-y: auto;
}

.chart-controls {
    display: flex;
    gap: 0.5rem;
}

.current-time {
    font-size: 1.1rem;
    font-weight: 500;
}

.content-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    height: 100%;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.content-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

/* Estilos específicos para gráficos */
.chart-container {
    position: relative;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-radius: 12px;
    padding: 1rem;
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: opacity 0.3s ease;
}

.chart-controls .btn {
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    padding: 0.4rem 0.8rem;
    transition: all 0.3s ease;
}

.chart-controls .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Animaciones para los gráficos */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.chart-container {
    animation: fadeInUp 0.6s ease-out;
}

/* Mejoras para tooltips personalizados */
.chart-tooltip {
    background: rgba(0, 0, 0, 0.9);
    color: white;
    border-radius: 8px;
    padding: 8px 12px;
    font-size: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}
</style> 
