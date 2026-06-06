<?php
require_once "Database.php";

class Usuario {
    private $conn;

    // Constructor: recibe la conexión a la base de datos
    public function __construct($db) {
        $this->conn = $db;
    }

    // Método de login
    public function login($email, $password) {
        // Consulta SQL para buscar el usuario por email
        $query = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        // Si existe el usuario
        if($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificamos la contraseña (usando hash si está guardada con password_hash)
            if(password_verify($password, $user['password'])) {
                // Devuelve el array con los datos del usuario
                return $user;
            } else {
                return false; // Contraseña incorrecta
            }
        } else {
            return false; // Usuario no encontrado
        }
    }

    // Método de registro
    public function registrar($nombre, $email, $dni, $password, $rol = "asistente") {
        // Encriptamos la contraseña antes de guardarla
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Consulta SQL para insertar un nuevo usuario
        $query = "INSERT INTO usuarios (nombre, email, dni, password, rol) 
                  VALUES (:nombre, :email, :dni, :password, :rol)";
        $stmt = $this->conn->prepare($query);

        // Vinculamos parámetros
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":dni", $dni);
        $stmt->bindParam(":password", $passwordHash);
        $stmt->bindParam(":rol", $rol);

        // Ejecutamos y devolvemos resultado
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
?>
