<?php
/**
 * Instalador de PropEasy para Laragon
 * 
 * Este script configura automáticamente PropEasy para funcionar con Laragon
 */

echo "<h1>🔧 Instalador de PropEasy para Laragon</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .success { color: #28a745; font-weight: bold; }
    .error { color: #dc3545; font-weight: bold; }
    .warning { color: #ffc107; font-weight: bold; }
    .info { color: #17a2b8; font-weight: bold; }
    .step { margin: 20px 0; padding: 15px; border-left: 4px solid #007bff; background: #f8f9fa; }
</style>";

echo "<div class='container'>";

// Paso 1: Verificar PHP
echo "<div class='step'>";
echo "<h2>📋 Paso 1: Verificar PHP</h2>";
echo "<p><strong>Versión PHP:</strong> " . PHP_VERSION . "</p>";

$required_extensions = ['mysqli', 'json', 'session', 'mbstring', 'openssl', 'curl'];
$all_extensions_ok = true;

foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p class='success'>✅ $ext</p>";
    } else {
        echo "<p class='error'>❌ $ext (FALTANTE)</p>";
        $all_extensions_ok = false;
    }
}

if ($all_extensions_ok) {
    echo "<p class='success'>✅ Todas las extensiones PHP requeridas están disponibles</p>";
} else {
    echo "<p class='error'>❌ Faltan extensiones PHP. Instala las extensiones faltantes.</p>";
    echo "</div></div>";
    exit;
}
echo "</div>";

// Paso 2: Verificar archivos
echo "<div class='step'>";
echo "<h2>📁 Paso 2: Verificar Archivos</h2>";

$required_files = [
    'config/config.php' => 'Configuración principal',
    'config/database.php' => 'Configuración de base de datos',
    'public/index.php' => 'Punto de entrada',
    'database/scheme.sql' => 'Esquema de base de datos'
];

foreach ($required_files as $file => $description) {
    if (file_exists($file)) {
        echo "<p class='success'>✅ $description ($file)</p>";
    } else {
        echo "<p class='error'>❌ $description ($file) - NO ENCONTRADO</p>";
        echo "</div></div>";
        exit;
    }
}
echo "</div>";

// Paso 3: Crear directorios necesarios
echo "<div class='step'>";
echo "<h2>📂 Paso 3: Crear Directorios</h2>";

$directories = [
    'logs' => 'Logs del sistema',
    'public/uploads' => 'Archivos subidos',
    'public/uploads/properties' => 'Imágenes de propiedades',
    'public/uploads/reportes' => 'Archivos de reportes'
];

foreach ($directories as $dir => $description) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "<p class='success'>✅ Creado: $description ($dir)</p>";
        } else {
            echo "<p class='error'>❌ Error creando: $description ($dir)</p>";
        }
    } else {
        echo "<p class='success'>✅ Existe: $description ($dir)</p>";
    }
}
echo "</div>";

// Paso 4: Configurar base de datos
echo "<div class='step'>";
echo "<h2>🗄️ Paso 4: Configurar Base de Datos</h2>";

try {
    // Incluir configuración
    require_once 'config/database.php';
    
    // Intentar conexión a MySQL
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS);
    
    if ($mysqli->connect_error) {
        echo "<p class='error'>❌ Error de conexión a MySQL: " . $mysqli->connect_error . "</p>";
        echo "<p class='warning'>⚠️ Asegúrate de que MySQL esté ejecutándose en Laragon</p>";
        echo "</div></div>";
        exit;
    }
    
    echo "<p class='success'>✅ Conexión a MySQL exitosa</p>";
    
    // Crear base de datos si no existe
    $result = $mysqli->query("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    if ($result) {
        echo "<p class='success'>✅ Base de datos '" . DB_NAME . "' creada/verificada</p>";
    } else {
        echo "<p class='error'>❌ Error creando base de datos: " . $mysqli->error . "</p>";
        echo "</div></div>";
        exit;
    }
    
    // Seleccionar la base de datos
    $mysqli->select_db(DB_NAME);
    
    // Importar esquema simplificado
    $sql_file = 'database/install.sql';
    if (file_exists($sql_file)) {
        // Leer y ejecutar el SQL línea por línea
        $sql_content = file_get_contents($sql_file);
        $queries = explode(';', $sql_content);
        $success_count = 0;
        $error_count = 0;
        
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query) && !preg_match('/^(--|\/\*|DELIMITER)/', $query)) {
                if ($mysqli->query($query)) {
                    $success_count++;
                } else {
                    $error_count++;
                    // No mostrar errores de tablas que ya existen
                    if (strpos($mysqli->error, 'already exists') === false) {
                        echo "<p class='warning'>⚠️ Error en consulta: " . $mysqli->error . "</p>";
                    }
                }
            }
        }
        
        echo "<p class='success'>✅ Esquema importado: $success_count consultas exitosas, $error_count errores</p>";
    } else {
        echo "<p class='error'>❌ No se pudo leer el archivo install.sql</p>";
        echo "</div></div>";
        exit;
    }
    
    // Verificar tablas principales
    $tables = ['usuarios', 'propiedades', 'solicitudes_compra', 'citas'];
    foreach ($tables as $table) {
        $result = $mysqli->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->num_rows > 0) {
            echo "<p class='success'>✅ Tabla $table existe</p>";
        } else {
            echo "<p class='error'>❌ Tabla $table NO EXISTE</p>";
        }
    }
    
    // Crear usuario administrador si no existe
    $admin_email = 'admin@propeasy.com';
    $result = $mysqli->query("SELECT id FROM usuarios WHERE email = '$admin_email'");
    
    if (!$result || $result->num_rows == 0) {
        $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
        $admin_sql = "INSERT INTO usuarios (nombre, apellido, email, password, rol, fecha_registro) 
                      VALUES ('Administrador', 'Sistema', '$admin_email', '$admin_password', 'admin', NOW())";
        
        if ($mysqli->query($admin_sql)) {
            echo "<p class='success'>✅ Usuario administrador creado</p>";
        } else {
            echo "<p class='warning'>⚠️ Error creando usuario administrador: " . $mysqli->error . "</p>";
        }
    } else {
        echo "<p class='success'>✅ Usuario administrador ya existe</p>";
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
    echo "</div></div>";
    exit;
}
echo "</div>";

// Paso 5: Configurar Laragon
echo "<div class='step'>";
echo "<h2>⚙️ Paso 5: Configuración de Laragon</h2>";

echo "<p class='info'>📋 Instrucciones para configurar Laragon:</p>";
echo "<ol>";
echo "<li>Abre Laragon</li>";
echo "<li>Haz clic en 'Start All' para iniciar Apache y MySQL</li>";
echo "<li>En Laragon, ve a 'Menu' → 'www' → 'propeasy'</li>";
echo "<li>O abre directamente: <a href='http://localhost/propeasy' target='_blank'>http://localhost/propeasy</a></li>";
echo "</ol>";

echo "<p class='success'>✅ Configuración de Laragon completada</p>";
echo "</div>";

// Paso 6: Información final
echo "<div class='step'>";
echo "<h2>🎉 Instalación Completada</h2>";

echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
echo "<h3>✅ PropEasy está listo para usar</h3>";
echo "<p><strong>URL de acceso:</strong> <a href='http://localhost/propeasy' target='_blank'>http://localhost/propeasy</a></p>";
echo "<p><strong>Email de administrador:</strong> admin@propeasy.com</p>";
echo "<p><strong>Contraseña:</strong> admin123</p>";
echo "</div>";

echo "<p class='info'>📝 <strong>Nota:</strong> Si tienes problemas, verifica que:</p>";
echo "<ul>";
echo "<li>Laragon esté ejecutándose (Apache y MySQL)</li>";
echo "<li>El proyecto esté en C:\\laragon\\www\\propeasy</li>";
echo "<li>No haya otros servicios usando el puerto 80</li>";
echo "</ul>";
echo "</div>";

echo "</div>";

echo "<p style='text-align: center; margin-top: 30px; color: #666;'>";
echo "Instalación completada el " . date('Y-m-d H:i:s');
echo "</p>";
?> 