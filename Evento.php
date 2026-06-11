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
        // Inserta reserva con estado 'reservado'
        $query = "INSERT INTO reservas (usuario_id, evento_id, estado) 
                  VALUES (:usuario_id, :evento_id, 'reservado')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->bindParam(":evento_id", $evento_id);
        return $stmt->execute() ? "Reserva realizada correctamente." : "Error al reservar.";
    }

    public function confirmarAsistencia($usuario_id, $evento_id) {
        // Verifica si la asistencia está habilitada
        $query = "SELECT asistencia_habilitada FROM eventos WHERE id = :evento_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":evento_id", $evento_id);
        $stmt->execute();
        $evento = $stmt->fetch(PDO::FETCH_ASSOC);

        if($evento && $evento['asistencia_habilitada'] == 1) {
            // Actualiza estado a 'presente'
            $update = "UPDATE reservas 
                       SET estado = 'presente', fecha_reserva = NOW() 
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

    public function obtenerEstadoReserva($usuario_id, $evento_id) {
        $query = "SELECT estado FROM reservas 
                  WHERE usuario_id = :usuario_id AND evento_id = :evento_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->bindParam(":evento_id", $evento_id);
        $stmt->execute();
        $reserva = $stmt->fetch(PDO::FETCH_ASSOC);
        return $reserva ? $reserva['estado'] : null;
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

        public function habilitarAsistencia($evento_id, $usuario_id) {
        $query = "UPDATE eventos SET asistencia_habilitada = 1 
                WHERE id = :id AND creador_id = :creador_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $evento_id, PDO::PARAM_INT);
        $stmt->bindParam(':creador_id', $usuario_id, PDO::PARAM_INT);

        if($stmt->execute()) {
            return "Asistencia habilitada para este evento.";
        } else {
            return "Error al habilitar asistencia.";
        }
    }


    public function eliminarEvento($evento_id, $usuario_id) {
    try {
        // Primero eliminar reservas asociadas al evento
        $queryReservas = "DELETE FROM reservas WHERE evento_id = :id";
        $stmtRes = $this->conn->prepare($queryReservas);
        $stmtRes->bindParam(':id', $evento_id, PDO::PARAM_INT);
        $stmtRes->execute();

        // Luego eliminar el evento (solo si pertenece al creador/admin actual)
        $queryEvento = "DELETE FROM eventos WHERE id = :id AND creador_id = :creador_id";
        $stmtEvt = $this->conn->prepare($queryEvento);
        $stmtEvt->bindParam(':id', $evento_id, PDO::PARAM_INT);
        $stmtEvt->bindParam(':creador_id', $usuario_id, PDO::PARAM_INT);

        if($stmtEvt->execute()) {
            return "Evento eliminado correctamente.";
        } else {
            return "Error al eliminar el evento.";
        }
        } catch(PDOException $e) {
            return "Error en la eliminación: " . $e->getMessage();
        }
    }

}
?>
