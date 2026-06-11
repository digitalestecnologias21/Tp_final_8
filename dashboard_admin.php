<?php
session_start();
require_once "Database.php";
require_once "Evento.php";

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
    echo "<div class='alert success'>$mensaje</div>";
}

// Habilitar asistencia
if(isset($_POST['habilitar'])) {
    $mensaje = $eventoObj->habilitarAsistencia($_POST['evento_id'], $_SESSION['usuario_id']);
    echo "<div class='alert info'>$mensaje</div>";
}

// Eliminar evento
if(isset($_POST['eliminar'])) {
    $mensaje = $eventoObj->eliminarEvento($_POST['evento_id'], $_SESSION['usuario_id']);
    echo "<div class='alert danger'>$mensaje</div>";
}

// Listar eventos del creador/admin
$eventos = $eventoObj->listarEventosPorCreador($_SESSION['usuario_id']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Administrador</title>
    <link rel="stylesheet" href="css/styleadmin.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">EventU</div>
        <ul>
            <li><a href="#">Panel</a></li>
            <li><a href="logout.php" class="btn-logout">Cerrar sesión</a></li>
        </ul>
    </nav>

    <div class="container">
        <h2>Dashboard Administrador</h2>

        <!-- Crear evento -->
        <section class="crear-evento">
            <h3>Crear Evento</h3>
            <form method="POST">
                <input type="text" name="titulo" placeholder="Título del evento" required>
                <textarea name="descripcion" placeholder="Descripción del evento"></textarea>
                <input type="date" name="fecha" required>
                <input type="text" name="ubicacion" placeholder="Ubicación" required>
                <input type="number" name="capacidad" placeholder="Capacidad" required>
                <button type="submit" name="crear" class="btn">Crear Evento</button>
            </form>
        </section>

        <!-- Mis eventos -->
        <section class="mis-eventos">
            <h3>Mis Eventos</h3>
            <input type="text" placeholder="Buscar evento..." class="search-bar">

            <table class="event-table">
                <tr>
                    <th>Título</th>
                    <th>Fecha</th>
                    <th>Ubicación</th>
                    <th>Capacidad</th>
                    <th>Estado Asistencia</th>
                    <th>Acciones</th>
                </tr>
                <?php foreach($eventos as $evento): ?>
                <tr>
                    <td><?php echo htmlspecialchars($evento['titulo']); ?></td>
                    <td><?php echo htmlspecialchars($evento['fecha']); ?></td>
                    <td><?php echo htmlspecialchars($evento['ubicacion']); ?></td>
                    <td><?php echo htmlspecialchars($evento['capacidad']); ?></td>
                    <td>
                        <?php echo $evento['asistencia_habilitada'] ? "Habilitada" : "No habilitada"; ?>
                    </td>
                    <td>
                        <!-- Botón habilitar asistencia -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="evento_id" value="<?php echo $evento['id']; ?>">
                            <button type="submit" name="habilitar" class="btn success">Habilitar Asistencia</button>
                        </form>

                        <!-- Botón eliminar evento -->
                        <form method="POST" style="display:inline;" onsubmit="return confirm('¿Seguro que deseas eliminar este evento?');">
                            <input type="hidden" name="evento_id" value="<?php echo $evento['id']; ?>">
                            <button type="submit" name="eliminar" class="btn danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </section>
    </div>

    <script src="js/busqueda.js"></script>
</body>
</html>
