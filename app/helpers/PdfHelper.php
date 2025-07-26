<?php
/**
 * Helper para generar reportes PDF usando DOMPDF
 */
class PdfHelper {
    private $dompdf;
    
    public function __construct() {
        // Lazy loading de DOMPDF
        if (!class_exists('\Dompdf\Dompdf')) {
            require_once VENDOR_PATH . '/autoload.php';
        }
        
        $this->dompdf = new \Dompdf\Dompdf();
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $this->dompdf->setOptions($options);
    }
    
    /**
     * Generar PDF del dashboard por período
     */
    public function generateDashboardReport($stats, $chartData, $recentActivities, $period = 'all') {
        $html = $this->getDashboardHtml($stats, $chartData, $recentActivities, $period);
        $periodName = $this->getPeriodName($period);
        return $this->generatePdf($html, 'Reporte_Dashboard_' . $periodName . '_' . date('Y-m-d_H-i-s') . '.pdf');
    }
    
    /**
     * Generar PDF con HTML personalizado
     */
    private function generatePdf($html, $filename) {
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        
        // Generar el PDF
        $this->dompdf->stream($filename, array('Attachment' => true));
        exit;
    }
    
    /**
     * HTML para el reporte del dashboard con gráficos
     */
    private function getDashboardHtml($stats, $chartData, $recentActivities, $period = 'all') {
        // Calcular alturas de las barras para el gráfico de propiedades
        $maxPropiedades = max(
            $stats['propiedades_activas'] ?? 0,
            $stats['propiedades_vendidas'] ?? 0,
            $stats['propiedades_en_revision'] ?? 0,
            $stats['propiedades_rechazadas'] ?? 0
        );
        
        $maxPropiedades = $maxPropiedades > 0 ? $maxPropiedades : 1;
        
        $alturaActivas = ($stats['propiedades_activas'] ?? 0) / $maxPropiedades * 100;
        $alturaVendidas = ($stats['propiedades_vendidas'] ?? 0) / $maxPropiedades * 100;
        $alturaRevision = ($stats['propiedades_en_revision'] ?? 0) / $maxPropiedades * 100;
        $alturaRechazadas = ($stats['propiedades_rechazadas'] ?? 0) / $maxPropiedades * 100;
        
        // Calcular alturas de las barras para el gráfico de usuarios
        $maxUsuarios = max(
            $stats['total_agentes'] ?? 0,
            $stats['total_clientes'] ?? 0,
            $stats['usuarios_suspendidos'] ?? 0
        );
        
        $maxUsuarios = $maxUsuarios > 0 ? $maxUsuarios : 1;
        
        $alturaAgentes = ($stats['total_agentes'] ?? 0) / $maxUsuarios * 100;
        $alturaClientes = ($stats['total_clientes'] ?? 0) / $maxUsuarios * 100;
        $alturaSuspendidos = ($stats['usuarios_suspendidos'] ?? 0) / $maxUsuarios * 100;
        
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Reporte del Dashboard - PropEasy</title>
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    margin: 20px; 
                    font-size: 12px;
                }
                .header { 
                    text-align: center; 
                    margin-bottom: 30px; 
                    border-bottom: 2px solid #2c3e50; 
                    padding-bottom: 20px; 
                }
                .logo { 
                    font-size: 24px; 
                    font-weight: bold; 
                    color: #2c3e50; 
                }
                .subtitle { 
                    color: #7f8c8d; 
                    margin-top: 5px; 
                }
                .stats-grid { 
                    display: table; 
                    width: 100%; 
                    margin-bottom: 30px; 
                    border-collapse: collapse;
                }
                .stat-card { 
                    display: table-cell; 
                    width: 33.33%; 
                    background: #f8f9fa; 
                    padding: 15px; 
                    border: 1px solid #dee2e6;
                    text-align: center;
                    vertical-align: top;
                }
                .stat-number { 
                    font-size: 24px; 
                    font-weight: bold; 
                    color: #2c3e50; 
                    margin-bottom: 5px;
                }
                .stat-label { 
                    color: #7f8c8d; 
                    font-size: 11px;
                }
                .chart-section { 
                    margin-top: 30px; 
                    page-break-inside: avoid;
                }
                .chart-title { 
                    font-size: 16px; 
                    font-weight: bold; 
                    color: #2c3e50; 
                    margin-bottom: 15px; 
                    border-bottom: 1px solid #eee; 
                    padding-bottom: 10px; 
                }
                .chart-container { 
                    background: #f8f9fa; 
                    padding: 20px; 
                    border: 1px solid #dee2e6;
                    margin-bottom: 20px;
                    text-align: center;
                }
                .chart-row {
                    display: table;
                    width: 100%;
                    margin-bottom: 20px;
                }
                .chart-bar-container {
                    display: table-cell;
                    width: 25%;
                    text-align: center;
                    vertical-align: bottom;
                    padding: 0 10px;
                }
                .chart-bar {
                    background: #3498db;
                    margin: 0 auto;
                    border-radius: 3px 3px 0 0;
                    position: relative;
                    min-height: 20px;
                }
                .chart-bar.vendidas { background: #27ae60; }
                .chart-bar.revision { background: #f39c12; }
                .chart-bar.rechazadas { background: #e74c3c; }
                .chart-bar.agentes { background: #3498db; }
                .chart-bar.clientes { background: #9b59b6; }
                .chart-bar.suspendidos { background: #e74c3c; }
                .chart-label { 
                    font-size: 10px; 
                    color: #7f8c8d; 
                    margin-top: 5px;
                    font-weight: bold;
                }
                .chart-value {
                    font-size: 10px;
                    color: #2c3e50;
                    margin-top: 2px;
                }
                .activities { 
                    margin-top: 30px; 
                    page-break-inside: avoid;
                }
                .activity-item { 
                    padding: 10px; 
                    border-bottom: 1px solid #eee; 
                    margin-bottom: 5px;
                    background: #f8f9fa;
                }
                .activity-title { 
                    font-weight: bold; 
                    color: #2c3e50; 
                    font-size: 11px;
                }
                .activity-desc { 
                    color: #7f8c8d; 
                    margin: 3px 0; 
                    font-size: 10px;
                }
                .activity-time { 
                    color: #95a5a6; 
                    font-size: 9px; 
                }
                .footer { 
                    margin-top: 30px; 
                    text-align: center; 
                    color: #7f8c8d; 
                    font-size: 10px; 
                    border-top: 1px solid #eee;
                    padding-top: 10px;
                }
                .legend {
                    margin-top: 15px;
                    font-size: 10px;
                }
                .legend-item {
                    display: inline-block;
                    margin-right: 15px;
                }
                .legend-color {
                    display: inline-block;
                    width: 12px;
                    height: 12px;
                    margin-right: 5px;
                    border-radius: 2px;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <div class="logo">PropEasy</div>
                <div class="subtitle">Sistema de Gestión Inmobiliaria</div>
                <h2>Reporte del Dashboard - ' . $this->getPeriodName($period) . '</h2>
                <p>Generado el: ' . date('d/m/Y H:i:s') . '</p>
                <p><strong>Período:</strong> ' . $this->getPeriodName($period) . '</p>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number">' . number_format($stats['total_usuarios'] ?? 0) . '</div>
                    <div class="stat-label">Total Usuarios</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">' . number_format($stats['total_propiedades'] ?? 0) . '</div>
                    <div class="stat-label">Total Propiedades</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">' . number_format($stats['total_agentes'] ?? 0) . '</div>
                    <div class="stat-label">Agentes Activos</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">' . number_format($stats['total_citas'] ?? 0) . '</div>
                    <div class="stat-label">Total Citas</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">' . number_format($stats['propiedades_en_revision'] ?? 0) . '</div>
                    <div class="stat-label">En Revisión</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">' . number_format($stats['reportes_pendientes'] ?? 0) . '</div>
                    <div class="stat-label">Reportes Pendientes</div>
                </div>
            </div>
            
            <div class="chart-section">
                <h3 class="chart-title">Gráfico de Propiedades por Estado</h3>
                <div class="chart-container">
                    <div class="chart-row">
                        <div class="chart-bar-container">
                            <div class="chart-bar" style="height: ' . max(20, $alturaActivas) . 'px;" title="Activas: ' . ($stats['propiedades_activas'] ?? 0) . '"></div>
                            <div class="chart-label">Activas</div>
                            <div class="chart-value">' . ($stats['propiedades_activas'] ?? 0) . '</div>
                        </div>
                        <div class="chart-bar-container">
                            <div class="chart-bar vendidas" style="height: ' . max(20, $alturaVendidas) . 'px;" title="Vendidas: ' . ($stats['propiedades_vendidas'] ?? 0) . '"></div>
                            <div class="chart-label">Vendidas</div>
                            <div class="chart-value">' . ($stats['propiedades_vendidas'] ?? 0) . '</div>
                        </div>
                        <div class="chart-bar-container">
                            <div class="chart-bar revision" style="height: ' . max(20, $alturaRevision) . 'px;" title="En Revisión: ' . ($stats['propiedades_en_revision'] ?? 0) . '"></div>
                            <div class="chart-label">En Revisión</div>
                            <div class="chart-value">' . ($stats['propiedades_en_revision'] ?? 0) . '</div>
                        </div>
                        <div class="chart-bar-container">
                            <div class="chart-bar rechazadas" style="height: ' . max(20, $alturaRechazadas) . 'px;" title="Rechazadas: ' . ($stats['propiedades_rechazadas'] ?? 0) . '"></div>
                            <div class="chart-label">Rechazadas</div>
                            <div class="chart-value">' . ($stats['propiedades_rechazadas'] ?? 0) . '</div>
                        </div>
                    </div>
                    <div class="legend">
                        <div class="legend-item">
                            <span class="legend-color" style="background: #3498db;"></span>Activas
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background: #27ae60;"></span>Vendidas
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background: #f39c12;"></span>En Revisión
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background: #e74c3c;"></span>Rechazadas
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="chart-section">
                <h3 class="chart-title">Gráfico de Usuarios por Rol</h3>
                <div class="chart-container">
                    <div class="chart-row">
                        <div class="chart-bar-container">
                            <div class="chart-bar agentes" style="height: ' . max(20, $alturaAgentes) . 'px;" title="Agentes: ' . ($stats['total_agentes'] ?? 0) . '"></div>
                            <div class="chart-label">Agentes</div>
                            <div class="chart-value">' . ($stats['total_agentes'] ?? 0) . '</div>
                        </div>
                        <div class="chart-bar-container">
                            <div class="chart-bar clientes" style="height: ' . max(20, $alturaClientes) . 'px;" title="Clientes: ' . ($stats['total_clientes'] ?? 0) . '"></div>
                            <div class="chart-label">Clientes</div>
                            <div class="chart-value">' . ($stats['total_clientes'] ?? 0) . '</div>
                        </div>
                        <div class="chart-bar-container">
                            <div class="chart-bar suspendidos" style="height: ' . max(20, $alturaSuspendidos) . 'px;" title="Suspendidos: ' . ($stats['usuarios_suspendidos'] ?? 0) . '"></div>
                            <div class="chart-label">Suspendidos</div>
                            <div class="chart-value">' . ($stats['usuarios_suspendidos'] ?? 0) . '</div>
                        </div>
                    </div>
                    <div class="legend">
                        <div class="legend-item">
                            <span class="legend-color" style="background: #3498db;"></span>Agentes
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background: #9b59b6;"></span>Clientes
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background: #e74c3c;"></span>Suspendidos
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="activities">
                <h3>Actividades Recientes</h3>';
        
        if (!empty($recentActivities)) {
            foreach (array_slice($recentActivities, 0, 10) as $activity) {
                $html .= '
                <div class="activity-item">
                    <div class="activity-title">' . htmlspecialchars($activity['action']) . '</div>
                    <div class="activity-desc">' . htmlspecialchars($activity['description']) . '</div>
                    <div class="activity-time">' . date('d/m/Y H:i', strtotime($activity['time'])) . '</div>
                </div>';
            }
        } else {
            $html .= '<p>No hay actividades recientes</p>';
        }
        
        $html .= '
            </div>
            
            <div class="footer">
                <p>PropEasy - Sistema de Gestión Inmobiliaria</p>
                <p>Reporte generado automáticamente</p>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    /**
     * Obtener nombre del período
     */
    private function getPeriodName($period) {
        switch ($period) {
            case 'month':
                return 'Mes';
            case 'quarter':
                return 'Trimestre';
            case 'year':
                return 'Año';
            default:
                return 'Completo';
        }
    }
}
?> 