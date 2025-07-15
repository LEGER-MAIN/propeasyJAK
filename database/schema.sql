-- =====================================================
-- ESQUEMA DE BASE DE DATOS - PROPEASY
-- Sistema Web de Venta de Bienes Raíces
-- =====================================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS propeasy_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE propeasy_db;

-- =====================================================
-- TABLA: usuarios
-- Almacena información de todos los usuarios del sistema
-- =====================================================
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    rol ENUM('cliente', 'agente', 'admin') NOT NULL DEFAULT 'cliente',
    estado ENUM('activo', 'inactivo', 'suspendido') NOT NULL DEFAULT 'activo',
    email_verificado TINYINT(1) NOT NULL DEFAULT 0,
    token_verificacion VARCHAR(255) NULL,
    token_reset_password VARCHAR(255) NULL,
    reset_password_expiry DATETIME NULL,
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso DATETIME NULL,
    INDEX idx_email (email),
    INDEX idx_rol (rol),
    INDEX idx_estado (estado),
    INDEX idx_fecha_registro (fecha_registro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: propiedades
-- Almacena información de las propiedades inmobiliarias
-- =====================================================
CREATE TABLE propiedades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    tipo ENUM('casa', 'apartamento', 'terreno', 'local_comercial', 'oficina') NOT NULL,
    precio DECIMAL(12,2) NOT NULL,
    moneda ENUM('USD', 'DOP', 'EUR') NOT NULL DEFAULT 'USD',
    ciudad VARCHAR(100) NOT NULL,
    sector VARCHAR(100) NOT NULL,
    direccion TEXT NOT NULL,
    metros_cuadrados DECIMAL(8,2) NOT NULL,
    habitaciones INT NOT NULL DEFAULT 0,
    banos INT NOT NULL DEFAULT 0,
    estacionamientos INT NOT NULL DEFAULT 0,
    estado_propiedad ENUM('excelente', 'bueno', 'regular', 'necesita_reparacion') NOT NULL DEFAULT 'bueno',
    estado_publicacion ENUM('en_revision', 'activa', 'vendida', 'rechazada') NOT NULL DEFAULT 'en_revision',
    agente_id INT NULL,
    cliente_vendedor_id INT NULL,
    token_validacion VARCHAR(255) NULL,
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    fecha_venta DATETIME NULL,
    precio_venta DECIMAL(12,2) NULL,
    FOREIGN KEY (agente_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    FOREIGN KEY (cliente_vendedor_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_tipo (tipo),
    INDEX idx_precio (precio),
    INDEX idx_ciudad (ciudad),
    INDEX idx_sector (sector),
    INDEX idx_estado_publicacion (estado_publicacion),
    INDEX idx_agente_id (agente_id),
    INDEX idx_fecha_creacion (fecha_creacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: imagenes_propiedades
-- Almacena las imágenes de las propiedades
-- =====================================================
CREATE TABLE imagenes_propiedades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    propiedad_id INT NOT NULL,
    nombre_archivo VARCHAR(255) NOT NULL,
    ruta VARCHAR(500) NOT NULL,
    es_principal TINYINT(1) NOT NULL DEFAULT 0,
    orden INT NOT NULL DEFAULT 0,
    fecha_subida DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (propiedad_id) REFERENCES propiedades(id) ON DELETE CASCADE,
    INDEX idx_propiedad_id (propiedad_id),
    INDEX idx_es_principal (es_principal),
    INDEX idx_orden (orden)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: solicitudes_compra
-- Almacena las solicitudes de compra de los clientes
-- =====================================================
CREATE TABLE solicitudes_compra (
    id INT AUTO_INCREMENT PRIMARY KEY,
    propiedad_id INT NOT NULL,
    cliente_id INT NOT NULL,
    agente_id INT NOT NULL,
    nombre_cliente VARCHAR(100) NOT NULL,
    email_cliente VARCHAR(255) NOT NULL,
    telefono_cliente VARCHAR(20) NOT NULL,
    mensaje TEXT NULL,
    presupuesto_min DECIMAL(12,2) NULL,
    presupuesto_max DECIMAL(12,2) NULL,
    estado ENUM('nuevo', 'en_revision', 'reunion_agendada', 'cerrado') NOT NULL DEFAULT 'nuevo',
    fecha_solicitud DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_respuesta DATETIME NULL,
    respuesta_agente TEXT NULL,
    FOREIGN KEY (propiedad_id) REFERENCES propiedades(id) ON DELETE CASCADE,
    FOREIGN KEY (cliente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (agente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_propiedad_id (propiedad_id),
    INDEX idx_cliente_id (cliente_id),
    INDEX idx_agente_id (agente_id),
    INDEX idx_estado (estado),
    INDEX idx_fecha_solicitud (fecha_solicitud)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: mensajes_chat
-- Almacena los mensajes del chat interno
-- =====================================================
CREATE TABLE mensajes_chat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    solicitud_id INT NOT NULL,
    remitente_id INT NOT NULL,
    remitente_rol ENUM('cliente', 'agente') NOT NULL,
    mensaje TEXT NOT NULL,
    leido TINYINT(1) NOT NULL DEFAULT 0,
    fecha_envio DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (solicitud_id) REFERENCES solicitudes_compra(id) ON DELETE CASCADE,
    FOREIGN KEY (remitente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_solicitud_id (solicitud_id),
    INDEX idx_remitente_id (remitente_id),
    INDEX idx_fecha_envio (fecha_envio),
    INDEX idx_leido (leido)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: citas
-- Almacena las citas entre clientes y agentes
-- =====================================================
CREATE TABLE citas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    solicitud_id INT NOT NULL,
    agente_id INT NOT NULL,
    cliente_id INT NOT NULL,
    propiedad_id INT NOT NULL,
    fecha_cita DATETIME NOT NULL,
    lugar VARCHAR(255) NOT NULL,
    tipo_cita ENUM('visita_propiedad', 'reunion_oficina', 'video_llamada') NOT NULL DEFAULT 'visita_propiedad',
    estado ENUM('propuesta', 'aceptada', 'rechazada', 'realizada', 'cancelada') NOT NULL DEFAULT 'propuesta',
    observaciones TEXT NULL,
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (solicitud_id) REFERENCES solicitudes_compra(id) ON DELETE CASCADE,
    FOREIGN KEY (agente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (cliente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (propiedad_id) REFERENCES propiedades(id) ON DELETE CASCADE,
    INDEX idx_solicitud_id (solicitud_id),
    INDEX idx_agente_id (agente_id),
    INDEX idx_cliente_id (cliente_id),
    INDEX idx_propiedad_id (propiedad_id),
    INDEX idx_fecha_cita (fecha_cita),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: reportes_irregularidades
-- Almacena los reportes de irregularidades
-- =====================================================
CREATE TABLE reportes_irregularidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo_reporte ENUM('queja_agente', 'problema_plataforma', 'informacion_falsa', 'otro') NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    archivo_adjunto VARCHAR(500) NULL,
    estado ENUM('pendiente', 'atendido', 'descartado') NOT NULL DEFAULT 'pendiente',
    respuesta_admin TEXT NULL,
    fecha_reporte DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_respuesta DATETIME NULL,
    admin_responsable_id INT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_responsable_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_usuario_id (usuario_id),
    INDEX idx_tipo_reporte (tipo_reporte),
    INDEX idx_estado (estado),
    INDEX idx_fecha_reporte (fecha_reporte)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: calificaciones_agentes
-- Almacena las calificaciones de los agentes
-- =====================================================
CREATE TABLE calificaciones_agentes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    agente_id INT NOT NULL,
    cliente_id INT NOT NULL,
    solicitud_id INT NOT NULL,
    calificacion INT NOT NULL CHECK (calificacion >= 1 AND calificacion <= 5),
    comentario TEXT NULL,
    fecha_calificacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (agente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (cliente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (solicitud_id) REFERENCES solicitudes_compra(id) ON DELETE CASCADE,
    UNIQUE KEY unique_solicitud_calificacion (solicitud_id),
    INDEX idx_agente_id (agente_id),
    INDEX idx_cliente_id (cliente_id),
    INDEX idx_calificacion (calificacion),
    INDEX idx_fecha_calificacion (fecha_calificacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: logs_actividad
-- Almacena logs de actividad del sistema
-- =====================================================
CREATE TABLE logs_actividad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL,
    accion VARCHAR(100) NOT NULL,
    tabla_afectada VARCHAR(50) NULL,
    registro_id INT NULL,
    datos_anteriores JSON NULL,
    datos_nuevos JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    fecha_actividad DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_usuario_id (usuario_id),
    INDEX idx_accion (accion),
    INDEX idx_tabla_afectada (tabla_afectada),
    INDEX idx_fecha_actividad (fecha_actividad)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- INSERTAR DATOS INICIALES
-- =====================================================

-- Insertar usuario administrador por defecto
INSERT INTO usuarios (
    nombre, 
    apellido, 
    email, 
    password, 
    telefono, 
    rol, 
    estado, 
    email_verificado, 
    fecha_registro
) VALUES (
    'Administrador',
    'Sistema',
    'admin@propeasy.com',
    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    '809-555-0000',
    'admin',
    'activo',
    1,
    NOW()
);

-- Insertar agente de ejemplo
INSERT INTO usuarios (
    nombre, 
    apellido, 
    email, 
    password, 
    telefono, 
    rol, 
    estado, 
    email_verificado, 
    fecha_registro
) VALUES (
    'Juan',
    'Pérez',
    'juan.perez@propeasy.com',
    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    '809-555-0001',
    'agente',
    'activo',
    1,
    NOW()
);

-- Insertar cliente de ejemplo
INSERT INTO usuarios (
    nombre, 
    apellido, 
    email, 
    password, 
    telefono, 
    rol, 
    estado, 
    email_verificado, 
    fecha_registro
) VALUES (
    'María',
    'García',
    'maria.garcia@example.com',
    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    '809-555-0002',
    'cliente',
    'activo',
    1,
    NOW()
);

-- =====================================================
-- CREAR VISTAS ÚTILES
-- =====================================================

-- Vista para estadísticas de propiedades
CREATE VIEW vista_estadisticas_propiedades AS
SELECT 
    COUNT(*) as total_propiedades,
    COUNT(CASE WHEN estado_publicacion = 'activa' THEN 1 END) as propiedades_activas,
    COUNT(CASE WHEN estado_publicacion = 'vendida' THEN 1 END) as propiedades_vendidas,
    COUNT(CASE WHEN estado_publicacion = 'en_revision' THEN 1 END) as propiedades_revision,
    AVG(precio) as precio_promedio,
    SUM(CASE WHEN estado_publicacion = 'vendida' THEN precio_venta ELSE 0 END) as total_ventas
FROM propiedades;

-- Vista para estadísticas de usuarios
CREATE VIEW vista_estadisticas_usuarios AS
SELECT 
    COUNT(*) as total_usuarios,
    COUNT(CASE WHEN rol = 'cliente' THEN 1 END) as total_clientes,
    COUNT(CASE WHEN rol = 'agente' THEN 1 END) as total_agentes,
    COUNT(CASE WHEN rol = 'admin' THEN 1 END) as total_admins,
    COUNT(CASE WHEN estado = 'activo' THEN 1 END) as usuarios_activos,
    COUNT(CASE WHEN email_verificado = 1 THEN 1 END) as usuarios_verificados
FROM usuarios;

-- Vista para propiedades con información del agente
CREATE VIEW vista_propiedades_agente AS
SELECT 
    p.*,
    CONCAT(u.nombre, ' ', u.apellido) as nombre_agente,
    u.email as email_agente,
    u.telefono as telefono_agente
FROM propiedades p
LEFT JOIN usuarios u ON p.agente_id = u.id
WHERE p.estado_publicacion = 'activa';

-- =====================================================
-- CREAR PROCEDIMIENTOS ALMACENADOS ÚTILES
-- =====================================================

DELIMITER //

-- Procedimiento para limpiar tokens expirados
CREATE PROCEDURE limpiar_tokens_expirados()
BEGIN
    UPDATE usuarios 
    SET token_verificacion = NULL 
    WHERE token_verificacion IS NOT NULL 
    AND fecha_registro < DATE_SUB(NOW(), INTERVAL 1 HOUR);
    
    UPDATE usuarios 
    SET token_reset_password = NULL, reset_password_expiry = NULL 
    WHERE reset_password_expiry IS NOT NULL 
    AND reset_password_expiry < NOW();
END //

-- Procedimiento para obtener estadísticas del agente
CREATE PROCEDURE estadisticas_agente(IN agente_id INT)
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
END //

DELIMITER ;

-- =====================================================
-- CREAR EVENTOS PARA MANTENIMIENTO AUTOMÁTICO
-- =====================================================

-- Evento para limpiar tokens expirados diariamente
CREATE EVENT limpiar_tokens_diario
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP
DO CALL limpiar_tokens_expirados();

-- =====================================================
-- FIN DEL ESQUEMA
-- ===================================================== 