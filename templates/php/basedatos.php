<?php
$servername = "localhost";
$username = "colminds_center_php_credential";
$password = "invasoras123.";
$dbname = "colminds_sistemas_esp_invasoras";

// Crear conexión
$conexion = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
?>