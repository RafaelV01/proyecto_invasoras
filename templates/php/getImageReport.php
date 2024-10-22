<?php
include('basedatos.php');

if (!empty($_GET['id']) && is_numeric($_GET['id'])) {

    // Primera consulta
    $stmt = $conexion->prepare("SELECT foto, tipo_mime FROM imagen_reporte WHERE num_reporte = ?");
    $stmt->bind_param('i', $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Si la primera consulta tiene resultados, se obtiene la imagen
        $imgData = $result->fetch_assoc();

        // Renderizar la imagen
        header("Content-type: " . $imgData['tipo_mime']);
        echo $imgData['foto'];
    } else {
        // Si no hay resultados, realizar una segunda consulta
        $stmt2 = $conexion->prepare("SELECT num_especie FROM registro_reporte WHERE num_reporte = ?");
        $stmt2->bind_param('i', $_GET['id']);
        $stmt2->execute();
        $result2 = $stmt2->get_result();

        if ($result2->num_rows > 0) {
            // Si la segunda consulta tiene resultados, obtener el id de la especie
            $registroData = $result2->fetch_assoc();
            $id_especie = $registroData['num_especie'];

            // Tercera consulta para obtener la imagen predeterminada
            $stmt3 = $conexion->prepare("SELECT foto, tipo_mime FROM imagen_especie WHERE num_especie = ?");
            $stmt3->bind_param('i', $id_especie);
            $stmt3->execute();
            $result3 = $stmt3->get_result();

            if ($result3->num_rows > 0) {
                $imgData2 = $result3->fetch_assoc();

                // Renderizar la imagen predeterminada
                header("Content-type: " . $imgData2['tipo_mime']);
                echo $imgData2['foto'];
            } else {
                // Si no se encuentra una imagen de especie, mostrar un mensaje
                echo 'Species image not found...';
            }
            $stmt3->close();
        } else {
            // Si la segunda consulta no devuelve resultados
            echo 'Report record not found...';
        }

        $stmt2->close();
    }

    $stmt->close();
} else {
    echo 'Invalid ID';
}
?>
