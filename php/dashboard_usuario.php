<?php
session_start();
require_once "Database.php";
require_once "Evento.php";

// Verificamos que el usuario esté logueado y sea asistente
if(!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== "asistente") {
    header("Location: login.php");
    exit;
}

// Conexión y objeto Evento
$database = new Database();
$db = $database->connect();
$eventoObj = new Evento($db);

// Si el usuario hizo una reserva
if(isset($_POST['reservar'])) {
    $mensaje = $eventoObj->reservarEvento($_SESSION['usuario_id'], $_POST['evento_id']);
    echo $mensaje;
}

// Si el usuario confirmó asistencia
if(isset($_POST['asistir'])) {
    $mensaje = $eventoObj->confirmarAsistencia($_SESSION['usuario_id'], $_POST['evento_id']);
    echo $mensaje;
}

// Listamos eventos
$eventos = $eventoObj->listarEventos();
?>

<h2>Bienvenido al Dashboard de Usuario</h2>
<p>Hola, usuario asistente. Aquí puedes ver los eventos disponibles:</p>

<table border="1">
    <tr>
        <th>Título</th>
        <th>Descripción</th>
        <th>Fecha</th>
        <th>Ubicación</th>
        <th>Acciones</th>
    </tr>
    <?php foreach($eventos as $evento): ?>
    <tr>
        <td><?php echo htmlspecialchars($evento['titulo']); ?></td>
        <td><?php echo htmlspecialchars($evento['descripcion']); ?></td>
        <td><?php echo htmlspecialchars($evento['fecha']); ?></td>
        <td><?php echo htmlspecialchars($evento['ubicacion']); ?></td>
        <td>
            <!-- Botón para reservar -->
            <form method="POST">
                <input type="hidden" name="evento_id" value="<?php echo $evento['id']; ?>">
                <button type="submit" name="reservar">Reservar</button>
            </form>

            <!-- Botón para confirmar asistencia -->
            <form method="POST">
                <input type="hidden" name="evento_id" value="<?php echo $evento['id']; ?>">
                <button type="submit" name="asistir">Confirmar Asistencia</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
