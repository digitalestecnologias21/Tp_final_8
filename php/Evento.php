<?php
class Evento {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // --- Métodos para asistentes ---
    public function listarEventos() {
        $query = "SELECT * FROM eventos";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function reservarEvento($usuario_id, $evento_id) {
        $query = "INSERT INTO reservas (usuario_id, evento_id) VALUES (:usuario_id, :evento_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->bindParam(":evento_id", $evento_id);
        return $stmt->execute() ? "Reserva realizada correctamente." : "Error al reservar.";
    }

    public function confirmarAsistencia($usuario_id, $evento_id) {
        $query = "SELECT asistencia_habilitada FROM eventos WHERE id = :evento_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":evento_id", $evento_id);
        $stmt->execute();
        $evento = $stmt->fetch(PDO::FETCH_ASSOC);

        if($evento && $evento['asistencia_habilitada'] == 1) {
            $update = "UPDATE reservas SET fecha_reserva = NOW() 
                       WHERE usuario_id = :usuario_id AND evento_id = :evento_id";
            $stmt2 = $this->conn->prepare($update);
            $stmt2->bindParam(":usuario_id", $usuario_id);
            $stmt2->bindParam(":evento_id", $evento_id);
            $stmt2->execute();
            return "Asistencia confirmada.";
        } else {
            return "La asistencia aún no está habilitada para este evento.";
        }
    }

    // --- Métodos para administradores/creadores ---
    public function crearEvento($titulo, $descripcion, $fecha, $ubicacion, $capacidad, $creador_id) {
        $queryCheck = "SELECT * FROM eventos WHERE titulo = :titulo AND fecha = :fecha AND creador_id = :creador_id";
        $stmtCheck = $this->conn->prepare($queryCheck);
        $stmtCheck->bindParam(":titulo", $titulo);
        $stmtCheck->bindParam(":fecha", $fecha);
        $stmtCheck->bindParam(":creador_id", $creador_id);
        $stmtCheck->execute();

        if($stmtCheck->rowCount() > 0) {
            return "Ya existe un evento con ese título y fecha.";
        }

        $query = "INSERT INTO eventos (titulo, descripcion, fecha, ubicacion, capacidad, creador_id, asistencia_habilitada) 
                  VALUES (:titulo, :descripcion, :fecha, :ubicacion, :capacidad, :creador_id, 0)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":titulo", $titulo);
        $stmt->bindParam(":descripcion", $descripcion);
        $stmt->bindParam(":fecha", $fecha);
        $stmt->bindParam(":ubicacion", $ubicacion);
        $stmt->bindParam(":capacidad", $capacidad);
        $stmt->bindParam(":creador_id", $creador_id);
        return $stmt->execute() ? "Evento creado correctamente." : "Error al crear evento.";
    }

    public function listarEventosPorCreador($creador_id) {
        $query = "SELECT * FROM eventos WHERE creador_id = :creador_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":creador_id", $creador_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function habilitarAsistencia($evento_id, $creador_id) {
        $query = "UPDATE eventos SET asistencia_habilitada = 1 
                  WHERE id = :evento_id AND creador_id = :creador_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":evento_id", $evento_id);
        $stmt->bindParam(":creador_id", $creador_id);
        return $stmt->execute() ? "Asistencia habilitada para el evento." : "Error al habilitar asistencia.";
    }
}
?>
