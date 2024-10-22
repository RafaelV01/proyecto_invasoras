<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sistemas_esp_invasoras";

// Crear conexión
$conexion = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
?>