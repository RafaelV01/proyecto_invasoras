<?php
include('basedatos.php');

if (!empty($_GET['id']) && is_numeric($_GET['id'])) {

    // Prepare and execute query
    $stmt = $conexion->prepare("SELECT foto, tipo_mime FROM imagen_especie WHERE num_especie = ? LIMIT 1");
    $stmt->bind_param('i', $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $imgData = $result->fetch_assoc();

        // Render image
        header("Content-type: " . $imgData['tipo_mime']);
        echo $imgData['foto'];
    } else {
        echo 'Image not found...';
    }

    $stmt->close();
} else {
    echo 'Invalid ID';
}
?>