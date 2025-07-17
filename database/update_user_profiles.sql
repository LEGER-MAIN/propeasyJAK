-- =====================================================
-- ACTUALIZACIÓN DE PERFILES DE USUARIOS EXISTENTES
-- PropEasy - Sistema Web de Venta de Bienes Raíces
-- =====================================================

USE propeasy_db;

-- Actualizar usuarios que no tienen estado definido
UPDATE usuarios 
SET estado = 'activo' 
WHERE estado IS NULL OR estado = '';

-- Actualizar usuarios que no tienen email verificado
UPDATE usuarios 
SET email_verificado = 1 
WHERE email_verificado IS NULL OR email_verificado = 0;

-- Actualizar usuarios que no tienen fecha de registro
UPDATE usuarios 
SET fecha_registro = NOW() 
WHERE fecha_registro IS NULL;

-- Actualizar usuarios que no tienen último acceso
UPDATE usuarios 
SET ultimo_acceso = NOW() 
WHERE ultimo_acceso IS NULL;

-- Actualizar usuarios que no tienen ciudad (establecer Santo Domingo por defecto)
UPDATE usuarios 
SET ciudad = 'Santo Domingo' 
WHERE ciudad IS NULL OR ciudad = '';

-- Actualizar usuarios que no tienen sector (establecer Centro por defecto)
UPDATE usuarios 
SET sector = 'Centro' 
WHERE sector IS NULL OR sector = '';

-- Actualizar usuarios que no tienen teléfono (establecer un teléfono por defecto)
UPDATE usuarios 
SET telefono = '809-000-0000' 
WHERE telefono IS NULL OR telefono = '';

-- Verificar y mostrar usuarios actualizados
SELECT 
    id,
    nombre,
    apellido,
    email,
    telefono,
    ciudad,
    sector,
    rol,
    estado,
    email_verificado,
    fecha_registro,
    ultimo_acceso
FROM usuarios 
ORDER BY id;

-- Mostrar estadísticas de usuarios por rol
SELECT 
    rol,
    COUNT(*) as total_usuarios,
    COUNT(CASE WHEN estado = 'activo' THEN 1 END) as usuarios_activos,
    COUNT(CASE WHEN email_verificado = 1 THEN 1 END) as emails_verificados
FROM usuarios 
GROUP BY rol;

-- Mostrar usuarios que aún necesitan atención
SELECT 
    id,
    nombre,
    apellido,
    email,
    telefono,
    ciudad,
    sector,
    rol,
    estado,
    email_verificado
FROM usuarios 
WHERE 
    nombre IS NULL OR nombre = '' OR
    apellido IS NULL OR apellido = '' OR
    email IS NULL OR email = '' OR
    telefono IS NULL OR telefono = '' OR
    ciudad IS NULL OR ciudad = '' OR
    sector IS NULL OR sector = '' OR
    estado IS NULL OR estado = '' OR
    email_verificado IS NULL;

-- Crear índice para mejorar consultas de perfil
CREATE INDEX IF NOT EXISTS idx_usuarios_perfil ON usuarios(id, nombre, apellido, email, telefono, ciudad, sector, rol, estado, email_verificado, fecha_registro, ultimo_acceso);

-- Procedimiento para limpiar y actualizar perfil de usuario
DELIMITER //
CREATE PROCEDURE `ActualizarPerfilUsuario`(IN p_user_id INT)
BEGIN
    DECLARE v_user_exists INT DEFAULT 0;
    
    -- Verificar que el usuario existe
    SELECT COUNT(*) INTO v_user_exists FROM usuarios WHERE id = p_user_id;
    
    IF v_user_exists = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Usuario no encontrado';
    END IF;
    
    -- Actualizar campos faltantes
    UPDATE usuarios 
    SET 
        estado = COALESCE(estado, 'activo'),
        email_verificado = COALESCE(email_verificado, 1),
        fecha_registro = COALESCE(fecha_registro, NOW()),
        ultimo_acceso = COALESCE(ultimo_acceso, NOW()),
        ciudad = COALESCE(NULLIF(ciudad, ''), 'Santo Domingo'),
        sector = COALESCE(NULLIF(sector, ''), 'Centro'),
        telefono = COALESCE(NULLIF(telefono, ''), '809-000-0000')
    WHERE id = p_user_id;
    
    -- Mostrar resultado
    SELECT 
        id,
        nombre,
        apellido,
        email,
        telefono,
        ciudad,
        sector,
        rol,
        estado,
        email_verificado,
        fecha_registro,
        ultimo_acceso
    FROM usuarios 
    WHERE id = p_user_id;
    
END//
DELIMITER ;

-- Procedimiento para limpiar todos los perfiles de usuarios
DELIMITER //
CREATE PROCEDURE `LimpiarTodosLosPerfiles`()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_user_id INT;
    DECLARE user_cursor CURSOR FOR SELECT id FROM usuarios;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    OPEN user_cursor;
    
    read_loop: LOOP
        FETCH user_cursor INTO v_user_id;
        IF done THEN
            LEAVE read_loop;
        END IF;
        
        CALL ActualizarPerfilUsuario(v_user_id);
    END LOOP;
    
    CLOSE user_cursor;
    
    SELECT 'Todos los perfiles han sido actualizados' as resultado;
END//
DELIMITER ;

-- Ejecutar limpieza de todos los perfiles
CALL LimpiarTodosLosPerfiles();

-- Mostrar resumen final
SELECT 
    'RESUMEN DE ACTUALIZACIÓN' as titulo,
    COUNT(*) as total_usuarios,
    COUNT(CASE WHEN estado = 'activo' THEN 1 END) as usuarios_activos,
    COUNT(CASE WHEN email_verificado = 1 THEN 1 END) as emails_verificados,
    COUNT(CASE WHEN ciudad IS NOT NULL AND ciudad != '' THEN 1 END) as con_ciudad,
    COUNT(CASE WHEN sector IS NOT NULL AND sector != '' THEN 1 END) as con_sector,
    COUNT(CASE WHEN telefono IS NOT NULL AND telefono != '' THEN 1 END) as con_telefono
FROM usuarios; 