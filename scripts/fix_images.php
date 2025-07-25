<?php
echo "=== ARREGLAR IMÁGENES ===\n\n";

require_once __DIR__ . '/../config/database.php';

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "✅ Conexión exitosa\n\n";
    
    // Obtener propiedades
    $stmt = $pdo->query("SELECT id, titulo FROM propiedades");
    $propiedades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Propiedades encontradas: " . count($propiedades) . "\n\n";
    
    // Obtener archivos de imagen
    $propertiesDir = __DIR__ . '/../public/uploads/properties/';
    $files = scandir($propertiesDir);
    $imageFiles = array_filter($files, function($file) {
        return !in_array($file, ['.', '..', '.gitkeep']) && 
               in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    });
    
    echo "Archivos de imagen: " . count($imageFiles) . "\n\n";
    
    if (empty($imageFiles)) {
        echo "❌ No hay archivos de imagen\n";
        exit;
    }
    
    $imageFilesArray = array_values($imageFiles);
    $insertedCount = 0;
    
    foreach ($propiedades as $propiedad) {
        echo "Procesando: " . $propiedad['titulo'] . "\n";
        
        // Verificar si ya tiene imágenes
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM imagenes_propiedades WHERE propiedad_id = ?");
        $stmt->execute([$propiedad['id']]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existing['total'] > 0) {
            echo "  Ya tiene imágenes, saltando...\n";
            continue;
        }
        
        // Asignar 3 imágenes
        for ($i = 0; $i < 3; $i++) {
            $selectedImage = $imageFilesArray[($insertedCount % count($imageFilesArray))];
            $ruta = '/uploads/properties/' . $selectedImage;
            $esPrincipal = ($i == 0) ? 1 : 0;
            
            $stmt = $pdo->prepare("
                INSERT INTO imagenes_propiedades (propiedad_id, nombre_archivo, ruta, es_principal, orden, fecha_subida) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $propiedad['id'],
                $selectedImage,
                $ruta,
                $esPrincipal,
                $i + 1
            ]);
            
            echo "  ✅ Imagen " . ($i + 1) . ": " . $selectedImage . ($esPrincipal ? " (PRINCIPAL)" : "") . "\n";
            $insertedCount++;
        }
        
        echo "\n";
    }
    
    echo "🎉 Total insertadas: " . $insertedCount . "\n";
    
    // Verificar resultado
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM imagenes_propiedades");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "📊 Total en BD: " . $result['total'] . "\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== FIN ===\n"; 