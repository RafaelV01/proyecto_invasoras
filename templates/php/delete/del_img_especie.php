<?php
include_once("../basedatos.php");

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);

    $sql = "DELETE FROM imagen_especie WHERE cod_imagen = $id";

    if ($conexion->query($sql)) {
        echo "Imagen eliminadoa exitosamente.";
    } else {
        echo "Error al eliminar la imagen: " . $conexion->error;
    }

    $conexion->close();
} else {
    echo "No se recibió un ID válido.";
}


?>