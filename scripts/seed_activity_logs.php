<?php
/**
 * Script para poblar la tabla de logs_actividad con datos de ejemplo
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 */

// Configuración de la base de datos
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/core/Database.php';

try {
    $db = new Database();
    
    // Datos de ejemplo para logs de actividades
    $sampleActivities = [
        // Actividades de usuarios
        [
            'usuario_id' => 27, // Jefferson Leger (admin)
            'accion' => 'register',
            'tabla_afectada' => 'usuarios',
            'registro_id' => 27,
            'datos_nuevos' => json_encode(['nombre' => 'Jefferson', 'apellido' => 'Leger', 'rol' => 'admin'])
        ],
        [
            'usuario_id' => 28, // Miguel Leger (agente)
            'accion' => 'register',
            'tabla_afectada' => 'usuarios',
            'registro_id' => 28,
            'datos_nuevos' => json_encode(['nombre' => 'Miguel', 'apellido' => 'Leger', 'rol' => 'agente'])
        ],
        
        // Actividades de propiedades
        [
            'usuario_id' => 28, // Miguel Leger
            'accion' => 'create',
            'tabla_afectada' => 'propiedades',
            'registro_id' => 1,
            'datos_nuevos' => json_encode(['titulo' => 'Local en Invivienda', 'precio' => 1000000])
        ],
        [
            'usuario_id' => 28, // Miguel Leger
            'accion' => 'create',
            'tabla_afectada' => 'propiedades',
            'registro_id' => 2,
            'datos_nuevos' => json_encode(['titulo' => 'Casa piantini', 'precio' => 40000])
        ],
        
        // Actividades de login
        [
            'usuario_id' => 27, // Jefferson Leger
            'accion' => 'login',
            'tabla_afectada' => 'usuarios',
            'registro_id' => 27,
            'datos_nuevos' => json_encode(['ip' => '127.0.0.1'])
        ],
        [
            'usuario_id' => 28, // Miguel Leger
            'accion' => 'login',
            'tabla_afectada' => 'usuarios',
            'registro_id' => 28,
            'datos_nuevos' => json_encode(['ip' => '127.0.0.1'])
        ],
        
        // Actividades de actualización de perfiles
        [
            'usuario_id' => 27, // Jefferson Leger
            'accion' => 'update',
            'tabla_afectada' => 'usuarios',
            'registro_id' => 27,
            'datos_anteriores' => json_encode(['ultimo_acceso' => '2024-01-01 00:00:00']),
            'datos_nuevos' => json_encode(['ultimo_acceso' => date('Y-m-d H:i:s')])
        ],
        [
            'usuario_id' => 28, // Miguel Leger
            'accion' => 'update',
            'tabla_afectada' => 'usuarios',
            'registro_id' => 28,
            'datos_anteriores' => json_encode(['ultimo_acceso' => '2024-01-01 00:00:00']),
            'datos_nuevos' => json_encode(['ultimo_acceso' => date('Y-m-d H:i:s')])
        ],
        
        // Actividades de validación de propiedades
        [
            'usuario_id' => 27, // Jefferson Leger
            'accion' => 'validate',
            'tabla_afectada' => 'propiedades',
            'registro_id' => 1,
            'datos_anteriores' => json_encode(['estado_publicacion' => 'pendiente']),
            'datos_nuevos' => json_encode(['estado_publicacion' => 'activa'])
        ],
        [
            'usuario_id' => 27, // Jefferson Leger
            'accion' => 'validate',
            'tabla_afectada' => 'propiedades',
            'registro_id' => 2,
            'datos_anteriores' => json_encode(['estado_publicacion' => 'pendiente']),
            'datos_nuevos' => json_encode(['estado_publicacion' => 'activa'])
        ],
        
        // Actividades de solicitudes (usando usuario existente)
        [
            'usuario_id' => 27, // Jefferson Leger
            'accion' => 'create',
            'tabla_afectada' => 'solicitudes_compra',
            'registro_id' => 1,
            'datos_nuevos' => json_encode(['propiedad_id' => 1, 'estado' => 'nueva'])
        ],
        
        // Actividades de reportes
        [
            'usuario_id' => 28, // Miguel Leger
            'accion' => 'create',
            'tabla_afectada' => 'reportes_irregularidades',
            'registro_id' => 1,
            'datos_nuevos' => json_encode(['tipo' => 'informacion_falsa', 'estado' => 'pendiente'])
        ],
        
        // Actividades de citas
        [
            'usuario_id' => 28, // Miguel Leger
            'accion' => 'create',
            'tabla_afectada' => 'citas',
            'registro_id' => 1,
            'datos_nuevos' => json_encode(['fecha_cita' => date('Y-m-d H:i:s', strtotime('+1 day')), 'estado' => 'propuesta'])
        ]
    ];
    
    // Insertar las actividades de ejemplo
    $inserted = 0;
    foreach ($sampleActivities as $activity) {
        $query = "INSERT INTO logs_actividad 
                  (usuario_id, accion, tabla_afectada, registro_id, datos_anteriores, datos_nuevos, ip_address, user_agent) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $activity['usuario_id'],
            $activity['accion'],
            $activity['tabla_afectada'],
            $activity['registro_id'],
            $activity['datos_anteriores'] ?? null,
            $activity['datos_nuevos'] ?? null,
            '127.0.0.1',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        ];
        
        $result = $db->insert($query, $params);
        if ($result !== false) {
            $inserted++;
            echo "✅ Insertada actividad: {$activity['accion']} en {$activity['tabla_afectada']}\n";
        } else {
            echo "❌ Error insertando: {$activity['accion']} en {$activity['tabla_afectada']}\n";
        }
    }
    
    echo "✅ Se insertaron {$inserted} actividades de ejemplo en la tabla logs_actividad\n";
    echo "📊 Ahora el dashboard mostrará actividades reales del sistema\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
} 
