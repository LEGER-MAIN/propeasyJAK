<?php
/**
 * Controlador AgenteController - Gestión de Agentes
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este controlador maneja todas las operaciones relacionadas con los agentes:
 * perfil público, propiedades, solicitudes, etc.
 */

require_once APP_PATH . '/models/User.php';
require_once APP_PATH . '/models/Property.php';

class AgenteController {
    private $userModel;
    private $propertyModel;
    
    /**
     * Constructor del controlador
     */
    public function __construct() {
        $this->userModel = new User();
        $this->propertyModel = new Property();
    }
    
    /**
     * Mostrar dashboard del agente
     */
    public function showDashboard() {
        requireAuth();
        requireRole(ROLE_AGENTE);
        
        $userId = $_SESSION['user_id'];
        
        // Obtener datos para el dashboard
        $stats = $this->getAgenteStats($userId);
        $recentProperties = $this->propertyModel->getPropiedadesPorAgente($userId, 5);
        $recentSolicitudes = $this->getSolicitudesRecientes($userId, 5);
        
        $pageTitle = 'Dashboard del Agente - ' . APP_NAME;
        
        // Capturar el contenido de la vista
        ob_start();
        include APP_PATH . '/views/agente/dashboard.php';
        $content = ob_get_clean();
        
        // Incluir el layout principal
        include APP_PATH . '/views/layouts/main.php';
    }
    
    /**
     * Mostrar perfil del agente
     */
    public function showPerfil() {
        requireAuth();
        requireRole(ROLE_AGENTE);
        
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getById($userId);
        
        if (!$user) {
            setFlashMessage('error', 'Usuario no encontrado.');
            redirect('/dashboard');
        }
        
        // Obtener estadísticas del agente
        $stats = $this->getAgenteStats($userId);
        
        // Obtener actividad reciente
        $actividadReciente = $this->getActividadReciente($userId, 5);
        
        // Obtener calificaciones recientes
        $calificaciones = $this->getCalificaciones($userId, 3);
        
        $pageTitle = 'Mi Perfil - ' . APP_NAME;
        $csrfToken = generateCSRFToken();
        
        // Capturar el contenido de la vista
        ob_start();
        include APP_PATH . '/views/agente/perfil.php';
        $content = ob_get_clean();
        
        // Incluir el layout principal
        include APP_PATH . '/views/layouts/main.php';
    }
    
    /**
     * Actualizar perfil del agente
     */
    public function updatePerfil() {
        requireAuth();
        requireRole(ROLE_AGENTE);
        
        // Verificar método HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/agente/perfil');
        }
        
        // Verificar CSRF token
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Token de seguridad inválido.');
            redirect('/agente/perfil');
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
                redirect('/agente/perfil');
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
        
        redirect('/agente/perfil');
    }
    
    /**
     * Mostrar perfil público del agente
     */
    public function showPerfilPublico() {
        requireAuth();
        requireRole(ROLE_AGENTE);
        
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getById($userId);
        
        if (!$user) {
            setFlashMessage('error', 'Usuario no encontrado.');
            redirect('/dashboard');
        }
        
        // Obtener datos del perfil público
        $perfilPublico = $this->userModel->getPerfilPublicoAgente($userId);
        $estadisticas = $this->userModel->getEstadisticasAgente($userId);
        $propiedadesRecientes = $this->userModel->getPropiedadesRecientesAgente($userId, 6);
        $calificaciones = $this->userModel->getCalificacionesAgente($userId, 5);
        
        $pageTitle = 'Mi Perfil Público - ' . APP_NAME;
        $csrfToken = generateCSRFToken();
        
        // Capturar el contenido de la vista
        ob_start();
        include APP_PATH . '/views/agente/perfil_publico.php';
        $content = ob_get_clean();
        
        // Incluir el layout principal
        include APP_PATH . '/views/layouts/main.php';
    }
    
    /**
     * Actualizar perfil público del agente
     */
    public function updatePerfilPublico() {
        requireAuth();
        requireRole(ROLE_AGENTE);
        
        // Verificar método HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/agente/perfil-publico');
        }
        
        // Verificar CSRF token
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Token de seguridad inválido.');
            redirect('/agente/perfil-publico');
        }
        
        $userId = $_SESSION['user_id'];
        
        // Obtener datos del formulario
        $perfilData = [
            'biografia' => $_POST['biografia'] ?? '',
            'experiencia_anos' => (int)($_POST['experiencia_anos'] ?? 0),
            'especialidades' => $_POST['especialidades'] ?? [],
            'licencia_inmobiliaria' => $_POST['licencia_inmobiliaria'] ?? '',
            'horario_disponibilidad' => $_POST['horario_disponibilidad'] ?? '',
            'idiomas' => $_POST['idiomas'] ?? [],
            'perfil_publico_activo' => isset($_POST['perfil_publico_activo']) ? 1 : 0
        ];
        
        $result = $this->userModel->actualizarPerfilPublico($userId, $perfilData);
        
        if ($result['success']) {
            setFlashMessage('success', $result['message']);
        } else {
            setFlashMessage('error', $result['message']);
        }
        
        redirect('/agente/perfil-publico');
    }
    
    /**
     * Obtener estadísticas del agente usando procedimiento almacenado
     */
    private function getAgenteStats($userId) {
        $stats = [
            'propiedades' => 0,
            'propiedades_activas' => 0,
            'propiedades_vendidas' => 0,
            'propiedades_revision' => 0,
            'solicitudes' => 0,
            'solicitudes_pendientes' => 0,
            'total_citas' => 0,
            'citas_pendientes' => 0,
            'calificacion_promedio' => 0,
            'total_ventas' => 0,
            'ingresos_mes' => 0
        ];
        
        try {
            // Usar el nuevo procedimiento almacenado
            $stmt = $this->db->prepare("CALL ObtenerEstadisticasAgente(?)");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result && $row = $result->fetch_assoc()) {
                $stats = [
                    'propiedades' => $row['total_propiedades'] ?? 0,
                    'propiedades_activas' => $row['propiedades_activas'] ?? 0,
                    'propiedades_vendidas' => $row['propiedades_vendidas'] ?? 0,
                    'propiedades_revision' => $row['propiedades_revision'] ?? 0,
                    'solicitudes' => $row['total_solicitudes'] ?? 0,
                    'solicitudes_pendientes' => $row['solicitudes_pendientes'] ?? 0,
                    'total_citas' => $row['total_citas'] ?? 0,
                    'citas_pendientes' => $row['citas_pendientes'] ?? 0,
                    'calificacion_promedio' => $row['calificacion_promedio'] ?? 0,
                    'total_ventas' => $row['total_ventas'] ?? 0,
                    'ingresos_mes' => $row['ingresos_mes'] ?? 0
                ];
            }
            
            $stmt->close();
        } catch (Exception $e) {
            error_log("Error obteniendo estadísticas del agente: " . $e->getMessage());
        }
        
        return $stats;
    }
    
    /**
     * Obtener actividad reciente del agente
     */
    private function getActividadReciente($userId, $limit = 10) {
        try {
            $stmt = $this->db->prepare("CALL ObtenerActividadRecienteAgente(?, ?)");
            $stmt->bind_param("ii", $userId, $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $actividades = [];
            while ($row = $result->fetch_assoc()) {
                $actividades[] = $row;
            }
            
            $stmt->close();
            return $actividades;
        } catch (Exception $e) {
            error_log("Error obteniendo actividad reciente: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener calificaciones del agente
     */
    private function getCalificaciones($userId, $limit = 5) {
        try {
            $stmt = $this->db->prepare("CALL ObtenerCalificacionesAgente(?, ?)");
            $stmt->bind_param("ii", $userId, $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $calificaciones = [];
            while ($row = $result->fetch_assoc()) {
                $calificaciones[] = $row;
            }
            
            $stmt->close();
            return $calificaciones;
        } catch (Exception $e) {
            error_log("Error obteniendo calificaciones: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener solicitudes recientes del agente
     */
    private function getSolicitudesRecientes($userId, $limit = 5) {
        try {
            require_once APP_PATH . '/models/SolicitudCompra.php';
            $solicitudModel = new SolicitudCompra();
            return $solicitudModel->getSolicitudesRecientesPorAgente($userId, $limit);
            
        } catch (Exception $e) {
            error_log("Error obteniendo solicitudes recientes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Listar propiedades pendientes de validación para el agente
     */
    public function propiedadesPendientes() {
        require_once APP_PATH . '/models/Property.php';
        require_once APP_PATH . '/models/User.php';
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'agente') {
            header('Location: /login');
            exit;
        }
        $agenteId = $_SESSION['user_id'];
        $propertyModel = new Property();
        $propiedades = $propertyModel->getPropiedadesPendientes($agenteId);
        $stats = $propertyModel->getStatsByAgent($agenteId);
        // Obtener historial de propiedades validadas y rechazadas
        $aprobadas = $propertyModel->getByAgent($agenteId, 'activa');
        $rechazadas = $propertyModel->getByAgent($agenteId, 'rechazada');
        $pageTitle = 'Propiedades Pendientes - ' . APP_NAME;
        include APP_PATH . '/views/agente/propiedades_pendientes.php';
    }

    /**
     * Aprobar propiedad (POST)
     */
    public function aprobarPropiedad() {
        require_once APP_PATH . '/models/Property.php';
        session_start();
        
        // Establecer cabeceras JSON
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'agente') {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            exit;
        }
        $agenteId = $_SESSION['user_id'];
        $propertyId = $_POST['property_id'] ?? null;
        $comentario = $_POST['comentario'] ?? '';
        if (!$propertyId) {
            echo json_encode(['success' => false, 'message' => 'ID de propiedad requerido']);
            exit;
        }
        $propertyModel = new Property();
        $result = $propertyModel->validarPropiedad($propertyId, $agenteId, $comentario);
        echo json_encode($result);
        exit;
    }

    /**
     * Rechazar propiedad (POST)
     */
    public function rechazarPropiedad() {
        require_once APP_PATH . '/models/Property.php';
        session_start();
        
        // Establecer cabeceras JSON
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'agente') {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            exit;
        }
        $agenteId = $_SESSION['user_id'];
        $propertyId = $_POST['property_id'] ?? null;
        $motivo = $_POST['motivo'] ?? '';
        if (!$propertyId) {
            echo json_encode(['success' => false, 'message' => 'ID de propiedad requerido']);
            exit;
        }
        $propertyModel = new Property();
        $result = $propertyModel->rechazarPropiedad($propertyId, $agenteId, $motivo);
        echo json_encode($result);
        exit;
    }

    /**
     * Eliminar propiedad (POST)
     */
    public function eliminarPropiedad() {
        require_once APP_PATH . '/models/Property.php';
        session_start();
        
        // Establecer cabeceras JSON
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'agente') {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            exit;
        }
        $agenteId = $_SESSION['user_id'];
        $propertyId = $_POST['property_id'] ?? null;
        if (!$propertyId) {
            echo json_encode(['success' => false, 'message' => 'ID de propiedad requerido']);
            exit;
        }
        $propertyModel = new Property();
        $prop = $propertyModel->getById($propertyId);
        if (!$prop || $prop['agente_id'] != $agenteId) {
            echo json_encode(['success' => false, 'message' => 'No autorizado para eliminar esta propiedad']);
            exit;
        }
        $result = $propertyModel->delete($propertyId);
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Propiedad eliminada correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar la propiedad']);
        }
        exit;
    }

    /**
     * Listar agentes con perfiles públicos
     */
    public function listarAgentes() {
        require_once APP_PATH . '/models/User.php';
        
        // Obtener filtros
        $ciudad = $_GET['ciudad'] ?? '';
        $experiencia = $_GET['experiencia'] ?? '';
        $idioma = $_GET['idioma'] ?? '';
        $ordenar = $_GET['ordenar'] ?? 'nombre';
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = 12;
        $offset = ($page - 1) * $limit;
        
        $userModel = new User();
        $agentes = $userModel->getAgentesConPerfilPublicoFiltrados($ciudad, $experiencia, $idioma, $ordenar, $limit, $offset);
        
        // Obtener total para paginación
        $totalAgentes = $userModel->getEstadisticasBusqueda('agentes', '', $ciudad);
        $totalPages = ceil($totalAgentes / $limit);
        
        // Configurar variables para la vista
        $pageTitle = 'Agentes Inmobiliarios - ' . APP_NAME;
        
        // Capturar el contenido de la vista
        ob_start();
        include APP_PATH . '/views/agente/listar_agentes.php';
        $content = ob_get_clean();
        
        // Incluir el layout principal con el contenido
        include APP_PATH . '/views/layouts/main.php';
    }

    /**
     * Procesar subida de foto de perfil
     * 
     * @param array $file Archivo subido
     * @return array Resultado del procesamiento
     */
    private function procesarFotoPerfil($file) {
        try {
            // Validar tipo de archivo
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!in_array($file['type'], $allowedTypes)) {
                return [
                    'success' => false,
                    'message' => 'Solo se permiten archivos JPG, PNG y GIF.'
                ];
            }
            
            // Validar tamaño (máximo 5MB)
            if ($file['size'] > 5 * 1024 * 1024) {
                return [
                    'success' => false,
                    'message' => 'El archivo es demasiado grande. Máximo 5MB.'
                ];
            }
            
            // Crear directorio si no existe
            $uploadDir = UPLOAD_PATH . '/agentes/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Generar nombre único
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'perfil_' . time() . '_' . uniqid() . '.' . $extension;
            $filepath = $uploadDir . $filename;
            
            // Mover archivo
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                return [
                    'success' => true,
                    'ruta' => '/uploads/agentes/' . $filename,
                    'message' => 'Foto de perfil subida exitosamente.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al subir el archivo.'
                ];
            }
            
        } catch (Exception $e) {
            error_log("Error procesando foto de perfil: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error interno del servidor.'
            ];
        }
    }
    
    /**
     * Mostrar perfil público de un agente específico
     * 
     * @param int $agenteId ID del agente
     */
    public function perfilPublico($agenteId) {
        require_once APP_PATH . '/models/User.php';
        require_once APP_PATH . '/models/Property.php';
        
        $userModel = new User();
        $propertyModel = new Property();
        
        // Obtener datos del perfil público del agente
        $perfilPublico = $userModel->getPerfilPublicoAgente($agenteId);
        
        if (!$perfilPublico) {
            // Si no existe perfil público, redirigir a 404
            http_response_code(404);
            $pageTitle = 'Perfil no encontrado - ' . APP_NAME;
            include APP_PATH . '/views/errors/404.php';
            return;
        }
        
        // Obtener estadísticas del agente
        $estadisticas = $userModel->getEstadisticasAgente($agenteId);
        
        // Obtener propiedades recientes del agente
        $propiedadesRecientes = $userModel->getPropiedadesRecientesAgente($agenteId, 6);
        
        // Obtener calificaciones del agente
        $calificaciones = $userModel->getCalificacionesAgente($agenteId, 5);
        
        // Configurar variables para la vista
        $pageTitle = $perfilPublico['nombre'] . ' ' . $perfilPublico['apellido'] . ' - Agente Inmobiliario - ' . APP_NAME;
        
        // Capturar el contenido de la vista
        ob_start();
        include APP_PATH . '/views/agente/perfil_publico.php';
        $content = ob_get_clean();
        
        // Incluir el layout principal con el contenido
        include APP_PATH . '/views/layouts/main.php';
    }
} 