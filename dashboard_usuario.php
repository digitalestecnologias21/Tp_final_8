<?php
session_start();
require_once "Database.php";
require_once "Evento.php";

// Verificamos que el usuario esté logueado y sea asistente
if(!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== "asistente") {
    header("Location: login.php");
    exit;
}

$database = new Database();
$db = $database->connect();
$eventoObj = new Evento($db);

// Mensajes de acción
if(isset($_POST['reservar'])) {
    $mensaje = $eventoObj->reservarEvento($_SESSION['usuario_id'], $_POST['evento_id']);
    echo "<div class='alert success'>$mensaje</div>";
}
if(isset($_POST['asistir'])) {
    $mensaje = $eventoObj->confirmarAsistencia($_SESSION['usuario_id'], $_POST['evento_id']);
    echo "<div class='alert info'>$mensaje</div>";
}

$eventos = $eventoObj->listarEventos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Usuario</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar">
        <div class="logo">EventU</div>
        <ul>
            <li><a href="#">Inicio</a></li>
            <li><a href="#">Mis Inscripciones</a></li>
            <li><a href="logout.php" class="btn-logout">Cerrar sesión</a></li>
        </ul>
    </nav>

    <div class="container">
        <h2>Dashboard de Usuario</h2>
        <p>Explora los eventos disponibles e inscríbete fácilmente.</p>

        <!-- Buscador -->
        <input type="text" placeholder="Buscar evento..." class="search-bar">

        <!-- Tabla de eventos -->
        <table class="event-table">
            <tr>
                <th>Título</th>
                <th>Descripción</th>
                <th>Fecha</th>
                <th>Ubicación</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
            <?php foreach($eventos as $evento): ?>
            <tr>
                <td><?php echo htmlspecialchars($evento['titulo']); ?></td>
                <td><?php echo htmlspecialchars($evento['descripcion']); ?></td>
                <td><?php echo htmlspecialchars($evento['fecha']); ?></td>
                <td><?php echo htmlspecialchars($evento['ubicacion']); ?></td>
                <td>
                    <?php 
                    $estado = $eventoObj->obtenerEstadoReserva($_SESSION['usuario_id'], $evento['id']);
                    echo $estado ? htmlspecialchars($estado) : "Sin reservar";
                    ?>
                </td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="evento_id" value="<?php echo $evento['id']; ?>">
                        <button type="submit" name="reservar" class="btn">Inscribirse</button>
                    </form>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="evento_id" value="<?php echo $evento['id']; ?>">
                        <button type="submit" name="asistir" class="btn">Confirmar Asistencia</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- Script externo para buscador -->
    </div> <!-- cierre del container -->
    <script src="js/busqueda.js"></script>
</body>
</html>
