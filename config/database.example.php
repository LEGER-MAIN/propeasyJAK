<?php
/**
 * Configuración de la Base de Datos - EJEMPLO
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Copia este archivo como 'database.php' y configura tus parámetros
 */

// Configuración de la base de datos
define('DB_HOST', 'localhost');        // Host de la base de datos
define('DB_NAME', 'propeasy_db');      // Nombre de la base de datos
define('DB_USER', 'tu_usuario');       // Usuario de la base de datos
define('DB_PASS', 'tu_contraseña');    // Contraseña de la base de datos
define('DB_CHARSET', 'utf8mb4');       // Charset para soporte completo de caracteres

// Configuración de seguridad
define('JWT_SECRET', 'cambia_esta_clave_secreta_por_una_segura');  // Clave secreta para JWT
define('PASSWORD_COST', 12);           // Costo para hashing de contraseñas 