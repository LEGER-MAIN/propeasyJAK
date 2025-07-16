<?php
/**
 * Controlador FavoriteController - Gestión de Propiedades Favoritas
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este controlador maneja todas las operaciones relacionadas con propiedades favoritas:
 * agregar, eliminar, listar favoritos de usuarios autenticados.
 */

require_once APP_PATH . '/models/Favorite.php';
require_once APP_PATH . '/models/Property.php';

class FavoriteController {
    private $favoriteModel;
    private $propertyModel;
    
    /**
     * Constructor del controlador
     */
    public function __construct() {
        $this->favoriteModel = new Favorite();
        $this->propertyModel = new Property();
    }
    
    /**
     * Middleware para verificar autenticación
     * 
     * @return bool True si está autenticado, false en caso contrario
     */
    private function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            if ($this->isAjaxRequest()) {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => 'Debes iniciar sesión para realizar esta acción.'
                ]);
            } else {
                header('Location: /login');
            }
            return false;
        }
        return true;
    }
    
    /**
     * Verificar si es una petición AJAX
     * 
     * @return bool True si es AJAX, false en caso contrario
     */
    private function isAjaxRequest() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
    
    /**
     * Mostrar página principal de favoritos
     */
    public function index() {
        if (!$this->requireAuth()) {
            return;
        }
        
        $usuarioId = $_SESSION['user_id'];
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;
        
        // Obtener favoritos del usuario
        $favoritos = $this->favoriteModel->getFavoritosCompletos($usuarioId, $limit, $offset);
        $totalFavoritos = $this->favoriteModel->getTotalFavoritosUsuario($usuarioId);
        $totalPages = ceil($totalFavoritos / $limit);
        
        // Obtener estadísticas
        $estadisticas = $this->favoriteModel->getEstadisticas($usuarioId);
        
        // Renderizar vista
        $data = [
            'favoritos' => $favoritos,
            'totalFavoritos' => $totalFavoritos,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'estadisticas' => $estadisticas,
            'title' => 'Mis Propiedades Favoritas'
        ];
        
        // Establecer variables para la vista
        $pageTitle = $data['title'];
        
        // Capturar el contenido de la vista
        ob_start();
        include APP_PATH . '/views/favorites/index.php';
        $content = ob_get_clean();
        
        // Incluir el layout principal
        include APP_PATH . '/views/layouts/main.php';
    }
    
    /**
     * API: Agregar propiedad a favoritos (AJAX)
     */
    public function agregar() {
        if (!$this->requireAuth()) {
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Método no permitido.'
            ]);
            return;
        }
        
        $usuarioId = $_SESSION['user_id'];
        $propiedadId = isset($_POST['propiedad_id']) ? intval($_POST['propiedad_id']) : 0;
        
        if (empty($propiedadId)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'ID de propiedad requerido.'
            ]);
            return;
        }
        
        $resultado = $this->favoriteModel->agregarFavorito($usuarioId, $propiedadId);
        
        // Actualizar contador de favoritos
        $totalFavoritos = $this->favoriteModel->getTotalFavoritosUsuario($usuarioId);
        
        echo json_encode([
            'success' => $resultado['success'],
            'message' => $resultado['message'],
            'total_favoritos' => $totalFavoritos,
            'is_favorite' => $resultado['success']
        ]);
    }
    
    /**
     * API: Eliminar propiedad de favoritos (AJAX)
     */
    public function eliminar() {
        if (!$this->requireAuth()) {
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Método no permitido.'
            ]);
            return;
        }
        
        $usuarioId = $_SESSION['user_id'];
        $propiedadId = isset($_POST['propiedad_id']) ? intval($_POST['propiedad_id']) : 0;
        
        if (empty($propiedadId)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'ID de propiedad requerido.'
            ]);
            return;
        }
        
        $resultado = $this->favoriteModel->eliminarFavorito($usuarioId, $propiedadId);
        
        // Actualizar contador de favoritos
        $totalFavoritos = $this->favoriteModel->getTotalFavoritosUsuario($usuarioId);
        
        echo json_encode([
            'success' => $resultado['success'],
            'message' => $resultado['message'],
            'total_favoritos' => $totalFavoritos,
            'is_favorite' => false
        ]);
    }
    
    /**
     * API: Toggle favorito (agregar/eliminar)
     */
    public function toggle() {
        if (!$this->requireAuth()) {
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Método no permitido.'
            ]);
            return;
        }
        
        $usuarioId = $_SESSION['user_id'];
        $propiedadId = isset($_POST['propiedad_id']) ? intval($_POST['propiedad_id']) : 0;
        
        if (empty($propiedadId)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'ID de propiedad requerido.'
            ]);
            return;
        }
        
        // Verificar si ya está en favoritos
        $esFavorito = $this->favoriteModel->esFavorito($usuarioId, $propiedadId);
        
        if ($esFavorito) {
            // Eliminar de favoritos
            $resultado = $this->favoriteModel->eliminarFavorito($usuarioId, $propiedadId);
            $accion = 'eliminada';
        } else {
            // Agregar a favoritos
            $resultado = $this->favoriteModel->agregarFavorito($usuarioId, $propiedadId);
            $accion = 'agregada';
        }
        
        // Actualizar contador de favoritos
        $totalFavoritos = $this->favoriteModel->getTotalFavoritosUsuario($usuarioId);
        
        echo json_encode([
            'success' => $resultado['success'],
            'message' => $resultado['message'],
            'total_favoritos' => $totalFavoritos,
            'is_favorite' => !$esFavorito,
            'accion' => $accion
        ]);
    }
    
    /**
     * API: Eliminar favorito por ID (desde listado)
     */
    public function eliminarPorId() {
        if (!$this->requireAuth()) {
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Método no permitido.'
            ]);
            return;
        }
        
        $usuarioId = $_SESSION['user_id'];
        $favoritoId = isset($_POST['favorito_id']) ? intval($_POST['favorito_id']) : 0;
        
        if (empty($favoritoId)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'ID de favorito requerido.'
            ]);
            return;
        }
        
        $resultado = $this->favoriteModel->eliminarFavoritoPorId($usuarioId, $favoritoId);
        
        // Actualizar contador de favoritos
        $totalFavoritos = $this->favoriteModel->getTotalFavoritosUsuario($usuarioId);
        
        echo json_encode([
            'success' => $resultado['success'],
            'message' => $resultado['message'],
            'total_favoritos' => $totalFavoritos
        ]);
    }
    
    /**
     * API: Obtener contador de favoritos (para header)
     */
    public function contador() {
        if (!$this->requireAuth()) {
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Método no permitido.'
            ]);
            return;
        }
        
        $usuarioId = $_SESSION['user_id'];
        $totalFavoritos = $this->favoriteModel->getTotalFavoritosUsuario($usuarioId);
        
        echo json_encode([
            'success' => true,
            'total_favoritos' => $totalFavoritos
        ]);
    }
    
    /**
     * API: Verificar si una propiedad está en favoritos
     */
    public function verificar() {
        if (!$this->requireAuth()) {
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Método no permitido.'
            ]);
            return;
        }
        
        $usuarioId = $_SESSION['user_id'];
        $propiedadId = isset($_GET['propiedad_id']) ? intval($_GET['propiedad_id']) : 0;
        
        if (empty($propiedadId)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'ID de propiedad requerido.'
            ]);
            return;
        }
        
        $esFavorito = $this->favoriteModel->esFavorito($usuarioId, $propiedadId);
        
        echo json_encode([
            'success' => true,
            'is_favorite' => $esFavorito
        ]);
    }
    
    /**
     * API: Obtener estadísticas de favoritos
     */
    public function estadisticas() {
        if (!$this->requireAuth()) {
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Método no permitido.'
            ]);
            return;
        }
        
        $usuarioId = $_SESSION['user_id'];
        $estadisticas = $this->favoriteModel->getEstadisticas($usuarioId);
        
        echo json_encode([
            'success' => true,
            'estadisticas' => $estadisticas
        ]);
    }
    
    /**
     * API: Limpiar todos los favoritos del usuario
     */
    public function limpiarTodos() {
        if (!$this->requireAuth()) {
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Método no permitido.'
            ]);
            return;
        }
        
        $usuarioId = $_SESSION['user_id'];
        
        // Verificar si el usuario tiene favoritos
        $totalFavoritos = $this->favoriteModel->getTotalFavoritosUsuario($usuarioId);
        
        if ($totalFavoritos === 0) {
            echo json_encode([
                'success' => false,
                'message' => 'No tienes propiedades favoritas para eliminar.'
            ]);
            return;
        }
        
        // Limpiar todos los favoritos
        $resultado = $this->favoriteModel->limpiarTodosLosFavoritos($usuarioId);
        
        if ($resultado['success']) {
            echo json_encode([
                'success' => true,
                'message' => 'Todos los favoritos han sido eliminados exitosamente.',
                'total_favoritos' => 0
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => $resultado['message']
            ]);
        }
    }
} 