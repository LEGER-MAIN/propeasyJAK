<?php
/**
 * Controlador de Administrador
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este controlador maneja todas las funcionalidades del panel de administrador,
 * incluyendo estadísticas globales, gestión de usuarios y reportes.
 */

class AdminController {
    private $userModel;
    private $propertyModel;
    private $solicitudModel;
    private $appointmentModel;
    private $reporteModel;
    private $chatModel;
    
    public function __construct() {
        require_once APP_PATH . '/models/User.php';
        require_once APP_PATH . '/models/Property.php';
        require_once APP_PATH . '/models/SolicitudCompra.php';
        require_once APP_PATH . '/models/Appointment.php';
        require_once APP_PATH . '/models/ReporteIrregularidad.php';
        require_once APP_PATH . '/models/Chat.php';
        
        $this->userModel = new User();
        $this->propertyModel = new Property();
        $this->solicitudModel = new SolicitudCompra();
        $this->appointmentModel = new Appointment();
        $this->reporteModel = new ReporteIrregularidad();
        $this->chatModel = new Chat();
    }
    
    /**
     * Dashboard principal del administrador
     */
    public function dashboard() {
        // Verificar que el usuario sea administrador
        requireRole(ROLE_ADMIN);
        
        try {
            // Obtener estadísticas globales
            $stats = $this->getGlobalStats();
            
            // Obtener datos para gráficos
            $chartData = $this->getChartData();
            
            // Obtener actividades recientes
            $recentActivities = $this->getRecentActivities();
            
            $pageTitle = 'Dashboard Administrativo - ' . APP_NAME;
            include APP_PATH . '/views/admin/dashboard.php';
            
        } catch (Exception $e) {
            error_log("Error en dashboard admin: " . $e->getMessage());
            setFlashMessage('error', 'Error al cargar el dashboard');
            redirect('/dashboard');
        }
    }
    
    /**
     * Obtener estadísticas globales del sistema
     */
    private function getGlobalStats() {
        $stats = [];
        
        // Estadísticas de propiedades
        $stats['total_propiedades'] = $this->propertyModel->getTotalProperties();
        $stats['propiedades_activas'] = $this->propertyModel->getPropertiesByStatus('activa');
        $stats['propiedades_vendidas'] = $this->propertyModel->getPropertiesByStatus('vendida');
        $stats['propiedades_en_revision'] = $this->propertyModel->getPropertiesByStatus('en_revision');
        $stats['propiedades_rechazadas'] = $this->propertyModel->getPropertiesByStatus('rechazada');
        
        // Estadísticas de usuarios
        $stats['total_agentes'] = $this->userModel->getUsersByRole('agente');
        $stats['total_clientes'] = $this->userModel->getUsersByRole('cliente');
        $stats['usuarios_activos'] = $this->userModel->getActiveUsers();
        $stats['usuarios_nuevos_mes'] = $this->userModel->getNewUsersThisMonth();
        
        // Estadísticas de solicitudes
        $stats['total_solicitudes'] = $this->solicitudModel->getTotalSolicitudes();
        $stats['solicitudes_nuevas'] = $this->solicitudModel->getSolicitudesByStatus('nuevo');
        $stats['solicitudes_en_revision'] = $this->solicitudModel->getSolicitudesByStatus('en_revision');
        $stats['solicitudes_cerradas'] = $this->solicitudModel->getSolicitudesByStatus('cerrado');
        
        // Estadísticas de citas
        $stats['total_citas'] = $this->appointmentModel->getTotalAppointments();
        $stats['citas_propuestas'] = $this->appointmentModel->getAppointmentsByStatus('propuesta');
        $stats['citas_aceptadas'] = $this->appointmentModel->getAppointmentsByStatus('aceptada');
        $stats['citas_completadas'] = $this->appointmentModel->getAppointmentsByStatus('completada');
        
        // Estadísticas de reportes
        $stats['total_reportes'] = $this->reporteModel->getTotalReportes();
        $stats['reportes_pendientes'] = $this->reporteModel->getReportesByStatus('pendiente');
        $stats['reportes_atendidos'] = $this->reporteModel->getReportesByStatus('atendido');
        
        // Estadísticas financieras
        $stats['total_ventas'] = $this->propertyModel->getTotalSales();
        $stats['comisiones_generadas'] = $this->calculateTotalCommissions();
        
        // Propiedades más vistas
        $stats['propiedades_mas_vistas'] = $this->propertyModel->getMostViewedProperties(5);
        
        return $stats;
    }
    
    /**
     * Obtener datos para gráficos
     */
    private function getChartData() {
        $chartData = [];
        
        // Datos de propiedades por mes (últimos 12 meses)
        $chartData['propiedades_por_mes'] = $this->propertyModel->getPropertiesByMonth(12);
        
        // Datos de ventas por mes
        $chartData['ventas_por_mes'] = $this->propertyModel->getSalesByMonth(12);
        
        // Datos de usuarios por mes
        $chartData['usuarios_por_mes'] = $this->userModel->getUsersByMonth(12);
        
        // Datos de propiedades por tipo
        $chartData['propiedades_por_tipo'] = $this->propertyModel->getPropertiesByType();
        
        // Datos de propiedades por ciudad
        $chartData['propiedades_por_ciudad'] = $this->propertyModel->getPropertiesByCity();
        
        return $chartData;
    }
    
    /**
     * Obtener actividades recientes
     */
    private function getRecentActivities() {
        $activities = [];
        
        // Propiedades recientes
        $activities['propiedades_recientes'] = $this->propertyModel->getRecentProperties(10);
        
        // Solicitudes recientes
        $activities['solicitudes_recientes'] = $this->solicitudModel->getRecentSolicitudes(10);
        
        // Citas recientes
        $activities['citas_recientes'] = $this->appointmentModel->getRecentAppointments(10);
        
        // Reportes recientes
        $activities['reportes_recientes'] = $this->reporteModel->getRecentReportes(10);
        
        // Usuarios recientes
        $activities['usuarios_recientes'] = $this->userModel->getRecentUsers(10);
        
        return $activities;
    }
    
    /**
     * Calcular comisiones totales generadas
     */
    private function calculateTotalCommissions() {
        // Obtener propiedades vendidas
        $propiedadesVendidas = $this->propertyModel->getPropertiesByStatus('vendida');
        
        $totalComisiones = 0;
        foreach ($propiedadesVendidas as $propiedad) {
            // Calcular comisión (ejemplo: 3% del precio de venta)
            $comision = $propiedad['precio_venta'] * 0.03;
            $totalComisiones += $comision;
        }
        
        return $totalComisiones;
    }
    
    /**
     * Gestión de usuarios
     */
    public function manageUsers() {
        requireRole(ROLE_ADMIN);
        
        try {
            $users = $this->userModel->getAllUsers();
            $pageTitle = 'Gestión de Usuarios - ' . APP_NAME;
            include APP_PATH . '/views/admin/users.php';
            
        } catch (Exception $e) {
            error_log("Error en gestión de usuarios: " . $e->getMessage());
            setFlashMessage('error', 'Error al cargar la gestión de usuarios');
            redirect('/admin/dashboard');
        }
    }
    
    /**
     * Cambiar estado de usuario
     */
    public function changeUserStatus() {
        requireRole(ROLE_ADMIN);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/users');
        }
        
        $userId = $_POST['user_id'] ?? null;
        $newStatus = $_POST['status'] ?? null;
        
        if (!$userId || !$newStatus) {
            setFlashMessage('error', 'Datos incompletos');
            redirect('/admin/users');
        }
        
        try {
            $result = $this->userModel->changeUserStatus($userId, $newStatus);
            
            if ($result) {
                setFlashMessage('success', 'Estado del usuario actualizado correctamente');
            } else {
                setFlashMessage('error', 'Error al actualizar el estado del usuario');
            }
            
        } catch (Exception $e) {
            error_log("Error al cambiar estado de usuario: " . $e->getMessage());
            setFlashMessage('error', 'Error al actualizar el estado del usuario');
        }
        
        redirect('/admin/users');
    }
    
    /**
     * Reportes detallados
     */
    public function reports() {
        requireRole(ROLE_ADMIN);
        
        try {
            $reportType = $_GET['type'] ?? 'general';
            
            switch ($reportType) {
                case 'ventas':
                    $data = $this->getSalesReport();
                    break;
                case 'usuarios':
                    $data = $this->getUsersReport();
                    break;
                case 'propiedades':
                    $data = $this->getPropertiesReport();
                    break;
                case 'citas':
                    $data = $this->getAppointmentsReport();
                    break;
                default:
                    $data = $this->getGeneralReport();
            }
            
            $pageTitle = 'Reportes - ' . APP_NAME;
            include APP_PATH . '/views/admin/reports.php';
            
        } catch (Exception $e) {
            error_log("Error en reportes: " . $e->getMessage());
            setFlashMessage('error', 'Error al generar reportes');
            redirect('/admin/dashboard');
        }
    }
    
    /**
     * Reporte general
     */
    private function getGeneralReport() {
        return [
            'stats' => $this->getGlobalStats(),
            'chart_data' => $this->getChartData(),
            'activities' => $this->getRecentActivities()
        ];
    }
    
    /**
     * Reporte de ventas
     */
    private function getSalesReport() {
        $data = [];
        
        // Ventas por período
        $data['ventas_mensuales'] = $this->propertyModel->getSalesByMonth(12);
        $data['ventas_por_agente'] = $this->propertyModel->getSalesByAgent();
        $data['ventas_por_tipo_propiedad'] = $this->propertyModel->getSalesByPropertyType();
        $data['comisiones_por_agente'] = $this->calculateCommissionsByAgent();
        
        return $data;
    }
    
    /**
     * Reporte de usuarios
     */
    private function getUsersReport() {
        $data = [];
        
        // Usuarios por período
        $data['usuarios_mensuales'] = $this->userModel->getUsersByMonth(12);
        $data['usuarios_por_rol'] = $this->userModel->getUsersByRole();
        $data['usuarios_activos_inactivos'] = $this->userModel->getUsersByStatus();
        $data['usuarios_por_ciudad'] = $this->userModel->getUsersByCity();
        
        return $data;
    }
    
    /**
     * Reporte de propiedades
     */
    private function getPropertiesReport() {
        $data = [];
        
        // Propiedades por período
        $data['propiedades_mensuales'] = $this->propertyModel->getPropertiesByMonth(12);
        $data['propiedades_por_tipo'] = $this->propertyModel->getPropertiesByType();
        $data['propiedades_por_ciudad'] = $this->propertyModel->getPropertiesByCity();
        $data['propiedades_por_estado'] = $this->propertyModel->getPropertiesByStatus();
        $data['propiedades_mas_vistas'] = $this->propertyModel->getMostViewedProperties(20);
        
        return $data;
    }
    
    /**
     * Reporte de citas
     */
    private function getAppointmentsReport() {
        $data = [];
        
        // Citas por período
        $data['citas_mensuales'] = $this->appointmentModel->getAppointmentsByMonth(12);
        $data['citas_por_estado'] = $this->appointmentModel->getAppointmentsByStatus();
        $data['citas_por_agente'] = $this->appointmentModel->getAppointmentsByAgent();
        $data['citas_por_tipo'] = $this->appointmentModel->getAppointmentsByType();
        
        return $data;
    }
    
    /**
     * Calcular comisiones por agente
     */
    private function calculateCommissionsByAgent() {
        $ventasPorAgente = $this->propertyModel->getSalesByAgent();
        $comisiones = [];
        
        foreach ($ventasPorAgente as $venta) {
            $agenteId = $venta['agente_id'];
            $comision = $venta['total_ventas'] * 0.03; // 3% de comisión
            
            if (!isset($comisiones[$agenteId])) {
                $comisiones[$agenteId] = [
                    'agente_nombre' => $venta['agente_nombre'],
                    'total_comisiones' => 0,
                    'total_ventas' => 0
                ];
            }
            
            $comisiones[$agenteId]['total_comisiones'] += $comision;
            $comisiones[$agenteId]['total_ventas'] += $venta['total_ventas'];
        }
        
        return $comisiones;
    }
    
    /**
     * Configuración del sistema
     */
    public function systemConfig() {
        requireRole(ROLE_ADMIN);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->updateSystemConfig();
        }
        
        try {
            $config = $this->getSystemConfig();
            $pageTitle = 'Configuración del Sistema - ' . APP_NAME;
            include APP_PATH . '/views/admin/config.php';
            
        } catch (Exception $e) {
            error_log("Error en configuración del sistema: " . $e->getMessage());
            setFlashMessage('error', 'Error al cargar la configuración');
            redirect('/admin/dashboard');
        }
    }
    
    /**
     * Obtener configuración del sistema
     */
    private function getSystemConfig() {
        // Aquí se obtendrían las configuraciones desde la base de datos
        // Por ahora retornamos valores por defecto
        return [
            'app_name' => APP_NAME,
            'app_url' => APP_URL,
            'smtp_host' => SMTP_HOST,
            'smtp_port' => SMTP_PORT,
            'max_file_size' => MAX_FILE_SIZE,
            'session_lifetime' => SESSION_LIFETIME,
            'commission_rate' => 0.03, // 3%
            'property_approval_required' => true,
            'email_notifications' => true
        ];
    }
    
    /**
     * Actualizar configuración del sistema
     */
    private function updateSystemConfig() {
        // Aquí se actualizarían las configuraciones en la base de datos
        setFlashMessage('success', 'Configuración actualizada correctamente');
        redirect('/admin/config');
    }
} 