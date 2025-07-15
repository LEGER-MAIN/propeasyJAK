<?php
/**
 * Controlador Simple - Cambio de Estado Básico
 * Ejemplo estándar basado en patrones de internet
 */

require_once APP_PATH . '/models/Property.php';

class SimpleController {
    
    /**
     * Cambiar estado de propiedad a activa
     */
    public function activate() {
        // Verificar que sea POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }
        
        // Obtener ID de la propiedad
        $propertyId = $_POST['property_id'] ?? null;
        if (!$propertyId) {
            http_response_code(400);
            echo json_encode(['error' => 'ID de propiedad requerido']);
            return;
        }
        
        // Conectar a BD
        require_once APP_PATH . '/core/Database.php';
        $db = new Database();
        
        // Cambiar estado directamente
        $query = "UPDATE propiedades SET estado_publicacion = 'activa' WHERE id = ?";
        $result = $db->update($query, [$propertyId]);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Propiedad activada']);
        } else {
            echo json_encode(['error' => 'Error al activar propiedad']);
        }
    }
    
    /**
     * Cambiar estado de propiedad a rechazada
     */
    public function reject() {
        // Verificar que sea POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }
        
        // Obtener ID de la propiedad
        $propertyId = $_POST['property_id'] ?? null;
        if (!$propertyId) {
            http_response_code(400);
            echo json_encode(['error' => 'ID de propiedad requerido']);
            return;
        }
        
        // Conectar a BD
        require_once APP_PATH . '/core/Database.php';
        $db = new Database();
        
        // Cambiar estado directamente
        $query = "UPDATE propiedades SET estado_publicacion = 'rechazada' WHERE id = ?";
        $result = $db->update($query, [$propertyId]);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Propiedad rechazada']);
        } else {
            echo json_encode(['error' => 'Error al rechazar propiedad']);
        }
    }
    
    /**
     * Listar propiedades pendientes
     */
    public function pending() {
        // Conectar a BD
        require_once APP_PATH . '/core/Database.php';
        $db = new Database();
        
        // Obtener propiedades pendientes
        $query = "SELECT * FROM propiedades WHERE estado_publicacion IN ('pending', 'en_revision')";
        $properties = $db->select($query);
        
        // Mostrar vista simple
        $pageTitle = 'Propiedades Pendientes';
        include APP_PATH . '/views/simple/pending.php';
    }
} 