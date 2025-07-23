<?php
/**
 * Modelo Favorite - Gestión de Propiedades Favoritas
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este modelo maneja todas las operaciones relacionadas con propiedades favoritas:
 * agregar, eliminar, listar favoritos de usuarios autenticados.
 */

require_once APP_PATH . '/core/Database.php';

class Favorite {
    private $db;
    private $table = 'favoritos_propiedades';
    private $view = 'vista_favoritos_usuario';
    
    /**
     * Constructor del modelo Favorite
     */
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Agregar una propiedad a favoritos
     * 
     * @param int $usuarioId ID del usuario
     * @param int $propiedadId ID de la propiedad
     * @return array Resultado de la operación
     */
    public function agregarFavorito($usuarioId, $propiedadId) {
        // Validar datos de entrada
        if (empty($usuarioId) || empty($propiedadId)) {
            return [
                'success' => false,
                'message' => 'Usuario y propiedad son requeridos.'
            ];
        }
        
        // Verificar si la propiedad existe y está activa
        $propiedadQuery = "SELECT id, titulo FROM propiedades WHERE id = ? AND estado_publicacion = 'activa'";
        $propiedad = $this->db->selectOne($propiedadQuery, [$propiedadId]);
        
        if (!$propiedad) {
            return [
                'success' => false,
                'message' => 'La propiedad no existe o no está disponible.'
            ];
        }
        
        // Verificar si ya está en favoritos
        $existeQuery = "SELECT id FROM {$this->table} WHERE usuario_id = ? AND propiedad_id = ?";
        $existe = $this->db->selectOne($existeQuery, [$usuarioId, $propiedadId]);
        
        if ($existe) {
            return [
                'success' => false,
                'message' => 'Esta propiedad ya está en tus favoritos.'
            ];
        }
        
        // Agregar a favoritos
        $query = "INSERT INTO {$this->table} (usuario_id, propiedad_id, fecha_agregado) VALUES (?, ?, NOW())";
        $favoritoId = $this->db->insert($query, [$usuarioId, $propiedadId]);
        
        if ($favoritoId) {
            return [
                'success' => true,
                'message' => 'Propiedad agregada a favoritos exitosamente.',
                'favorito_id' => $favoritoId,
                'propiedad' => $propiedad
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al agregar a favoritos. Inténtalo de nuevo.'
            ];
        }
    }
    
    /**
     * Eliminar una propiedad de favoritos
     * 
     * @param int $usuarioId ID del usuario
     * @param int $propiedadId ID de la propiedad
     * @return array Resultado de la operación
     */
    public function eliminarFavorito($usuarioId, $propiedadId) {
        // Validar datos de entrada
        if (empty($usuarioId) || empty($propiedadId)) {
            return [
                'success' => false,
                'message' => 'Usuario y propiedad son requeridos.'
            ];
        }
        
        // Verificar si existe en favoritos
        $existeQuery = "SELECT id FROM {$this->table} WHERE usuario_id = ? AND propiedad_id = ?";
        $existe = $this->db->selectOne($existeQuery, [$usuarioId, $propiedadId]);
        
        if (!$existe) {
            return [
                'success' => false,
                'message' => 'Esta propiedad no está en tus favoritos.'
            ];
        }
        
        // Eliminar de favoritos
        $query = "DELETE FROM {$this->table} WHERE usuario_id = ? AND propiedad_id = ?";
        $resultado = $this->db->delete($query, [$usuarioId, $propiedadId]);
        
        if ($resultado) {
            return [
                'success' => true,
                'message' => 'Propiedad eliminada de favoritos exitosamente.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al eliminar de favoritos. Inténtalo de nuevo.'
            ];
        }
    }
    
    /**
     * Obtener todos los favoritos de un usuario
     * 
     * @param int $usuarioId ID del usuario
     * @param int $limit Límite de resultados
     * @param int $offset Offset para paginación
     * @return array Lista de favoritos
     */
    public function getFavoritosUsuario($usuarioId, $limit = 12, $offset = 0) {
        // Validar datos de entrada
        if (empty($usuarioId)) {
            return [];
        }
        
        $query = "SELECT * FROM {$this->view} WHERE usuario_id = ? LIMIT ? OFFSET ?";
        return $this->db->select($query, [$usuarioId, $limit, $offset]);
    }
    
    /**
     * Obtener el total de favoritos de un usuario
     * 
     * @param int $usuarioId ID del usuario
     * @return int Total de favoritos
     */
    public function getTotalFavoritosUsuario($usuarioId) {
        if (empty($usuarioId)) {
            return 0;
        }
        
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE usuario_id = ?";
        $resultado = $this->db->selectOne($query, [$usuarioId]);
        
        return $resultado ? (int)$resultado['total'] : 0;
    }
    
    /**
     * Verificar si una propiedad está en favoritos
     * 
     * @param int $usuarioId ID del usuario
     * @param int $propiedadId ID de la propiedad
     * @return bool True si está en favoritos, false en caso contrario
     */
    public function esFavorito($usuarioId, $propiedadId) {
        if (empty($usuarioId) || empty($propiedadId)) {
            return false;
        }
        
        $query = "SELECT id FROM {$this->table} WHERE usuario_id = ? AND propiedad_id = ?";
        $resultado = $this->db->selectOne($query, [$usuarioId, $propiedadId]);
        
        return $resultado !== false;
    }
    
    /**
     * Obtener propiedades favoritas con información completa
     * 
     * @param int $usuarioId ID del usuario
     * @param int $limit Límite de resultados
     * @param int $offset Offset para paginación
     * @return array Lista de propiedades favoritas con información completa
     */
    public function getFavoritosCompletos($usuarioId, $limit = 12, $offset = 0) {
        if (empty($usuarioId)) {
            return [];
        }
        
        $query = "SELECT 
                    f.id as favorito_id,
                    f.fecha_agregado,
                    p.*,
                    u.nombre as agente_nombre,
                    u.apellido as agente_apellido,
                    u.telefono as agente_telefono,
                    (SELECT ruta FROM imagenes_propiedades 
                     WHERE propiedad_id = p.id AND es_principal = 1 
                     LIMIT 1) as imagen_principal,
                    (SELECT COUNT(*) FROM imagenes_propiedades WHERE propiedad_id = p.id) as total_imagenes
                  FROM {$this->table} f
                  INNER JOIN propiedades p ON f.propiedad_id = p.id
                  LEFT JOIN usuarios u ON p.agente_id = u.id
                  WHERE f.usuario_id = ? AND p.estado_publicacion = 'activa'
                  ORDER BY f.fecha_agregado DESC
                  LIMIT ? OFFSET ?";
        
        return $this->db->select($query, [$usuarioId, $limit, $offset]);
    }
    
    /**
     * Eliminar favorito por ID del favorito
     * 
     * @param int $usuarioId ID del usuario
     * @param int $favoritoId ID del favorito
     * @return array Resultado de la operación
     */
    public function eliminarFavoritoPorId($usuarioId, $favoritoId) {
        if (empty($usuarioId) || empty($favoritoId)) {
            return [
                'success' => false,
                'message' => 'Usuario y favorito son requeridos.'
            ];
        }
        
        // Verificar que el favorito pertenece al usuario
        $existeQuery = "SELECT id FROM {$this->table} WHERE id = ? AND usuario_id = ?";
        $existe = $this->db->selectOne($existeQuery, [$favoritoId, $usuarioId]);
        
        if (!$existe) {
            return [
                'success' => false,
                'message' => 'Favorito no encontrado o no tienes permisos para eliminarlo.'
            ];
        }
        
        // Eliminar favorito
        $query = "DELETE FROM {$this->table} WHERE id = ? AND usuario_id = ?";
        $resultado = $this->db->delete($query, [$favoritoId, $usuarioId]);
        
        if ($resultado) {
            return [
                'success' => true,
                'message' => 'Favorito eliminado exitosamente.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al eliminar el favorito. Inténtalo de nuevo.'
            ];
        }
    }
    
    /**
     * Obtener estadísticas de favoritos
     * 
     * @param int $usuarioId ID del usuario (opcional)
     * @return array Estadísticas de favoritos
     */
    public function getEstadisticas($usuarioId = null) {
        $stats = [];
        
        if ($usuarioId) {
            // Estadísticas del usuario específico
            $query = "SELECT 
                        COUNT(*) as total_favoritos,
                        COUNT(DISTINCT p.ciudad) as ciudades_diferentes,
                        COUNT(DISTINCT p.tipo) as tipos_diferentes,
                        AVG(p.precio) as precio_promedio
                      FROM {$this->table} f
                      INNER JOIN propiedades p ON f.propiedad_id = p.id
                      WHERE f.usuario_id = ? AND p.estado_publicacion = 'activa'";
            
            $resultado = $this->db->selectOne($query, [$usuarioId]);
            $stats = $resultado ?: [
                'total_favoritos' => 0,
                'ciudades_diferentes' => 0,
                'tipos_diferentes' => 0,
                'precio_promedio' => 0
            ];
        } else {
            // Estadísticas generales
            $query = "SELECT 
                        COUNT(*) as total_favoritos,
                        COUNT(DISTINCT usuario_id) as usuarios_con_favoritos,
                        COUNT(DISTINCT propiedad_id) as propiedades_favoritas
                      FROM {$this->table}";
            
            $resultado = $this->db->selectOne($query);
            $stats = $resultado ?: [
                'total_favoritos' => 0,
                'usuarios_con_favoritos' => 0,
                'propiedades_favoritas' => 0
            ];
        }
        
        return $stats;
    }
    
    /**
     * Limpiar todos los favoritos de un usuario
     * 
     * @param int $usuarioId ID del usuario
     * @return array Resultado de la operación
     */
    public function limpiarTodosLosFavoritos($usuarioId) {
        if (empty($usuarioId)) {
            return [
                'success' => false,
                'message' => 'ID de usuario requerido.'
            ];
        }
        
        // Verificar si el usuario tiene favoritos
        $totalFavoritos = $this->getTotalFavoritosUsuario($usuarioId);
        
        if ($totalFavoritos === 0) {
            return [
                'success' => false,
                'message' => 'No tienes propiedades favoritas para eliminar.'
            ];
        }
        
        // Eliminar todos los favoritos del usuario
        $query = "DELETE FROM {$this->table} WHERE usuario_id = ?";
        $resultado = $this->db->delete($query, [$usuarioId]);
        
        if ($resultado) {
            return [
                'success' => true,
                'message' => "Se eliminaron {$totalFavoritos} favoritos exitosamente."
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al eliminar los favoritos. Inténtalo de nuevo.'
            ];
        }
    }
    
    /**
     * Obtener las propiedades más favoritas entre todos los usuarios
     * 
     * @param int $limit Límite de propiedades a retornar
     * @return array Lista de propiedades más favoritas
     */
    public function getPropiedadesMasFavoritas($limit = 6) {
        // Primero intentar obtener propiedades con favoritos
        $query = "SELECT 
                    p.*,
                    u.nombre as agente_nombre,
                    u.apellido as agente_apellido,
                    u.telefono as agente_telefono,
                    COUNT(f.id) as total_favoritos,
                    (SELECT ruta FROM imagenes_propiedades 
                     WHERE propiedad_id = p.id AND es_principal = 1 
                     LIMIT 1) as imagen_principal,
                    (SELECT COUNT(*) FROM imagenes_propiedades WHERE propiedad_id = p.id) as total_imagenes
                  FROM propiedades p
                  LEFT JOIN {$this->table} f ON p.id = f.propiedad_id
                  LEFT JOIN usuarios u ON p.agente_id = u.id
                  WHERE p.estado_publicacion = 'activa'
                  GROUP BY p.id
                  HAVING total_favoritos > 0
                  ORDER BY total_favoritos DESC, p.fecha_creacion DESC
                  LIMIT ?";
        
        $propiedadesConFavoritos = $this->db->select($query, [$limit]);
        
        // Si no hay suficientes propiedades con favoritos, completar con las más recientes
        if (count($propiedadesConFavoritos) < $limit) {
            $propiedadesRestantes = $limit - count($propiedadesConFavoritos);
            
            // Obtener IDs de propiedades que ya tenemos
            $idsExistentes = array_column($propiedadesConFavoritos, 'id');
            $idsExistentes = !empty($idsExistentes) ? implode(',', $idsExistentes) : '0';
            
            // Obtener propiedades recientes que no estén en la lista
            $queryRecientes = "SELECT 
                                p.*,
                                u.nombre as agente_nombre,
                                u.apellido as agente_apellido,
                                u.telefono as agente_telefono,
                                0 as total_favoritos,
                                (SELECT ruta FROM imagenes_propiedades 
                                 WHERE propiedad_id = p.id AND es_principal = 1 
                                 LIMIT 1) as imagen_principal,
                                (SELECT COUNT(*) FROM imagenes_propiedades WHERE propiedad_id = p.id) as total_imagenes
                              FROM propiedades p
                              LEFT JOIN usuarios u ON p.agente_id = u.id
                              WHERE p.estado_publicacion = 'activa'
                              AND p.id NOT IN ({$idsExistentes})
                              ORDER BY p.fecha_creacion DESC
                              LIMIT ?";
            
            $propiedadesRecientes = $this->db->select($queryRecientes, [$propiedadesRestantes]);
            
            // Combinar ambos arrays
            return array_merge($propiedadesConFavoritos, $propiedadesRecientes);
        }
        
        return $propiedadesConFavoritos;
    }
    
    /**
     * Obtener conteo de favoritos de un usuario (alias para compatibilidad)
     * 
     * @param int $usuarioId ID del usuario
     * @return int Total de favoritos
     */
    public function getCountByUser($usuarioId) {
        return $this->getTotalFavoritosUsuario($usuarioId);
    }
} 