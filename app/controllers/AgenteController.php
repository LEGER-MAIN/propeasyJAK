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
} 