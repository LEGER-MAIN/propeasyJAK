<?php
/**
 * Controlador SolicitudController
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este controlador maneja todas las operaciones relacionadas con las solicitudes de compra
 * de propiedades por parte de los clientes.
 */

// Incluir modelos necesarios
require_once APP_PATH . '/models/SolicitudCompra.php';
require_once APP_PATH . '/models/Property.php';
require_once APP_PATH . '/models/User.php';

class SolicitudController {
    private $solicitudModel;
    private $propertyModel;
    private $userModel;
    
    /**
     * Constructor del controlador
     */
    public function __construct() {
        $this->solicitudModel = new SolicitudCompra();
        $this->propertyModel = new Property();
        $this->userModel = new User();
    }
    
    /**
     * Mostrar formulario de solicitud de compra
     * 
     * @param int $propiedadId ID de la propiedad
     */
    public function show($propiedadId) {
        // Debug: Verificar si el usuario está autenticado
        error_log("SolicitudController::show - Usuario autenticado: " . (isAuthenticated() ? 'SÍ' : 'NO'));
        if (isAuthenticated()) {
            error_log("SolicitudController::show - User ID: " . $_SESSION['user_id']);
            error_log("SolicitudController::show - User Rol: " . $_SESSION['user_rol']);
        }
        
        // Verificar que el usuario esté autenticado
        requireAuth();
        
        // Debug: Verificar propiedad
        error_log("SolicitudController::show - Propiedad ID: " . $propiedadId);
        
        // Verificar que la propiedad existe y está activa
        $propiedad = $this->propertyModel->getById($propiedadId);
        if (!$propiedad || $propiedad['estado_publicacion'] !== PROPERTY_STATUS_ACTIVE) {
            error_log("SolicitudController::show - Propiedad no encontrada o inactiva");
            setFlashMessage('error', 'La propiedad no está disponible para solicitudes.');
            redirect('/properties');
        }
        
        error_log("SolicitudController::show - Propiedad encontrada: " . $propiedad['titulo']);
        
        // Verificar que el usuario no sea el agente de la propiedad
        if ($propiedad['agente_id'] == $_SESSION['user_id']) {
            error_log("SolicitudController::show - Usuario es el agente de la propiedad");
            setFlashMessage('error', 'No puedes solicitar compra de tu propia propiedad.');
            redirect('/properties/show/' . $propiedadId);
        }
        
        // Verificar si ya existe una solicitud del usuario para esta propiedad
        if ($this->solicitudModel->existeSolicitud($_SESSION['user_id'], $propiedadId)) {
            error_log("SolicitudController::show - Ya existe una solicitud");
            setFlashMessage('info', 'Ya has enviado una solicitud para esta propiedad.');
            redirect('/properties/show/' . $propiedadId);
        }
        
        // Obtener información del agente
        $agente = $this->userModel->getById($propiedad['agente_id']);
        
        error_log("SolicitudController::show - Cargando vista de creación");
        
        $pageTitle = 'Solicitar Compra - ' . $propiedad['titulo'] . ' - ' . APP_NAME;
        include APP_PATH . '/views/solicitudes/create.php';
    }
    
    /**
     * Crear una nueva solicitud de compra
     */
    public function store() {
        // Verificar que el usuario esté autenticado
        requireAuth();
        
        // Verificar método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/properties');
        }
        
        // Verificar CSRF token
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Token de seguridad inválido.');
            redirect('/properties');
        }
        
        // Validar datos de entrada
        $propiedadId = (int)($_POST['propiedad_id'] ?? 0);
        $mensaje = sanitizeInput($_POST['mensaje'] ?? '');
        
        // Validar presupuestos con límites seguros para decimal(12,2)
        $presupuestoMin = null;
        $presupuestoMax = null;
        
        if (!empty($_POST['presupuesto_min'])) {
            $presupuestoMin = (float)$_POST['presupuesto_min'];
            // Limitar a 999999999.99 (máximo para decimal(12,2))
            if ($presupuestoMin > 999999999.99) {
                setFlashMessage('error', 'El presupuesto mínimo es demasiado alto. Máximo permitido: $999,999,999.99');
                redirect('/properties/show/' . $propiedadId);
            }
        }
        
        if (!empty($_POST['presupuesto_max'])) {
            $presupuestoMax = (float)$_POST['presupuesto_max'];
            // Limitar a 999999999.99 (máximo para decimal(12,2))
            if ($presupuestoMax > 999999999.99) {
                setFlashMessage('error', 'El presupuesto máximo es demasiado alto. Máximo permitido: $999,999,999.99');
                redirect('/properties/show/' . $propiedadId);
            }
        }
        
        // Validar que presupuesto máximo sea mayor que mínimo
        if ($presupuestoMin !== null && $presupuestoMax !== null && $presupuestoMax < $presupuestoMin) {
            setFlashMessage('error', 'El presupuesto máximo debe ser mayor que el presupuesto mínimo.');
            redirect('/properties/show/' . $propiedadId);
        }
        
        // Validar propiedad
        if (!$propiedadId) {
            setFlashMessage('error', 'ID de propiedad inválido.');
            redirect('/properties');
        }
        
        $propiedad = $this->propertyModel->getById($propiedadId);
        if (!$propiedad || $propiedad['estado_publicacion'] !== PROPERTY_STATUS_ACTIVE) {
            setFlashMessage('error', 'La propiedad no está disponible para solicitudes.');
            redirect('/properties');
        }
        
        // Verificar que no sea el agente de la propiedad
        if ($propiedad['agente_id'] == $_SESSION['user_id']) {
            setFlashMessage('error', 'No puedes solicitar compra de tu propia propiedad.');
            redirect('/properties/show/' . $propiedadId);
        }
        
        // Verificar si ya existe una solicitud
        if ($this->solicitudModel->existeSolicitud($_SESSION['user_id'], $propiedadId)) {
            setFlashMessage('info', 'Ya has enviado una solicitud para esta propiedad.');
            redirect('/properties/show/' . $propiedadId);
        }
        
        // Obtener datos del usuario
        $usuario = $this->userModel->getById($_SESSION['user_id']);
        
        // Preparar datos de la solicitud
        $solicitudData = [
            'propiedad_id' => $propiedadId,
            'cliente_id' => $_SESSION['user_id'],
            'agente_id' => $propiedad['agente_id'],
            'nombre_cliente' => $usuario['nombre'] . ' ' . $usuario['apellido'],
            'email_cliente' => $usuario['email'],
            'telefono_cliente' => $usuario['telefono'],
            'mensaje' => $mensaje,
            'presupuesto_min' => $presupuestoMin,
            'presupuesto_max' => $presupuestoMax
        ];
        
        // Crear la solicitud
        $solicitudId = $this->solicitudModel->crear($solicitudData);
        
        if ($solicitudId) {
            setFlashMessage('success', 'Solicitud enviada correctamente. El agente se pondrá en contacto contigo pronto.');
            
            // Enviar notificación por email al agente (opcional)
            try {
                $this->enviarNotificacionAgente($solicitudId);
            } catch (Exception $e) {
                error_log("Error enviando email de notificación: " . $e->getMessage());
                // No fallar la solicitud si el email falla
            }
            
            redirect('/solicitudes/' . $solicitudId);
        } else {
            setFlashMessage('error', 'Error al enviar la solicitud. Inténtalo de nuevo.');
            redirect('/properties/show/' . $propiedadId);
        }
    }
    
    /**
     * Mostrar una solicitud específica
     * 
     * @param int $id ID de la solicitud
     */
    public function showSolicitud($id) {
        // Verificar que el usuario esté autenticado
        requireAuth();
        
        $solicitud = $this->solicitudModel->obtenerPorId($id);
        if (!$solicitud) {
            setFlashMessage('error', 'Solicitud no encontrada.');
            redirect('/dashboard');
        }
        
        // Verificar que el usuario tenga acceso a esta solicitud
        if ($solicitud['cliente_id'] != $_SESSION['user_id'] && 
            $solicitud['agente_id'] != $_SESSION['user_id'] && 
            !hasRole(ROLE_ADMIN)) {
            setFlashMessage('error', 'No tienes permisos para ver esta solicitud.');
            redirect('/dashboard');
        }
        
        $pageTitle = 'Solicitud de Compra - ' . $solicitud['titulo_propiedad'] . ' - ' . APP_NAME;
        include APP_PATH . '/views/solicitudes/show.php';
    }
    
    /**
     * Mostrar listado de solicitudes del usuario
     */
    public function index() {
        // Verificar que el usuario esté autenticado
        requireAuth();
        
        $page = (int)($_GET['page'] ?? 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        if (hasRole(ROLE_AGENTE)) {
            // Para agentes, mostrar sus solicitudes recibidas
            $solicitudes = $this->solicitudModel->obtenerPorAgente($_SESSION['user_id'], $limit, $offset);
            $estadisticas = $this->solicitudModel->obtenerEstadisticasAgente($_SESSION['user_id']);
        } else {
            // Para clientes, mostrar sus solicitudes enviadas
            $solicitudes = $this->solicitudModel->obtenerPorCliente($_SESSION['user_id'], $limit, $offset);
            $estadisticas = $this->solicitudModel->obtenerEstadisticasCliente($_SESSION['user_id']);
        }
        
        $pageTitle = 'Mis Solicitudes - ' . APP_NAME;
        include APP_PATH . '/views/solicitudes/index.php';
    }
    
    /**
     * Actualizar estado de una solicitud (solo agentes)
     * 
     * @param int $id ID de la solicitud
     */
    public function updateStatus($id) {
        // Verificar que el usuario esté autenticado y sea agente
        requireRole(ROLE_AGENTE);
        
        // Verificar método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/solicitudes');
        }
        
        // Verificar CSRF token
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Token de seguridad inválido.');
            redirect('/solicitudes');
        }
        
        $solicitud = $this->solicitudModel->obtenerPorId($id);
        if (!$solicitud) {
            setFlashMessage('error', 'Solicitud no encontrada.');
            redirect('/solicitudes');
        }
        
        // Verificar que el agente sea el propietario de la solicitud
        if ($solicitud['agente_id'] != $_SESSION['user_id']) {
            setFlashMessage('error', 'No tienes permisos para modificar esta solicitud.');
            redirect('/solicitudes');
        }
        
        $nuevoEstado = sanitizeInput($_POST['estado'] ?? '');
        $respuesta = sanitizeInput($_POST['respuesta'] ?? '');
        
        // Validar estado
        $estadosValidos = [REQUEST_STATUS_NEW, REQUEST_STATUS_REVIEW, REQUEST_STATUS_MEETING, REQUEST_STATUS_CLOSED];
        if (!in_array($nuevoEstado, $estadosValidos)) {
            setFlashMessage('error', 'Estado inválido.');
            redirect('/solicitudes/' . $id);
        }
        
        // Actualizar estado
        if ($this->solicitudModel->actualizarEstado($id, $nuevoEstado, $respuesta)) {
            setFlashMessage('success', 'Estado de la solicitud actualizado correctamente.');
            
            // Enviar notificación por email al cliente (opcional)
            try {
                $this->enviarNotificacionCliente($id, $nuevoEstado, $respuesta);
            } catch (Exception $e) {
                error_log("Error enviando email de notificación: " . $e->getMessage());
                // No fallar la actualización si el email falla
            }
            
            redirect('/solicitudes/' . $id);
        } else {
            setFlashMessage('error', 'Error al actualizar el estado de la solicitud.');
            redirect('/solicitudes/' . $id);
        }
    }
    
    /**
     * Eliminar una solicitud (solo clientes pueden eliminar sus propias solicitudes)
     * 
     * @param int $id ID de la solicitud
     */
    public function delete($id) {
        // Verificar que el usuario esté autenticado
        requireAuth();
        
        // Verificar método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/solicitudes');
        }
        
        // Verificar CSRF token
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Token de seguridad inválido.');
            redirect('/solicitudes');
        }
        
        $solicitud = $this->solicitudModel->obtenerPorId($id);
        if (!$solicitud) {
            setFlashMessage('error', 'Solicitud no encontrada.');
            redirect('/solicitudes');
        }
        
        // Verificar que el usuario sea el cliente de la solicitud o sea admin
        if ($solicitud['cliente_id'] != $_SESSION['user_id'] && !hasRole(ROLE_ADMIN)) {
            setFlashMessage('error', 'No tienes permisos para eliminar esta solicitud.');
            redirect('/solicitudes');
        }
        
        // Solo se pueden eliminar solicitudes en estado 'nuevo'
        if ($solicitud['estado'] !== REQUEST_STATUS_NEW) {
            setFlashMessage('error', 'Solo se pueden eliminar solicitudes en estado "nuevo".');
            redirect('/solicitudes/' . $id);
        }
        
        if ($this->solicitudModel->eliminar($id)) {
            setFlashMessage('success', 'Solicitud eliminada correctamente.');
            redirect('/solicitudes');
        } else {
            setFlashMessage('error', 'Error al eliminar la solicitud.');
            redirect('/solicitudes/' . $id);
        }
    }
    
    /**
     * API: Obtener solicitudes de un cliente (para búsqueda de agentes)
     * 
     * @param int $clienteId ID del cliente
     */
    public function getSolicitudesCliente($clienteId) {
        // Verificar que el usuario esté autenticado
        requireAuth();
        
        // Solo agentes y admins pueden ver solicitudes de clientes
        if (!hasAnyRole([ROLE_AGENTE, ROLE_ADMIN])) {
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permisos para acceder a esta información.']);
            return;
        }
        
        $solicitudes = $this->solicitudModel->obtenerPorCliente($clienteId, 50, 0);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'solicitudes' => $solicitudes
        ]);
    }
    
    /**
     * API: Obtener estadísticas de solicitudes
     */
    public function getStats() {
        // Verificar que el usuario esté autenticado
        requireAuth();
        
        if (hasRole(ROLE_AGENTE)) {
            $estadisticas = $this->solicitudModel->obtenerEstadisticasAgente($_SESSION['user_id']);
        } elseif (hasRole(ROLE_CLIENTE)) {
            $estadisticas = $this->solicitudModel->obtenerEstadisticasCliente($_SESSION['user_id']);
        } else {
            $estadisticas = $this->solicitudModel->obtenerEstadisticasGenerales();
        }
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'estadisticas' => $estadisticas
        ]);
    }
    
    /**
     * Enviar notificación por email al agente
     * 
     * @param int $solicitudId ID de la solicitud
     */
    private function enviarNotificacionAgente($solicitudId) {
        try {
            $solicitud = $this->solicitudModel->obtenerPorId($solicitudId);
            if (!$solicitud) {
                return;
            }
            
            // Por ahora, solo registrar en el log para evitar problemas con el email
            error_log("NOTIFICACIÓN AGENTE - Solicitud ID: {$solicitudId}");
            error_log("NOTIFICACIÓN AGENTE - Propiedad: {$solicitud['titulo_propiedad']}");
            error_log("NOTIFICACIÓN AGENTE - Cliente: {$solicitud['nombre_cliente']} ({$solicitud['email_cliente']})");
            error_log("NOTIFICACIÓN AGENTE - Agente: {$solicitud['email_agente']}");
            
            // TODO: Implementar envío de email cuando esté configurado correctamente
            // require_once APP_PATH . '/helpers/EmailHelper.php';
            // $emailHelper = new EmailHelper();
            // $subject = 'Nueva solicitud de compra - ' . $solicitud['titulo_propiedad'];
            // $message = "...";
            // $emailHelper->sendCustomEmail($solicitud['email_agente'], $subject, $message);
            
        } catch (Exception $e) {
            error_log("Error enviando notificación al agente: " . $e->getMessage());
        }
    }
    
    /**
     * Enviar notificación por email al cliente
     * 
     * @param int $solicitudId ID de la solicitud
     * @param string $estado Nuevo estado
     * @param string $respuesta Respuesta del agente
     */
    private function enviarNotificacionCliente($solicitudId, $estado, $respuesta) {
        try {
            $solicitud = $this->solicitudModel->obtenerPorId($solicitudId);
            if (!$solicitud) {
                return;
            }
            
            // Por ahora, solo registrar en el log para evitar problemas con el email
            error_log("NOTIFICACIÓN CLIENTE - Solicitud ID: {$solicitudId}");
            error_log("NOTIFICACIÓN CLIENTE - Propiedad: {$solicitud['titulo_propiedad']}");
            error_log("NOTIFICACIÓN CLIENTE - Cliente: {$solicitud['nombre_cliente']} ({$solicitud['email_cliente']})");
            error_log("NOTIFICACIÓN CLIENTE - Nuevo Estado: {$estado}");
            if ($respuesta) {
                error_log("NOTIFICACIÓN CLIENTE - Respuesta: {$respuesta}");
            }
            
            // TODO: Implementar envío de email cuando esté configurado correctamente
            // require_once APP_PATH . '/helpers/EmailHelper.php';
            // $emailHelper = new EmailHelper();
            // $subject = 'Actualización de solicitud - ' . $solicitud['titulo_propiedad'];
            // $message = "...";
            // $emailHelper->sendCustomEmail($solicitud['email_cliente'], $subject, $message);
            
        } catch (Exception $e) {
            error_log("Error enviando notificación al cliente: " . $e->getMessage());
        }
    }
} 