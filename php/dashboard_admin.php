<?php
session_start();
require_once "Database.php";
require_once "Evento.php";

// Verificamos que el usuario esté logueado y sea admin o creador
if(!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] !== "admin" && $_SESSION['rol'] !== "creador")) {
    header("Location: login.php");
    exit;
}

$database = new Database();
$db = $database->connect();
$eventoObj = new Evento($db);

// Crear evento
if(isset($_POST['crear'])) {
    $mensaje = $eventoObj->crearEvento(
        $_POST['titulo'],
        $_POST['descripcion'],
        $_POST['fecha'],
        $_POST['ubicacion'],
        $_POST['capacidad'],
        $_SESSION['usuario_id']
    );
    echo $mensaje;
}

// Habilitar asistencia
if(isset($_POST['habilitar'])) {
    $mensaje = $eventoObj->habilitarAsistencia($_POST['evento_id'], $_SESSION['usuario_id']);
    echo $mensaje;
}

// Listar eventos del creador/admin
$eventos = $eventoObj->listarEventosPorCreador($_SESSION['usuario_id']);
?>

<h2>Dashboard Administrador</h2>
<p>Bienvenido, aquí puedes crear y gestionar tus eventos:</p>

<!-- Formulario para crear evento -->
<form method="POST">
    <label>Título:</label>
    <input type="text" name="titulo" required><br>

    <label>Descripción:</label>
    <textarea name="descripcion"></textarea><br>

    <label>Fecha:</label>
    <input type="date" name="fecha" required><br>

    <label>Ubicación:</label>
    <input type="text" name="ubicacion" required><br>

    <label>Capacidad:</label>
    <input type="number" name="capacidad" required><br>

    <button type="submit" name="crear">Crear Evento</button>
</form>

<hr>

<h3>Mis Eventos</h3>
<table border="1">
    <tr>
        <th>Título</th>
        <th>Fecha</th>
        <th>Ubicación</th>
        <th>Capacidad</th>
        <th>Acciones</th>
    </tr>
    <?php foreach($eventos as $evento): ?>
    <tr>
        <td><?php echo htmlspecialchars($evento['titulo']); ?></td>
        <td><?php echo htmlspecialchars($evento['fecha']); ?></td>
        <td><?php echo htmlspecialchars($evento['ubicacion']); ?></td>
        <td><?php echo htmlspecialchars($evento['capacidad']); ?></td>
        <td>
            <!-- Botón para habilitar asistencia -->
            <form method="POST">
                <input type="hidden" name="evento_id" value="<?php echo $evento['id']; ?>">
                <button type="submit" name="habilitar">Habilitar Asistencia</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
