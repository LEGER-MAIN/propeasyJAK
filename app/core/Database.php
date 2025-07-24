<?php
/**
 * Clase Database - Conexión y operaciones de base de datos
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Esta clase maneja la conexión a MySQL y proporciona métodos
 * para ejecutar consultas preparadas de forma segura.
 */

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $charset;
    private $conn;
    
    /**
     * Constructor de la clase Database
     */
    public function __construct() {
        if (!defined('DB_HOST')) {
            require_once __DIR__ . '/../../config/database.php';
        }
        $this->host = DB_HOST;
        $this->db_name = DB_NAME;
        $this->username = DB_USER;
        $this->password = DB_PASS;
        $this->charset = DB_CHARSET;
    }
    
    /**
     * Obtener la conexión a la base de datos
     * 
     * @return PDO|null Retorna la conexión PDO o null si hay error
     */
    public function getConnection() {
        $this->conn = null;
        
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset}"
            ];
            
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch(PDOException $e) {
            error_log("Error de conexión a la base de datos: " . $e->getMessage());
            return null;
        }
        
        return $this->conn;
    }
    
    /**
     * Ejecutar una consulta SELECT
     * 
     * @param string $query Consulta SQL
     * @param array $params Parámetros para la consulta preparada
     * @return array|false Retorna array de resultados o false si hay error
     */
    public function select($query, $params = []) {
        try {
            $conn = $this->getConnection();
            if (!$conn) return [];
            
            $stmt = $conn->prepare($query);
            $stmt->execute($params);
            
            $result = $stmt->fetchAll();
            return $result ?: [];
            
        } catch(PDOException $e) {
            error_log("Error en consulta SELECT: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Ejecutar una consulta SELECT y obtener una sola fila
     * 
     * @param string $query Consulta SQL
     * @param array $params Parámetros para la consulta preparada
     * @return array|false Retorna una fila o false si hay error
     */
    public function selectOne($query, $params = []) {
        try {
            $conn = $this->getConnection();
            if (!$conn) return false;
            
            $stmt = $conn->prepare($query);
            $stmt->execute($params);
            
            return $stmt->fetch();
            
        } catch(PDOException $e) {
            error_log("Error en consulta SELECT ONE: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Ejecutar una consulta INSERT
     * 
     * @param string $query Consulta SQL
     * @param array $params Parámetros para la consulta preparada
     * @return int|false Retorna el ID del último registro insertado o false si hay error
     */
    public function insert($query, $params = []) {
        try {
            $conn = $this->getConnection();
            if (!$conn) return false;
            
            $stmt = $conn->prepare($query);
            $stmt->execute($params);
            
            return $conn->lastInsertId();
            
        } catch(PDOException $e) {
            error_log("Error en consulta INSERT: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Ejecutar una consulta UPDATE
     * 
     * @param string $query Consulta SQL
     * @param array $params Parámetros para la consulta preparada
     * @return int|false Retorna el número de filas afectadas o false si hay error
     */
    public function update($query, $params = []) {
        try {
            $conn = $this->getConnection();
            if (!$conn) return false;
            
            $stmt = $conn->prepare($query);
            $stmt->execute($params);
            
            return $stmt->rowCount();
            
        } catch(PDOException $e) {
            error_log("Error en consulta UPDATE: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Ejecutar una consulta DELETE
     * 
     * @param string $query Consulta SQL
     * @param array $params Parámetros para la consulta preparada
     * @return int|false Retorna el número de filas afectadas o false si hay error
     */
    public function delete($query, $params = []) {
        try {
            $conn = $this->getConnection();
            if (!$conn) return false;
            
            $stmt = $conn->prepare($query);
            $stmt->execute($params);
            
            return $stmt->rowCount();
            
        } catch(PDOException $e) {
            error_log("Error en consulta DELETE: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Ejecutar una consulta personalizada
     * 
     * @param string $query Consulta SQL
     * @param array $params Parámetros para la consulta preparada
     * @return PDOStatement|false Retorna el statement o false si hay error
     */
    public function execute($query, $params = []) {
        try {
            $conn = $this->getConnection();
            if (!$conn) return false;
            
            $stmt = $conn->prepare($query);
            $stmt->execute($params);
            
            return $stmt;
            
        } catch(PDOException $e) {
            error_log("Error en consulta EXECUTE: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Iniciar una transacción
     * 
     * @return bool Retorna true si se inició correctamente
     */
    public function beginTransaction() {
        try {
            $conn = $this->getConnection();
            if (!$conn) return false;
            
            return $conn->beginTransaction();
            
        } catch(PDOException $e) {
            error_log("Error al iniciar transacción: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Confirmar una transacción
     * 
     * @return bool Retorna true si se confirmó correctamente
     */
    public function commit() {
        try {
            if ($this->conn) {
                return $this->conn->commit();
            }
            return false;
            
        } catch(PDOException $e) {
            error_log("Error al confirmar transacción: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Revertir una transacción
     * 
     * @return bool Retorna true si se revirtió correctamente
     */
    public function rollback() {
        try {
            if ($this->conn) {
                return $this->conn->rollback();
            }
            return false;
            
        } catch(PDOException $e) {
            error_log("Error al revertir transacción: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verificar si la conexión está activa
     * 
     * @return bool Retorna true si la conexión está activa
     */
    public function isConnected() {
        return $this->conn !== null;
    }
    
    /**
     * Cerrar la conexión
     */
    public function closeConnection() {
        $this->conn = null;
    }
} 
