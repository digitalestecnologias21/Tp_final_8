<?php
session_start();
require_once "Database.php";
require_once "Usuario.php";

$error_login = ""; // Variable para mostrar errores en el HTML

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $database = new Database();
    $db = $database->connect();
    $usuarioObj = new Usuario($db);

    // El método login debe devolver un array con los datos del usuario o false
    $usuario = $usuarioObj->login($email, $password);

    if($usuario) {
        // Guardamos datos en la sesión
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['rol'] = $usuario['rol'];

        // Redirigimos al puente eventos.php
        header("Location: eventos.php");
        exit;
    } else {
        $error_login = "Credenciales incorrectas.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <img src="img/logo.png" alt="Logo" class="logo">
            <h2>Iniciar Sesión</h2>

            <?php if(!empty($error_login)): ?>
                <div class="alert-error" style="color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; margin-bottom: 15px; border-radius: 4px; font-size: 14px; text-align: center;">
                    <?php echo htmlspecialchars($error_login); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group" style="margin-bottom: 15px; text-align: left;">
                    <label>Email:</label>
                    <input type="text" name="email" style="width: 100%; padding: 8px;" required>
                </div>

                <div class="form-group" style="margin-bottom: 15px; text-align: left;">
                    <label>Password:</label>
                    <input type="password" name="password" style="width: 100%; padding: 8px;" required>
                </div>

                <button type="submit" style="width: 100%; padding: 10px; cursor: pointer;">Ingresar</button>
            </form>
        </div>
    </div>
</body>
</html>
