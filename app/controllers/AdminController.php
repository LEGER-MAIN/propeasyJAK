<?php
/**
 * Controlador de Administrador - Panel de Control Total
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este controlador maneja todas las funcionalidades del panel de administrador,
 * incluyendo control total del sistema, gestión de usuarios, propiedades, reportes, etc.
 */

class AdminController {
    private $userModel;
    private $propertyModel;
    private $solicitudModel;
    private $appointmentModel;
    private $reporteModel;
    private $chatModel;
    private $favoriteModel;
    private $alertManager;
    
    public function __construct() {
        require_once APP_PATH . '/models/User.php';
        require_once APP_PATH . '/models/Property.php';
        require_once APP_PATH . '/models/SolicitudCompra.php';
        require_once APP_PATH . '/models/Appointment.php';
        require_once APP_PATH . '/models/ReporteIrregularidad.php';
        require_once APP_PATH . '/models/Chat.php';
        require_once APP_PATH . '/models/Favorite.php';
        
        $this->userModel = new User();
        $this->propertyModel = new Property();
        $this->solicitudModel = new SolicitudCompra();
        $this->appointmentModel = new Appointment();
        $this->reporteModel = new ReporteIrregularidad();
        $this->chatModel = new Chat();
        $this->favoriteModel = new Favorite();
        
        // Cargar AlertManager de forma opcional
        try {
            require_once APP_PATH . '/models/AlertManager.php';
            $this->alertManager = new AlertManager();
        } catch (Exception $e) {
            $this->alertManager = null;
            error_log("AlertManager no disponible: " . $e->getMessage());
        }
    }
    
    /**
     * Dashboard principal del administrador - Panel de Control Total
     */
    public function dashboard() {
        requireRole(ROLE_ADMIN);
        
        try {
            // Obtener estadísticas globales en tiempo real
            $stats = $this->getRealTimeStats();
            
            // Obtener actividades recientes del sistema
            $recentActivities = $this->getSystemActivities();
            
            // Obtener alertas y notificaciones críticas
            $alerts = $this->getSystemAlerts();
            
            // Obtener datos para gráficos en tiempo real
            $chartData = $this->getRealTimeChartData();
            
            // Asegurar que las variables sean arrays
            if (!is_array($recentActivities)) $recentActivities = [];
            if (!is_array($alerts)) $alerts = [];
            if (!is_array($chartData)) $chartData = [];
            
            // Preparar datos para el layout
            $pageTitle = 'Panel de Control Total - ' . APP_NAME;
            $currentPage = 'dashboard';
            
            // Capturar el contenido de la vista
            ob_start();
            include APP_PATH . '/views/admin/dashboard_content.php';
            $content = ob_get_clean();
            
            // Incluir el layout administrativo
            include APP_PATH . '/views/layouts/admin.php';
            
        } catch (Exception $e) {
            error_log("Error en dashboard admin: " . $e->getMessage());
            setFlashMessage('error', 'Error al cargar el panel de control');
            // No redirigir aquí para evitar bucle infinito
            echo "Error al cargar el panel de control: " . $e->getMessage();
            exit;
        }
    }
    
    /**
     * Obtener estadísticas en tiempo real del sistema
     */
    private function getRealTimeStats() {
        $stats = [];
        
        try {
            // Estadísticas de usuarios
            $stats['total_usuarios'] = $this->userModel->getTotalCount();
            $stats['usuarios_activos'] = $this->userModel->getActiveUsers();
            $stats['usuarios_suspendidos'] = $this->userModel->getUsersByStatusCount('suspendido');
            $stats['usuarios_nuevos_hoy'] = $this->userModel->getNewUsersToday();
            $stats['usuarios_nuevos_semana'] = $this->userModel->getNewUsersThisWeek();
            
            // Estadísticas por rol
            $stats['total_agentes'] = $this->userModel->getCountByRole('agente');
            $stats['agentes_activos'] = $this->userModel->getActiveUsersByRole('agente');
            $stats['total_clientes'] = $this->userModel->getCountByRole('cliente');
            $stats['clientes_activos'] = $this->userModel->getActiveUsersByRole('cliente');
            
            // Estadísticas de propiedades
            $stats['total_propiedades'] = $this->propertyModel->getTotalCount();
            $stats['propiedades_activas'] = $this->propertyModel->getPropertiesByStatus('activa');
            $stats['propiedades_vendidas'] = $this->propertyModel->getPropertiesByStatus('vendida');
            $stats['propiedades_en_revision'] = $this->propertyModel->getPropertiesByStatus('en_revision');
            $stats['propiedades_rechazadas'] = $this->propertyModel->getPropertiesByStatus('rechazada');
            $stats['propiedades_nuevas_hoy'] = $this->propertyModel->getNewPropertiesToday();
            $stats['propiedades_pendientes_hoy'] = $this->propertyModel->getPendingPropertiesToday();
            
            // Estadísticas financieras
            $stats['comisiones_generadas'] = $this->calculateTotalCommissions();
            
            // Estadísticas de solicitudes
            $stats['total_solicitudes'] = $this->solicitudModel->getTotalCount();
            $stats['solicitudes_nuevas'] = $this->solicitudModel->getSolicitudesByStatus('nuevo');
            $stats['solicitudes_en_revision'] = $this->solicitudModel->getSolicitudesByStatus('en_revision');
            $stats['solicitudes_cerradas'] = $this->solicitudModel->getSolicitudesByStatus('cerrado');
            
        } catch (Exception $e) {
            error_log("Error obteniendo estadísticas: " . $e->getMessage());
            // Usar valores por defecto si hay error
            $stats['total_usuarios'] = 0;
            $stats['usuarios_activos'] = 0;
            $stats['usuarios_suspendidos'] = 0;
            $stats['usuarios_nuevos_hoy'] = 0;
            $stats['usuarios_nuevos_semana'] = 0;
            $stats['total_agentes'] = 0;
            $stats['agentes_activos'] = 0;
            $stats['total_clientes'] = 0;
            $stats['clientes_activos'] = 0;
            $stats['total_propiedades'] = 0;
            $stats['propiedades_activas'] = 0;
            $stats['propiedades_vendidas'] = 0;
            $stats['propiedades_en_revision'] = 0;
            $stats['propiedades_rechazadas'] = 0;
            $stats['propiedades_nuevas_hoy'] = 0;
            $stats['propiedades_pendientes_hoy'] = 0;

            $stats['comisiones_generadas'] = 0;
            $stats['total_solicitudes'] = 0;
            $stats['solicitudes_nuevas'] = 0;
            $stats['solicitudes_en_revision'] = 0;
            $stats['solicitudes_cerradas'] = 0;
        }
        
        // Estadísticas de citas (valores temporales)
        $stats['total_citas'] = 0;
        $stats['citas_propuestas'] = 0;
        $stats['citas_aceptadas'] = 0;
        $stats['citas_completadas'] = 0;
        $stats['citas_hoy'] = 0;
        
        // Estadísticas de reportes (valores temporales)
        $stats['total_reportes'] = 0;
        $stats['reportes_pendientes'] = 0;
        $stats['reportes_atendidos'] = 0;
        $stats['reportes_nuevos_hoy'] = 0;
        
        // Estadísticas de chat (valores temporales)
        $stats['conversaciones_activas'] = 0;
        $stats['mensajes_hoy'] = 0;
        
        // Estadísticas de favoritos (valores temporales)
        $stats['total_favoritos'] = 0;
        
        return $stats;
    }
    

    
    /**
     * Mostrar todas las actividades del sistema
     */
    public function allActivities() {
        requireRole(ROLE_ADMIN);
        
        try {
            // Cargar el modelo de actividades
            require_once APP_PATH . '/models/ActivityLog.php';
            $activityModel = new ActivityLog();
            
            // Obtener todas las actividades con paginación
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = 50; // Actividades por página
            $offset = ($page - 1) * $limit;
            
            // Obtener actividades paginadas
            $activities = $activityModel->getAllActivities($limit, $offset);
            $totalActivities = $activityModel->getTotalCount();
            $totalPages = ceil($totalActivities / $limit);
            

            
            // Obtener estadísticas de actividades
            $stats = [
                'total_activities' => $totalActivities,
                'activities_today' => $activityModel->getActivitiesToday(),
                'activities_this_week' => $activityModel->getActivitiesThisWeek(),
                'activities_this_month' => $activityModel->getActivitiesThisMonth()
            ];
            
            // Configurar variables para el layout del admin
            $pageTitle = 'Todas las Actividades - Panel Administrativo';
            $currentPage = 'activities';
            
            // Capturar el contenido para pasarlo al layout
            ob_start();
            include APP_PATH . '/views/admin/all_activities.php';
            $content = ob_get_clean();
            
            // Incluir el layout del admin
            include APP_PATH . '/views/layouts/admin.php';
            
        } catch (Exception $e) {
            error_log("Error obteniendo todas las actividades: " . $e->getMessage());
            header('Location: /admin/dashboard');
            exit;
        }
    }
    
    /**
     * Obtener actividades recientes del sistema
     */
    private function getSystemActivities() {
        try {
            // Cargar el modelo de actividades
            require_once APP_PATH . '/models/ActivityLog.php';
            $activityModel = new ActivityLog();
            
            // Obtener actividades reales del sistema
            $activities = $activityModel->getRecentActivities(15);
            
            // Si no hay actividades en los logs, generar algunas basadas en datos recientes
            if (empty($activities)) {
                $activities = $this->generateActivitiesFromData();
            }
            
            return $activities;
            
        } catch (Exception $e) {
            error_log("Error obteniendo actividades del sistema: " . $e->getMessage());
            return $this->generateActivitiesFromData();
        }
    }
    
    /**
     * Generar actividades basadas en datos recientes cuando no hay logs
     */
    private function generateActivitiesFromData() {
        $activities = [];
        
        try {
            // Actividades de usuarios recientes
            $recentUsers = $this->userModel->getRecentUsers(5);
            foreach ($recentUsers as $user) {
                $activities[] = [
                    'type' => 'user',
                    'action' => 'Nuevo usuario registrado',
                    'description' => $user['nombre'] . ' ' . $user['apellido'] . ' (' . $user['rol'] . ')',
                    'time' => $user['fecha_registro'],
                    'icon' => 'fas fa-user-plus',
                    'color' => 'success',
                    'user' => [
                        'name' => $user['nombre'] . ' ' . $user['apellido'],
                        'role' => $user['rol'],
                        'email' => $user['email']
                    ]
                ];
            }
            
            // Actividades de propiedades recientes
            $recentProperties = $this->propertyModel->getRecentProperties(5);
            foreach ($recentProperties as $property) {
                $activities[] = [
                    'type' => 'property',
                    'action' => 'Nueva propiedad agregada',
                    'description' => $property['titulo'] . ' - $' . number_format($property['precio']),
                    'time' => $property['fecha_creacion'],
                    'icon' => 'fas fa-home',
                    'color' => 'primary',
                    'user' => [
                        'name' => ($property['agente_nombre'] ?? '') . ' ' . ($property['agente_apellido'] ?? ''),
                        'role' => 'agente',
                        'email' => ''
                    ]
                ];
            }
            
            // Actividades de solicitudes (si existen)
            if (class_exists('SolicitudCompra')) {
                $solicitudModel = new SolicitudCompra();
                $recentSolicitudes = $solicitudModel->getRecentSolicitudes(3);
                foreach ($recentSolicitudes as $solicitud) {
                    $activities[] = [
                        'type' => 'request',
                        'action' => 'Nueva solicitud de compra',
                        'description' => 'Solicitud para ' . ($solicitud['titulo_propiedad'] ?? 'propiedad'),
                        'time' => $solicitud['fecha_solicitud'],
                        'icon' => 'fas fa-file-alt',
                        'color' => 'warning',
                        'user' => [
                            'name' => ($solicitud['cliente_nombre'] ?? '') . ' ' . ($solicitud['cliente_apellido'] ?? ''),
                            'role' => 'cliente',
                            'email' => ''
                        ]
                    ];
                }
            }
            
        } catch (Exception $e) {
            error_log("Error generando actividades desde datos: " . $e->getMessage());
        }
        
        // Ordenar por fecha más reciente
        usort($activities, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });
        
        return array_slice($activities, 0, 10);
    }
    
    /**
     * Obtener alertas del sistema
     */
    private function getSystemAlerts() {
        $alerts = [];
        
        try {
            // Alerta de reportes pendientes (más importante - danger)
            $reportesPendientes = $this->solicitudModel->getSolicitudesByStatus('nuevo');
            if ($reportesPendientes > 0) {
                $alertTitle = 'Reportes Nuevos';
                $showAlert = true;
                
                // Verificar si la alerta ha sido eliminada (solo si AlertManager está disponible)
                if ($this->alertManager !== null) {
                    try {
                        $showAlert = !$this->alertManager->alertaEliminada('reportes_nuevos', $alertTitle);
                    } catch (Exception $e) {
                        error_log("Error verificando alerta eliminada: " . $e->getMessage());
                        $showAlert = true; // Mostrar alerta por defecto si hay error
                    }
                }
                
                if ($showAlert) {
                    $alerts[] = [
                        'type' => 'danger',
                        'title' => $alertTitle,
                        'message' => "Hay {$reportesPendientes} reportes nuevos sin revisar",
                        'icon' => 'fas fa-exclamation-triangle',
                        'priority' => 1,
                        'alert_key' => 'reportes_nuevos'
                    ];
                }
            }
            
            // Alerta de propiedades pendientes de revisión (warning)
            $propiedadesPendientes = $this->propertyModel->getPropertiesByStatus('en_revision');
            if ($propiedadesPendientes > 0) {
                $alertTitle = 'Propiedades Pendientes';
                $showAlert = true;
                
                if ($this->alertManager !== null) {
                    try {
                        $showAlert = !$this->alertManager->alertaEliminada('propiedades_pendientes', $alertTitle);
                    } catch (Exception $e) {
                        error_log("Error verificando alerta eliminada: " . $e->getMessage());
                        $showAlert = true;
                    }
                }
                
                if ($showAlert) {
                    $alerts[] = [
                        'type' => 'warning',
                        'title' => $alertTitle,
                        'message' => "Tienes {$propiedadesPendientes} propiedades esperando revisión",
                        'icon' => 'fas fa-clock',
                        'priority' => 2,
                        'alert_key' => 'propiedades_pendientes'
                    ];
                }
            }
            
            // Alerta de usuarios suspendidos (info)
            $usuariosSuspendidos = $this->userModel->getUsersByStatusCount('suspendido');
            if ($usuariosSuspendidos > 0) {
                $alertTitle = 'Usuarios Suspendidos';
                $showAlert = true;
                
                if ($this->alertManager !== null) {
                    try {
                        $showAlert = !$this->alertManager->alertaEliminada('usuarios_suspendidos', $alertTitle);
                    } catch (Exception $e) {
                        error_log("Error verificando alerta eliminada: " . $e->getMessage());
                        $showAlert = true;
                    }
                }
                
                if ($showAlert) {
                    $alerts[] = [
                        'type' => 'info',
                        'title' => $alertTitle,
                        'message' => "Hay {$usuariosSuspendidos} usuarios suspendidos en el sistema",
                        'icon' => 'fas fa-user-slash',
                        'priority' => 3,
                        'alert_key' => 'usuarios_suspendidos'
                    ];
                }
            }
            
            // Alerta de propiedades rechazadas (secondary - menos importante)
            $propiedadesRechazadas = $this->propertyModel->getPropertiesByStatus('rechazada');
            if ($propiedadesRechazadas > 0) {
                $alertTitle = 'Propiedades Rechazadas';
                $showAlert = true;
                
                if ($this->alertManager !== null) {
                    try {
                        $showAlert = !$this->alertManager->alertaEliminada('propiedades_rechazadas', $alertTitle);
                    } catch (Exception $e) {
                        error_log("Error verificando alerta eliminada: " . $e->getMessage());
                        $showAlert = true;
                    }
                }
                
                if ($showAlert) {
                    $alerts[] = [
                        'type' => 'secondary',
                        'title' => $alertTitle,
                        'message' => "Hay {$propiedadesRechazadas} propiedades rechazadas",
                        'icon' => 'fas fa-times-circle',
                        'priority' => 4,
                        'alert_key' => 'propiedades_rechazadas'
                    ];
                }
            }
            
        } catch (Exception $e) {
            error_log("Error obteniendo alertas: " . $e->getMessage());
        }
        
        // Ordenar alertas por prioridad (más importante primero)
        usort($alerts, function($a, $b) {
            return $a['priority'] - $b['priority'];
        });
        
        return $alerts;
    }
    
        /**
     * Eliminar una alerta del sistema
     */
    public function dismissAlert() {
        requireRole(ROLE_ADMIN);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $alertKey = $input['alert_key'] ?? '';
        $alertTitle = $input['alert_title'] ?? '';
        
        if (empty($alertKey) || empty($alertTitle)) {
            echo json_encode(['success' => false, 'message' => 'Datos de alerta requeridos']);
            return;
        }
        
        // Verificar si AlertManager está disponible
        if ($this->alertManager === null) {
            echo json_encode(['success' => false, 'message' => 'Sistema de alertas no disponible']);
            return;
        }
        
        try {
            $adminId = $_SESSION['user_id'] ?? 0;
            $success = $this->alertManager->marcarAlertaEliminada($adminId, $alertKey, $alertTitle);
            
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Alerta eliminada correctamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al eliminar la alerta']);
            }
        } catch (Exception $e) {
            error_log("Error eliminando alerta: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
        }
    }

    /**
     * Obtener datos para gráficos en tiempo real
     */
    private function getRealTimeChartData() {
        $chartData = [];

        try {
            // Default to 'month' view (last 4 weeks) for initial load
            $usersData = $this->userModel->getUsersByPeriod('week', 4);
            $propertiesData = $this->propertyModel->getPropertiesByPeriod('week', 4);
            $salesData = $this->propertyModel->getSalesByPeriod('week', 4);

            $chartData['labels'] = $usersData['labels'];
            $chartData['usuarios_data'] = $usersData['data'];
            $chartData['propiedades_data'] = $propertiesData['data'];
            $chartData['ventas_data'] = $salesData['data'];

            // Other chart data (distribution, etc.)
            $chartData['propiedades_por_tipo'] = $this->propertyModel->getPropertiesByType();
            $chartData['propiedades_por_ciudad'] = $this->propertyModel->getPropertiesByCity();
            $chartData['usuarios_por_estado'] = $this->userModel->getUsersByStatus();

        } catch (Exception $e) {
            error_log("Error obteniendo datos de gráficos: " . $e->getMessage());
            // Usar datos temporales si hay error
            $chartData['labels'] = ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4'];
            $chartData['usuarios_data'] = [0, 0, 0, 0];
            $chartData['propiedades_data'] = [0, 0, 0, 0];
            $chartData['ventas_data'] = [0, 0, 0, 0];
            $chartData['propiedades_por_tipo'] = [];
            $chartData['propiedades_por_ciudad'] = [];
            $chartData['usuarios_por_estado'] = [];
        }

        return $chartData;
    }
    

    
    /**
     * Obtener datos de gráficos por período (AJAX)
     */
    public function getChartData() {
        requireRole(ROLE_ADMIN);
        
        $period = $_GET['period'] ?? 'month';
        
        try {
            $chartData = [];
            
            switch($period) {
                case 'month':
                    $usersData = $this->userModel->getUsersByPeriod('week', 4);
                    $propertiesData = $this->propertyModel->getPropertiesByPeriod('week', 4);
                    $salesData = $this->propertyModel->getSalesByPeriod('week', 4);

                    $chartData = [
                        'labels' => $usersData['labels'],
                        'usuarios' => $usersData['data'],
                        'propiedades' => $propertiesData['data'],
                        'ventas' => $salesData['data']
                    ];
                    break;
                    
                case 'quarter':
                    $usersData = $this->userModel->getUsersByPeriod('quarter', 4);
                    $propertiesData = $this->propertyModel->getPropertiesByPeriod('quarter', 4);
                    $salesData = $this->propertyModel->getSalesByPeriod('quarter', 4);

                    $chartData = [
                        'labels' => $usersData['labels'],
                        'usuarios' => $usersData['data'],
                        'propiedades' => $propertiesData['data'],
                        'ventas' => $salesData['data']
                    ];
                    break;
                    
                case 'year':
                    $usersData = $this->userModel->getUsersByPeriod('year', 6);
                    $propertiesData = $this->propertyModel->getPropertiesByPeriod('year', 6);
                    $salesData = $this->propertyModel->getSalesByPeriod('year', 6);

                    $chartData = [
                        'labels' => $usersData['labels'],
                        'usuarios' => $usersData['data'],
                        'propiedades' => $propertiesData['data'],
                        'ventas' => $salesData['data']
                    ];
                    break;
                    
                default:
                    // Default to month (4 weeks)
                    $usersData = $this->userModel->getUsersByPeriod('week', 4);
                    $propertiesData = $this->propertyModel->getPropertiesByPeriod('week', 4);
                    $salesData = $this->propertyModel->getSalesByPeriod('week', 4);

                    $chartData = [
                        'labels' => $usersData['labels'],
                        'usuarios' => $usersData['data'],
                        'propiedades' => $propertiesData['data'],
                        'ventas' => $salesData['data']
                    ];
            }
            
            header('Content-Type: application/json');
            echo json_encode($chartData);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    

    
    /**
     * Calcular comisiones totales
     */
    private function calculateTotalCommissions() {
        try {
            // Calcular 5% de comisión sobre ventas totales
            $totalSales = $this->propertyModel->getTotalSales();
            return is_numeric($totalSales) ? $totalSales * 0.05 : 0;
        } catch (Exception $e) {
            error_log("Error calculando comisiones: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Gestión completa de usuarios
     */
    public function manageUsers() {
        requireRole(ROLE_ADMIN);
        
        $action = $_GET['action'] ?? 'list';
        $userId = $_GET['id'] ?? null;
        
        switch ($action) {
            case 'list':
                try {
                    // Obtener estadísticas de usuarios
                    $stats = [
                        'total' => $this->userModel->getTotalCount(),
                        'admins' => $this->userModel->getCountByRole('admin'),
                        'agentes' => $this->userModel->getCountByRole('agente'),
                        'clientes' => $this->userModel->getCountByRole('cliente'),
                        'activos' => $this->userModel->getUsersByStatusCount('activo'),
                        'suspendidos' => $this->userModel->getUsersByStatusCount('suspendido'),
                        'nuevos_hoy' => $this->userModel->getNewUsersToday(),
                        'nuevos_semana' => $this->userModel->getNewUsersThisWeek()
                    ];
                    
                    $users = $this->userModel->getAllUsers();
                    if (!is_array($users)) {
                        $users = [];
                    }
                } catch (Exception $e) {
                    error_log("Error obteniendo usuarios: " . $e->getMessage());
                    $users = [];
                    $stats = [
                        'total' => 0, 'admins' => 0, 'agentes' => 0, 'clientes' => 0,
                        'activos' => 0, 'suspendidos' => 0, 'nuevos_hoy' => 0, 'nuevos_semana' => 0
                    ];
                }
                $pageTitle = 'Gestión de Usuarios - ' . APP_NAME;
                $currentPage = 'users';
                $includeDataTables = true;
                
                // Capturar el contenido de la vista
                ob_start();
                include APP_PATH . '/views/admin/users_content.php';
                $content = ob_get_clean();
                
                // Incluir el layout administrativo
                include APP_PATH . '/views/layouts/admin.php';
                break;
                
            case 'edit':
                if ($userId) {
                    $user = $this->userModel->getById($userId);
                    if (!$user) {
                        setFlashMessage('error', 'Usuario no encontrado');
                        redirect('/admin/users');
                    }
                    $pageTitle = 'Editar Usuario - ' . APP_NAME;
                    $currentPage = 'users';
                    
                    // Capturar el contenido de la vista
                    ob_start();
                    include APP_PATH . '/views/admin/user_edit_content.php';
                    $content = ob_get_clean();
                    
                    // Incluir el layout administrativo
                    include APP_PATH . '/views/layouts/admin.php';
                } else {
                    setFlashMessage('error', 'ID de usuario requerido');
                    redirect('/admin/users');
                }
                break;
                
            case 'delete':
                if ($userId) {
                    $this->deleteUser($userId);
                } else {
                    setFlashMessage('error', 'ID de usuario requerido');
                    redirect('/admin/users');
                }
                break;
                
            case 'block':
                if ($userId) {
                    $this->blockUser($userId);
                } else {
                    setFlashMessage('error', 'ID de usuario requerido');
                    redirect('/admin/users');
                }
                break;
                
            case 'unblock':
                if ($userId) {
                    $this->unblockUser($userId);
                } else {
                    setFlashMessage('error', 'ID de usuario requerido');
                    redirect('/admin/users');
                }
                break;
                
            case 'change_role':
                if ($userId) {
                    // Si es POST, procesar el cambio de rol
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $this->changeUserRole($userId);
                    } else {
                        // Si es GET, mostrar formulario de cambio de rol
                        $this->showChangeRoleForm($userId);
                    }
                } else {
                    setFlashMessage('error', 'ID de usuario requerido');
                    redirect('/admin/users');
                }
                break;
                
            case 'update':
                if ($userId) {
                    $this->updateUser($userId);
                } else {
                    setFlashMessage('error', 'ID de usuario requerido');
                    redirect('/admin/users');
                }
                break;
                
            case 'export':
                $this->exportUsers();
                break;
                
            default:
                setFlashMessage('error', 'Acción no válida');
                redirect('/admin/users');
                break;
        }
    }
    
    /**
     * Bloquear usuario
     */
    private function blockUser($userId) {
        // Obtener la descripción del motivo del bloqueo
        $blockReason = $_POST['block_reason'] ?? '';
        
        // Validar que se proporcione una razón
        if (empty($blockReason)) {
            setFlashMessage('error', 'Debe proporcionar una razón para el bloqueo');
            redirect('/admin/users?action=edit&id=' . $userId);
        }
        
        // Cambiar estado del usuario
        $result = $this->userModel->changeUserStatus($userId, 'suspendido');
        
        if ($result) {
            // Enviar correo de notificación al usuario
            $this->sendUserBlockNotification($userId, $blockReason);
            
            setFlashMessage('success', 'Usuario bloqueado exitosamente. Se ha enviado notificación por correo.');
        } else {
            setFlashMessage('error', 'Error al bloquear usuario');
        }
        redirect('/admin/users');
    }
    
    /**
     * Desbloquear usuario
     */
    private function unblockUser($userId) {
        $result = $this->userModel->changeUserStatus($userId, 'activo');
        if ($result) {
            setFlashMessage('success', 'Usuario desbloqueado exitosamente');
        } else {
            setFlashMessage('error', 'Error al desbloquear usuario');
        }
        redirect('/admin/users');
    }
    
    /**
     * Cambiar rol de usuario
     */
    /**
     * Mostrar formulario de cambio de rol
     */
    public function showChangeRoleForm($userId) {
        requireRole(ROLE_ADMIN);
        
        $user = $this->userModel->getById($userId);
        if (!$user) {
            setFlashMessage('error', 'Usuario no encontrado');
            redirect('/admin/users');
        }
        
        $pageTitle = 'Cambiar Rol de Usuario - ' . APP_NAME;
        $currentPage = 'users';
        
        // Capturar el contenido de la vista
        ob_start();
        include APP_PATH . '/views/admin/user_change_role_content.php';
        $content = ob_get_clean();
        
        // Incluir el layout administrativo
        include APP_PATH . '/views/layouts/admin.php';
    }
    
    /**
     * Cambiar rol de usuario
     */
    public function changeUserRole($userId) {
        
        $newRole = $_POST['new_role'] ?? '';
        $changeReason = $_POST['change_reason'] ?? '';
        
        // Validar rol
        if (!in_array($newRole, ['cliente', 'agente', 'admin'])) {
            setFlashMessage('error', 'Rol no válido');
            redirect('/admin/users?action=edit&id=' . $userId);
        }
        
        // Obtener usuario actual para comparar
        $currentUser = $this->userModel->getById($userId);
        if (!$currentUser) {
            setFlashMessage('error', 'Usuario no encontrado');
            redirect('/admin/users');
        }
        
        // Verificar que no se esté cambiando el rol del administrador actual
        if ($userId == $_SESSION['user_id'] && $newRole !== 'admin') {
            setFlashMessage('error', 'No puedes cambiar tu propio rol de administrador');
            redirect('/admin/users?action=edit&id=' . $userId);
        }
        
        // Cambiar rol
        $result = $this->userModel->changeUserRole($userId, $newRole);
        
        if ($result) {
            // Enviar notificación por correo si se proporciona razón
            if (!empty($changeReason)) {
                $this->sendUserRoleChangeNotification($userId, $newRole, $changeReason);
            }
            
            setFlashMessage('success', 'Rol de usuario cambiado exitosamente' . (!empty($changeReason) ? '. Se ha enviado notificación por correo.' : ''));
        } else {
            setFlashMessage('error', 'Error al cambiar rol de usuario');
        }
        
        redirect('/admin/users');
    }
    
    /**
     * Eliminar usuario
     */
    private function deleteUser($userId) {
        // Verificar que no sea el usuario actual
        if ($userId == $_SESSION['user_id']) {
            setFlashMessage('error', 'No puedes eliminar tu propia cuenta');
            redirect('/admin/users');
        }
        
        $result = $this->userModel->deleteUser($userId);
        if ($result) {
            setFlashMessage('success', 'Usuario eliminado exitosamente');
        } else {
            setFlashMessage('error', 'Error al eliminar usuario');
        }
        redirect('/admin/users');
    }
    
    /**
     * Actualizar usuario
     */
    public function updateUser($userId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            setFlashMessage('error', 'Método no permitido');
            redirect('/admin/users');
        }
        
        // Obtener datos del formulario
        $userData = [
            'nombre' => $_POST['nombre'] ?? '',
            'apellido' => $_POST['apellido'] ?? '',
            'email' => $_POST['email'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'rol' => $_POST['rol'] ?? '',
            'estado' => $_POST['estado'] ?? '',
            'ciudad' => $_POST['ciudad'] ?? '',
            'sector' => $_POST['sector'] ?? ''
        ];
        
        // Validar datos requeridos
        if (empty($userData['nombre']) || empty($userData['apellido']) || empty($userData['email'])) {
            setFlashMessage('error', 'Nombre, apellido y email son requeridos');
            redirect('/admin/users?action=edit&id=' . $userId);
        }
        
        // Validar email único
        $existingUser = $this->userModel->getByEmail($userData['email']);
        if ($existingUser && $existingUser['id'] != $userId) {
            setFlashMessage('error', 'El email ya está registrado por otro usuario');
            redirect('/admin/users?action=edit&id=' . $userId);
        }
        

        
        // Actualizar usuario
        $result = $this->userModel->updateUserByAdmin($userId, $userData);
        
        if ($result) {
            setFlashMessage('success', 'Usuario actualizado exitosamente');
        } else {
            setFlashMessage('error', 'Error al actualizar usuario');
        }
        
        redirect('/admin/users');
    }
    
    /**
     * Exportar usuarios a CSV
     */
    private function exportUsers() {
        try {
            $users = $this->userModel->getAllUsers();
            
            // Configurar headers para descarga
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="usuarios_' . date('Y-m-d_H-i-s') . '.csv"');
            
            // Crear archivo CSV
            $output = fopen('php://output', 'w');
            
            // BOM para UTF-8
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers del CSV
            fputcsv($output, [
                'ID', 'Nombre', 'Apellido', 'Email', 'Teléfono', 'Rol', 'Estado',
                'Email Verificado', 'Fecha Registro', 'Último Acceso', 'Ciudad', 'Sector'
            ]);
            
            // Datos de usuarios
            foreach ($users as $user) {
                fputcsv($output, [
                    $user['id'],
                    $user['nombre'] ?? '',
                    $user['apellido'] ?? '',
                    $user['email'] ?? '',
                    $user['telefono'] ?? '',
                    $user['rol'] ?? '',
                    $user['estado'] ?? '',
                    $user['email_verificado'] ? 'Sí' : 'No',
                    $user['fecha_registro'] ?? '',
                    $user['ultimo_acceso'] ?? 'Nunca',
                    $user['ciudad'] ?? '',
                    $user['sector'] ?? ''
                ]);
            }
            
            fclose($output);
            exit;
            
        } catch (Exception $e) {
            error_log("Error exportando usuarios: " . $e->getMessage());
            setFlashMessage('error', 'Error al exportar usuarios');
            redirect('/admin/users');
        }
    }
    
    /**
     * Gestión completa de propiedades
     */
    public function manageProperties() {
        requireRole(ROLE_ADMIN);
        
        $action = $_GET['action'] ?? 'list';
        $propertyId = $_GET['id'] ?? null;
        
        switch ($action) {
            case 'list':
                try {
                    // Obtener filtros de la URL
                    $filters = [];
                    if (!empty($_GET['status'])) {
                        $filters['estado_publicacion'] = $_GET['status'];
                    }
                    if (!empty($_GET['type'])) {
                        $filters['tipo'] = $_GET['type'];
                    }
                    if (!empty($_GET['city'])) {
                        $filters['ciudad'] = $_GET['city'];
                    }
                    if (!empty($_GET['search'])) {
                        $filters['search'] = $_GET['search'];
                    }
                    
                    // Obtener propiedades con filtros
                    $properties = $this->propertyModel->getAll($filters);
                    if (!is_array($properties)) {
                        $properties = [];
                    }
                    
                    // Obtener estadísticas actualizadas
                    $totalProperties = count($this->propertyModel->getAll());
                    $activeProperties = $this->propertyModel->getPropertiesByStatus('activa');
                    $reviewProperties = $this->propertyModel->getPropertiesByStatus('en_revision');
                    $soldProperties = $this->propertyModel->getPropertiesByStatus('vendida');
                    
                } catch (Exception $e) {
                    error_log("Error obteniendo propiedades: " . $e->getMessage());
                    $properties = [];
                    $totalProperties = 0;
                    $activeProperties = 0;
                    $reviewProperties = 0;
                    $soldProperties = 0;
                }
                $pageTitle = 'Gestión de Propiedades - ' . APP_NAME;
                $currentPage = 'properties';
                $includeDataTables = true;
                
                // Capturar el contenido de la vista
                ob_start();
                include APP_PATH . '/views/admin/properties_content.php';
                $content = ob_get_clean();
                
                // Incluir el layout administrativo
                include APP_PATH . '/views/layouts/admin.php';
                break;
                
            case 'view':
                if ($propertyId) {
                    $property = $this->propertyModel->getById($propertyId);
                    $pageTitle = 'Ver Propiedad - ' . APP_NAME;
                    $currentPage = 'properties';
                    
                    // Capturar el contenido de la vista
                    ob_start();
                    include APP_PATH . '/views/admin/property_view_content.php';
                    $content = ob_get_clean();
                    
                    // Incluir el layout administrativo
                    include APP_PATH . '/views/layouts/admin.php';
                }
                break;
                
            case 'delete':
                if ($propertyId) {
                    $this->deleteProperty($propertyId);
                }
                break;
                
            case 'approve':
                if ($propertyId) {
                    $this->approveProperty($propertyId);
                }
                break;
                
            case 'reject':
                if ($propertyId) {
                    $this->rejectProperty($propertyId);
                }
                break;
                

                
            case 'export':
                $this->exportProperties();
                break;
        }
    }
    
    /**
     * Eliminar propiedad
     */
    private function deleteProperty($propertyId) {
        $result = $this->propertyModel->delete($propertyId);
        if ($result && isset($result['success']) && $result['success']) {
            setFlashMessage('success', 'Propiedad eliminada exitosamente');
        } else {
            $message = isset($result['message']) ? $result['message'] : 'Error al eliminar propiedad';
            setFlashMessage('error', $message);
        }
        redirect('/admin/properties');
    }
    
    /**
     * Aprobar propiedad
     */
    private function approveProperty($propertyId) {
        $result = $this->propertyModel->validarPropiedad($propertyId, $_SESSION['user_id'] ?? 1);
        if ($result && isset($result['success']) && $result['success']) {
            setFlashMessage('success', 'Propiedad aprobada exitosamente');
        } else {
            $message = isset($result['message']) ? $result['message'] : 'Error al aprobar propiedad';
            setFlashMessage('error', $message);
        }
        redirect('/admin/properties');
    }
    
    /**
     * Rechazar propiedad
     */
    private function rejectProperty($propertyId) {
        $motivo = $_POST['motivo'] ?? 'Rechazada por administrador';
        $result = $this->propertyModel->rechazarPropiedad($propertyId, $_SESSION['user_id'] ?? 1, $motivo);
        if ($result && isset($result['success']) && $result['success']) {
            setFlashMessage('success', 'Propiedad rechazada exitosamente');
        } else {
            $message = isset($result['message']) ? $result['message'] : 'Error al rechazar propiedad';
            setFlashMessage('error', $message);
        }
        redirect('/admin/properties');
    }
    

    
    /**
     * Exportar propiedades a CSV
     */
    private function exportProperties() {
        try {
            // Obtener filtros de la URL
            $filters = [];
            if (!empty($_GET['status'])) {
                $filters['estado_publicacion'] = $_GET['status'];
            }
            if (!empty($_GET['type'])) {
                $filters['tipo'] = $_GET['type'];
            }
            if (!empty($_GET['city'])) {
                $filters['ciudad'] = $_GET['city'];
            }
            if (!empty($_GET['search'])) {
                $filters['search'] = $_GET['search'];
            }
            
            // Obtener propiedades con filtros
            $properties = $this->propertyModel->getAll($filters);
            if (!is_array($properties)) {
                $properties = [];
            }
            
            // Configurar headers para descarga CSV
            $filename = 'propiedades_' . date('Y-m-d_H-i-s') . '.csv';
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            // Crear archivo CSV
            $output = fopen('php://output', 'w');
            
            // BOM para UTF-8
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers del CSV
            fputcsv($output, [
                'ID',
                'Título',
                'Tipo',
                'Precio',
                'Moneda',
                'Ciudad',
                'Sector',
                'Dirección',
                'Metros Cuadrados',
                'Habitaciones',
                'Baños',
                'Estacionamientos',
                'Estado Propiedad',
                'Estado Publicación',
                'Agente',
                'Fecha Creación'
            ]);
            
            // Datos de las propiedades
            foreach ($properties as $property) {
                fputcsv($output, [
                    $property['id'],
                    $property['titulo'],
                    $property['tipo'],
                    $property['precio'],
                    $property['moneda'] ?? 'USD',
                    $property['ciudad'],
                    $property['sector'],
                    $property['direccion'],
                    $property['metros_cuadrados'],
                    $property['habitaciones'],
                    $property['banos'],
                    $property['estacionamientos'] ?? 0,
                    $property['estado_propiedad'] ?? 'bueno',
                    $property['estado_publicacion'] ?? 'activa',
                    $property['agente_nombre'] ?? $property['agente_id'] ?? 'N/A',
                    $property['fecha_creacion'] ?? 'N/A'
                ]);
            }
            
            fclose($output);
            exit;
            
        } catch (Exception $e) {
            error_log("Error exportando propiedades: " . $e->getMessage());
            setFlashMessage('error', 'Error al exportar propiedades');
            redirect('/admin/properties');
        }
    }
    
    /**
     * Gestión de reportes
     */
    public function manageReports() {
        // Log para debugging
        error_log("AdminController::manageReports() - Método llamado");
        error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
        // Debug data removed
        
        // Verificar si es una acción GET específica
        $action = $_GET['action'] ?? '';
        $id = $_GET['id'] ?? null;
        
        error_log("Acción detectada: " . $action . ", ID: " . $id);
        
        if ($action === 'view' && $id) {
            error_log("Procesando vista de reporte ID: " . $id);
            $this->viewReport($id);
            return;
        }
        
        if ($action === 'resolve' && $id) {
            error_log("Procesando acción: resolve para ID: " . $id);
            $respuesta = $_GET['respuesta'] ?? 'Reporte resuelto por administrador';
            $this->resolveReport($id, $respuesta);
            return;
        }
        
        if ($action === 'dismiss' && $id) {
            error_log("Procesando acción: dismiss para ID: " . $id);
            $motivo = $_GET['motivo'] ?? 'Reporte descartado por administrador';
            $this->dismissReport($id, $motivo);
            return;
        }
        
        if ($action === 'delete' && $id) {
            error_log("Procesando acción: delete para ID: " . $id);
            $this->deleteReport($id);
            return;
        }
        
        if ($action === 'export') {
            error_log("Procesando exportación");
            $this->exportReports();
            return;
        }
        
        // Mostrar lista de reportes (incluye action=list y sin action)
        error_log("Mostrando lista de reportes");
        
        try {
            // Obtener filtros
            $filtros = [
                'estado' => $_GET['status'] ?? '',
                'tipo_reporte' => $_GET['type'] ?? '',
                'prioridad' => $_GET['priority'] ?? '',
                'search' => $_GET['search'] ?? ''
            ];
            
            // Obtener reportes con filtros
            $reports = $this->reporteModel->obtenerTodos($filtros);
            
            // Obtener estadísticas
            $totalReportes = $this->reporteModel->getTotalReportes();
            $pendientes = $this->reporteModel->getReportesByStatus('pendiente');
            $resueltos = $this->reporteModel->getReportesByStatus('atendido');
            $descartados = $this->reporteModel->getReportesByStatus('descartado');
            
            // Obtener opciones para filtros
            $tiposReporte = $this->reporteModel->obtenerTiposReporte();
            $estados = $this->reporteModel->obtenerEstados();
            
            // Variables para la vista
            $pageTitle = 'Gestión de Reportes - PropEasy';
            $currentPage = 'reports';
            $includeDataTables = true;
            
            // Capturar el contenido de la vista
            ob_start();
            include APP_PATH . '/views/admin/reports_content.php';
            $content = ob_get_clean();
            
            // Incluir el layout administrativo
            include APP_PATH . '/views/layouts/admin.php';
            
        } catch (Exception $e) {
            error_log("Error en manageReports: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            // Mostrar error amigable
            http_response_code(500);
            include APP_PATH . '/views/errors/500.php';
        }
    }
    
    /**
     * Resolver reporte
     */
    private function resolveReport($reportId, $respuesta = null) {
        try {
            error_log("resolveReport llamado con ID: $reportId, respuesta: $respuesta");
            
            if (!$reportId) {
                error_log("Error: ID de reporte no válido");
                header('Location: /admin/reports');
                exit;
            }
            
            // Usar respuesta proporcionada o valor por defecto
            $respuesta = $respuesta ?: 'Reporte resuelto por administrador';
            
            // Obtener ID del administrador (usar el primer admin disponible)
            $db = new Database();
            $conn = $db->getConnection();
            $stmt = $conn->prepare("SELECT id FROM usuarios WHERE rol = 'admin' LIMIT 1");
            $stmt->execute();
            $admin = $stmt->fetch();
            $adminId = $admin ? $admin['id'] : null;
            
            if (!$adminId) {
                error_log("Error: No se encontró un administrador válido");
                header('Location: /admin/reports');
                exit;
            }
            
            error_log("Actualizando reporte $reportId con admin $adminId");
            
            // Actualizar el reporte
            $result = $this->reporteModel->actualizarEstado($reportId, 'atendido', $respuesta, $adminId);
            
            if ($result) {
                error_log("Reporte $reportId resuelto exitosamente");
            } else {
                error_log("Error al resolver reporte $reportId");
            }
            
        } catch (Exception $e) {
            error_log("Error resolviendo reporte: " . $e->getMessage());
        }
        
        header('Location: /admin/reports');
        exit;
    }
    
    /**
     * Descartar reporte
     */
    private function dismissReport($reportId, $motivo = null) {
        try {
            error_log("dismissReport llamado con ID: $reportId, motivo: $motivo");
            
            if (!$reportId) {
                error_log("Error: ID de reporte no válido");
                header('Location: /admin/reports');
                exit;
            }
            
            // Usar motivo proporcionado o valor por defecto
            $motivo = $motivo ?: 'Reporte descartado por administrador';
            
            // Obtener ID del administrador (usar el primer admin disponible)
            $db = new Database();
            $conn = $db->getConnection();
            $stmt = $conn->prepare("SELECT id FROM usuarios WHERE rol = 'admin' LIMIT 1");
            $stmt->execute();
            $admin = $stmt->fetch();
            $adminId = $admin ? $admin['id'] : null;
            
            if (!$adminId) {
                error_log("Error: No se encontró un administrador válido");
                header('Location: /admin/reports');
                exit;
            }
            
            error_log("Descartando reporte $reportId con admin $adminId");
            
            // Actualizar el reporte
            $result = $this->reporteModel->actualizarEstado($reportId, 'descartado', $motivo, $adminId);
            
            if ($result) {
                error_log("Reporte $reportId descartado exitosamente");
            } else {
                error_log("Error al descartar reporte $reportId");
            }
            
        } catch (Exception $e) {
            error_log("Error descartando reporte: " . $e->getMessage());
        }
        
        header('Location: /admin/reports');
        exit;
    }
    
    /**
     * Eliminar reporte
     */
    private function deleteReport($reportId) {
        try {
            error_log("deleteReport llamado con ID: $reportId");
            
            if (!$reportId) {
                error_log("Error: ID de reporte no válido");
                header('Location: /admin/reports');
                exit;
            }
            
            $result = $this->reporteModel->eliminar($reportId);
            
            if ($result) {
                error_log("Reporte $reportId eliminado exitosamente");
            } else {
                error_log("Error al eliminar reporte $reportId");
            }
            
        } catch (Exception $e) {
            error_log("Error eliminando reporte: " . $e->getMessage());
        }
        
        header('Location: /admin/reports');
        exit;
    }
    
    /**
     * Exportar reportes a CSV
     */
    private function exportReports() {
        try {
            // Obtener filtros de la URL
            $filters = [];
            if (!empty($_GET['status'])) {
                $filters['estado'] = $_GET['status'];
            }
            if (!empty($_GET['priority'])) {
                $filters['prioridad'] = $_GET['priority'];
            }
            if (!empty($_GET['type'])) {
                $filters['tipo'] = $_GET['type'];
            }
            if (!empty($_GET['search'])) {
                $filters['search'] = $_GET['search'];
            }
            
            // Obtener reportes con filtros
            $reports = $this->reporteModel->obtenerTodos($filters, 1000, 0);
            if (!is_array($reports)) {
                $reports = [];
            }
            
            // Configurar headers para descarga CSV
            $filename = 'reportes_' . date('Y-m-d_H-i-s') . '.csv';
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            // Crear archivo CSV
            $output = fopen('php://output', 'w');
            
            // BOM para UTF-8
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers del CSV
            fputcsv($output, [
                'ID',
                'Título',
                'Descripción',
                'Tipo',
                'Prioridad',
                'Estado',
                'Reportado por',
                'Fecha Creación'
            ]);
            
            // Datos de los reportes
            foreach ($reports as $report) {
                fputcsv($output, [
                    $report['id'],
                    $report['titulo'],
                    $report['descripcion'],
                    $report['tipo'],
                    $report['prioridad'],
                    $report['estado'],
                    $report['reportado_por'],
                    $report['fecha_creacion']
                ]);
            }
            
            fclose($output);
            exit;
            
        } catch (Exception $e) {
            error_log("Error exportando reportes: " . $e->getMessage());
            setFlashMessage('error', 'Error al exportar reportes');
            redirect('/admin/reports');
        }
    }
    
    /**
     * Configuración del sistema
     */
    public function systemConfig() {
        requireRole(ROLE_ADMIN);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->updateSystemConfig();
        }
        
        $config = $this->getSystemConfig();
        $pageTitle = 'Configuración del Sistema - ' . APP_NAME;
        $currentPage = 'config';
        
        // Capturar el contenido de la vista
        ob_start();
        include APP_PATH . '/views/admin/config_content.php';
        $content = ob_get_clean();
        
        // Incluir el layout administrativo
        include APP_PATH . '/views/layouts/admin.php';
    }
    
    /**
     * Obtener configuración del sistema
     */
    private function getSystemConfig() {
        // Aquí se obtendría la configuración desde la base de datos
        return [
            'site_name' => APP_NAME,
            'site_description' => 'Sistema de Gestión Inmobiliaria',
            'maintenance_mode' => false,
            'registration_enabled' => true,
            'email_notifications' => true,
            'max_properties_per_user' => 10,
            'max_images_per_property' => 10,
            'commission_rate' => 5.0
        ];
    }
    
    /**
     * Actualizar configuración del sistema
     */
    private function updateSystemConfig() {
        // Aquí se actualizaría la configuración en la base de datos
        setFlashMessage('success', 'Configuración actualizada exitosamente');
        redirect('/admin/config');
    }
    
    /**
     * Logs del sistema
     */
    public function systemLogs() {
        requireRole(ROLE_ADMIN);
        
        $logType = $_GET['type'] ?? 'all';
        $logs = $this->getSystemLogs($logType);
        
        $pageTitle = 'Logs del Sistema - ' . APP_NAME;
        $currentPage = 'logs';
        $includeDataTables = true;
        
        // Capturar el contenido de la vista
        ob_start();
        include APP_PATH . '/views/admin/logs_content.php';
        $content = ob_get_clean();
        
        // Incluir el layout administrativo
        include APP_PATH . '/views/layouts/admin.php';
    }
    
    /**
     * Obtener logs del sistema
     */
    private function getSystemLogs($type = 'all') {
        // Aquí se obtendrían los logs desde archivos o base de datos
        return [];
    }
    
    /**
     * Backup del sistema
     */
    public function systemBackup() {
        requireRole(ROLE_ADMIN);
        
        $action = $_GET['action'] ?? 'list';
        
        switch ($action) {
            case 'create':
                $this->createBackup();
                break;
                
            case 'restore':
                $this->restoreBackup();
                break;
                
            case 'list':
            default:
                $backups = $this->getBackups();
                $pageTitle = 'Backup del Sistema - ' . APP_NAME;
                $currentPage = 'backup';
                $includeDataTables = true;
                
                // Capturar el contenido de la vista
                ob_start();
                include APP_PATH . '/views/admin/backup_content.php';
                $content = ob_get_clean();
                
                // Incluir el layout administrativo
                include APP_PATH . '/views/layouts/admin.php';
                break;
        }
    }
    
    /**
     * Crear backup
     */
    private function createBackup() {
        // Implementar lógica de backup
        setFlashMessage('success', 'Backup creado exitosamente');
        redirect('/admin/backup');
    }
    
    /**
     * Restaurar backup
     */
    private function restoreBackup() {
        // Implementar lógica de restauración
        setFlashMessage('success', 'Backup restaurado exitosamente');
        redirect('/admin/backup');
    }
    
    /**
     * Obtener backups
     */
    private function getBackups() {
        // Implementar lógica para obtener lista de backups
        return [];
    }
    
    /**
     * Enviar notificación de bloqueo al usuario
     * 
     * @param int $userId ID del usuario bloqueado
     * @param string $reason Razón del bloqueo
     */
    private function sendUserBlockNotification($userId, $reason) {
        try {
            // Obtener datos del usuario
            $user = $this->userModel->getById($userId);
            if (!$user) {
                error_log("Error: Usuario no encontrado para enviar notificación de bloqueo - ID: {$userId}");
                return false;
            }
            
            // Obtener datos del administrador que realizó la acción
            $adminId = $_SESSION['user_id'] ?? 1;
            $admin = $this->userModel->getById($adminId);
            $adminName = $admin ? ($admin['nombre'] . ' ' . $admin['apellido']) : 'Administrador del Sistema';
            
            // Incluir EmailHelper
            require_once APP_PATH . '/helpers/EmailHelper.php';
            $emailHelper = new EmailHelper();
            
            // Preparar datos del correo
            $subject = 'Tu cuenta ha sido suspendida - ' . APP_NAME;
            $userName = $user['nombre'] . ' ' . $user['apellido'];
            
            // Generar contenido HTML del correo
            $htmlBody = $this->getUserBlockEmailTemplate($userName, $reason, $adminName);
            
            // Generar contenido de texto plano
            $textBody = $this->getUserBlockEmailText($userName, $reason, $adminName);
            
            // Enviar correo
            $result = $emailHelper->sendCustomEmail(
                $user['email'],
                $subject,
                $htmlBody,
                $textBody,
                $userName
            );
            
            if ($result) {
                error_log("Notificación de bloqueo enviada exitosamente a: {$user['email']}");
            } else {
                error_log("Error enviando notificación de bloqueo a: {$user['email']}");
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Error en sendUserBlockNotification: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Generar template HTML para correo de bloqueo
     */
    private function getUserBlockEmailTemplate($userName, $reason, $adminName) {
        $contactEmail = SUPPORT_EMAIL ?? 'soporte@propeasy.com';
        $supportPhone = SUPPORT_PHONE ?? '809 359 5322';
        
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Cuenta Suspendida - " . APP_NAME . "</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; background-color: #f4f4f4; }
                .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; }
                .header h1 { margin: 0; font-size: 24px; }
                .content { padding: 30px; }
                .alert { background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px; padding: 15px; margin: 20px 0; }
                .alert h3 { color: #856404; margin-top: 0; }
                .reason-box { background: #f8f9fa; border-left: 4px solid #dc3545; padding: 15px; margin: 20px 0; }
                .contact-info { background: #e9ecef; padding: 20px; border-radius: 5px; margin: 20px 0; }
                .footer { background: #f8f9fa; padding: 20px; text-align: center; color: #6c757d; font-size: 14px; }
                .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px; }
                .btn:hover { background: #0056b3; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>⚠️ Cuenta Suspendida</h1>
                    <p>Tu cuenta en " . APP_NAME . " ha sido suspendida temporalmente</p>
                </div>
                
                <div class='content'>
                    <p>Hola <strong>{$userName}</strong>,</p>
                    
                    <div class='alert'>
                        <h3>🚫 Acceso Restringido</h3>
                        <p>Tu cuenta ha sido suspendida por el administrador del sistema. Durante este período, no podrás acceder a los servicios de " . APP_NAME . ".</p>
                    </div>
                    
                    <div class='reason-box'>
                        <h4>📋 Motivo de la Suspensión:</h4>
                        <p><em>\"{$reason}\"</em></p>
                        <p><small>Acción realizada por: <strong>{$adminName}</strong></small></p>
                    </div>
                    
                    <h4>🔄 ¿Qué puedes hacer?</h4>
                    <ul>
                        <li><strong>Revisar el motivo:</strong> Lee cuidadosamente la razón proporcionada arriba</li>
                        <li><strong>Contactar soporte:</strong> Si consideras que esto es un error, puedes contactarnos</li>
                        <li><strong>Esperar revisión:</strong> Tu caso será revisado por nuestro equipo</li>
                    </ul>
                    
                    <div class='contact-info'>
                        <h4>📞 Contacto de Soporte</h4>
                        <p><strong>Email:</strong> <a href='mailto:{$contactEmail}'>{$contactEmail}</a></p>
                        <p><strong>Teléfono:</strong> {$supportPhone}</p>
                        <p><strong>Horario:</strong> Lunes a Viernes, 8:00 AM - 6:00 PM</p>
                    </div>
                    
                    <p>Gracias por tu comprensión.</p>
                    <p>Saludos,<br><strong>Equipo de " . APP_NAME . "</strong></p>
                </div>
                
                <div class='footer'>
                    <p>Este es un mensaje automático del sistema " . APP_NAME . "</p>
                    <p>Si tienes preguntas, no respondas a este correo. Contacta directamente a soporte.</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Generar contenido de texto plano para correo de bloqueo
     */
    private function getUserBlockEmailText($userName, $reason, $adminName) {
        $contactEmail = SUPPORT_EMAIL ?? 'soporte@propeasy.com';
        $supportPhone = SUPPORT_PHONE ?? '809 359 5322';
        
        return "
CUENTA SUSPENDIDA - " . APP_NAME . "

Hola {$userName},

Tu cuenta ha sido suspendida por el administrador del sistema. Durante este período, no podrás acceder a los servicios de " . APP_NAME . ".

MOTIVO DE LA SUSPENSIÓN:
\"{$reason}\"

Acción realizada por: {$adminName}

¿QUÉ PUEDES HACER?

1. Revisar el motivo: Lee cuidadosamente la razón proporcionada arriba
2. Contactar soporte: Si consideras que esto es un error, puedes contactarnos
3. Esperar revisión: Tu caso será revisado por nuestro equipo

CONTACTO DE SOPORTE:
Email: {$contactEmail}
Teléfono: {$supportPhone}
Horario: Lunes a Viernes, 8:00 AM - 6:00 PM

Gracias por tu comprensión.

Saludos,
Equipo de " . APP_NAME . "

---
Este es un mensaje automático del sistema " . APP_NAME . "
Si tienes preguntas, no respondas a este correo. Contacta directamente a soporte.";
    }
    
    /**
     * Enviar notificación de cambio de rol al usuario
     * 
     * @param int $userId ID del usuario
     * @param string $newRole Nuevo rol asignado
     * @param string $reason Razón del cambio
     */
    private function sendUserRoleChangeNotification($userId, $newRole, $reason) {
        try {
            // Obtener datos del usuario
            $user = $this->userModel->getById($userId);
            if (!$user) {
                error_log("Error: Usuario no encontrado para enviar notificación de cambio de rol - ID: {$userId}");
                return false;
            }
            
            // Obtener datos del administrador que realizó la acción
            $adminId = $_SESSION['user_id'] ?? 1;
            $admin = $this->userModel->getById($adminId);
            $adminName = $admin ? ($admin['nombre'] . ' ' . $admin['apellido']) : 'Administrador del Sistema';
            
            // Incluir EmailHelper
            require_once APP_PATH . '/helpers/EmailHelper.php';
            $emailHelper = new EmailHelper();
            
            // Preparar datos del correo
            $subject = 'Tu rol ha sido actualizado - ' . APP_NAME;
            $userName = $user['nombre'] . ' ' . $user['apellido'];
            
            // Generar contenido HTML del correo
            $htmlBody = $this->getUserRoleChangeEmailTemplate($userName, $newRole, $reason, $adminName);
            
            // Generar contenido de texto plano
            $textBody = $this->getUserRoleChangeEmailText($userName, $newRole, $reason, $adminName);
            
            // Enviar correo
            $result = $emailHelper->sendCustomEmail(
                $user['email'],
                $subject,
                $htmlBody,
                $textBody,
                $userName
            );
            
            if ($result) {
                error_log("Notificación de cambio de rol enviada exitosamente a: {$user['email']}");
            } else {
                error_log("Error enviando notificación de cambio de rol a: {$user['email']}");
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Error en sendUserRoleChangeNotification: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Generar template HTML para correo de cambio de rol
     */
    private function getUserRoleChangeEmailTemplate($userName, $newRole, $reason, $adminName) {
        $contactEmail = SUPPORT_EMAIL ?? 'soporte@propeasy.com';
        $supportPhone = SUPPORT_PHONE ?? '809 359 5322';
        
        $roleNames = [
            'admin' => 'Administrador',
            'agente' => 'Agente Inmobiliario',
            'cliente' => 'Cliente'
        ];
        
        $newRoleName = $roleNames[$newRole] ?? ucfirst($newRole);
        
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Rol Actualizado - " . APP_NAME . "</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; background-color: #f4f4f4; }
                .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 30px; text-align: center; }
                .header h1 { margin: 0; font-size: 24px; }
                .content { padding: 30px; }
                .alert { background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; padding: 15px; margin: 20px 0; }
                .alert h3 { color: #155724; margin-top: 0; }
                .role-box { background: #f8f9fa; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0; }
                .contact-info { background: #e9ecef; padding: 20px; border-radius: 5px; margin: 20px 0; }
                .footer { background: #f8f9fa; padding: 20px; text-align: center; color: #6c757d; font-size: 14px; }
                .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px; }
                .btn:hover { background: #0056b3; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🔄 Rol Actualizado</h1>
                    <p>Tu rol en " . APP_NAME . " ha sido modificado</p>
                </div>
                
                <div class='content'>
                    <p>Hola <strong>{$userName}</strong>,</p>
                    
                    <div class='alert'>
                        <h3>✅ Cambio de Rol Completado</h3>
                        <p>Tu rol en el sistema ha sido actualizado exitosamente. Ahora tienes acceso a nuevas funcionalidades según tu nuevo rol.</p>
                    </div>
                    
                    <div class='role-box'>
                        <h4>🎯 Nuevo Rol Asignado:</h4>
                        <p><strong>{$newRoleName}</strong></p>
                        <p><small>Acción realizada por: <strong>{$adminName}</strong></small></p>
                        " . (!empty($reason) ? "<p><strong>Motivo:</strong> <em>\"{$reason}\"</em></p>" : "") . "
                    </div>
                    
                    <h4>🆕 ¿Qué cambia con tu nuevo rol?</h4>
                    <ul>
                        <li><strong>Nuevas funcionalidades:</strong> Acceso a herramientas específicas de tu rol</li>
                        <li><strong>Permisos actualizados:</strong> Puedes realizar acciones según tu nuevo nivel de acceso</li>
                        <li><strong>Interfaz personalizada:</strong> Verás opciones relevantes para tu rol</li>
                    </ul>
                    
                    <div class='contact-info'>
                        <h4>📞 Contacto de Soporte</h4>
                        <p><strong>Email:</strong> <a href='mailto:{$contactEmail}'>{$contactEmail}</a></p>
                        <p><strong>Teléfono:</strong> {$supportPhone}</p>
                        <p><strong>Horario:</strong> Lunes a Viernes, 8:00 AM - 6:00 PM</p>
                    </div>
                    
                    <p>¡Bienvenido a tu nuevo rol!</p>
                    <p>Saludos,<br><strong>Equipo de " . APP_NAME . "</strong></p>
                </div>
                
                <div class='footer'>
                    <p>Este es un mensaje automático del sistema " . APP_NAME . "</p>
                    <p>Si tienes preguntas, no respondas a este correo. Contacta directamente a soporte.</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Generar contenido de texto plano para correo de cambio de rol
     */
    private function getUserRoleChangeEmailText($userName, $newRole, $reason, $adminName) {
        $contactEmail = SUPPORT_EMAIL ?? 'soporte@propeasy.com';
        $supportPhone = SUPPORT_PHONE ?? '809 359 5322';
        
        $roleNames = [
            'admin' => 'Administrador',
            'agente' => 'Agente Inmobiliario',
            'cliente' => 'Cliente'
        ];
        
        $newRoleName = $roleNames[$newRole] ?? ucfirst($newRole);
        
        return "
ROL ACTUALIZADO - " . APP_NAME . "

Hola {$userName},

Tu rol en el sistema ha sido actualizado exitosamente. Ahora tienes acceso a nuevas funcionalidades según tu nuevo rol.

NUEVO ROL ASIGNADO:
{$newRoleName}

Acción realizada por: {$adminName}
" . (!empty($reason) ? "Motivo: \"{$reason}\"" : "") . "

¿QUÉ CAMBIA CON TU NUEVO ROL?

1. Nuevas funcionalidades: Acceso a herramientas específicas de tu rol
2. Permisos actualizados: Puedes realizar acciones según tu nuevo nivel de acceso
3. Interfaz personalizada: Verás opciones relevantes para tu rol

CONTACTO DE SOPORTE:
Email: {$contactEmail}
Teléfono: {$supportPhone}
Horario: Lunes a Viernes, 8:00 AM - 6:00 PM

¡Bienvenido a tu nuevo rol!

Saludos,
Equipo de " . APP_NAME . "

---
Este es un mensaje automático del sistema " . APP_NAME . "
Si tienes preguntas, no respondas a este correo. Contacta directamente a soporte.";
    }
    
    /**
     * Toggle favorito (agregar/quitar de favoritos)
     */
    public function toggleFavorite() {
        requireAuth();
        requireRole('admin');
        
        $propertyId = $_POST['property_id'] ?? null;
        $userId = $_SESSION['user_id'] ?? null;
        
        if (!$propertyId || !$userId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Datos requeridos faltantes']);
            return;
        }
        
        require_once APP_PATH . '/models/Favorite.php';
        $favoriteModel = new Favorite();
        
        // Verificar si ya está en favoritos
        $esFavorito = $favoriteModel->esFavorito($userId, $propertyId);
        
        if ($esFavorito) {
            // Quitar de favoritos
            $result = $favoriteModel->eliminarFavorito($userId, $propertyId);
            $action = 'removed';
        } else {
            // Agregar a favoritos
            $result = $favoriteModel->agregarFavorito($userId, $propertyId);
            $action = 'added';
        }
        
        if ($result['success']) {
            // Obtener datos actualizados
            $favorites = $favoriteModel->getFavoritosCompletos($userId);
            $totalFavorites = $favoriteModel->getTotalFavoritosUsuario($userId);
            
            echo json_encode([
                'success' => true,
                'action' => $action,
                'message' => $result['message'],
                'favorites' => $favorites,
                'total_favorites' => $totalFavorites,
                'is_favorite' => !$esFavorito
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => $result['message']
            ]);
        }
    }
    
    /**
     * Obtener favoritos del admin
     */
    public function getFavorites() {
        requireAuth();
        requireRole('admin');
        
        $userId = $_SESSION['user_id'] ?? null;
        
        if (!$userId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
            return;
        }
        
        require_once APP_PATH . '/models/Favorite.php';
        $favoriteModel = new Favorite();
        
        $favorites = $favoriteModel->getFavoritosCompletos($userId);
        $totalFavorites = $favoriteModel->getTotalFavoritosUsuario($userId);
        
        echo json_encode([
            'success' => true,
            'favorites' => $favorites,
            'total_favorites' => $totalFavorites
        ]);
    }
    
    /**
     * Remover favorito específico
     */
    public function removeFavorite() {
        requireAuth();
        requireRole('admin');
        
        $favoriteId = $_POST['favorite_id'] ?? null;
        $userId = $_SESSION['user_id'] ?? null;
        
        if (!$favoriteId || !$userId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Datos requeridos faltantes']);
            return;
        }
        
        require_once APP_PATH . '/models/Favorite.php';
        $favoriteModel = new Favorite();
        
        $result = $favoriteModel->eliminarFavoritoPorId($userId, $favoriteId);
        
        if ($result['success']) {
            // Obtener datos actualizados
            $favorites = $favoriteModel->getFavoritosCompletos($userId);
            $totalFavorites = $favoriteModel->getTotalFavoritosUsuario($userId);
            
            echo json_encode([
                'success' => true,
                'message' => $result['message'],
                'favorites' => $favorites,
                'total_favorites' => $totalFavorites
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => $result['message']
            ]);
        }
    }
    
    /**
     * Ver detalles de un reporte específico
     */
    public function viewReport($reportId) {
        try {
            error_log("viewReport llamado con ID: $reportId");
            
            if (!$reportId) {
                error_log("Error: ID de reporte no válido");
                header('Location: /admin/reports');
                exit;
            }
            
            // Obtener el reporte
            $report = $this->reporteModel->obtenerPorId($reportId);
            
            if (!$report) {
                error_log("Error: Reporte no encontrado con ID: $reportId");
                header('Location: /admin/reports');
                exit;
            }
            
            error_log("Reporte encontrado: " . $report['titulo']);
            
            // Variables para la vista
            $pageTitle = 'Ver Reporte - PropEasy';
            $currentPage = 'reports';
            $includeDataTables = false;
            
            // Capturar el contenido de la vista
            ob_start();
            include APP_PATH . '/views/admin/report_view_content.php';
            $content = ob_get_clean();
            
            // Incluir el layout administrativo
            include APP_PATH . '/views/layouts/admin.php';
            
        } catch (Exception $e) {
            error_log("Error en viewReport: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            // Mostrar error amigable
            http_response_code(500);
            include APP_PATH . '/views/errors/500.php';
        }
    }
} 
