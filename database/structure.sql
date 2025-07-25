-- ========================================
-- ESTRUCTURA DE BASE DE DATOS - PROPEASY
-- ========================================

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS `propeasy_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `propeasy_db`;

-- ========================================
-- FUNCIONES
-- ========================================

-- Función para calcular edad de cuenta
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
        SET v_resultado = CONCAT(v_anos, ' año', IF(v_anos > 1, 's', ''), ' y ', v_meses, ' mes', IF(v_meses > 1, 'es', ''));
    ELSEIF v_meses > 0 THEN
        SET v_resultado = CONCAT(v_meses, ' mes', IF(v_meses > 1, 'es', ''), ' y ', v_dias, ' día', IF(v_dias > 1, 's', ''));
    ELSE
        SET v_resultado = CONCAT(v_dias, ' día', IF(v_dias > 1, 's', ''));
    END IF;
    
    RETURN v_resultado;
END//
DELIMITER ;

-- Función para formatear precio
DELIMITER //
CREATE FUNCTION `FormatearPrecio`(p_precio DECIMAL(12,2), p_moneda ENUM('USD','DOP','EUR')) RETURNS varchar(50) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci
    DETERMINISTIC
BEGIN
    DECLARE v_simbolo VARCHAR(5);
    DECLARE v_precio_formateado VARCHAR(50);
    
    CASE p_moneda
        WHEN 'USD' THEN SET v_simbolo = '$';
        WHEN 'DOP' THEN SET v_simbolo = 'RD$';
        WHEN 'EUR' THEN SET v_simbolo = '€';
        ELSE SET v_simbolo = '$';
    END CASE;
    
    SET v_precio_formateado = CONCAT(v_simbolo, FORMAT(p_precio, 2));
    RETURN v_precio_formateado;
END//
DELIMITER ;

-- ========================================
-- TABLAS PRINCIPALES
-- ========================================

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` enum('admin','agente','cliente') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cliente',
  `telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ultimo_acceso` datetime DEFAULT NULL,
  `estado` enum('activo','inactivo','suspendido') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activo',
  `foto_perfil` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_rol` (`rol`),
  KEY `idx_estado` (`estado`),
  KEY `idx_fecha_registro` (`fecha_registro`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de propiedades
CREATE TABLE IF NOT EXISTS `propiedades` (
  `id` int NOT NULL AUTO_INCREMENT,
  `agente_id` int NOT NULL,
  `titulo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('casa','apartamento','terreno','local_comercial','oficina') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `operacion` enum('venta','alquiler') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio` decimal(12,2) NOT NULL,
  `moneda` enum('USD','DOP','EUR') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `habitaciones` int DEFAULT NULL,
  `banos` int DEFAULT NULL,
  `area` decimal(10,2) DEFAULT NULL,
  `direccion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ciudad` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo_postal` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitud` decimal(10,8) DEFAULT NULL,
  `longitud` decimal(11,8) DEFAULT NULL,
  `estado_propiedad` enum('disponible','reservada','vendida','alquilada') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'disponible',
  `fecha_publicacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `caracteristicas` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_agente_id` (`agente_id`),
  KEY `idx_tipo` (`tipo`),
  KEY `idx_operacion` (`operacion`),
  KEY `idx_precio` (`precio`),
  KEY `idx_estado_propiedad` (`estado_propiedad`),
  KEY `idx_ciudad` (`ciudad`),
  KEY `idx_fecha_publicacion` (`fecha_publicacion`),
  CONSTRAINT `propiedades_ibfk_1` FOREIGN KEY (`agente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de imágenes de propiedades
CREATE TABLE IF NOT EXISTS `imagenes_propiedades` (
  `id` int NOT NULL AUTO_INCREMENT,
  `propiedad_id` int NOT NULL,
  `url_imagen` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `es_principal` tinyint(1) NOT NULL DEFAULT '0',
  `orden` int NOT NULL DEFAULT '0',
  `fecha_subida` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_propiedad_id` (`propiedad_id`),
  KEY `idx_es_principal` (`es_principal`),
  KEY `idx_orden` (`orden`),
  CONSTRAINT `imagenes_propiedades_ibfk_1` FOREIGN KEY (`propiedad_id`) REFERENCES `propiedades` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de solicitudes de compra
CREATE TABLE IF NOT EXISTS `solicitudes_compra` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cliente_id` int NOT NULL,
  `propiedad_id` int NOT NULL,
  `mensaje` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('pendiente','en_revision','aceptada','rechazada','cancelada') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `fecha_solicitud` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_respuesta` datetime DEFAULT NULL,
  `respuesta_agente` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `idx_cliente_id` (`cliente_id`),
  KEY `idx_propiedad_id` (`propiedad_id`),
  KEY `idx_estado` (`estado`),
  KEY `idx_fecha_solicitud` (`fecha_solicitud`),
  CONSTRAINT `solicitudes_compra_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `solicitudes_compra_ibfk_2` FOREIGN KEY (`propiedad_id`) REFERENCES `propiedades` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de citas
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
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_solicitud_id` (`solicitud_id`),
  KEY `idx_agente_id` (`agente_id`),
  KEY `idx_cliente_id` (`cliente_id`),
  KEY `idx_propiedad_id` (`propiedad_id`),
  KEY `idx_fecha_cita` (`fecha_cita`),
  KEY `idx_estado` (`estado`),
  CONSTRAINT `citas_ibfk_1` FOREIGN KEY (`solicitud_id`) REFERENCES `solicitudes_compra` (`id`) ON DELETE CASCADE,
  CONSTRAINT `citas_ibfk_2` FOREIGN KEY (`agente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `citas_ibfk_3` FOREIGN KEY (`cliente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `citas_ibfk_4` FOREIGN KEY (`propiedad_id`) REFERENCES `propiedades` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABLAS DE CHAT
-- ========================================

-- Tabla de mensajes de chat
CREATE TABLE IF NOT EXISTS `mensajes_chat` (
  `id` int NOT NULL AUTO_INCREMENT,
  `solicitud_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `mensaje` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_envio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `leido` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_solicitud_id` (`solicitud_id`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_fecha_envio` (`fecha_envio`),
  KEY `idx_leido` (`leido`),
  CONSTRAINT `mensajes_chat_ibfk_1` FOREIGN KEY (`solicitud_id`) REFERENCES `solicitudes_compra` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mensajes_chat_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de conversaciones directas
CREATE TABLE IF NOT EXISTS `conversaciones_directas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario1_id` int NOT NULL,
  `usuario2_id` int NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ultimo_mensaje` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_conversacion` (`usuario1_id`,`usuario2_id`),
  KEY `idx_usuario1_id` (`usuario1_id`),
  KEY `idx_usuario2_id` (`usuario2_id`),
  KEY `idx_ultimo_mensaje` (`ultimo_mensaje`),
  CONSTRAINT `conversaciones_directas_ibfk_1` FOREIGN KEY (`usuario1_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `conversaciones_directas_ibfk_2` FOREIGN KEY (`usuario2_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de mensajes directos
CREATE TABLE IF NOT EXISTS `mensajes_directos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `conversacion_id` int NOT NULL,
  `emisor_id` int NOT NULL,
  `receptor_id` int NOT NULL,
  `mensaje` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_envio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `leido` tinyint(1) NOT NULL DEFAULT '0',
  `tipo` enum('texto','imagen','archivo') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'texto',
  `archivo_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_conversacion_id` (`conversacion_id`),
  KEY `idx_emisor_id` (`emisor_id`),
  KEY `idx_receptor_id` (`receptor_id`),
  KEY `idx_fecha_envio` (`fecha_envio`),
  KEY `idx_leido` (`leido`),
  KEY `idx_tipo` (`tipo`),
  CONSTRAINT `mensajes_directos_ibfk_1` FOREIGN KEY (`conversacion_id`) REFERENCES `conversaciones_directas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mensajes_directos_ibfk_2` FOREIGN KEY (`emisor_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mensajes_directos_ibfk_3` FOREIGN KEY (`receptor_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABLAS ADICIONALES
-- ========================================

-- Tabla de favoritos
CREATE TABLE IF NOT EXISTS `favoritos_propiedades` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `propiedad_id` int NOT NULL,
  `fecha_agregado` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_favorito` (`usuario_id`,`propiedad_id`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_propiedad_id` (`propiedad_id`),
  KEY `idx_fecha_agregado` (`fecha_agregado`),
  CONSTRAINT `favoritos_propiedades_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `favoritos_propiedades_ibfk_2` FOREIGN KEY (`propiedad_id`) REFERENCES `propiedades` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de calificaciones de agentes
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

-- Tabla de reportes de irregularidades
CREATE TABLE IF NOT EXISTS `reportes_irregularidades` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `tipo_reporte` enum('propiedad_falsa','agente_irregular','precio_incorrecto','otro') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `propiedad_id` int DEFAULT NULL,
  `agente_id` int DEFAULT NULL,
  `estado` enum('pendiente','en_revision','resuelto','desestimado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `fecha_reporte` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_resolucion` datetime DEFAULT NULL,
  `respuesta_admin` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_tipo_reporte` (`tipo_reporte`),
  KEY `idx_propiedad_id` (`propiedad_id`),
  KEY `idx_agente_id` (`agente_id`),
  KEY `idx_estado` (`estado`),
  KEY `idx_fecha_reporte` (`fecha_reporte`),
  CONSTRAINT `reportes_irregularidades_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reportes_irregularidades_ibfk_2` FOREIGN KEY (`propiedad_id`) REFERENCES `propiedades` (`id`) ON DELETE SET NULL,
  CONSTRAINT `reportes_irregularidades_ibfk_3` FOREIGN KEY (`agente_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de logs de actividad
CREATE TABLE IF NOT EXISTS `logs_actividad` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int DEFAULT NULL,
  `accion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `detalles` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_accion` (`accion`),
  KEY `idx_fecha` (`fecha`),
  CONSTRAINT `logs_actividad_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de alertas eliminadas
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- VISTAS
-- ========================================

-- Vista de conversaciones directas
CREATE TABLE `vista_conversaciones_directas` (
  `conversacion_id` int NOT NULL,
  `usuario1_id` int NOT NULL,
  `usuario1_nombre` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `usuario2_id` int NOT NULL,
  `usuario2_nombre` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ultimo_mensaje` datetime DEFAULT NULL,
  `total_mensajes` bigint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Vista de estadísticas de usuarios
CREATE TABLE `vista_estadisticas_usuarios` (
  `total_usuarios` bigint NOT NULL DEFAULT '0',
  `usuarios_activos` bigint NOT NULL DEFAULT '0',
  `usuarios_inactivos` bigint NOT NULL DEFAULT '0',
  `admins` bigint NOT NULL DEFAULT '0',
  `agentes` bigint NOT NULL DEFAULT '0',
  `clientes` bigint NOT NULL DEFAULT '0',
  `usuarios_mes_actual` bigint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Vista de estadísticas de propiedades
CREATE TABLE `vista_estadisticas_propiedades` (
  `total_propiedades` bigint NOT NULL DEFAULT '0',
  `propiedades_disponibles` bigint NOT NULL DEFAULT '0',
  `propiedades_vendidas` bigint NOT NULL DEFAULT '0',
  `propiedades_alquiladas` bigint NOT NULL DEFAULT '0',
  `propiedades_mes_actual` bigint NOT NULL DEFAULT '0',
  `valor_total_propiedades` decimal(32,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Vista de favoritos por usuario
CREATE TABLE `vista_favoritos_usuario` (
  `usuario_id` int NOT NULL,
  `usuario_nombre` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `propiedad_id` int NOT NULL,
  `propiedad_titulo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `propiedad_precio` decimal(12,2) NOT NULL,
  `propiedad_moneda` enum('USD','DOP','EUR') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `propiedad_ciudad` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_agregado` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Vista de propiedades por agente
CREATE TABLE `vista_propiedades_agente` (
  `agente_id` int NOT NULL,
  `agente_nombre` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_propiedades` bigint NOT NULL DEFAULT '0',
  `propiedades_disponibles` bigint NOT NULL DEFAULT '0',
  `propiedades_vendidas` bigint NOT NULL DEFAULT '0',
  `valor_total` decimal(32,2) DEFAULT NULL,
  `promedio_precio` decimal(14,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Vista de estadísticas detalladas por agente
CREATE TABLE `vista_estadisticas_detalladas_agente` (
  `agente_id` int NOT NULL,
  `agente_nombre` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_propiedades` bigint NOT NULL DEFAULT '0',
  `propiedades_disponibles` bigint NOT NULL DEFAULT '0',
  `propiedades_vendidas` bigint NOT NULL DEFAULT '0',
  `propiedades_alquiladas` bigint NOT NULL DEFAULT '0',
  `total_solicitudes` bigint NOT NULL DEFAULT '0',
  `solicitudes_pendientes` bigint NOT NULL DEFAULT '0',
  `solicitudes_aceptadas` bigint NOT NULL DEFAULT '0',
  `total_citas` bigint NOT NULL DEFAULT '0',
  `citas_completadas` bigint NOT NULL DEFAULT '0',
  `valor_total_propiedades` decimal(32,2) DEFAULT NULL,
  `promedio_precio` decimal(14,2) DEFAULT NULL,
  `calificacion_promedio` decimal(3,2) DEFAULT NULL,
  `total_calificaciones` bigint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Vista de propiedades detalladas por agente
CREATE TABLE `vista_propiedades_agente_detallada` (
  `agente_id` int NOT NULL,
  `agente_nombre` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `propiedad_id` int NOT NULL,
  `propiedad_titulo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `propiedad_tipo` enum('casa','apartamento','terreno','local_comercial','oficina') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `propiedad_operacion` enum('venta','alquiler') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `propiedad_precio` decimal(12,2) NOT NULL,
  `propiedad_moneda` enum('USD','DOP','EUR') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `propiedad_estado` enum('disponible','reservada','vendida','alquilada') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `propiedad_ciudad` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_publicacion` datetime NOT NULL,
  `total_imagenes` bigint NOT NULL DEFAULT '0',
  `total_solicitudes` bigint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- RESTAURAR CONFIGURACIONES
-- ========================================

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */; 