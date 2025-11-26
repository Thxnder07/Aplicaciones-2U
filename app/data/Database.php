<?php
require_once __DIR__ . '/../config/config.php';

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct() {
        $this->host = DB_HOST;
        $this->db_name = DB_NAME;
        $this->username = DB_USER;
        $this->password = DB_PASS;
    }

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4", 
                $this->username, 
                $this->password
            );
            $this->conn->exec("set names utf8mb4");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch(PDOException $exception) {
            // En producción, no exponer detalles del error
            if (APP_MODE === 'production') {
                error_log("Error de conexión a BD: " . $exception->getMessage());
                // Mostrar mensaje genérico al usuario
                die("Error de conexión a la base de datos. Por favor, contacte al administrador.");
            } else {
                // En desarrollo, mostrar el error completo
                die("Error de conexión: " . $exception->getMessage());
            }
        }
        return $this->conn;
    }
}
?>