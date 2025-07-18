<?php
/**
 * Script para enviar recordatorios automáticos de citas
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este script debe ejecutarse diariamente vía cron job para enviar
 * recordatorios de citas programadas para el día siguiente.
 * 
 * Ejemplo de cron job:
 * 0 8 * * * /usr/bin/php /path/to/propeasy/scripts/send_appointment_reminders.php
 */

// Configurar zona horaria
date_default_timezone_set('America/Santo_Domingo');

// Incluir configuración
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/models/Appointment.php';
require_once __DIR__ . '/../app/models/User.php';
require_once __DIR__ . '/../app/helpers/EmailHelper.php';

// Inicializar base de datos
$db = new Database();
$appointmentModel = new Appointment();
$userModel = new User();
$emailHelper = new EmailHelper();

/**
 * Función principal para enviar recordatorios
 */
function sendAppointmentReminders() {
    global $appointmentModel, $userModel, $emailHelper;
    
    echo "Iniciando envío de recordatorios de citas...\n";
    
    try {
        // Obtener citas para mañana que estén aceptadas
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $citas = $appointmentModel->getByDateRange(null, $tomorrow . ' 00:00:00', $tomorrow . ' 23:59:59');
        
        // Filtrar solo citas aceptadas
        $citasAceptadas = array_filter($citas, function($cita) {
            return $cita['estado'] === 'aceptada';
        });
        
        echo "Encontradas " . count($citasAceptadas) . " citas para mañana.\n";
        
        $enviados = 0;
        $errores = 0;
        
        foreach ($citasAceptadas as $cita) {
            try {
                // Obtener datos del agente y cliente
                $agente = $userModel->getById($cita['agente_id']);
                $cliente = $userModel->getById($cita['cliente_id']);
                
                if (!$agente || !$cliente) {
                    echo "Error: No se encontraron datos del agente o cliente para la cita {$cita['id']}\n";
                    $errores++;
                    continue;
                }
                
                // Verificar que no se haya enviado recordatorio hoy
                $recordatorioEnviado = checkReminderSent($cita['id']);
                if ($recordatorioEnviado) {
                    echo "Recordatorio ya enviado para la cita {$cita['id']}\n";
                    continue;
                }
                
                // Enviar recordatorio
                $enviado = $emailHelper->sendAppointmentReminder($cita, $agente, $cliente);
                
                if ($enviado) {
                    // Marcar recordatorio como enviado
                    markReminderSent($cita['id']);
                    echo "Recordatorio enviado para la cita {$cita['id']} - {$agente['email']}, {$cliente['email']}\n";
                    $enviados++;
                } else {
                    echo "Error enviando recordatorio para la cita {$cita['id']}\n";
                    $errores++;
                }
                
                // Pausa pequeña para no sobrecargar el servidor de email
                sleep(1);
                
            } catch (Exception $e) {
                echo "Error procesando cita {$cita['id']}: " . $e->getMessage() . "\n";
                $errores++;
            }
        }
        
        echo "\nResumen:\n";
        echo "- Recordatorios enviados: {$enviados}\n";
        echo "- Errores: {$errores}\n";
        echo "- Total procesadas: " . count($citasAceptadas) . "\n";
        
    } catch (Exception $e) {
        echo "Error general: " . $e->getMessage() . "\n";
        exit(1);
    }
}

/**
 * Verificar si ya se envió recordatorio para una cita
 */
function checkReminderSent($appointmentId) {
    global $db;
    
    $sql = "SELECT COUNT(*) as count FROM recordatorios_citas WHERE cita_id = ? AND fecha_envio = CURDATE()";
    $stmt = $db->prepare($sql);
    $stmt->execute([$appointmentId]);
    $result = $stmt->fetch();
    
    return $result['count'] > 0;
}

/**
 * Marcar recordatorio como enviado
 */
function markReminderSent($appointmentId) {
    global $db;
    
    $sql = "INSERT INTO recordatorios_citas (cita_id, fecha_envio, created_at) VALUES (?, CURDATE(), NOW())";
    $stmt = $db->prepare($sql);
    $stmt->execute([$appointmentId]);
}

/**
 * Crear tabla de recordatorios si no existe
 */
function createRemindersTable() {
    global $db;
    
    $sql = "CREATE TABLE IF NOT EXISTS recordatorios_citas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        cita_id INT NOT NULL,
        fecha_envio DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_reminder (cita_id, fecha_envio),
        FOREIGN KEY (cita_id) REFERENCES citas(id) ON DELETE CASCADE
    )";
    
    $db->exec($sql);
    echo "Tabla de recordatorios verificada/creada.\n";
}

// Ejecutar script
if (php_sapi_name() === 'cli') {
    echo "=== Script de Recordatorios de Citas ===\n";
    echo "Fecha: " . date('Y-m-d H:i:s') . "\n\n";
    
    // Crear tabla si no existe
    createRemindersTable();
    
    // Enviar recordatorios
    sendAppointmentReminders();
    
    echo "\nScript completado.\n";
} else {
    echo "Este script debe ejecutarse desde la línea de comandos.\n";
    exit(1);
}
?> 