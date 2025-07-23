<?php
/**
 * Controlador de API
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este controlador maneja las APIs REST para integraciones futuras
 * y acceso programático a los datos del sistema.
 */

class ApiController {
    private $userModel;
    private $propertyModel;
    private $solicitudModel;
    private $appointmentModel;
    private $reporteModel;
    
    public function __construct() {
        require_once APP_PATH . '/models/User.php';
        require_once APP_PATH . '/models/Property.php';
        require_once APP_PATH . '/models/SolicitudCompra.php';
        require_once APP_PATH . '/models/Appointment.php';
        require_once APP_PATH . '/models/ReporteIrregularidad.php';
        
        $this->userModel = new User();
        $this->propertyModel = new Property();
        $this->solicitudModel = new SolicitudCompra();
        $this->appointmentModel = new Appointment();
        $this->reporteModel = new ReporteIrregularidad();
        
        // Configurar headers para API
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        
        // Manejar preflight requests
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }
    
    /**
     * Obtener lista de propiedades
     */
    public function properties() {
        try {
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? 10;
            $status = $_GET['status'] ?? null;
            $type = $_GET['type'] ?? null;
            $city = $_GET['city'] ?? null;
            $minPrice = $_GET['min_price'] ?? null;
            $maxPrice = $_GET['max_price'] ?? null;
            
            $properties = $this->propertyModel->getPropertiesForAPI([
                'page' => $page,
                'limit' => $limit,
                'status' => $status,
                'type' => $type,
                'city' => $city,
                'min_price' => $minPrice,
                'max_price' => $maxPrice
            ]);
            
            $total = $this->propertyModel->getTotalPropertiesForAPI([
                'status' => $status,
                'type' => $type,
                'city' => $city,
                'min_price' => $minPrice,
                'max_price' => $maxPrice
            ]);
            
            $response = [
                'success' => true,
                'data' => $properties,
                'pagination' => [
                    'page' => (int)$page,
                    'limit' => (int)$limit,
                    'total' => $total,
                    'pages' => ceil($total / $limit)
                ]
            ];
            
            echo json_encode($response);
            
        } catch (Exception $e) {
            $this->sendErrorResponse('Error al obtener propiedades: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Obtener una propiedad específica
     */
    public function property($id) {
        try {
            if (!$id) {
                $this->sendErrorResponse('ID de propiedad requerido', 400);
                return;
            }
            
            $property = $this->propertyModel->getPropertyById($id);
            
            if (!$property) {
                $this->sendErrorResponse('Propiedad no encontrada', 404);
                return;
            }
            
            // Obtener imágenes de la propiedad
            $images = $this->propertyModel->getPropertyImages($id);
            $property['images'] = $images;
            
            // Obtener información del agente
            if ($property['agente_id']) {
                $agent = $this->userModel->getUserById($property['agente_id']);
                $property['agent'] = $agent;
            }
            
            $response = [
                'success' => true,
                'data' => $property
            ];
            
            echo json_encode($response);
            
        } catch (Exception $e) {
            $this->sendErrorResponse('Error al obtener la propiedad: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Crear una nueva solicitud de compra
     */
    public function createRequest() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->sendErrorResponse('Método no permitido', 405);
                return;
            }
            
            // Obtener datos del JSON
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                $this->sendErrorResponse('Datos JSON inválidos', 400);
                return;
            }
            
            // Validar datos requeridos
            $requiredFields = ['propiedad_id', 'cliente_id', 'nombre', 'email', 'telefono'];
            foreach ($requiredFields as $field) {
                if (empty($input[$field])) {
                    $this->sendErrorResponse("Campo requerido: {$field}", 400);
                    return;
                }
            }
            
            // Crear la solicitud
            $solicitudData = [
                'propiedad_id' => $input['propiedad_id'],
                'cliente_id' => $input['cliente_id'],
                'nombre' => $input['nombre'],
                'email' => $input['email'],
                'telefono' => $input['telefono'],
                'mensaje' => $input['mensaje'] ?? '',
                'presupuesto' => $input['presupuesto'] ?? null,
                'fecha_visita_preferida' => $input['fecha_visita_preferida'] ?? null
            ];
            
            $solicitudId = $this->solicitudModel->createSolicitud($solicitudData);
            
            if ($solicitudId) {
                // Obtener la solicitud creada
                $solicitud = $this->solicitudModel->getSolicitudById($solicitudId);
                
                $response = [
                    'success' => true,
                    'message' => 'Solicitud creada correctamente',
                    'data' => $solicitud
                ];
                
                echo json_encode($response);
            } else {
                $this->sendErrorResponse('Error al crear la solicitud', 500);
            }
            
        } catch (Exception $e) {
            $this->sendErrorResponse('Error al crear la solicitud: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Obtener estadísticas del sistema
     */
    public function stats() {
        try {
            $stats = [
                'properties' => [
                    'total' => $this->propertyModel->getTotalProperties(),
                    'active' => $this->propertyModel->getPropertiesByStatus('activa'),
                    'sold' => $this->propertyModel->getPropertiesByStatus('vendida'),
                    'pending' => $this->propertyModel->getPropertiesByStatus('en_revision')
                ],
                'users' => [
                    'total_agents' => $this->userModel->getUsersByRole('agente'),
                    'total_clients' => $this->userModel->getUsersByRole('cliente'),
                    'active_users' => $this->userModel->getActiveUsers()
                ],
                'requests' => [
                    'total' => $this->solicitudModel->getTotalSolicitudes(),
                    'new' => $this->solicitudModel->getSolicitudesByStatus('nuevo'),
                    'in_review' => $this->solicitudModel->getSolicitudesByStatus('en_revision'),
                    'closed' => $this->solicitudModel->getSolicitudesByStatus('cerrado')
                ],
                'appointments' => [
                    'total' => $this->appointmentModel->getTotalAppointments(),
                    'proposed' => $this->appointmentModel->getAppointmentsByStatus('propuesta'),
                    'accepted' => $this->appointmentModel->getAppointmentsByStatus('aceptada'),
                    'completed' => $this->appointmentModel->getAppointmentsByStatus('completada')
                ]
            ];
            
            $response = [
                'success' => true,
                'data' => $stats
            ];
            
            echo json_encode($response);
            
        } catch (Exception $e) {
            $this->sendErrorResponse('Error al obtener estadísticas: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Obtener ciudades disponibles
     */
    public function cities() {
        try {
            $cities = $this->propertyModel->getAvailableCities();
            
            $response = [
                'success' => true,
                'data' => $cities
            ];
            
            echo json_encode($response);
            
        } catch (Exception $e) {
            $this->sendErrorResponse('Error al obtener ciudades: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Obtener tipos de propiedades
     */
    public function propertyTypes() {
        try {
            $types = $this->propertyModel->getAvailablePropertyTypes();
            
            $response = [
                'success' => true,
                'data' => $types
            ];
            
            echo json_encode($response);
            
        } catch (Exception $e) {
            $this->sendErrorResponse('Error al obtener tipos de propiedades: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Buscar propiedades
     */
    public function searchProperties() {
        try {
            $query = $_GET['q'] ?? '';
            $filters = [
                'type' => $_GET['type'] ?? null,
                'city' => $_GET['city'] ?? null,
                'min_price' => $_GET['min_price'] ?? null,
                'max_price' => $_GET['max_price'] ?? null,
                'bedrooms' => $_GET['bedrooms'] ?? null,
                'bathrooms' => $_GET['bathrooms'] ?? null
            ];
            
            $properties = $this->propertyModel->searchProperties($query, $filters);
            
            $response = [
                'success' => true,
                'data' => $properties,
                'query' => $query,
                'filters' => $filters
            ];
            
            echo json_encode($response);
            
        } catch (Exception $e) {
            $this->sendErrorResponse('Error en la búsqueda: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Obtener agentes disponibles con paginación y búsqueda
     */
    public function agents() {
        try {
            $page = intval($_GET['page'] ?? 0);
            $search = $_GET['search'] ?? '';
            $limit = 20; // 20 agentes por página
            $offset = $page * $limit;
            
            // Obtener agentes con filtros
            $agents = $this->propertyModel->getAgentesDisponiblesPaginated($search, $limit, $offset);
            
            $response = [
                'success' => true,
                'agentes' => $agents,
                'page' => $page,
                'limit' => $limit,
                'hasMore' => count($agents) === $limit
            ];
            
            echo json_encode($response);
            
        } catch (Exception $e) {
            $this->sendErrorResponse('Error al obtener agentes: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Obtener perfil público de un agente
     */
    public function agentProfile($id) {
        try {
            if (!$id) {
                $this->sendErrorResponse('ID de agente requerido', 400);
                return;
            }
            
            $agent = $this->userModel->getAgentPublicProfile($id);
            
            if (!$agent) {
                $this->sendErrorResponse('Agente no encontrado', 404);
                return;
            }
            
            // Obtener propiedades del agente
            $properties = $this->propertyModel->getPropertiesByAgent($id, ['activa']);
            
            // Obtener estadísticas del agente
            $stats = $this->userModel->getAgentStats($id);
            
            $agent['properties'] = $properties;
            $agent['stats'] = $stats;
            
            $response = [
                'success' => true,
                'data' => $agent
            ];
            
            echo json_encode($response);
            
        } catch (Exception $e) {
            $this->sendErrorResponse('Error al obtener perfil del agente: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Enviar respuesta de error
     */
    private function sendErrorResponse($message, $code = 400) {
        http_response_code($code);
        echo json_encode([
            'success' => false,
            'error' => $message,
            'code' => $code
        ]);
    }
    
    /**
     * Validar autenticación API (para futuras implementaciones)
     */
    private function validateApiAuth() {
        // Aquí se implementaría la validación de API keys o tokens
        // Por ahora retornamos true
        return true;
    }
} 