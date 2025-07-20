<?php
/**
 * Configuración de la Base de Datos
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este archivo contiene los parámetros de conexión a la base de datos MySQL
 * para el sistema PropEasy.
 */

// Configuración de la base de datos
define('DB_HOST', 'localhost');        // Host de la base de datos
define('DB_NAME', 'propeasy_db');      // Nombre de la base de datos
define('DB_USER', 'root');             // Usuario de la base de datos
define('DB_PASS', '');                 // Contraseña de la base de datos
define('DB_CHARSET', 'utf8mb4');       // Charset para soporte completo de caracteres

// Configuración de seguridad
define('JWT_SECRET', 'propeasy_jwt_secret_key_2025');  // Clave secreta para JWT
define('PASSWORD_COST', 12);           // Costo para hashing de contraseñas 