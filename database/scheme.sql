-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.4.3 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for propeasy_db
CREATE DATABASE IF NOT EXISTS `propeasy_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `propeasy_db`;

-- Dumping structure for table propeasy_db.alertas_eliminadas
CREATE TABLE IF NOT EXISTS `alertas_eliminadas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `admin_id` int NOT NULL,
  `tipo_alerta` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `titulo_alerta` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_eliminacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_admin_id` (`admin_id`),
  KEY `idx_tipo_alerta` (`tipo_alerta`),
  KEY `idx_fecha_eliminacion` (`fecha_eliminacion`),
  CONSTRAINT `alertas_eliminadas_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for function propeasy_db.CalcularEdadCuenta
DELIMITER //
CREATE FUNCTION `CalcularEdadCuenta`(p_fecha_registro DATETIME) RETURNS varchar(50) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci
    READS SQL DATA
    DETERMINISTIC
BEGIN
    DECLARE v_anos INT;
    DECLARE v_meses INT;
    DECLARE v_dias INT;
    DECLARE v_resultado VARCHAR(50);
    
    SET v_anos = TIMESTAMPDIFF(YEAR, p_fecha_registro, NOW());
    SET v_meses = TIMESTAMPDIFF(MONTH, p_fecha_registro, NOW()) % 12;
    SET v_dias = TIMESTAMPDIFF(DAY, p_fecha_registro, NOW()) % 30;
    
    IF v_anos > 0 THEN
        SET v_resultado = CONCAT(v_anos, ' a??o', IF(v_anos > 1, 's', ''), ' y ', v_meses, ' mes', IF(v_meses > 1, 'es', ''));
    ELSEIF v_meses > 0 THEN
        SET v_resultado = CONCAT(v_meses, ' mes', IF(v_meses > 1, 'es', ''), ' y ', v_dias, ' d??a', IF(v_dias > 1, 's', ''));
    ELSE
        SET v_resultado = CONCAT(v_dias, ' d??a', IF(v_dias > 1, 's', ''));
    END IF;
    
    RETURN v_resultado;
END//
DELIMITER ;

-- Dumping structure for table propeasy_db.calificaciones_agentes
CREATE TABLE IF NOT EXISTS `calificaciones_agentes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `agente_id` int NOT NULL,
  `cliente_id` int NOT NULL,
  `solicitud_id` int NOT NULL,
  `calificacion` int NOT NULL,
  `comentario` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fecha_calificacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_solicitud_calificacion` (`solicitud_id`),
  KEY `idx_agente_id` (`agente_id`),
  KEY `idx_cliente_id` (`cliente_id`),
  KEY `idx_calificacion` (`calificacion`),
  KEY `idx_fecha_calificacion` (`fecha_calificacion`),
  KEY `idx_calificaciones_agente_fecha` (`agente_id`,`fecha_calificacion`),
  CONSTRAINT `calificaciones_agentes_ibfk_1` FOREIGN KEY (`agente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `calificaciones_agentes_ibfk_2` FOREIGN KEY (`cliente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `calificaciones_agentes_ibfk_3` FOREIGN KEY (`solicitud_id`) REFERENCES `solicitudes_compra` (`id`) ON DELETE CASCADE,
  CONSTRAINT `calificaciones_agentes_chk_1` CHECK (((`calificacion` >= 1) and (`calificacion` <= 5)))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table propeasy_db.citas
CREATE TABLE IF NOT EXISTS `citas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `solicitud_id` int NOT NULL,
  `agente_id` int NOT NULL,
  `cliente_id` int NOT NULL,
  `propiedad_id` int NOT NULL,
  `fecha_cita` datetime NOT NULL,
  `lugar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_cita` enum('visita_propiedad','reunion_oficina','video_llamada') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'visita_propiedad',
  `estado` enum('propuesta','aceptada','rechazada','completada','cancelada','cambio_solicitado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'propuesta',
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `comentarios_cambio` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_solicitud_id` (`solicitud_id`),
  KEY `idx_agente_id` (`agente_id`),
  KEY `idx_cliente_id` (`cliente_id`),
  KEY `idx_propiedad_id` (`propiedad_id`),
  KEY `idx_fecha_cita` (`fecha_cita`),
  KEY `idx_estado` (`estado`),
  KEY `idx_citas_agente_estado` (`agente_id`,`estado`),
  KEY `idx_citas_agente_fecha` (`agente_id`,`fecha_cita`),
  KEY `idx_comentarios_cambio` (`comentarios_cambio`(100)),
  CONSTRAINT `citas_ibfk_1` FOREIGN KEY (`solicitud_id`) REFERENCES `solicitudes_compra` (`id`) ON DELETE CASCADE,
  CONSTRAINT `citas_ibfk_2` FOREIGN KEY (`agente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `citas_ibfk_3` FOREIGN KEY (`cliente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `citas_ibfk_4` FOREIGN KEY (`propiedad_id`) REFERENCES `propiedades` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table propeasy_db.conversaciones_directas
CREATE TABLE IF NOT EXISTS `conversaciones_directas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cliente_id` int NOT NULL,
  `agente_id` int NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` enum('activa','archivada','bloqueada') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activa',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_conversacion` (`cliente_id`,`agente_id`),
  KEY `idx_cliente_id` (`cliente_id`),
  KEY `idx_agente_id` (`agente_id`),
  KEY `idx_fecha_creacion` (`fecha_creacion`),
  KEY `idx_estado` (`estado`),
  CONSTRAINT `conversaciones_directas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `conversaciones_directas_ibfk_2` FOREIGN KEY (`agente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for procedure propeasy_db.CrearConversacionDirecta
DELIMITER //
CREATE PROCEDURE `CrearConversacionDirecta`(
    IN p_cliente_id INT,
    IN p_agente_id INT
)
BEGIN
    DECLARE v_conversacion_id INT;
    
    -- Verificar que los usuarios existen y tienen roles correctos
    IF EXISTS (SELECT 1 FROM usuarios WHERE id = p_cliente_id AND rol = 'cliente') 
       AND EXISTS (SELECT 1 FROM usuarios WHERE id = p_agente_id AND rol = 'agente') THEN
        
        -- Buscar conversación existente
        SELECT id INTO v_conversacion_id 
        FROM conversaciones_directas 
        WHERE cliente_id = p_cliente_id AND agente_id = p_agente_id;
        
        -- Si no existe, crear nueva
        IF v_conversacion_id IS NULL THEN
            INSERT INTO conversaciones_directas (cliente_id, agente_id) 
            VALUES (p_cliente_id, p_agente_id);
            SET v_conversacion_id = LAST_INSERT_ID();
        END IF;
        
        SELECT v_conversacion_id as conversacion_id, 'success' as status;
    ELSE
        SELECT NULL as conversacion_id, 'error' as status;
    END IF;
END//
DELIMITER ;

-- Dumping structure for procedure propeasy_db.EnviarMensajeDirecto
DELIMITER //
CREATE PROCEDURE `EnviarMensajeDirecto`(
    IN p_conversacion_id INT,
    IN p_remitente_id INT,
    IN p_remitente_rol VARCHAR(10),
    IN p_mensaje TEXT
)
BEGIN
    DECLARE v_mensaje_id INT;
    
    -- Verificar acceso a la conversación
    IF EXISTS (SELECT 1 FROM conversaciones_directas 
               WHERE id = p_conversacion_id 
               AND (cliente_id = p_remitente_id OR agente_id = p_remitente_id)) THEN
        
        -- Insertar mensaje
        INSERT INTO mensajes_directos (conversacion_id, remitente_id, remitente_rol, mensaje) 
        VALUES (p_conversacion_id, p_remitente_id, p_remitente_rol, p_mensaje);
        
        SET v_mensaje_id = LAST_INSERT_ID();
        SELECT v_mensaje_id as mensaje_id, 'success' as status;
    ELSE
        SELECT NULL as mensaje_id, 'access_denied' as status;
    END IF;
END//
DELIMITER ;

-- Dumping structure for procedure propeasy_db.estadisticas_agente
DELIMITER //
CREATE PROCEDURE `estadisticas_agente`(IN agente_id INT)
BEGIN
    SELECT 
        COUNT(*) as total_propiedades,
        COUNT(CASE WHEN estado_publicacion = 'activa' THEN 1 END) as propiedades_activas,
        COUNT(CASE WHEN estado_publicacion = 'vendida' THEN 1 END) as propiedades_vendidas,
        SUM(CASE WHEN estado_publicacion = 'vendida' THEN precio_venta ELSE 0 END) as total_ventas,
        COUNT(DISTINCT sc.id) as total_solicitudes,
        COUNT(DISTINCT c.id) as total_citas,
        AVG(ca.calificacion) as calificacion_promedio
    FROM propiedades p
    LEFT JOIN solicitudes_compra sc ON p.id = sc.propiedad_id
    LEFT JOIN citas c ON sc.id = c.solicitud_id
    LEFT JOIN calificaciones_agentes ca ON p.agente_id = ca.agente_id
    WHERE p.agente_id = agente_id;
END//
DELIMITER ;

-- Dumping structure for table propeasy_db.favoritos_propiedades
CREATE TABLE IF NOT EXISTS `favoritos_propiedades` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `propiedad_id` int NOT NULL,
  `fecha_agregado` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_usuario_propiedad` (`usuario_id`,`propiedad_id`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_propiedad_id` (`propiedad_id`),
  KEY `idx_fecha_agregado` (`fecha_agregado`),
  CONSTRAINT `favoritos_propiedades_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `favoritos_propiedades_ibfk_2` FOREIGN KEY (`propiedad_id`) REFERENCES `propiedades` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for function propeasy_db.FormatearPrecio
DELIMITER //
CREATE FUNCTION `FormatearPrecio`(p_precio DECIMAL(12,2), p_moneda ENUM('USD','DOP','EUR')) RETURNS varchar(50) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci
    READS SQL DATA
    DETERMINISTIC
BEGIN
    DECLARE v_simbolo VARCHAR(5);
    DECLARE v_resultado VARCHAR(50);
    
    CASE p_moneda
        WHEN 'USD' THEN SET v_simbolo = '$';
        WHEN 'DOP' THEN SET v_simbolo = 'RD$';
        WHEN 'EUR' THEN SET v_simbolo = '???';
        ELSE SET v_simbolo = '$';
    END CASE;
    
    SET v_resultado = CONCAT(v_simbolo, FORMAT(p_precio, 2));
    
    RETURN v_resultado;
END//
DELIMITER ;

-- Dumping structure for table propeasy_db.imagenes_propiedades
CREATE TABLE IF NOT EXISTS `imagenes_propiedades` (
  `id` int NOT NULL AUTO_INCREMENT,
  `propiedad_id` int NOT NULL,
  `nombre_archivo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ruta` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `es_principal` tinyint(1) NOT NULL DEFAULT '0',
  `orden` int NOT NULL DEFAULT '0',
  `fecha_subida` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_propiedad_id` (`propiedad_id`),
  KEY `idx_es_principal` (`es_principal`),
  KEY `idx_orden` (`orden`),
  CONSTRAINT `imagenes_propiedades_ibfk_1` FOREIGN KEY (`propiedad_id`) REFERENCES `propiedades` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for event propeasy_db.limpiar_tokens_diario
DELIMITER //
CREATE EVENT `limpiar_tokens_diario` ON SCHEDULE EVERY 1 DAY STARTS '2025-07-14 17:34:05' ON COMPLETION NOT PRESERVE ENABLE DO CALL limpiar_tokens_expirados()//
DELIMITER ;

-- Dumping structure for procedure propeasy_db.limpiar_tokens_expirados
DELIMITER //
CREATE PROCEDURE `limpiar_tokens_expirados`()
BEGIN
    UPDATE usuarios 
    SET token_verificacion = NULL 
    WHERE token_verificacion IS NOT NULL 
    AND fecha_registro < DATE_SUB(NOW(), INTERVAL 1 HOUR);
    
    UPDATE usuarios 
    SET token_reset_password = NULL, reset_password_expiry = NULL 
    WHERE reset_password_expiry IS NOT NULL 
    AND reset_password_expiry < NOW();
END//
DELIMITER ;

-- Dumping structure for table propeasy_db.logs_actividad
CREATE TABLE IF NOT EXISTS `logs_actividad` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int DEFAULT NULL,
  `accion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tabla_afectada` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registro_id` int DEFAULT NULL,
  `datos_anteriores` json DEFAULT NULL,
  `datos_nuevos` json DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fecha_actividad` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_accion` (`accion`),
  KEY `idx_tabla_afectada` (`tabla_afectada`),
  KEY `idx_fecha_actividad` (`fecha_actividad`),
  CONSTRAINT `logs_actividad_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=264 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table propeasy_db.mensajes_chat
CREATE TABLE IF NOT EXISTS `mensajes_chat` (
  `id` int NOT NULL AUTO_INCREMENT,
  `solicitud_id` int NOT NULL,
  `remitente_id` int NOT NULL,
  `remitente_rol` enum('cliente','agente') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensaje` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT '0',
  `fecha_envio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_solicitud_id` (`solicitud_id`),
  KEY `idx_remitente_id` (`remitente_id`),
  KEY `idx_fecha_envio` (`fecha_envio`),
  KEY `idx_leido` (`leido`),
  CONSTRAINT `mensajes_chat_ibfk_1` FOREIGN KEY (`solicitud_id`) REFERENCES `solicitudes_compra` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mensajes_chat_ibfk_2` FOREIGN KEY (`remitente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table propeasy_db.mensajes_directos
CREATE TABLE IF NOT EXISTS `mensajes_directos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `conversacion_id` int NOT NULL,
  `remitente_id` int NOT NULL,
  `remitente_rol` enum('cliente','agente') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensaje` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT '0',
  `fecha_envio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_conversacion_id` (`conversacion_id`),
  KEY `idx_remitente_id` (`remitente_id`),
  KEY `idx_fecha_envio` (`fecha_envio`),
  KEY `idx_leido` (`leido`),
  CONSTRAINT `mensajes_directos_ibfk_1` FOREIGN KEY (`conversacion_id`) REFERENCES `conversaciones_directas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mensajes_directos_ibfk_2` FOREIGN KEY (`remitente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for procedure propeasy_db.ObtenerActividadRecienteAgente
DELIMITER //
CREATE PROCEDURE `ObtenerActividadRecienteAgente`(IN p_agente_id INT, IN p_limit INT)
BEGIN
    DECLARE v_limit INT DEFAULT 10;
    
    IF p_limit IS NOT NULL AND p_limit > 0 THEN
        SET v_limit = p_limit;
    END IF;
    
    -- Verificar que el agente existe
    IF NOT EXISTS (SELECT 1 FROM usuarios WHERE id = p_agente_id AND rol = 'agente') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Agente no encontrado';
    END IF;
    
    -- Obtener actividad reciente combinando diferentes tipos
    (SELECT 
        'propiedad' as tipo,
        p.id as item_id,
        p.titulo as titulo,
        p.estado_publicacion as estado,
        p.fecha_creacion as fecha,
        CONCAT('Propiedad: ', p.titulo) as descripcion
    FROM propiedades p
    WHERE p.agente_id = p_agente_id
    ORDER BY p.fecha_creacion DESC
    LIMIT v_limit)
    
    UNION ALL
    
    (SELECT 
        'solicitud' as tipo,
        sc.id as item_id,
        CONCAT('Solicitud #', sc.id) as titulo,
        sc.estado as estado,
        sc.fecha_solicitud as fecha,
        CONCAT('Nueva solicitud de ', sc.nombre_cliente) as descripcion
    FROM solicitudes_compra sc
    WHERE sc.agente_id = p_agente_id
    ORDER BY sc.fecha_solicitud DESC
    LIMIT v_limit)
    
    UNION ALL
    
    (SELECT 
        'cita' as tipo,
        c.id as item_id,
        CONCAT('Cita #', c.id) as titulo,
        c.estado as estado,
        c.fecha_creacion as fecha,
        CONCAT('Cita programada para ', DATE_FORMAT(c.fecha_cita, '%d/%m/%Y %H:%i')) as descripcion
    FROM citas c
    WHERE c.agente_id = p_agente_id
    ORDER BY c.fecha_creacion DESC
    LIMIT v_limit)
    
    ORDER BY fecha DESC
    LIMIT v_limit;
    
END//
DELIMITER ;

-- Dumping structure for procedure propeasy_db.ObtenerCalificacionesAgente
DELIMITER //
CREATE PROCEDURE `ObtenerCalificacionesAgente`(IN p_agente_id INT, IN p_limit INT)
BEGIN
    DECLARE v_limit INT DEFAULT 5;
    
    IF p_limit IS NOT NULL AND p_limit > 0 THEN
        SET v_limit = p_limit;
    END IF;
    
    -- Verificar que el agente existe
    IF NOT EXISTS (SELECT 1 FROM usuarios WHERE id = p_agente_id AND rol = 'agente') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Agente no encontrado';
    END IF;
    
    SELECT 
        ca.id,
        ca.calificacion,
        ca.comentario,
        ca.fecha_calificacion,
        CONCAT(u.nombre, ' ', u.apellido) as nombre_cliente,
        u.email as email_cliente,
        p.titulo as titulo_propiedad,
        sc.id as solicitud_id
    FROM calificaciones_agentes ca
    INNER JOIN usuarios u ON ca.cliente_id = u.id
    INNER JOIN solicitudes_compra sc ON ca.solicitud_id = sc.id
    INNER JOIN propiedades p ON sc.propiedad_id = p.id
    WHERE ca.agente_id = p_agente_id
    ORDER BY ca.fecha_calificacion DESC
    LIMIT v_limit;
    
END//
DELIMITER ;

-- Dumping structure for procedure propeasy_db.ObtenerEstadisticasAgente
DELIMITER //
CREATE PROCEDURE `ObtenerEstadisticasAgente`(IN p_agente_id INT)
BEGIN
    DECLARE v_total_propiedades INT DEFAULT 0;
    DECLARE v_propiedades_activas INT DEFAULT 0;
    DECLARE v_propiedades_vendidas INT DEFAULT 0;
    DECLARE v_propiedades_revision INT DEFAULT 0;
    DECLARE v_total_solicitudes INT DEFAULT 0;
    DECLARE v_solicitudes_pendientes INT DEFAULT 0;
    DECLARE v_total_citas INT DEFAULT 0;
    DECLARE v_citas_pendientes INT DEFAULT 0;
    DECLARE v_calificacion_promedio DECIMAL(3,2) DEFAULT 0;
    DECLARE v_total_ventas DECIMAL(12,2) DEFAULT 0;
    DECLARE v_ingresos_mes DECIMAL(12,2) DEFAULT 0;
    
    -- Verificar que el agente existe
    IF NOT EXISTS (SELECT 1 FROM usuarios WHERE id = p_agente_id AND rol = 'agente') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Agente no encontrado';
    END IF;
    
    -- Obtener estad??sticas de propiedades
    SELECT 
        COUNT(*) INTO v_total_propiedades
    FROM propiedades 
    WHERE agente_id = p_agente_id;
    
    SELECT 
        COUNT(*) INTO v_propiedades_activas
    FROM propiedades 
    WHERE agente_id = p_agente_id AND estado_publicacion = 'activa';
    
    SELECT 
        COUNT(*) INTO v_propiedades_vendidas
    FROM propiedades 
    WHERE agente_id = p_agente_id AND estado_publicacion = 'vendida';
    
    SELECT 
        COUNT(*) INTO v_propiedades_revision
    FROM propiedades 
    WHERE agente_id = p_agente_id AND estado_publicacion = 'en_revision';
    
    -- Obtener estad??sticas de solicitudes
    SELECT 
        COUNT(*) INTO v_total_solicitudes
    FROM solicitudes_compra 
    WHERE agente_id = p_agente_id;
    
    SELECT 
        COUNT(*) INTO v_solicitudes_pendientes
    FROM solicitudes_compra 
    WHERE agente_id = p_agente_id AND estado = 'nuevo';
    
    -- Obtener estad??sticas de citas
    SELECT 
        COUNT(*) INTO v_total_citas
    FROM citas 
    WHERE agente_id = p_agente_id;
    
    SELECT 
        COUNT(*) INTO v_citas_pendientes
    FROM citas 
    WHERE agente_id = p_agente_id AND estado IN ('propuesta', 'aceptada');
    
    -- Obtener calificaci??n promedio
    SELECT 
        COALESCE(AVG(calificacion), 0) INTO v_calificacion_promedio
    FROM calificaciones_agentes 
    WHERE agente_id = p_agente_id;
    
    -- Obtener total de ventas
    SELECT 
        COALESCE(SUM(precio_venta), 0) INTO v_total_ventas
    FROM propiedades 
    WHERE agente_id = p_agente_id AND estado_publicacion = 'vendida';
    
    -- Obtener ingresos del mes actual
    SELECT 
        COALESCE(SUM(precio_venta), 0) INTO v_ingresos_mes
    FROM propiedades 
    WHERE agente_id = p_agente_id 
    AND estado_publicacion = 'vendida'
    AND MONTH(fecha_venta) = MONTH(CURRENT_DATE())
    AND YEAR(fecha_venta) = YEAR(CURRENT_DATE());
    
    -- Retornar resultados
    SELECT 
        v_total_propiedades as total_propiedades,
        v_propiedades_activas as propiedades_activas,
        v_propiedades_vendidas as propiedades_vendidas,
        v_propiedades_revision as propiedades_revision,
        v_total_solicitudes as total_solicitudes,
        v_solicitudes_pendientes as solicitudes_pendientes,
        v_total_citas as total_citas,
        v_citas_pendientes as citas_pendientes,
        v_calificacion_promedio as calificacion_promedio,
        v_total_ventas as total_ventas,
        v_ingresos_mes as ingresos_mes;
        
END//
DELIMITER ;

-- Dumping structure for table propeasy_db.propiedades
CREATE TABLE IF NOT EXISTS `propiedades` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('casa','apartamento','terreno','local_comercial','oficina') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio` decimal(12,2) NOT NULL,
  `moneda` enum('USD','DOP','EUR') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `ciudad` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sector` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `direccion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `metros_cuadrados` decimal(8,2) NOT NULL,
  `habitaciones` int NOT NULL DEFAULT '0',
  `banos` int NOT NULL DEFAULT '0',
  `estacionamientos` int NOT NULL DEFAULT '0',
  `estado_propiedad` enum('excelente','bueno','regular','necesita_reparacion') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bueno',
  `estado_publicacion` enum('en_revision','activa','vendida','rechazada') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_revision',
  `agente_id` int DEFAULT NULL,
  `cliente_vendedor_id` int DEFAULT NULL,
  `token_validacion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fecha_venta` datetime DEFAULT NULL,
  `precio_venta` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cliente_vendedor_id` (`cliente_vendedor_id`),
  KEY `idx_tipo` (`tipo`),
  KEY `idx_precio` (`precio`),
  KEY `idx_ciudad` (`ciudad`),
  KEY `idx_sector` (`sector`),
  KEY `idx_estado_publicacion` (`estado_publicacion`),
  KEY `idx_agente_id` (`agente_id`),
  KEY `idx_fecha_creacion` (`fecha_creacion`),
  KEY `idx_propiedades_agente_estado` (`agente_id`,`estado_publicacion`),
  KEY `idx_propiedades_agente_fecha` (`agente_id`,`fecha_creacion`),
  CONSTRAINT `propiedades_ibfk_1` FOREIGN KEY (`agente_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `propiedades_ibfk_2` FOREIGN KEY (`cliente_vendedor_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table propeasy_db.reportes_irregularidades
CREATE TABLE IF NOT EXISTS `reportes_irregularidades` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `tipo_reporte` enum('queja_agente','problema_plataforma','informacion_falsa','otro') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `titulo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `archivo_adjunto` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('pendiente','atendido','descartado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `respuesta_admin` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fecha_reporte` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_respuesta` datetime DEFAULT NULL,
  `admin_responsable_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `admin_responsable_id` (`admin_responsable_id`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_tipo_reporte` (`tipo_reporte`),
  KEY `idx_estado` (`estado`),
  KEY `idx_fecha_reporte` (`fecha_reporte`),
  CONSTRAINT `reportes_irregularidades_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reportes_irregularidades_ibfk_2` FOREIGN KEY (`admin_responsable_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table propeasy_db.solicitudes_compra
CREATE TABLE IF NOT EXISTS `solicitudes_compra` (
  `id` int NOT NULL AUTO_INCREMENT,
  `propiedad_id` int NOT NULL,
  `cliente_id` int NOT NULL,
  `agente_id` int NOT NULL,
  `nombre_cliente` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_cliente` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono_cliente` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensaje` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `presupuesto_min` decimal(12,2) DEFAULT NULL,
  `presupuesto_max` decimal(12,2) DEFAULT NULL,
  `estado` enum('nuevo','en_revision','reunion_agendada','cerrado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'nuevo',
  `fecha_solicitud` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_respuesta` datetime DEFAULT NULL,
  `respuesta_agente` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `idx_propiedad_id` (`propiedad_id`),
  KEY `idx_cliente_id` (`cliente_id`),
  KEY `idx_agente_id` (`agente_id`),
  KEY `idx_estado` (`estado`),
  KEY `idx_fecha_solicitud` (`fecha_solicitud`),
  KEY `idx_solicitudes_agente_estado` (`agente_id`,`estado`),
  KEY `idx_solicitudes_agente_fecha` (`agente_id`,`fecha_solicitud`),
  CONSTRAINT `solicitudes_compra_ibfk_1` FOREIGN KEY (`propiedad_id`) REFERENCES `propiedades` (`id`) ON DELETE CASCADE,
  CONSTRAINT `solicitudes_compra_ibfk_2` FOREIGN KEY (`cliente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `solicitudes_compra_ibfk_3` FOREIGN KEY (`agente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table propeasy_db.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ciudad` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sector` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rol` enum('cliente','agente','admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cliente',
  `estado` enum('activo','inactivo','suspendido') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activo',
  `email_verificado` tinyint(1) NOT NULL DEFAULT '0',
  `token_verificacion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token_reset_password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_password_expiry` datetime DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ultimo_acceso` datetime DEFAULT NULL,
  `perfil_publico_activo` tinyint(1) DEFAULT '0' COMMENT 'Indica si el perfil público del agente está activo',
  `biografia` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Biografía del agente',
  `experiencia_anos` int DEFAULT '0' COMMENT 'Años de experiencia',
  `especialidades` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Especialidades del agente (separadas por comas)',
  `foto_perfil` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ruta de la foto de perfil',
  `licencia_inmobiliaria` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Número de licencia inmobiliaria',
  `horario_disponibilidad` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Horarios de disponibilidad',
  `idiomas` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Idiomas que habla (separados por comas)',
  `redes_sociales` json DEFAULT NULL COMMENT 'Redes sociales en formato JSON',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_rol` (`rol`),
  KEY `idx_estado` (`estado`),
  KEY `idx_fecha_registro` (`fecha_registro`),
  KEY `idx_ciudad` (`ciudad`),
  KEY `idx_sector` (`sector`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for view propeasy_db.vista_conversaciones_directas
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `vista_conversaciones_directas` (
	`conversacion_id` INT NULL,
	`cliente_id` INT NULL,
	`agente_id` INT NULL,
	`fecha_creacion` DATETIME NULL,
	`estado` ENUM('activa','archivada','bloqueada') NULL COLLATE 'utf8mb4_unicode_ci',
	`nombre_cliente` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`apellido_cliente` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`email_cliente` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`nombre_agente` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`apellido_agente` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`email_agente` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`total_mensajes_no_leidos` BIGINT NULL,
	`ultimo_mensaje` MEDIUMTEXT NULL COLLATE 'utf8mb4_unicode_ci',
	`fecha_ultimo_mensaje` DATETIME NULL
) ENGINE=MyISAM;

-- Dumping structure for view propeasy_db.vista_estadisticas_detalladas_agente
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `vista_estadisticas_detalladas_agente` (
	`agente_id` INT NOT NULL,
	`nombre_agente` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`email_agente` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`telefono_agente` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`ciudad_agente` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`sector_agente` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`fecha_registro` DATETIME NOT NULL,
	`ultimo_acceso` DATETIME NULL,
	`perfil_publico_activo` TINYINT(1) NULL COMMENT 'Indica si el perfil público del agente está activo',
	`biografia` TEXT NULL COMMENT 'Biografía del agente' COLLATE 'utf8mb4_unicode_ci',
	`experiencia_anos` INT NULL COMMENT 'Años de experiencia',
	`especialidades` TEXT NULL COMMENT 'Especialidades del agente (separadas por comas)' COLLATE 'utf8mb4_unicode_ci',
	`licencia_inmobiliaria` VARCHAR(1) NULL COMMENT 'Número de licencia inmobiliaria' COLLATE 'utf8mb4_unicode_ci',
	`horario_disponibilidad` TEXT NULL COMMENT 'Horarios de disponibilidad' COLLATE 'utf8mb4_unicode_ci',
	`idiomas` TEXT NULL COMMENT 'Idiomas que habla (separados por comas)' COLLATE 'utf8mb4_unicode_ci',
	`redes_sociales` JSON NULL COMMENT 'Redes sociales en formato JSON',
	`total_propiedades` BIGINT NOT NULL,
	`propiedades_activas` BIGINT NOT NULL,
	`propiedades_vendidas` BIGINT NOT NULL,
	`propiedades_revision` BIGINT NOT NULL,
	`propiedades_rechazadas` BIGINT NOT NULL,
	`total_solicitudes` BIGINT NOT NULL,
	`solicitudes_nuevas` BIGINT NOT NULL,
	`solicitudes_revision` BIGINT NOT NULL,
	`solicitudes_reunion` BIGINT NOT NULL,
	`solicitudes_cerradas` BIGINT NOT NULL,
	`total_citas` BIGINT NOT NULL,
	`citas_propuestas` BIGINT NOT NULL,
	`citas_aceptadas` BIGINT NOT NULL,
	`citas_realizadas` BIGINT NOT NULL,
	`citas_canceladas` BIGINT NOT NULL,
	`total_ventas` DECIMAL(34,2) NOT NULL,
	`precio_promedio_venta` DECIMAL(16,6) NOT NULL,
	`calificacion_promedio` DECIMAL(14,4) NOT NULL,
	`total_calificaciones` BIGINT NOT NULL,
	`calificaciones_5` BIGINT NOT NULL,
	`calificaciones_4` BIGINT NOT NULL,
	`calificaciones_3` BIGINT NOT NULL,
	`calificaciones_2` BIGINT NOT NULL,
	`calificaciones_1` BIGINT NOT NULL
) ENGINE=MyISAM;

-- Dumping structure for view propeasy_db.vista_estadisticas_propiedades
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `vista_estadisticas_propiedades` (
	`total_propiedades` BIGINT NOT NULL,
	`propiedades_activas` BIGINT NOT NULL,
	`propiedades_vendidas` BIGINT NOT NULL,
	`propiedades_revision` BIGINT NOT NULL,
	`precio_promedio` DECIMAL(16,6) NULL,
	`total_ventas` DECIMAL(34,2) NULL
) ENGINE=MyISAM;

-- Dumping structure for view propeasy_db.vista_estadisticas_usuarios
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `vista_estadisticas_usuarios` (
	`total_usuarios` BIGINT NOT NULL,
	`total_clientes` BIGINT NOT NULL,
	`total_agentes` BIGINT NOT NULL,
	`total_admins` BIGINT NOT NULL,
	`usuarios_activos` BIGINT NOT NULL,
	`usuarios_verificados` BIGINT NOT NULL
) ENGINE=MyISAM;

-- Dumping structure for view propeasy_db.vista_favoritos_usuario
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `vista_favoritos_usuario` (
	`favorito_id` INT NOT NULL,
	`usuario_id` INT NOT NULL,
	`propiedad_id` INT NOT NULL,
	`fecha_agregado` DATETIME NOT NULL,
	`titulo` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`descripcion` TEXT NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`tipo` ENUM('casa','apartamento','terreno','local_comercial','oficina') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`precio` DECIMAL(12,2) NOT NULL,
	`moneda` ENUM('USD','DOP','EUR') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`ciudad` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`sector` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`direccion` TEXT NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`metros_cuadrados` DECIMAL(8,2) NOT NULL,
	`habitaciones` INT NOT NULL,
	`banos` INT NOT NULL,
	`estacionamientos` INT NOT NULL,
	`estado_propiedad` ENUM('excelente','bueno','regular','necesita_reparacion') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`estado_publicacion` ENUM('en_revision','activa','vendida','rechazada') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`fecha_creacion` DATETIME NOT NULL,
	`agente_nombre` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`agente_apellido` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`agente_telefono` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`imagen_principal` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci'
) ENGINE=MyISAM;

-- Dumping structure for view propeasy_db.vista_propiedades_agente
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `vista_propiedades_agente` (
	`id` INT NOT NULL,
	`titulo` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`descripcion` TEXT NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`tipo` ENUM('casa','apartamento','terreno','local_comercial','oficina') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`precio` DECIMAL(12,2) NOT NULL,
	`moneda` ENUM('USD','DOP','EUR') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`ciudad` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`sector` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`direccion` TEXT NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`metros_cuadrados` DECIMAL(8,2) NOT NULL,
	`habitaciones` INT NOT NULL,
	`banos` INT NOT NULL,
	`estacionamientos` INT NOT NULL,
	`estado_propiedad` ENUM('excelente','bueno','regular','necesita_reparacion') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`estado_publicacion` ENUM('en_revision','activa','vendida','rechazada') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`agente_id` INT NULL,
	`cliente_vendedor_id` INT NULL,
	`token_validacion` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`fecha_creacion` DATETIME NOT NULL,
	`fecha_actualizacion` DATETIME NOT NULL,
	`fecha_venta` DATETIME NULL,
	`precio_venta` DECIMAL(12,2) NULL,
	`nombre_agente` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`email_agente` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`telefono_agente` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci'
) ENGINE=MyISAM;

-- Dumping structure for view propeasy_db.vista_propiedades_agente_detallada
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `vista_propiedades_agente_detallada` (
	`id` INT NOT NULL,
	`titulo` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`descripcion` TEXT NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`tipo` ENUM('casa','apartamento','terreno','local_comercial','oficina') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`precio` DECIMAL(12,2) NOT NULL,
	`moneda` ENUM('USD','DOP','EUR') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`ciudad` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`sector` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`direccion` TEXT NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`metros_cuadrados` DECIMAL(8,2) NOT NULL,
	`habitaciones` INT NOT NULL,
	`banos` INT NOT NULL,
	`estacionamientos` INT NOT NULL,
	`estado_propiedad` ENUM('excelente','bueno','regular','necesita_reparacion') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`estado_publicacion` ENUM('en_revision','activa','vendida','rechazada') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`agente_id` INT NULL,
	`cliente_vendedor_id` INT NULL,
	`fecha_creacion` DATETIME NOT NULL,
	`fecha_actualizacion` DATETIME NOT NULL,
	`fecha_venta` DATETIME NULL,
	`precio_venta` DECIMAL(12,2) NULL,
	`nombre_agente` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`email_agente` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`telefono_agente` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`ciudad_agente` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`sector_agente` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`nombre_vendedor` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`email_vendedor` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`telefono_vendedor` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`total_solicitudes` BIGINT NOT NULL,
	`total_citas` BIGINT NOT NULL,
	`total_favoritos` BIGINT NOT NULL,
	`imagen_principal` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci'
) ENGINE=MyISAM;

-- Dumping structure for trigger propeasy_db.tr_calificacion_agregada
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `tr_calificacion_agregada` AFTER INSERT ON `calificaciones_agentes` FOR EACH ROW BEGIN
    -- Registrar en logs de actividad
    INSERT INTO logs_actividad (usuario_id, accion, tabla_afectada, registro_id, datos_nuevos)
    VALUES (NEW.agente_id, 'recibir_calificacion', 'calificaciones_agentes', NEW.id, 
            JSON_OBJECT('calificacion', NEW.calificacion, 'cliente_id', NEW.cliente_id));
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger propeasy_db.tr_propiedad_creada
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `tr_propiedad_creada` AFTER INSERT ON `propiedades` FOR EACH ROW BEGIN
    -- Registrar en logs de actividad
    INSERT INTO logs_actividad (usuario_id, accion, tabla_afectada, registro_id, datos_nuevos)
    VALUES (NEW.agente_id, 'crear_propiedad', 'propiedades', NEW.id, JSON_OBJECT('titulo', NEW.titulo, 'tipo', NEW.tipo, 'precio', NEW.precio));
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger propeasy_db.tr_propiedad_vendida
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `tr_propiedad_vendida` AFTER UPDATE ON `propiedades` FOR EACH ROW BEGIN
    IF OLD.estado_publicacion != 'vendida' AND NEW.estado_publicacion = 'vendida' THEN
        -- Registrar en logs de actividad
        INSERT INTO logs_actividad (usuario_id, accion, tabla_afectada, registro_id, datos_anteriores, datos_nuevos)
        VALUES (NEW.agente_id, 'vender_propiedad', 'propiedades', NEW.id, 
                JSON_OBJECT('estado_anterior', OLD.estado_publicacion, 'precio', OLD.precio),
                JSON_OBJECT('estado_nuevo', NEW.estado_publicacion, 'precio_venta', NEW.precio_venta));
    END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `vista_conversaciones_directas`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_conversaciones_directas` AS select `cd`.`id` AS `conversacion_id`,`cd`.`cliente_id` AS `cliente_id`,`cd`.`agente_id` AS `agente_id`,`cd`.`fecha_creacion` AS `fecha_creacion`,`cd`.`estado` AS `estado`,`cl`.`nombre` AS `nombre_cliente`,`cl`.`apellido` AS `apellido_cliente`,`cl`.`email` AS `email_cliente`,`ag`.`nombre` AS `nombre_agente`,`ag`.`apellido` AS `apellido_agente`,`ag`.`email` AS `email_agente`,(select count(0) from `mensajes_directos` `md` where ((`md`.`conversacion_id` = `cd`.`id`) and (`md`.`leido` = 0))) AS `total_mensajes_no_leidos`,(select `md`.`mensaje` from `mensajes_directos` `md` where (`md`.`conversacion_id` = `cd`.`id`) order by `md`.`fecha_envio` desc limit 1) AS `ultimo_mensaje`,(select `md`.`fecha_envio` from `mensajes_directos` `md` where (`md`.`conversacion_id` = `cd`.`id`) order by `md`.`fecha_envio` desc limit 1) AS `fecha_ultimo_mensaje` from ((`conversaciones_directas` `cd` join `usuarios` `cl` on((`cd`.`cliente_id` = `cl`.`id`))) join `usuarios` `ag` on((`cd`.`agente_id` = `ag`.`id`)));

-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `vista_estadisticas_detalladas_agente`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_estadisticas_detalladas_agente` AS select `u`.`id` AS `agente_id`,concat(`u`.`nombre`,' ',`u`.`apellido`) AS `nombre_agente`,`u`.`email` AS `email_agente`,`u`.`telefono` AS `telefono_agente`,`u`.`ciudad` AS `ciudad_agente`,`u`.`sector` AS `sector_agente`,`u`.`fecha_registro` AS `fecha_registro`,`u`.`ultimo_acceso` AS `ultimo_acceso`,`u`.`perfil_publico_activo` AS `perfil_publico_activo`,`u`.`biografia` AS `biografia`,`u`.`experiencia_anos` AS `experiencia_anos`,`u`.`especialidades` AS `especialidades`,`u`.`licencia_inmobiliaria` AS `licencia_inmobiliaria`,`u`.`horario_disponibilidad` AS `horario_disponibilidad`,`u`.`idiomas` AS `idiomas`,`u`.`redes_sociales` AS `redes_sociales`,count(`p`.`id`) AS `total_propiedades`,count((case when (`p`.`estado_publicacion` = 'activa') then 1 end)) AS `propiedades_activas`,count((case when (`p`.`estado_publicacion` = 'vendida') then 1 end)) AS `propiedades_vendidas`,count((case when (`p`.`estado_publicacion` = 'en_revision') then 1 end)) AS `propiedades_revision`,count((case when (`p`.`estado_publicacion` = 'rechazada') then 1 end)) AS `propiedades_rechazadas`,count(`sc`.`id`) AS `total_solicitudes`,count((case when (`sc`.`estado` = 'nuevo') then 1 end)) AS `solicitudes_nuevas`,count((case when (`sc`.`estado` = 'en_revision') then 1 end)) AS `solicitudes_revision`,count((case when (`sc`.`estado` = 'reunion_agendada') then 1 end)) AS `solicitudes_reunion`,count((case when (`sc`.`estado` = 'cerrado') then 1 end)) AS `solicitudes_cerradas`,count(`c`.`id`) AS `total_citas`,count((case when (`c`.`estado` = 'propuesta') then 1 end)) AS `citas_propuestas`,count((case when (`c`.`estado` = 'aceptada') then 1 end)) AS `citas_aceptadas`,count((case when (`c`.`estado` = 'realizada') then 1 end)) AS `citas_realizadas`,count((case when (`c`.`estado` = 'cancelada') then 1 end)) AS `citas_canceladas`,coalesce(sum((case when (`p`.`estado_publicacion` = 'vendida') then `p`.`precio_venta` else 0 end)),0) AS `total_ventas`,coalesce(avg((case when (`p`.`estado_publicacion` = 'vendida') then `p`.`precio_venta` end)),0) AS `precio_promedio_venta`,coalesce(avg(`ca`.`calificacion`),0) AS `calificacion_promedio`,count(`ca`.`id`) AS `total_calificaciones`,count((case when (`ca`.`calificacion` = 5) then 1 end)) AS `calificaciones_5`,count((case when (`ca`.`calificacion` = 4) then 1 end)) AS `calificaciones_4`,count((case when (`ca`.`calificacion` = 3) then 1 end)) AS `calificaciones_3`,count((case when (`ca`.`calificacion` = 2) then 1 end)) AS `calificaciones_2`,count((case when (`ca`.`calificacion` = 1) then 1 end)) AS `calificaciones_1` from ((((`usuarios` `u` left join `propiedades` `p` on((`u`.`id` = `p`.`agente_id`))) left join `solicitudes_compra` `sc` on((`u`.`id` = `sc`.`agente_id`))) left join `citas` `c` on((`u`.`id` = `c`.`agente_id`))) left join `calificaciones_agentes` `ca` on((`u`.`id` = `ca`.`agente_id`))) where (`u`.`rol` = 'agente') group by `u`.`id`;

-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `vista_estadisticas_propiedades`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_estadisticas_propiedades` AS select count(0) AS `total_propiedades`,count((case when (`propiedades`.`estado_publicacion` = 'activa') then 1 end)) AS `propiedades_activas`,count((case when (`propiedades`.`estado_publicacion` = 'vendida') then 1 end)) AS `propiedades_vendidas`,count((case when (`propiedades`.`estado_publicacion` = 'en_revision') then 1 end)) AS `propiedades_revision`,avg(`propiedades`.`precio`) AS `precio_promedio`,sum((case when (`propiedades`.`estado_publicacion` = 'vendida') then `propiedades`.`precio_venta` else 0 end)) AS `total_ventas` from `propiedades`;

-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `vista_estadisticas_usuarios`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_estadisticas_usuarios` AS select count(0) AS `total_usuarios`,count((case when (`usuarios`.`rol` = 'cliente') then 1 end)) AS `total_clientes`,count((case when (`usuarios`.`rol` = 'agente') then 1 end)) AS `total_agentes`,count((case when (`usuarios`.`rol` = 'admin') then 1 end)) AS `total_admins`,count((case when (`usuarios`.`estado` = 'activo') then 1 end)) AS `usuarios_activos`,count((case when (`usuarios`.`email_verificado` = 1) then 1 end)) AS `usuarios_verificados` from `usuarios`;

-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `vista_favoritos_usuario`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_favoritos_usuario` AS select `f`.`id` AS `favorito_id`,`f`.`usuario_id` AS `usuario_id`,`f`.`propiedad_id` AS `propiedad_id`,`f`.`fecha_agregado` AS `fecha_agregado`,`p`.`titulo` AS `titulo`,`p`.`descripcion` AS `descripcion`,`p`.`tipo` AS `tipo`,`p`.`precio` AS `precio`,`p`.`moneda` AS `moneda`,`p`.`ciudad` AS `ciudad`,`p`.`sector` AS `sector`,`p`.`direccion` AS `direccion`,`p`.`metros_cuadrados` AS `metros_cuadrados`,`p`.`habitaciones` AS `habitaciones`,`p`.`banos` AS `banos`,`p`.`estacionamientos` AS `estacionamientos`,`p`.`estado_propiedad` AS `estado_propiedad`,`p`.`estado_publicacion` AS `estado_publicacion`,`p`.`fecha_creacion` AS `fecha_creacion`,`u`.`nombre` AS `agente_nombre`,`u`.`apellido` AS `agente_apellido`,`u`.`telefono` AS `agente_telefono`,(select `imagenes_propiedades`.`ruta` from `imagenes_propiedades` where ((`imagenes_propiedades`.`propiedad_id` = `p`.`id`) and (`imagenes_propiedades`.`es_principal` = 1)) limit 1) AS `imagen_principal` from ((`favoritos_propiedades` `f` join `propiedades` `p` on((`f`.`propiedad_id` = `p`.`id`))) left join `usuarios` `u` on((`p`.`agente_id` = `u`.`id`))) where (`p`.`estado_publicacion` = 'activa') order by `f`.`fecha_agregado` desc;

-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `vista_propiedades_agente`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_propiedades_agente` AS select `p`.`id` AS `id`,`p`.`titulo` AS `titulo`,`p`.`descripcion` AS `descripcion`,`p`.`tipo` AS `tipo`,`p`.`precio` AS `precio`,`p`.`moneda` AS `moneda`,`p`.`ciudad` AS `ciudad`,`p`.`sector` AS `sector`,`p`.`direccion` AS `direccion`,`p`.`metros_cuadrados` AS `metros_cuadrados`,`p`.`habitaciones` AS `habitaciones`,`p`.`banos` AS `banos`,`p`.`estacionamientos` AS `estacionamientos`,`p`.`estado_propiedad` AS `estado_propiedad`,`p`.`estado_publicacion` AS `estado_publicacion`,`p`.`agente_id` AS `agente_id`,`p`.`cliente_vendedor_id` AS `cliente_vendedor_id`,`p`.`token_validacion` AS `token_validacion`,`p`.`fecha_creacion` AS `fecha_creacion`,`p`.`fecha_actualizacion` AS `fecha_actualizacion`,`p`.`fecha_venta` AS `fecha_venta`,`p`.`precio_venta` AS `precio_venta`,concat(`u`.`nombre`,' ',`u`.`apellido`) AS `nombre_agente`,`u`.`email` AS `email_agente`,`u`.`telefono` AS `telefono_agente` from (`propiedades` `p` left join `usuarios` `u` on((`p`.`agente_id` = `u`.`id`))) where (`p`.`estado_publicacion` = 'activa');

-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `vista_propiedades_agente_detallada`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_propiedades_agente_detallada` AS select `p`.`id` AS `id`,`p`.`titulo` AS `titulo`,`p`.`descripcion` AS `descripcion`,`p`.`tipo` AS `tipo`,`p`.`precio` AS `precio`,`p`.`moneda` AS `moneda`,`p`.`ciudad` AS `ciudad`,`p`.`sector` AS `sector`,`p`.`direccion` AS `direccion`,`p`.`metros_cuadrados` AS `metros_cuadrados`,`p`.`habitaciones` AS `habitaciones`,`p`.`banos` AS `banos`,`p`.`estacionamientos` AS `estacionamientos`,`p`.`estado_propiedad` AS `estado_propiedad`,`p`.`estado_publicacion` AS `estado_publicacion`,`p`.`agente_id` AS `agente_id`,`p`.`cliente_vendedor_id` AS `cliente_vendedor_id`,`p`.`fecha_creacion` AS `fecha_creacion`,`p`.`fecha_actualizacion` AS `fecha_actualizacion`,`p`.`fecha_venta` AS `fecha_venta`,`p`.`precio_venta` AS `precio_venta`,concat(`u`.`nombre`,' ',`u`.`apellido`) AS `nombre_agente`,`u`.`email` AS `email_agente`,`u`.`telefono` AS `telefono_agente`,`u`.`ciudad` AS `ciudad_agente`,`u`.`sector` AS `sector_agente`,concat(`v`.`nombre`,' ',`v`.`apellido`) AS `nombre_vendedor`,`v`.`email` AS `email_vendedor`,`v`.`telefono` AS `telefono_vendedor`,count(distinct `sc`.`id`) AS `total_solicitudes`,count(distinct `c`.`id`) AS `total_citas`,count(distinct `fp`.`id`) AS `total_favoritos`,(select `ip`.`ruta` from `imagenes_propiedades` `ip` where ((`ip`.`propiedad_id` = `p`.`id`) and (`ip`.`es_principal` = 1)) limit 1) AS `imagen_principal` from (((((`propiedades` `p` join `usuarios` `u` on((`p`.`agente_id` = `u`.`id`))) left join `usuarios` `v` on((`p`.`cliente_vendedor_id` = `v`.`id`))) left join `solicitudes_compra` `sc` on((`p`.`id` = `sc`.`propiedad_id`))) left join `citas` `c` on((`sc`.`id` = `c`.`solicitud_id`))) left join `favoritos_propiedades` `fp` on((`p`.`id` = `fp`.`propiedad_id`))) group by `p`.`id`;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
