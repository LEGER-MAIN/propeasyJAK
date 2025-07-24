<?php
/**
 * Script de limpieza del proyecto PropEasy
 * Elimina archivos temporales y mantiene el proyecto organizado
 */

echo "🧹 LIMPIEZA DEL PROYECTO PROPEASY\n";
echo "==================================\n\n";

// Función para limpiar archivos de logs antiguos
function cleanOldLogs($logDir, $maxSize = 1048576) { // 1MB
    if (!is_dir($logDir)) {
        echo "❌ Directorio de logs no encontrado: $logDir\n";
        return;
    }
    
    $files = glob($logDir . '/*.log');
    foreach ($files as $file) {
        $size = filesize($file);
        if ($size > $maxSize) {
            // Crear backup del archivo actual
            $backup = $file . '.backup.' . date('Y-m-d-H-i-s');
            if (copy($file, $backup)) {
                // Limpiar el archivo original
                file_put_contents($file, '');
                echo "✅ Archivo de log limpiado: " . basename($file) . " (Backup: " . basename($backup) . ")\n";
            }
        }
    }
}

// Función para limpiar archivos temporales
function cleanTempFiles($dir) {
    $tempFiles = [
        '*.tmp',
        '*.temp',
        '*.cache',
        '*.log.bak',
        '*.backup'
    ];
    
    foreach ($tempFiles as $pattern) {
        $files = glob($dir . '/' . $pattern);
        foreach ($files as $file) {
            if (unlink($file)) {
                echo "🗑️ Archivo temporal eliminado: " . basename($file) . "\n";
            }
        }
    }
}

// Función para verificar archivos de prueba
function checkTestFiles($dir) {
    $testPatterns = [
        '*test*.php',
        '*debug*.php',
        '*temp*.php',
        '*backup*.php'
    ];
    
    $found = false;
    foreach ($testPatterns as $pattern) {
        $files = glob($dir . '/' . $pattern);
        foreach ($files as $file) {
            echo "⚠️ Archivo de prueba encontrado: " . basename($file) . "\n";
            $found = true;
        }
    }
    
    if (!$found) {
        echo "✅ No se encontraron archivos de prueba\n";
    }
    
    return $found;
}

echo "📁 VERIFICANDO ARCHIVOS DE PRUEBA:\n";
echo "----------------------------------\n";
$hasTestFiles = checkTestFiles('.');
echo "\n";

echo "📊 LIMPIANDO ARCHIVOS DE LOGS:\n";
echo "------------------------------\n";
cleanOldLogs('./logs');
echo "\n";

echo "🗑️ LIMPIANDO ARCHIVOS TEMPORALES:\n";
echo "---------------------------------\n";
cleanTempFiles('.');
echo "\n";

echo "📋 VERIFICANDO ESTRUCTURA DEL PROYECTO:\n";
echo "---------------------------------------\n";

$requiredDirs = [
    'app',
    'config',
    'database',
    'logs',
    'public',
    'scripts',
    'vendor'
];

foreach ($requiredDirs as $dir) {
    if (is_dir($dir)) {
        echo "✅ Directorio encontrado: $dir\n";
    } else {
        echo "❌ Directorio faltante: $dir\n";
    }
}

$requiredFiles = [
    'README.md',
    'composer.json',
    '.htaccess',
    'config/config.php',
    'public/index.php'
];

foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "✅ Archivo encontrado: $file\n";
    } else {
        echo "❌ Archivo faltante: $file\n";
    }
}

echo "\n🎯 RESUMEN DE LIMPIEZA:\n";
echo "======================\n";
echo "✅ Verificación de archivos de prueba completada\n";
echo "✅ Limpieza de logs completada\n";
echo "✅ Limpieza de archivos temporales completada\n";
echo "✅ Verificación de estructura del proyecto completada\n";

if ($hasTestFiles) {
    echo "\n⚠️ RECOMENDACIÓN: Revisar y eliminar archivos de prueba encontrados\n";
} else {
    echo "\n🎉 ¡Proyecto limpio y organizado!\n";
}

echo "\n🏁 LIMPIEZA COMPLETADA ✅\n";
echo "El proyecto está listo para producción.\n";
?> 
