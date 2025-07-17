-- =====================================================
-- MEJORAS PARA EL PERFIL DEL AGENTE
-- PropEasy - Sistema Web de Venta de Bienes Raíces
-- =====================================================

USE propeasy_db;

-- =====================================================
-- PROCEDIMIENTOS ALMACENADOS PARA ESTADÍSTICAS DEL AGENTE
-- =====================================================

-- Procedimiento para obtener estadísticas completas del agente
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
    
    -- Obtener estadísticas de propiedades
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
    
    -- Obtener estadísticas de solicitudes
    SELECT 
        COUNT(*) INTO v_total_solicitudes
    FROM solicitudes_compra 
    WHERE agente_id = p_agente_id;
    
    SELECT 
        COUNT(*) INTO v_solicitudes_pendientes
    FROM solicitudes_compra 
    WHERE agente_id = p_agente_id AND estado = 'nuevo';
    
    -- Obtener estadísticas de citas
    SELECT 
        COUNT(*) INTO v_total_citas
    FROM citas 
    WHERE agente_id = p_agente_id;
    
    SELECT 
        COUNT(*) INTO v_citas_pendientes
    FROM citas 
    WHERE agente_id = p_agente_id AND estado IN ('propuesta', 'aceptada');
    
    -- Obtener calificación promedio
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

-- Procedimiento para obtener actividad reciente del agente
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

-- Procedimiento para obtener calificaciones del agente
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

-- =====================================================
-- VISTAS MEJORADAS PARA EL AGENTE
-- =====================================================

-- Vista para estadísticas detalladas del agente
CREATE OR REPLACE VIEW `vista_estadisticas_detalladas_agente` AS
SELECT 
    u.id as agente_id,
    CONCAT(u.nombre, ' ', u.apellido) as nombre_agente,
    u.email as email_agente,
    u.telefono as telefono_agente,
    u.ciudad as ciudad_agente,
    u.sector as sector_agente,
    u.fecha_registro as fecha_registro,
    u.ultimo_acceso as ultimo_acceso,
    u.perfil_publico_activo,
    u.biografia,
    u.experiencia_anos,
    u.especialidades,
    u.licencia_inmobiliaria,
    u.horario_disponibilidad,
    u.idiomas,
    u.redes_sociales,
    -- Estadísticas de propiedades
    COUNT(p.id) as total_propiedades,
    COUNT(CASE WHEN p.estado_publicacion = 'activa' THEN 1 END) as propiedades_activas,
    COUNT(CASE WHEN p.estado_publicacion = 'vendida' THEN 1 END) as propiedades_vendidas,
    COUNT(CASE WHEN p.estado_publicacion = 'en_revision' THEN 1 END) as propiedades_revision,
    COUNT(CASE WHEN p.estado_publicacion = 'rechazada' THEN 1 END) as propiedades_rechazadas,
    -- Estadísticas de solicitudes
    COUNT(sc.id) as total_solicitudes,
    COUNT(CASE WHEN sc.estado = 'nuevo' THEN 1 END) as solicitudes_nuevas,
    COUNT(CASE WHEN sc.estado = 'en_revision' THEN 1 END) as solicitudes_revision,
    COUNT(CASE WHEN sc.estado = 'reunion_agendada' THEN 1 END) as solicitudes_reunion,
    COUNT(CASE WHEN sc.estado = 'cerrado' THEN 1 END) as solicitudes_cerradas,
    -- Estadísticas de citas
    COUNT(c.id) as total_citas,
    COUNT(CASE WHEN c.estado = 'propuesta' THEN 1 END) as citas_propuestas,
    COUNT(CASE WHEN c.estado = 'aceptada' THEN 1 END) as citas_aceptadas,
    COUNT(CASE WHEN c.estado = 'realizada' THEN 1 END) as citas_realizadas,
    COUNT(CASE WHEN c.estado = 'cancelada' THEN 1 END) as citas_canceladas,
    -- Estadísticas financieras
    COALESCE(SUM(CASE WHEN p.estado_publicacion = 'vendida' THEN p.precio_venta ELSE 0 END), 0) as total_ventas,
    COALESCE(AVG(CASE WHEN p.estado_publicacion = 'vendida' THEN p.precio_venta END), 0) as precio_promedio_venta,
    -- Calificaciones
    COALESCE(AVG(ca.calificacion), 0) as calificacion_promedio,
    COUNT(ca.id) as total_calificaciones,
    COUNT(CASE WHEN ca.calificacion = 5 THEN 1 END) as calificaciones_5,
    COUNT(CASE WHEN ca.calificacion = 4 THEN 1 END) as calificaciones_4,
    COUNT(CASE WHEN ca.calificacion = 3 THEN 1 END) as calificaciones_3,
    COUNT(CASE WHEN ca.calificacion = 2 THEN 1 END) as calificaciones_2,
    COUNT(CASE WHEN ca.calificacion = 1 THEN 1 END) as calificaciones_1
FROM usuarios u
LEFT JOIN propiedades p ON u.id = p.agente_id
LEFT JOIN solicitudes_compra sc ON u.id = sc.agente_id
LEFT JOIN citas c ON u.id = c.agente_id
LEFT JOIN calificaciones_agentes ca ON u.id = ca.agente_id
WHERE u.rol = 'agente'
GROUP BY u.id;

-- Vista para propiedades del agente con información detallada
CREATE OR REPLACE VIEW `vista_propiedades_agente_detallada` AS
SELECT 
    p.id,
    p.titulo,
    p.descripcion,
    p.tipo,
    p.precio,
    p.moneda,
    p.ciudad,
    p.sector,
    p.direccion,
    p.metros_cuadrados,
    p.habitaciones,
    p.banos,
    p.estacionamientos,
    p.estado_propiedad,
    p.estado_publicacion,
    p.agente_id,
    p.cliente_vendedor_id,
    p.fecha_creacion,
    p.fecha_actualizacion,
    p.fecha_venta,
    p.precio_venta,
    -- Información del agente
    CONCAT(u.nombre, ' ', u.apellido) as nombre_agente,
    u.email as email_agente,
    u.telefono as telefono_agente,
    u.ciudad as ciudad_agente,
    u.sector as sector_agente,
    -- Información del vendedor
    CONCAT(v.nombre, ' ', v.apellido) as nombre_vendedor,
    v.email as email_vendedor,
    v.telefono as telefono_vendedor,
    -- Estadísticas de la propiedad
    COUNT(DISTINCT sc.id) as total_solicitudes,
    COUNT(DISTINCT c.id) as total_citas,
    COUNT(DISTINCT fp.id) as total_favoritos,
    -- Imagen principal
    (SELECT ip.ruta FROM imagenes_propiedades ip WHERE ip.propiedad_id = p.id AND ip.es_principal = 1 LIMIT 1) as imagen_principal
FROM propiedades p
INNER JOIN usuarios u ON p.agente_id = u.id
LEFT JOIN usuarios v ON p.cliente_vendedor_id = v.id
LEFT JOIN solicitudes_compra sc ON p.id = sc.propiedad_id
LEFT JOIN citas c ON sc.id = c.solicitud_id
LEFT JOIN favoritos_propiedades fp ON p.id = fp.propiedad_id
GROUP BY p.id;

-- =====================================================
-- FUNCIONES UTILITARIAS
-- =====================================================

-- Función para calcular la edad de una cuenta
DELIMITER //
CREATE FUNCTION `CalcularEdadCuenta`(p_fecha_registro DATETIME) 
RETURNS VARCHAR(50)
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
CREATE FUNCTION `FormatearPrecio`(p_precio DECIMAL(12,2), p_moneda ENUM('USD','DOP','EUR')) 
RETURNS VARCHAR(50)
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE v_simbolo VARCHAR(5);
    DECLARE v_resultado VARCHAR(50);
    
    CASE p_moneda
        WHEN 'USD' THEN SET v_simbolo = '$';
        WHEN 'DOP' THEN SET v_simbolo = 'RD$';
        WHEN 'EUR' THEN SET v_simbolo = '€';
        ELSE SET v_simbolo = '$';
    END CASE;
    
    SET v_resultado = CONCAT(v_simbolo, FORMAT(p_precio, 2));
    
    RETURN v_resultado;
END//
DELIMITER ;

-- =====================================================
-- TRIGGERS PARA MANTENER ESTADÍSTICAS ACTUALIZADAS
-- =====================================================

-- Trigger para actualizar estadísticas cuando se crea una propiedad
DELIMITER //
CREATE TRIGGER `tr_propiedad_creada` 
AFTER INSERT ON `propiedades`
FOR EACH ROW
BEGIN
    -- Registrar en logs de actividad
    INSERT INTO logs_actividad (usuario_id, accion, tabla_afectada, registro_id, datos_nuevos)
    VALUES (NEW.agente_id, 'crear_propiedad', 'propiedades', NEW.id, JSON_OBJECT('titulo', NEW.titulo, 'tipo', NEW.tipo, 'precio', NEW.precio));
END//
DELIMITER ;

-- Trigger para actualizar estadísticas cuando se vende una propiedad
DELIMITER //
CREATE TRIGGER `tr_propiedad_vendida` 
AFTER UPDATE ON `propiedades`
FOR EACH ROW
BEGIN
    IF OLD.estado_publicacion != 'vendida' AND NEW.estado_publicacion = 'vendida' THEN
        -- Registrar en logs de actividad
        INSERT INTO logs_actividad (usuario_id, accion, tabla_afectada, registro_id, datos_anteriores, datos_nuevos)
        VALUES (NEW.agente_id, 'vender_propiedad', 'propiedades', NEW.id, 
                JSON_OBJECT('estado_anterior', OLD.estado_publicacion, 'precio', OLD.precio),
                JSON_OBJECT('estado_nuevo', NEW.estado_publicacion, 'precio_venta', NEW.precio_venta));
    END IF;
END//
DELIMITER ;

-- Trigger para registrar calificaciones
DELIMITER //
CREATE TRIGGER `tr_calificacion_agregada` 
AFTER INSERT ON `calificaciones_agentes`
FOR EACH ROW
BEGIN
    -- Registrar en logs de actividad
    INSERT INTO logs_actividad (usuario_id, accion, tabla_afectada, registro_id, datos_nuevos)
    VALUES (NEW.agente_id, 'recibir_calificacion', 'calificaciones_agentes', NEW.id, 
            JSON_OBJECT('calificacion', NEW.calificacion, 'cliente_id', NEW.cliente_id));
END//
DELIMITER ;

-- =====================================================
-- ÍNDICES ADICIONALES PARA OPTIMIZAR CONSULTAS
-- =====================================================

-- Índices para mejorar el rendimiento de las consultas del agente
CREATE INDEX idx_propiedades_agente_estado ON propiedades(agente_id, estado_publicacion);
CREATE INDEX idx_propiedades_agente_fecha ON propiedades(agente_id, fecha_creacion);
CREATE INDEX idx_solicitudes_agente_estado ON solicitudes_compra(agente_id, estado);
CREATE INDEX idx_solicitudes_agente_fecha ON solicitudes_compra(agente_id, fecha_solicitud);
CREATE INDEX idx_citas_agente_estado ON citas(agente_id, estado);
CREATE INDEX idx_citas_agente_fecha ON citas(agente_id, fecha_cita);
CREATE INDEX idx_calificaciones_agente_fecha ON calificaciones_agentes(agente_id, fecha_calificacion);

-- =====================================================
-- DATOS DE EJEMPLO PARA PRUEBAS
-- =====================================================

-- Insertar algunos datos de ejemplo para agentes (si no existen)
INSERT IGNORE INTO usuarios (nombre, apellido, email, password, telefono, ciudad, sector, rol, estado, email_verificado, biografia, experiencia_anos, especialidades, licencia_inmobiliaria, horario_disponibilidad, idiomas, perfil_publico_activo) VALUES
('María', 'González', 'maria.gonzalez@propeasy.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '809-555-0101', 'Santo Domingo', 'Piantini', 'agente', 'activo', 1, 'Agente inmobiliaria con más de 8 años de experiencia en el mercado dominicano. Especializada en propiedades residenciales de lujo y desarrollo de proyectos.', 8, 'Propiedades residenciales,Propiedades de lujo,Desarrollo de proyectos', 'LIC-2024-001', 'Lun-Vie: 9:00 AM - 6:00 PM, Sáb: 9:00 AM - 2:00 PM', 'Español,Inglés', 1),
('Carlos', 'Rodríguez', 'carlos.rodriguez@propeasy.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '809-555-0102', 'Santiago', 'Centro', 'agente', 'activo', 1, 'Experto en propiedades comerciales e inversiones inmobiliarias. Más de 12 años ayudando a clientes a encontrar las mejores oportunidades.', 12, 'Propiedades comerciales,Inversiones inmobiliarias,Propiedades industriales', 'LIC-2024-002', 'Lun-Vie: 8:00 AM - 7:00 PM', 'Español,Inglés,Francés', 1),
('Ana', 'Martínez', 'ana.martinez@propeasy.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '809-555-0103', 'Santo Domingo', 'Naco', 'agente', 'activo', 1, 'Especialista en propiedades nuevas y proyectos en desarrollo. Conectando familias con sus hogares ideales desde 2015.', 9, 'Propiedades nuevas,Proyectos en desarrollo,Propiedades familiares', 'LIC-2024-003', 'Lun-Vie: 9:00 AM - 5:00 PM, Dom: 10:00 AM - 2:00 PM', 'Español,Inglés', 1);

-- =====================================================
-- COMENTARIOS Y DOCUMENTACIÓN
-- =====================================================

/*
ESTRUCTURA DE MEJORAS IMPLEMENTADAS:

1. PROCEDIMIENTOS ALMACENADOS:
   - ObtenerEstadisticasAgente: Estadísticas completas del agente
   - ObtenerActividadRecienteAgente: Actividad reciente combinada
   - ObtenerCalificacionesAgente: Calificaciones con detalles

2. VISTAS MEJORADAS:
   - vista_estadisticas_detalladas_agente: Estadísticas completas
   - vista_propiedades_agente_detallada: Propiedades con información extendida

3. FUNCIONES UTILITARIAS:
   - CalcularEdadCuenta: Calcula la antigüedad de una cuenta
   - FormatearPrecio: Formatea precios con símbolos de moneda

4. TRIGGERS:
   - tr_propiedad_creada: Registra creación de propiedades
   - tr_propiedad_vendida: Registra ventas de propiedades
   - tr_calificacion_agregada: Registra nuevas calificaciones

5. ÍNDICES:
   - Optimización de consultas frecuentes del agente

6. DATOS DE EJEMPLO:
   - Agentes de prueba con información completa

USO:
- Ejecutar este script en la base de datos propeasy_db
- Los procedimientos se pueden llamar desde PHP
- Las vistas proporcionan datos optimizados para el frontend
- Los triggers mantienen automáticamente los logs de actividad
*/ 