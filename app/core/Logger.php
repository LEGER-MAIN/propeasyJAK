<?php
/**
 * Clase Logger para el sistema PropEasy
 * Maneja el logging de eventos del sistema de manera profesional
 */

class Logger {
    private $logFile;
    private $logLevel;
    
    // Niveles de log
    const LEVEL_DEBUG = 'DEBUG';
    const LEVEL_INFO = 'INFO';
    const LEVEL_WARNING = 'WARNING';
    const LEVEL_ERROR = 'ERROR';
    const LEVEL_CRITICAL = 'CRITICAL';
    
    public function __construct($logFile = null, $logLevel = self::LEVEL_INFO) {
        $this->logFile = $logFile ?: APP_PATH . '/../logs/error.log';
        $this->logLevel = $logLevel;
        
        // Crear directorio de logs si no existe
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }
    
    /**
     * Registrar un mensaje de debug
     */
    public function debug($message, $context = []) {
        $this->log(self::LEVEL_DEBUG, $message, $context);
    }
    
    /**
     * Registrar un mensaje de información
     */
    public function info($message, $context = []) {
        $this->log(self::LEVEL_INFO, $message, $context);
    }
    
    /**
     * Registrar un mensaje de advertencia
     */
    public function warning($message, $context = []) {
        $this->log(self::LEVEL_WARNING, $message, $context);
    }
    
    /**
     * Registrar un mensaje de error
     */
    public function error($message, $context = []) {
        $this->log(self::LEVEL_ERROR, $message, $context);
    }
    
    /**
     * Registrar un mensaje crítico
     */
    public function critical($message, $context = []) {
        $this->log(self::LEVEL_CRITICAL, $message, $context);
    }
    
    /**
     * Registrar un evento de autenticación
     */
    public function auth($action, $user = null, $ip = null, $success = true) {
        $message = "Acción de autenticación: $action";
        $context = [
            'module' => 'auth',
            'user' => $user ?: 'Sistema',
            'ip' => $ip ?: $_SERVER['REMOTE_ADDR'] ?? 'N/A',
            'success' => $success
        ];
        
        $level = $success ? self::LEVEL_INFO : self::LEVEL_WARNING;
        $this->log($level, $message, $context);
    }
    
    /**
     * Registrar un evento de propiedad
     */
    public function property($action, $propertyId = null, $user = null) {
        $message = "Acción de propiedad: $action";
        if ($propertyId) {
            $message .= " (ID: $propertyId)";
        }
        
        $context = [
            'module' => 'property',
            'user' => $user ?: 'Sistema',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'N/A',
            'property_id' => $propertyId
        ];
        
        $this->log(self::LEVEL_INFO, $message, $context);
    }
    
    /**
     * Registrar un evento de usuario
     */
    public function user($action, $userId = null, $adminUser = null) {
        $message = "Acción de usuario: $action";
        if ($userId) {
            $message .= " (ID: $userId)";
        }
        
        $context = [
            'module' => 'user',
            'user' => $adminUser ?: 'Sistema',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'N/A',
            'target_user_id' => $userId
        ];
        
        $this->log(self::LEVEL_INFO, $message, $context);
    }
    
    /**
     * Registrar un evento del sistema
     */
    public function system($action, $details = null) {
        $message = "Evento del sistema: $action";
        if ($details) {
            $message .= " - $details";
        }
        
        $context = [
            'module' => 'system',
            'user' => 'Sistema',
            'ip' => '127.0.0.1',
            'details' => $details
        ];
        
        $this->log(self::LEVEL_INFO, $message, $context);
    }
    
    /**
     * Método principal de logging
     */
    private function log($level, $message, $context = []) {
        // Verificar si el nivel de log es suficiente
        if (!$this->shouldLog($level)) {
            return;
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $module = $context['module'] ?? 'system';
        $user = $context['user'] ?? 'Sistema';
        $ip = $context['ip'] ?? '127.0.0.1';
        
        // Formato del log: [timestamp] LEVEL: message | module | user | ip
        $logEntry = sprintf(
            "[%s] %s: %s | %s | %s | %s\n",
            $timestamp,
            $level,
            $message,
            $module,
            $user,
            $ip
        );
        
        // Escribir al archivo de log
        file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Verificar si se debe registrar el log según el nivel
     */
    private function shouldLog($level) {
        $levels = [
            self::LEVEL_DEBUG => 1,
            self::LEVEL_INFO => 2,
            self::LEVEL_WARNING => 3,
            self::LEVEL_ERROR => 4,
            self::LEVEL_CRITICAL => 5
        ];
        
        $currentLevel = $levels[$this->logLevel] ?? 2;
        $messageLevel = $levels[$level] ?? 2;
        
        return $messageLevel >= $currentLevel;
    }
    
    /**
     * Limpiar logs antiguos (más de 30 días)
     */
    public function cleanOldLogs($days = 30) {
        if (!file_exists($this->logFile)) {
            return;
        }
        
        $content = file_get_contents($this->logFile);
        $lines = explode("\n", $content);
        $cutoffTime = time() - ($days * 24 * 60 * 60);
        
        $newLines = [];
        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }
            
            // Extraer timestamp del log
            if (preg_match('/\[(.*?)\]/', $line, $matches)) {
                $logTime = strtotime($matches[1]);
                if ($logTime && $logTime > $cutoffTime) {
                    $newLines[] = $line;
                }
            } else {
                // Si no se puede parsear, mantener la línea
                $newLines[] = $line;
            }
        }
        
        // Crear backup antes de limpiar
        $backupFile = $this->logFile . '.backup.' . date('Y-m-d-H-i-s');
        copy($this->logFile, $backupFile);
        
        // Escribir logs filtrados
        file_put_contents($this->logFile, implode("\n", $newLines) . "\n");
        
        return count($lines) - count($newLines);
    }
    
    /**
     * Obtener estadísticas de logs
     */
    public function getStats() {
        if (!file_exists($this->logFile)) {
            return [
                'total' => 0,
                'by_level' => [],
                'by_module' => [],
                'recent' => 0
            ];
        }
        
        $content = file_get_contents($this->logFile);
        $lines = explode("\n", $content);
        
        $stats = [
            'total' => 0,
            'by_level' => [],
            'by_module' => [],
            'recent' => 0
        ];
        
        $cutoffTime = time() - (24 * 60 * 60); // Últimas 24 horas
        
        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }
            
            $stats['total']++;
            
            // Extraer nivel y módulo
            if (preg_match('/\[(.*?)\]\s*(\w+):\s*(.*?)\s*\|\s*(\w+)/', $line, $matches)) {
                $level = $matches[2];
                $module = $matches[4];
                $logTime = strtotime($matches[1]);
                
                // Contar por nivel
                $stats['by_level'][$level] = ($stats['by_level'][$level] ?? 0) + 1;
                
                // Contar por módulo
                $stats['by_module'][$module] = ($stats['by_module'][$module] ?? 0) + 1;
                
                // Contar recientes
                if ($logTime && $logTime > $cutoffTime) {
                    $stats['recent']++;
                }
            }
        }
        
        return $stats;
    }
} 