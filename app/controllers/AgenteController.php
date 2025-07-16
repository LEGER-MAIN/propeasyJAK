<?php
/**
 * Controlador AgenteController - Panel del Agente Inmobiliario
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 */

class AgenteController {
    /**
     * Mostrar el dashboard del agente
     */
    public function dashboard() {
        $pageTitle = 'Panel del Agente - ' . APP_NAME;
        include APP_PATH . '/views/agente/dashboard.php';
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
     * Mostrar perfil público del agente
     * 
     * @param int $agenteId ID del agente (opcional, si no se proporciona se usa el agente logueado)
     */
    public function perfilPublico($agenteId = null) {
        require_once APP_PATH . '/models/User.php';
        require_once APP_PATH . '/models/Property.php';
        
        // Si no se proporciona ID, usar el agente logueado
        if (!$agenteId) {
            session_start();
            if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'agente') {
                header('Location: /login');
                exit;
            }
            $agenteId = $_SESSION['user_id'];
        }
        
        $userModel = new User();
        $propertyModel = new Property();
        
        // Obtener datos del perfil público
        $agente = $userModel->getPerfilPublicoAgente($agenteId);
        
        if (!$agente) {
            http_response_code(404);
            $pageTitle = 'Agente no encontrado - ' . APP_NAME;
            include APP_PATH . '/views/errors/404.php';
            return;
        }
        
        // Obtener estadísticas del agente
        $estadisticas = $userModel->getEstadisticasAgente($agenteId);
        
        // Obtener propiedades recientes
        $propiedadesRecientes = $userModel->getPropiedadesRecientesAgente($agenteId, 6);
        
        // Obtener calificaciones
        $calificaciones = $userModel->getCalificacionesAgente($agenteId, 5);
        
        $pageTitle = $agente['nombre'] . ' ' . $agente['apellido'] . ' - Agente Inmobiliario - ' . APP_NAME;
        
        // Capturar el contenido de la vista
        ob_start();
        include APP_PATH . '/views/agente/perfil_publico.php';
        $content = ob_get_clean();
        
        // Incluir el layout principal con el contenido
        include APP_PATH . '/views/layouts/main.php';
    }

    /**
     * Mostrar formulario de edición del perfil público
     */
    public function editarPerfilPublico() {
        require_once APP_PATH . '/models/User.php';
        session_start();
        
        if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'agente') {
            header('Location: /login');
            exit;
        }
        
        $agenteId = $_SESSION['user_id'];
        $userModel = new User();
        
        // Obtener datos actuales del agente
        $agente = $userModel->getById($agenteId);
        
        if (!$agente) {
            header('Location: /login');
            exit;
        }
        
        $pageTitle = 'Editar Perfil Público - ' . APP_NAME;
        include APP_PATH . '/views/agente/editar_perfil_publico.php';
    }

    /**
     * Actualizar perfil público (POST)
     */
    public function actualizarPerfilPublico() {
        require_once APP_PATH . '/models/User.php';
        session_start();
        
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'agente') {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            exit;
        }
        
        $agenteId = $_SESSION['user_id'];
        $userModel = new User();
        
        // Procesar datos del formulario
        $data = [
            'biografia' => $_POST['biografia'] ?? '',
            'experiencia_anos' => intval($_POST['experiencia_anos'] ?? 0),
            'especialidades' => isset($_POST['especialidades']) ? $_POST['especialidades'] : [],
            'licencia_inmobiliaria' => $_POST['licencia_inmobiliaria'] ?? '',
            'horario_disponibilidad' => $_POST['horario_disponibilidad'] ?? '',
            'idiomas' => isset($_POST['idiomas']) ? $_POST['idiomas'] : [],
            'perfil_publico_activo' => isset($_POST['perfil_publico_activo']) ? true : false
        ];
        
        // Procesar redes sociales
        $redesSociales = [];
        if (isset($_POST['redes_sociales'])) {
            foreach ($_POST['redes_sociales'] as $red => $url) {
                if (!empty($url)) {
                    $redesSociales[$red] = $url;
                }
            }
        }
        $data['redes_sociales'] = $redesSociales;
        
        // Procesar foto de perfil si se subió
        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->procesarFotoPerfil($_FILES['foto_perfil']);
            if ($uploadResult['success']) {
                $data['foto_perfil'] = $uploadResult['ruta'];
            } else {
                echo json_encode($uploadResult);
                exit;
            }
        }
        
        // Actualizar perfil
        $result = $userModel->actualizarPerfilPublico($agenteId, $data);
        echo json_encode($result);
        exit;
    }

    /**
     * Listar agentes con perfiles públicos
     */
    public function listarAgentes() {
        require_once APP_PATH . '/models/User.php';
        
        $ciudad = $_GET['ciudad'] ?? '';
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = 12;
        $offset = ($page - 1) * $limit;
        
        $userModel = new User();
        $agentes = $userModel->getAgentesConPerfilPublico($ciudad, $limit, $offset);
        
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
} 