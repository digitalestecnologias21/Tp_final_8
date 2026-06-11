<?php
class Database {
    private $host = "host.docker.internal";   // Servidor de base de datos
    private $db_name = "universidad_eventos"; // Nombre de la base
    private $username = "root";    // Usuario por defecto en XAMPP
    private $password = "";        // Contraseña (vacía por defecto en XAMPP)
    public $conn;

    // Método para conectar a la base de datos usando PDO
    public function connect() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            // Configuramos PDO para que lance excepciones si hay errores
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
        }
        return $this->conn;
    }
}
