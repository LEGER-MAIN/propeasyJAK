<?php
/**
 * Script de Instalación Automática - PropEasy
 * Sistema Web de Venta de Bienes Raíces
 * 
 * Este script automatiza la instalación completa de PropEasy en un entorno Laragon
 * Incluye: configuración de BD, instalación de dependencias, verificación de requisitos
 */

// Configuración de errores para instalación
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Colores para la consola
class Colors {
    const GREEN = "\033[32m";
    const RED = "\033[31m";
    const YELLOW = "\033[33m";
    const BLUE = "\033[34m";
    const RESET = "\033[0m";
    const BOLD = "\033[1m";
}

function printHeader() {
    echo Colors::BOLD . Colors::BLUE . "
╔══════════════════════════════════════════════════════════════╗
║                    PropEasy - Instalador                     ║
║              Sistema Web de Venta de Bienes Raíces           ║
║                        v2.9.0                                ║
╚══════════════════════════════════════════════════════════════╝" . Colors::RESET . "\n\n";
}

function printStep($step, $message) {
    echo Colors::BOLD . Colors::BLUE . "[PASO {$step}] " . Colors::RESET . $message . "\n";
}

function printSuccess($message) {
    echo Colors::GREEN . "✓ " . $message . Colors::RESET . "\n";
}

function printError($message) {
    echo Colors::RED . "✗ " . $message . Colors::RESET . "\n";
}

function printWarning($message) {
    echo Colors::YELLOW . "⚠ " . $message . Colors::RESET . "\n";
}

function printInfo($message) {
    echo Colors::BLUE . "ℹ " . $message . Colors::RESET . "\n";
}

// Función para verificar requisitos del sistema
function checkSystemRequirements() {
    printStep(1, "Verificando requisitos del sistema...\n");
    
    $requirements = [
        'php' => ['version' => '8.0.0', 'current' => PHP_VERSION],
        'extensions' => [
            'pdo',
            'pdo_mysql',
            'gd',
            'json',
            'mbstring',
            'openssl'
        ]
    ];
    
    // Verificar versión de PHP
    if (version_compare(PHP_VERSION, $requirements['php']['version'], '>=')) {
        printSuccess("PHP " . PHP_VERSION . " (requerido: " . $requirements['php']['version'] . "+)");
    } else {
        printError("PHP " . PHP_VERSION . " (requerido: " . $requirements['php']['version'] . "+)");
        return false;
    }
    
    // Verificar extensiones
    foreach ($requirements['extensions'] as $ext) {
        if (extension_loaded($ext)) {
            printSuccess("Extensión {$ext} cargada");
        } else {
            printError("Extensión {$ext} no encontrada");
            return false;
        }
    }
    
    // Verificar si estamos en Laragon
    $laragonPath = 'C:\laragon';
    if (is_dir($laragonPath)) {
        printSuccess("Laragon detectado en {$laragonPath}");
    } else {
        printWarning("Laragon no detectado en {$laragonPath}");
    }
    
    return true;
}

// Función para crear estructura de directorios
function createDirectoryStructure() {
    printStep(2, "Creando estructura de directorios...\n");
    
    $directories = [
        'logs',
        'public/uploads/properties',
        'public/uploads/profiles',
        'public/uploads/reportes',
        'sessions'
    ];
    
    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            if (mkdir($dir, 0755, true)) {
                printSuccess("Directorio creado: {$dir}");
            } else {
                printError("No se pudo crear el directorio: {$dir}");
                return false;
            }
        } else {
            printSuccess("Directorio ya existe: {$dir}");
        }
    }
    
    // Crear archivos .gitkeep
    $gitkeepDirs = [
        'public/uploads/properties',
        'public/uploads/profiles',
        'public/uploads/reportes'
    ];
    
    foreach ($gitkeepDirs as $dir) {
        $gitkeepFile = $dir . '/.gitkeep';
        if (!file_exists($gitkeepFile)) {
            file_put_contents($gitkeepFile, '');
            printSuccess("Archivo .gitkeep creado en: {$dir}");
        }
    }
    
    return true;
}

// Función para configurar la base de datos
function setupDatabase() {
    printStep(3, "Configurando base de datos...\n");
    
    // Verificar si existe el archivo de configuración
    if (!file_exists('config/database.example.php')) {
        printError("Archivo config/database.example.php no encontrado");
        return false;
    }
    
    // Crear archivo de configuración si no existe
    if (!file_exists('config/database.php')) {
        if (copy('config/database.example.php', 'config/database.php')) {
            printSuccess("Archivo de configuración de BD creado");
        } else {
            printError("No se pudo crear el archivo de configuración de BD");
            return false;
        }
    } else {
        printSuccess("Archivo de configuración de BD ya existe");
    }
    
    // Intentar conectar a la base de datos
    try {
        $pdo = new PDO('mysql:host=localhost', 'root', '');
        printSuccess("Conexión a MySQL exitosa");
        
        // Crear base de datos si no existe
        $dbName = 'propeasy_db';
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        printSuccess("Base de datos '{$dbName}' creada/verificada");
        
        // Seleccionar la base de datos
        $pdo->exec("USE `{$dbName}`");
        
        // Importar esquema si existe
        if (file_exists('database/scheme.sql')) {
            $sql = file_get_contents('database/scheme.sql');
            
            // Dividir el SQL en comandos individuales
            $commands = array_filter(array_map('trim', explode(';', $sql)));
            
            $successCount = 0;
            $errorCount = 0;
            
            foreach ($commands as $command) {
                if (!empty($command)) {
                    try {
                        $pdo->exec($command);
                        $successCount++;
                    } catch (PDOException $e) {
                        // Ignorar errores de funciones/triggers que ya existen
                        if (strpos($e->getMessage(), 'already exists') === false) {
                            $errorCount++;
                            printWarning("Comando SQL con error: " . substr($command, 0, 50) . "...");
                        }
                    }
                }
            }
            
            printSuccess("Esquema de BD importado ({$successCount} comandos exitosos, {$errorCount} errores ignorados)");
        } else {
            printError("Archivo database/scheme.sql no encontrado");
            return false;
        }
        
    } catch (PDOException $e) {
        printError("Error de conexión a MySQL: " . $e->getMessage());
        printInfo("Asegúrate de que MySQL esté ejecutándose en Laragon");
        return false;
    }
    
    return true;
}

// Función para instalar dependencias de Composer
function installComposerDependencies() {
    printStep(4, "Instalando dependencias de Composer...\n");
    
    if (!file_exists('composer.json')) {
        printWarning("composer.json no encontrado - saltando instalación de dependencias");
        return true;
    }
    
    // Verificar si Composer está disponible
    $composerPath = '';
    $possiblePaths = [
        'composer',
        'C:\laragon\bin\composer\composer.bat',
        'C:\ProgramData\ComposerSetup\bin\composer.phar'
    ];
    
    foreach ($possiblePaths as $path) {
        $output = shell_exec("where {$path} 2>nul");
        if (!empty($output)) {
            $composerPath = trim($output);
            break;
        }
    }
    
    if (empty($composerPath)) {
        printWarning("Composer no encontrado - instalando manualmente...");
        
        // Crear directorio vendor básico
        if (!is_dir('vendor')) {
            mkdir('vendor', 0755, true);
        }
        
        // Crear autoload básico
        $autoloadDir = 'vendor/autoload.php';
        if (!file_exists($autoloadDir)) {
            $autoloadContent = '<?php
// Autoload básico para PropEasy
spl_autoload_register(function ($class) {
    $file = __DIR__ . "/../app/models/" . $class . ".php";
    if (file_exists($file)) {
        require_once $file;
    }
});';
            file_put_contents($autoloadDir, $autoloadContent);
            printSuccess("Autoload básico creado");
        }
        
        return true;
    }
    
    // Ejecutar composer install
    $command = "cd " . escapeshellarg(getcwd()) . " && {$composerPath} install --no-dev --optimize-autoloader";
    $output = shell_exec($command . " 2>&1");
    
    if (strpos($output, 'error') !== false || strpos($output, 'Error') !== false) {
        printWarning("Error en Composer: " . substr($output, 0, 200) . "...");
        return false;
    } else {
        printSuccess("Dependencias de Composer instaladas");
    }
    
    return true;
}

// Función para verificar la instalación
function verifyInstallation() {
    printStep(5, "Verificando instalación...\n");
    
    $checks = [
        'config/database.php' => 'Configuración de BD',
        'database/scheme.sql' => 'Esquema de BD',
        'app/core/Database.php' => 'Clase Database',
        'app/controllers/AdminController.php' => 'Controlador Admin',
        'app/models/AlertManager.php' => 'Modelo AlertManager',
        'public/index.php' => 'Archivo principal',
        'logs' => 'Directorio de logs',
        'public/uploads' => 'Directorio de uploads'
    ];
    
    $allGood = true;
    
    foreach ($checks as $path => $description) {
        if (file_exists($path) || is_dir($path)) {
            printSuccess("{$description}: OK");
        } else {
            printError("{$description}: FALTANTE");
            $allGood = false;
        }
    }
    
    // Verificar conexión a BD
    try {
        require_once 'config/config.php';
        require_once 'app/core/Database.php';
        $db = new Database();
        $conn = $db->getConnection();
        
        if ($conn) {
            printSuccess("Conexión a BD: OK");
        } else {
            printError("Conexión a BD: FALLIDA");
            $allGood = false;
        }
    } catch (Exception $e) {
        printError("Conexión a BD: ERROR - " . $e->getMessage());
        $allGood = false;
    }
    
    return $allGood;
}

// Función para mostrar información final
function showFinalInfo() {
    printStep(6, "Instalación completada\n");
    
    echo Colors::BOLD . Colors::GREEN . "
╔══════════════════════════════════════════════════════════════╗
║                    ¡INSTALACIÓN EXITOSA!                     ║
╚══════════════════════════════════════════════════════════════╝" . Colors::RESET . "\n\n";
    
    echo Colors::BOLD . "📋 Información de Acceso:\n" . Colors::RESET;
    echo "• URL Principal: http://localhost/propeasy\n";
    echo "• Panel Admin: http://localhost/propeasy/admin/dashboard\n";
    echo "• Base de Datos: propeasy_db\n";
    echo "• Usuario MySQL: root (sin contraseña)\n\n";
    
    echo Colors::BOLD . "🔧 Configuración Adicional:\n" . Colors::RESET;
    echo "• Edita config/database.php si necesitas cambiar credenciales\n";
    echo "• Configura config/config.php para emails y otras opciones\n";
    echo "• Asegúrate de que mod_rewrite esté habilitado en Apache\n\n";
    
    echo Colors::BOLD . "📚 Documentación:\n" . Colors::RESET;
    echo "• Lee README.md para más información\n";
    echo "• Revisa la estructura del proyecto en la documentación\n\n";
    
    echo Colors::BOLD . "🚀 Próximos Pasos:\n" . Colors::RESET;
    echo "1. Accede a http://localhost/propeasy\n";
    echo "2. Registra un usuario administrador\n";
    echo "3. Configura las opciones del sistema\n";
    echo "4. ¡Disfruta usando PropEasy!\n\n";
}

// Función principal
function main() {
    printHeader();
    
    echo Colors::BOLD . "Este script instalará PropEasy en tu entorno Laragon.\n";
    echo "Asegúrate de que Laragon esté ejecutándose con MySQL y Apache.\n\n" . Colors::RESET;
    
    // Verificar requisitos
    if (!checkSystemRequirements()) {
        printError("Los requisitos del sistema no se cumplen. Abortando instalación.");
        return false;
    }
    
    // Crear estructura de directorios
    if (!createDirectoryStructure()) {
        printError("Error al crear la estructura de directorios. Abortando instalación.");
        return false;
    }
    
    // Configurar base de datos
    if (!setupDatabase()) {
        printError("Error al configurar la base de datos. Abortando instalación.");
        return false;
    }
    
    // Instalar dependencias
    if (!installComposerDependencies()) {
        printWarning("Advertencia: No se pudieron instalar todas las dependencias.");
    }
    
    // Verificar instalación
    if (!verifyInstallation()) {
        printError("La verificación de instalación falló. Revisa los errores anteriores.");
        return false;
    }
    
    // Mostrar información final
    showFinalInfo();
    
    return true;
}

// Ejecutar instalación
if (php_sapi_name() === 'cli') {
    main();
} else {
    echo "<h1>PropEasy - Instalador</h1>";
    echo "<p>Este script debe ejecutarse desde la línea de comandos:</p>";
    echo "<code>php install_propeasy.php</code>";
    echo "<p>O desde la terminal de Laragon.</p>";
}
?> 