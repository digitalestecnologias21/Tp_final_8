<?php
session_start();
//Si no hay usuario logueado lo redirijo a la pantalla de Login
if (($_SESSION['usuario']??'')==''){
    header("Location: login.php");
    exit;
}


echo('Login OK');