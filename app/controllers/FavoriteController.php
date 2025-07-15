<?php
/**
 * Controlador Favorite - Gestión de Favoritos de Propiedades
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este controlador maneja todas las operaciones relacionadas con los favoritos
 * de propiedades por parte de los usuarios autenticados.
 */

require_once APP_PATH . '/models/Favorite.php';
require_once APP_PATH . '/models/Property.php';

class FavoriteController {
    private $favoriteModel;
    private $propertyModel;
    
    public function __construct() {
        $this->favoriteModel = new Favorite();
        $this->propertyModel = new Property();
    }
    
    /**
     * Mostrar página de favoritos del usuario
     */
    public function index() {
        // Verificar autenticación
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        $usuarioId = $_SESSION['user_id'];
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;
        
        // Obtener favoritos
        $favoritos = $this->favoriteModel->getFavoritosCompletos($usuarioId, $limit, $offset);
        $totalFavoritos = $this->favoriteModel->getTotalFavoritos($usuarioId);
        $totalPages = ceil($totalFavoritos / $limit);
        
        // Renderizar vista
        $pageTitle = 'Mis Favoritos - ' . APP_NAME;
        include APP_PATH . '/views/favorites/index.php';
    }
    
    /**
     * API: Agregar propiedad a favoritos
     */
    public function add() {
        header('Content-Type: application/json');
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Debes iniciar sesión']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }
        
        $usuarioId = $_SESSION['user_id'];
        
        // Obtener datos del JSON
        $input = json_decode(file_get_contents('php://input'), true);
        $propiedadId = $input['property_id'] ?? null;
        
        if (!$propiedadId) {
            http_response_code(400);
            echo json_encode(['error' => 'ID de propiedad requerido']);
            return;
        }
        
        // Verificar que la propiedad existe y está activa
        $propiedad = $this->propertyModel->getById($propiedadId);
        if (!$propiedad || $propiedad['estado_publicacion'] !== 'activa') {
            http_response_code(404);
            echo json_encode(['error' => 'Propiedad no encontrada']);
            return;
        }
        
        try {
            $resultado = $this->favoriteModel->agregarFavorito($usuarioId, $propiedadId);
            
            if ($resultado) {
                $totalFavoritos = $this->favoriteModel->getTotalFavoritosPropiedad($propiedadId);
                echo json_encode([
                    'success' => true,
                    'message' => 'Propiedad agregada a favoritos',
                    'count' => $totalFavoritos
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al agregar a favoritos']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }
    
    /**
     * API: Remover propiedad de favoritos
     */
    public function remove() {
        header('Content-Type: application/json');
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Debes iniciar sesión']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }
        
        $usuarioId = $_SESSION['user_id'];
        
        // Obtener datos del JSON
        $input = json_decode(file_get_contents('php://input'), true);
        $propiedadId = $input['property_id'] ?? null;
        
        if (!$propiedadId) {
            http_response_code(400);
            echo json_encode(['error' => 'ID de propiedad requerido']);
            return;
        }
        
        try {
            $resultado = $this->favoriteModel->removerFavorito($usuarioId, $propiedadId);
            
            if ($resultado) {
                $totalFavoritos = $this->favoriteModel->getTotalFavoritosPropiedad($propiedadId);
                echo json_encode([
                    'success' => true,
                    'message' => 'Propiedad removida de favoritos',
                    'count' => $totalFavoritos
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al remover de favoritos']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }
    
    /**
     * API: Verificar si una propiedad está en favoritos
     */
    public function verify($id) {
        header('Content-Type: application/json');
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => true, 'is_favorite' => false]);
            return;
        }
        
        $usuarioId = $_SESSION['user_id'];
        $propiedadId = $id;
        
        if (!$propiedadId) {
            http_response_code(400);
            echo json_encode(['error' => 'ID de propiedad requerido']);
            return;
        }
        
        try {
            $esFavorito = $this->favoriteModel->esFavorito($usuarioId, $propiedadId);
            
            echo json_encode([
                'success' => true,
                'is_favorite' => $esFavorito
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }
    
    /**
     * API: Obtener total de favoritos del usuario
     */
    public function total() {
        header('Content-Type: application/json');
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['total' => 0]);
            return;
        }
        
        $usuarioId = $_SESSION['user_id'];
        
        try {
            $total = $this->favoriteModel->getTotalFavoritos($usuarioId);
            echo json_encode(['total' => $total]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }
    
    /**
     * API: Listar favoritos del usuario (para JavaScript)
     */
    public function list() {
        header('Content-Type: application/json');
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => true, 'favorites' => []]);
            return;
        }
        
        $usuarioId = $_SESSION['user_id'];
        
        try {
            $favoritos = $this->favoriteModel->getFavoritos($usuarioId);
            echo json_encode(['success' => true, 'favorites' => $favoritos]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }
    
    /**
     * API: Contar favoritos de una propiedad específica
     */
    public function count($id) {
        header('Content-Type: application/json');
        
        $propiedadId = $id;
        
        if (!$propiedadId) {
            http_response_code(400);
            echo json_encode(['error' => 'ID de propiedad requerido']);
            return;
        }
        
        try {
            $total = $this->favoriteModel->getTotalFavoritosPropiedad($propiedadId);
            echo json_encode([
                'success' => true,
                'count' => $total
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }
    
    /**
     * Limpiar todos los favoritos del usuario
     */
    public function clear() {
        header('Content-Type: application/json');
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Debes iniciar sesión']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }
        
        $usuarioId = $_SESSION['user_id'];
        
        try {
            $resultado = $this->favoriteModel->limpiarFavoritos($usuarioId);
            
            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Todos los favoritos han sido eliminados'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al limpiar favoritos']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }
} 