<?php
include_once("../basedatos.php");

header('Content-Type: application/json'); // Asegurarse de que el contenido es JSON

$response = array(); // Array para la respuesta

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);

    $sql = "DELETE FROM animal WHERE num_especie = $id";

    if ($conexion->query($sql)) {
        $response['success'] = true;
        $response['message'] = "Registro eliminado exitosamente.";
    } else {
        $response['success'] = false;
        $response['message'] = "Error al eliminar el registro: " . $conexion->error;
    }

    $conexion->close();
} else {
    $response['success'] = false;
    $response['message'] = "No se recibió un ID válido.";
}

echo json_encode($response); // Devolver la respuesta en formato JSON
?>
