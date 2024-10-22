<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['user'];
    $password = $_POST['password'];

    
    if ($username == 'admin@admin.com' && $password == 'especie123') {
        $_SESSION['loggedin'] = true;
        $_SESSION['usuario'] = $username;

        
        echo "<script> window.location.href ='../templates/home_admin.php';</script>";
        echo "credenciales correctas";
    } else {
        // Devolver un mensaje de error
        echo "Credenciales incorrectas.";
    }
}
?>