<?php
/**
 * Controlador ClienteController - Gestión de Clientes
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este controlador maneja todas las operaciones relacionadas con los clientes:
 * perfil, favoritos, solicitudes, etc.
 */

require_once APP_PATH . '/models/User.php';
require_once APP_PATH . '/models/Favorite.php';
require_once APP_PATH . '/models/SolicitudCompra.php';
require_once APP_PATH . '/models/Property.php';

class ClienteController {
    private $userModel;
    private $favoriteModel;
    private $solicitudModel;
    private $propertyModel;
    
    /**
     * Constructor del controlador
     */
    public function __construct() {
        $this->userModel = new User();
        $this->favoriteModel = new Favorite();
        $this->solicitudModel = new SolicitudCompra();
        $this->propertyModel = new Property();
    }
    
    /**
     * Mostrar perfil del cliente
     */
    public function showPerfil() {
        requireAuth();
        requireRole(ROLE_CLIENTE);
        
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getById($userId);
        
        if (!$user) {
            setFlashMessage('error', 'Usuario no encontrado.');
            redirect('/dashboard');
        }
        
        // Obtener estadísticas del cliente
        $stats = $this->getClienteStats($userId);
        
        $pageTitle = 'Mi Perfil - ' . APP_NAME;
        $csrfToken = generateCSRFToken();
        
        // Capturar el contenido de la vista
        ob_start();
        include APP_PATH . '/views/cliente/perfil.php';
        $content = ob_get_clean();
        
        // Incluir el layout principal
        include APP_PATH . '/views/layouts/main.php';
    }
    
    /**
     * Actualizar perfil del cliente
     */
    public function updatePerfil() {
        requireAuth();
        requireRole(ROLE_CLIENTE);
        
        // Verificar método HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/cliente/perfil');
        }
        
        // Verificar CSRF token
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Token de seguridad inválido.');
            redirect('/cliente/perfil');
        }
        
        $userId = $_SESSION['user_id'];
        
        // Obtener datos del formulario
        $userData = [
            'nombre' => $_POST['nombre'] ?? '',
            'apellido' => $_POST['apellido'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'ciudad' => $_POST['ciudad'] ?? '',
            'sector' => $_POST['sector'] ?? '',
            'password' => $_POST['password'] ?? '',
            'confirm_password' => $_POST['confirm_password'] ?? ''
        ];
        
        // Validar confirmación de contraseña si se proporciona
        if (!empty($userData['password'])) {
            if ($userData['password'] !== $userData['confirm_password']) {
                setFlashMessage('error', 'Las contraseñas no coinciden.');
                redirect('/cliente/perfil');
            }
        } else {
            // Si no se proporciona contraseña, remover del array
            unset($userData['password']);
            unset($userData['confirm_password']);
        }
        
        $result = $this->userModel->updateProfile($userId, $userData);
        
        if ($result['success']) {
            setFlashMessage('success', $result['message']);
            
            // Actualizar datos de sesión si se cambió el nombre
            if (isset($userData['nombre'])) {
                $_SESSION['user_nombre'] = $userData['nombre'];
            }
            if (isset($userData['apellido'])) {
                $_SESSION['user_apellido'] = $userData['apellido'];
            }
        } else {
            setFlashMessage('error', $result['message']);
        }
        
        redirect('/cliente/perfil');
    }
    
    /**
     * Obtener estadísticas del cliente
     */
    private function getClienteStats($userId) {
        $stats = [
            'favoritos' => 0,
            'solicitudes' => 0,
            'citas' => 0
        ];
        
        try {
            // Contar favoritos
            $stats['favoritos'] = $this->favoriteModel->getTotalFavoritosUsuario($userId);
            
            // Contar solicitudes - usar método correcto
            $solicitudes = $this->solicitudModel->obtenerPorCliente($userId);
            $stats['solicitudes'] = count($solicitudes);
            
            // Contar citas
            require_once APP_PATH . '/models/Appointment.php';
            $appointmentModel = new Appointment();
            $stats['citas'] = $appointmentModel->countByClient($userId);
            
        } catch (Exception $e) {
            error_log("Error obteniendo estadísticas del cliente: " . $e->getMessage());
        }
        
        return $stats;
    }
    
    /**
     * Mostrar dashboard del cliente
     */
    public function showDashboard() {
        requireAuth();
        requireRole(ROLE_CLIENTE);
        
        $userId = $_SESSION['user_id'];
        
        // Obtener datos para el dashboard
        $recentFavorites = $this->favoriteModel->getFavoritosUsuario($userId, 5);
        $recentSolicitudes = $this->solicitudModel->obtenerPorCliente($userId, 5);
        $stats = $this->getClienteStats($userId);
        
        // Obtener citas recientes del cliente
        require_once APP_PATH . '/models/Appointment.php';
        $appointmentModel = new Appointment();
        $recentCitas = $appointmentModel->getByClient($userId, null, 5, 0);
        
        // Obtener propiedades del cliente (las que ha enviado como solicitudes)
        $misPropiedades = $this->getMisPropiedades($userId, 6);
        
        $pageTitle = 'Dashboard del Cliente - ' . APP_NAME;
        
        // Capturar el contenido de la vista
        ob_start();
        include APP_PATH . '/views/cliente/dashboard.php';
        $content = ob_get_clean();
        
        // Incluir el layout principal
        include APP_PATH . '/views/layouts/main.php';
    }
    
    /**
     * Mostrar historial de actividades
     */
    public function showHistorial() {
        requireAuth();
        requireRole(ROLE_CLIENTE);
        
        $userId = $_SESSION['user_id'];
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        // Obtener historial de actividades
        $actividades = $this->getActividadesCliente($userId, $limit, $offset);
        
        $pageTitle = 'Mi Historial - ' . APP_NAME;
        
        // Capturar el contenido de la vista
        ob_start();
        include APP_PATH . '/views/cliente/historial.php';
        $content = ob_get_clean();
        
        // Incluir el layout principal
        include APP_PATH . '/views/layouts/main.php';
    }
    
    /**
     * Endpoint AJAX para cargar más propiedades del cliente
     */
    public function ajaxMisPropiedades() {
        requireAuth();
        requireRole(ROLE_CLIENTE);
        
        $userId = $_SESSION['user_id'];
        $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
        $limit = 6;
        
        $propiedades = $this->getMisPropiedades($userId, $limit, $offset);
        header('Content-Type: application/json');
        echo json_encode(['propiedades' => $propiedades]);
        exit;
    }
    
    /**
     * Eliminar una solicitud de compra del cliente
     */
    public function eliminarSolicitud() {
        requireAuth();
        requireRole(ROLE_CLIENTE);
        
        // Verificar método HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            exit;
        }
        
        // Verificar CSRF token
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            echo json_encode(['error' => 'Token de seguridad inválido']);
            exit;
        }
        
        $solicitudId = (int)($_POST['solicitud_id'] ?? 0);
        $userId = $_SESSION['user_id'];
        
        if ($solicitudId <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'ID de solicitud inválido']);
            exit;
        }
        
        // Verificar que la solicitud pertenece al cliente
        $solicitud = $this->solicitudModel->obtenerPorId($solicitudId);
        if (!$solicitud || $solicitud['cliente_id'] != $userId) {
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permisos para eliminar esta solicitud']);
            exit;
        }
        
        // Verificar si se puede eliminar
        if (!$this->solicitudModel->puedeEliminar($solicitudId)) {
            http_response_code(400);
            echo json_encode(['error' => 'No se puede eliminar esta solicitud en su estado actual']);
            exit;
        }
        
        // Determinar el tipo de mensaje según el estado
        $mensajeEliminacion = 'Solicitud eliminada correctamente';
        if ($solicitud['estado'] === 'cerrado') {
            $mensajeEliminacion = 'Solicitud cerrada eliminada correctamente';
        }
        
        // Eliminar la solicitud (usar eliminación lógica por seguridad)
        $eliminado = $this->solicitudModel->eliminarLogico($solicitudId);
        
        if ($eliminado) {
            // Registrar actividad
            require_once APP_PATH . '/models/ActivityLog.php';
            $activityLog = new ActivityLog();
            $activityLog->logActivity($userId, 'eliminar_solicitud', [
                'solicitud_id' => $solicitudId,
                'propiedad_id' => $solicitud['propiedad_id'],
                'propiedad_titulo' => $solicitud['titulo_propiedad']
            ]);
            
            echo json_encode(['success' => true, 'message' => $mensajeEliminacion]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al eliminar la solicitud']);
        }
        exit;
    }

    /**
     * Obtener propiedades del cliente (las que ha enviado como solicitudes)
     */
    private function getMisPropiedades($userId, $limit = 6, $offset = 0) {
        try {
            $sql = "SELECT sc.id as solicitud_id,
                           sc.cliente_id,
                           sc.propiedad_id,
                           sc.agente_id,
                           sc.estado,
                           sc.fecha_solicitud,
                           p.id as p_propiedad_id,
                           p.titulo as titulo_propiedad,
                           p.precio as precio_propiedad,
                           p.ciudad as ciudad_propiedad,
                           p.sector as sector_propiedad,
                           p.tipo as tipo_propiedad,
                           p.habitaciones as habitaciones_propiedad,
                           p.banos as banos_propiedad,
                           p.metros_cuadrados as area_propiedad,
                           (SELECT ruta FROM imagenes_propiedades WHERE propiedad_id = p.id AND es_principal = 1 LIMIT 1) as foto_propiedad,
                           ua.nombre as nombre_agente,
                           ua.apellido as apellido_agente,
                           ua.foto_perfil as foto_agente
                    FROM solicitudes_compra sc
                    INNER JOIN propiedades p ON sc.propiedad_id = p.id
                    INNER JOIN usuarios ua ON sc.agente_id = ua.id
                    WHERE sc.cliente_id = ?
                    ORDER BY sc.fecha_solicitud DESC
                    LIMIT ? OFFSET ?";
            $db = new Database();
            return $db->select($sql, [$userId, $limit, $offset]);
        } catch (Exception $e) {
            error_log("Error obteniendo propiedades del cliente: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener actividades del cliente
     */
    private function getActividadesCliente($userId, $limit = 20, $offset = 0) {
        $actividades = [];
        
        try {
            // Obtener solicitudes recientes
            $solicitudes = $this->solicitudModel->obtenerPorCliente($userId, $limit, $offset);
            foreach ($solicitudes as $solicitud) {
                $actividades[] = [
                    'tipo' => 'solicitud',
                    'fecha' => $solicitud['fecha_solicitud'],
                    'descripcion' => 'Solicitud de compra para ' . $solicitud['titulo_propiedad'],
                    'estado' => $solicitud['estado'],
                    'data' => $solicitud
                ];
            }
            
            // Obtener favoritos recientes
            $favoritos = $this->favoriteModel->getFavoritosUsuario($userId, $limit, $offset);
            foreach ($favoritos as $favorito) {
                $actividades[] = [
                    'tipo' => 'favorito',
                    'fecha' => $favorito['fecha_agregado'],
                    'descripcion' => 'Agregaste a favoritos: ' . $favorito['titulo_propiedad'],
                    'estado' => 'activo',
                    'data' => $favorito
                ];
            }
            
            // Ordenar por fecha
            usort($actividades, function($a, $b) {
                return strtotime($b['fecha']) - strtotime($a['fecha']);
            });
            
        } catch (Exception $e) {
            error_log("Error obteniendo actividades del cliente: " . $e->getMessage());
        }
        
        return $actividades;
    }
    
    /**
     * Mostrar configuración del cliente
     */
    public function showConfiguracion() {
        requireAuth();
        requireRole(ROLE_CLIENTE);
        
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getById($userId);
        
        // Obtener configuración actual del cliente (simulado por ahora)
        $config = [
            'notificaciones_email' => true,
            'notificaciones_push' => true,
            'privacidad_perfil' => 'publico',
            'precio_min' => '',
            'precio_max' => '',
            'tipo_propiedad' => '',
            'ciudad_preferida' => ''
        ];
        
        $pageTitle = 'Configuración - ' . APP_NAME;
        $csrfToken = generateCSRFToken();
        
        // Capturar el contenido de la vista
        ob_start();
        include APP_PATH . '/views/cliente/configuracion.php';
        $content = ob_get_clean();
        
        // Incluir el layout principal
        include APP_PATH . '/views/layouts/main.php';
    }
    
    /**
     * Actualizar configuración del cliente
     */
    public function updateConfiguracion() {
        requireAuth();
        requireRole(ROLE_CLIENTE);
        
        // Verificar método HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/cliente/configuracion');
        }
        
        // Verificar CSRF token
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Token de seguridad inválido.');
            redirect('/cliente/configuracion');
        }
        
        $userId = $_SESSION['user_id'];
        
        // Obtener datos del formulario
        $configData = [
            'notificaciones_email' => isset($_POST['notificaciones_email']),
            'notificaciones_push' => isset($_POST['notificaciones_push']),
            'privacidad_perfil' => $_POST['privacidad_perfil'] ?? 'publico',
            'precio_min' => $_POST['precio_min'] ?? '',
            'precio_max' => $_POST['precio_max'] ?? '',
            'tipo_propiedad' => $_POST['tipo_propiedad'] ?? '',
            'ciudad_preferida' => $_POST['ciudad_preferida'] ?? ''
        ];
        
        // Validar datos
        if (!empty($configData['precio_min']) && !empty($configData['precio_max'])) {
            if ($configData['precio_min'] > $configData['precio_max']) {
                setFlashMessage('error', 'El precio mínimo no puede ser mayor al precio máximo.');
                redirect('/cliente/configuracion');
            }
        }
        
        // Aquí se implementaría la lógica para guardar la configuración en la base de datos
        // Por ahora solo mostraremos un mensaje de éxito
        
        setFlashMessage('success', 'Configuración actualizada exitosamente.');
        redirect('/cliente/configuracion');
    }

    /**
     * Mostrar propiedades enviadas por el cliente (Mis Ventas)
     */
    public function misVentas() {
        // Verificar sesión de forma segura
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        if ($_SESSION['user_rol'] !== 'cliente') {
            header('Location: /dashboard');
            exit;
        }

        $user_id = $_SESSION['user_id'];

        try {
            // Obtener propiedades enviadas por el cliente
            $propiedadesEnviadas = $this->propertyModel->getPropiedadesEnviadasPorCliente($user_id);

            // Preparar datos para la vista
            $data = [
                'propiedades' => $propiedadesEnviadas,
                'total_propiedades' => count($propiedadesEnviadas)
            ];

            // Configurar variables para el layout
            $pageTitle = 'Mis Ventas - ' . APP_NAME;

            // Capturar el contenido de la vista
            ob_start();
            include APP_PATH . '/views/cliente/propiedades_enviadas.php';
            $content = ob_get_clean();

            // Incluir el layout principal
            include APP_PATH . '/views/layouts/main.php';

        } catch (Exception $e) {
            error_log("Error en misVentas: " . $e->getMessage());
            // Mostrar página de error
            http_response_code(500);
            include APP_PATH . '/views/errors/500.php';
        }
    }
} 
