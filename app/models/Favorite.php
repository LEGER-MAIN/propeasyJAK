<?php
/**
 * Modelo Favorite - Gestión de Favoritos de Propiedades
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este modelo maneja todas las operaciones relacionadas con los favoritos
 * de propiedades por parte de los usuarios.
 */

require_once APP_PATH . '/core/Database.php';

class Favorite {
    private $db;
    
    /**
     * Constructor del modelo
     */
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Agregar una propiedad a favoritos
     * 
     * @param int $usuarioId ID del usuario
     * @param int $propiedadId ID de la propiedad
     * @return bool True si se agregó correctamente
     */
    public function agregarFavorito($usuarioId, $propiedadId) {
        $sql = "INSERT IGNORE INTO favoritos_propiedades (usuario_id, propiedad_id) VALUES (?, ?)";
        return $this->db->insert($sql, [$usuarioId, $propiedadId]) !== false;
    }
    
    /**
     * Remover una propiedad de favoritos
     * 
     * @param int $usuarioId ID del usuario
     * @param int $propiedadId ID de la propiedad
     * @return bool True si se removió correctamente
     */
    public function removerFavorito($usuarioId, $propiedadId) {
        $sql = "DELETE FROM favoritos_propiedades WHERE usuario_id = ? AND propiedad_id = ?";
        return $this->db->delete($sql, [$usuarioId, $propiedadId]) !== false;
    }
    
    /**
     * Verificar si una propiedad está en favoritos
     * 
     * @param int $usuarioId ID del usuario
     * @param int $propiedadId ID de la propiedad
     * @return bool True si está en favoritos
     */
    public function esFavorito($usuarioId, $propiedadId) {
        $sql = "SELECT COUNT(*) as count FROM favoritos_propiedades WHERE usuario_id = ? AND propiedad_id = ?";
        $result = $this->db->selectOne($sql, [$usuarioId, $propiedadId]);
        return $result && $result['count'] > 0;
    }
    
    /**
     * Obtener todas las propiedades favoritas de un usuario
     * 
     * @param int $usuarioId ID del usuario
     * @param int $limit Límite de resultados
     * @param int $offset Offset para paginación
     * @return array Array de propiedades favoritas
     */
    public function getFavoritos($usuarioId, $limit = 20, $offset = 0) {
        $sql = "SELECT 
                    p.*,
                    u.nombre as nombre_agente,
                    u.apellido as apellido_agente,
                    u.telefono as telefono_agente,
                    u.email as email_agente,
                    fp.fecha_creacion as fecha_favorito
                FROM favoritos_propiedades fp
                INNER JOIN propiedades p ON fp.propiedad_id = p.id
                INNER JOIN usuarios u ON p.usuario_id = u.id
                WHERE fp.usuario_id = ?
                ORDER BY fp.fecha_creacion DESC
                LIMIT ? OFFSET ?";
        
        return $this->db->select($sql, [$usuarioId, $limit, $offset]);
    }
    
    /**
     * Obtener el total de favoritos de un usuario
     * 
     * @param int $usuarioId ID del usuario
     * @return int Total de favoritos
     */
    public function getTotalFavoritos($usuarioId) {
        $sql = "SELECT COUNT(*) as total FROM favoritos_propiedades WHERE usuario_id = ?";
        $result = $this->db->selectOne($sql, [$usuarioId]);
        return $result ? $result['total'] : 0;
    }
    
    /**
     * Obtener el total de favoritos de una propiedad
     * 
     * @param int $propiedadId ID de la propiedad
     * @return int Total de favoritos
     */
    public function getTotalFavoritosPropiedad($propiedadId) {
        $sql = "SELECT COUNT(*) as total FROM favoritos_propiedades WHERE propiedad_id = ?";
        $result = $this->db->selectOne($sql, [$propiedadId]);
        return $result ? $result['total'] : 0;
    }
    
    /**
     * Obtener propiedades favoritas con información completa
     * 
     * @param int $usuarioId ID del usuario
     * @param int $limit Límite de resultados
     * @param int $offset Offset para paginación
     * @return array Array de propiedades favoritas con información completa
     */
    public function getFavoritosCompletos($usuarioId, $limit = 20, $offset = 0) {
        $sql = "SELECT 
                    p.*,
                    u.nombre as nombre_agente,
                    u.apellido as apellido_agente,
                    u.telefono as telefono_agente,
                    u.email as email_agente,
                    fp.fecha_creacion as fecha_favorito,
                    (SELECT COUNT(*) FROM favoritos_propiedades WHERE propiedad_id = p.id) as total_favoritos
                FROM favoritos_propiedades fp
                INNER JOIN propiedades p ON fp.propiedad_id = p.id
                INNER JOIN usuarios u ON p.usuario_id = u.id
                WHERE fp.usuario_id = ? AND p.estado_publicacion = 'activa'
                ORDER BY fp.fecha_creacion DESC
                LIMIT ? OFFSET ?";
        
        return $this->db->select($sql, [$usuarioId, $limit, $offset]);
    }
    
    /**
     * Limpiar favoritos de un usuario (eliminar todos)
     * 
     * @param int $usuarioId ID del usuario
     * @return bool True si se limpiaron correctamente
     */
    public function limpiarFavoritos($usuarioId) {
        $sql = "DELETE FROM favoritos_propiedades WHERE usuario_id = ?";
        return $this->db->delete($sql, [$usuarioId]) !== false;
    }
} 