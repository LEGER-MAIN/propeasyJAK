<?php
/**
 * Modelo Chat - Gestión de Mensajes del Chat Interno
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este modelo maneja todas las operaciones relacionadas con el chat interno
 * entre clientes y agentes inmobiliarios.
 */

require_once APP_PATH . '/core/Database.php';

class Chat {
    private $db;
    
    /**
     * Constructor del modelo
     */
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Obtener mensajes de una solicitud específica
     * 
     * @param int $solicitudId ID de la solicitud
     * @param int $limit Límite de mensajes a obtener
     * @param int $offset Offset para paginación
     * @return array Array de mensajes
     */
    public function getMensajes($solicitudId, $limit = 50, $offset = 0) {
        $sql = "SELECT 
                    mc.id,
                    mc.solicitud_id,
                    mc.remitente_id,
                    mc.remitente_rol,
                    mc.mensaje,
                    mc.leido,
                    mc.fecha_envio,
                    u.nombre,
                    u.apellido,
                    u.email
                FROM mensajes_chat mc
                INNER JOIN usuarios u ON mc.remitente_id = u.id
                WHERE mc.solicitud_id = ?
                ORDER BY mc.fecha_envio ASC
                LIMIT ? OFFSET ?";
        
        return $this->db->select($sql, [$solicitudId, $limit, $offset]);
    }
    
    /**
     * Enviar un nuevo mensaje
     * 
     * @param int $solicitudId ID de la solicitud
     * @param int $remitenteId ID del remitente
     * @param string $remitenteRol Rol del remitente (cliente/agente)
     * @param string $mensaje Contenido del mensaje
     * @return int|false ID del mensaje insertado o false en error
     */
    public function enviarMensaje($solicitudId, $remitenteId, $remitenteRol, $mensaje) {
        $sql = "INSERT INTO mensajes_chat (solicitud_id, remitente_id, remitente_rol, mensaje) 
                VALUES (?, ?, ?, ?)";
        
        $result = $this->db->insert($sql, [$solicitudId, $remitenteId, $remitenteRol, $mensaje]);
        
        if ($result !== false) {
            return $this->getLastInsertId();
        }
        
        return false;
    }
    
    /**
     * Marcar mensajes como leídos
     * 
     * @param int $solicitudId ID de la solicitud
     * @param int $usuarioId ID del usuario que lee los mensajes
     * @param string $rolUsuario Rol del usuario
     * @return bool True si se actualizaron correctamente
     */
    public function marcarComoLeidos($solicitudId, $usuarioId, $rolUsuario) {
        $sql = "UPDATE mensajes_chat 
                SET leido = 1 
                WHERE solicitud_id = ? 
                AND remitente_id != ? 
                AND remitente_rol != ?";
        
        return $this->db->update($sql, [$solicitudId, $usuarioId, $rolUsuario]) !== false;
    }
    
    /**
     * Obtener conversaciones de un usuario
     * 
     * @param int $usuarioId ID del usuario
     * @param string $rol Rol del usuario (cliente/agente)
     * @return array Array de conversaciones
     */
    public function getConversaciones($usuarioId, $rol) {
        $sql = "SELECT DISTINCT
                    sc.id as solicitud_id,
                    sc.cliente_id,
                    sc.agente_id,
                    sc.propiedad_id,
                    sc.estado as estado_solicitud,
                    sc.fecha_solicitud,
                    COALESCE(p.titulo, 'Conversación General') as titulo_propiedad,
                    COALESCE(p.precio, 0) as precio,
                    COALESCE(p.ciudad, 'General') as ciudad,
                    COALESCE(p.sector, 'General') as sector,
                    CASE 
                        WHEN sc.cliente_id = ? THEN ag.nombre
                        ELSE cl.nombre
                    END as nombre_otro_usuario,
                    CASE 
                        WHEN sc.cliente_id = ? THEN ag.apellido
                        ELSE cl.apellido
                    END as apellido_otro_usuario,
                    CASE 
                        WHEN sc.cliente_id = ? THEN ag.email
                        ELSE cl.email
                    END as email_otro_usuario,
                    CASE 
                        WHEN sc.cliente_id = ? THEN ag.foto_perfil
                        ELSE cl.foto_perfil
                    END as foto_perfil_otro_usuario,
                    CASE 
                        WHEN sc.cliente_id = ? THEN 'agente'
                        ELSE 'cliente'
                    END as rol_otro_usuario,
                    (SELECT COUNT(*) FROM mensajes_chat mc 
                     WHERE mc.solicitud_id = sc.id AND mc.leido = 0 
                     AND mc.remitente_id != ?) as mensajes_no_leidos,
                    (SELECT mc.mensaje FROM mensajes_chat mc 
                     WHERE mc.solicitud_id = sc.id 
                     ORDER BY mc.fecha_envio DESC LIMIT 1) as ultimo_mensaje,
                    (SELECT mc.fecha_envio FROM mensajes_chat mc 
                     WHERE mc.solicitud_id = sc.id 
                     ORDER BY mc.fecha_envio DESC LIMIT 1) as fecha_ultimo_mensaje
                FROM solicitudes_compra sc
                LEFT JOIN propiedades p ON sc.propiedad_id = p.id
                INNER JOIN usuarios cl ON sc.cliente_id = cl.id
                INNER JOIN usuarios ag ON sc.agente_id = ag.id
                WHERE (sc.cliente_id = ? OR sc.agente_id = ?)
                ORDER BY fecha_ultimo_mensaje DESC";
        
        return $this->db->select($sql, [$usuarioId, $usuarioId, $usuarioId, $usuarioId, $usuarioId, $usuarioId, $usuarioId]);
    }
    
    /**
     * Obtener conversación específica con información de la solicitud
     * 
     * @param int $solicitudId ID de la solicitud
     * @param int $usuarioId ID del usuario actual
     * @return array|false Información de la conversación o false si no existe
     */
    public function getConversacion($solicitudId, $usuarioId) {
        $sql = "SELECT 
                    sc.id as solicitud_id,
                    sc.propiedad_id,
                    sc.cliente_id,
                    sc.agente_id,
                    sc.estado as estado_solicitud,
                    sc.fecha_solicitud,
                    sc.mensaje as mensaje_solicitud,
                    sc.presupuesto_min,
                    sc.presupuesto_max,
                    COALESCE(p.titulo, 'Conversación General') as titulo_propiedad,
                    COALESCE(p.precio, 0) as precio,
                    COALESCE(p.ciudad, 'General') as ciudad,
                    COALESCE(p.sector, 'General') as sector,
                    COALESCE(p.direccion, 'Conversación general') as direccion,
                    COALESCE(p.metros_cuadrados, 0) as metros_cuadrados,
                    COALESCE(p.habitaciones, 0) as habitaciones,
                    COALESCE(p.banos, 0) as banos,
                    cl.nombre as nombre_cliente,
                    cl.apellido as apellido_cliente,
                    cl.email as email_cliente,
                    cl.telefono as telefono_cliente,
                    ag.nombre as nombre_agente,
                    ag.apellido as apellido_agente,
                    ag.email as email_agente,
                    ag.telefono as telefono_agente
                FROM solicitudes_compra sc
                LEFT JOIN propiedades p ON sc.propiedad_id = p.id
                INNER JOIN usuarios cl ON sc.cliente_id = cl.id
                INNER JOIN usuarios ag ON sc.agente_id = ag.id
                WHERE sc.id = ? AND (sc.cliente_id = ? OR sc.agente_id = ?)";
        
        return $this->db->selectOne($sql, [$solicitudId, $usuarioId, $usuarioId]);
    }
    
    /**
     * Obtener estadísticas de chat para un usuario
     * 
     * @param int $usuarioId ID del usuario
     * @param string $rol Rol del usuario
     * @return array Estadísticas del chat
     */
    public function getEstadisticasChat($usuarioId, $rol) {
        $sql = "SELECT 
                    COUNT(DISTINCT sc.id) as total_conversaciones,
                    COUNT(DISTINCT CASE WHEN sc.estado = 'nuevo' THEN sc.id END) as conversaciones_nuevas,
                    COUNT(DISTINCT CASE WHEN sc.estado = 'en_revision' THEN sc.id END) as conversaciones_en_revision,
                    COUNT(DISTINCT CASE WHEN sc.estado = 'reunion_agendada' THEN sc.id END) as conversaciones_con_cita,
                    COUNT(DISTINCT CASE WHEN sc.estado = 'cerrado' THEN sc.id END) as conversaciones_cerradas,
                    (SELECT COUNT(*) FROM mensajes_chat mc 
                     INNER JOIN solicitudes_compra sc2 ON mc.solicitud_id = sc2.id
                     WHERE (sc2.cliente_id = ? OR sc2.agente_id = ?) 
                     AND mc.leido = 0 AND mc.remitente_id != ?) as mensajes_no_leidos
                FROM solicitudes_compra sc
                WHERE sc.cliente_id = ? OR sc.agente_id = ?";
        
        $result = $this->db->selectOne($sql, [$usuarioId, $usuarioId, $usuarioId, $usuarioId, $usuarioId]);
        
        if ($result) {
            return $result;
        }
        
        return [
            'total_conversaciones' => 0,
            'conversaciones_nuevas' => 0,
            'conversaciones_en_revision' => 0,
            'conversaciones_con_cita' => 0,
            'conversaciones_cerradas' => 0,
            'mensajes_no_leidos' => 0
        ];
    }
    
    /**
     * Verificar si un usuario tiene acceso a una conversación
     * 
     * @param int $solicitudId ID de la solicitud
     * @param int $usuarioId ID del usuario
     * @return bool True si tiene acceso
     */
    public function tieneAcceso($solicitudId, $usuarioId) {
        $sql = "SELECT COUNT(*) as total 
                FROM solicitudes_compra 
                WHERE id = ? AND (cliente_id = ? OR agente_id = ?)";
        
        $result = $this->db->selectOne($sql, [$solicitudId, $usuarioId, $usuarioId]);
        
        return $result && $result['total'] > 0;
    }
    
    /**
     * Verificar acceso al chat (alias de tieneAcceso para compatibilidad)
     * 
     * @param int $usuarioId ID del usuario
     * @param int $solicitudId ID de la solicitud
     * @return bool True si tiene acceso
     */
    public function verificarAcceso($usuarioId, $solicitudId) {
        return $this->tieneAcceso($solicitudId, $usuarioId);
    }
    
    /**
     * Obtener mensajes no leídos de un usuario
     * 
     * @param int $usuarioId ID del usuario
     * @return array Array de mensajes no leídos
     */
    public function getMensajesNoLeidos($usuarioId) {
        $sql = "SELECT 
                    mc.id,
                    mc.solicitud_id,
                    mc.remitente_id,
                    mc.remitente_rol,
                    mc.mensaje,
                    mc.fecha_envio,
                    sc.propiedad_id,
                    COALESCE(p.titulo, 'Conversación General') as titulo_propiedad,
                    u.nombre,
                    u.apellido
                FROM mensajes_chat mc
                INNER JOIN solicitudes_compra sc ON mc.solicitud_id = sc.id
                LEFT JOIN propiedades p ON sc.propiedad_id = p.id
                INNER JOIN usuarios u ON mc.remitente_id = u.id
                WHERE (sc.cliente_id = ? OR sc.agente_id = ?)
                AND mc.leido = 0 
                AND mc.remitente_id != ?
                ORDER BY mc.fecha_envio DESC";
        
        return $this->db->select($sql, [$usuarioId, $usuarioId, $usuarioId]);
    }
    
    /**
     * Obtener el último ID insertado
     * 
     * @return int Último ID insertado
     */
    public function getLastInsertId() {
        $conn = $this->db->getConnection();
        return $conn ? $conn->lastInsertId() : 0;
    }
    
    /**
     * Crear o recuperar una conversación general entre cliente y agente
     * No requiere propiedad asociada
     * @param int $user1_id
     * @param int $user2_id
     * @return int|false ID de la solicitud/conversación o false en error
     */
    public function crearObtenerSolicitud($user1_id, $user2_id) {
        // Obtener roles de los usuarios
        $sqlRol = "SELECT id, rol FROM usuarios WHERE id IN (?, ?)";
        $usuarios = $this->db->select($sqlRol, [$user1_id, $user2_id]);
        if (count($usuarios) != 2) return false;
        $roles = array_column($usuarios, 'rol', 'id');
        if (!isset($roles[$user1_id]) || !isset($roles[$user2_id])) return false;
        $rol1 = $roles[$user1_id];
        $rol2 = $roles[$user2_id];
        // Solo permitir cliente-agente
        if ($rol1 == $rol2) return false;
        $cliente_id = $rol1 === 'cliente' ? $user1_id : $user2_id;
        $agente_id  = $rol1 === 'agente' ? $user1_id : $user2_id;
        // Buscar si ya existe una conversación entre estos usuarios
        $sql = "SELECT id FROM solicitudes_compra WHERE cliente_id = ? AND agente_id = ? LIMIT 1";
        $row = $this->db->selectOne($sql, [$cliente_id, $agente_id]);
        if ($row && isset($row['id'])) {
            return $row['id'];
        }
        
        // Obtener una propiedad del agente para usar como placeholder
        $sqlProp = "SELECT id FROM propiedades WHERE agente_id = ? AND estado_publicacion = 'activa' LIMIT 1";
        $prop = $this->db->selectOne($sqlProp, [$agente_id]);
        
        if (!$prop) {
            // Si no hay propiedades del agente, crear una propiedad temporal
            $sqlTemp = "INSERT INTO propiedades (titulo, descripcion, tipo, precio, moneda, ciudad, sector, direccion, metros_cuadrados, habitaciones, banos, agente_id, estado_publicacion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $propId = $this->db->insert($sqlTemp, [
                'Conversación General',
                'Conversación general entre cliente y agente',
                'casa',
                0.00,
                'USD',
                'General',
                'General',
                'Conversación general',
                0.00,
                0,
                0,
                $agente_id,
                'activa'
            ]);
        } else {
            $propId = $prop['id'];
        }
        
        // Crear nueva conversación
        $sql = "INSERT INTO solicitudes_compra (cliente_id, agente_id, propiedad_id, estado, fecha_solicitud) VALUES (?, ?, ?, 'nuevo', NOW())";
        $id = $this->db->insert($sql, [$cliente_id, $agente_id, $propId]);
        return $id ? $id : false;
    }
    
    /**
     * Crear o recuperar una conversación directa entre dos usuarios
     * Sin necesidad de solicitud de compra
     * @param int $user1_id ID del primer usuario
     * @param int $user2_id ID del segundo usuario
     * @return int|false ID de la conversación o false en error
     */
    public function crearObtenerConversacionDirecta($user1_id, $user2_id) {
        error_log("🔍 crearObtenerConversacionDirecta() llamado - User1: $user1_id, User2: $user2_id");
        
        try {
            // Obtener roles de los usuarios
            $sqlRol = "SELECT id, rol FROM usuarios WHERE id IN (?, ?)";
            $usuarios = $this->db->select($sqlRol, [$user1_id, $user2_id]);
            error_log("🔍 Usuarios encontrados: " . json_encode($usuarios));
            
            if (count($usuarios) != 2) {
                error_log("❌ No se encontraron ambos usuarios");
                return false;
            }
            
            $roles = array_column($usuarios, 'rol', 'id');
            if (!isset($roles[$user1_id]) || !isset($roles[$user2_id])) {
                error_log("❌ No se pudieron obtener los roles");
                return false;
            }
            
            $rol1 = $roles[$user1_id];
            $rol2 = $roles[$user2_id];
            error_log("🔍 Roles - User1: $rol1, User2: $rol2");
            
            // Solo permitir cliente-agente o agente-cliente
            if ($rol1 == $rol2) {
                error_log("❌ Ambos usuarios tienen el mismo rol: $rol1");
                return false;
            }
            
            // Determinar quién es cliente y quién es agente
            $cliente_id = $rol1 === 'cliente' ? $user1_id : $user2_id;
            $agente_id = $rol1 === 'agente' ? $user1_id : $user2_id;
            error_log("🔍 Cliente ID: $cliente_id, Agente ID: $agente_id");
            
            // Buscar si ya existe una conversación directa entre estos usuarios
            $sql = "SELECT id FROM conversaciones_directas WHERE cliente_id = ? AND agente_id = ? LIMIT 1";
            $row = $this->db->selectOne($sql, [$cliente_id, $agente_id]);
            error_log("🔍 Conversación existente: " . json_encode($row));
            
            if ($row && isset($row['id'])) {
                error_log("✅ Conversación existente encontrada: " . $row['id']);
                return $row['id'];
            }
            
            // Crear nueva conversación directa
            $sql = "INSERT INTO conversaciones_directas (cliente_id, agente_id, fecha_creacion) VALUES (?, ?, NOW())";
            $id = $this->db->insert($sql, [$cliente_id, $agente_id]);
            error_log("🔍 Nueva conversación creada con ID: " . ($id ? $id : 'false'));
            
            return $id ? $id : false;
            
        } catch (Exception $e) {
            error_log("❌ Error en crearObtenerConversacionDirecta: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener conversaciones directas de un usuario
     * @param int $usuarioId ID del usuario
     * @param string $rol Rol del usuario
     * @return array Array de conversaciones
     */
    public function getConversacionesDirectas($usuarioId, $rol) {
        error_log("🔍 getConversacionesDirectas() llamado - Usuario: $usuarioId, Rol: $rol");
        
        $sql = "SELECT 
                    cd.id as conversacion_id,
                    cd.cliente_id,
                    cd.agente_id,
                    cd.fecha_creacion,
                    CASE 
                        WHEN cd.cliente_id = ? THEN ag.nombre
                        ELSE cl.nombre
                    END as nombre_otro_usuario,
                    CASE 
                        WHEN cd.cliente_id = ? THEN ag.apellido
                        ELSE cl.apellido
                    END as apellido_otro_usuario,
                    CASE 
                        WHEN cd.cliente_id = ? THEN ag.email
                        ELSE cl.email
                    END as email_otro_usuario,
                    CASE 
                        WHEN cd.cliente_id = ? THEN ag.foto_perfil
                        ELSE cl.foto_perfil
                    END as foto_perfil_otro_usuario,
                    CASE 
                        WHEN cd.cliente_id = ? THEN 'agente'
                        ELSE 'cliente'
                    END as rol_otro_usuario,
                    (SELECT COUNT(*) FROM mensajes_directos md 
                     WHERE md.conversacion_id = cd.id AND md.leido = 0 
                     AND md.remitente_id != ?) as mensajes_no_leidos,
                    (SELECT md.mensaje FROM mensajes_directos md 
                     WHERE md.conversacion_id = cd.id 
                     ORDER BY md.fecha_envio DESC LIMIT 1) as ultimo_mensaje,
                    (SELECT md.fecha_envio FROM mensajes_directos md 
                     WHERE md.conversacion_id = cd.id 
                     ORDER BY md.fecha_envio DESC LIMIT 1) as fecha_ultimo_mensaje
                FROM conversaciones_directas cd
                INNER JOIN usuarios cl ON cd.cliente_id = cl.id
                INNER JOIN usuarios ag ON cd.agente_id = ag.id
                WHERE (cd.cliente_id = ? OR cd.agente_id = ?)
                ORDER BY fecha_ultimo_mensaje DESC";
        
        $params = [$usuarioId, $usuarioId, $usuarioId, $usuarioId, $usuarioId, $usuarioId, $usuarioId, $usuarioId];
        error_log("🔍 SQL: $sql");
        error_log("🔍 Params: " . json_encode($params));
        
        $result = $this->db->select($sql, $params);
        error_log("📊 Resultado: " . json_encode($result));
        
        return $result;
    }
    
    /**
     * Obtener conversación directa específica
     * @param int $conversacionId ID de la conversación
     * @param int $usuarioId ID del usuario
     * @return array|false Información de la conversación o false si no existe
     */
    public function getConversacionDirecta($conversacionId, $usuarioId) {
        $sql = "SELECT 
                    cd.id as conversacion_id,
                    cd.cliente_id,
                    cd.agente_id,
                    cd.fecha_creacion,
                    cl.nombre as nombre_cliente,
                    cl.apellido as apellido_cliente,
                    cl.email as email_cliente,
                    cl.foto_perfil as foto_cliente,
                    ag.nombre as nombre_agente,
                    ag.apellido as apellido_agente,
                    ag.email as email_agente,
                    ag.foto_perfil as foto_agente
                FROM conversaciones_directas cd
                INNER JOIN usuarios cl ON cd.cliente_id = cl.id
                INNER JOIN usuarios ag ON cd.agente_id = ag.id
                WHERE cd.id = ? AND (cd.cliente_id = ? OR cd.agente_id = ?)";
        
        return $this->db->selectOne($sql, [$conversacionId, $usuarioId, $usuarioId]);
    }
    
    /**
     * Enviar mensaje directo (sin solicitud de compra)
     * @param int $conversacionId ID de la conversación directa
     * @param int $remitenteId ID del remitente
     * @param string $remitenteRol Rol del remitente
     * @param string $mensaje Contenido del mensaje
     * @return int|false ID del mensaje insertado o false en error
     */
    public function enviarMensajeDirecto($conversacionId, $remitenteId, $remitenteRol, $mensaje) {
        try {
            $sql = "INSERT INTO mensajes_directos (conversacion_id, remitente_id, remitente_rol, mensaje) 
                    VALUES (?, ?, ?, ?)";
            
            $result = $this->db->insert($sql, [$conversacionId, $remitenteId, $remitenteRol, $mensaje]);
            
            if ($result !== false) {
                return $result;
            }
            
            return false;
            
        } catch (Exception $e) {
            error_log("Error en enviarMensajeDirecto: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener mensajes de una conversación directa
     * @param int $conversacionId ID de la conversación
     * @param int $limit Límite de mensajes
     * @param int $offset Offset para paginación
     * @return array Array de mensajes
     */
    public function getMensajesDirectos($conversacionId, $limit = 50, $offset = 0) {
        $sql = "SELECT 
                    md.id,
                    md.conversacion_id,
                    md.remitente_id,
                    md.remitente_rol,
                    md.mensaje,
                    md.leido,
                    md.fecha_envio,
                    u.nombre,
                    u.apellido,
                    u.email
                FROM mensajes_directos md
                INNER JOIN usuarios u ON md.remitente_id = u.id
                WHERE md.conversacion_id = ?
                ORDER BY md.fecha_envio ASC
                LIMIT ? OFFSET ?";
        
        return $this->db->select($sql, [$conversacionId, $limit, $offset]);
    }
    
    /**
     * Verificar acceso a conversación directa
     * @param int $conversacionId ID de la conversación
     * @param int $usuarioId ID del usuario
     * @return bool True si tiene acceso
     */
    public function tieneAccesoConversacionDirecta($conversacionId, $usuarioId) {
        $sql = "SELECT COUNT(*) as total 
                FROM conversaciones_directas 
                WHERE id = ? AND (cliente_id = ? OR agente_id = ?)";
        
        $result = $this->db->selectOne($sql, [$conversacionId, $usuarioId, $usuarioId]);
        
        return $result && $result['total'] > 0;
    }

    // ===== MÉTODOS PARA EL NUEVO SISTEMA DE CHAT CON SSE =====

    /**
     * Obtener conversaciones de un usuario (nuevo método)
     * 
     * @param int $usuarioId ID del usuario
     * @return array Array de conversaciones
     */
    public function getConversacionesByUserId($usuarioId) {
        $sql = "SELECT DISTINCT
                    sc.id as solicitud_id,
                    sc.cliente_id,
                    sc.agente_id,
                    sc.propiedad_id,
                    sc.estado as estado_solicitud,
                    sc.fecha_solicitud,
                    COALESCE(p.titulo, 'Conversación General') as titulo_propiedad,
                    COALESCE(p.precio, 0) as precio,
                    COALESCE(p.ciudad, 'General') as ciudad,
                    COALESCE(p.sector, 'General') as sector,
                    CASE 
                        WHEN sc.cliente_id = ? THEN ag.nombre
                        ELSE cl.nombre
                    END as nombre_otro_usuario,
                    CASE 
                        WHEN sc.cliente_id = ? THEN ag.apellido
                        ELSE cl.apellido
                    END as apellido_otro_usuario,
                    CASE 
                        WHEN sc.cliente_id = ? THEN ag.email
                        ELSE cl.email
                    END as email_otro_usuario,
                    CASE 
                        WHEN sc.cliente_id = ? THEN 'agente'
                        ELSE 'cliente'
                    END as rol_otro_usuario,
                    (SELECT COUNT(*) FROM mensajes_chat mc 
                     WHERE mc.solicitud_id = sc.id AND mc.leido = 0 
                     AND mc.remitente_id != ?) as mensajes_no_leidos,
                    (SELECT mc.mensaje FROM mensajes_chat mc 
                     WHERE mc.solicitud_id = sc.id 
                     ORDER BY mc.fecha_envio DESC LIMIT 1) as ultimo_mensaje,
                    (SELECT mc.fecha_envio FROM mensajes_chat mc 
                     WHERE mc.solicitud_id = sc.id 
                     ORDER BY mc.fecha_envio DESC LIMIT 1) as fecha_ultimo_mensaje
                FROM solicitudes_compra sc
                LEFT JOIN propiedades p ON sc.propiedad_id = p.id
                INNER JOIN usuarios cl ON sc.cliente_id = cl.id
                INNER JOIN usuarios ag ON sc.agente_id = ag.id
                WHERE (sc.cliente_id = ? OR sc.agente_id = ?)
                ORDER BY fecha_ultimo_mensaje DESC";
        
        return $this->db->select($sql, [$usuarioId, $usuarioId, $usuarioId, $usuarioId, $usuarioId, $usuarioId, $usuarioId]);
    }

    /**
     * Enviar mensaje (nuevo método)
     * 
     * @param int $roomId ID de la sala/conversación
     * @param int $userId ID del usuario
     * @param string $message Contenido del mensaje
     * @return int|false ID del mensaje insertado o false en error
     */
    public function sendMessage($roomId, $userId, $message) {
        // Obtener información del usuario
        $user = $this->getUserById($userId);
        if (!$user) {
            return false;
        }

        $sql = "INSERT INTO mensajes_chat (solicitud_id, remitente_id, remitente_rol, mensaje) 
                VALUES (?, ?, ?, ?)";
        
        $result = $this->db->insert($sql, [$roomId, $userId, $user['rol'], $message]);
        
        if ($result !== false) {
            return $this->getLastInsertId();
        }
        
        return false;
    }

    /**
     * Obtener mensaje por ID
     * 
     * @param int $messageId ID del mensaje
     * @return array|false Información del mensaje
     */
    public function getMessageById($messageId) {
        $sql = "SELECT 
                    mc.id,
                    mc.solicitud_id as room_id,
                    mc.remitente_id as user_id,
                    mc.remitente_rol as user_role,
                    mc.mensaje as message,
                    mc.leido as read,
                    mc.fecha_envio as timestamp,
                    u.nombre,
                    u.apellido,
                    CONCAT(u.nombre, ' ', u.apellido) as user_name
                FROM mensajes_chat mc
                INNER JOIN usuarios u ON mc.remitente_id = u.id
                WHERE mc.id = ?";
        
        return $this->db->selectOne($sql, [$messageId]);
    }

    /**
     * Obtener mensajes de una sala
     * 
     * @param int $roomId ID de la sala
     * @return array Array de mensajes
     */
    public function getMessagesByRoomId($roomId) {
        $sql = "SELECT 
                    mc.id,
                    mc.solicitud_id as room_id,
                    mc.remitente_id as user_id,
                    mc.remitente_rol as user_role,
                    mc.mensaje as message,
                    mc.leido as read,
                    mc.fecha_envio as timestamp,
                    u.nombre,
                    u.apellido,
                    CONCAT(u.nombre, ' ', u.apellido) as user_name
                FROM mensajes_chat mc
                INNER JOIN usuarios u ON mc.remitente_id = u.id
                WHERE mc.solicitud_id = ?
                ORDER BY mc.fecha_envio ASC";
        
        return $this->db->select($sql, [$roomId]);
    }

    /**
     * Obtener nuevos mensajes desde un ID específico
     * 
     * @param int $roomId ID de la sala
     * @param int $lastMessageId ID del último mensaje conocido
     * @return array Array de nuevos mensajes
     */
    public function getNewMessages($roomId, $lastMessageId) {
        $sql = "SELECT 
                    mc.id,
                    mc.solicitud_id as room_id,
                    mc.remitente_id as user_id,
                    mc.remitente_rol as user_role,
                    mc.mensaje as message,
                    mc.leido as read,
                    mc.fecha_envio as timestamp,
                    u.nombre,
                    u.apellido,
                    CONCAT(u.nombre, ' ', u.apellido) as user_name
                FROM mensajes_chat mc
                INNER JOIN usuarios u ON mc.remitente_id = u.id
                WHERE mc.solicitud_id = ? AND mc.id > ?
                ORDER BY mc.fecha_envio ASC";
        
        return $this->db->select($sql, [$roomId, $lastMessageId]);
    }

    /**
     * Marcar mensajes como leídos
     * 
     * @param int $roomId ID de la sala
     * @param int $userId ID del usuario
     * @return bool True si se actualizaron correctamente
     */
    public function markMessagesAsRead($roomId, $userId) {
        $sql = "UPDATE mensajes_chat 
                SET leido = 1 
                WHERE solicitud_id = ? 
                AND remitente_id != ? 
                AND leido = 0";
        
        return $this->db->update($sql, [$roomId, $userId]) !== false;
    }



    /**
     * Crear conversación directa
     * 
     * @param int $user1Id ID del primer usuario
     * @param int $user2Id ID del segundo usuario
     * @return int|false ID de la conversación creada o false en error
     */
    public function createDirectChat($user1Id, $user2Id) {
        // Verificar si ya existe una conversación directa
        $existingChat = $this->getExistingDirectChat($user1Id, $user2Id);
        if ($existingChat) {
            return $existingChat['id'];
        }

        // Crear nueva conversación directa
        $sql = "INSERT INTO conversaciones_directas (usuario1_id, usuario2_id, fecha_creacion) 
                VALUES (?, ?, NOW())";
        
        $result = $this->db->insert($sql, [$user1Id, $user2Id]);
        
        if ($result !== false) {
            return $this->getLastInsertId();
        }
        
        return false;
    }

    /**
     * Obtener conversación directa existente
     * 
     * @param int $user1Id ID del primer usuario
     * @param int $user2Id ID del segundo usuario
     * @return array|false Información de la conversación
     */
    private function getExistingDirectChat($user1Id, $user2Id) {
        $sql = "SELECT * FROM conversaciones_directas 
                WHERE (usuario1_id = ? AND usuario2_id = ?) 
                OR (usuario1_id = ? AND usuario2_id = ?)";
        
        return $this->db->selectOne($sql, [$user1Id, $user2Id, $user2Id, $user1Id]);
    }

    /**
     * Obtener usuario por ID
     * 
     * @param int $userId ID del usuario
     * @return array|false Información del usuario
     */
    private function getUserById($userId) {
        $sql = "SELECT * FROM usuarios WHERE id = ?";
        return $this->db->selectOne($sql, [$userId]);
    }
} 