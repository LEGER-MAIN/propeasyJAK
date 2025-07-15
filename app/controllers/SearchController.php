<?php
/**
 * Controlador Search - Búsqueda de Usuarios
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este controlador maneja las búsquedas de agentes y clientes
 * según el rol del usuario autenticado.
 */

class SearchController {
    private $userModel;
    
    /**
     * Constructor del controlador
     */
    public function __construct() {
        // Verificar autenticación
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        // Incluir modelo
        require_once APP_PATH . '/models/User.php';
        
        $this->userModel = new User();
    }
    
    /**
     * Mostrar página de búsqueda de agentes (para clientes)
     */
    public function buscarAgentes() {
        // Verificar que el usuario sea cliente
        if ($_SESSION['user_rol'] !== 'cliente') {
            http_response_code(403);
            $pageTitle = 'Acceso Denegado - ' . APP_NAME;
            include APP_PATH . '/views/errors/403.php';
            return;
        }
        
        $nombre = trim($_GET['nombre'] ?? '');
        $ciudad = trim($_GET['ciudad'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        // Realizar búsqueda
        $agentes = $this->userModel->buscarAgentes($nombre, $ciudad, $limit, $offset);
        $total = $this->userModel->getEstadisticasBusqueda('agentes', $nombre, $ciudad);
        $totalPages = ceil($total / $limit);
        
        // Configurar página
        $pageTitle = 'Buscar Agentes - ' . APP_NAME;
        $pageDescription = 'Encuentra agentes inmobiliarios por nombre y ciudad';
        
        // Incluir vista
        include APP_PATH . '/views/search/agentes.php';
    }
    
    /**
     * Mostrar página de búsqueda de clientes (para agentes)
     */
    public function buscarClientes() {
        // Verificar que el usuario sea agente
        if ($_SESSION['user_rol'] !== 'agente') {
            http_response_code(403);
            $pageTitle = 'Acceso Denegado - ' . APP_NAME;
            include APP_PATH . '/views/errors/403.php';
            return;
        }
        
        $nombre = trim($_GET['nombre'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        // Realizar búsqueda
        $clientes = $this->userModel->buscarClientes($nombre, $limit, $offset);
        $total = $this->userModel->getEstadisticasBusqueda('clientes', $nombre);
        $totalPages = ceil($total / $limit);
        
        // Configurar página
        $pageTitle = 'Buscar Clientes - ' . APP_NAME;
        $pageDescription = 'Encuentra clientes por nombre';
        
        // Incluir vista
        include APP_PATH . '/views/search/clientes.php';
    }
    
    /**
     * API para búsqueda de agentes (AJAX)
     */
    public function apiBuscarAgentes() {
        // Verificar que el usuario sea cliente
        if ($_SESSION['user_rol'] !== 'cliente') {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            return;
        }
        
        $nombre = trim($_GET['nombre'] ?? '');
        $ciudad = trim($_GET['ciudad'] ?? '');
        $limit = min(50, max(1, (int)($_GET['limit'] ?? 20)));
        $offset = max(0, (int)($_GET['offset'] ?? 0));
        
        $agentes = $this->userModel->buscarAgentes($nombre, $ciudad, $limit, $offset);
        $total = $this->userModel->getEstadisticasBusqueda('agentes', $nombre, $ciudad);
        
        echo json_encode([
            'success' => true,
            'agentes' => $agentes,
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset
        ]);
    }
    
    /**
     * API para búsqueda de clientes (AJAX)
     */
    public function apiBuscarClientes() {
        // Verificar que el usuario sea agente
        if ($_SESSION['user_rol'] !== 'agente') {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            return;
        }
        
        $nombre = trim($_GET['nombre'] ?? '');
        $limit = min(50, max(1, (int)($_GET['limit'] ?? 20)));
        $offset = max(0, (int)($_GET['offset'] ?? 0));
        
        $clientes = $this->userModel->buscarClientes($nombre, $limit, $offset);
        $total = $this->userModel->getEstadisticasBusqueda('clientes', $nombre);
        
        echo json_encode([
            'success' => true,
            'clientes' => $clientes,
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset
        ]);
    }
    
    /**
     * Obtener ciudades disponibles para filtro
     */
    public function getCiudades() {
        $query = "SELECT DISTINCT ciudad FROM usuarios WHERE rol = 'agente' AND estado = 'activo' AND ciudad IS NOT NULL AND ciudad != '' ORDER BY ciudad";
        $ciudades = $this->userModel->db->select($query);
        
        $ciudadesList = array_map(function($ciudad) {
            return $ciudad['ciudad'];
        }, $ciudades);
        
        echo json_encode([
            'success' => true,
            'ciudades' => $ciudadesList
        ]);
    }
    
    /**
     * Obtener solicitudes de un cliente específico
     */
    public function getSolicitudesCliente($clienteId) {
        // Verificar que el usuario sea agente
        if ($_SESSION['user_rol'] !== 'agente') {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            return;
        }
        
        $query = "SELECT 
                    sc.id,
                    sc.estado,
                    sc.fecha_solicitud,
                    p.titulo as titulo_propiedad,
                    p.precio
                  FROM solicitudes_compra sc
                  LEFT JOIN propiedades p ON sc.propiedad_id = p.id
                  WHERE sc.cliente_id = ?
                  ORDER BY sc.fecha_solicitud DESC";
        
        $solicitudes = $this->userModel->db->select($query, [$clienteId]);
        
        echo json_encode([
            'success' => true,
            'solicitudes' => $solicitudes
        ]);
    }
} 