<?php

/**
 * Modelo para gestionar alertas eliminadas del sistema
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 */

class AlertManager {
    private $db;
    private $table = 'alertas_eliminadas';

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Marcar una alerta como eliminada
     * 
     * @param int $adminId ID del administrador que eliminó la alerta
     * @param string $tipoAlerta Tipo de alerta (ej: 'reportes_nuevos', 'propiedades_pendientes', etc.)
     * @param string $tituloAlerta Título de la alerta
     * @return bool True si se guardó correctamente
     */
    public function marcarAlertaEliminada($adminId, $tipoAlerta, $tituloAlerta) {
        try {
            $query = "INSERT INTO {$this->table} (admin_id, tipo_alerta, titulo_alerta) VALUES (?, ?, ?)";
            $result = $this->db->insert($query, [$adminId, $tipoAlerta, $tituloAlerta]);
            return $result > 0;
        } catch (Exception $e) {
            error_log("Error marcando alerta como eliminada: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si una alerta específica ha sido eliminada
     * 
     * @param string $tipoAlerta Tipo de alerta
     * @param string $tituloAlerta Título de la alerta
     * @return bool True si la alerta ha sido eliminada
     */
    public function alertaEliminada($tipoAlerta, $tituloAlerta) {
        try {
            $query = "SELECT COUNT(*) as total FROM {$this->table} 
                      WHERE tipo_alerta = ? AND titulo_alerta = ? 
                      AND fecha_eliminacion >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
            $result = $this->db->selectOne($query, [$tipoAlerta, $tituloAlerta]);
            return $result && $result['total'] > 0;
        } catch (Exception $e) {
            error_log("Error verificando alerta eliminada: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener todas las alertas eliminadas por un administrador
     * 
     * @param int $adminId ID del administrador
     * @return array Lista de alertas eliminadas
     */
    public function getAlertasEliminadasPorAdmin($adminId) {
        try {
            $query = "SELECT * FROM {$this->table} 
                      WHERE admin_id = ? 
                      ORDER BY fecha_eliminacion DESC";
            return $this->db->select($query, [$adminId]);
        } catch (Exception $e) {
            error_log("Error obteniendo alertas eliminadas: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Limpiar alertas eliminadas antiguas (más de 24 horas)
     * 
     * @return bool True si se limpiaron correctamente
     */
    public function limpiarAlertasAntiguas() {
        try {
            $query = "DELETE FROM {$this->table} 
                      WHERE fecha_eliminacion < DATE_SUB(NOW(), INTERVAL 24 HOUR)";
            $result = $this->db->delete($query);
            return $result > 0;
        } catch (Exception $e) {
            error_log("Error limpiando alertas antiguas: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Restaurar todas las alertas eliminadas (para testing)
     * 
     * @return bool True si se restauraron correctamente
     */
    public function restaurarTodasLasAlertas() {
        try {
            $query = "DELETE FROM {$this->table}";
            $result = $this->db->delete($query);
            return $result > 0;
        } catch (Exception $e) {
            error_log("Error restaurando alertas: " . $e->getMessage());
            return false;
        }
    }
} 