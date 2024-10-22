<?php 
include("../../basedatos.php"); 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $num_reporte = $_POST['num_reporte'];
    $estado = $_POST['estado'];

    // Obtener el id_estado basado en el nombre
    $query = "SELECT id_estado FROM estado_reporte WHERE nombre = '$estado'";
    $resultado = mysqli_query($conexion, $query);
    $row = mysqli_fetch_assoc($resultado);
    $id_estado = $row['id_estado'];

    // Actualizar la tabla rel_estado_reporte
    $query_update = "UPDATE rel_estado_reporte SET id_estado = '$id_estado', fecha_actualizado = NOW() WHERE num_reporte = '$num_reporte'";
    if (mysqli_query($conexion, $query_update)) {
        echo "Estado actualizado con éxito";
    } else {
        echo "Error: " . mysqli_error($conexion);
    }
    
    mysqli_close($conexion);
}

