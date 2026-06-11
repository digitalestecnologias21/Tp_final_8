<?php
session_start();

// Si no hay sesión activa, volver al login
if(!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Redirigir según el rol
if($_SESSION['rol'] === "asistente") {
    header("Location: dashboard_usuario.php");
    exit;
} elseif($_SESSION['rol'] === "admin" || $_SESSION['rol'] === "creador") {
    header("Location: dashboard_admin.php");
    exit;
} else {
    // Si el rol no es válido, cerrar sesión y volver al login
    session_destroy();
    header("Location: login.php");
    exit;
}
?>

